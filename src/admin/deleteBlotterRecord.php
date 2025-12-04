<?php 
include_once '../connection.php';
header('Content-Type: application/json'); // Return JSON to frontend

try{
  if(isset($_REQUEST['id'])){
    // 1. Prepare the IDs (Wrap them in quotes for SQL)
    $raw_ids = $_REQUEST['id'];
    $id_array = explode(",", $raw_ids);
    $clean_ids = [];
    foreach($id_array as $id){
        $clean_ids[] = $con->real_escape_string(trim($id));
    }
    // Result: '2025-11-15-0002', '2025-11-15-0003'
    $formatted_ids_string = "'" . implode("','", $clean_ids) . "'";

    // --- DELETE CHILDREN FIRST (To fix Foreign Key Constraint) ---

    // 2. Delete Complainants
    $sql_complainant = "DELETE FROM blotter_complainant WHERE blotter_main IN ($formatted_ids_string)";
    if(!$con->query($sql_complainant)){
        throw new Exception("Error deleting complainants: " . $con->error);
    }

    // 3. Delete Status/Person Involved
    $sql_status = "DELETE FROM blotter_status WHERE blotter_main IN ($formatted_ids_string)";
    if(!$con->query($sql_status)){
        throw new Exception("Error deleting status: " . $con->error);
    }

    // --- DELETE PARENT LAST ---

    // 4. Delete Blotter Record
    $sql_record = "DELETE FROM blotter_record WHERE blotter_id IN ($formatted_ids_string)";
    if(!$con->query($sql_record)){
        throw new Exception("Error deleting record: " . $con->error);
    }

    // 5. Log it (Optional but recommended)
    $date_activity = date("j-n-Y g:i A");
    $message = "ADMIN: DELETED BLOTTER RECORDS - " . $raw_ids;
    $sql_log = "INSERT INTO activity_log (`message`,`date`,`status`) VALUES (?,?, 'delete')";
    $stmt_log = $con->prepare($sql_log);
    $stmt_log->bind_param('ss', $message, $date_activity);
    $stmt_log->execute();

    echo json_encode(['status' => 'success']);

  } else {
    echo json_encode(['status' => 'error', 'message' => 'No ID provided']);
  }

}catch(Exception $e){
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>