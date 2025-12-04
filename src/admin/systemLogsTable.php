<?php
// 1. Start buffering immediately to catch any accidental whitespace or included text
ob_start();

include_once '../connection.php';

// 2. Clear the buffer. This wipes out any newlines/spaces from connection.php
ob_clean(); 

// 3. Set the correct header so the browser knows this is JSON
header('Content-Type: application/json');

try {
    $col = ['id', 'message', 'date'];
    $sql = "SELECT * FROM activity_log";
    $whereClauses = [];

    // --- FILTERS ---
    if (isset($_POST['log_type_filter']) && !empty($_POST['log_type_filter'])) {
        $filter = $_POST['log_type_filter'];
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
        $searchValue = $_REQUEST['search']['value'];
        $whereClauses[] = "(message LIKE '%" . $searchValue . "%' OR date LIKE '%" . $searchValue . "%')";
    }

    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(' AND ', $whereClauses);
    }

    // --- COUNT FILTERED RECORDS ---
    // Remove 'or die()' so we don't break JSON. Throw exception instead.
    $stmt_filtered = $con->prepare($sql);
    if(!$stmt_filtered) { throw new Exception($con->error); }
    
    $stmt_filtered->execute();
    $result_filtered = $stmt_filtered->get_result();
    $totalFiltered = $result_filtered->num_rows;

    // --- ORDERING ---
    if (isset($_REQUEST['order'])) {
        $sql .= ' ORDER BY ' . $col[$_REQUEST['order']['0']['column']] . ' ' . $_REQUEST['order']['0']['dir'] . ' ';
    } else {
        $sql .= ' ORDER BY id DESC ';
    }

    // --- PAGINATION ---
    if ($_REQUEST['length'] != -1) {
        $sql .= ' LIMIT ' . $_REQUEST['start'] . ' ,' . $_REQUEST['length'] . ' ';
    }

    // --- GET DATA ---
    $stmt = $con->prepare($sql);
    if(!$stmt) { throw new Exception($con->error); }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $subdata = [];
        $subdata[] = $row['id'];
        $subdata[] = $row['message'];
        $subdata[] = $row['date'];
        $data[] = $subdata;
    }

    // --- TOTAL COUNT ---
    $total_query = "SELECT COUNT(*) as total FROM `activity_log`";
    $total_stmt = $con->prepare($total_query);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result()->fetch_assoc();
    $totalData = $total_result['total'];

    $json_data = [
        'draw' => intval($_REQUEST['draw']),
        'recordsTotal' => intval($totalData),
        'recordsFiltered' => intval($totalFiltered),
        'data' => $data,
    ];

    echo json_encode($json_data);

} catch (Exception $e) {
    // 4. Return valid JSON even if there is an error
    echo json_encode([
        'draw' => intval($_REQUEST['draw'] ?? 0),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => $e->getMessage()
    ]);
}
?>