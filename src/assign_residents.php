<?php
// assign_residents_debug.php
// RUN THIS ONCE TO POPULATE YOUR DATABASE FOR TESTING
include_once 'connection.php';

echo "<h2>Starting Assignment Process...</h2>";

// 1. DEFINE YOUR CENTERS (Coordinates from your screenshot)
$centers = [
    [
        'name' => 'Barangay Kalusugan Elementary School',
        'lat' => 14.62450000,
        'lon' => 121.02300000,
        'capacity' => 200,
        'occupancy' => 0
    ],
    [
        'name' => 'Kalusugan Open Basketball Court',
        'lat' => 14.62100000,
        'lon' => 121.01950000,
        'capacity' => 150,
        'occupancy' => 0
    ],
    [
        'name' => 'Quezon City High School Annex',
        'lat' => 14.62600000,
        'lon' => 121.02500000,
        'capacity' => 300,
        'occupancy' => 0
    ]
];

// Helper: Calculate Distance
function getDistance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; 
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earth_radius * $c; // Returns KM
}

// 2. FETCH ALL RESIDENTS WITH COORDINATES
$sql = "SELECT residence_id, latitude, longitude, first_name, last_name FROM residence_information WHERE latitude != '' AND longitude != ''";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $uLat = $row['latitude'];
        $uLon = $row['longitude'];
        $uID = $row['residence_id'];
        
        // Calculate distance to all 3 centers
        $options = [];
        foreach ($centers as $key => $c) {
            $dist = getDistance($uLat, $uLon, $c['lat'], $c['lon']);
            $options[] = [
                'key' => $key,
                'dist' => $dist,
                'name' => $c['name']
            ];
        }

        // Sort by distance (Nearest first)
        usort($options, function($a, $b) {
            return $a['dist'] <=> $b['dist'];
        });

        // Assign to the nearest one that isn't full
        $assigned_name = '';
        foreach ($options as $opt) {
            $k = $opt['key'];
            if ($centers[$k]['occupancy'] < $centers[$k]['capacity']) {
                $assigned_name = $centers[$k]['name'];
                $centers[$k]['occupancy']++;
                break;
            }
        }
        
        // If all full, assign to nearest anyway
        if ($assigned_name == '') {
            $assigned_name = $options[0]['name']; 
        }

        // UPDATE DATABASE
        $updateSQL = "UPDATE residence_information SET assigned_center = '$assigned_name' WHERE residence_id = '$uID'";
        $con->query($updateSQL);
        
        echo "Assigned <b>" . $row['last_name'] . "</b> to: " . $assigned_name . "<br>";
        $count++;
    }
    echo "<hr><h3>DONE! Assigned $count residents.</h3>";
    echo "<a href='drrmEvacuation.php'>Go back to Evacuation Dashboard</a>";

} else {
    echo "No residents found with Latitude/Longitude coordinates. Please check your user data.";
}
?>