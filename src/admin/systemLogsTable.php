<?php
// systemLogsTable.php

// 1. Start buffering
ob_start();
include_once '../connection.php';
ob_clean(); 
header('Content-Type: application/json');

try {
    // We only have one table, so we sort by its columns
    $col = ['id', 'message', 'message', 'message', 'date']; // Map visual columns to DB columns for sorting

    $sql = "SELECT * FROM activity_log";
    $whereClauses = [];

    // --- FILTERS ---
    if (isset($_POST['log_type_filter']) && !empty($_POST['log_type_filter'])) {
        $filter = $con->real_escape_string($_POST['log_type_filter']);
        // Maps to the 'status' column in your DB image
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

        // --- PARSING LOGIC ---
        // Default values
        $userType = 'System';
        $userName = '-';
        $actionMessage = $rawMessage;

        // 1. Extract User Type (Before the first colon)
        // Example: "ADMIN: Admin Admin | LOGIN" -> Type = ADMIN
        if (strpos($rawMessage, ':') !== false) {
            $parts = explode(':', $rawMessage, 2);
            $userType = strtoupper(trim($parts[0])); 
            $rest = trim($parts[1]);

            // 2. Extract User Name (If a pipe | exists AND it's a Login/Logout event)
            // We verify it's login/logout because other logs (like ADD RESIDENT) might use | for data, not names.
            if (strpos($rest, '|') !== false && (stripos($row['status'], 'log') !== false)) {
                $nameParts = explode('|', $rest, 2);
                $userName = trim($nameParts[0]); // "Admin Admin"
                $actionMessage = trim($nameParts[1]); // "LOGIN"
            } else {
                // If no name is explicitly listed (common in "ADDED RESIDENT" logs), leave name as '-'
                $actionMessage = $rest;
            }
        }

        // --- BADGES ---
        $badgeClass = 'badge-secondary';
        if ($userType === 'ADMIN') $badgeClass = 'badge-danger';
        if ($userType === 'RESIDENT') $badgeClass = 'badge-success';

        // --- BUILD ROW ---
        $subdata[] = $row['id'];
        $subdata[] = "<span class='badge $badgeClass'>$userType</span>";
        $subdata[] = "<span class='font-weight-bold'>$userName</span>";
        $subdata[] = htmlspecialchars($actionMessage);
        $subdata[] = "<small>" . $row['date'] . "</small>";

        $data[] = $subdata;
    }

    // Total count
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