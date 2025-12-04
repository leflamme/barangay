<?php
// 1. Start buffering immediately to catch any accidental text or warnings
ob_start();

// Load connection
include_once '../connection.php';

// 2. Clear the buffer. This wipes out any PHP Warnings or "Connected" messages from connection.php
ob_clean(); 

// 3. Set the correct header
header('Content-Type: application/json');

try {
    // Default columns
    $col = ['id', 'message', 'date'];
    $sql = "SELECT * FROM activity_log";
    $whereClauses = [];

    // --- FILTERS ---
    if (isset($_POST['log_type_filter']) && !empty($_POST['log_type_filter'])) {
        $filter = $con->real_escape_string($_POST['log_type_filter']);
        switch ($filter) {
            case 'LOGIN':
                $whereClauses[] = "message LIKE '%logged in%'";
                break;
            case 'LOGOUT':
                $whereClauses[] = "message LIKE '%logged out%'";
                break;
            case 'UPDATE':
                $whereClauses[] = "(message LIKE '%updated%' OR message LIKE '%added%')";
                break;
            case 'DELETE':
                $whereClauses[] = "message LIKE '%deleted%'";
                break;
        }
    }

    // --- SEARCH ---
    if (isset($_REQUEST['search']['value']) && !empty($_REQUEST['search']['value'])) {
        $searchValue = $con->real_escape_string($_REQUEST['search']['value']);
        $whereClauses[] = "(message LIKE '%" . $searchValue . "%' OR date LIKE '%" . $searchValue . "%')";
    }

    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(' AND ', $whereClauses);
    }

    // --- COUNT FILTERED RECORDS ---
    $stmt_filtered = $con->prepare($sql);
    if (!$stmt_filtered) {
        throw new Exception("Query Error: " . $con->error);
    }
    $stmt_filtered->execute();
    $result_filtered = $stmt_filtered->get_result();
    $totalFiltered = $result_filtered->num_rows;

    // --- ORDERING ---
    // Validate inputs to prevent errors
    $orderColumnIndex = isset($_REQUEST['order'][0]['column']) ? intval($_REQUEST['order'][0]['column']) : 0;
    $orderDir = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : 'DESC';
    
    // Ensure column index is valid
    $orderBy = isset($col[$orderColumnIndex]) ? $col[$orderColumnIndex] : 'id';
    $sql .= " ORDER BY " . $orderBy . " " . ($orderDir === 'asc' ? 'ASC' : 'DESC');

    // --- PAGINATION (The likely cause of "NaN" errors) ---
    // We force these to be integers using intval(). If they are "NaN", they become 0.
    $start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
    $length = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 10;

    if ($length != -1) {
        $sql .= " LIMIT " . $start . ", " . $length;
    }

    // --- GET DATA ---
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Query Error: " . $con->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $subdata = [];
        $subdata[] = $row['id'];
        // Use htmlspecialchars to prevent breaking the table HTML with weird characters
        $subdata[] = htmlspecialchars($row['message']);
        $subdata[] = htmlspecialchars($row['date']);
        $data[] = $subdata;
    }

    // --- TOTAL COUNT ---
    $total_query = "SELECT COUNT(*) as total FROM `activity_log`";
    $total_stmt = $con->prepare($total_query);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result()->fetch_assoc();
    $totalData = $total_result['total'];

    // Output valid JSON
    $json_data = [
        'draw' => intval($_REQUEST['draw'] ?? 0),
        'recordsTotal' => intval($totalData),
        'recordsFiltered' => intval($totalFiltered),
        'data' => $data,
    ];

    echo json_encode($json_data);

} catch (Exception $e) {
    // If there is an error, send it as JSON so DataTables doesn't crash
    echo json_encode([
        'draw' => intval($_REQUEST['draw'] ?? 0),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => $e->getMessage() 
    ]);
}
?>