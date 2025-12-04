<?php
// 1. Start buffering to prevent whitespace errors
ob_start();

include_once '../connection.php';

// 2. Clear buffer
ob_clean(); 

// 3. Set JSON header
header('Content-Type: application/json');

try {
    $col = ['id', 'message', 'date'];
    $sql = "SELECT * FROM activity_log";
    $whereClauses = [];

    // --- FILTERS (Now Case-Insensitive) ---
    if (isset($_POST['log_type_filter']) && !empty($_POST['log_type_filter'])) {
        $filter = $con->real_escape_string($_POST['log_type_filter']);
        
        // We use LOWER(...) to ensure uppercase DB data matches our lowercase search terms
        switch ($filter) {
            case 'LOGIN':
                $whereClauses[] = "(LOWER(status) = 'login' OR LOWER(message) LIKE '%logged in%' OR LOWER(message) LIKE '%login%')";
                break;
            case 'LOGOUT':
                $whereClauses[] = "(LOWER(status) = 'logout' OR LOWER(message) LIKE '%logged out%' OR LOWER(message) LIKE '%logout%')";
                break;
            case 'UPDATE':
                // Matches 'update', 'create', 'edit', 'added', 'modified'
                $whereClauses[] = "(LOWER(status) IN ('update', 'create', 'edit') OR LOWER(message) LIKE '%update%' OR LOWER(message) LIKE '%add%' OR LOWER(message) LIKE '%create%')";
                break;
            case 'DELETE':
                $whereClauses[] = "(LOWER(status) = 'delete' OR LOWER(message) LIKE '%delete%' OR LOWER(message) LIKE '%remove%' OR LOWER(message) LIKE '%archive%')";
                break;
        }
    }

    // --- SEARCH BAR (Now Case-Insensitive) ---
    if (isset($_REQUEST['search']['value']) && !empty($_REQUEST['search']['value'])) {
        $searchValue = $con->real_escape_string($_REQUEST['search']['value']);
        // Search across message, date, AND status
        $whereClauses[] = "(LOWER(message) LIKE LOWER('%" . $searchValue . "%') OR date LIKE '%" . $searchValue . "%' OR LOWER(status) LIKE LOWER('%" . $searchValue . "%'))";
    }

    // Apply Filters
    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(' AND ', $whereClauses);
    }

    // --- COUNT FILTERED ---
    $stmt_filtered = $con->prepare($sql);
    if (!$stmt_filtered) { throw new Exception("Query Error: " . $con->error); }
    $stmt_filtered->execute();
    $result_filtered = $stmt_filtered->get_result();
    $totalFiltered = $result_filtered->num_rows;

    // --- ORDERING ---
    $orderColumnIndex = isset($_REQUEST['order'][0]['column']) ? intval($_REQUEST['order'][0]['column']) : 0;
    $orderDir = isset($_REQUEST['order'][0]['dir']) && strtolower($_REQUEST['order'][0]['dir']) === 'asc' ? 'ASC' : 'DESC';
    $orderBy = isset($col[$orderColumnIndex]) ? $col[$orderColumnIndex] : 'id';
    
    $sql .= " ORDER BY " . $orderBy . " " . $orderDir;

    // --- PAGINATION ---
    $start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
    $length = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 10;
    
    if ($length != -1) {
        $sql .= " LIMIT " . $start . ", " . $length;
    }

    // --- FETCH DATA ---
    $stmt = $con->prepare($sql);
    if (!$stmt) { throw new Exception("Query Error: " . $con->error); }
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $subdata = [];
        $subdata[] = $row['id'];
        
        // Display Message (we don't change the display, just the search logic)
        $subdata[] = htmlspecialchars($row['message']);
        $subdata[] = htmlspecialchars($row['date']);
        $data[] = $subdata;
    }

    // --- TOTAL COUNT (Unfiltered) ---
    $total_query = "SELECT COUNT(*) as total FROM `activity_log`";
    $total_stmt = $con->prepare($total_query);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result()->fetch_assoc();
    $totalData = $total_result['total'];

    echo json_encode([
        'draw' => intval($_REQUEST['draw'] ?? 0),
        'recordsTotal' => intval($totalData),
        'recordsFiltered' => intval($totalFiltered),
        'data' => $data,
    ]);

} catch (Exception $e) {
    // Return error as JSON so DataTables alerts the user
    echo json_encode([
        'draw' => intval($_REQUEST['draw'] ?? 0),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => "Server Error: " . $e->getMessage()
    ]);
}
?>