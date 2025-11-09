<?php 
session_start();
include_once '../connection.php';

try {
    $edit_residence_id = $_REQUEST['edit_residence_id'] ?? '';
    
    if (empty($edit_residence_id)) {
        echo json_encode([
            'draw' => intval($_REQUEST['draw'] ?? 1),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => []
        ]);
        exit;
    }

    // Get user's name to match against respodent field
    $sql_user = "SELECT first_name, last_name FROM users WHERE id = ?";
    $stmt_user = $con->prepare($sql_user);
    if (!$stmt_user) {
        throw new Exception("Database error");
    }
    $stmt_user->bind_param('s', $edit_residence_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    
    if ($result_user->num_rows === 0) {
        echo json_encode([
            'draw' => intval($_REQUEST['draw'] ?? 1),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => []
        ]);
        exit;
    }
    
    $user_data = $result_user->fetch_assoc();
    $user_full_name = trim($user_data['first_name'] . ' ' . $user_data['last_name']);

    $draw = intval($_REQUEST['draw'] ?? 1);
    
    // Count total records for this user - try both exact match and LIKE for safety
    $sql_count = "SELECT COUNT(*) as total FROM blotter_record WHERE TRIM(respodent) = ?";
    $stmt_count = $con->prepare($sql_count);
    if (!$stmt_count) {
        throw new Exception("Database error");
    }
    $stmt_count->bind_param('s', $user_full_name);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $totalDataBlotter = $result_count->fetch_assoc()['total'] ?? 0;
    
    // If no records found with exact match, try case-insensitive
    if ($totalDataBlotter == 0) {
        $sql_count_like = "SELECT COUNT(*) as total FROM blotter_record WHERE LOWER(TRIM(respodent)) = LOWER(?)";
        $stmt_count_like = $con->prepare($sql_count_like);
        if ($stmt_count_like) {
            $stmt_count_like->bind_param('s', $user_full_name);
            $stmt_count_like->execute();
            $result_count_like = $stmt_count_like->get_result();
            $totalDataBlotter = $result_count_like->fetch_assoc()['total'] ?? 0;
        }
    }
    
    $totalFilteredBlotter = $totalDataBlotter;

    // Get the actual data
    $sql = "SELECT * FROM blotter_record WHERE TRIM(respodent) = ? ORDER BY date_reported DESC";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database error");
    }
    $stmt->bind_param('s', $user_full_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // If no results, try case-insensitive search
    if ($result->num_rows == 0 && $totalDataBlotter > 0) {
        $sql_like = "SELECT * FROM blotter_record WHERE LOWER(TRIM(respodent)) = LOWER(?) ORDER BY date_reported DESC";
        $stmt_like = $con->prepare($sql_like);
        if ($stmt_like) {
            $stmt_like->bind_param('s', $user_full_name);
            $stmt_like->execute();
            $result = $stmt_like->get_result();
        }
    }

    $data = [];
    while($row = $result->fetch_assoc()) {
        date_default_timezone_set('Asia/Manila');
        
        $date_incident = !empty($row['date_incident']) && $row['date_incident'] !== '0000-00-00 00:00:00' ? 
            date("m/d/Y - h:i A", strtotime($row['date_incident'])) : 'N/A';
        $date_reported = !empty($row['date_reported']) && $row['date_reported'] !== '0000-00-00 00:00:00' ? 
            date("m/d/Y - h:i A", strtotime($row['date_reported'])) : 'N/A';

        if($row['status'] == 'NEW') {
            $status_blotter = '<span class="badge badge-primary">'.$row['status'] .'</span>';
        } else {
            $status_blotter = '<span class="badge badge-warning">'.$row['status'] .'</span>';
        }

        if($row['remarks'] == 'CLOSED') {
            $remarks_blotter = '<span class="badge badge-success">'.$row['remarks'] .'</span>';
        } else {
            $remarks_blotter = '<span class="badge badge-danger">'.$row['remarks'] .'</span>';
        }

        $subdata = [];
        $subdata[] = "1";
        $subdata[] = htmlspecialchars($row['blotter_id'], ENT_QUOTES, 'UTF-8');
        $subdata[] = $status_blotter;
        $subdata[] = $remarks_blotter;
        $subdata[] = htmlspecialchars($row['type_of_incident'], ENT_QUOTES, 'UTF-8');
        $subdata[] = htmlspecialchars($row['location_incident'], ENT_QUOTES, 'UTF-8');
        $subdata[] = $date_incident;
        $subdata[] = $date_reported;
        $subdata[] = '<i style="cursor: pointer; color: yellow; text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" class="fa fa-book-open text-lg px-2 viewRecords" id="'.htmlspecialchars($row['blotter_id'], ENT_QUOTES, 'UTF-8').'"></i>';

        $data[] = $subdata;
    }

    $json_data = [
        'draw' => $draw,
        'recordsTotal' => intval($totalDataBlotter),
        'recordsFiltered' => intval($totalFilteredBlotter),
        'data' => $data,
    ];

    echo json_encode($json_data);

} catch(Exception $e) {
    echo json_encode([
        'draw' => intval($_REQUEST['draw'] ?? 1),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => []
    ]);
}

$con->close();
?>