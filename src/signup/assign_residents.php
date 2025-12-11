<?php
// assign_residents.php
// FORCE ASSIGNMENT VERSION
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once __DIR__ . '/../connection.php'; 

// 1. DEFINE EVACUATION CENTERS
$centers = [
    [
        'name' => 'Barangay Kalusugan Elementary School',
        'lat' => 14.62450000,
        'lon' => 121.02300000,
        'capacity' => 200,
        'occupancy' => 0 
    ],
    [
        'name' => 'Kalusugan Open Area',
        'lat' => 14.625376,
        'lon' => 121.022461,
        'capacity' => 150,
        'occupancy' => 0
    ],
    [
        'name' => 'St. Joseph\'s College of Quezon City',
        'lat' => 14.62260,
        'lon' => 121.02546,
        'capacity' => 300,
        'occupancy' => 0
    ]
];

// Helper: Distance Calculation (Haversine Formula)
function getDistance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; // Kilometers
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earth_radius * $c; 
}

// 2. GET THE SPECIFIC USER
if(!isset($_POST['request'])){
    echo "Error: No Request ID sent.";
    exit();
}

$req_id = $con->real_escape_string($_POST['request']);

// We select the user WITHOUT checking for lat/long in SQL to ensure we find the row
$sql = "SELECT residence_id, latitude, longitude, first_name, last_name FROM residence_information WHERE residence_id = '$req_id'";
$result = $con->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // PHP CHECK: Do we have coordinates?
    $uLat = $row['latitude'];
    $uLon = $row['longitude'];

    // If coordinates are missing/zero, force a DEFAULT assignment so the column isn't empty
    if(empty($uLat) || empty($uLon) || $uLat == 0) {
        // Fallback: Assign to the biggest center if map failed
        $assigned_name = 'Barangay Kalusugan Elementary School'; 
    } else {
        // CALCULATE NEAREST CENTER
        $options = [];
        foreach ($centers as $key => $c) {
            $dist = getDistance($uLat, $uLon, $c['lat'], $c['lon']);
            $options[] = [
                'key' => $key,
                'dist' => $dist,
                'name' => $c['name']
            ];
        }

        // Sort by distance (Nearest to Farthest)
        usort($options, function($a, $b) {
            return $a['dist'] <=> $b['dist'];
        });

        // Pick the nearest one
        // (Since we don't have real-time occupancy from DB yet, we just pick the nearest)
        $assigned_name = $options[0]['name'];
    }

    // 3. EXECUTE UPDATE
    $updateSQL = "UPDATE residence_information SET assigned_center = ? WHERE residence_id = ?";
    $stmt = $con->prepare($updateSQL);
    $stmt->bind_param("ss", $assigned_name, $req_id);
    
    if($stmt->execute()){
        echo "Success: Assigned to $assigned_name";
    } else {
        echo "Error Updating: " . $con->error;
    }

} else {
    echo "Error: User ID $req_id not found in database.";
}
?>