<?php 
include_once '../connection.php';

// Added from 1.php: Ensure JSON header
header('Content-Type: application/json');

try{

    // --- 1. CAPTURE DATA (Safe Handling) ---
    $archive_status = trim('YES');
    $first_name = $con->real_escape_string($_POST['first_name'] ?? '');
    $middle_name = $con->real_escape_string($_POST['middle_name'] ?? '');
    $last_name = $con->real_escape_string($_POST['last_name'] ?? '');
    $resident_id = $con->real_escape_string($_POST['resident_id'] ?? '');
    $residency_type = $con->real_escape_string($_POST['residency_type'] ?? '');

    $whereClause = [];

    // --- 2. BUILD FILTER LOGIC ---
    if(!empty($resident_id)) {
        $whereClause[] = "residence_information.residence_id = '$resident_id'";
    }

    if(!empty($first_name)) {
        $whereClause[] = "residence_information.first_name LIKE '%" .$first_name. "%'";
    }

    if(!empty($middle_name)) {
        $whereClause[] = "residence_information.middle_name LIKE '%" .$middle_name. "%'";
    }

    if(!empty($last_name)) {
        $whereClause[] = "residence_information.last_name LIKE '%" .$last_name. "%'";
    }

    // Filter by residency_type using UPPER for consistency
    if (!empty($residency_type)) {
        $whereClause[] = "UPPER(residence_status.residency_type) = '" . strtoupper($residency_type) . "'";
    }

    $where = '';
    if(count($whereClause) > 0){
        $where .= ' AND ' .implode(' AND ', $whereClause);
    }

    // --- 3. COUNT TOTALS (For Pagination) ---
    // Count filtered records
    $sql_count = "SELECT COUNT(residence_information.residence_id) as total 
                  FROM residence_information 
                  INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id 
                  WHERE residence_status.archive = '$archive_status'" . $where;

    $query_count = $con->query($sql_count) or die($con->error);
    $row_count = $query_count->fetch_assoc();
    $totalFiltered = intval($row_count['total']);

    // Get total records without filter
    $sql_total = "SELECT COUNT(residence_id) as total FROM residence_status WHERE archive = '$archive_status'";
    $query_total = $con->query($sql_total);
    $row_total = $query_total->fetch_assoc();
    $totalData = intval($row_total['total']);

    // --- 4. MAIN SELECT QUERY ---
    $sql = "SELECT 
        residence_information.residence_id, 
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
        residence_status.date_archive  
    FROM residence_information 
    INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id 
    WHERE residence_status.archive = '$archive_status'" .$where;

    // --- 5. ORDERING LOGIC ---
    $columns = [
        0 => 'residence_information.image_path',
        1 => 'residence_information.residence_id',
        2 => 'residence_information.first_name',
        3 => 'residence_information.age',
        4 => 'residence_status.pwd', // Fixed: Sort by pwd status, not info
        5 => 'residence_status.single_parent',
        6 => 'residence_status.residency_type', 
        7 => 'residence_status.status',
        8 => 'residence_status.date_archive'
    ];

    if (isset($_REQUEST['order']) && isset($_REQUEST['order'][0])) {
        $columnIndex = intval($_REQUEST['order'][0]['column']);
        $columnName = $columns[$columnIndex] ?? 'residence_status.date_archive';
        $dir = strtolower($_REQUEST['order'][0]['dir']) === 'asc' ? 'ASC' : 'DESC';
        
        $sql .= " ORDER BY $columnName $dir ";
    } else {
        $sql .= " ORDER BY residence_status.date_archive DESC ";
    }

    // --- 6. PAGINATION LIMIT ---
    if(isset($_REQUEST['length']) && $_REQUEST['length'] != -1){
        $start = intval($_REQUEST['start']);
        $length = intval($_REQUEST['length']);
        $sql .= " LIMIT $start, $length";
    }

    $query = $con->query($sql) or die ($con->error);
    $data = [];

    while($row = $query->fetch_assoc()){
      
        // Image Logic
        if(!empty($row['image_path'])){
            $image = '<span style="cursor: pointer;" class="pop"><img src="'.$row['image_path'].'" alt="residence_image" class="img-circle" width="40"></span>';
        }else{
            $image = '<span style="cursor: pointer;" class="pop"><img src="../assets/dist/img/blank_image.png" alt="residence_image" class="img-circle"  width="40"></span>';
        }

        // Name Logic (Middle Initial)
        if(!empty($row['middle_name'])){
            $middle_name = ucfirst($row['middle_name'])[0].'.';
        }else{
            $middle_name = '';
        }

        // --- Residency Type Badge Logic ---
        $db_residency_type_upper = strtoupper($row['residency_type']);

        if($db_residency_type_upper == 'RESIDENT'){
            $residency_type_label = '<span class="badge badge-success text-md">RESIDENT</span>';
        } else if ($db_residency_type_upper == 'TENANT') {
            $residency_type_label = '<span class="badge badge-danger text-md">TENANT</span>';
        } else {
            $residency_type_label = '<span class="badge badge-secondary text-md">'.$row['residency_type'].'</span>';
        }

        // --- FIXED: PWD Status Logic ---
        if($row['pwd'] == 'YES'){
            $pwd_status = '<span class="badge badge-info text-md">YES</span>';
        } else {
            // If empty or NO, default to NO
            $display_pwd = !empty($row['pwd']) ? $row['pwd'] : 'NO';
            $pwd_status = '<span class="badge badge-warning text-md">'.$display_pwd.'</span>';
        }

        // Single Parent Logic
        if($row['single_parent'] == 'YES'){
            $single_parent = '<span class="badge badge-info text-md ">'.$row['single_parent'].'</span>';
        }else{
            $single_parent = '<span class="badge badge-warning text-md ">'.$row['single_parent'].'</span>';
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

        $subdata = [];
        $subdata[] = $image;
        $subdata[] = $row['residence_id'];
        $subdata[] = ucfirst($row['first_name']).' '. $middle_name .' '. ucfirst($row['last_name']); 
        $subdata[] = $row['age'];
        $subdata[] = $pwd_status; // CHANGED: Now displays the PWD badge (Yes/No)
        $subdata[] = $single_parent; 
        $subdata[] = $residency_type_label; 
        $subdata[] = $status;
        
        $subdata[] = '<i style="cursor: pointer;  color: yellow;  text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" class="fa fa-user-edit text-lg px-3 viewResidence" id="'.$row['residence_id'].'"></i>
        <i style="cursor: pointer;  color: lime;  text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" class="fas fa-box-open text-lg px-2 unArchiveResidence" id="'.$row['residence_id'].'"></i>';
        
        $data[] = $subdata;
    }

    $json_data = [
        'draw' => intval($_REQUEST['draw']),
        'recordsTotal' => intval($totalData),
        'recordsFiltered' => intval($totalFiltered),
        'data' => $data,
    ];

    echo json_encode($json_data);

} catch(Exception $e){
    echo json_encode(['error' => $e->getMessage()]);
}
?>