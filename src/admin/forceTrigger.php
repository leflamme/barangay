<?php 
session_start();
// Load Composer Autoloader and Connection
require __DIR__ . '/../vendor/autoload.php';
include_once '../connection.php';

/**
 * BROADCAST FUNCTION
 * Iterates through all residents and sends SMS (Twilio) and Email (Resend).
 */
function broadcastEmergencyAlerts($con, $alert_type) {
    
    // 1. Setup Environment Variables
    $barangay_name = getenv('BARANGAY_NAME');
    $resend_api_key = getenv('RESEND_API_KEY');
    $twilio_sid = getenv('TWILIO_SID');
    $twilio_token = getenv('TWILIO_TOKEN');
    $twilio_number = getenv('TWILIO_PHONE_NUMBER');

    // 2. Fetch Residents (Email AND Phone)
    // We join users and residence_information to get contact details
    $sql = "SELECT r.email_address, r.contact_number, u.first_name, u.last_name 
            FROM users u
            JOIN residence_information r ON u.id = r.residence_id
            WHERE u.user_type = 'resident'";
            
    $result = $con->query($sql);
    
    if ($result->num_rows == 0) {
        return "No residents found in database.";
    }

    $count_email = 0;
    $count_sms = 0;

    // 3. Loop through residents and send alerts
    while($row = $result->fetch_assoc()) {
        $email = $row['email_address'];
        $phone = $row['contact_number'];
        $name = $row['first_name'];

        // --- A. SEND SMS (Twilio) ---
        if (!empty($phone) && !empty($twilio_sid)) {
            // Ensure phone is E.164 format (+63...)
            if (substr($phone, 0, 1) == '0') {
                $phone = '+63' . substr($phone, 1);
            }
            
            $sms_body = ($alert_type == 'evacuate') 
                ? "🚨 URGENT {$barangay_name}: EVACUATE NOW. Severe flooding expected. Proceed to evacuation centers."
                : "⚠️ WARNING {$barangay_name}: Heavy rain detected. Please stay alert.";

            // Send via Twilio REST API
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$twilio_sid}/Messages.json";
            $postData = http_build_query([
                'From' => $twilio_number,
                'To' => $phone,
                'Body' => $sms_body
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_USERPWD, "{$twilio_sid}:{$twilio_token}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code == 201 || $http_code == 200) {
                $count_sms++;
            }
        }

        // --- B. SEND EMAIL (Resend API) ---
        if (!empty($email) && !empty($resend_api_key)) {
            $subject = ($alert_type == 'evacuate') ? "🚨 URGENT: EVACUATE NOW - {$barangay_name}" : "⚠️ WEATHER WARNING - {$barangay_name}";
            $html_body = ($alert_type == 'evacuate') 
                ? "<h1>URGENT EVACUATION ORDER</h1><p>Dear {$name},<br>The AI Flood System has triggered a <strong>RED ALERT</strong>. Please <strong>EVACUATE IMMEDIATELY</strong> to the nearest designated center.</p>"
                : "<h1>Weather Warning</h1><p>Dear {$name},<br>Heavy rainfall has been detected. Please monitor local news and be prepared to evacuate.</p>";

            $data = [
                'from' => "Barangay Alert <onboarding@resend.dev>", // Change to your verified domain if you have one
                'to' => [$email],
                'subject' => $subject,
                'html' => $html_body
            ];

            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $resend_api_key,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code == 200) {
                $count_email++;
            }
        }
    }

    return "Broadcast Complete. Sent {$count_sms} SMS and {$count_email} Emails.";
}
// --- END HELPER FUNCTION ---

// --- MAIN PAGE LOGIC ---
$page_message = ""; 

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
        
        // --- GET FLOOD HISTORY ---
        $sql_info = "SELECT flood_history FROM barangay_information LIMIT 1";
        $stmt_info = $con->prepare($sql_info);
        $stmt_info->execute();
        $result_info = $stmt_info->get_result();
        $barangay_info = $result_info->fetch_assoc();
        $flood_history = $barangay_info['flood_history'] ?? 'rare'; 

    }else{
        echo '<script>window.location.href = "../login.php";</script>';
        exit;
    }

    // --- FORM SUBMISSION LOGIC ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['trigger'])) {
        $trigger_type = $_POST['trigger'];
        $simulated_data = [];
        $local_flood_history = $flood_history;

        // 1. Create Simulated Weather Data
        switch ($trigger_type) {
            case 'red':
                $simulated_data = [
                    'rainfall_category' => 'heavy',
                    'rainfall_amount_mm' => 70.0,
                    'flood_history' => $local_flood_history,
                ];
                break;
            case 'orange':
                $simulated_data = [
                    'rainfall_category' => 'moderate',
                    'rainfall_amount_mm' => 35.0,
                    'flood_history' => $local_flood_history,
                ];
                break;
            case 'yellow':
                $simulated_data = [
                    'rainfall_category' => 'light',
                    'rainfall_amount_mm' => 10.0,
                    'flood_history' => $local_flood_history,
                ];
                break;
            case 'normal':
            default:
                $simulated_data = [
                    'rainfall_category' => 'light',
                    'rainfall_amount_mm' => 0.0,
                    'flood_history' => $local_flood_history,
                ];
                break;
        }

        // 2. Call the AI Model (Flask API)
        $flask_api_url = 'http://barangay_api.railway.internal:8080/predict';
        
        $api_payload = json_encode($simulated_data);

        $ch = curl_init($flask_api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $api_payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
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

            // 4. Update Database
            // For Force Trigger, we usually want to update even if status is same, 
            // but for safety, let's follow standard logic:
            if ($new_status != $current_status || $trigger_type == 'red' || $trigger_type == 'orange') {
                
                $sql_update = "UPDATE current_alert_status SET status = ? WHERE id = 1";
                $stmt_update = $con->prepare($sql_update);
                $stmt_update->bind_param('s', $new_status);
                $stmt_update->execute();

                // 5. BROADCAST ALERTS (Email + SMS)
                if ($new_status == 'evacuate' || $new_status == 'warn') {
                    $page_message .= " Sending alerts... <br>";
                    $broadcast_result = broadcastEmergencyAlerts($con, $new_status);
                    $page_message .= $broadcast_result;
                } else {
                    $page_message .= " Status is normal/yellow. No alerts sent.";
                }
            } else {
                $page_message .= "Status unchanged. No action taken.";
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
    body { font-family: 'Poppins', sans-serif; background-color: #ffffff; }
    .wrapper, .content-wrapper, .main-footer, .content, .content-header { background-color: #ffffff !important; color: #050C9C; }
    .main-header.navbar { background-color: #050C9C !important; }
    .navbar .nav-link, .navbar .nav-link:hover { color: #ffffff !important; }
    .main-sidebar { background-color: #050C9C !important; }
    .brand-link { background-color: transparent !important; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .sidebar .nav-link { color: #A7E6FF !important; }
    .sidebar .nav-link.active, .sidebar .nav-link:hover { background-color: #3572EF !important; color: #ffffff !important; }
    .sidebar .nav-icon { color: #3ABEF9 !important; }
    .card { border-radius: 12px; }
    .card-primary .card-header { background-color: #050C9C; }
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
                <p>Click a button to force the system to simulate a weather condition. This will call the AI model and trigger any necessary alerts (Email & SMS).</p>
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