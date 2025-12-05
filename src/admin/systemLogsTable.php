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

    // --- COUNT & ORDER ---
    $stmt_filtered = $con->prepare($sql);
    $stmt_filtered->execute();
    $totalFiltered = $stmt_filtered->get_result()->num_rows;

    $orderIndex = $_REQUEST['order'][0]['column'] ?? 0;
    $orderDir = $_REQUEST['order'][0]['dir'] ?? 'DESC';
    $orderBy = $col[$orderIndex] ?? 'id';
    $sql .= " ORDER BY " . $orderBy . " " . $orderDir;

    $start = $_REQUEST['start'] ?? 0;
    $length = $_REQUEST['length'] ?? 10;
    if ($length != -1) $sql .= " LIMIT $start, $length";

    // --- FETCH ---
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $subdata = [];
        $rawMessage = $row['message'];
        $status = strtolower($row['status']);

        // --- 1. DETERMINE BADGE / TYPE ---
        $userType = 'RESIDENT'; // Default
        if (stripos($rawMessage, 'ADMIN:') !== false) $userType = 'ADMIN';
        elseif (stripos($rawMessage, 'OFFICIAL:') !== false) $userType = 'OFFICIAL';

        // Clean the message prefix
        $cleanMsg = preg_replace('/^(ADMIN:|OFFICIAL:|RESIDENT:)\s*/i', '', $rawMessage);

        // --- 2. PARSING LOGIC ---
        // Defaults
        $userName = '-'; 
        $finalMessage = $cleanMsg;

        // RULE 1: Login / Logout (Structure is always: NAME | ACTION)
        if ($status == 'login' || $status == 'logout') {
            if (strpos($cleanMsg, '|') !== false) {
                $parts = explode('|', $cleanMsg, 2);
                $userName = trim($parts[0]);
                $finalMessage = trim($parts[1]);
            }
        }
        // RULE 2: Registration (Structure is always: ACTION | NAME)
        elseif (stripos($cleanMsg, 'REGISTER') !== false) {
            if (strpos($cleanMsg, '|') !== false) {
                $parts = explode('|', $cleanMsg, 2);
                $finalMessage = trim($parts[0]);
                $userName = trim($parts[1]);
            }
        }
        // RULE 3: Certificate Requests (Structure is usually: NAME | REQUEST)
        elseif (stripos($cleanMsg, 'REQUEST') !== false && strpos($cleanMsg, '|') !== false) {
            $parts = explode('|', $cleanMsg, 2);
            // Handle complex ID prefixes like "RESIDENT - 875823: David Santos"
            $leftSide = trim($parts[0]);
            if (strpos($leftSide, ':') !== false) {
                $nameParts = explode(':', $leftSide);
                $userName = trim(end($nameParts));
            } else {
                $userName = $leftSide;
            }
            $finalMessage = trim($parts[1]);
        }
        // RULE 4: Admin Actions (Delete, Add, Update)
        // We DO NOT try to parse a name here. The "User" is just the Admin type.
        // This prevents the "DELETED BLOTTER RECORD" error you saw.
        else {
            $userName = '-';
            $finalMessage = $cleanMsg;
        }

        // --- 3. RENDER ---
        $badgeClass = 'badge-secondary';
        if ($userType === 'ADMIN') $badgeClass = 'badge-danger';
        if ($userType === 'RESIDENT') $badgeClass = 'badge-success';
        if ($userType === 'OFFICIAL') $badgeClass = 'badge-info';

        $subdata[] = $row['id'];
        $subdata[] = "<span class='badge $badgeClass'>$userType</span>";
        $subdata[] = "<span class='font-weight-bold'>$userName</span>";
        $subdata[] = htmlspecialchars($finalMessage);
        $subdata[] = "<small>" . $row['date'] . "</small>";

        $data[] = $subdata;
    }

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