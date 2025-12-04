<?php 
include_once '../connection.php';
header('Content-Type: application/json'); // Return JSON so the frontend can read the error

try{
  if(isset($_REQUEST['id'])){
    $blotter_id = $con->real_escape_string($_REQUEST['id']);

    // 1. Log the Activity (Optional: Keep your logging logic)
    $sql_blotter = "SELECT * FROM blotter_record WHERE blotter_id IN ($blotter_id)";
    $stmt_blotter = $con->prepare($sql_blotter);
    if($stmt_blotter){
        $stmt_blotter->execute();
        $result_blotter = $stmt_blotter->get_result();
        $row_blotter = $result_blotter->fetch_assoc();

        if($row_blotter){
            $admin = strtoupper('ADMIN');
            $date_activity = date("j-n-Y g:i A");  
            $message = $admin.':' .' '. 'DELETED BLOTTER RECORD - '.' ' .$blotter_id;
            $status_activity_log = 'delete';
            
            $sql_activity_log = "INSERT INTO activity_log (`message`,`date`,`status`)VALUES(?,?,?)";
            $stmt_activity_log = $con->prepare($sql_activity_log);
            $stmt_activity_log->bind_param('sss',$message,$date_activity,$status_activity_log);
            $stmt_activity_log->execute();
        }
    }

    // --- THE FIX: DELETE CHILDREN FIRST, THEN PARENT ---

    // 2. Delete from 'blotter_complainant'
    $sql_delete_complainant = "DELETE FROM blotter_complainant WHERE blotter_main IN ($blotter_id)";
    if(!$con->query($sql_delete_complainant)){
        throw new Exception("Error deleting complainant links: " . $con->error);
    }

    // 3. Delete from 'blotter_status'
    $sql_delete_status = "DELETE FROM blotter_status WHERE blotter_main IN ($blotter_id)";
    if(!$con->query($sql_delete_status)){
        throw new Exception("Error deleting status links: " . $con->error);
    }

    // 4. Finally, delete from 'blotter_record'
    $sql_delete_record = "DELETE FROM blotter_record WHERE blotter_id IN ($blotter_id)";
    if(!$con->query($sql_delete_record)){
        throw new Exception("Error deleting main record: " . $con->error);
    }

    // Success Response
    echo json_encode(['status' => 'success']);

  } else {
    echo json_encode(['status' => 'error', 'message' => 'No ID provided']);
  }

}catch(Exception $e){
  // Error Response
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>