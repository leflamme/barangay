<?php
// get_household_member.php
session_start();

// Set headers FIRST - before any output
header('Content-Type: application/json');

// Turn off error display for production, but log them
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Include database connection
include_once '../connection.php';

// Check session and user type
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'secretary') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if household_id is provided
if (!isset($_POST['household_id']) || empty($_POST['household_id'])) {
    echo json_encode(['success' => false, 'message' => 'Household ID is required']);
    exit;
}

$household_id = (int)$_POST['household_id'];

// Initialize response array
$response = ['success' => false, 'message' => 'Unknown error', 'total_members' => 0, 'members' => []];

try {
    // Check database connection
    if (!$con) {
        throw new Exception("Database connection failed");
    }
    
    // Test the connection
    if ($con->connect_error) {
        throw new Exception("Database connection error: " . $con->connect_error);
    }
    
    // First, check if household exists
    $check_sql = "SELECT COUNT(*) as count FROM households WHERE id = ?";
    $check_stmt = $con->prepare($check_sql);
    if (!$check_stmt) {
        throw new Exception("Prepare check failed: " . $con->error);
    }
    
    $check_stmt->bind_param('i', $household_id);
    if (!$check_stmt->execute()) {
        throw new Exception("Execute check failed: " . $check_stmt->error);
    }
    
    $check_result = $check_stmt->get_result();
    if (!$check_result) {
        throw new Exception("Get result check failed: " . $con->error);
    }
    
    $check_row = $check_result->fetch_assoc();
    
    if ($check_row['count'] == 0) {
        $response['message'] = 'Household not found';
        echo json_encode($response);
        exit;
    }
    
    // Get household members
    $sql = "SELECT 
                hm.id as member_id,
                hm.relationship_to_head,
                hm.is_head,
                u.id as user_id,
                u.first_name,
                u.last_name,
                u.contact_number,
                COALESCE(ri.age, 'N/A') as age,
                COALESCE(rs.pwd, 'NO') as pwd,
                COALESCE(rs.senior, 'NO') as senior,
                COALESCE(rs.single_parent, 'NO') as single_parent,
                COALESCE(rs.voters, 'NO') as voters,
                COALESCE(rs.status, 'ACTIVE') as status
            FROM household_members hm
            LEFT JOIN users u ON hm.user_id = u.id
            LEFT JOIN residence_information ri ON u.id = ri.residence_id
            LEFT JOIN residence_status rs ON u.id = rs.residence_id
            WHERE hm.household_id = ?
            ORDER BY 
                CASE WHEN hm.is_head = 1 THEN 1 ELSE 2 END,
                hm.date_added ASC";
    
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $con->error);
    }
    
    $stmt->bind_param('i', $household_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Get result failed: " . $con->error);
    }
    
    $members = [];
    $total_members = $result->num_rows;
    
    if ($total_members > 0) {
        while ($row = $result->fetch_assoc()) {
            // Get name
            $name = trim($row['first_name'] . ' ' . $row['last_name']);
            if (empty($name) || $name == ' ') {
                $name = 'Unnamed Member #' . $row['member_id'];
            }
            
            // Get relationship
            $relationship = 'Member';
            if ($row['is_head'] == 1) {
                $relationship = 'Head of Household';
            } elseif (!empty($row['relationship_to_head'])) {
                $relationship = $row['relationship_to_head'];
            }
            
            $members[] = [
                'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                'age' => $row['age'],
                'relationship' => htmlspecialchars($relationship, ENT_QUOTES, 'UTF-8'),
                'contact' => $row['contact_number'] ?? 'N/A',
                'pwd' => $row['pwd'],
                'senior' => $row['senior'],
                'single_parent' => $row['single_parent'],
                'voters' => $row['voters'],
                'status' => $row['status']
            ];
        }
    }
    
    $response = [
        'success' => true,
        'total_members' => $total_members,
        'members' => $members,
        'message' => $total_members > 0 ? '' : 'No members found in this household'
    ];
    
} catch (Exception $e) {
    // Log the error
    error_log("Error in get_household_member.php: " . $e->getMessage());
    
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

// Output the JSON response
echo json_encode($response);

// Close connections
if (isset($stmt) && $stmt) {
    $stmt->close();
}
if (isset($check_stmt) && $check_stmt) {
    $check_stmt->close();
}
if (isset($con) && $con) {
    $con->close();
}

exit;
?>