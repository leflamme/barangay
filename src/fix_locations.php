<?php
// fix_locations_v2.php
// SMART FIXER: Tries Exact -> Tries Street -> Defaults to Barangay Center
ini_set('max_execution_time', 300); // Allow 5 mins to run
require 'connection.php'; 

// --- CONFIGURATION ---
// Set the "Center" of Brgy Kalusugan (Used as a fallback)
$DEFAULT_LAT = 14.6231;
$DEFAULT_LON = 121.0219;

function getGeo($query) {
    // Clean the query: Remove "QC" or "Philippines" if already there to avoid duplicates
    $clean_query = str_replace(["QC", "Philippines"], "", $query);
    $final_query = urlencode($clean_query . ", Quezon City, Philippines");
    
    $url = "https://nominatim.openstreetmap.org/search?q={$final_query}&format=json&limit=1";
    $opts = ["http" => ["header" => "User-Agent: BarangaySystem/1.0\r\n"]];
    $context = stream_context_create($opts);
    
    $json = @file_get_contents($url, false, $context);
    if ($json) {
        $data = json_decode($json, true);
        if (!empty($data)) {
            return ['lat' => $data[0]['lat'], 'lon' => $data[0]['lon']];
        }
    }
    return null;
}

// 1. Get ONLY the residents who failed last time (still have NULL latitude)
$residents = $con->query("SELECT a_i, address, street, barangay FROM residence_information WHERE latitude IS NULL");

echo "<h2>Retrying Failed Addresses...</h2>";

while($r = $residents->fetch_assoc()) {
    $id = $r['a_i'];
    $address = $r['address'];
    $street = $r['street'] ?? '';
    $barangay = $r['barangay'] ?? 'Barangay Kalusugan';
    
    // ATTEMPT 1: Exact Address (You already tried this, but we retry just in case)
    $coords = getGeo($address);
    $method = "Exact Match";

    // ATTEMPT 2: Street + Barangay (Ignore House Number)
    if (!$coords && !empty($street)) {
        $coords = getGeo("$street, $barangay");
        $method = "Street Approximation";
    }

    // ATTEMPT 3: Just Barangay Center (Fallback)
    if (!$coords) {
        $coords = ['lat' => $DEFAULT_LAT, 'lon' => $DEFAULT_LON];
        $method = "DEFAULT (Barangay Center)";
    }

    // UPDATE DATABASE
    if ($coords) {
        $stmt = $con->prepare("UPDATE residence_information SET latitude=?, longitude=? WHERE a_i=?");
        $stmt->bind_param("ddi", $coords['lat'], $coords['lon'], $id);
        $stmt->execute();
        
        $color = ($method == "Exact Match") ? "green" : "orange";
        if ($method == "DEFAULT (Barangay Center)") $color = "red";
        
        echo "<div style='color:$color'>ID $id: $method ($address)</div>";
    }
    
    flush(); // Push output to browser immediately
    sleep(1); // Be polite to API
}

echo "<h3>All Fixed! Everyone now has a location.</h3>";
?>