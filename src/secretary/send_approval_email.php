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
    // Retrieve Key from Server Environment Variables
    $resendApiKey = getenv('RESEND_API_KEY'); 

    if (!$resendApiKey) {
        // Fallback or Error Logging if key is missing
        error_log("Resend API Key is missing in environment variables.");
        exit;
    }
    
    $url = 'https://api.resend.com/emails';
    $data = [
        'from' => 'Barangay Kalusugan <noreply@qc-brgy-kalusugan.online>', // Ensure this domain is verified in Resend
        'to' => [$email],
        'subject' => 'Registration Approved - Barangay Kalusugan',
        'html' => "
            <div style='font-family: Arial, sans-serif; color: #333;'>
                <h2 style='color: #003366;'>Welcome to Barangay Kalusugan!</h2>
                <p>Dear <strong>$name</strong>,</p>
                <p>We are pleased to inform you that your registration has been <strong>APPROVED</strong> by the Barangay Secretary.</p>
                <p>You may now login to the resident portal using the username and password you created.</p>
                <hr>
                <p><em>Stay Safe,</em><br><strong>Barangay Kalusugan Team</strong></p>
            </div>
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
    
    if (curl_errno($ch)) {
        error_log('Resend Error: ' . curl_error($ch));
    }
    
    curl_close($ch);
}
?>