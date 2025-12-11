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

    // --- SMART LOGIC VARIABLES ---
    $final_household_id = $row['target_household_id'];
    $household_action = $row['household_action'];
    $relationship = $row['relationship_to_head'];
    
    // Default Location Vars
    $municipality = 'Quezon City';
    $barangay = 'Barangay Kalusugan';
    $zip = '1112';
    $street = $row['street'];
    $house_num = $row['house_number'];
    
    // Reconstruct full address
    $parts = array_filter([$house_num, $street, $barangay, $municipality, $zip]);
    $full_address = implode(', ', $parts);

    // --- 2. THE "SECOND CHECK" (Smart Logic) ---
    if ($household_action === 'new') {
        $check_hh = $con->prepare("SELECT id FROM households WHERE street = ? AND house_number = ? LIMIT 1");
        $check_hh->bind_param("ss", $street, $house_num);
        $check_hh->execute();
        $res_hh = $check_hh->get_result();
        
        if ($res_hh->num_rows > 0) {
            $existing_row = $res_hh->fetch_assoc();
            $final_household_id = $existing_row['id'];
            $household_action = 'join'; 
            if ($relationship === 'Head') { $relationship = 'Member'; }
        }
    }

    // --- 3. CREATE HOUSEHOLD IF NEEDED ---
    if ($household_action === 'new' || empty($final_household_id)) {
        $new_hh_number = date("Y") . '-' . mt_rand(1000, 9999);
        $sql_hh = "INSERT INTO households (household_number, household_head_id, municipality, barangay, street, house_number, address, zip_code, date_created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt_hh = $con->prepare($sql_hh);
        $stmt_hh->bind_param("ssssssss", $new_hh_number, $pending_id, $municipality, $barangay, $street, $house_num, $full_address, $zip);
        
        if(!$stmt_hh->execute()) throw new Exception("Household Error: ".$con->error);
        $final_household_id = $stmt_hh->insert_id;
        $relationship = 'Head'; 
    }

    // --- 4. HANDLE IMAGES (File System) ---
    // If using the BLOB method for ID, we don't need to move the ID file.
    // We only check if the Profile Picture needs moving (if you use folders for Profile Pics)
    $new_image_path = '';
    $new_image_name = $row['image_name'];
    if (!empty($new_image_name) && !empty($row['image_path']) && file_exists($row['image_path'])) {
        // Assuming images are already in the permanent folder from newResidence.php
        $new_image_path = $row['image_path']; 
    }

    // --- 5. INSERT USERS ---
    $val_user_type = 'resident'; 
    $sql_user = "INSERT INTO `users` (`id`,`first_name`,`middle_name`,`last_name`,`username`,`password`,`user_type`,`contact_number`,`image`,`image_path`,`household_id`,`relationship_to_head`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($sql_user);
    $stmt->bind_param('ssssssssssss', $pending_id, $row['first_name'], $row['middle_name'], $row['last_name'], $row['username'], $row['password_plain'], $val_user_type, $row['contact_number'], $new_image_name, $new_image_path, $final_household_id, $relationship);
    $stmt->execute();

    // --- 6. INSERT RESIDENCE INFO (FIXED) ---
    // Added: municipality, barangay, zip
    $dob = new DateTime($row['birth_date']);
    $age = (new DateTime())->diff($dob)->y;
    $senior = ($age >= 60) ? 'YES' : 'NO';
    $alias = $row['first_name'].' '.$row['last_name'];
    
    // UPDATED SQL: Added 3 new columns
    $sql_info = "INSERT INTO residence_information (
        residence_id, first_name, middle_name, last_name, age, suffix, gender, civil_status, religion, nationality, 
        contact_number, email_address, birth_date, birth_place, house_number, street, 
        municipality, barangay, zip, 
        fathers_name, mothers_name, guardian, guardian_contact, image, image_path, alias, address
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, ?, ?, ?, ?, ?
    )";
    
    $stmt = $con->prepare($sql_info);
    
    // UPDATED BIND: Added 3 variables ($municipality, $barangay, $zip)
    // Total params: 27
    $stmt->bind_param('sssssssssssssssssssssssssss', 
        $pending_id, $row['first_name'], $row['middle_name'], $row['last_name'], $age, $row['suffix'], $row['gender'], $row['civil_status'], $row['religion'], $row['nationality'], 
        $row['contact_number'], $row['email_address'], $row['birth_date'], $row['birth_place'], $row['house_number'], $row['street'], 
        $municipality, $barangay, $zip, 
        $row['fathers_name'], $row['mothers_name'], $row['guardian'], $row['guardian_contact'], $new_image_name, $new_image_path, $alias, $full_address
    );
    $stmt->execute();

    // --- 7. INSERT STATUS (With ID Blob Logic) ---
    $val_status = 'ACTIVE';
    $val_archive = 'NO';
    $date_added = date("m/d/Y h:i A");
    
    // Note: If you have a blob column in residence_status, add it here. 
    // Otherwise, we proceed with standard status fields.
    $sql_stat = "INSERT INTO residence_status (residence_id, status, residency_type, archive, pwd, pwd_info, single_parent, senior, date_added) VALUES (?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($sql_stat);
    $stmt->bind_param('sssssssss', $pending_id, $val_status, $row['residency_type'], $val_archive, $row['pwd'], $row['pwd_info'], $row['single_parent'], $senior, $date_added);
    $stmt->execute();

    // --- 8. HOUSEHOLD MEMBERS ---
    $is_head = ($relationship == 'Head') ? 1 : 0;
    $sql_mem = "INSERT INTO household_members (household_id, user_id, relationship_to_head, is_head, date_added) VALUES (?,?,?,?,NOW())";
    $stmt_mem = $con->prepare($sql_mem);
    $stmt_mem->bind_param('issi', $final_household_id, $pending_id, $relationship, $is_head);
    $stmt_mem->execute();

    // --- 9. CLEANUP ---
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