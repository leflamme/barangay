<?php
include_once '../connection.php';

try {
    $draw = intval($_REQUEST['draw'] ?? 1);
    $start = intval($_REQUEST['start'] ?? 0);
    $length = intval($_REQUEST['length'] ?? 10);
    $searchValue = $_REQUEST['search']['value'] ?? '';
    
    $params = [];
    $paramTypes = '';

    // 1. Get Total Record Count (recordsTotal)
    $sql_total = "SELECT COUNT(blotter_id) as total FROM blotter_record";
    $query_total = $con->prepare($sql_total);
    $query_total->execute();
    $result_total = $query_total->get_result();
    $totalData = $result_total->fetch_assoc()['total'] ?? 0;

    // 2. Build the Main Query with Search (recordsFiltered)
    $sql_main = "SELECT * FROM blotter_record";
    
    // Add WHERE clause if there is a search
    if (!empty($searchValue)) {
        $sql_main .= " WHERE (status LIKE ? OR blotter_id LIKE ? OR remarks LIKE ? OR type_of_incident LIKE ? OR location_incident LIKE ? OR date_incident LIKE ? OR date_reported LIKE ?)";
        $likeValue = "%{$searchValue}%";
        for ($i = 0; $i < 7; $i++) {
            $params[] = &$likeValue;
            $paramTypes .= 's';
        }
    }

    // 3. Get Total Filtered Count
    $query_filtered = $con->prepare($sql_main);
    if (!empty($params)) {
        $query_filtered->bind_param($paramTypes, ...$params);
    }
    $query_filtered->execute();
    $result_filtered = $query_filtered->get_result();
    $totalFiltered = $result_filtered->num_rows;

    // 4. Add Order and Limit for pagination
    $sql_main .= " ORDER BY date_reported DESC"; // Default order
    if ($length != -1) {
        $sql_main .= " LIMIT ?, ?";
        $params[] = &$start;
        $params[] = &$length;
        $paramTypes .= 'ii';
    }

    // 5. Run the Final Query to get the data
    $query_data = $con->prepare($sql_main);
    if (!empty($params)) {
        $query_data->bind_param($paramTypes, ...$params);
    }
    $query_data->execute();
    $result_data = $query_data->get_result();
    $data = [];

    while($row_blotter_check = $result_data->fetch_assoc()){
        date_default_timezone_set('Asia/Manila');
        $date_incident = date("m/d/Y - h:i A", strtotime($row_blotter_check['date_incident']));
        $date_reported = date("m/d/Y - h:i A", strtotime($row_blotter_check['date_reported']));

        if($row_blotter_check['status'] == 'NEW'){
            $status_blotter = '<span class="badge badge-primary">'.$row_blotter_check['status'] .'</span>';
        }else{
            $status_blotter = '<span class="badge badge-warning">'.$row_blotter_check['status'] .'</span>';
        }

        if($row_blotter_check['remarks'] == 'CLOSED'){
            $remarks_blotter = '<span class="badge badge-success">'.$row_blotter_check['remarks'] .'</span>';
        }else{
            $remarks_blotter = '<span class="badge badge-danger">'.$row_blotter_check['remarks'] .'</span>';
        }

        $subdata = [];
        $subdata[] = '<input type="checkbox" id="'. $row_blotter_check['blotter_id'].'" class="sub_checkbox">';
        $subdata[] = $row_blotter_check['blotter_id'];
        $subdata[] = $status_blotter;
        $subdata[] = $remarks_blotter;
        $subdata[] = $row_blotter_check['type_of_incident'];
        $subdata[] = $row_blotter_check['location_incident'];
        $subdata[] = $date_incident;
        $subdata[] = $date_reported;
        $subdata[] =   '<i style="cursor: pointer;  color: yellow;  text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" class="fa fa-book-open text-lg px-2 viewRecords" id="'.$row_blotter_check['blotter_id'].'"></i>';

        $data[] = $subdata;
    }

    // 6. Send the JSON response
    $json_data = [
        'draw' => $draw,
        'recordsTotal' => intval($totalData),
        'recordsFiltered' => intval($totalFiltered),
        'data' => $data,
    ];

    echo json_encode($json_data);

} catch(Exception $e){
    // Send a valid, empty JSON response in case of error
    echo json_encode([
        'draw' => intval($_REQUEST['draw'] ?? 1),
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => $e->getMessage() // Optional: for debugging
    ]);
}
?>