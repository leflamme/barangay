<?php 
include_once '../connection.php';

header('Content-Type: application/json');

// --- 1. CAPTURE DATA (Safe Handling) ---
$archive_status = trim('YES');
$first_name = $con->real_escape_string($_POST['first_name'] ?? '');
$middle_name = $con->real_escape_string($_POST['middle_name'] ?? '');
$last_name = $con->real_escape_string($_POST['last_name'] ?? '');
$resident_id = $con->real_escape_string($_POST['resident_id'] ?? '');

// CHANGED: Capture residency_type instead of voters
$residency_type = $con->real_escape_string($_POST['residency_type'] ?? '');

$whereClause = [];

// --- 2. BUILD FILTER LOGIC ---
if(!empty($resident_id)) {
    $whereClause[] = "residence_information.residence_id='$resident_id'";
}

if(!empty($first_name)) {
    $whereClause[] = "first_name LIKE '%" .$first_name. "%'";
}

if(!empty($middle_name)) {
    $whereClause[] = "middle_name LIKE '%" .$middle_name. "%'";
}

if(!empty($last_name)) {
    $whereClause[] = "last_name LIKE '%" .$last_name. "%'";
}

// CHANGED: Filter by residency_type
if (!empty($residency_type)) {
    $whereClause[] = "residence_status.residency_type = '{$residency_type}'";
}

$where = '';
if(count($whereClause) > 0){
  $where .= ' AND ' .implode(' AND ', $whereClause);
}

// --- 3. COUNT TOTALS (For Pagination) ---
$sql_count = "SELECT COUNT(residence_information.residence_id) as total 
              FROM residence_information 
              INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id 
              WHERE residence_status.archive = '$archive_status'" . $where;

$query_count = $con->query($sql_count) or die($con->error);
$row_count = $query_count->fetch_assoc();
$totalFiltered = $row_count['total'];

// Get total records without filter
$sql_total = "SELECT COUNT(residence_id) as total FROM residence_status WHERE archive = '$archive_status'";
$query_total = $con->query($sql_total);
$row_total = $query_total->fetch_assoc();
$totalData = $row_total['total'];

// --- 4. MAIN SELECT QUERY ---
// CHANGED: Selected residency_type instead of voters
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
residence_status.date_archive  
FROM residence_information 
INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id 
WHERE residence_status.archive = '$archive_status'" .$where;

// --- 5. ORDERING LOGIC (Fixed 'oder' typo and mapped columns) ---
$columns = [
    0 => 'residence_information.image_path',
    1 => 'residence_information.residence_id',
    2 => 'residence_information.first_name',
    3 => 'residence_information.age',
    4 => 'residence_status.pwd_info',
    5 => 'residence_status.single_parent',
    6 => 'residence_status.residency_type', // Maps to the new column
    7 => 'residence_status.status',
    8 => 'residence_status.date_archive'
];

if (isset($_REQUEST['order']) && isset($_REQUEST['order'][0])) {
    $columnIndex = $_REQUEST['order'][0]['column'];
    $columnName = $columns[$columnIndex] ?? 'residence_status.date_archive';
    $dir = $_REQUEST['order'][0]['dir'] ?? 'desc';
    
    $sql .= " ORDER BY $columnName $dir ";
} else {
    $sql .= " ORDER BY residence_status.date_archive DESC ";
}

// --- 6. PAGINATION LIMIT ---
if($_REQUEST['length'] != -1){
  $start = $_REQUEST['start'];
  $length = $_REQUEST['length'];
  $sql .= " LIMIT $start, $length";
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

  // Name Logic
  if($row['middle_name'] != ''){
    $middle_name = ucfirst($row['middle_name'])[0].'.';
  }else{
    $middle_name = '';
  }

  // CHANGED: Logic for Residency Type Badge
  if(strtoupper($row['residency_type']) == 'RESIDENT'){
    $residency_type_label = '<span class="badge badge-success text-md">RESIDENT</span>';
  } else {
    $residency_type_label = '<span class="badge badge-danger text-md">WORKER</span>';
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
  $subdata[] = $row['pwd_info']; 
  $subdata[] = $single_parent; 
  $subdata[] = $residency_type_label; // Display the new label
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
?>