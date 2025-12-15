<?php 
session_start();
include_once '../connection.php';

// 1. Auth Check
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
    
    // Default to 'MEMBER' if not specified
    $restore_mode = isset($_REQUEST['restore_mode']) ? $_REQUEST['restore_mode'] : 'MEMBER';

    $archive_status = 'NO';
    $residence_status = 'ACTIVE';
    $date_archive = date("m/d/Y h:i A");

    // 1. PERFORM BASIC UNARCHIVE (Set to Active)
    $sql_archive = "UPDATE `residence_status` SET `archive` = ?, `date_unarchive` = ?,  `status` = ? WHERE `residence_id` = ?";
    $stmt_a = $con->prepare($sql_archive);
    $stmt_a->bind_param('ssss',$archive_status,$date_archive,$residence_status,$residence_id);
    $stmt_a->execute();
    $stmt_a->close();

    // Fetch Details for Logic
    $sql_check = "SELECT first_name, last_name, gender FROM residence_information WHERE residence_id = ?";
    $stmt_check = $con->prepare($sql_check);
    $stmt_check->bind_param('s',$residence_id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    $row_check = $res_check->fetch_assoc();
    $first_name = $row_check['first_name'];
    $last_name = $row_check['last_name'];
    $gender_restored = strtoupper($row_check['gender']);
    $stmt_check->close();

    // Get Household ID
    $sql_hh = "SELECT household_id FROM household_members WHERE user_id = ?";
    $stmt_hh = $con->prepare($sql_hh);
    $stmt_hh->bind_param('s', $residence_id);
    $stmt_hh->execute();
    $res_hh = $stmt_hh->get_result();
    $household_id = null;
    if($res_hh->num_rows > 0){
        $r = $res_hh->fetch_assoc();
        $household_id = $r['household_id'];
    }
    $stmt_hh->close();

    // ------------------------------------------------------------
    // --- MODE SPECIFIC LOGIC ---
    // ------------------------------------------------------------
    
    if($household_id) {

        if ($restore_mode == 'HEAD') {
            // === OPTION A: RESTORE AS HEAD (COUP) ===
            // 1. Find Current Head
            $sql_curr = "SELECT hm.user_id, ri.gender FROM household_members hm 
                         JOIN residence_status rs ON hm.user_id = rs.residence_id 
                         JOIN residence_information ri ON hm.user_id = ri.residence_id
                         WHERE hm.household_id = ? AND hm.is_head = 1 AND rs.status = 'ACTIVE'";
            $stmt_curr = $con->prepare($sql_curr);
            $stmt_curr->bind_param('s', $household_id);
            $stmt_curr->execute();
            $res_curr = $stmt_curr->get_result();

            if($res_curr->num_rows > 0){
                $row_curr = $res_curr->fetch_assoc();
                $current_head_id = $row_curr['user_id'];
                $current_head_gender = strtoupper($row_curr['gender']);

                // If the restored person is NOT already the head
                if($current_head_id != $residence_id){
                    
                    // Determine Title for Demoted Head
                    // If Demoted is Female -> Wife/Daughter. If Male -> Husband/Son.
                    // We assume if Wife is demoted, she becomes Wife. If Son is demoted, he becomes Son.
                    $demoted_title = "Member";
                    if($current_head_gender == 'FEMALE') {
                        $demoted_title = ($gender_restored == 'MALE') ? "Wife" : "Daughter"; 
                    } else {
                        $demoted_title = "Son"; // Assume son gave up seat for Father
                    }

                    // A. DEMOTE CURRENT HEAD
                    $sql_d = "UPDATE users SET relationship_to_head = ? WHERE id = ?";
                    $stmt_d = $con->prepare($sql_d);
                    $stmt_d->bind_param('ss', $demoted_title, $current_head_id);
                    $stmt_d->execute(); $stmt_d->close();

                    $sql_dm = "UPDATE household_members SET is_head = 0, relationship_to_head = ? WHERE user_id = ?";
                    $stmt_dm = $con->prepare($sql_dm);
                    $stmt_dm->bind_param('ss', $demoted_title, $current_head_id);
                    $stmt_dm->execute(); $stmt_dm->close();

                    // B. PROMOTE RESTORED PERSON
                    $sql_p = "UPDATE users SET relationship_to_head = 'Head' WHERE id = ?";
                    $stmt_p = $con->prepare($sql_p);
                    $stmt_p->bind_param('s', $residence_id);
                    $stmt_p->execute(); $stmt_p->close();

                    $sql_pm = "UPDATE household_members SET is_head = 1, relationship_to_head = 'Head' WHERE user_id = ?";
                    $stmt_pm = $con->prepare($sql_pm);
                    $stmt_pm->bind_param('s', $residence_id);
                    $stmt_pm->execute(); $stmt_pm->close();

                    // C. UPDATE MASTER TABLE
                    $sql_m = "UPDATE households SET household_head_id = ? WHERE household_id = ?";
                    $stmt_m = $con->prepare($sql_m);
                    $stmt_m->bind_param('ss', $residence_id, $household_id);
                    $stmt_m->execute(); $stmt_m->close();
                }
            } 
            // If No Active Head exists, simply make them head
            else {
                 $sql_p = "UPDATE users SET relationship_to_head = 'Head' WHERE id = ?";
                 $stmt_p = $con->prepare($sql_p); $stmt_p->bind_param('s', $residence_id); $stmt_p->execute(); $stmt_p->close();

                 $sql_pm = "UPDATE household_members SET is_head = 1, relationship_to_head = 'Head' WHERE user_id = ?";
                 $stmt_pm = $con->prepare($sql_pm); $stmt_pm->bind_param('s', $residence_id); $stmt_pm->execute(); $stmt_pm->close();

                 $sql_m = "UPDATE households SET household_head_id = ? WHERE household_id = ?";
                 $stmt_m = $con->prepare($sql_m); $stmt_m->bind_param('ss', $residence_id, $household_id); $stmt_m->execute(); $stmt_m->close();
            }

        } else {
            // === OPTION B: RESTORE AS MEMBER (MISTAKE) ===
            // Just ensure they are NOT marked as head (safety check)
            
            // Check if there is ALREADY another head.
            $sql_check_h = "SELECT user_id FROM household_members WHERE household_id = ? AND is_head = 1 AND user_id != ?";
            $stmt_ch = $con->prepare($sql_check_h);
            $stmt_ch->bind_param('ss', $household_id, $residence_id);
            $stmt_ch->execute();
            $res_ch = $stmt_ch->get_result();

            if($res_ch->num_rows > 0){
                // There is already a head, so ensure this restored person is just a member
                $sql_demote = "UPDATE household_members SET is_head = 0 WHERE user_id = ?";
                $stmt_demote = $con->prepare($sql_demote);
                $stmt_demote->bind_param('s', $residence_id);
                $stmt_demote->execute();
                $stmt_demote->close();
                
                // If they were previously Head, we might want to change their title to "Member" or "Husband" to avoid confusion, 
                // but usually, if it was a mistake, their old title is fine. 
                // However, if the wife was auto-promoted during the 'Archive' process, 
                // this guy might still have "Head" as his title text even if is_head=0.
                
                // FIX: Update title if it says "Head" but they are not head anymore
                $sql_fix_title = "UPDATE users SET relationship_to_head = 'Member' WHERE id = ? AND relationship_to_head = 'Head'";
                $stmt_ft = $con->prepare($sql_fix_title);
                $stmt_ft->bind_param('s', $residence_id);
                $stmt_ft->execute();
                $stmt_ft->close();
            }
        }
    }

    // 3. LOG ACTIVITY
    $date_activity = date("j-n-Y g:i A");  
    $log_msg = 'UNARCHIVED RESIDENT (' . $restore_mode . ') - ' . $first_name .' '. $last_name;
    $admin = strtoupper('OFFICIAL').': ' .$first_name_user.' '.$last_name_user. ' - ' .$user_id.' | '. $log_msg;
    $status_activity_log = 'update'; // 'delete' was in your old code, but 'update' is more accurate for unarchiving
    
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