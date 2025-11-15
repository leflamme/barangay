<?php
session_start();
include_once '../connection.php';

$response = ['success' => false, 'message' => 'An error occurred.'];

try {
    if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] != 'admin' && $_SESSION['user_type'] != 'secretary')) {
        throw new Exception('Unauthorized access.');
    }

    $request_id = $_POST['request_id'] ?? 0;
    $action = $_POST['action'] ?? '';

    if (empty($request_id) || empty($action)) {
        throw new Exception('Invalid request.');
    }

    if ($action == 'approve') {
        // Set status to 'APPROVED'
        $sql = "UPDATE `edit_requests` SET `status` = 'APPROVED', `approved_date` = NOW() WHERE `id` = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $request_id);
        $stmt->execute();
        
        $response['success'] = true;
        $response['message'] = 'Request approved successfully.';

    } elseif ($action == 'deny') {
        // Set status to 'USED' (or 'DENIED') to remove it from the list
        // Using 'USED' as per your original table enum
        $sql = "UPDATE `edit_requests` SET `status` = 'USED' WHERE `id` = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $request_id);
        $stmt->execute();
        
        $response['success'] = true;
        $response['message'] = 'Request denied.';
    
    } else {
        throw new Exception('Invalid action.');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>