<?php
session_start();
include_once '../connection.php';

$response = ['success' => false, 'message' => 'An error occurred.'];

try {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'resident') {
        throw new Exception('Unauthorized access.');
    }

    $user_id = $_SESSION['user_id'];

    // Check if a request already exists
    $sql_check = "SELECT * FROM `edit_requests` WHERE `user_id` = ? AND `status` IN ('PENDING', 'APPROVED')";
    $stmt_check = $con->prepare($sql_check);
    $stmt_check->bind_param('s', $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // A request already exists, just return its status
        $existing_request = $result_check->fetch_assoc();
        $response['success'] = true;
        $response['message'] = 'Request already ' . $existing_request['status'];
        $response['status'] = $existing_request['status'];
    } else {
        // No active request, create a new one
        $sql_insert = "INSERT INTO `edit_requests` (user_id, status) VALUES (?, 'PENDING')";
        $stmt_insert = $con->prepare($sql_insert);
        $stmt_insert->bind_param('s', $user_id);
        
        if ($stmt_insert->execute()) {
            $response['success'] = true;
            $response['message'] = 'Request submitted successfully.';
            $response['status'] = 'PENDING';
        } else {
            throw new Exception('Failed to submit request.');
        }
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>