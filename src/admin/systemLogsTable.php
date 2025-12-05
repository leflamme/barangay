<?php
// systemLogsTable.php

ob_start();
include_once '../connection.php';
ob_clean(); 
header('Content-Type: application/json');

try {
    // Columns for DataTables sorting
    $col = ['id', 'status', 'message', 'message', 'date']; 

    $sql = "SELECT * FROM activity_log";
    $whereClauses = [];

    // --- FILTERS ---
    if (isset($_POST['log_type_filter']) && !empty($_POST['log_type_filter'])) {
        $filter = $con->real_escape_string($_POST['log_type_filter']);
        
        if ($filter == 'LOGIN')  $whereClauses[] = "LOWER(status) = 'login'";
        if ($filter == 'LOGOUT') $whereClauses[] = "LOWER(status) = 'logout'";
        if ($filter == 'UPDATE') $whereClauses[] = "LOWER(status) IN ('update', 'create', 'edit')";
        if ($filter == 'DELETE') $whereClauses[] = "LOWER(status) = 'delete'";
    }

    // --- SEARCH ---
    if (isset($_REQUEST['search']['value']) && !empty($_REQUEST['search']['value'])) {
        $searchValue = $con->real_escape_string($_REQUEST['search']['value']);
        $whereClauses[] = "(LOWER(message) LIKE LOWER('%$searchValue%') OR date LIKE '%$searchValue%' OR LOWER(status) LIKE LOWER('%$searchValue%'))";
    }

    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(' AND ', $whereClauses);
    }

    // --- COUNT FILTERED ---
    $stmt_filtered = $con->prepare($sql);
    $stmt_filtered->execute();
    $totalFiltered = $stmt_filtered->get_result()->num_rows;

    // --- ORDERING ---
    $orderIndex = $_REQUEST['order'][0]['column'] ?? 0;
    $orderDir = $_REQUEST['order'][0]['dir'] ?? 'DESC';
    $orderBy = $col[$orderIndex] ?? 'id';
    $sql .= " ORDER BY " . $orderBy . " " . $orderDir;

    // --- PAGINATION ---
    $start = $_REQUEST['start'] ?? 0;
    $length = $_REQUEST['length'] ?? 10;
    if ($length != -1) $sql .= " LIMIT $start, $length";

    // --- FETCH DATA ---
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $subdata = [];
        $rawMessage = $row['message'];
        $status = strtolower($row['status']); // login, logout, create, delete, update

        // --- 1. IDENTIFY USER TYPE ---
        $userType = 'RESIDENT'; // Default
        if (stripos($rawMessage, 'ADMIN:') !== false) {
            $userType = 'ADMIN';
        } elseif (stripos($rawMessage, 'OFFICIAL:') !== false) {
            $userType = 'OFFICIAL';
        }

        // --- 2. CLEAN PREFIX ---
        // Remove "ADMIN:", "OFFICIAL:", or "RESIDENT:" from the start of the string
        $cleanMsg = preg_replace('/^(ADMIN:|OFFICIAL:|RESIDENT:)\s*/i', '', $rawMessage);

        // --- 3. PARSE NAME vs MESSAGE ---
        $userName = '-';
        $finalMessage = $cleanMsg;

        if (strpos($cleanMsg, '|') !== false) {
            $parts = explode('|', $cleanMsg, 2);
            $partA = trim($parts[0]); // Left of pipe
            $partB = trim($parts[1]); // Right of pipe

            // SCENARIO A: Login or Logout (Format: Name | Action)
            if ($status == 'login' || $status == 'logout') {
                $userName = $partA;
                $finalMessage = $partB;
            }
            // SCENARIO B: Resident Request (Format: Name | Request Type)
            // Usually Resident logs don't start with "ADMIN:"
            elseif ($userType == 'RESIDENT') {
                $userName = $partA;
                $finalMessage = $partB;
            }
            // SCENARIO C: Admin Actions (Add/Delete/Update)
            // Format: ACTION - ID | Subject Name
            // In this case, Part A is the ACTION, not the user name.
            else {
                $userName = '-'; // The Admin's specific name isn't in these logs
                $finalMessage = $cleanMsg; // Show the full detail line
            }
        }

        // --- 4. BADGE COLORS ---
        $badgeClass = 'badge-secondary';
        if ($userType === 'ADMIN') $badgeClass = 'badge-danger';
        if ($userType === 'RESIDENT') $badgeClass = 'badge-success';
        if ($userType === 'OFFICIAL') $badgeClass = 'badge-info';

        // --- BUILD ROW ---
        $subdata[] = $row['id'];
        $subdata[] = "<span class='badge $badgeClass'>$userType</span>";
        $subdata[] = "<span class='font-weight-bold'>$userName</span>";
        $subdata[] = htmlspecialchars($finalMessage);
        $subdata[] = "<small>" . $row['date'] . "</small>";

        $data[] = $subdata;
    }

    // Total count query
    $total_query = "SELECT COUNT(*) as total FROM `activity_log`";
    $total_res = $con->query($total_query)->fetch_assoc();

    echo json_encode([
        'draw' => intval($_REQUEST['draw'] ?? 0),
        'recordsTotal' => intval($total_res['total']),
        'recordsFiltered' => intval($totalFiltered),
        'data' => $data,
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>