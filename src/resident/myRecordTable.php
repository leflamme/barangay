<?php 
session_start();
include_once '../connection.php';

$draw = intval($_REQUEST['draw'] ?? 1);
$data = [];
$totalDataBlotter = 0;
$totalFilteredBlotter = 0;

try {
    // 1. Get the logged-in user's ID
    $complainant_id = $_SESSION['user_id'] ?? '';
    
    if (empty($complainant_id) || $_SESSION['user_type'] != 'resident') {
        throw new Exception("User not authenticated.");
    }

    // 2. Count total records WHERE THE USER IS THE COMPLAINANT
    $sql_count = "SELECT COUNT(*) as total 
                  FROM blotter_record br
                  JOIN blotter_complainant bc ON br.blotter_id = bc.blotter_main
                  WHERE bc.complainant_id = ?";
                  
    $stmt_count = $con->prepare($sql_count);
    $stmt_count->bind_param('s', $complainant_id);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $totalDataBlotter = $result_count->fetch_assoc()['total'] ?? 0;
    $totalFilteredBlotter = $totalDataBlotter;

    // 3. Get the actual data for the table
    $sql = "SELECT br.* FROM blotter_record br
            JOIN blotter_complainant bc ON br.blotter_id = bc.blotter_main
            WHERE bc.complainant_id = ?
            ORDER BY br.date_reported DESC";
            
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $complainant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // 4. Format the data for DataTables
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
        $subdata[] = "1"; // For the fnRowCallback color
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

} catch(Exception $e) {
    // In case of error, send back an empty table structure
    $data = [];
    $totalDataBlotter = 0;
    $totalFilteredBlotter = 0;
}

$json_data = [
    'draw' => $draw,
    'recordsTotal' => intval($totalDataBlotter),
    'recordsFiltered' => intval($totalFilteredBlotter),
    'data' => $data,
];

echo json_encode($json_data);
$con->close();
?>