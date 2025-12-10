<?php 
session_start();
include_once '../connection.php';

// 1. SECURITY: Check if user is logged in as secretary
if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'secretary'){
    $user_id = $_SESSION['user_id'];
    $sql_user = "SELECT * FROM `users` WHERE `id` = ? ";
    $stmt_user = $con->prepare($sql_user) or die ($con->error);
    $stmt_user->bind_param('s',$user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $row_user = $result_user->fetch_assoc();
    $first_name_user = $row_user['first_name'];
    $last_name_user = $row_user['last_name'];
} else {
    // If not authorized, stop immediately
    echo "Error: Unauthorized access.";
    exit();
}

try{
  if(isset($_REQUEST['id'])){
    $blotter_id = $con->real_escape_string($_REQUEST['id']);

    // 2. ACTIVITY LOG (Get data before deleting)
    $sql_blotter = "SELECT * FROM blotter_record WHERE blotter_id IN ($blotter_id) LIMIT 1";
    $stmt_blotter = $con->query($sql_blotter);
    
    if($stmt_blotter && $stmt_blotter->num_rows > 0){
        $row_blotter = $stmt_blotter->fetch_assoc();
        $old_date_incident = $row_blotter['date_incident'];
        $old_date_reported = $row_blotter['date_reported'];
        $old_location_incident = $row_blotter['location_incident'];

        $date_activity = date("j-n-Y g:i A");  
        $admin = strtoupper('OFFICIAL').': ' .$first_name_user.' '.$last_name_user. ' - ' .$user_id.' | DELETED BLOTTER RECORD - ' .$blotter_id;
        $status_activity_log = 'delete';

        $sql_activity_log = "INSERT INTO activity_log (`message`,`date`,`status`) VALUES (?,?,?)";
        $stmt_activity_log = $con->prepare($sql_activity_log);
        if($stmt_activity_log){
            $stmt_activity_log->bind_param('sss',$admin,$date_activity,$status_activity_log);
            $stmt_activity_log->execute();
            $stmt_activity_log->close();
        }
    }

    // 3. DELETE EXECUTION (Order is Critical: Children First -> Parent Last)
    
    // A. Delete from blotter_complainant (Child Table)
    $sql_delete_complainant = "DELETE FROM blotter_complainant WHERE blotter_main IN ($blotter_id)";
    if(!$con->query($sql_delete_complainant)){
        die("Error deleting complainant: " . $con->error);
    }

    // B. Delete from blotter_status (Child Table)
    $sql_delete_status = "DELETE FROM blotter_status WHERE blotter_main IN ($blotter_id)";
    if(!$con->query($sql_delete_status)){
        die("Error deleting status: " . $con->error);
    }

    // C. Delete from blotter_record (Main/Parent Table)
    $sql_delete_record = "DELETE FROM blotter_record WHERE blotter_id IN ($blotter_id)";
    if(!$con->query($sql_delete_record)){
        die("Error deleting main record: " . $con->error);
    }

    // If we reached here, everything worked
    echo "success";
  }
} catch(Exception $e){
  echo "Error: " . $e->getMessage();
}
?>