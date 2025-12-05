<?php
// test_sms.php - Debugging Tool
// RUN THIS IN YOUR BROWSER

// 1. CONFIGURATION
$PHILSMS_URL = "https://dashboard.philsms.com/api/v3/";
$PHILSMS_KEY = "554|CayRg2wWAqSX68oeKVh7YmEg5MXKVVtemT16dIos75bdf39f"; // Your Key
$TEST_NUMBER = "09274176508"; // <--- PUT YOUR REAL NUMBER HERE
$MESSAGE     = "This is a test message from Barangay Kalusugan System.";

// 2. FORMAT NUMBER
$clean_phone = preg_replace('/[^0-9]/', '', $TEST_NUMBER);
if (substr($clean_phone, 0, 1) == "0") $final_phone = "63" . substr($clean_phone, 1);
elseif (substr($clean_phone, 0, 1) == "9") $final_phone = "63" . $clean_phone;
else $final_phone = $clean_phone;

echo "<h2>SMS Debugger</h2>";
echo "<strong>Target:</strong> $final_phone <br>";
echo "<strong>Message:</strong> $MESSAGE <br><hr>";

// 3. SEND REQUEST
$ch = curl_init($PHILSMS_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "recipient" => $final_phone,
    "sender_id" => "PhilSMS",
    "message"   => $message
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $PHILSMS_KEY",
    "Content-Type: application/json"
]);

// DISABLE SSL CHECK (Fix for Localhost/XAMPP issues)
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// 4. SHOW RESULTS
if ($curl_error) {
    echo "<h3 style='color:red'>CURL ERROR (Connection Failed):</h3>";
    echo $curl_error;
} else {
    echo "<h3>API Response (HTTP $http_code):</h3>";
    echo "<pre style='background:#eee; padding:10px; border:1px solid #999;'>";
    var_dump($response); // Show the raw reply from PhilSMS
    echo "</pre>";
    
    $json = json_decode($response, true);
    if (isset($json['status']) && $json['status'] == 'error') {
        echo "<h3 style='color:red'>API REJECTED THE MESSAGE:</h3>";
        echo "Reason: " . ($json['message'] ?? 'Unknown');
    } elseif ($http_code == 200) {
        echo "<h3 style='color:green'>SUCCESS! Check your phone.</h3>";
    }
}
?>