<?php 
session_start();
// Load Composer Autoloader 
require __DIR__ . '/../vendor/autoload.php';
// Load the database connection
require __DIR__ . '/../connection.php';

// Import Twilio
use Twilio\Rest\Client;

/**
 * Your Resend Email Function (Unchanged)
 */
function sendBarangayWelcomeEmail($recipientEmail, $recipientName, $userData) {
    $apiKey = getenv('RESEND_API_KEY');
    $barangay_name = getenv('BARANGAY_NAME');
    $sender_email = 'no-reply@qc-brgy-kalusugan.online'; // Your verified sender email

    if (empty($apiKey)) {
        error_log("Barangay Mailer Error: RESEND_API_KEY is not set.");
        return false;
    }

    // Build the email body
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
    $body .= "<p>You may now log in using your registered username and password you created during registration.</p>";

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
    
    if (curl_errno($ch)) {
        error_log('Resend cURL error: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        return true; // Success!
    } else {
        error_log("Resend API Error: HTTP {$httpCode} - Response: {$response}");
        return false;
    }
}

/**
 * NEW: Twilio SMS Function
 */
function sendWelcomeSMS($recipientPhone, $firstName) {
    // Get credentials from Railway
    $sid = getenv('TWILIO_SID');
    $token = getenv('TWILIO_TOKEN');
    $twilio_number = getenv('TWILIO_PHONE_NUMBER');
    $barangay_name = getenv('BARANGAY_NAME');

    // Stop if credentials are not set
    if(empty($sid) || empty($token) || empty($twilio_number)) {
        error_log("Twilio SMS Error: Credentials are not set.");
        return false;
    }

    // Format phone number to E.164
    if (substr($recipientPhone, 0, 1) == '0') {
        $recipientPhone = '+63' . substr($recipientPhone, 1);
    } elseif (substr($recipientPhone, 0, 2) == '63') {
         $recipientPhone = '+' . $recipientPhone;
    }

    try {
        $client = new Client($sid, $token);
        $client->messages->create(
            $recipientPhone, // The 'To' number
            [
                'from' => $twilio_number, // Your Twilio 'From' number
                'body' => "Welcome to {$barangay_name}, {$firstName}! Your registration is successful. You can now log in to the portal."
            ]
        );
        return true;
    } catch (Exception $e) {
        // Log the error but don't crash the whole registration
        error_log("Twilio SMS Error: " . $e->getMessage());
        return false; 
    }
}


// --- MAIN SCRIPT ---
try{  
date_default_timezone_set('Asia/Manila');
$date = new DateTime();

// NEW ID GENERATOR
$number = mt_rand(100000, 999999) . $date->format("mdHis");
$date_added = date("m/d/Y h:i A");
$archive = 'NO';


if(isset($_POST['add_pwd_info'])){
  $add_pwd_check = $con->real_escape_string($_POST['add_pwd_info']);
}else{
  $add_pwd_check = '';
}
$add_single_parent = $con->real_escape_string($_POST['add_single_parent']);
$add_pwd = $con->real_escape_string($_POST['add_pwd']);
$add_voters = $con->real_escape_string($_POST['add_voters']);
$add_first_name = $con->real_escape_string($_POST['add_first_name']);
$add_middle_name = $con->real_escape_string($_POST['add_middle_name']);
$add_last_name = $con->real_escape_string($_POST['add_last_name']);
$add_suffix = $con->real_escape_string($_POST['add_suffix']);
$add_gender = $con->real_escape_string($_POST['add_gender']);
$add_civil_status = $con->real_escape_string($_POST['add_civil_status']);
$add_religion = $con->real_escape_string($_POST['add_religion']);
$add_nationality = $con->real_escape_string($_POST['add_nationality']);
$add_contact_number = $con->real_escape_string($_POST['add_contact_number']);
$add_email_address = $con->real_escape_string($_POST['add_email_address']);
$add_address = $con->real_escape_string($_POST['add_address']);
$add_birth_date = $con->real_escape_string($_POST['add_birth_date']);
$add_birth_place = $con->real_escape_string($_POST['add_birth_place']);
$add_municipality = $con->real_escape_string($_POST['add_municipality']);
$add_zip = $con->real_escape_string($_POST['add_zip']);
$add_barangay = $con->real_escape_string($_POST['add_barangay']);
$add_house_number = $con->real_escape_string($_POST['add_house_number']);
$add_street = $con->real_escape_string($_POST['add_street']);
$add_fathers_name = $con->real_escape_string($_POST['add_fathers_name']);
$add_mothers_name = $con->real_escape_string($_POST['add_mothers_name']);
$add_guardian = $con->real_escape_string($_POST['add_guardian']);
$add_guardian_contact = $con->real_escape_string($_POST['add_guardian_contact']);
$add_image = $con->real_escape_string($_FILES['add_image_residence']['name']);
$add_status = 'ACTIVE';
$user_type = 'resident';

$add_username = $con->real_escape_string($_POST['add_username']);
$add_password = $con->real_escape_string($_POST['add_password']);
$add_confirm_password = $con->real_escape_string($_POST['add_confirm_password']);

if($add_password != $add_confirm_password){
  exit('errorPassword');
}

$sql_check_username = "SELECT username FROM users WHERE username = ?";
$stmt_check = $con->prepare($sql_check_username) or die ($con->error);
$stmt_check->bind_param('s', $add_username);
$stmt_check->execute();
$query_check_username = $stmt_check->get_result();
$count_username = $query_check_username->num_rows;

if($count_username > 0){
  exit('errorUsername');
}


// --- FIXED IMAGE UPLOAD PATH ---
$new_image_name = '';
$new_image_path = '';
if(isset($add_image) && !empty($add_image)){
    $type = explode('.', $add_image);
    $type = $type[count($type) -1];
    $new_image_name = uniqid(rand()) .'.'. $type;
    
    // --- THIS IS THE CORRECT PERMANENT VOLUME PATH ---
    $upload_dir = '../permanent-data/images/';
    $new_image_path = $upload_dir . $new_image_name;

    // Create the directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!move_uploaded_file($_FILES['add_image_residence']['tmp_name'], $new_image_path)) {
        // Handle file move error
        $new_image_name = '';
        $new_image_path = '';
    }
}
// --- END IMAGE FIX ---


$sql_add_user = "INSERT INTO `users`(`id`, `first_name`, `middle_name`, `last_name`, `username`, `password`, `user_type`,`contact_number`, `image`,`image_path`) VALUES (?,?,?,?,?,?,?,?,?,?)";
$stmt_user = $con->prepare($sql_add_user) or die ($con->error);
$stmt_user->bind_param('ssssssssss',$number,$add_first_name,$add_middle_name,$add_last_name,$add_username,$add_password,$user_type,$add_contact_number,$new_image_name,$new_image_path);
$stmt_user->execute();
$stmt_user->close();


$today = date("Y/m/d");
$age = date_diff(date_create($add_birth_date), date_create($today));
$add_age_date = $age->format("%y");

if($add_age_date >= '60'){
  $senior = 'YES';
}else{
  $senior = 'NO';
}

if($add_age_date == '0'){
  $age_add = '';
}else{
  $age_add = $add_age_date;
}

// CREATE A NEW ALIAS VARIABLE
$alias = $add_first_name . ' ' . $add_last_name; 
$add_occupation = ''; // Add a blank occupation

$sql = "INSERT INTO `residence_information`(
  `residence_id`, `first_name`, `middle_name`, `last_name`, `age`, 
  `suffix`, `gender`, `civil_status`, `religion`, `nationality`, 
  `contact_number`, `email_address`, `address`, `birth_date`, `birth_place`, 
  `municipality`, `zip`, `barangay`, `house_number`, `street`, 
  `fathers_name`, `mothers_name`, `guardian`, `guardian_contact`, `image`, 
  `image_path`, `alias`, `occupation`
) 
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"; // 28 '?' marks
$stmt = $con->prepare($sql) or die ($con->error);

