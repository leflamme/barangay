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
    
    // --- 1. ARCHIVE THE RESIDENT (Standard Procedure) ---
    $archive_status = 'YES';
    $residence_status = 'INACTIVE';
    $date_archive = date("m/d/Y h:i A");

    // Fetch name for logs
    $sql_check = "SELECT first_name, last_name, gender FROM residence_information WHERE residence_id = ?";
    $stmt_check = $con->prepare($sql_check);
    $stmt_check->bind_param('s',$residence_id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    $row_check = $res_check->fetch_assoc();
    $first_name = $row_check['first_name'];
    $last_name = $row_check['last_name'];
    $gender_of_archived = $row_check['gender']; // Needed for logic later
    $stmt_check->close();

    $sql_archive = "UPDATE `residence_status` SET `archive` = ?, `date_archive` = ?,  `status` = ? WHERE `residence_id` = ?";
    $stmt_a = $con->prepare($sql_archive);
    $stmt_a->bind_param('ssss',$archive_status,$date_archive,$residence_status,$residence_id);
    $stmt_a->execute();
    $stmt_a->close();

    // ---------------------------------------------------------
    // --- SIMPLIFIED TITLE SWAP LOGIC -------------------------
    // ---------------------------------------------------------
    
    // A. Is this person currently the Head?
    $sql_is_head = "SELECT household_id, is_head FROM household_members WHERE user_id = ?";
    $stmt_h = $con->prepare($sql_is_head);
    $stmt_h->bind_param('s', $residence_id);
    $stmt_h->execute();
    $res_h = $stmt_h->get_result();

    if($res_h->num_rows > 0){
        $row_h = $res_h->fetch_assoc();
        
        // ONLY RUN IF THEY ARE HEAD (is_head = 1)
        if($row_h['is_head'] == 1){
            $household_id = $row_h['household_id'];

            // B. Find the Wife/Husband (Priority) or Eldest Child
            $sql_find_new = "
                SELECT hm.user_id, ri.gender 
                FROM household_members hm
                JOIN residence_status rs ON hm.user_id = rs.residence_id
                JOIN residence_information ri ON hm.user_id = ri.residence_id
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
            
            $stmt_new = $con->prepare($sql_find_new);
            $stmt_new->bind_param('ss', $household_id, $residence_id);
            $stmt_new->execute();
            $res_new = $stmt_new->get_result();

            if($res_new->num_rows > 0){
                $row_new = $res_new->fetch_assoc();
                $new_head_id = $row_new['user_id'];
                $new_head_gender = $row_new['gender'];

                // C. DETERMINE TITLES (The Logic You Requested)
                // New Head becomes "Head".
                // Old Head becomes "Husband" if male, "Wife" if female (or just "Member" if not a spouse).
                
                $title_for_new_head = "Head";
                
                // Determine title for the OLD head based on their gender
                if(strtoupper($gender_of_archived) == 'MALE'){
                    $title_for_old_head = "Husband"; 
                } elseif(strtoupper($gender_of_archived) == 'FEMALE'){
                     $title_for_old_head = "Wife";
                } else {
                    $title_for_old_head = "Member";
                }

                // --- EXECUTE THE SWAP ---

                // 1. UPDATE OLD HEAD (The person being archived)
                // Set is_head = 0, and Change Title (e.g., to "Husband")
                $sql_demote_users = "UPDATE users SET relationship_to_head = ? WHERE id = ?";
                $stmt_d1 = $con->prepare($sql_demote_users);
                $stmt_d1->bind_param('ss', $title_for_old_head, $residence_id);
                $stmt_d1->execute();
                $stmt_d1->close();

                $sql_demote_members = "UPDATE household_members SET is_head = 0, relationship_to_head = ? WHERE user_id = ?";
                $stmt_d2 = $con->prepare($sql_demote_members);
                $stmt_d2->bind_param('ss', $title_for_old_head, $residence_id);
                $stmt_d2->execute();
                $stmt_d2->close();

                // 2. UPDATE NEW HEAD (The Successor)
                // Set is_head = 1, Change Title to "Head"
                $sql_promote_users = "UPDATE users SET relationship_to_head = ? WHERE id = ?";
                $stmt_p1 = $con->prepare($sql_promote_users);
                $stmt_p1->bind_param('ss', $title_for_new_head, $new_head_id);
                $stmt_p1->execute();
                $stmt_p1->close();

                $sql_promote_members = "UPDATE household_members SET is_head = 1, relationship_to_head = ? WHERE user_id = ?";
                $stmt_p2 = $con->prepare($sql_promote_members);
                $stmt_p2->bind_param('ss', $title_for_new_head, $new_head_id);
                $stmt_p2->execute();
                $stmt_p2->close();

                // 3. FIX HOUSEHOLD MASTER TABLE
                $sql_master = "UPDATE households SET household_head_id = ? WHERE household_id = ?";
                $stmt_m = $con->prepare($sql_master);
                $stmt_m->bind_param('ss', $new_head_id, $household_id);
                $stmt_m->execute();
                $stmt_m->close();
                
                // Log it
                $sys_msg = "SWAP: $residence_id became $title_for_old_head. $new_head_id became Head.";
                $sys_stat = "system";
                $sql_log_sys = "INSERT INTO activity_log (`message`,`date`,`status`) VALUES (?,?,?)";
                $stmt_sys = $con->prepare($sql_log_sys);
                $stmt_sys->bind_param('sss', $sys_msg, $date_archive, $sys_stat);
                $stmt_sys->execute();
                $stmt_sys->close();
            }
            $stmt_new->close();
        }
    }
    $stmt_h->close();

    // --- LOG ACTIVITY ---
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