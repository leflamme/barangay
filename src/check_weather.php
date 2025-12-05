<?php
// check_weather.php - PRODUCTION MODE (FINAL)
// Monitors Weather + Earthquake and Broadcasts Smart Evacuation Routes
ini_set('max_execution_time', 300); // 5 minutes execution time
require __DIR__ . '/vendor/autoload.php';
require 'connection.php'; 

// ==========================================
//   CONFIGURATION
// ==========================================
$USER_LAT = 14.6231;
$USER_LON = 121.0219;
$ALERT_RADIUS_KM = 300.0;
$MIN_MAGNITUDE = 4.0;

$owm_api_key = getenv('OWM_API_KEY');
$barangay_name = getenv('BARANGAY_NAME');
$resend_api_key = getenv('RESEND_API_KEY');
$flask_api_url = 'http://barangay_api.railway.internal:8080/predict';

// PhilSMS Credentials
$PHILSMS_URL = "https://dashboard.philsms.com/api/v3/sms/send";
$PHILSMS_KEY = "554|CayRg2wWAqSX68oeKVh7YmEg5MXKVVtemT16dIos75bdf39f";

// ==========================================
//   HELPER FUNCTIONS
// ==========================================

function getDistance($lat1, $lon1, $lat2, $lon2) {
    if (empty($lat1) || empty($lon1) || empty($lat2) || empty($lon2)) return 99999;
    $R = 6371; 
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $R * $c; // Returns KM
}

