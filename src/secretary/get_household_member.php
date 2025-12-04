<?php
// 1. START OUTPUT BUFFERING (Crucial for clean JSON)
ob_start();

session_start();

// Disable error display to prevent HTML error text from breaking JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Include database connection
include_once '../connection.php';

// Check session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$response = ['success' => false, 'message' => 'Unknown error'];

try {
    if (!isset($_POST['household_id'])) {
        throw new Exception("Household ID is required");
    }

    $household_id = (int)$_POST['household_id'];

    // Query to get members
    $sql = "SELECT 
                hm.id as member_id,
                hm.relationship_to_head,
                hm.is_head,
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
            ORDER BY hm.is_head DESC, u.last_name ASC"; // Head first, then alphabetical

    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $household_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $members = [];
    $total_members = 0;

    while ($row = $result->fetch_assoc()) {
        $total_members++;
        
        // Format Name
        $name = trim($row['first_name'] . ' ' . $row['last_name']);
        if (empty($name)) $name = "Unknown Member";

        // Format Relationship
        $relationship = $row['relationship_to_head'];
        if ($row['is_head'] == 1) $relationship = "Head of Household";

        $members[] = [
            'name' => htmlspecialchars($name),
            'relationship' => htmlspecialchars($relationship),
            'age' => $row['age'],
            'contact' => $row['contact_number'] ?? '',
            'status' => $row['status'],
            'pwd' => $row['pwd'],
            'senior' => $row['senior'],
            'single_parent' => $row['single_parent'],
            'voters' => $row['voters']
        ];
    }

    $response = [
        'success' => true,
        'total_members' => $total_members,
        'members' => $members
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// 2. CLEAN BUFFER AND OUTPUT JSON
ob_clean();
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>