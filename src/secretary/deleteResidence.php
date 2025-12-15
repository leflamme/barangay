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

    // Get info of person being archived
    $sql_check = "SELECT first_name, last_name, gender FROM residence_information WHERE residence_id = ?";
    $stmt_check = $con->prepare($sql_check);
    $stmt_check->bind_param('s',$residence_id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    $row_check = $res_check->fetch_assoc();
    $first_name = $row_check['first_name'];
    $last_name = $row_check['last_name'];
    $gender_old_head = strtoupper($row_check['gender']); 
    $stmt_check->close();

    $sql_archive = "UPDATE `residence_status` SET `archive` = ?, `date_archive` = ?,  `status` = ? WHERE `residence_id` = ?";
    $stmt_a = $con->prepare($sql_archive);
    $stmt_a->bind_param('ssss',$archive_status,$date_archive,$residence_status,$residence_id);
    $stmt_a->execute();
    $stmt_a->close();

    // --- 2. SUCCESSION & TITLE SWAP LOGIC ---
    
    // Check if this person was a Head
    $sql_is_head = "SELECT household_id, is_head FROM household_members WHERE user_id = ?";
    $stmt_h = $con->prepare($sql_is_head);
    $stmt_h->bind_param('s', $residence_id);
    $stmt_h->execute();
    $res_h = $stmt_h->get_result();

    if($res_h->num_rows > 0){
        $row_h = $res_h->fetch_assoc();
        
        if($row_h['is_head'] == 1){
            $household_id = $row_h['household_id'];

            // Find Successor (Spouse > Child)
            $sql_find_new = "
                SELECT hm.user_id, hm.relationship_to_head, ri.gender 
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
                $successor_relation = strtoupper($row_new['relationship_to_head']);
                
                // --- DETERMINE NEW TITLES ---
                $title_for_new_head = "Head";
                $title_for_old_head = "Member"; // Default

                // Logic: What should the Old Head be called now?
                // If Successor is Spouse (Wife) -> Old Head is Husband
                if(in_array($successor_relation, ['WIFE', 'HUSBAND', 'SPOUSE'])){
                    $title_for_old_head = ($gender_old_head == 'MALE') ? "Husband" : "Wife";
                }
                // If Successor is Child -> Old Head is Father/Mother
                elseif(in_array($successor_relation, ['SON', 'DAUGHTER', 'CHILD'])){
                    $title_for_old_head = ($gender_old_head == 'MALE') ? "Father" : "Mother";
                }

                // --- EXECUTE SWAP ---

                // 1. Update Old Head
                $sql_demote = "UPDATE users SET relationship_to_head = ? WHERE id = ?";
                $stmt_d = $con->prepare($sql_demote);
                $stmt_d->bind_param('ss', $title_for_old_head, $residence_id);
                $stmt_d->execute();
                $stmt_d->close();

                $sql_demote_m = "UPDATE household_members SET is_head = 0, relationship_to_head = ? WHERE user_id = ?";
                $stmt_dm = $con->prepare($sql_demote_m);
                $stmt_dm->bind_param('ss', $title_for_old_head, $residence_id);
                $stmt_dm->execute();
                $stmt_dm->close();

                // 2. Update New Head
                $sql_promote = "UPDATE users SET relationship_to_head = ? WHERE id = ?";
                $stmt_p = $con->prepare($sql_promote);
                $stmt_p->bind_param('ss', $title_for_new_head, $new_head_id);
                $stmt_p->execute();
                $stmt_p->close();

                $sql_promote_m = "UPDATE household_members SET is_head = 1, relationship_to_head = ? WHERE user_id = ?";
                $stmt_pm = $con->prepare($sql_promote_m);
                $stmt_pm->bind_param('ss', $title_for_new_head, $new_head_id);
                $stmt_pm->execute();
                $stmt_pm->close();

                // 3. Update Household Master
                $sql_master = "UPDATE households SET household_head_id = ? WHERE household_id = ?";
                $stmt_m = $con->prepare($sql_master);
                $stmt_m->bind_param('ss', $new_head_id, $household_id);
                $stmt_m->execute();
                $stmt_m->close();

                // Log
                $sys_msg = "SUCCESSION: $residence_id ($title_for_old_head) -> $new_head_id (Head)";
                $sys_stat = "system";
                $sql_log = "INSERT INTO activity_log (`message`,`date`,`status`) VALUES (?,?,?)";
                $stmt_l = $con->prepare($sql_log);
                $stmt_l->bind_param('sss', $sys_msg, $date_archive, $sys_stat);
                $stmt_l->execute();
                $stmt_l->close();
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