<?php 
include_once '../connection.php';

// Ensure the server returns JSON format to prevent table crashing
header('Content-Type: application/json');

$archive_status = trim('YES');
$first_name = isset($_POST['first_name']) ? $con->real_escape_string($_POST['first_name']) : '';
$middle_name = isset($_POST['middle_name']) ? $con->real_escape_string($_POST['middle_name']) : '';
$last_name = isset($_POST['last_name']) ? $con->real_escape_string($_POST['last_name']) : '';
$resident_id = isset($_POST['resident_id']) ? $con->real_escape_string($_POST['resident_id']) : '';
$residency_type = isset($_POST['residency_type']) ? $con->real_escape_string($_POST['residency_type']) : '';

$whereClause = [];

if(!empty($resident_id))  
    $whereClause[] = "residence_information.residence_id='$resident_id'";

if(!empty($first_name))  
    $whereClause[] = "first_name LIKE '%" .$first_name. "%'";

if(!empty($middle_name))  
    $whereClause[] = "middle_name LIKE '%" .$middle_name. "%'";

if(!empty($last_name))  
    $whereClause[] = "last_name LIKE '%" .$last_name. "%'";

// Filter by Residency Type if selected
if(!empty($residency_type))
    $whereClause[] = "residence_status.residency_type='$residency_type'";


$where = '';
if(count($whereClause) > 0){
    $where .= ' AND ' .implode(' AND ', $whereClause);
}

// Added 'date_archive' to the SELECT list to prevent sorting errors
$sql = "SELECT residence_information.residence_id, 
    residence_information.first_name, 
    residence_information.last_name, 
    residence_information.middle_name,
    residence_information.age,
    residence_information.image, 
    residence_information.image_path,
    residence_status.pwd, 
    residence_status.status, 
    residence_status.residency_type, 
    residence_status.archive,
    residence_status.single_parent,
    residence_status.pwd_info,
    residence_status.date_added,
    residence_status.date_archive  
    FROM residence_information 
    INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id 
    WHERE residence_status.archive = '$archive_status'" .$where;

$query = $con->query($sql) or die ($con->error);
$totalData = $query->num_rows;
$totalFiltered = $totalData;

// Handle Sorting
if(isset($_REQUEST['order'])){
    $sql .= ' ORDER BY '.
    $_REQUEST['order']['0']['column'].
    ' '.
    $_REQUEST['order']['0']['dir'].
    ' ';
}else{
    // Default sort by date_archive
    $sql .= ' ORDER BY date_archive DESC ';
}

// Handle Pagination
if($_REQUEST['length'] != -1){
    $sql .= ' LIMIT '.
    $_REQUEST['start'].
    ' ,'.
    $_REQUEST['length'].
    '';
}

$query = $con->query($sql) or die ($con->error);
$data = [];

while($row = $query->fetch_assoc()){
    
    // Image Logic
    if($row['image'] != '' || $row['image'] != null || !empty($row['image'])){
        $image = '<span style="cursor: pointer;" class="pop"><img src="'.$row['image_path'].'" alt="residence_image" class="img-circle" width="40"></span>';
    }else{
        $image = '<span style="cursor: pointer;" class="pop"><img src="../assets/dist/img/blank_image.png" alt="residence_image" class="img-circle"  width="40"></span>';
    }

    // Middle Name Logic
    if($row['middle_name'] != ''){
        $middle_name = ucfirst($row['middle_name'])[0].'.';
    }else{
        $middle_name = '';
    }

    // --- RESIDENCY TYPE LOGIC ---
    // We clean the data to uppercase and remove extra spaces
    $db_residency_type = isset($row['residency_type']) ? trim(strtoupper($row['residency_type'])) : '';

    if ($db_residency_type === 'RESIDENT') {
        $residency_type_label = '<span class="badge badge-success text-md">RESIDENT</span>';
    } elseif ($db_residency_type === 'TENANT' || $db_residency_type === 'WORKER') {
        // Updated to include TENANT logic or keep WORKER if legacy data exists
        $label_text = $db_residency_type === 'TENANT' ? 'TENANT' : 'WORKER';
        $residency_type_label = '<span class="badge badge-danger text-md">'.$label_text.'</span>';
    } else {
        $display_text = !empty($db_residency_type) ? $db_residency_type : 'N/A';
        $residency_type_label = '<span class="badge badge-secondary text-md">'.$display_text.'</span>';
    }
    // ----------------------------

    // Single Parent Logic
    if($row['single_parent'] == 'YES'){
        $single_parent = '<span class="badge badge-info text-md ">'.$row['single_parent'].'</span>';
    }else{
        $single_parent = '<span class="badge badge-warning text-md ">'.$row['single_parent'].'</span>';
    }

    // PWD Logic - UPDATED
    if($row['pwd'] == 'YES'){
        $pwd_display = $row['pwd_info'];
    }else{
        $pwd_display = 'NO';
    }

    // Status Logic
    if($row['status'] == 'ACTIVE'){
        $status = '<label class="switch">
                    <input type="checkbox" class="editStatus" data-status="ACTIVE"  id="'.$row['residence_id'].'"  checked disabled>
                    <div class="slider round">
                        <span class="on ">ACTIVE</span>
                        <span class="off ">INACTIVE</span>
                    </div>
                </label>';
    }else{
        $status = '<label class="switch">
                    <input type="checkbox" class="editStatus" id="'.$row['residence_id'].'" data-status="INACTIVE" disabled>
                    <div class="slider round">
                        <span class="off ">INACTIVE</span>
                        <span class="on ">ACTIVE</span>
                    </div>
                </label> ';
    }

    // Build the data row
    $subdata = [];
    $subdata[] = $image;
    $subdata[] = $row['residence_id'];
    $subdata[] = ucfirst($row['first_name']).' '. $middle_name .' '. ucfirst($row['last_name']); 
    $subdata[] = $row['age'];
    $subdata[] = $pwd_display; // Updated to show correct logic
    $subdata[] = $single_parent; 
    $subdata[] = $residency_type_label;
    $subdata[] = $status;
    $subdata[] = '<i style="cursor: pointer;  color: yellow;  text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" class="fa fa-user-edit text-lg px-3 viewResidence" id="'.$row['residence_id'].'"></i>
                  <i style="cursor: pointer;  color: red;  text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" class="fa fa-times text-lg px-2 unArchiveResidence" id="'.$row['residence_id'].'"></i>';
    $data[] = $subdata;
}

$json_data = [
    'draw' => intval($_REQUEST['draw']),
    'recordsTotal' => intval($totalData),
    'recordsFiltered' => intval($totalFiltered),
    'data' => $data,
];

echo json_encode($json_data);
?>