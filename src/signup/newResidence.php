<?php 
// DISABLE DISPLAY ERRORS TO PREVENT BREAKING JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// START SESSION AND SET HEADER
session_start();
header('Content-Type: application/json'); // Crucial for JSON response

// Load Composer Autoloader 
require __DIR__ . '/../vendor/autoload.php';
// Load the database connection
require __DIR__ . '/../connection.php';

/**
 * ---------------------------------------------------------
 * EMAIL FUNCTION
 * ---------------------------------------------------------
 */
function sendBarangayWelcomeEmail($recipientEmail, $recipientName, $userData) {
    $apiKey = getenv('RESEND_API_KEY');
    $barangay_name = getenv('BARANGAY_NAME');
    $sender_email = 'no-reply@qc-brgy-kalusugan.online'; 

    if (empty($apiKey)) return false;

    $body = "<h2>Mabuhay, {$recipientName}!</h2>";
    $body .= "<p>Your registration is successful. Welcome!</p>";
    $body .= "<h3>Summary of Details:</h3>";
    $body .= "<ul style='list-style-type: none; padding: 0;'>";
    foreach ($userData as $key => $value) {
      if (!empty($value)) {
        $body .= "<li><strong>" . ucfirst($key) . ":</strong> " . htmlspecialchars($value) . "</li>";
      }
    }
    $body .= "</ul>";
    $body .= "<p>You may now log in using your registered username and password.</p>";

    $postData = [
        'from'    => $barangay_name . ' Admin <' . $sender_email . '>',
        'to'      => [$recipientEmail],
        'subject' => "✅ Welcome! Your Registration to {$barangay_name} System is Complete",
        'html'    => $body
    ];

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode >= 200 && $httpCode < 300);
}

