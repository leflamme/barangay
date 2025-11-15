<?php 
session_start();
// Load Composer Autoloader and Connection
require __DIR__ . '/../vendor/autoload.php';
include_once '../connection.php';

// Import PHPMailer Classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Sends a mass evacuation email to all residents.
 * Uses BCC for privacy and efficiency.
 */
function sendEvacuationEmailToAll($con, $alert_type) {
    
    $recipients = [];
    // Fetch emails from users table
    $sql_emails = "SELECT email FROM users WHERE user_type = 'resident' AND email IS NOT NULL AND email != ''";
    $result = $con->query($sql_emails);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $recipients[] = $row['email'];
        }
    }

    if (empty($recipients)) {
        return "No residents with email addresses found.";
    }

    $mail = new PHPMailer(true);
    $gmail_username = getenv('GMAIL_USER');
    $gmail_password = getenv('GMAIL_PASS');
    $barangay_name = getenv('BARANGAY_NAME');

    try {     
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $gmail_username;
        $mail->Password   = $gmail_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;

        // Sender
        $mail->setFrom($gmail_username, $barangay_name . ' ALERT SYSTEM');

        // Add all recipients as BCC
        foreach ($recipients as $email) {
            $mail->addBCC($email);
        }

        // --- Email Content ---
        $mail->isHTML(true);
        if ($alert_type == 'evacuate') { // Check for lowercase
            $mail->Subject = "🚨 URGENT: EVACUATION NOTICE for {$barangay_name}";
            $body = "<h2>Mabuhay, {$barangay_name} Residents!</h2>";
            $body .= "<p>This is an **URGENT EVACUATION ALERT**.</p>";
            $body .= "<p>The weather system has reached a critical level, and our system predicts a high probability of severe flooding. For your safety, all residents in flood-prone areas are advised to **EVACUATE IMMEDIATELY**.</p>";
            $body .= "<p>Please proceed to your designated evacuation center. Follow all instructions from barangay officials.</p>";
        } else { // 'warn'
            $mail->Subject = "⚠️ WEATHER ALERT for {$barangay_name}";
            $body = "<h2>Mabuhay, {$barangay_name} Residents!</h2>";
            $body .= "<p>This is a **SEVERE WEATHER ALERT**.</p>";
            $body .= "<p>A heavy rainfall warning is in effect. Our system predicts a high probability of flooding. Please prepare for potential evacuation. Secure your belongings and stay tuned for further announcements.</p>";
        }
        $body .= "<p>Stay safe!</p>";
        $mail->Body = $body;

        $mail->send();
        return "Successfully sent evacuation emails to " . count($recipients) . " residents.";

    } catch (Exception $e) {
      error_log("Barangay Mailer Error: " . $mail->ErrorInfo);
      return "Failed to send emails. Error: " . $mail->ErrorInfo;
    }
}
// --- END HELPER FUNCTION ---


// --- MAIN PAGE LOGIC ---
$page_message = ""; // To show results of the trigger

