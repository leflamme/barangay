<?php
ob_start();
include_once '../connection.php';
ob_clean(); 
header('Content-Type: application/json');

try {
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
        $status = strtolower($row['status']);

        // --- 1. IDENTIFY USER TYPE ---
        $userType = 'RESIDENT'; // Default
        if (stripos($rawMessage, 'ADMIN:') !== false) $userType = 'ADMIN';
        elseif (stripos($rawMessage, 'OFFICIAL:') !== false) $userType = 'OFFICIAL';

        // --- 2. CLEAN PREFIX ---
        // Remove "ADMIN:", "OFFICIAL:", "RESIDENT:" to get the clean content
        $cleanMsg = preg_replace('/^(ADMIN:|OFFICIAL:|RESIDENT:)\s*/i', '', $rawMessage);

        // --- 3. INTELLIGENT PARSING ---
        $userName = '-';
        $finalMessage = $cleanMsg;

        // CHECK A: Does it start with an ACTION VERB? (e.g. "ADDED", "DELETED", "REGISTER", "UPDATED")
        // If yes, the first part is the MESSAGE, not the name.
        if (preg_match('/^(ADDED|DELETED|UPDATED|REGISTER)/i', $cleanMsg)) {
            
            // Special Case: "REGISTER RESIDENT... | Name"
            // For registration, the name is usually at the end (after the pipe).
            if (stripos($cleanMsg, 'REGISTER') !== false && strpos($cleanMsg, '|') !== false) {
                $parts = explode('|', $cleanMsg, 2);
                $finalMessage = trim($parts[0]); // "REGISTER RESIDENT - ID"
                $userName = trim($parts[1]);     // "Lara Croft"
            } else {
                // Standard Admin Action (e.g. "DELETED BLOTTER RECORD...")
                // In these cases, the User is the Admin (represented as '-'), and the whole text is the message.
                $userName = '-';
                $finalMessage = $cleanMsg;
            }

        } 
        // CHECK B: Standard "Name | Action" format (Login, Logout, Requests)
        elseif (strpos($cleanMsg, '|') !== false) {
            $parts = explode('|', $cleanMsg, 2);
            $leftPart = trim($parts[0]);
            $rightPart = trim($parts[1]);

            // Clean up messy names (e.g. "RESIDENT - 1234 : Stella Carisma")
            if (strpos($leftPart, ':') !== false) {
                $namePieces = explode(':', $leftPart);
                $userName = trim(end($namePieces)); // Takes "Stella Carisma"
            } else {
                $userName = $leftPart;
            }

            $finalMessage = $rightPart;
        }

        // --- 4. BADGES ---
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