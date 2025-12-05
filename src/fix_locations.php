<?php
// fix_locations.php - Run ONCE to update existing residents
require 'connection.php'; // Use your existing db connection

function getGeo($address) {
    $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($address . ", Quezon City, Philippines") . "&format=json&limit=1";
    $opts = ["http" => ["header" => "User-Agent: BarangaySystem/1.0\r\n"]];
    $context = stream_context_create($opts);
    $json = file_get_contents($url, false, $context);
    $data = json_decode($json, true);
    return !empty($data) ? ['lat' => $data[0]['lat'], 'lon' => $data[0]['lon']] : null;
}

$residents = $con->query("SELECT a_i, address FROM residence_information WHERE latitude IS NULL");

echo "<h2>Updating Resident Coordinates...</h2>";
while($r = $residents->fetch_assoc()) {
    $coords = getGeo($r['address']);
    if ($coords) {
        $stmt = $con->prepare("UPDATE residence_information SET latitude=?, longitude=? WHERE a_i=?");
        $stmt->bind_param("ddi", $coords['lat'], $coords['lon'], $r['a_i']);
        $stmt->execute();
        echo "Updated: {$r['address']} <br>";
    } else {
        echo "<b style='color:red'>Failed:</b> {$r['address']} <br>";
    }
    sleep(1); // Anti-ban delay
}
echo "<h3>Done.</h3>";
?>