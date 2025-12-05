<?php 
session_start();
include_once '../connection.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'resident'){
 echo '<script>window.location.href = "../login.php";</script>';
 exit();
}

try {
    $user_id = $_SESSION['user_id'];
    
    // --- 1. CAPTURE ALL INPUTS ---
    $edit_residency_type = $con->real_escape_string($_POST['edit_residency_type']);
    $edit_residence_id = $con->real_escape_string(trim($_POST['edit_residence_id']));
    $edit_voters = $con->real_escape_string($_POST['edit_voters']);
    $edit_pwd = $con->real_escape_string($_POST['edit_pwd']);
    
    $edit_pwd_info = isset($_POST['edit_pwd_info']) ? $con->real_escape_string($_POST['edit_pwd_info']) : '';
    $edit_single_parent = isset($_POST['edit_single_parent']) ? $con->real_escape_string($_POST['edit_single_parent']) : 'NO';

    $edit_first_name = $con->real_escape_string($_POST['edit_first_name']);
    $edit_middle_name = $con->real_escape_string($_POST['edit_middle_name']);
    $edit_last_name = $con->real_escape_string($_POST['edit_last_name']);
    $edit_suffix = $con->real_escape_string($_POST['edit_suffix']);
    $edit_gender = $con->real_escape_string($_POST['edit_gender']);
    $edit_civil_status = $con->real_escape_string($_POST['edit_civil_status']);
    $edit_religion = $con->real_escape_string($_POST['edit_religion']);
    $edit_nationality = $con->real_escape_string($_POST['edit_nationality']);
    $edit_contact_number = $con->real_escape_string($_POST['edit_contact_number']);
    $edit_email_address = $con->real_escape_string($_POST['edit_email_address']);
    $edit_address = $con->real_escape_string($_POST['edit_address']);
    $edit_birth_date = $con->real_escape_string($_POST['edit_birth_date']);
    $edit_birth_place = $con->real_escape_string($_POST['edit_birth_place']);
    $edit_municipality = $con->real_escape_string($_POST['edit_municipality']);
    $edit_zip = $con->real_escape_string($_POST['edit_zip']);
    $edit_barangay = $con->real_escape_string($_POST['edit_barangay']);
    $edit_house_number = $con->real_escape_string($_POST['edit_house_number']);
    $edit_street = $con->real_escape_string($_POST['edit_street']);
    $edit_fathers_name = $con->real_escape_string($_POST['edit_fathers_name']);
    $edit_mothers_name = $con->real_escape_string($_POST['edit_mothers_name']);
    $edit_guardian = $con->real_escape_string($_POST['edit_guardian']);
    $edit_guardian_contact = $con->real_escape_string($_POST['edit_guardian_contact']);

    // --- Image Handling ---
    $sql_check_image = "SELECT `image`, `image_path` FROM `residence_information` WHERE `residence_id` = ?";
    $stmt_check_image = $con->prepare($sql_check_image);
    $stmt_check_image->bind_param('s', $edit_residence_id);
    $stmt_check_image->execute();
    $result_check_image = $stmt_check_image->get_result();
    $row_check_image = $result_check_image->fetch_assoc();
    
    $new_edit_image_name = $row_check_image['image'];
    $new_edit_image_path = $row_check_image['image_path'];

    if(isset($_FILES['edit_image_residence']['name']) && $_FILES['edit_image_residence']['name'] != ''){
        $edit_image = $_FILES['edit_image_residence']['name'];
        if(!empty($row_check_image['image_path']) && file_exists($row_check_image['image_path'])){
             unlink($row_check_image['image_path']);
        }
        $type = pathinfo($edit_image, PATHINFO_EXTENSION);
        $new_edit_image_name = uniqid(rand()) .'.'. $type;
        $new_edit_image_path = '../assets/dist/img/' . $new_edit_image_name;
        move_uploaded_file($_FILES['edit_image_residence']['tmp_name'], $new_edit_image_path);
    }

    $today = date("Y/m/d");
    $age = date_diff(date_create($edit_birth_date), date_create($today));
    $edit_age_date = $age->format("%y");
    $senior = ($edit_age_date >= 60) ? 'YES' : 'NO';

    // --- 2. UPDATE RESIDENCE INFORMATION ---
    $sql_edit_residence = "UPDATE `residence_information` SET 
    `first_name`= ?, `middle_name`= ?, `last_name`= ?, `age`= ?, `suffix`= ?, 
    `gender`= ?, `civil_status`= ?, `religion`= ?, `nationality`= ?, `contact_number`= ?, 
    `email_address`= ?, `address`= ?, `birth_date`= ?, `birth_place`= ?, `municipality`= ?, 
    `zip`= ?, `barangay`= ?, `house_number`= ?, `street`= ?, `fathers_name`= ?, 
    `mothers_name`= ?, `guardian`= ?, `guardian_contact`= ?, `image`= ?, `image_path`= ? 
    WHERE `residence_id` = ?";
    
    $stmt_edit_residence = $con->prepare($sql_edit_residence) or die ($con->error);
    $stmt_edit_residence->bind_param('ssssssssssssssssssssssssss',
        $edit_first_name, $edit_middle_name, $edit_last_name, $edit_age_date, $edit_suffix,
        $edit_gender, $edit_civil_status, $edit_religion, $edit_nationality, $edit_contact_number,
        $edit_email_address, $edit_address, $edit_birth_date, $edit_birth_place, $edit_municipality,
        $edit_zip, $edit_barangay, $edit_house_number, $edit_street, $edit_fathers_name,
        $edit_mothers_name, $edit_guardian, $edit_guardian_contact, $new_edit_image_name, $new_edit_image_path,
        $edit_residence_id
    );
    $stmt_edit_residence->execute();
    $stmt_edit_residence->close();

    // --- 3. UPDATE RESIDENCE STATUS ---
    $sql_edit_residence_status = "UPDATE `residence_status` SET `voters` = ?, `senior` = ?, `pwd` = ?, `pwd_info`= ? , `single_parent` = ?, `residency_type` = ? WHERE `residence_id` = ?";
    $stmt_edit_residence_status = $con->prepare($sql_edit_residence_status) or die ($con->error);
    $stmt_edit_residence_status->bind_param('sssssss', $edit_voters, $senior, $edit_pwd, $edit_pwd_info, $edit_single_parent, $edit_residency_type, $edit_residence_id);
    $stmt_edit_residence_status->execute();
    $stmt_edit_residence_status->close();

    // --- 4. UPDATE USER TABLE ---
    $sql_edit_residence_users = "UPDATE `users` SET `first_name` = ?, `middle_name` = ?, `last_name` = ?, `contact_number` = ?, `image` = ?, `image_path`= ? WHERE `id` = ?";
    $stmt_edit_residence_users = $con->prepare($sql_edit_residence_users) or die ($con->error);
    $stmt_edit_residence_users->bind_param('sssssss', $edit_first_name, $edit_middle_name, $edit_last_name, $edit_contact_number, $new_edit_image_name, $new_edit_image_path, $edit_residence_id);
    $stmt_edit_residence_users->execute();
    $stmt_edit_residence_users->close();

    // ============================================================
    // --- 5. CLOSE THE EDIT REQUEST (THIS WAS MISSING) ---
    // ============================================================
    $status_completed = 'COMPLETED';
    $status_approved = 'APPROVED';
    
    // We only close requests that are currently marked as APPROVED
    $sql_close_request = "UPDATE `edit_requests` SET `status` = ? WHERE `user_id` = ? AND `status` = ?";
    $stmt_close = $con->prepare($sql_close_request);
    $stmt_close->bind_param('sss', $status_completed, $user_id, $status_approved);
    $stmt_close->execute();
    $stmt_close->close();
    // ============================================================


    // --- 6. LOGGING ---
    if(isset($_POST['edit_first_name_check']) && ($_POST['edit_first_name_check'] == 'true')){
         $date_activity = date("j-n-Y g:i A");  
         $admin = 'RESIDENT: ' .$edit_first_name.' '.$edit_last_name. ' UPDATED INFO';
         $status_activity_log = 'update';
         $sql_activity_log = "INSERT INTO activity_log (`message`,`date`,`status`)VALUES(?,?,?)";
         $stmt_activity_log = $con->prepare($sql_activity_log);
         $stmt_activity_log->bind_param('sss',$admin,$date_activity,$status_activity_log);
         $stmt_activity_log->execute();
    }

} catch(Exception $e) {
    echo $e->getMessage();
}
?>