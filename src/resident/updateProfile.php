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
    $update_image_sql = '';
    $update_image_params_users = [];
    $update_image_params_res = [];
    $types_users = '';
    $types_res = '';

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

        if (move_uploaded_file($_FILES['image']['tmp_name'], $new_image_path)) {
            // If upload is successful, prepare to update image fields
            $update_image_sql_users = ', `image` = ?, `image_path` = ?';
            $update_image_sql_res = ', `image` = ?, `image_path` = ?';
            $types_users = 'ss';
            $types_res = 'ss';
            $update_image_params_users = [$new_image_name, $new_image_path];
            $update_image_params_res = [$new_image_name, $new_image_path];
        }
    }

    // --- 1. UPDATE `users` TABLE ---
    $sql_users = "UPDATE `users` SET `first_name` = ?, `middle_name` = ?, `last_name` = ?, `contact_number` = ? $update_image_sql_users WHERE `id` = ?";
    $stmt_users = $con->prepare($sql_users);
    $all_params_users = array_merge([$first_name, $middle_name, $last_name, $contact_number], $update_image_params_users, [$user_id]);
    $stmt_users->bind_param('ssss' . $types_users . 's', ...$all_params_users);
    $stmt_users->execute();
    $stmt_users->close();

    // --- 2. UPDATE `residence_information` TABLE ---
    $sql_res = "UPDATE `residence_information` SET `first_name` = ?, `middle_name` = ?, `last_name` = ?, `contact_number` = ?, `email_address` = ? $update_image_sql_res WHERE `residence_id` = ?";
    $stmt_res = $con->prepare($sql_res);
    $all_params_res = array_merge([$first_name, $middle_name, $last_name, $contact_number, $email], $update_image_params_res, [$user_id]);
    $stmt_res->bind_param('sssss' . $types_res . 's', ...$all_params_res);
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