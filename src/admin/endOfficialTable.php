<?php 
include_once '../connection.php';

try{
  // 1. Get the Position Filter
  $position = $con->real_escape_string($_REQUEST['position']);

  $whereClause = [];

  if(!empty($position)){
    $whereClause[] = "official_end_status.position='".$position."'";
  }

  // Note: We generally don't filter by status here because this page 
  // shows ALL history (End Term, Inactive, Archived, etc.)
  
  $where = '';

  if(count($whereClause) > 0){
    $where .= ' WHERE ' .implode(' AND ', $whereClause);
  }
  
  // 2. Main Query - MUST join official_end_status and official_end_information
  $sql = "SELECT 
    official_end_status.position, 
    official_end_status.voters, 
    official_end_status.status, 
    official_end_status.pwd, 
    official_end_status.single_parent, 
    official_end_information.official_id, 
    official_end_information.first_name, 
    official_end_information.middle_name, 
    official_end_information.last_name, 
    official_end_information.image, 
    official_end_information.image_path, 
    position.color, 
    position.position as official_position 
  FROM official_end_status
  INNER JOIN official_end_information ON official_end_status.official_id = official_end_information.official_id
  INNER JOIN position ON official_end_status.position = position.position_id" .$where;

  // 3. Search Functionality
  if($_REQUEST['search']['value']){
    // If WHERE clause exists, use AND, otherwise use WHERE
    $sql .= (strpos($sql, 'WHERE') !== false) ? " AND " : " WHERE ";
    
    $sql .= " (first_name LIKE '%" . $_REQUEST['search']['value']. "%' ";
    $sql .= " OR last_name LIKE '%" . $_REQUEST['search']['value']. "%' ";
    $sql .= " OR official_end_information.official_id LIKE '%" . $_REQUEST['search']['value']. "%' ";
    $sql .= " OR status LIKE '%" . $_REQUEST['search']['value']. "%' )";
  }

  // 4. Count Total Records (for Pagination)
  $stmt = $con->prepare($sql) or die ($con->error);
  $stmt->execute();
  $result_for_count = $stmt->get_result();
  $totalData = $result_for_count->num_rows;

  // 5. Ordering
  if(isset($_REQUEST['order'])){
    $sql .= ' ORDER BY '.
    $_REQUEST['order']['0']['column'].
    ' '.
    $_REQUEST['order']['0']['dir'].
    ' ';
  }else{
    $sql .= ' ORDER BY position ASC ';
  }

  // 6. Pagination Limit
  if($_REQUEST['length'] != -1){
    $sql .= ' LIMIT '.
    $_REQUEST['start'].
    ' ,'.
    $_REQUEST['length'].
    ' ';
  }

  $stmt = $con->prepare($sql) or die ($con->error);
  $stmt->execute();
  $result = $stmt->get_result();
  $data = [];

  while($row = $result->fetch_assoc()){
    
    // Image Handling
    if($row['image'] != '' || $row['image'] != null || !empty($row['image'])){
      $image = '<span style="cursor: pointer;" class="pop"><img src="'.$row['image_path'].'" alt="official_image" class="img-circle" width="40"></span>';
    }else{
      $image = '<span style="cursor: pointer;" class="pop"><img src="../assets/dist/img/blank_image.png" alt="official_image" class="img-circle"  width="40"></span>';
    }

    // Voters Badge
    if($row['voters'] == 'YES'){
      $voters = '<span class="badge badge-success text-md">'.$row['voters'].'</span>';
    }else{
      $voters = '<span class="badge badge-danger text-md">'.$row['voters'].'</span>';
    }
  
    // Middle Name Formatting
    if($row['middle_name'] != ''){
      $middle_name = ucfirst($row['middle_name'])[0].'.';
    }else{
      $middle_name = '';
    }

    // Single Parent Badge
    if($row['single_parent'] == 'YES'){
      $single_parent = '<span class="badge badge-info text-md ">'.$row['single_parent'].'</span>';
    }else{
      $single_parent = '<span class="badge badge-warning text-md ">'.$row['single_parent'].'</span>';
    }

    // Status Badge
    // This will show "INACTIVE" or "ARCHIVED" based on what deleteOfficial.php saved
    if($row['status'] == 'ACTIVE'){
        $status = '<span class="badge badge-success">ACTIVE</span>';
    } else {
        $status = '<span class="badge badge-danger">'.$row['status'].'</span>';
    }
  
    // Prepare Row Data
    $subdata = [];
    $subdata[] = $image;
    $subdata[] = '<span class="badge" style="background-color: '.$row['color'].'">'.$row['official_position'].'</span>';
    $subdata[] = $row['official_id'];
    $subdata[] = ucfirst($row['first_name']).' '. $middle_name .' '. ucfirst($row['last_name']); 
    $subdata[] = $row['pwd']; // Note: ensure column name matches DB (pwd vs pwd_info)
    $subdata[] = $single_parent;
    $subdata[] = $voters;
    $subdata[] = $status;

    // Action Buttons: Edit (Yellow) and Restore/Undelete (Red X/Trash)
    // Note: The deleteOfficial class here triggers the "Undelete" function in your officialEndTerm.php
    $subdata[] = '
    <a href="viewEndOfficial.php?request='.$row['official_id'].'" style="cursor: pointer;  color: yellow;  text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" class="fa fa-user-edit text-lg px-3 "></a>
    <i style="cursor: pointer;  color: red;  text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" class="fa fa-trash-restore text-lg px-2 deleteOfficial" id="'.$row['official_id'].'" title="Restore Official"></i>';
    
    $data[] = $subdata;
  }

  $json_data = [
    'draw' => intval($_REQUEST['draw']),
    'recordsTotal' => intval($totalData),
    'recordsFiltered' => intval($totalData),
    'data' => $data,
    'total' => intval($totalData),
  ];

  echo json_encode($json_data);

}catch(Exception $e){
  echo $e->getMessage();
}
?>