try{
    if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){
        $user_id = $_SESSION['user_id'];
        
        // Fetch Admin User Info
        $sql_user = "SELECT * FROM `users` WHERE `id` = ? ";
        $stmt_user = $con->prepare($sql_user) or die ($con->error);
        $stmt_user->bind_param('s',$user_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        $row_user = $result_user->fetch_assoc();
        $first_name_user = $row_user['first_name'];
        $last_name_user = $row_user['last_name'];
        $user_image = $row_user['image'];

        // Fetch Barangay Info
        $sql_brgy = "SELECT * FROM `barangay_information`";
        $query_brgy = $con->prepare($sql_brgy) or die ($con->error);
        $query_brgy->execute();
        $result_brgy = $query_brgy->get_result();
        $row_brgy = $result_brgy->fetch_assoc();
        $barangay = $row_brgy['barangay'];
        $zone = $row_brgy['zone'];
        $district = $row_brgy['district'];

        // --- GET FLOOD HISTORY ---
        $sql_info = "SELECT flood_history FROM barangay_information LIMIT 1";
        $stmt_info = $con->prepare($sql_info);
        $stmt_info->execute();
        $result_info = $stmt_info->get_result();
        $barangay_info = $result_info->fetch_assoc();
        $flood_history = $barangay_info['flood_history'] ?? 'rare'; // Default to 'rare'

    }else{
        echo '<script>window.location.href = "../login.php";</script>';
        exit;
    }

    // --- FORM SUBMISSION LOGIC ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['trigger'])) {
        $trigger_type = $_POST['trigger'];
        $simulated_data = [];
        $local_flood_history = $flood_history; // Get history from query above

        // 1. Create Simulated Weather Data based on button pressed
        //    **THIS BLOCK SENDS THE 5 CORRECT FEATURES**
        switch ($trigger_type) {
            case 'red':
                $simulated_data = [
                    'rainfall_category' => 'heavy',
                    'rainfall_amount_mm' => 35.0,
                    'flood_history' => $local_flood_history,
                    'location_type' => 'urban', // Added dummy data
                    'past_response' => 'evacuated' // Added dummy data
                ];
                break;
            case 'orange':
                $simulated_data = [
                    'rainfall_category' => 'moderate',
                    'rainfall_amount_mm' => 20.0,
                    'flood_history' => $local_flood_history,
                    'location_type' => 'urban', 
                    'past_response' => 'warned'
                ];
                break;
            case 'yellow':
                $simulated_data = [
                    'rainfall_category' => 'light',
                    'rainfall_amount_mm' => 10.0,
                    'flood_history' => $local_flood_history,
                    'location_type' => 'urban',
                    'past_response' => 'monitored'
                ];
                break;
            case 'normal':
            default:
                $simulated_data = [
                    'rainfall_category' => 'light',
                    'rainfall_amount_mm' => 0.0,
                    'flood_history' => $local_flood_history,
                    'location_type' => 'urban',
                    'past_response' => 'none'
                ];
                break;
        }

        // 2. Call the AI Model (Flask API)
      // ...
      $flask_api_url = 'http://barangay_api.railway.internal:8080/predict';
      
      // --- START NEW CODE (Attempt 8) ---
      // This is a special attempt to fix a contradictory API.
      // The API returns a 415 error if Content-Type is NOT 'application/json'.
      // The API returns "columns missing" if the data is *sent* as JSON.
      // This implies the API checks for the JSON header, but *reads* from form data.
      // We will send Form Data, but *lie* about the Content-Type.

      $ch = curl_init($flask_api_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, true);

      // 1. Send the data as a URL-encoded string (Form Data)
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($simulated_data));
      
      // 2. Set the Content-Type header to 'application/json' (to pass the 415 check)
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
      // --- END NEW CODE ---
      
      $response = curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      curl_close($ch);
        
        $result_data = json_decode($response, true);
        $new_status = $result_data['prediction'] ?? 'Error';

        if ($new_status == 'Error' || $http_code != 200) {
            $page_message = "Error calling AI API. HTTP Status: {$http_code}. Response: " . $response;
        } else {
            // 3. Check current status from DB
            $sql_check = "SELECT status FROM current_alert_status WHERE id = 1";
            $result_check = $con->query($sql_check);
            $row_check = $result_check->fetch_assoc();
            $current_status = $row_check['status'];

            $page_message = "Triggered '{$trigger_type}'. AI Prediction: <strong>{$new_status}</strong>. ";

            // 4. Compare and Update if status changed
            if ($new_status != $current_status) {
                $page_message .= "Status changed from '{$current_status}'. Updating database. ";
                
                $sql_update = "UPDATE current_alert_status SET status = ? WHERE id = 1";
                $stmt_update = $con->prepare($sql_update);
                $stmt_update->bind_param('s', $new_status);
                $stmt_update->execute();

                // 5. Send Email Blast if needed (e.g., 'evacuate' or 'warn')
                if ($new_status == 'evacuate' || $new_status == 'warn') {
                    $email_result = sendEvacuationEmailToAll($con, $new_status);
                    $page_message .= $email_result;
                }
            } else {
                $page_message .= "Status '{$new_status}' is unchanged. No action taken.";
            }
        }
    }

}catch(Exception $e){
  echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Force Weather Trigger</title>
  
  <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #ffffff;
    }
    .wrapper, .content-wrapper, .main-footer, .content, .content-header {
      background-color: #ffffff !important;
      color: #050C9C;
    }
    .main-header.navbar {
      background-color: #050C9C !important;
    }
    .navbar .nav-link, .navbar .nav-link:hover {
      color: #ffffff !important;
    }
    .main-sidebar {
      background-color: #050C9C !important;
    }
    .brand-link {
      background-color: transparent !important;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .sidebar .nav-link {
      color: #A7E6FF !important;
    }
    .sidebar .nav-link.active, .sidebar .nav-link:hover {
      background-color: #3572EF !important;
      color: #ffffff !important;
    }
    .sidebar .nav-icon {
      color: #3ABEF9 !important;
    }
    .card {
        border-radius: 12px;
    }
    .card-primary .card-header {
        background-color: #050C9C;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-footer-fixed">
<div class="wrapper">

  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble " src="../assets/dist/img/loader.gif" alt="Loader" height="70" width="70">
  </div>

  <nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
      <li class="nav-item">
        <h5><a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></h5>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="myProfile.php" class="dropdown-item">
            <div class="media">
              <?php 
                if(!empty($user_image)){
                  echo '<img src="../assets/dist/img/'.$user_image.'" class="img-size-50 mr-3 img-circle" alt="User Image">';
                } else {
                  echo '<img src="../assets/dist/img/image.png" class="img-size-50 mr-3 img-circle" alt="User Image">';
                }
              ?>
              <div class="media-body">
                <h3 class="dropdown-item-title py-3">
                  <?= ucfirst($first_name_user) .' '. ucfirst($last_name_user) ?>
                </h3>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a href="../logout.php" class="dropdown-item dropdown-footer">LOGOUT</a>
        </div>
      </li>
    </ul>
  </nav>
  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <img src="../assets/logo/ksugan.jpg" alt="Barangay Logo" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">

    <div class="sidebar">
      <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="allOfficial.php" class="nav-link">
              <i class="nav-icon fas fa-users-cog"></i><p>Barangay Official</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="allResidence.php" class="nav-link">
              <i class="nav-icon fas fa-users"></i><p>Residence</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="editRequests.php" class="nav-link">
                <i class="nav-icon fas fa-user-edit"></i><p>Edit Requests</p>
            </a>
          </li>
           <li class="nav-item">
            <a href="forceTrigger.php" class="nav-link active">
                <i class="nav-icon fas fa-broadcast-tower"></i><p>Force Trigger</p>
            </a>
          </li>
          </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Force Weather Trigger</h1>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        
        <?php if (!empty($page_message)): ?>
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-info"></i> Trigger Result:</h5>
                <?= $page_message ?>
            </div>
        <?php endif; ?>

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Select Simulated Weather Condition</h3>
            </div>
            <div class="card-body">
                <p>Click a button to force the system to simulate a weather condition. This will call the AI model and trigger any necessary alerts, including evacuation emails, just as the real cron job would.</p>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-3">
                            <button type="submit" name="trigger" value="normal" class="btn btn-lg btn-block btn-success">
                                <i class="fas fa-sun"></i> Normal
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" name="trigger" value="yellow" class="btn btn-lg btn-block btn-warning">
                                <i class="fas fa-cloud-sun-rain"></i> Yellow Alert
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" name="trigger" value="orange" class="btn btn-lg btn-block" style="background-color: #fd7e14; color: white;">
                                <i class="fas fa-cloud-showers-heavy"></i> Orange Alert
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" name="trigger" value="red" class="btn btn-lg btn-block btn-danger">
                                <i class="fas fa-poo-storm"></i> Red Alert (Evacuate)
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
          
      </div></section>
    </div>
  <footer class="main-footer">
    <strong>&copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')) ?></strong>
  </footer>
</div>
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>

</body>
</html>