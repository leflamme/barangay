<?php 
session_start();
require __DIR__ . '/../vendor/autoload.php';
include_once '../connection.php';

// ==========================================
//   USER CONFIGURATION (TESTING MODE)
// ==========================================
// 1. Your Resend Signup Email:
$MY_TEST_EMAIL = 'lawrencejohnmhinanay@tua.edu.ph'; 

// 2. Your Phone Number for PhilSMS testing
// (PhilSMS usually handles 09... or 639... formats)
$MY_TEST_PHONE = '09274176508'; 
// ==========================================

// --- DEBUG: CHECK VARIABLES ---
// Updated to check Resend only, as PhilSMS credentials are hardcoded below
$vars_status = [
    'PhilSMS API'  => '✅ Integrated (Hardcoded)',
    'Resend Key'   => !empty(getenv('RESEND_API_KEY')) ? '✅ Loaded' : '❌ Missing',
];

function broadcastEmergencyAlerts($con, $alert_type, $test_email, $test_phone) {
    $barangay_name = getenv('BARANGAY_NAME');
    $resend_api_key = getenv('RESEND_API_KEY');

    // PhilSMS Credentials (From your file)
    $philsms_url = "https://dashboard.philsms.com/api/v3/sms/send";
    $philsms_key = "81|dLhVxHfXMFlYlfpofWvPjeKoXYiI3g6OnsP9dhNEbaa33ce3";

    $debug_log = "<strong>Test Mode Active:</strong> Sending only to Admin.<br>";

    // --- 1. FORCE SEND SMS (PhilSMS Integration) ---
    if (!empty($test_phone)) {
        
        // PHONE FORMATTING LOGIC
        // PhilSMS expects "639..." format.
        $clean_phone = preg_replace('/[^0-9]/', '', $test_phone);
        
        // Convert 09... to 639...
        if (substr($clean_phone, 0, 1) == "0") {
            $final_phone = "63" . substr($clean_phone, 1);
        } 
        // Convert 9... to 639...
        elseif (substr($clean_phone, 0, 1) == "9") {
            $final_phone = "63" . $clean_phone;
        } 
        // Assume it's already 639... or 11 digits
        else {
            $final_phone = $clean_phone;
        }

        $sms_body = ($alert_type == 'evacuate') 
            ? "URGENT {$barangay_name}: EVACUATE NOW. Severe flooding expected."
            : "WARNING {$barangay_name}: Heavy rain detected. Stay alert.";

        // Prepare Data
        $data = [
            "recipient" => $final_phone,
            "sender_id" => "PhilSMS", 
            "message"   => $sms_body,
        ];

        // Send Request via cURL
        $ch = curl_init($philsms_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $philsms_key,
            "Content-Type: application/json",
            "Accept: application/json"
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Analyze Response
        // PhilSMS typically returns JSON. We decode it to check success.
        $resp_json = json_decode($response, true);
        
        // Check if the API request was successful (HTTP 200/201)
        if ($http_code >= 200 && $http_code < 300) {
            $debug_log .= "<span style='color:green'>✅ SMS sent via PhilSMS to {$final_phone}</span><br>";
        } else {
            // Try to get error message from response
            $error_msg = $resp_json['message'] ?? 'Unknown Error';
            $debug_log .= "<span style='color:red'>❌ SMS Failed: {$error_msg} (HTTP {$http_code})</span><br>";
        }
    }

    // --- 2. FORCE SEND EMAIL (To Admin Only) ---
    // (This part remains unchanged using Resend)
    if (!empty($resend_api_key) && !empty($test_email)) {
        $subject = ($alert_type == 'evacuate') ? "🚨 EVACUATE NOW - {$barangay_name}" : "⚠️ WEATHER WARNING - {$barangay_name}";
        $html = "<h1>System Test</h1><p>This is a test alert sent to the admin email: <strong>{$test_email}</strong></p>";

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'from' => "Barangay Alert <onboarding@resend.dev>",
            'to' => [$test_email], 
            'subject' => $subject, 
            'html' => $html
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $resend_api_key, 'Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($code == 200) {
            $debug_log .= "<span style='color:green'>✅ Email sent to {$test_email}</span><br>";
        } else {
            $debug_log .= "<span style='color:red'>❌ Email Failed ({$test_email}): HTTP {$code}</span><br>";
        }
    }

    return $debug_log;
}

// --- MAIN LOGIC ---
$page_message = ""; 
try {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
        echo '<script>window.location.href = "../login.php";</script>'; exit;
    }

    $stmt_info = $con->prepare("SELECT flood_history FROM barangay_information LIMIT 1");
    $stmt_info->execute();
    $flood_history = $stmt_info->get_result()->fetch_assoc()['flood_history'] ?? 'rare';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['trigger'])) {
        $trigger = $_POST['trigger'];
        
        $data = ['flood_history' => $flood_history, 'rainfall_category' => 'light', 'rainfall_amount_mm' => 0];
        if ($trigger == 'red') $data = ['rainfall_category' => 'heavy', 'rainfall_amount_mm' => 70.0, 'flood_history' => $flood_history];
        if ($trigger == 'orange') $data = ['rainfall_category' => 'moderate', 'rainfall_amount_mm' => 35.0, 'flood_history' => $flood_history];
        if ($trigger == 'yellow') $data = ['rainfall_category' => 'light', 'rainfall_amount_mm' => 10.0, 'flood_history' => $flood_history];

        $ch = curl_init('http://barangay_api.railway.internal:8080/predict');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        $status = $result['prediction'] ?? 'Error';

        if ($status != 'Error') {
            $con->query("UPDATE current_alert_status SET status = '$status' WHERE id = 1");
            $page_message = "Triggered: <strong>$trigger</strong>. Result: <strong>$status</strong>.<br>";
            
            if ($status == 'evacuate' || $status == 'warn') {
                $page_message .= "<hr>" . broadcastEmergencyAlerts($con, $status, $MY_TEST_EMAIL, $MY_TEST_PHONE);
            }
        } else {
            $page_message = "Error calling AI: " . $response;
        }
    }
} catch(Exception $e) { echo $e->getMessage(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <div class="content-wrapper">
    <section class="content-header"><h1>Force Weather Trigger (ADMIN ONLY)</h1></section>
    <section class="content">
      <div class="container-fluid">
        
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">System Health Check</h3></div>
            <div class="card-body">
                <div class="row">
                    <?php foreach($vars_status as $key => $val): ?>
                        <div class="col-md-3"><strong><?= $key ?>:</strong> <?= $val ?></div>
                    <?php endforeach; ?>
                </div>
                <small class="text-danger mt-2 d-block">
                    Note: Using PhilSMS for SMS and Resend for Email.
                </small>
            </div>
        </div>

        <?php if (!empty($page_message)): ?>
            <div class="alert alert-warning"><?= $page_message ?></div>
        <?php endif; ?>

        <div class="card card-primary">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-3"><button name="trigger" value="normal" class="btn btn-success btn-block">Normal</button></div>
                        <div class="col-md-3"><button name="trigger" value="yellow" class="btn btn-warning btn-block">Yellow</button></div>
                        <div class="col-md-3"><button name="trigger" value="orange" class="btn btn-block" style="background:#fd7e14;color:white">Orange</button></div>
                        <div class="col-md-3"><button name="trigger" value="red" class="btn btn-danger btn-block">Red (Evacuate)</button></div>
                    </div>
                </form>
            </div>
        </div>
      </div>
    </section>
  </div>
</div>
</body>
</html>