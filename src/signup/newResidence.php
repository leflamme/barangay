<?php 
// Turn off error display to prevent HTML breaking the JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json'); 
require __DIR__ . '/../connection.php';

try {  
    date_default_timezone_set('Asia/Manila');

    // --- CHECK FILE SIZE ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
        throw new Exception("File too large. Maximum allowed size is " . ini_get('upload_max_filesize'));
    }
    
    // --- 1. CAPTURE INPUTS ---
    $add_username = $con->real_escape_string($_POST['add_username'] ?? '');
    $add_password = $con->real_escape_string($_POST['add_password'] ?? ''); 
    $add_confirm_password = $_POST['add_confirm_password'] ?? '';

    if($add_password != $add_confirm_password){ echo json_encode(['status' => 'errorPassword']); exit(); }

    // Check Username
    $stmt_check = $con->prepare("SELECT username FROM users WHERE username = ? UNION SELECT username FROM pending_residents WHERE username = ?");
    $stmt_check->bind_param('ss', $add_username, $add_username);
    $stmt_check->execute();
    if($stmt_check->get_result()->num_rows > 0){ echo json_encode(['status' => 'errorUsername']); exit(); }

    // Address
    $add_municipality = 'Quezon City';
    $add_barangay     = 'Barangay Kalusugan';
    $add_zip          = '1112';
    $add_street       = $con->real_escape_string($_POST['add_street'] ?? '');
    $add_house_number = $con->real_escape_string($_POST['add_house_number'] ?? '');
    $parts = array_filter([$add_house_number, $add_street, $add_barangay, $add_municipality, $add_zip]);
    $add_address = implode(', ', $parts);
    
    // Household Logic
    $household_action = $_POST['household_action'] ?? null;
    $target_household_id = $_POST['household_id'] ?? null;
    $relationship = $_POST['relationship_to_head'] ?? 'Head';

    // --- 2. HOUSEHOLD CHECK ---
    if (empty($household_action)) {
        $check_sql = "SELECT h.*, u.first_name as head_first_name, u.last_name as head_last_name 
                      FROM households h 
                      LEFT JOIN users u ON h.household_head_id = u.id 
                      WHERE (h.street = ? AND h.house_number = ?) OR h.address = ? LIMIT 1";
        $stmt_h = $con->prepare($check_sql);
        $stmt_h->bind_param("sss", $add_street, $add_house_number, $add_address);
        $stmt_h->execute();
        $result_h = $stmt_h->get_result();

        if ($result_h->num_rows > 0) {
            echo json_encode(['status' => 'showHouseholdModal', 'household' => $result_h->fetch_assoc()]);
            exit(); 
        } else {
            echo json_encode(['status' => 'askCreateNew']);
            exit();
        }
    }

    // --- 3. SAVE TO PENDING ---
    $date = new DateTime();
    $pending_id = mt_rand(100000, 999999) . $date->format("mdHis");
    $date_submitted = date("Y-m-d H:i:s");
    
    // Variables
    $add_first_name = $con->real_escape_string($_POST['add_first_name'] ?? '');
    $add_middle_name = $con->real_escape_string($_POST['add_middle_name'] ?? '');
    $add_last_name = $con->real_escape_string($_POST['add_last_name'] ?? '');
    $add_suffix = $con->real_escape_string($_POST['add_suffix'] ?? '');
    $add_gender = $con->real_escape_string($_POST['add_gender'] ?? '');
    $add_civil_status = $con->real_escape_string($_POST['add_civil_status'] ?? '');
    $add_religion = $con->real_escape_string($_POST['add_religion'] ?? '');
    $add_nationality = $con->real_escape_string($_POST['add_nationality'] ?? '');
    $add_contact_number = $con->real_escape_string($_POST['add_contact_number'] ?? '');
    $add_email_address = $con->real_escape_string($_POST['add_email_address'] ?? '');
    $add_birth_date = $con->real_escape_string($_POST['add_birth_date'] ?? '');
    $add_birth_place = $con->real_escape_string($_POST['add_birth_place'] ?? '');
    $add_fathers_name = $con->real_escape_string($_POST['add_fathers_name'] ?? '');
    $add_mothers_name = $con->real_escape_string($_POST['add_mothers_name'] ?? '');
    $add_guardian = $con->real_escape_string($_POST['add_guardian'] ?? '');
    $add_guardian_contact = $con->real_escape_string($_POST['add_guardian_contact'] ?? '');
    $add_pwd = $con->real_escape_string($_POST['add_pwd'] ?? 'NO');
    $add_pwd_info = $con->real_escape_string($_POST['add_pwd_info'] ?? '');
    $add_single_parent = $con->real_escape_string($_POST['add_single_parent'] ?? 'NO');
    $add_residency_type = $con->real_escape_string($_POST['add_residency_type'] ?? '');

    // --- IMAGE HANDLING ---
    // [FIXED] Removed mkdir() to prevent Permission Denied errors on Railway
    $target_dir = '../permanent-data/residence_photos/'; 

    // Profile Pic
    $image_name = ''; $image_path = '';
    if(isset($_FILES['add_image_residence']['name']) && !empty($_FILES['add_image_residence']['name'])){
        $temp = explode('.', $_FILES['add_image_residence']['name']);
        $image_name = uniqid('PROF_') . '.' . end($temp);
        $image_path = $target_dir . $image_name;
        // Suppress errors with @ in case folder is missing, prevents crash
        @move_uploaded_file($_FILES['add_image_residence']['tmp_name'], $image_path);
    }

    // Valid ID (Blob)
    $valid_id_blob = null; 
    if(isset($_FILES['add_valid_id']['tmp_name']) && !empty($_FILES['add_valid_id']['tmp_name'])){
        $valid_id_blob = file_get_contents($_FILES['add_valid_id']['tmp_name']);
    }

    // INSERT
    // [FIXED] Added the missing comma and question mark (?,) at the end of VALUES
    $sql = "INSERT INTO pending_residents (
        pending_id, first_name, middle_name, last_name, suffix, gender, civil_status, religion, nationality,
        contact_number, email_address, birth_date, birth_place, house_number, street, 
        fathers_name, mothers_name, guardian, guardian_contact, image_name, image_path,
        residency_type, pwd, pwd_info, single_parent, username, password_plain, date_submitted,
        household_action, target_household_id, relationship_to_head,
        valid_id_blob
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"; 
    // ^ The line above now has 32 question marks.

    $stmt = $con->prepare($sql);
    
    // Bind 32 variables
    $stmt->bind_param('ssssssssssssssssssssssssssssssss', 
        $pending_id, $add_first_name, $add_middle_name, $add_last_name, $add_suffix, $add_gender, 
        $add_civil_status, $add_religion, $add_nationality, $add_contact_number, $add_email_address, 
        $add_birth_date, $add_birth_place, $add_house_number, $add_street, 
        $add_fathers_name, $add_mothers_name, $add_guardian, $add_guardian_contact, $image_name, $image_path,
        $add_residency_type, $add_pwd, $add_pwd_info, $add_single_parent, $add_username, $add_password, $date_submitted,
        $household_action, $target_household_id, $relationship,
        $valid_id_blob
    );

    if($stmt->execute()){
        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception($stmt->error);
    }

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>