<?php
// assign_residents.php
include_once __DIR__ . '/../connection.php'; 

// 1. DEFINE CENTERS
$centers = [
    ['name' => 'Barangay Kalusugan Elementary School', 'lat' => 14.62450000, 'lon' => 121.02300000, 'capacity' => 200, 'occupancy' => 0],
    ['name' => 'Kalusugan Open Basketball Court', 'lat' => 14.62100000, 'lon' => 121.01950000, 'capacity' => 150, 'occupancy' => 0],
    ['name' => 'Quezon City High School Annex', 'lat' => 14.62600000, 'lon' => 121.02500000, 'capacity' => 300, 'occupancy' => 0]
];

function getDistance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; 
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earth_radius * $c; 
}

// 2. QUERY BUILDER
$sql = "SELECT residence_id, latitude, longitude, first_name, last_name FROM residence_information WHERE latitude != '' AND longitude != ''";

// IF SINGLE REQUEST, FILTER BY ID
if(isset($_POST['request'])){
    $req_id = $con->real_escape_string($_POST['request']);
    $sql .= " AND residence_id = '$req_id'";
}

$result = $con->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $uLat = $row['latitude'];
        $uLon = $row['longitude'];
        $uID = $row['residence_id'];
        
        $options = [];
        foreach ($centers as $key => $c) {
            $dist = getDistance($uLat, $uLon, $c['lat'], $c['lon']);
            $options[] = ['key' => $key, 'dist' => $dist, 'name' => $c['name']];
        }

        usort($options, function($a, $b) { return $a['dist'] <=> $b['dist']; });

        $assigned_name = $options[0]['name']; // Default to nearest
        
        // Simple occupancy check (mock logic since we aren't querying current DB counts)
        foreach ($options as $opt) {
            $k = $opt['key'];
            if ($centers[$k]['occupancy'] < $centers[$k]['capacity']) {
                $assigned_name = $centers[$k]['name'];
                $centers[$k]['occupancy']++;
                break;
            }
        }

        $con->query("UPDATE residence_information SET assigned_center = '$assigned_name' WHERE residence_id = '$uID'");
        
        if(!isset($_POST['request'])) echo "Assigned " . $row['last_name'] . "<br>";
    }
}
?>