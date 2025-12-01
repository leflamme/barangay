<?php
// check_weather.php - AUTOMATED SCRIPT
// Set execution time (4 mins)
ini_set('max_execution_time', 240);

// Load Database & Composer
require __DIR__ . '/vendor/autoload.php';
require 'connection.php'; 

// --- CONFIGURATION ---
// 1. UPDATE THESE WITH YOUR DETAILS (Same as forceTrigger.php)
$MY_TEST_EMAIL = 'lawrencejohnmhinanay@tua.edu.ph'; 
$MY_TEST_PHONE = '9274176508'; 

// 2. Load Env Vars
$owm_api_key = getenv('OWM_API_KEY');
$barangay_name = getenv('BARANGAY_NAME');
$resend_api_key = getenv('RESEND_API_KEY');
$twilio_sid = getenv('TWILIO_SID');
$twilio_token = getenv('TWILIO_TOKEN');
$twilio_number = getenv('TWILIO_PHONE_NUMBER');
$flask_api_url = 'http://barangay_api.railway.internal:8080/predict';

// 3. GET BARANGAY HISTORY
$stmt_history = $con->prepare("SELECT flood_history FROM barangay_information LIMIT 1");
$stmt_history->execute();
$flood_history = $stmt_history->get_result()->fetch_assoc()['flood_history'] ?? 'rare';

// 4. GET WEATHER (OpenWeatherMap)
// Coordinates for Kalusugan, QC
$lat = '14.6191'; 
$lon = '121.0189';
$owm_url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$owm_api_key}&units=metric";

$weather_json = @file_get_contents($owm_url);
if ($weather_json === FALSE) {
    die("Error: OpenWeatherMap API unreachable.");
}
$weather_data = json_decode($weather_json, true);
$rainfall_amount_mm = $weather_data['rain']['1h'] ?? 0;

// 5. PREPARE DATA FOR AI
$rainfall_category = 'light';
if ($rainfall_amount_mm > 50) $rainfall_category = 'heavy';
elseif ($rainfall_amount_mm > 7.5) $rainfall_category = 'moderate';

// 6. CALL PYTHON AI
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

// 7. CHECK STATUS & SEND ALERTS
$stmt_status = $con->prepare("SELECT status FROM current_alert_status WHERE id = 1");
$stmt_status->execute();
$current_status = $stmt_status->get_result()->fetch_assoc()['status'];

// Only trigger if status CHANGED
if ($prediction != $current_status) {
    echo "Status Update: {$current_status} -> {$prediction}. \n";

    // Update DB
    $stmt_update = $con->prepare("UPDATE current_alert_status SET status = ? WHERE id = 1");
    $stmt_update->bind_param('s', $prediction);
    $stmt_update->execute();

    // Send Alerts if Dangerous
    if ($prediction == 'evacuate' || $prediction == 'warn') {
        
        // --- A. SEND SMS (To Verified Admin Only) ---
        if (!empty($twilio_sid) && !empty($MY_TEST_PHONE)) {
            $final_phone = $MY_TEST_PHONE;
            if (substr($final_phone, 0, 1) == '0') $final_phone = '+63' . substr($final_phone, 1);
            elseif (substr($final_phone, 0, 1) == '9') $final_phone = '+63' . $final_phone;
            elseif (substr($final_phone, 0, 2) == '63') $final_phone = '+' . $final_phone;

            $sms_body = ($prediction == 'evacuate') 
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
            curl_close($ch);
            echo "SMS Sent attempt to Admin.\n";
        }

        // --- B. SEND EMAIL (To Verified Admin Only) ---
        if (!empty($resend_api_key) && !empty($MY_TEST_EMAIL)) {
            $subject = ($prediction == 'evacuate') ? "🚨 EVACUATE NOW - {$barangay_name}" : "⚠️ WEATHER WARNING - {$barangay_name}";
            $html = "<h1>Automated Alert</h1><p>Weather changed to <strong>{$prediction}</strong>. Rainfall: {$rainfall_amount_mm}mm.</p>";

            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'from' => "Barangay Alert <onboarding@resend.dev>",
                'to' => [$MY_TEST_EMAIL], 
                'subject' => $subject, 
                'html' => $html
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $resend_api_key, 'Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resp = curl_exec($ch);
            curl_close($ch);
            echo "Email Sent attempt to Admin.\n";
        }
    }
} else {
    echo "Status unchanged ({$current_status}). No alerts needed.\n";
}
?>