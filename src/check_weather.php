<?php
// check_weather.php - PRODUCTION WITH SMART ALLOCATION
ini_set('max_execution_time', 300);
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
$flask_api_url = 'http://barangay_api.railway.internal:8080/predict'; // Your AI URL

// PhilSMS
$PHILSMS_URL = "https://dashboard.philsms.com/api/v3/";
$PHILSMS_KEY = "554|CayRg2wWAqSX68oeKVh7YmEg5MXKVVtemT16dIos75bdf39f";

// ==========================================
//   HELPER: SHARED FUNCTIONS
// ==========================================

// 1. Math: Haversine Formula (Used by both Earthquake & Flood)
function getDistance($lat1, $lon1, $lat2, $lon2) {
    if (empty($lat1) || empty($lon1) || empty($lat2) || empty($lon2)) return 99999;
    $R = 6371; // Radius of earth in km
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $R * $c; // Returns KM
}

// 2. Sender: Generic SMS Sender (Used by Broadcast functions)
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
    $resp = curl_exec($ch);
    curl_close($ch);
}

// 3. Logic: Standard Broadcast (For Earthquake / Warnings)
function broadcastToResidents($con, $type, $message) {
    echo "📢 STARTING BROADCAST: $type\n";
    $sql = "SELECT r.contact_number FROM users u JOIN residence_information r ON u.id = r.residence_id WHERE u.user_type = 'resident'";
    $result = $con->query($sql);
    while($row = $result->fetch_assoc()) {
        if (!empty($row['contact_number'])) {
            sendSingleSMS($row['contact_number'], $message);
            usleep(100000); // 0.1s delay
        }
    }
    echo "✅ Broadcast Complete.\n";
}

// 4. Logic: SMART EVACUATION (Calculates Routes)
function processSmartEvacuation($con) {
    echo "🚑 STARTING SMART EVACUATION ROUTING...\n";
    
    // A. Fetch all Centers and Residents
    $centers_query = $con->query("SELECT * FROM evacuation_centers");
    $centers = [];
    while ($c = $centers_query->fetch_assoc()) { $centers[] = $c; }

    $residents_query = $con->query("SELECT r.contact_number, r.latitude, r.longitude, u.first_name 
                                    FROM users u 
                                    JOIN residence_information r ON u.id = r.residence_id 
                                    WHERE u.user_type = 'resident'");

    // B. Process Each Resident
    while ($res = $residents_query->fetch_assoc()) {
        if (empty($res['contact_number'])) continue;

        $target_center = null;
        $min_dist = 99999;
        
        if (!empty($res['latitude']) && !empty($res['longitude'])) {
            // Calculate distance to ALL centers
            $resident_options = [];
            foreach ($centers as $key => $c) {
                $dist = getDistance($res['latitude'], $res['longitude'], $c['latitude'], $c['longitude']);
                $centers[$key]['calc_dist'] = $dist; // Temp store
                $resident_options[] = [
                    'key_index' => $key, // Reference to master list
                    'dist' => $dist
                ];
            }

            // Sort by nearest
            usort($resident_options, function($a, $b) { return $a['dist'] <=> $b['dist']; });

            // Find first one with capacity
            foreach ($resident_options as $opt) {
                $idx = $opt['key_index'];
                if ($centers[$idx]['current_occupancy'] < $centers[$idx]['max_capacity']) {
                    $target_center = $centers[$idx];
                    // RESERVE SPOT IN MEMORY (so next loop sees it as taken)
                    $centers[$idx]['current_occupancy']++; 
                    break;
                }
            }
        }

        // C. Send Personalized Message
        if ($target_center) {
            $dist_str = number_format($target_center['calc_dist'], 2);
            $msg = "URGENT EVACUATION: Flood detected. Proceed immediately to {$target_center['name']} ({$dist_str}km away). It has space available.";
        } else {
            // Fallback: No coords OR all full
            $msg = "URGENT EVACUATION: Flood detected. Proceed immediately to the nearest evacuation center or high ground.";
        }

        sendSingleSMS($res['contact_number'], $msg);
        echo " -> Sent to {$res['first_name']}\n";
        usleep(100000);
    }
    echo "✅ Smart Evacuation Complete.\n";
}

// ==========================================
//   LOGIC 1: EARTHQUAKE DETECTION
// ==========================================
// ... (Your existing Earthquake Logic stays here, mostly unchanged) ...
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
//   LOGIC 2: WEATHER DETECTION (DEBUG MODE)
// ==========================================

// 1. Enable Error Reporting (So we see if it crashes)
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>🔎 DEBUG MODE STARTED...</h3>";

// 2. Force the Prediction
$pred = 'evacuate'; 
echo "<strong>Forced Prediction:</strong> $pred <br>";

// 3. Check Database Status
$status_query = $con->query("SELECT status FROM current_alert_status WHERE id = 1");
$curr_status = $status_query->fetch_assoc()['status'];
echo "<strong>Current DB Status:</strong> $curr_status <br>";

// 4. BYPASS THE 'IF' CHECK (Run logic regardless of status)
// if ($pred != $curr_status) {  <--- COMMENTED OUT FOR TESTING

    echo "<strong>Status Update:</strong> Simulating update to '$pred'...<br>";
    
    // Update DB (Optional for test, but good to keep)
    $con->query("UPDATE current_alert_status SET status = '$pred' WHERE id = 1");
    
    if ($pred == 'evacuate') {
        echo "<hr><h4>🚀 TRIGGERING SMART EVACUATION...</h4>";
        processSmartEvacuation($con);
    } elseif ($pred == 'warn') {
        echo "Triggering Warning Broadcast...<br>";
        broadcastToResidents($con, "WEATHER", "WARNING: Heavy rain detected.");
    } else {
        echo "Weather is Normal.<br>";
    }

// } <--- COMMENTED OUT FOR TESTING

echo "<hr><h3>✅ END OF TEST</h3>";
?>