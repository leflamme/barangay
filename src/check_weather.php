<?php
// check_weather.php

// Set execution time (4 mins)
ini_set('max_execution_time', 240);

// Load Database Connection
require 'connection.php'; // Ensure this path is correct relative to this file

// --- CONFIGURATION FROM ENV ---
$owm_api_key = getenv('OWM_API_KEY');
$barangay_name = getenv('BARANGAY_NAME');
$flask_api_url = 'http://barangay_api.railway.internal:8080/predict';

// Notification Keys
$resend_api_key = getenv('RESEND_API_KEY');
$twilio_sid = getenv('TWILIO_SID');
$twilio_token = getenv('TWILIO_TOKEN');
$twilio_number = getenv('TWILIO_PHONE_NUMBER');

// 1. GET BARANGAY HISTORY
$stmt_history = $con->prepare("SELECT flood_history FROM barangay_information LIMIT 1");
$stmt_history->execute();
$result_history = $stmt_history->get_result();
$barangay_info = $result_history->fetch_assoc();
$flood_history = $barangay_info['flood_history'] ?? 'rare';

// 2. GET WEATHER (OpenWeatherMap)
$lat = '14.6191'; // Kalusugan, QC
$lon = '121.0189';
$owm_url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$owm_api_key}&units=metric";

$weather_json = @file_get_contents($owm_url);
if ($weather_json === FALSE) {
    die("Error: OpenWeatherMap API unreachable.");
}
$weather_data = json_decode($weather_json, true);
$rainfall_amount_mm = $weather_data['rain']['1h'] ?? 0;

// 3. PREPARE DATA FOR AI
// We still categorize roughly for the AI mapping
$rainfall_category = 'light';
if ($rainfall_amount_mm > 50) {
    $rainfall_category = 'heavy';
} elseif ($rainfall_amount_mm > 7.5) {
    $rainfall_category = 'moderate';
}

// 4. CALL PYTHON AI (Revised Logic)
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
curl_close($ch);

$api_response = json_decode($api_response_json, true);
$prediction = $api_response['prediction'] ?? 'normal';

// 5. CHECK STATUS & SEND ALERTS
$stmt_status = $con->prepare("SELECT status FROM current_alert_status WHERE id = 1");
$stmt_status->execute();
$current_status = $stmt_status->get_result()->fetch_assoc()['status'];

if ($prediction != $current_status) {
    echo "Status Update: {$current_status} -> {$prediction}. Sending Notifications...\n";

    // Update DB
    $stmt_update = $con->prepare("UPDATE current_alert_status SET status = ? WHERE id = 1");
    $stmt_update->bind_param('s', $prediction);
    $stmt_update->execute();

    // Only notify on DANGER (Evacuate) or WARNING
    if ($prediction == 'evacuate' || $prediction == 'warn') {
        
        // Fetch Residents
        $sql = "SELECT r.email_address, r.contact_number, u.first_name 
                FROM users u
                JOIN residence_information r ON u.id = r.residence_id
                WHERE u.user_type = 'resident'";
        $result_users = $con->query($sql);

        while ($user = $result_users->fetch_assoc()) {
            $email = $user['email_address'];
            $phone = $user['contact_number']; // Ensure format is +639...
            $name = $user['first_name'];

            // --- A. SEND EMAIL (RESEND API) ---
            if (!empty($email) && !empty($resend_api_key)) {
                sendResendEmail($email, $name, $prediction, $barangay_name, $resend_api_key);
            }

            // --- B. SEND SMS (TWILIO API) ---
            if (!empty($phone) && !empty($twilio_sid)) {
                sendTwilioSMS($phone, $prediction, $barangay_name, $twilio_sid, $twilio_token, $twilio_number);
            }
        }
    }
} else {
    echo "Status unchanged ({$current_status}). No alerts sent.";
}

// ==========================================
//  HELPER FUNCTIONS
// ==========================================

function sendResendEmail($to, $name, $status, $brgy, $apiKey) {
    $subject = ($status == 'evacuate') ? "🚨 URGENT: EVACUATE NOW - {$brgy}" : "⚠️ WEATHER WARNING - {$brgy}";
    
    $html = ($status == 'evacuate') 
        ? "<h1>URGENT EVACUATION ORDER</h1><p>Dear {$name},<br>The AI Flood System has detected critical rainfall levels. <strong>Please EVACUATE immediately</strong> to the nearest center.</p>"
        : "<h1>Weather Warning</h1><p>Dear {$name},<br>Heavy rainfall detected. Please stay alert and prepare for possible evacuation.</p>";

    $data = [
        'from' => "Barangay Alert <onboarding@resend.dev>", // Or your verified domain
        'to' => [$to],
        'subject' => $subject,
        'html' => $html
    ];

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    // echo "Resend Response: " . $response . "\n";
}

function sendTwilioSMS($to, $status, $brgy, $sid, $token, $fromNumber) {
    // Format number: Ensure it starts with +63 if it starts with 0
    if (substr($to, 0, 1) == '0') {
        $to = '+63' . substr($to, 1);
    }

    $body = ($status == 'evacuate') 
        ? "🚨 URGENT {$brgy}: EVACUATE NOW. Severe flooding expected. Proceed to evacuation centers immediately."
        : "⚠️ WARNING {$brgy}: Heavy rain detected. Please stay alert and monitor updates.";

    $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";
    $postData = http_build_query([
        'From' => $fromNumber,
        'To' => $to,
        'Body' => $body
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_USERPWD, "{$sid}:{$token}"); // Basic Auth
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    // echo "Twilio Response: " . $response . "\n";
}
?>