$stmt->bind_param('ssssssssssssssssssssssssssss',  // 28 's' characters
  $number, $add_first_name, $add_middle_name, $add_last_name, $age_add,
  $add_suffix, $add_gender, $add_civil_status, $add_religion, $add_nationality,
  $add_contact_number, $add_email_address, $add_address, $add_birth_date, $add_birth_place,
  $add_municipality, $add_zip, $add_barangay, $add_house_number, $add_street,
  $add_fathers_name, $add_mothers_name, $add_guardian, $add_guardian_contact, $new_image_name,
  $new_image_path, $alias, $add_occupation
);

$stmt->execute();
$stmt->close();

$sql_residence_status = "INSERT INTO `residence_status` (`residence_id`, `status`, `voters`,`archive`,`pwd`,`pwd_info`,`single_parent`,`senior`, `date_added`) VALUES (?,?,?,?,?,?,?,?,?)";
$stmt_residence_status = $con->prepare($sql_residence_status) or die ($con->error);
$stmt_residence_status->bind_param('sssssssss',$number,$add_status,$add_voters,$archive,$add_pwd,$add_pwd_check,$add_single_parent,$senior,$date_added);
$stmt_residence_status->execute();
$stmt_residence_status->close();

  $date_activity = $now = date("j-n-Y g:i A");  
  $admin = strtoupper('RESIDENT').':' .' '. 'REGISTER RESIDENT -'.' ' .$number.' |' .'  '.$add_first_name .' '. $add_last_name .' '. $add_suffix;
  $status_activity_log = 'create';

  $sql_activity_log = "INSERT INTO activity_log (`message`,`date`,`status`) VALUES (?,?,?)";
  $stmt_activity_log = $con->prepare($sql_activity_log) or die ($con->error);
  $stmt_activity_log->bind_param('sss',$admin,$date_activity,$status_activity_log);
  $stmt_activity_log->execute();
  $stmt_activity_log->close();

  // Prepare data for the email function
  $email = $add_email_address;
  $firstName = $add_first_name;
  $lastName = $add_last_name;

  $resident_data_for_email = [
    'Full Name'        => $firstName . ' ' . $add_middle_name . ' ' . $lastName . ' ' . $add_suffix,
    'Date of Birth'    => $add_birth_date,
    'Contact Number'   => $add_contact_number,
    'Email Address'    => $add_email_address,
    'Address'          => $add_address,
    'Username'         => $add_username,
  ];
  
  // Call the function to send the email
  if (!empty($email)) {
    sendBarangayWelcomeEmail($email, $firstName . ' ' . $lastName, $resident_data_for_email);
  }
  
  // --- NEW: CALL THE SMS FUNCTION ---
  if (!empty($add_contact_number)) {
      // Send to the phone number they registered with
      sendWelcomeSMS($add_contact_number, $firstName);
  }

  // Send the final success signal back to the AJAX handler
  echo 'success';

}catch(\Throwable $t){ // <-- Catch  all errors here too
  echo $t->getMessage(); // <-- This will now show the fatal error
}
?>