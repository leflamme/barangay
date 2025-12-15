<?php 
session_start();
include_once '../connection.php';

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
  $stmt_user->close(); 
}else{
 echo '<script>window.location.href = "../login.php";</script>';
 exit();
}

try{
  if(isset($_REQUEST['residence_id'])){
    $residence_id = $con->real_escape_string(trim($_REQUEST['residence_id']));
    
    // --- 1. ARCHIVE THE RESIDENT ---
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
    $stmt_archive = $con->prepare($sql_archive_residence_information) or die($con->error);
    $stmt_archive->bind_param('ssss',$archive_status,$date_archive,$residence_status,$residence_id);
    $stmt_archive->execute();
    $stmt_archive->close();

    // --- 2. SUCCESSION LOGIC (FIXED) ---
    
    // A. Check 'household_members' directly to see if this person is the head
    // We assume 'user_id' in this table matches the $residence_id
    $sql_check_member = "SELECT household_id, is_head FROM household_members WHERE user_id = ?";
    $stmt_check = $con->prepare($sql_check_member);
    $stmt_check->bind_param('s', $residence_id);
    $stmt_check->execute();
    $res_member = $stmt_check->get_result();
    
    if ($res_member->num_rows > 0) {
        $row_member = $res_member->fetch_assoc();
        $is_head_flag = $row_member['is_head'];
        $current_household_id = $row_member['household_id'];

        // If they are the head (is_head == 1), we must find a successor
        if ($is_head_flag == 1) {
            
            // B. Find Successor (Spouse > Child)
            // Note: We use UPPER() to be safe with casing (Wife vs WIFE)
            $sql_find_successor = "
                SELECT hm.user_id 
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

                // --- C. APPLY UPDATES ---

                // 1. Update HOUSEHOLDS table (The main record)
                // We update based on household_id
                $sql_upd_households = "UPDATE households SET household_head_id = ? WHERE household_id = ?";
                $stmt_upd_h = $con->prepare($sql_upd_households);
                $stmt_upd_h->bind_param('ss', $new_head_id, $current_household_id);
                $stmt_upd_h->execute();
                $stmt_upd_h->close();

                // 2. Update HOUSEHOLD_MEMBERS table
                // Demote Old Head
                $sql_demote = "UPDATE household_members SET is_head = 0 WHERE user_id = ?";
                $stmt_demote = $con->prepare($sql_demote);
                $stmt_demote->bind_param('s', $residence_id);
                $stmt_demote->execute();
                $stmt_demote->close();

                // Promote New Head
                $sql_promote = "UPDATE household_members SET is_head = 1, relationship_to_head = 'Head' WHERE user_id = ?";
                $stmt_promote = $con->prepare($sql_promote);
                $stmt_promote->bind_param('s', $new_head_id);
                $stmt_promote->execute();
                $stmt_promote->close();

                // 3. Update USERS table (Vital for UI display)
                // Assuming 'id' in users table matches $residence_id. 
                // IF NOT, change 'id' to 'residence_id' in the queries below.

                // Fix Old Head in Users (Strip the 'Head' title)
                $sql_fix_users_old = "UPDATE users SET relationship_to_head = 'Deceased' WHERE id = ?";
                $stmt_u_old = $con->prepare($sql_fix_users_old);
                $stmt_u_old->bind_param('s', $residence_id);
                $stmt_u_old->execute();
                $stmt_u_old->close();

                // Fix New Head in Users (Give 'Head' title)
                $sql_fix_users_new = "UPDATE users SET relationship_to_head = 'Head' WHERE id = ?";
                $stmt_u_new = $con->prepare($sql_fix_users_new);
                $stmt_u_new->bind_param('s', $new_head_id);
                $stmt_u_new->execute();
                $stmt_u_new->close();

                // Log system action
                $sys_msg = "SYSTEM: Transferred Head from $residence_id to $new_head_id";
                $sys_stat = "system";
                $sql_log_sys = "INSERT INTO activity_log (`message`,`date`,`status`) VALUES (?,?,?)";
                $stmt_sys = $con->prepare($sql_log_sys);
                $stmt_sys->bind_param('sss', $sys_msg, $date_archive, $sys_stat);
                $stmt_sys->execute();
                $stmt_sys->close();
            }
            $stmt_successor->close();
        }
    }
    $stmt_check->close();

    // --- 3. LOG ACTIVITY ---
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