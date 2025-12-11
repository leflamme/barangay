<?php
require __DIR__ . '/../connection.php';

if (!isset($_POST['user_id'])) exit;
$user_id = $con->real_escape_string($_POST['user_id']);

// Get Email and Name
$sql = "SELECT first_name, email_address FROM residence_information WHERE residence_id = '$user_id'";
$result = $con->query($sql);
if($row = $result->fetch_assoc()){
    
    $email = $row['email_address'];
    $name = $row['first_name'];

    // --- RESEND API CONFIGURATION ---
    // If you don't have an API Key yet, this part will just skip.
    $resendApiKey = 're_bygoEX77_Medtrd76SJtddddi8b5TJEB1'; // REPLACE WITH YOUR REAL KEY
    
    $url = 'https://api.resend.com/emails';
    $data = [
        'from' => 'Barangay Kalusugan <noreply@qc-brgy-kalusugan.online>', // Use your verified domain
        'to' => [$email],
        'subject' => 'Registration Approved - Barangay Kalusugan',
        'html' => "
            <h1>Welcome to Barangay Kalusugan!</h1>
            <p>Dear $name,</p>
            <p>Your registration has been approved by the Barangay Secretary.</p>
            <p>You may now login to the portal using the username and password you created.</p>
            <br>
            <p>Stay Safe,<br>Barangay Kalusugan Team</p>
        "
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $resendApiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Log the attempt
    // $con->query("INSERT INTO activity_log ...");
}
?>