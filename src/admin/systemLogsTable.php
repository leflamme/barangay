<?php
// 1. Start buffering to prevent whitespace errors
ob_start();

include_once '../connection.php';

// 2. Clear buffer
ob_clean(); 

// 3. Set JSON header
header('Content-Type: application/json');

try {
    // Columns that DataTables expects
    $col = ['id', 'message', 'date'];
    
    // Select everything so we can access the 'status' column
    $sql = "SELECT * FROM activity_log";
    $whereClauses = [];

    // --- FIXED FILTERS (Using the 'status' column) ---
    if (isset($_POST['log_type_filter']) && !empty($_POST['log_type_filter'])) {
        $filter = $con->real_escape_string($_POST['log_type_filter']);
        
        // We check BOTH the 'status' column AND the 'message' column to be safe
        switch ($filter) {
            case 'LOGIN':
                $whereClauses[] = "(status = 'login' OR message LIKE '%logged in%')";
                break;
            case 'LOGOUT':
                $whereClauses[] = "(status = 'logout' OR message LIKE '%logged out%')";
                break;
            case 'UPDATE':
                // Matches 'update' (edit) AND 'create' (add)
                $whereClauses[] = "(status IN ('update', 'create', 'edit') OR message LIKE '%update%' OR message LIKE '%added%')";
                break;
            case 'DELETE':
                $whereClauses[] = "(status = 'delete' OR message LIKE '%deleted%' OR message LIKE '%archive%')";
                break;
        }
    }

    // --- SEARCH BAR ---
    if (isset($_REQUEST['search']['value']) && !empty($_REQUEST['search']['value'])) {
        $searchValue = $con->real_escape_string($_REQUEST['search']['value']);
        $whereClauses[] = "(message LIKE '%" . $searchValue . "%' OR date LIKE '%" . $searchValue . "%' OR status LIKE '%" . $searchValue . "%')";
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
    // Prevent SQL injection in ordering
    $orderColumnIndex = isset($_REQUEST['order'][0]['column']) ? intval($_REQUEST['order'][0]['column']) : 0;
    $orderDir = isset($_REQUEST['order'][0]['dir']) && strtolower($_REQUEST['order'][0]['dir']) === 'asc' ? 'ASC' : 'DESC';
    // Map index 0->id, 1->message, 2->date
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
        
        // Combine status and message for a better view? 
        // Or just keep the message as requested. Let's append status nicely if it exists.
        $displayMessage = htmlspecialchars($row['message']);
        /* Optional: Uncomment this line if you want to see the status tag in the table
           if(!empty($row['status'])) {
               $displayMessage = '<span class="badge badge-info">' . strtoupper($row['status']) . '</span> ' . $displayMessage;
           } 
        */
        
        $subdata[] = $displayMessage;
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