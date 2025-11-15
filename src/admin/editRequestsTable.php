<?php
include_once '../connection.php';

try {
    $draw = intval($_REQUEST['draw'] ?? 1);
    $start = intval($_REQUEST['start'] ?? 0);
    $length = intval($_REQUEST['length'] ?? 10);
    $searchValue = $_REQUEST['search']['value'] ?? '';

    // Count total records
    $sql_total = "SELECT COUNT(r.id) as total 
                  FROM edit_requests r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.status = 'PENDING'";
    $query_total = $con->prepare($sql_total);
    $query_total->execute();
    $result_total = $query_total->get_result();
    $totalData = $result_total->fetch_assoc()['total'] ?? 0;
    $totalFiltered = $totalData; // We're not using search for this simple table

    // Build the Main Query
    $sql_main = "SELECT r.id, r.user_id, r.status, r.request_date, u.first_name, u.last_name 
                 FROM edit_requests r 
                 JOIN users u ON r.user_id = u.id 
                 WHERE r.status = 'PENDING'";

    // Add Order and Limit for pagination
    $sql_main .= " ORDER BY r.request_date ASC LIMIT ?, ?";
    
    $query_data = $con->prepare($sql_main);
    $query_data->bind_param('ii', $start, $length);
    $query_data->execute();
    $result_data = $query_data->get_result();
    $data = [];

    while($row = $result_data->fetch_assoc()){
        
        $resident_name = ucfirst($row['first_name']) . ' ' . ucfirst($row['last_name']);
        $date_requested = date("F d, Y - h:i A", strtotime($row['request_date']));
        $status_badge = '<span class="badge badge-warning">PENDING</span>';

        $action_buttons = '
            <button class="btn btn-success btn-sm approveRequest" data-id="'.$row['id'].'">
                <i class="fas fa-check"></i> Approve
            </button>
            <button class="btn btn-danger btn-sm denyRequest" data-id="'.$row['id'].'">
                <i class="fas fa-times"></i> Deny
            </button>
        ';

        $subdata = [];
        $subdata[] = $resident_name;
        $subdata[] = $date_requested;
        $subdata[] = $status_badge;
        $subdata[] = $action_buttons;

        $data[] = $subdata;
    }

    // Send the JSON response
    $json_data = [
        'draw' => $draw,
        'recordsTotal' => intval($totalData),
        'recordsFiltered' => intval($totalFiltered),
        'data' => $data,
    ];

    echo json_encode($json_data);

} catch(Exception $e){
    echo json_encode([
        'draw' => intval($_REQUEST['draw'] ?? 1),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => $e->getMessage()
    ]);
}
?>