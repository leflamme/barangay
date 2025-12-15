<?php 
session_start();
include_once '../connection.php';

// 1. Authentication Check
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
  $user_type = $row_user['user_type'];
  $user_image = $row_user['image'];

}else{
 echo '<script>
        window.location.href = "../login.php";
      </script>';
 exit(); // Good practice to stop script execution after redirect
}

try{
  if(isset($_REQUEST['residence_id'])){
    $residence_id = $con->real_escape_string(trim($_REQUEST['residence_id']));
    
    // 2. Archive the Resident (Soft Delete)
    $archive_status = 'YES';
    $residence_status = 'INACTIVE';
    $date_archive = date("m/d/Y h:i A");

    // Fetch name for logs
    $sql_check_resident = "SELECT first_name, last_name FROM residence_information WHERE residence_id = ?";
    $stmt_check_resident = $con->prepare($sql_check_resident) or die ($con->error);
    $stmt_check_resident->bind_param('s',$residence_id);
    $stmt_check_resident->execute();
    $result_check_resident = $stmt_check_resident->get_result();
    $row_resident_check = $result_check_resident->fetch_assoc();
    $first_name = $row_resident_check['first_name'];
    $last_name = $row_resident_check['last_name'];
    $stmt_check_resident->close();

    // Perform the update
    $sql_archive_residence_information = "UPDATE `residence_status` SET `archive` = ?, `date_archive` = ?,  `status` = ? WHERE `residence_id` = ?";
    $stmt_archive_residence_information = $con->prepare($sql_archive_residence_information) or die($con->error);
    $stmt_archive_residence_information->bind_param('ssss',$archive_status,$date_archive,$residence_status,$residence_id);
    $stmt_archive_residence_information->execute();
    $stmt_archive_residence_information->close();

    // ---------------------------------------------------------
    // --- SUCCESSION LOGIC START: CHECK HOUSEHOLD HEAD ---
    // ---------------------------------------------------------
    
    // A. Check if the person we just archived was a Head of Household
    $sql_check_head = "SELECT id, household_id FROM households WHERE household_head_id = ?";
    $stmt_check_head = $con->prepare($sql_check_head);
    $stmt_check_head->bind_param('s', $residence_id);
    $stmt_check_head->execute();
    $res_head = $stmt_check_head->get_result();

    if($res_head->num_rows > 0) {
        $row_head = $res_head->fetch_assoc();
        $current_household_id = $row_head['household_id'];
        $table_primary_id = $row_head['id']; // ID of the household row

        // B. Find the Successor
        // We look for ACTIVE members in the same household, excluding the person we just deleted.
        // We prioritize Wife/Husband (Level 1), then Children (Level 2), then others (Level 3).
        // NOTE: Please ensure 'birthdate' exists in residence_information. If not, remove "ri.birthdate ASC".
        
        $sql_find_successor = "
            SELECT hm.user_id, hm.relationship_to_head
            FROM household_members hm
            JOIN residence_status rs ON hm.user_id = rs.residence_id
            JOIN residence_information ri ON hm.user_id = ri.residence_id 
            WHERE hm.household_id = ? 
            AND rs.status = 'ACTIVE' 
            AND hm.user_id != ?
            ORDER BY 
                CASE 
                    WHEN hm.relationship_to_head IN ('Wife', 'Husband', 'Spouse') THEN 1 
                    WHEN hm.relationship_to_head IN ('Son', 'Daughter', 'Child') THEN 2 
                    ELSE 3 
                END ASC,
                ri.birthdate ASC 
            LIMIT 1
        ";

        $stmt_successor = $con->prepare($sql_find_successor);
        $stmt_successor->bind_param('ss', $current_household_id, $residence_id);
        $stmt_successor->execute();
        $res_successor = $stmt_successor->get_result();

        if($res_successor->num_rows > 0){
            // Successor Found
            $row_successor = $res_successor->fetch_assoc();
            $new_head_id = $row_successor['user_id'];

            // C. Perform the Transfer Updates

            // 1. Update the main 'households' table
            $sql_update_household = "UPDATE households SET household_head_id = ? WHERE id = ?";
            $stmt_update_household = $con->prepare($sql_update_household);
            $stmt_update_household->bind_param('ss', $new_head_id, $table_primary_id);
            $stmt_update_household->execute();
            $stmt_update_household->close();

            // 2. Update 'household_members' table 
            
            // Reset old head (ensure they are no longer marked as head)
            $update_old_member = "UPDATE household_members SET is_head = 0 WHERE user_id = ?";
            $stmt_old = $con->prepare($update_old_member);
            $stmt_old->bind_param('s', $residence_id);
            $stmt_old->execute();
            $stmt_old->close();

            // Set new head (mark them as head and update relationship string)
            $update_new_member = "UPDATE household_members SET is_head = 1, relationship_to_head = 'Head' WHERE user_id = ?";
            $stmt_new = $con->prepare($update_new_member);
            $stmt_new->bind_param('s', $new_head_id);
            $stmt_new->execute();
            $stmt_new->close();

            // Optional: Log the succession event in activity_log
            $admin_log_msg = "SYSTEM AUTO-TRANSFER: Household Head changed from $residence_id to $new_head_id";
            $sql_log_sys = "INSERT INTO activity_log (`message`,`date`,`status`) VALUES (?,?,?)";
            $stmt_log_sys = $con->prepare($sql_log_sys);
            $sys_status = 'system'; 
            $stmt_log_sys->bind_param('sss', $admin_log_msg, $date_archive, $sys_status);
            $stmt_log_sys->execute();
            $stmt_log_sys->close();
        }
        $stmt_successor->close();
    }
    $stmt_check_head->close();
    // ---------------------------------------------------------
    // --- SUCCESSION LOGIC END --------------------------------
    // ---------------------------------------------------------
    
    // 3. Log the Delete Activity
    $date_activity = $now = date("j-n-Y g:i A");  
    $admin = strtoupper('OFFICAL').': ' .$first_name_user.' '.$last_name_user. ' - ' .$user_id.' | '. 'DELETED RESIDENT - '.' ' .$residence_id.' | '  .' - '.$first_name .' '. $last_name;
    $status_activity_log = 'update';
    $sql_activity_log = "INSERT INTO activity_log (`message`,`date`,`status`)VALUES(?,?,?)";
    $stmt_activity_log = $con->prepare($sql_activity_log) or die ($con->error);
    $stmt_activity_log->bind_param('sss',$admin,$date_activity,$status_activity_log);
    $stmt_activity_log->execute();
    $stmt_activity_log->close();
  }

}catch(Exception $e){
  echo $e->getMessage();
}

?>