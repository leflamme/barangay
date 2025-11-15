<?php
session_start();
include_once '../connection.php';

// We wrap the entire script in a try...catch block
// This will catch any fatal SQL error
try {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'resident') {
        exit('Unauthorized access.');
    }

    $user_id = $_SESSION['user_id'];

    // Get data from form
    $first_name = $_POST['first_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $email = $_POST['email'] ?? '';

    $new_image_name = '';
    $new_image_path = '';

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $type = explode('.', $_FILES['image']['name']);
        $type = $type[count($type) - 1];
        $new_image_name = uniqid(rand()) . '.' . $type;
        
        // --- THIS IS THE CORRECT PERMANENT VOLUME PATH ---
        $upload_dir = '../permanent-data/images/';
        $new_image_path = $upload_dir . $new_image_name;

        // Create the directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $new_image_path)) {
            // Handle file move error
            $new_image_name = '';
            $new_image_path = '';
        }
    }

    // --- 1. UPDATE `users` TABLE ---
    if (!empty($new_image_name)) {
        // If a new image is uploaded, update all fields
        $sql_users = "UPDATE `users` SET `first_name` = ?, `middle_name` = ?, `last_name` = ?, `contact_number` = ?, `image` = ?, `image_path` = ? WHERE `id` = ?";
        $stmt_users = $con->prepare($sql_users);
        $stmt_users->bind_param('sssssss', $first_name, $middle_name, $last_name, $contact_number, $new_image_name, $new_image_path, $user_id);
    } else {
        // If no new image, update only text fields
        $sql_users = "UPDATE `users` SET `first_name` = ?, `middle_name` = ?, `last_name` = ?, `contact_number` = ? WHERE `id` = ?";
        $stmt_users = $con->prepare($sql_users);
        $stmt_users->bind_param('sssss', $first_name, $middle_name, $last_name, $contact_number, $user_id);
    }
    $stmt_users->execute();
    $stmt_users->close();

    // --- 2. UPDATE `residence_information` TABLE ---
    if (!empty($new_image_name)) {
        // If a new image is uploaded, update all fields
        $sql_res = "UPDATE `residence_information` SET `first_name` = ?, `middle_name` = ?, `last_name` = ?, `contact_number` = ?, `email_address` = ?, `image` = ?, `image_path` = ? WHERE `residence_id` = ?";
        $stmt_res = $con->prepare($sql_res);
        $stmt_res->bind_param('ssssssss', $first_name, $middle_name, $last_name, $contact_number, $email, $new_image_name, $new_image_path, $user_id);
    } else {
        // If no new image, update only text fields
        $sql_res = "UPDATE `residence_information` SET `first_name` = ?, `middle_name` = ?, `last_name` = ?, `contact_number` = ?, `email_address` = ? WHERE `residence_id` = ?";
        $stmt_res = $con->prepare($sql_res);
        $stmt_res->bind_param('ssssss', $first_name, $middle_name, $last_name, $contact_number, $email, $user_id);
    }
    $stmt_res->execute();
    $stmt_res->close();

    // --- 3. UPDATE `edit_requests` TABLE ---
    // Set the request status to 'USED' so they must request again
    $sql_req = "UPDATE `edit_requests` SET `status` = 'USED' WHERE `user_id` = ? AND `status` = 'APPROVED'";
    $stmt_req = $con->prepare($sql_req);
    $stmt_req->bind_param('s', $user_id);
    $stmt_req->execute();
    $stmt_req->close();

    // Send a success response
    echo 'success';

} catch (Exception $e) {
    // Send a failure response
    echo 'Error: ' . $e->getMessage();
}
?>