function sendSingleSMS($phone, $message) {
    global $PHILSMS_URL, $PHILSMS_KEY;
    $clean_phone = preg_replace('/[^0-9]/', '', $phone);
    if (substr($clean_phone, 0, 1) == "0") $final_phone = "63" . substr($clean_phone, 1);
    elseif (substr($clean_phone, 0, 1) == "9") $final_phone = "63" . $clean_phone;
    else $final_phone = $clean_phone;

    $ch = curl_init($PHILSMS_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        "recipient" => $final_phone, "sender_id" => "PhilSMS", "message" => $message
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $PHILSMS_KEY", "Content-Type: application/json"]);
    curl_exec($ch);
    curl_close($ch);
}

function broadcastToResidents($con, $type, $message) {
    // Standard broadcast for non-evacuation events (like Earthquakes)
    $sql = "SELECT r.contact_number FROM users u JOIN residence_information r ON u.id = r.residence_id WHERE u.user_type = 'resident'";
    $result = $con->query($sql);
    while($row = $result->fetch_assoc()) {
        if (!empty($row['contact_number'])) {
            sendSingleSMS($row['contact_number'], $message);
            usleep(100000); // 0.1s delay to prevent API clogging
        }
    }
}

function processSmartEvacuation($con) {
    // SMART ROUTING LOGIC
    $centers_query = $con->query("SELECT * FROM evacuation_centers");
    $centers = [];
    while ($c = $centers_query->fetch_assoc()) { $centers[] = $c; }

    $residents_query = $con->query("SELECT r.contact_number, r.latitude, r.longitude, u.first_name 
                                    FROM users u 
                                    JOIN residence_information r ON u.id = r.residence_id 
                                    WHERE u.user_type = 'resident'");

    while ($res = $residents_query->fetch_assoc()) {
        if (empty($res['contact_number'])) continue;

        $target_center = null;
        
        if (!empty($res['latitude']) && !empty($res['longitude'])) {
            // 1. Calculate Distances
            $resident_options = [];
            foreach ($centers as $key => $c) {
                $dist = getDistance($res['latitude'], $res['longitude'], $c['latitude'], $c['longitude']);
                $centers[$key]['calc_dist'] = $dist;
                $resident_options[] = ['key_index' => $key, 'dist' => $dist];
            }

            // 2. Sort by Nearest
            usort($resident_options, function($a, $b) { return $a['dist'] <=> $b['dist']; });

            // 3. Find First Available Spot
            foreach ($resident_options as $opt) {
                $idx = $opt['key_index'];
                if ($centers[$idx]['current_occupancy'] < $centers[$idx]['max_capacity']) {
                    $target_center = $centers[$idx];
                    $centers[$idx]['current_occupancy']++; // Reserve spot in memory
                    break;
                }
            }
        }

        // 4. Send SMS
        if ($target_center) {
            $dist_str = number_format($target_center['calc_dist'], 2);
            $msg = "URGENT EVACUATION: Flood detected. Proceed to {$target_center['name']} ({$dist_str}km).";
        } else {
            $msg = "URGENT EVACUATION: Flood detected. Proceed to the nearest safe high ground.";
        }
        
        sendSingleSMS($res['contact_number'], $msg);
        usleep(100000); 
    }
}

// ==========================================
//   LOGIC 1: EARTHQUAKE DETECTION
// ==========================================
$start_time = gmdate("Y-m-d\TH:i:s", time() - 900);
$emsc_url = "https://www.seismicportal.eu/fdsnws/event/1/query?format=json&starttime={$start_time}&minlat=5.0&maxlat=20.0&minlon=115.0&maxlon=127.0&minmag={$MIN_MAGNITUDE}";
$quake_json = @file_get_contents($emsc_url);

if ($quake_json) {
    $data = json_decode($quake_json, true);
    if (!empty($data['features'])) {
        foreach ($data['features'] as $quake) {
            $props = $quake['properties'];
            $coords = $quake['geometry']['coordinates'];
            $dist = getDistance($USER_LAT, $USER_LON, $coords[1], $coords[0]);

            if ($dist <= $ALERT_RADIUS_KM) {
                $q_id = $quake['id'];
                $last_id_file = 'last_quake_id.txt';
                $last_alerted_id = file_exists($last_id_file) ? file_get_contents($last_id_file) : '';

                if (trim($last_alerted_id) !== $q_id) {
                    $msg = "EARTHQUAKE ALERT: Mag {$props['mag']} in {$props['flynn_region']}. Dist: " . number_format($dist, 1) . "km.";
                    broadcastToResidents($con, "EARTHQUAKE", $msg);
                    file_put_contents($last_id_file, $q_id);
                }
            }
        }
    }
}

// ==========================================
//   LOGIC 2: WEATHER DETECTION (LIVE)
// ==========================================
$stmt_history = $con->prepare("SELECT flood_history FROM barangay_information LIMIT 1");
$stmt_history->execute();
$flood_history = $stmt_history->get_result()->fetch_assoc()['flood_history'] ?? 'rare';

// Call Weather API
$owm_url = "https://api.openweathermap.org/data/2.5/weather?lat={$USER_LAT}&lon={$USER_LON}&appid={$owm_api_key}&units=metric";
$weather_json = @file_get_contents($owm_url);

if ($weather_json) {
    $weather_data = json_decode($weather_json, true);
    $rain_mm = $weather_data['rain']['1h'] ?? 0;
    
    // Categorize
    $cat = 'light';
    if ($rain_mm > 50) $cat = 'heavy';
    elseif ($rain_mm > 7.5) $cat = 'moderate';

    // Call Python AI
    $payload = json_encode(['rainfall_category' => $cat, 'rainfall_amount_mm' => (float)$rain_mm, 'flood_history' => $flood_history]);
    $ch = curl_init($flask_api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $ai_resp = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    $pred = $ai_resp['prediction'] ?? 'normal';
    
    // Check Status Change
    $curr_status = $con->query("SELECT status FROM current_alert_status WHERE id = 1")->fetch_assoc()['status'];
    
    if ($pred != $curr_status) {
        $con->query("UPDATE current_alert_status SET status = '$pred' WHERE id = 1");
        
        if ($pred == 'evacuate') {
            processSmartEvacuation($con);
        } elseif ($pred == 'warn') {
            broadcastToResidents($con, "WEATHER", "WARNING {$barangay_name}: Heavy rain detected.");
        }
    }
}
?>