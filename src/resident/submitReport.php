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

try {
    // --- 1. SET UP VARIABLES ---
    // These variables already exist from your code above:
    // $blotter_id, $location_incident, $incident_date, $date_reported,
    // $person_being_reported, $respondent_name, $reason, $justification
    
    // Map them to the correct column names for clarity
    $statement = $justification;          // The 'justification' is the main statement
    $respodent = $person_being_reported;  // This is the person from the form
    $statement_person = $respondent_name; // This is the logged-in user's name
    $type_of_incident = $reason;          // 'reason' is the type of incident
    
    // Set defaults
    $status = 'NEW';
    $remarks = 'OPEN';
    $date_added = date('Y-m-d H:i:s'); // Use a standard timestamp format

    // --- 2. THE CORRECTED SQL QUERY ---
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
    ) VALUES (?, '', ?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database prepare error: " . $con->error);
    }
    
    // --- 3. THE CORRECTED BIND_PARAM ---
    // 11 '?' placeholders, so we need 11 's' characters and 11 variables.
    $stmt->bind_param(
        'sssssssssss', // 11 's' characters
        $blotter_id,
        $statement,         // $justification
        $respodent,         // $person_being_reported
        $statement_person,  // $respondent_name (logged-in user)
        $incident_date,
        $date_reported,
        $type_of_incident,  // $reason
        $location_incident,
        $status,
        $remarks,
        $date_added
    );
    
    if ($stmt->execute()) {
        // --- 4. LINK THE COMPLAINANT (Logged-in User) ---
        $sql_complainant = "INSERT INTO blotter_complainant (blotter_main, complainant_id) VALUES (?, ?)";
        $stmt_complainant = $con->prepare($sql_complainant);
        if ($stmt_complainant) {
            $stmt_complainant->bind_param('ss', $blotter_id, $resident_id); // $resident_id is from session
            $stmt_complainant->execute();
            $stmt_complainant->close();
        }
        
        echo json_encode(['success' => true, 'message' => 'Report submitted successfully']);
    } else {
        throw new Exception("Failed to save report: " . $stmt->error);
    }
    
    $stmt->close();
    $con->close();
    
} catch (Exception $e) {
    // Send the actual error message back to the user
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>