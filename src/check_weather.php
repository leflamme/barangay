<?php
// check_weather.php - PRODUCTION BROADCAST MODE
// Monitors Weather + Earthquake and Broadcasts to ALL Residents
ini_set('max_execution_time', 300); // 5 minutes execution time
require __DIR__ . '/vendor/autoload.php';
require 'connection.php'; 

// ==========================================
//   CONFIGURATION
// ==========================================
// Brgy Kalusugan, QC Coordinates
$USER_LAT = 14.6231;
$USER_LON = 121.0219;
$ALERT_RADIUS_KM = 300.0;
$MIN_MAGNITUDE = 4.0;

// API Keys & Env
$owm_api_key = getenv('OWM_API_KEY');
$barangay_name = getenv('BARANGAY_NAME');
$resend_api_key = getenv('RESEND_API_KEY');
$flask_api_url = 'http://barangay_api.railway.internal:8080/predict';

// PhilSMS Credentials
$PHILSMS_URL = "https://dashboard.philsms.com/api/v3/";
$PHILSMS_KEY = "554|CayRg2wWAqSX68oeKVh7YmEg5MXKVVtemT16dIos75bdf39f";

// ==========================================
//   HELPER: BROADCAST FUNCTION
// ==========================================
function broadcastToResidents($con, $type, $message) {
    global $PHILSMS_URL, $PHILSMS_KEY;
    
    echo "📢 STARTING BROADCAST: $type\n";
    $resend_key = getenv('RESEND_API_KEY');

    // 1. Fetch ALL Residents
    $sql = "SELECT r.contact_number, r.email_address, u.first_name 
            FROM users u
            JOIN residence_information r ON u.id = r.residence_id
            WHERE u.user_type = 'resident'";
    $result = $con->query($sql);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $phone = $row['contact_number'];
            $email = $row['email_address'];
            $name  = $row['first_name'];

            // --- A. PhilSMS (SMS) ---
            if (!empty($phone)) {
                // Format: 09... or 9... -> 639...
                $clean_phone = preg_replace('/[^0-9]/', '', $phone);
                if (substr($clean_phone, 0, 1) == "0") $final_phone = "63" . substr($clean_phone, 1);
                elseif (substr($clean_phone, 0, 1) == "9") $final_phone = "63" . $clean_phone;
                else $final_phone = $clean_phone;

                $ch = curl_init($PHILSMS_URL);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    "recipient" => $final_phone,
                    "sender_id" => "PhilSMS",
                    "message"   => $message
                ]));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $PHILSMS_KEY", "Content-Type: application/json"]);
                $resp = curl_exec($ch);
                curl_close($ch);
                // echo "SMS sent to $final_phone\n"; // Uncomment to debug
            }

            // --- B. Resend (Email) ---
            if (!empty($resend_key) && !empty($email)) {
                $subject = ($type == 'EARTHQUAKE') ? "🚨 EARTHQUAKE ALERT - QC" : "⚠️ WEATHER WARNING";
                $html = "<h1>{$type} ALERT</h1><p>Dear {$name},</p><p>{$message}</p>";

                $ch = curl_init('https://api.resend.com/emails');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    'from' => "Barangay Alert <onboarding@resend.dev>",
                    'to' => [$email], 
                    'subject' => $subject, 
                    'html' => $html
                ]));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $resend_key, 'Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);
            }
            
            // Anti-Spam throttle (Optional: sleep 0.2s)
            usleep(200000); 
        }
        echo "✅ Broadcast Complete.\n";
    } else {
        echo "⚠️ No residents found in database.\n";
    }
}

// ==========================================
//   LOGIC 1: EARTHQUAKE DETECTION
// ==========================================
function getDistance($lat1, $lon1, $lat2, $lon2) {
    $R = 6371; 
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $R * $c;
}

$start_time = gmdate("Y-m-d\TH:i:s", time() - 900); // Last 15 mins
$emsc_url = "https://www.seismicportal.eu/fdsnws/event/1/query?format=json&starttime={$start_time}&minlat=5.0&maxlat=20.0&minlon=115.0&maxlon=127.0&minmag={$MIN_MAGNITUDE}";

$quake_json = @file_get_contents($emsc_url);
if ($quake_json) {
    $data = json_decode($quake_json, true);
    if (!empty($data['features'])) {
        foreach ($data['features'] as $quake) {
            $props = $quake['properties'];
            $coords = $quake['geometry']['coordinates'];
            $q_lon = $coords[0];
            $q_lat = $coords[1];
            $mag = $props['mag'];
            $place = $props['flynn_region'] ?? "Unknown";
            $q_id = $quake['id'];

            $dist = getDistance($USER_LAT, $USER_LON, $q_lat, $q_lon);

            if ($dist <= $ALERT_RADIUS_KM) {
                // Deduplication Logic
                $last_id_file = 'last_quake_id.txt';
                $last_alerted_id = file_exists($last_id_file) ? file_get_contents($last_id_file) : '';

                if (trim($last_alerted_id) !== $q_id) {
                    $msg = "ALERT: Mag {$mag} Earthquake detected in {$place}. Dist: " . number_format($dist, 1) . "km. Prepare for shaking.";
                    
                    // BROADCAST TO ALL
                    broadcastToResidents($con, "EARTHQUAKE", $msg);
                    
                    file_put_contents($last_id_file, $q_id);
                }
            }
        }
    }
}

// ==========================================
//   LOGIC 2: WEATHER DETECTION
// ==========================================
$stmt_history = $con->prepare("SELECT flood_history FROM barangay_information LIMIT 1");
$stmt_history->execute();
$flood_history = $stmt_history->get_result()->fetch_assoc()['flood_history'] ?? 'rare';

$owm_url = "https://api.openweathermap.org/data/2.5/weather?lat={$USER_LAT}&lon={$USER_LON}&appid={$owm_api_key}&units=metric";
$weather_json = @file_get_contents($owm_url);

if ($weather_json) {
    $weather_data = json_decode($weather_json, true);
    $rain_mm = $weather_data['rain']['1h'] ?? 0;
    
    $cat = 'light';
    if ($rain_mm > 50) $cat = 'heavy';
    elseif ($rain_mm > 7.5) $cat = 'moderate';

    $payload = json_encode(['rainfall_category' => $cat, 'rainfall_amount_mm' => (float)$rain_mm, 'flood_history' => $flood_history]);
    $ch = curl_init($flask_api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $ai_resp = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    $pred = $ai_resp['prediction'] ?? 'normal';
    
    $curr_status = $con->query("SELECT status FROM current_alert_status WHERE id = 1")->fetch_assoc()['status'];
    
    if ($pred != $curr_status) {
        $con->query("UPDATE current_alert_status SET status = '$pred' WHERE id = 1");
        echo "Weather Update: $pred\n";
        
        if ($pred == 'evacuate' || $pred == 'warn') {
            $msg = ($pred == 'evacuate') ? "URGENT {$barangay_name}: EVACUATE NOW. Severe flooding." : "WARNING {$barangay_name}: Heavy rain detected.";
            
            // BROADCAST TO ALL
            broadcastToResidents($con, "WEATHER", $msg);
        }
    }
}
?>