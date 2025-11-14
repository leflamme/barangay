<?php 
session_start();
// Load Composer Autoloader 
require __DIR__ . '/../vendor/autoload.php';
// Load the database connection
require '../connection.php';
// Import PHPmailer Classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendBarangayWelcomeEmail($recipientEmail, $recipientName, $userData) {
  $mail = new PHPMailer(true); 
   
  /* For Debugging Purposes
  $mail->SMTPDebug = 2; // Shows client/server conversation
  $mail->Debugoutput = 'error_log'; // Sends this output to your Railway logs
  */

  // --- OLD (Hard-Coded) ---
  // $gmail_username = 'brgykalusugan814@gmail.com'; 
  // $gmail_password = 'sphx awcj lmlv upgc'; 
  // $barangay_name = 'BARANGAY KALUSUGAN'; 

  $gmail_username = getenv('GMAIL_USER');
  $gmail_password = getenv('GMAIL_PASS');
  $barangay_name = getenv('BARANGAY_NAME');
  // ---------------------------------------------------

  try {     
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $gmail_username;
    $mail->Password   = $gmail_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
    $mail->Port       = 465;

    // Sender and Recipient
    $mail->setFrom($gmail_username, $barangay_name . ' Admin');
    $mail->addAddress($recipientEmail, $recipientName);

    // Content
    $mail->isHTML(true);
    $mail->Subject = "✅ Welcome! Your Registration to {$barangay_name} System is Complete";
        
    // Build the email body
    $body = "<h2>Mabuhay, {$recipientName}!</h2>";
    $body .= "<p>Your registration is successful. Welcome!</p>";
    $body .= "<h3>Summary of Details:</h3>";
        
    // This dynamically lists the resident's data
    $body .= "<ul style='list-style-type: none; padding: 0;'>";
    foreach ($userData as $key => $value) {
      if (!empty($value)) {
        $body .= "<li><strong>" . ucfirst($key) . ":</strong> " . htmlspecialchars($value) . "</li>";
      }
    }
    $body .= "</ul>";
        
    $body .= "<p>You may now log in using your registered username and password.</p>";
    $mail->Body    = $body;

    $mail->send();
    return true;

    } catch (Exception $e) {
      // Log the error (optional but recommended)
      error_log("Barangay Mailer Error: " . $mail->ErrorInfo);
      return false;
    }
}

try{  
date_default_timezone_set('Asia/Manila');
$date = new DateTime();

// NEW ID GENERATOR
$number = mt_rand(100000, 999999) . $date->format("mdHis");

$date_added = date("m/d/Y h:i A");
$archive = 'NO';


date_default_timezone_set('Asia/Manila');
$date = new DateTime();

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

$password = $date->format("mdYHisv");


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




if(isset($add_image)){
  if($add_image != '' || $add_image != null || !empty($add_image)){
    $type = explode('.', $add_image);
    $type = $type[count($type) -1];
    $new_image_name = uniqid(rand()) .'.'. $type;
    $new_image_path = '../assets/dist/img/' . $new_image_name;
    move_uploaded_file($_FILES['add_image_residence']['tmp_name'],$new_image_path);
  }else{
    $new_image_name = '';
    $new_image_path = '';
  }
}


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
  $add_fathers_name, $add_mothers_name, $add_guardian, $add_guardian_contact, $new_image_name, // [!code focus]
  $new_image_path, $alias, $add_occupation // Add the new occupation variable here
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
  // $alias = 'RESIDENT'; // <-- DELETE THIS LINE

  // REMOVE `alias` from the query
  $sql_activity_log = "INSERT INTO activity_log (`message`,`date`,`status`) VALUES (?,?,?)";
  $stmt_activity_log = $con->prepare($sql_activity_log) or die ($con->error);

  // REMOVE 's' and $alias
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
  // This runs ONLY if the database queries above were successful.
  sendBarangayWelcomeEmail($email, $firstName . '' . $lastName, $resident_data_for_email);

  // Send the final success signal back to the AJAX handler
  echo 'success';

  // Call the function to send the email
  $emailSent = sendBarangayWelcomeEmail($email, $firstName . ' ' . $lastName, $resident_data_for_email);

  if ($emailSent) {
    // Send the final success signal back to the AJAX handler
    echo 'success';
  } else {
    // Email failed, but registration worked. Send a specific message.
    // Your JS doesn't handle this, but it's better than a false 'success'
    echo 'Registration successful, but the welcome email could not be sent.'; 
  }
}catch(Exception $e){
  echo $e->getMessage();
}
?>