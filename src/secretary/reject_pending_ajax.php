<?php
session_start();
include_once '../connection.php';

if (!isset($_POST['pending_id'])) {
    echo 'Missing ID';
    exit;
}

$pending_id = $con->real_escape_string($_POST['pending_id']);


try {
    // Fetch pending resident
    $sql = "SELECT * FROM pending_residents WHERE pending_id = ? LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $pending_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo 'Resident not found';
        exit;
    }

    $row = $res->fetch_assoc();
    $stmt->close();

    // Delete image if exists
    $pending_dir = __DIR__ . '/../permanent-data/pending_images/';
    if (!empty($row['image_name']) && file_exists($pending_dir . $row['image_name'])) {
        unlink($pending_dir . $row['image_name']);
    }

    // Delete from pending
    $sql_del = "DELETE FROM pending_residents WHERE pending_id = ?";
    $stmt2 = $con->prepare($sql_del);
    $stmt2->bind_param('s', $pending_id);
    $stmt2->execute();
    $stmt2->close();

    // Log activity
    $date_activity = date("j-n-Y g:i A");
    $admin = strtoupper('SECRETARY') . ': REJECT RESIDENT - ' . $pending_id;
    $status_activity_log = 'reject';
    $sql_log = "INSERT INTO activity_log (`message`,`date`,`status`) VALUES (?,?,?)";
    $stmt_log = $con->prepare($sql_log);
    $stmt_log->bind_param('sss', $admin, $date_activity, $status_activity_log);
    $stmt_log->execute();
    $stmt_log->close();

    echo 'Resident rejected successfully';
} catch (Throwable $t) {
    echo 'Error: '.$t->getMessage();
}
