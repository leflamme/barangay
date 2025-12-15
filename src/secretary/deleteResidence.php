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
  // Close stmt_user to avoid "Commands out of sync" errors
  $stmt_user->close(); 

}else{
 echo '<script>
        window.location.href = "../login.php";
      </script>';
 exit();
}

try{
  if(isset($_REQUEST['residence_id'])){
    $residence_id = $con->real_escape_string(trim($_REQUEST['residence_id']));
    
    // --- STEP 1: ARCHIVE THE RESIDENT (Your original logic) ---
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

    // Update status to INACTIVE
    $sql_archive_residence_information = "UPDATE `residence_status` SET `archive` = ?, `date_archive` = ?,  `status` = ? WHERE `residence_id` = ?";
    $stmt_archive_residence_information = $con->prepare($sql_archive_residence_information) or die($con->error);
    $stmt_archive_residence_information->bind_param('ssss',$archive_status,$date_archive,$residence_status,$residence_id);
    $stmt_archive_residence_information->execute();
    $stmt_archive_residence_information->close();

    // ---------------------------------------------------------
    // --- SUCCESSION LOGIC START ------------------------------
    // ---------------------------------------------------------
    
    // A. Check if the person we just archived was a Head of Household
    // We check the households table to see if this ID is listed as the head
    $sql_check_head = "SELECT id, household_id FROM households WHERE household_head_id = ?";
    $stmt_check_head = $con->prepare($sql_check_head);
    $stmt_check_head->bind_param('s', $residence_id);
    $stmt_check_head->execute();
    $res_head = $stmt_check_head->get_result();

    if($res_head->num_rows > 0) {
        // Person was a Head
        $row_head = $res_head->fetch_assoc();
        $current_household_id = $row_head['household_id'];
        $households_primary_id = $row_head['id']; 

        // B. Find the Successor
        // We assume 'users' table or 'household_members' contains relationship info.
        // We use UPPER() to ensure 'Wife' matches 'WIFE' or 'wife'.
        // We removed birthdate sorting to prevent crashes if column is missing.
        
        $sql_find_successor = "
            SELECT hm.user_id, hm.relationship_to_head
            FROM household_members hm
            JOIN residence_status rs ON hm.user_id = rs.residence_id
            WHERE hm.household_id = ? 
            AND rs.status = 'ACTIVE' 
            AND hm.user_id != ?
            ORDER BY 
                CASE 
                    WHEN UPPER(hm.relationship_to_head) IN ('WIFE', 'HUSBAND', 'SPOUSE') THEN 1 
                    WHEN UPPER(hm.relationship_to_head) IN ('SON', 'DAUGHTER', 'CHILD') THEN 2 
                    ELSE 3 
                END ASC,
                hm.id ASC 
            LIMIT 1
        ";

        $stmt_successor = $con->prepare($sql_find_successor);
        $stmt_successor->bind_param('ss', $current_household_id, $residence_id);
        $stmt_successor->execute();
        $res_successor = $stmt_successor->get_result();

        if($res_successor->num_rows > 0){
            $row_successor = $res_successor->fetch_assoc();
            $new_head_id = $row_successor['user_id'];

            // --- C. PERFORM UPDATES ON ALL 3 TABLES ---

            // 1. Update 'households' table (The master record)
            $sql_update_household = "UPDATE households SET household_head_id = ? WHERE id = ?";
            $stmt_update_household = $con->prepare($sql_update_household);
            $stmt_update_household->bind_param('ss', $new_head_id, $households_primary_id);
            $stmt_update_household->execute();
            $stmt_update_household->close();

            // 2. Update 'household_members' table (The linking table)
            // Demote the old head
            $update_old_member = "UPDATE household_members SET is_head = 0 WHERE user_id = ?";
            $stmt_old = $con->prepare($update_old_member);
            $stmt_old->bind_param('s', $residence_id);
            $stmt_old->execute();
            $stmt_old->close();

            // Promote the new head
            $update_new_member = "UPDATE household_members SET is_head = 1, relationship_to_head = 'Head' WHERE user_id = ?";
            $stmt_new = $con->prepare($update_new_member);
            $stmt_new->bind_param('s', $new_head_id);
            $stmt_new->execute();
            $stmt_new->close();

            // 3. Update 'users' table (The display table)
            // IMPORTANT: This was missing before. We must update the users table too.
            
            // Fix old head in users table (optional, maybe set to 'Former Head' or keep as is)
            // We usually just leave them archived, but let's focus on the NEW head.
            
            // Promote new head in users table
            $update_users_new = "UPDATE users SET relationship_to_head = 'Head' WHERE id = ?"; 
            // NOTE: Assuming 'id' in users table matches 'user_id' / 'residence_id'
            // If users table uses 'residence_id' column, change 'WHERE id' to 'WHERE residence_id'
            $stmt_users_new = $con->prepare($update_users_new);
            $stmt_users_new->bind_param('s', $new_head_id);
            $stmt_users_new->execute();
            $stmt_users_new->close();

            // Log it
            error_log("Succession: $residence_id replaced by $new_head_id");
        } else {
            error_log("Succession Failed: No active successor found for household $current_household_id");
        }
        $stmt_successor->close();
    }
    $stmt_check_head->close();
    // ---------------------------------------------------------
    // --- SUCCESSION LOGIC END --------------------------------
    // ---------------------------------------------------------

    // --- STEP 3: LOG ACTIVITY ---
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