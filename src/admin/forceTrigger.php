<?php 
session_start();
require __DIR__ . '/../vendor/autoload.php';
include_once '../connection.php';

// --- DEBUG: CHECK VARIABLES ---
// This will help you see if Railway/Env vars are actually loading
$vars_status = [
    'Twilio SID' => !empty(getenv('TWILIO_SID')) ? '✅ Loaded' : '❌ Missing',
    'Twilio Token' => !empty(getenv('TWILIO_TOKEN')) ? '✅ Loaded' : '❌ Missing',
    'Twilio Number' => !empty(getenv('TWILIO_PHONE_NUMBER')) ? '✅ Loaded' : '❌ Missing',
    'Resend Key' => !empty(getenv('RESEND_API_KEY')) ? '✅ Loaded' : '❌ Missing',
];
// ------------------------------

function broadcastEmergencyAlerts($con, $alert_type) {
    $barangay_name = getenv('BARANGAY_NAME');
    $resend_api_key = getenv('RESEND_API_KEY');
    $twilio_sid = getenv('TWILIO_SID');
    $twilio_token = getenv('TWILIO_TOKEN');
    $twilio_number = getenv('TWILIO_PHONE_NUMBER');

    $sql = "SELECT r.email_address, r.contact_number, u.first_name 
            FROM users u
            JOIN residence_information r ON u.id = r.residence_id
            WHERE u.user_type = 'resident'";
            
    $result = $con->query($sql);
    
    if ($result->num_rows == 0) return "No residents found.";

    $count_email = 0;
    $count_sms = 0;

    while($row = $result->fetch_assoc()) {
        $email = $row['email_address'];
        $phone = $row['contact_number'];
        $name = $row['first_name'];

        // --- SMS (Twilio) ---
        if (!empty($phone) && !empty($twilio_sid)) {
            $clean_phone = preg_replace('/[^0-9]/', '', $phone);
            if (substr($clean_phone, 0, 1) == '0') $clean_phone = '63' . substr($clean_phone, 1);
            $final_phone = '+' . $clean_phone;
            
            $sms_body = ($alert_type == 'evacuate') 
                ? "🚨 URGENT {$barangay_name}: EVACUATE NOW. Severe flooding expected."
                : "⚠️ WARNING {$barangay_name}: Heavy rain detected. Stay alert.";

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

            if ($code == 200 || $code == 201) $count_sms++;
        }

        // --- EMAIL (Resend) ---
        if (!empty($email) && !empty($resend_api_key)) {
            $subject = ($alert_type == 'evacuate') ? "🚨 EVACUATE NOW - {$barangay_name}" : "⚠️ WEATHER WARNING - {$barangay_name}";
            $html = "<h1>{$subject}</h1><p>Dear {$name}, please take action immediately.</p>";

            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'from' => "Barangay Alert <onboarding@resend.dev>",
                'to' => [$email], 'subject' => $subject, 'html' => $html
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $resend_api_key, 'Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resp = curl_exec($ch);
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) $count_email++;
            curl_close($ch);
        }
    }
    return "Sent {$count_sms} SMS and {$count_email} Emails.";
}

// --- MAIN LOGIC ---
$page_message = ""; 
try {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
        echo '<script>window.location.href = "../login.php";</script>'; exit;
    }
    // ... (User fetching code hidden for brevity, same as before) ...
    
    // FETCH HISTORY (Needed for simulation)
    $stmt_info = $con->prepare("SELECT flood_history FROM barangay_information LIMIT 1");
    $stmt_info->execute();
    $flood_history = $stmt_info->get_result()->fetch_assoc()['flood_history'] ?? 'rare';

    // TRIGGER LOGIC
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

        // 3. Force Database Update & Alert
        if ($status != 'Error') {
            $con->query("UPDATE current_alert_status SET status = '$status' WHERE id = 1");
            $page_message = "Triggered: <strong>$trigger</strong>. Result: <strong>$status</strong>.";
            
            if ($status == 'evacuate' || $status == 'warn') {
                $page_message .= "<br>Sending Alerts... " . broadcastEmergencyAlerts($con, $status);
            } else {
                $page_message .= "<br>Status is normal. No alerts sent.";
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
    <section class="content-header"><h1>Force Weather Trigger</h1></section>
    <section class="content">
      <div class="container-fluid">
        
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">System Health Check</h3></div>
            <div class="card-body">
                <div class="row">
                    <?php foreach($vars_status as $key => $val): ?>
                        <div class="col-md-3">
                            <strong><?= $key ?>:</strong> <?= $val ?>
                        </div>
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