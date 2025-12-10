<?php
// fix_locations.php
// SMART FIXER: Supports Bulk Fix and Single User Fix
ini_set('max_execution_time', 300); 
require __DIR__ . '/../connection.php'; // Adjusted path if needed

// --- CONFIGURATION ---
$DEFAULT_LAT = 14.6231;
$DEFAULT_LON = 121.0219;

function getGeo($query) {
    $clean_query = str_replace(["QC", "Philippines"], "", $query);
    $final_query = urlencode($clean_query . ", Quezon City, Philippines");
    $url = "https://nominatim.openstreetmap.org/search?q={$final_query}&format=json&limit=1";
    $opts = ["http" => ["header" => "User-Agent: BarangaySystem/1.0\r\n"]];
    $context = stream_context_create($opts);
    $json = @file_get_contents($url, false, $context);
    if ($json) {
        $data = json_decode($json, true);
        if (!empty($data)) return ['lat' => $data[0]['lat'], 'lon' => $data[0]['lon']];
    }
    return null;
}

// CHECK IF THIS IS A SINGLE USER REQUEST (FROM REGISTRATION)
if(isset($_POST['request'])){
    $target_id = $_POST['request'];
    // Select specific user by residence_id
    $sql = "SELECT a_i, address, street, barangay FROM residence_information WHERE residence_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $target_id);
    $stmt->execute();
    $residents = $stmt->get_result();
} else {
    // DEFAULT: Run for everyone with missing coordinates
    echo "<h2>Retrying Failed Addresses...</h2>";
    $residents = $con->query("SELECT a_i, address, street, barangay FROM residence_information WHERE latitude IS NULL");
}

while($r = $residents->fetch_assoc()) {
    $id = $r['a_i']; // We use the Auto-Increment ID for the update statement
    $address = $r['address'];
    $street = $r['street'] ?? '';
    $barangay = $r['barangay'] ?? 'Barangay Kalusugan';
    
    // Logic: 1. Exact -> 2. Street -> 3. Default
    $coords = getGeo($address);
    $method = "Exact Match";

    if (!$coords && !empty($street)) {
        $coords = getGeo("$street, $barangay");
        $method = "Street Approximation";
    }

    if (!$coords) {
        $coords = ['lat' => $DEFAULT_LAT, 'lon' => $DEFAULT_LON];
        $method = "DEFAULT (Barangay Center)";
    }

    if ($coords) {
        $update = $con->prepare("UPDATE residence_information SET latitude=?, longitude=? WHERE a_i=?");
        $update->bind_param("ddi", $coords['lat'], $coords['lon'], $id);
        $update->execute();
        
        if(!isset($_POST['request'])){
            echo "<div>Updated ID $id via $method</div>";
            flush();
            sleep(1); // Sleep only during bulk processing
        }
    }
}

if(!isset($_POST['request'])) {
    echo "<h3>Done.</h3>";
}
?>