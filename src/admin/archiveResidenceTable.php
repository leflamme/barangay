<?php 
include_once '../connection.php';

$archive_status = trim('YES');
$first_name = $con->real_escape_string($_POST['first_name']);
$middle_name = $con->real_escape_string($_POST['middle_name']);
$last_name = $con->real_escape_string($_POST['last_name']);
$resident_id = $con->real_escape_string($_POST['resident_id']);
// 1. ADDED: Input for residency_type
$residency_type = $con->real_escape_string($_POST['residency_type']);

$whereClause = [];

if(!empty($resident_id))  
$whereClause[] = "residence_information.residence_id='$resident_id'";

if(!empty($first_name))  
$whereClause[] = "first_name LIKE '%" .$first_name. "%'";

if(!empty($middle_name))  
$whereClause[] = "middle_name LIKE '%" .$middle_name. "%'";

if(!empty($last_name))  
$whereClause[] = "last_name LIKE '%" .$last_name. "%'";

// 2. ADDED: Filter logic for residency_type
if(!empty($residency_type))
$whereClause[] = "residence_status.residency_type='$residency_type'";


$where = '';

if(count($whereClause) > 0){
  $where .= ' AND ' .implode(' AND ', ($whereClause));
}

// 3. UPDATED: Selected residency_type instead of voters
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
residence_status.date_added  
FROM residence_information INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id WHERE residence_status.archive = '$archive_status'" .$where;


$query = $con->query($sql) or die ($con->error);
$totalData = $query->num_rows;
$totalFiltered = $totalData;


// 4. FIXED: Typo 'oder' to 'order'
if(isset($_REQUEST['order'])){
  $sql .= ' ORDER BY '.
  $_REQUEST['order']['0']['column'].
  ' '.
  $_REQUEST['order']['0']['dir'].
  ' ';
}else{
  $sql .= ' ORDER BY date_archive DESC ';
}

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
  if($row['image'] != '' || $row['image'] != null || !empty($row['image'])){
    $image = '<span style="cursor: pointer;" class="pop"><img src="'.$row['image_path'].'" alt="residence_image" class="img-circle" width="40"></span>';
  }else{
    $image = '<span style="cursor: pointer;" class="pop"><img src="../assets/dist/img/blank_image.png" alt="residence_image" class="img-circle"  width="40"></span>';
  }

  if($row['middle_name'] != ''){
    $middle_name = ucfirst($row['middle_name'])[0].'.';
  }else{
    $middle_name = '';
  }

  // 5. UPDATED: Logic for Residency Type Badges (Resident/Worker) instead of Voters
  if (isset($row['residency_type']) && strtoupper($row['residency_type']) === 'RESIDENT') {
    $residency_type_label = '<span class="badge badge-success text-md">RESIDENT</span>';
  } else {
    // Default to WORKER for non-resident types
    $residency_type_label = '<span class="badge badge-danger text-md">WORKER</span>';
  }

  if($row['single_parent'] == 'YES'){
    $single_parent = '<span class="badge badge-info text-md ">'.$row['single_parent'].'</span>';
  }else{
    $single_parent = '<span class="badge badge-warning text-md ">'.$row['single_parent'].'</span>';
  }

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
$subdata[] =  $row['residence_id'];
$subdata[] =  ucfirst($row['first_name']).' '. $middle_name .' '. ucfirst($row['last_name']); 
$subdata[] =  $row['age'];
$subdata[] =  $row['pwd_info']; 
$subdata[] =  $single_parent; 
// 6. UPDATED: Output residency type label
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
  'total' => intval($totalData),
];


echo json_encode($json_data);




?>