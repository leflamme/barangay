<?php
session_start();
include_once '../connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'resident') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$resident_id = $_SESSION['user_id'];

// Get user's full name for respondent field
$sql_user = "SELECT first_name, last_name FROM users WHERE id = ?";
$stmt_user = $con->prepare($sql_user);
if (!$stmt_user) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}
$stmt_user->bind_param('s', $resident_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$user_data = $result_user->fetch_assoc();
$respondent_name = trim($user_data['first_name'] . ' ' . $user_data['last_name']);

// Validate required fields
if (empty($_POST['personName']) || empty($_POST['location']) || 
    empty($_POST['reason']) || empty($_POST['justification']) || empty($_POST['incidentDate'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$person_being_reported = trim($_POST['personName']);
$location_incident = trim($_POST['location']);
$reason = trim($_POST['reason']);
$justification = trim($_POST['justification']);
$incident_date = $_POST['incidentDate'];
$date_reported = date('Y-m-d H:i:s');

// Validate date format
if (!strtotime($incident_date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid incident date format']);
    exit;
}

// Generate blotter_id in format YYYY-MM-DD-XXXX
$incident_date_formatted = date('Y-m-d', strtotime($incident_date));
$prefix = $incident_date_formatted;

// Get the next sequence number for this date
$sql_sequence = "SELECT MAX(CAST(SUBSTRING_INDEX(blotter_id, '-', -1) AS UNSIGNED)) as max_seq 
                 FROM blotter_record 
                 WHERE blotter_id LIKE ?";
$stmt_seq = $con->prepare($sql_sequence);
if (!$stmt_seq) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}
$search_pattern = $prefix . '-%';
$stmt_seq->bind_param('s', $search_pattern);
$stmt_seq->execute();
$result_seq = $stmt_seq->get_result();
$row_seq = $result_seq->fetch_assoc();
$next_seq = ($row_seq['max_seq'] ?? 0) + 1;
$blotter_id = $prefix . '-' . str_pad($next_seq, 4, '0', STR_PAD_LEFT);

// Handle file upload if provided (optional)
$proof_path = null;
if (isset($_FILES['proof']) && $_FILES['proof']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    $max_size = 5 * 1024 * 1024;
    
    if (in_array($_FILES['proof']['type'], $allowed_types) && $_FILES['proof']['size'] <= $max_size) {
        $upload_dir = '../uploads/reports/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                // Continue without file upload if directory creation fails
            }
        }
        
        $file_extension = pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION);
        $file_name = 'report_' . time() . '_' . uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['proof']['tmp_name'], $file_path)) {
            $proof_path = $file_name;
        }
    }
}

try {
    // Insert into blotter_record table
    $sql = "INSERT INTO blotter_record (
        blotter_id,
        complainant_not_residence,
        statement,
        respodent,
        involved_not_resident,
        statement_person,
        date_incident,
        date_reported,
        type_of_incident,
        location_incident,
        status,
        remarks,
        date_added
    ) VALUES (?, '', ?, ?, '', ?, ?, ?, 'Complaint', ?, 'NEW', ?, ?)";
    
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database error");
    }
    
    $stmt->bind_param(
        'ssssssssss',
        $blotter_id,
        $reason,
        $respondent_name,
        $person_being_reported,
        $incident_date,
        $date_reported,
        $location_incident,
        $justification,
        $date_reported
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Report submitted successfully']);
    } else {
        throw new Exception("Failed to save report");
    }
    
    $stmt->close();
    $con->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to submit report. Please try again later.']);
}
?>