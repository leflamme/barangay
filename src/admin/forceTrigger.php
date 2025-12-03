<?php 
session_start();
require __DIR__ . '/../vendor/autoload.php';
include_once '../connection.php';

// CONFIG
$MY_TEST_EMAIL = 'lawrencejohnmhinanay@tua.edu.ph'; 
$MY_TEST_PHONE = '09274176508'; 

$vars_status = [
    'PhilSMS API' => '✅ Integrated',
    'Resend Key'  => !empty(getenv('RESEND_API_KEY')) ? '✅ Loaded' : '❌ Missing',
];

function sendCombinedAlert($type, $message, $phone, $email) {
    $log = "";
    
    // 1. PhilSMS
    if (!empty($phone)) {
        $clean_phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($clean_phone, 0, 1) == "0") $final_phone = "63" . substr($clean_phone, 1);
        elseif (substr($clean_phone, 0, 1) == "9") $final_phone = "63" . $clean_phone;
        else $final_phone = $clean_phone;

        $ch = curl_init("https://dashboard.philsms.com/api/v3/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "recipient" => $final_phone, "sender_id" => "PhilSMS", "message" => $message
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer 554|CayRg2wWAqSX68oeKVh7YmEg5MXKVVtemT16dIos75bdf39f", "Content-Type: application/json"]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $log .= ($code >= 200 && $code < 300) ? "✅ SMS Sent via PhilSMS.<br>" : "❌ SMS Failed.<br>";
    }

    // 2. Resend
    if (!empty(getenv('RESEND_API_KEY')) && !empty($email)) {
        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'from' => "Barangay Alert <onboarding@resend.dev>",
            'to' => [$email], 
            'subject' => "🚨 TEST ALERT: {$type}", 
            'html' => "<h1>{$type} ALERT</h1><p>{$message}</p>"
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . getenv('RESEND_API_KEY'), 'Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $log .= ($code == 200) ? "✅ Email Sent via Resend.<br>" : "❌ Email Failed.<br>";
    }
    return $log;
}

$page_message = "";
try {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
        echo '<script>window.location.href = "../login.php";</script>'; exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['trigger'])) {
        $trigger = $_POST['trigger'];
        $brgy = getenv('BARANGAY_NAME');

        if ($trigger == 'earthquake') {
            $msg = "ALERT: Mag 6.5 Earthquake detected in TEST REGION. Distance: 50.0km from {$brgy}. Prepare for shaking.";
            $page_message = "<strong>Simulated Earthquake Triggered.</strong><br>" . sendCombinedAlert("EARTHQUAKE", $msg, $MY_TEST_PHONE, $MY_TEST_EMAIL);
        } else {
            // WEATHER LOGIC
            $stmt_info = $con->prepare("SELECT flood_history FROM barangay_information LIMIT 1");
            $stmt_info->execute();
            $hist = $stmt_info->get_result()->fetch_assoc()['flood_history'] ?? 'rare';

            $data = ['flood_history' => $hist, 'rainfall_category' => 'light', 'rainfall_amount_mm' => 0];
            if ($trigger == 'red') $data = ['rainfall_category' => 'heavy', 'rainfall_amount_mm' => 70.0, 'flood_history' => $hist];
            if ($trigger == 'orange') $data = ['rainfall_category' => 'moderate', 'rainfall_amount_mm' => 35.0, 'flood_history' => $hist];

            $ch = curl_init('http://barangay_api.railway.internal:8080/predict');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $ai_resp = json_decode(curl_exec($ch), true);
            curl_close($ch);
            
            $status = $ai_resp['prediction'] ?? 'Error';
            if ($status != 'Error') {
                $con->query("UPDATE current_alert_status SET status = '$status' WHERE id = 1");
                $page_message = "Triggered: <strong>$trigger</strong>. Result: <strong>$status</strong>.<br>";
                
                if ($status == 'evacuate' || $status == 'warn') {
                    $msg = ($status == 'evacuate') ? "URGENT {$brgy}: EVACUATE NOW." : "WARNING {$brgy}: Heavy rain detected.";
                    $page_message .= sendCombinedAlert("WEATHER", $msg, $MY_TEST_PHONE, $MY_TEST_EMAIL);
                }
            }
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
    <section class="content-header"><h1>Force Trigger (Weather & Earthquake)</h1></section>
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
                <small class="text-danger mt-2 d-block">Test Mode: Alerts sent to Admin Only.</small>
            </div>
        </div>

        <?php if (!empty($page_message)): ?>
            <div class="alert alert-warning"><?= $page_message ?></div>
        <?php endif; ?>

        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Weather Simulations</h3></div>
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

        <div class="card card-danger">
            <div class="card-header"><h3 class="card-title">Seismic Simulation</h3></div>
            <div class="card-body">
                <p>Simulate a Magnitude 6.5 Earthquake occurring 50km from Barangay Kalusugan.</p>
                <form method="POST">
                    <button name="trigger" value="earthquake" class="btn btn-danger btn-block btn-lg">
                        <i class="fas fa-house-damage"></i> FORCE EARTHQUAKE ALERT
                    </button>
                </form>
            </div>
        </div>

      </div>
    </section>
  </div>
</div>
</body>
</html>