// ---------------------------------------------------------
// MAIN PROCESSING
// ---------------------------------------------------------
try {  
    date_default_timezone_set('Asia/Manila');
    $date = new DateTime();
    $today = date("Y/m/d");

    // --- 1. CAPTURE DATA (Safe Handling) ---
    $add_username = isset($_POST['add_username']) ? $con->real_escape_string($_POST['add_username']) : '';
    $add_password = isset($_POST['add_password']) ? $con->real_escape_string($_POST['add_password']) : '';
    $add_confirm_password = isset($_POST['add_confirm_password']) ? $con->real_escape_string($_POST['add_confirm_password']) : '';

    // Validate Password
    if($add_password != $add_confirm_password){
        echo json_encode(['status' => 'errorPassword']);
        exit();
    }

    // Validate Username
    $sql_check_username = "SELECT username FROM users WHERE username = ?";
    $stmt_check = $con->prepare($sql_check_username);
    $stmt_check->bind_param('s', $add_username);
    $stmt_check->execute();
    if($stmt_check->get_result()->num_rows > 0){
        echo json_encode(['status' => 'errorUsername']);
        exit();
    }

    // Address Inputs
    $add_municipality = isset($_POST['add_municipality']) ? $con->real_escape_string($_POST['add_municipality']) : '';
    $add_barangay = isset($_POST['add_barangay']) ? $con->real_escape_string($_POST['add_barangay']) : '';
    $add_street = isset($_POST['add_street']) ? $con->real_escape_string($_POST['add_street']) : '';
    $add_house_number = isset($_POST['add_house_number']) ? $con->real_escape_string($_POST['add_house_number']) : '';
    $add_address = isset($_POST['add_address']) ? $con->real_escape_string($_POST['add_address']) : '';
    $add_zip = isset($_POST['add_zip']) ? $con->real_escape_string($_POST['add_zip']) : '';
    
    // Household Action Inputs
    $household_action = isset($_POST['household_action']) ? $_POST['household_action'] : null;
    $target_household_id = isset($_POST['household_id']) ? $_POST['household_id'] : null;
    $relationship = isset($_POST['relationship_to_head']) ? $_POST['relationship_to_head'] : 'Head';

    // --- 2. HOUSEHOLD CHECK LOGIC ---
    
    // Only check if user hasn't made a choice yet
    if (empty($household_action)) {
        // Look for exact address match
        $check_sql = "SELECT h.*, u.first_name as head_first_name, u.last_name as head_last_name 
                      FROM households h 
                      LEFT JOIN users u ON h.household_head_id = u.id 
                      WHERE h.municipality = ? AND h.barangay = ? AND h.street = ? AND h.house_number = ? LIMIT 1";
        
        $stmt_h = $con->prepare($check_sql);
        $stmt_h->bind_param("ssss", $add_municipality, $add_barangay, $add_street, $add_house_number);
        $stmt_h->execute();
        $result_h = $stmt_h->get_result();

        if ($result_h->num_rows > 0) {
            // Found one! Ask user what to do.
            $household_data = $result_h->fetch_assoc();
            echo json_encode([
                'status' => 'showHouseholdModal',
                'household' => $household_data
            ]);
            exit(); 
        } else {
            // None found, creating new
            $household_action = 'new';
        }
    }

    // --- 3. PREPARE GENERAL DATA ---
    
    // ID Generation (Using Old Format to match DB schema)
    $number = mt_rand(100000, 999999) . $date->format("mdHis");
    $date_added = date("m/d/Y h:i A");
    $archive = 'NO';
    $user_type = 'resident';
    $add_status = 'ACTIVE';

    // Other Inputs
    $add_pwd_check = isset($_POST['add_pwd_info']) ? $con->real_escape_string($_POST['add_pwd_info']) : '';
    $add_single_parent = isset($_POST['add_single_parent']) ? $con->real_escape_string($_POST['add_single_parent']) : 'NO';
    $add_pwd = isset($_POST['add_pwd']) ? $con->real_escape_string($_POST['add_pwd']) : 'NO';
    $add_voters = isset($_POST['add_voters']) ? $con->real_escape_string($_POST['add_voters']) : 'NO';
    $add_first_name = isset($_POST['add_first_name']) ? $con->real_escape_string($_POST['add_first_name']) : '';
    $add_middle_name = isset($_POST['add_middle_name']) ? $con->real_escape_string($_POST['add_middle_name']) : '';
    $add_last_name = isset($_POST['add_last_name']) ? $con->real_escape_string($_POST['add_last_name']) : '';
    $add_suffix = isset($_POST['add_suffix']) ? $con->real_escape_string($_POST['add_suffix']) : '';
    $add_gender = isset($_POST['add_gender']) ? $con->real_escape_string($_POST['add_gender']) : '';
    $add_civil_status = isset($_POST['add_civil_status']) ? $con->real_escape_string($_POST['add_civil_status']) : '';
    $add_religion = isset($_POST['add_religion']) ? $con->real_escape_string($_POST['add_religion']) : '';
    $add_nationality = isset($_POST['add_nationality']) ? $con->real_escape_string($_POST['add_nationality']) : '';
    $add_contact_number = isset($_POST['add_contact_number']) ? $con->real_escape_string($_POST['add_contact_number']) : '';
    $add_email_address = isset($_POST['add_email_address']) ? $con->real_escape_string($_POST['add_email_address']) : '';
    $add_birth_date = isset($_POST['add_birth_date']) ? $con->real_escape_string($_POST['add_birth_date']) : '';
    $add_birth_place = isset($_POST['add_birth_place']) ? $con->real_escape_string($_POST['add_birth_place']) : '';
    $add_fathers_name = isset($_POST['add_fathers_name']) ? $con->real_escape_string($_POST['add_fathers_name']) : '';
    $add_mothers_name = isset($_POST['add_mothers_name']) ? $con->real_escape_string($_POST['add_mothers_name']) : '';
    $add_guardian = isset($_POST['add_guardian']) ? $con->real_escape_string($_POST['add_guardian']) : '';
    $add_guardian_contact = isset($_POST['add_guardian_contact']) ? $con->real_escape_string($_POST['add_guardian_contact']) : '';

    // Image Upload
    $new_image_name = '';
    $new_image_path = '';
    if(isset($_FILES['add_image_residence']['name']) && !empty($_FILES['add_image_residence']['name'])){
        $temp = explode('.', $_FILES['add_image_residence']['name']);
        $new_image_name = uniqid(rand()) . '.' . end($temp);
        $upload_dir = '../permanent-data/images/';
        $new_image_path = $upload_dir . $new_image_name;
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        move_uploaded_file($_FILES['add_image_residence']['tmp_name'], $new_image_path);
    }

    // --- 4. EXECUTE HOUSEHOLD CREATION/JOINING ---
    
    $final_household_id = null;
    $final_household_number = '';
    
    if ($household_action === 'join' && !empty($target_household_id)) {
        // JOINING
        $final_household_id = $target_household_id;
        $get_num = $con->query("SELECT household_number FROM households WHERE id = '$final_household_id'");
        if ($get_num && $row_num = $get_num->fetch_assoc()) {
            $final_household_number = $row_num['household_number'];
        }
    } else {
        // CREATING NEW
        $new_household_number = date("Y") . '-' . mt_rand(1000, 9999);
        
        $sql_hh = "INSERT INTO households (household_number, household_head_id, municipality, barangay, street, house_number, address, zip_code, date_created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt_hh = $con->prepare($sql_hh);
        // We set the head_id to the NEW user's ID
        $stmt_hh->bind_param("ssssssss", $new_household_number, $number, $add_municipality, $add_barangay, $add_street, $add_house_number, $add_address, $add_zip);
        
        if ($stmt_hh->execute()) {
            $final_household_id = $stmt_hh->insert_id;
            $final_household_number = $new_household_number;
            $relationship = 'Head';
        }
        $stmt_hh->close();
    }

    // --- 5. INSERT USER & RESIDENT DATA ---

    // A. Users Table
    // Note: We use $add_password directly (Plain Text) to match your existing login system.
    $sql_add_user = "INSERT INTO `users`(`id`, `first_name`, `middle_name`, `last_name`, `username`, `password`, `user_type`,`contact_number`, `image`,`image_path`, `household_id`, `relationship_to_head`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt_user = $con->prepare($sql_add_user);
    if (!$stmt_user) throw new Exception($con->error);
    $stmt_user->bind_param('ssssssssssss',$number,$add_first_name,$add_middle_name,$add_last_name,$add_username,$add_password,$user_type,$add_contact_number,$new_image_name,$new_image_path, $final_household_id, $relationship);
    $stmt_user->execute();
    $stmt_user->close();

    // B. Residence Information
    $age_obj = date_diff(date_create($add_birth_date), date_create($today));
    $age_val = $age_obj->format("%y");
    $senior = ($age_val >= 60) ? 'YES' : 'NO';
    $alias = $add_first_name . ' ' . $add_last_name; 
    $add_occupation = '';

    $sql = "INSERT INTO `residence_information`(
      `residence_id`, `first_name`, `middle_name`, `last_name`, `age`, 
      `suffix`, `gender`, `civil_status`, `religion`, `nationality`, 
      `contact_number`, `email_address`, `address`, `birth_date`, `birth_place`, 
      `municipality`, `zip`, `barangay`, `house_number`, `street`, 
      `fathers_name`, `mothers_name`, `guardian`, `guardian_contact`, `image`, 
      `image_path`, `alias`, `occupation`
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    
    $stmt = $con->prepare($sql);
    if (!$stmt) throw new Exception($con->error);
    $stmt->bind_param('ssssssssssssssssssssssssssss', 
      $number, $add_first_name, $add_middle_name, $add_last_name, $age_val,
      $add_suffix, $add_gender, $add_civil_status, $add_religion, $add_nationality,
      $add_contact_number, $add_email_address, $add_address, $add_birth_date, $add_birth_place,
      $add_municipality, $add_zip, $add_barangay, $add_house_number, $add_street,
      $add_fathers_name, $add_mothers_name, $add_guardian, $add_guardian_contact, $new_image_name,
      $new_image_path, $alias, $add_occupation
    );
    $stmt->execute();
    $stmt->close();

    // C. Residence Status
    $sql_residence_status = "INSERT INTO `residence_status` (`residence_id`, `status`, `voters`,`archive`,`pwd`,`pwd_info`,`single_parent`,`senior`, `date_added`) VALUES (?,?,?,?,?,?,?,?,?)";
    $stmt_status = $con->prepare($sql_residence_status);
    $stmt_status->bind_param('sssssssss',$number,$add_status,$add_voters,$archive,$add_pwd,$add_pwd_check,$add_single_parent,$senior,$date_added);
    $stmt_status->execute();
    $stmt_status->close();

    // D. Household Members (New Table)
    // Check if table exists to prevent errors if you haven't created it yet
    $check_table = $con->query("SHOW TABLES LIKE 'household_members'");
    if($check_table->num_rows > 0) {
        $is_head_val = ($relationship === 'Head') ? 1 : 0;
        $sql_mem = "INSERT INTO household_members (household_id, user_id, relationship_to_head, is_head, date_added) VALUES (?, ?, ?, ?, NOW())";
        $stmt_mem = $con->prepare($sql_mem);
        if ($stmt_mem) {
            $stmt_mem->bind_param('issi', $final_household_id, $number, $relationship, $is_head_val);
            $stmt_mem->execute();
            $stmt_mem->close();
        }
    }

    // E. Activity Log
    $admin_log = strtoupper('RESIDENT').':' .' '. 'REGISTER RESIDENT -'.' ' .$number.' |' .'  '.$add_first_name .' '. $add_last_name;
    $status_log = 'create';
    $date_log = date("j-n-Y g:i A");

    $sql_log = "INSERT INTO activity_log (`message`,`date`,`status`) VALUES (?,?,?)";
    $stmt_log = $con->prepare($sql_log);
    $stmt_log->bind_param('sss',$admin_log,$date_log,$status_log);
    $stmt_log->execute();
    $stmt_log->close();

    // --- 6. NOTIFICATIONS (EMAIL ONLY) ---
    
    $resident_data_for_email = [
        'Full Name'        => $add_first_name . ' ' . $add_last_name,
        'Household #'      => $final_household_number,
        'Username'         => $add_username,
        'Address'          => $add_address
    ];
  
    if (!empty($add_email_address)) {
        sendBarangayWelcomeEmail($add_email_address, $add_first_name, $resident_data_for_email);
    }
  
    // --- 7. FINAL SUCCESS RESPONSE ---
    echo json_encode([
        'status' => 'success',
        'household_number' => $final_household_number,
        'action' => $household_action
    ]);

} catch (Throwable $e) {
    // Return JSON Error if anything fails
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>