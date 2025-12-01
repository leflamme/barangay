<?php 
session_start();
require __DIR__ . '/../vendor/autoload.php';
include_once '../connection.php';

// --- DEBUG: CHECK VARIABLES ---
$vars_status = [
    'Twilio SID' => !empty(getenv('TWILIO_SID')) ? '✅ Loaded' : '❌ Missing',
    'Twilio Token' => !empty(getenv('TWILIO_TOKEN')) ? '✅ Loaded' : '❌ Missing',
    'Twilio Number' => !empty(getenv('TWILIO_PHONE_NUMBER')) ? '✅ Loaded' : '❌ Missing',
    'Resend Key' => !empty(getenv('RESEND_API_KEY')) ? '✅ Loaded' : '❌ Missing',
];

function broadcastEmergencyAlerts($con, $alert_type) {
    $barangay_name = getenv('BARANGAY_NAME');
    $resend_api_key = getenv('RESEND_API_KEY');
    $twilio_sid = getenv('TWILIO_SID');
    $twilio_token = getenv('TWILIO_TOKEN');
    $twilio_number = getenv('TWILIO_PHONE_NUMBER');

    // DEBUG: Print Query Info
    $sql = "SELECT r.email_address, r.contact_number, u.first_name 
            FROM users u
            JOIN residence_information r ON u.id = r.residence_id
            WHERE u.user_type = 'resident'";
            
    $result = $con->query($sql);
    $num_rows = $result->num_rows;
    
    // --- DEBUG OUTPUT 1: Database Check ---
    $debug_log = "<strong>Database Check:</strong> Found {$num_rows} resident(s).<br>";
    
    if ($num_rows == 0) {
        $debug_log .= "⚠️ No residents found. Check if users have 'resident' type in database.<br>";
        return $debug_log;
    }

    $count_email = 0;
    $count_sms = 0;

    while($row = $result->fetch_assoc()) {
        $email = $row['email_address'];
        $phone = $row['contact_number'];
        $name = $row['first_name'];

        // --- SMS (Twilio) ---
        if (!empty($phone) && !empty($twilio_sid)) {
            // Phone Formatting
            $clean_phone = preg_replace('/[^0-9]/', '', $phone);
            if (substr($clean_phone, 0, 1) == '0') $clean_phone = '63' . substr($clean_phone, 1);
            $final_phone = '+' . $clean_phone;
            
            $sms_body = "TEST ALERT {$barangay_name}: {$alert_type}.";

            $url = "https://api.twilio.com/2010-04-01/Accounts/{$twilio_sid}/Messages.json";
            $postData = http_build_query(['From' => $twilio_number, 'To' => $final_phone, 'Body' => $sms_body]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_USERPWD, "{$twilio_sid}:{$twilio_token}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resp = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code == 200 || $code == 201) {
                $count_sms++;
                $debug_log .= "✅ SMS sent to ...".substr($final_phone, -4)."<br>";
            } else {
                // --- DEBUG OUTPUT 2: Twilio Error ---
                $resp_json = json_decode($resp, true);
                $error_msg = $resp_json['message'] ?? $resp;
                $debug_log .= "❌ SMS Failed ({$final_phone}): <strong>{$error_msg}</strong> (Code: {$code})<br>";
            }
        }

        // --- EMAIL (Resend) ---
        if (!empty($email) && !empty($resend_api_key)) {
            $subject = "TEST ALERT: {$alert_type}";
            $html = "<p>Test Alert for {$name}</p>";

            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'from' => "Barangay Alert <onboarding@resend.dev>",
                'to' => [$email], 'subject' => $subject, 'html' => $html
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $resend_api_key, 'Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resp = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($code == 200) {
                $count_email++;
                $debug_log .= "✅ Email sent to {$email}<br>";
            } else {
                // --- DEBUG OUTPUT 3: Resend Error ---
                $debug_log .= "❌ Email Failed ({$email}): HTTP {$code}<br>";
            }
        }
    }
    return $debug_log . "<br><strong>Summary:</strong> Sent {$count_sms} SMS and {$count_email} Emails.";
}

// --- MAIN LOGIC ---
$page_message = ""; 
try {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
        echo '<script>window.location.href = "../login.php";</script>'; exit;
    }
    // FETCH HISTORY
    $stmt_info = $con->prepare("SELECT flood_history FROM barangay_information LIMIT 1");
    $stmt_info->execute();
    $flood_history = $stmt_info->get_result()->fetch_assoc()['flood_history'] ?? 'rare';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['trigger'])) {
        $trigger = $_POST['trigger'];
        
        // 1. Simulate Data
        $data = ['flood_history' => $flood_history, 'rainfall_category' => 'light', 'rainfall_amount_mm' => 0];
        if ($trigger == 'red') $data = ['rainfall_category' => 'heavy', 'rainfall_amount_mm' => 70.0, 'flood_history' => $flood_history];
        if ($trigger == 'orange') $data = ['rainfall_category' => 'moderate', 'rainfall_amount_mm' => 35.0, 'flood_history' => $flood_history];
        if ($trigger == 'yellow') $data = ['rainfall_category' => 'light', 'rainfall_amount_mm' => 10.0, 'flood_history' => $flood_history];

        // 2. Call AI
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
                $page_message .= "<hr>" . broadcastEmergencyAlerts($con, $status);
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
    <section class="content-header"><h1>Force Weather Trigger (DEBUG MODE)</h1></section>
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