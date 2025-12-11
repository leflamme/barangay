<?php
session_start();
require __DIR__ . '/../connection.php';

if (!isset($_POST['pending_id'])) { echo json_encode(['status'=>'error','message'=>'Missing ID']); exit; }
$pending_id = $con->real_escape_string($_POST['pending_id']);

try {
    // 1. Fetch Pending Data
    $sql = "SELECT * FROM pending_residents WHERE pending_id = ? LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $pending_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    
    if (!$row) { echo json_encode(['status'=>'error','message'=>'Resident not found']); exit; }

    $con->begin_transaction();

    // 2. Household Logic
    $final_household_id = $row['target_household_id'];
    $household_action = $row['household_action'];
    $relationship = $row['relationship_to_head'];
    
    // Address reconstruction
    $parts = array_filter([$row['house_number'], $row['street'], 'Barangay Kalusugan', 'Quezon City', '1112']);
    $full_address = implode(', ', $parts);

    // FIXED: Define variables for bind_param
    $municipality = 'Quezon City';
    $barangay = 'Barangay Kalusugan';
    $zip = '1112';

    if ($household_action === 'new' || empty($final_household_id)) {
        $new_hh_number = date("Y") . '-' . mt_rand(1000, 9999);
        $sql_hh = "INSERT INTO households (household_number, household_head_id, municipality, barangay, street, house_number, address, zip_code, date_created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt_hh = $con->prepare($sql_hh);
        // Using variables for municipality, barangay, zip
        $stmt_hh->bind_param("ssssssss", $new_hh_number, $pending_id, $municipality, $barangay, $row['street'], $row['house_number'], $full_address, $zip);
        
        if(!$stmt_hh->execute()) throw new Exception("Household Error: ".$con->error);
        $final_household_id = $stmt_hh->insert_id;
        $relationship = 'Head'; 
    }

    // 3. Move Image
    $new_image_path = '';
    $new_image_name = $row['image_name'];
    if (!empty($new_image_name) && file_exists($row['image_path'])) {
        $perm_dir = __DIR__ . '/../permanent-data/images/';
        if (!is_dir($perm_dir)) mkdir($perm_dir, 0777, true);
        
        $new_dest = $perm_dir . $new_image_name;
        rename($row['image_path'], $new_dest);
        $new_image_path = '../permanent-data/images/' . $new_image_name;
    }

    // 4. Insert to USERS (Active)
    // FIXED: Created variable $val_user_type instead of passing 'resident' directly
    $val_user_type = 'resident'; 
    
    $sql_user = "INSERT INTO `users` (`id`,`first_name`,`middle_name`,`last_name`,`username`,`password`,`user_type`,`contact_number`,`image`,`image_path`,`household_id`,`relationship_to_head`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($sql_user);
    $stmt->bind_param('ssssssssssss', $pending_id, $row['first_name'], $row['middle_name'], $row['last_name'], $row['username'], $row['password_plain'], $val_user_type, $row['contact_number'], $new_image_name, $new_image_path, $final_household_id, $relationship);
    $stmt->execute();

    // 5. Insert to RESIDENCE_INFO
    $dob = new DateTime($row['birth_date']);
    $now = new DateTime();
    $age = $now->diff($dob)->y; // This is an integer, but binding as string is fine
    $senior = ($age >= 60) ? 'YES' : 'NO';
    $alias = $row['first_name'].' '.$row['last_name'];
    
    $sql_info = "INSERT INTO residence_information (residence_id, first_name, middle_name, last_name, age, suffix, gender, civil_status, religion, nationality, contact_number, email_address, birth_date, birth_place, house_number, street, fathers_name, mothers_name, guardian, guardian_contact, image, image_path, alias, address) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($sql_info);
    $stmt->bind_param('ssssssssssssssssssssssss', $pending_id, $row['first_name'], $row['middle_name'], $row['last_name'], $age, $row['suffix'], $row['gender'], $row['civil_status'], $row['religion'], $row['nationality'], $row['contact_number'], $row['email_address'], $row['birth_date'], $row['birth_place'], $row['house_number'], $row['street'], $row['fathers_name'], $row['mothers_name'], $row['guardian'], $row['guardian_contact'], $new_image_name, $new_image_path, $alias, $full_address);
    $stmt->execute();

    // 6. Insert to RESIDENCE_STATUS
    // FIXED: Created variables for status values
    $val_status = 'ACTIVE';
    $val_archive = 'NO';
    $date_added = date("m/d/Y h:i A");
    
    $sql_stat = "INSERT INTO residence_status (residence_id, status, residency_type, archive, pwd, pwd_info, single_parent, senior, date_added) VALUES (?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($sql_stat);
    $stmt->bind_param('sssssssss', $pending_id, $val_status, $row['residency_type'], $val_archive, $row['pwd'], $row['pwd_info'], $row['single_parent'], $senior, $date_added);
    $stmt->execute();

    // 7. Household Members Link
    $is_head = ($relationship == 'Head') ? 1 : 0;
    $sql_mem = "INSERT INTO household_members (household_id, user_id, relationship_to_head, is_head, date_added) VALUES (?,?,?,?,NOW())";
    $stmt_mem = $con->prepare($sql_mem);
    $stmt_mem->bind_param('issi', $final_household_id, $pending_id, $relationship, $is_head);
    $stmt_mem->execute();

    // 8. Log & Delete Pending
    $log_msg = "SECRETARY: APPROVE RESIDENT - $pending_id";
    $con->query("INSERT INTO activity_log (message, date, status) VALUES ('$log_msg', '$date_added', 'approve')");
    $con->query("DELETE FROM pending_residents WHERE pending_id = '$pending_id'");

    $con->commit();
    echo json_encode(['status'=>'success', 'new_id' => $pending_id]);

} catch(Exception $e) {
    $con->rollback();
    echo json_encode(['status'=>'error', 'message'=>$e->getMessage()]);
}
?>