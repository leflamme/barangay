<?php
// Set a long execution time, as this is a background script
ini_set('max_execution_time', 240); // 4 minutes

// 1. LOAD ALL DEPENDENCIES
// Use Composer's autoloader (assumes vendor is in the root: /barangay/vendor)
require __DIR__ . '/../vendor/autoload.php';
// Load your database connection
require 'connection.php';

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 2. CONFIGURATION (All from Railway Environment Variables)
$owm_api_key = getenv('OWM_API_KEY');
$gmail_user = getenv('GMAIL_USER');
$gmail_pass = getenv('GMAIL_PASS');
$barangay_name = getenv('BARANGAY_NAME');

// This is the internal URL Railway gives your Flask API
// It MUST match the service name you created for the API (e.g., 'barangay-api')
$flask_api_url = 'http://barangay_api.railway.internal:8080/predict';

// --- 3. GET BARANGAY FLOOD HISTORY ---
// We need this to send to the model.
$stmt_history = $con->prepare("SELECT flood_history FROM barangay_information LIMIT 1");
$stmt_history->execute();
$result_history = $stmt_history->get_result();
$barangay_info = $result_history->fetch_assoc();
// Default to 'rare' if the database has no value
$flood_history = $barangay_info['flood_history'] ?? 'rare'; 

// --- 4. GET CURRENT WEATHER FROM OPENWEATHERMAP ---
// This is for Kalusugan, Quezon City. Update lat/lon if needed.
$lat = '14.6191';
$lon = '121.0189';
$owm_url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$owm_api_key}&units=metric";

$weather_json = @file_get_contents($owm_url);
if ($weather_json === FALSE) {
    die("Error: Could not fetch from OpenWeatherMap API. Check API key or network.");
}
$weather_data = json_decode($weather_json, true);

// Get rainfall in mm. OWM provides it for '1h' (last hour).
$rainfall_amount_mm = $weather_data['rain']['1h'] ?? 0;

// --- 5. PRE-PROCESS DATA FOR YOUR AI MODEL ---
$rainfall_category = 'light'; // Default
if ($rainfall_amount_mm > 50) {
    $rainfall_category = 'heavy';
} elseif ($rainfall_amount_mm > 10) {
    $rainfall_category = 'moderate';
}

// --- 6. CALL YOUR FLASK API TO GET PREDICTION ---
$api_payload = json_encode([
    'rainfall_category' => $rainfall_category,
    'rainfall_amount_mm' => (float)$rainfall_amount_mm,
    'flood_history' => $flood_history
]);

$ch = curl_init($flask_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $api_payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$api_response_json = curl_exec($ch);

if (curl_errno($ch)) {
    die("Error: cURL failed to connect to Flask API: " . curl_error($ch));
}
curl_close($ch);

$api_response = json_decode($api_response_json, true);
$prediction = $api_response['prediction'] ?? 'normal'; // e.g., "evacuate", "warn", "normal"

// --- 7. CHECK IF STATUS HAS CHANGED & SEND ALERTS ---
$stmt_status = $con->prepare("SELECT status FROM current_alert_status WHERE id = 1");
$stmt_status->execute();
$result_status = $stmt_status->get_result();
$current_status_row = $result_status->fetch_assoc();
$current_status = $current_status_row['status'];

if ($prediction != $current_status) {
    echo "Status changed! From '{$current_status}' to '{$prediction}'. Sending alerts.";

    // A. Update the status in the database
    $stmt_update = $con->prepare("UPDATE current_alert_status SET status = ? WHERE id = 1");
    $stmt_update->bind_param('s', $prediction);
    $stmt_update->execute();

    // B. Send the email alerts
    if ($prediction == 'evacuate' || $prediction == 'warn') {
        // Fetch all resident emails
        $stmt_users = $con->prepare("SELECT email, first_name, last_name FROM users WHERE user_type = 'resident' AND email IS NOT NULL AND email != ''");
        $stmt_users->execute();
        $result_users = $stmt_users->get_result();
        
        while ($user = $result_users->fetch_assoc()) {
            $full_name = $user['first_name'] . ' ' . $user['last_name'];
            sendAlertEmail($user['email'], $full_name, $prediction, $barangay_name, $gmail_user, $gmail_pass);
        }
    }
} else {
    echo "Status '{$current_status}' is unchanged. No alert sent.";
}

// --- 8. PHPMailer Function (re-used from your structure) ---
function sendAlertEmail($to_email, $to_name, $status, $barangay_name, $gmail_user, $gmail_pass) {
    $mail = new PHPMailer(true);
    
    $subject = ($status == 'evacuate') ? "URGENT: EVACUATION NOTICE" : "WEATHER ALERT: Heavy Rainfall Warning";
    $body = ($status == 'evacuate')
        ? "This is an URGENT notice from {$barangay_name}. Due to severe weather conditions, the AI model has triggered an EVACUATION order. Please proceed to the nearest evacuation center."
        : "This is an important alert from {$barangay_name}: Heavy rainfall ahead, stay safe and be prepared.";

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $gmail_user;
        $mail->Password   = $gmail_pass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom($gmail_user, $barangay_name . ' Alert System');
        $mail->addAddress($to_email, $to_name);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        $mail->send();
        echo "Alert sent to {$to_email}\n";
    } catch (Exception $e) {
        // Log the error
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
    }
}
?>