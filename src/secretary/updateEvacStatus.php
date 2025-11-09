<?php
include_once '../connection.php';
if(isset($_POST['residence_id']) && isset($_POST['status'])) {
    $residence_id = $_POST['residence_id'];
    $status = $_POST['status'];
    // Insert or update evacuation status
    $sql = "INSERT INTO evacuation_status (residence_id, status) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE status = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('sss', $residence_id, $status, $status);
    $stmt->execute();
    echo "success";
}
?>