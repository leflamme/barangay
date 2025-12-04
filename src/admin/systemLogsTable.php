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

    // --- IMPROVED FILTERS ---
    if (isset($_POST['log_type_filter']) && !empty($_POST['log_type_filter'])) {
        $filter = $con->real_escape_string($_POST['log_type_filter']);
        switch ($filter) {
            case 'LOGIN':
                // Matches "User Login", "Admin Logged In", "Successfully Logged In"
                $whereClauses[] = "(message LIKE '%login%' OR message LIKE '%logged in%')";
                break;
            case 'LOGOUT':
                // Matches "User Logout", "Logged Out"
                $whereClauses[] = "(message LIKE '%logout%' OR message LIKE '%logged out%')";
                break;
            case 'UPDATE':
                // Matches "Updated profile", "Added new user", "Modified"
                $whereClauses[] = "(message LIKE '%update%' OR message LIKE '%add%' OR message LIKE '%register%' OR message LIKE '%create%')";
                break;
            case 'DELETE':
                // Matches "Deleted user", "Remove", "Archive"
                $whereClauses[] = "(message LIKE '%delete%' OR message LIKE '%remove%' OR message LIKE '%archive%')";
                break;
        }
    }

    // --- SEARCH BAR ---
    if (isset($_REQUEST['search']['value']) && !empty($_REQUEST['search']['value'])) {
        $searchValue = $con->real_escape_string($_REQUEST['search']['value']);
        $whereClauses[] = "(message LIKE '%" . $searchValue . "%' OR date LIKE '%" . $searchValue . "%')";
    }

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
    $orderDir = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir'] : 'DESC';
    $orderBy = isset($col[$orderColumnIndex]) ? $col[$orderColumnIndex] : 'id';
    $sql .= " ORDER BY " . $orderBy . " " . ($orderDir === 'asc' ? 'ASC' : 'DESC');

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
    echo json_encode([
        'draw' => intval($_REQUEST['draw'] ?? 0),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => $e->getMessage()
    ]);
}
?>