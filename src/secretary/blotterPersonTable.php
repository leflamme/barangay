<?php 
include_once '../connection.php';

// Set a default JSON structure for DataTables
$json_data = [
    'draw' => intval($_REQUEST['draw'] ?? 1),
    'recordsTotal' => 0,
    'recordsFiltered' => 0,
    'data' => [],
];

try{
    $edit_residence_id = $_REQUEST['edit_residence_id'] ?? '';
  
    if(empty($edit_residence_id)){
        // If no ID is provided, just return the empty JSON.
        echo json_encode($json_data);
        exit;
    }

    // 2. Fix the SQL Query:
    // - Use LEFT JOIN to find matches even if one table is missing data.
    // - Search in both complainant and status tables.
    // - Use GROUP BY to avoid duplicate blotter records.
    $sql_blooter_check = "SELECT 
                            br.*, 
                            br.blotter_id AS gago,
                            bc.complainant_id,
                            bs.person_id
                        FROM blotter_record br
                        LEFT JOIN blotter_complainant bc ON br.blotter_id = bc.blotter_main
                        LEFT JOIN blotter_status bs ON br.blotter_id = bs.blotter_main
                        WHERE 
                            bc.complainant_id = ? OR bs.person_id = ?
                        GROUP BY br.blotter_id
                        ORDER BY br.date_reported DESC";
    
    $query_blotter_check = $con->prepare($sql_blooter_check);
    if (!$query_blotter_check) {
        throw new Exception("SQL Prepare Error: " . $con->error);
    }
    
    $query_blotter_check->bind_param('ss', $edit_residence_id, $edit_residence_id);
    $query_blotter_check->execute();
    $result_blotter_check = $query_blotter_check->get_result();
    
    $totalDataBlotter = $result_blotter_check->num_rows;
    $totalFilteredBlotter = $totalDataBlotter;

    $data= [];

    while($row_blotter_check = $result_blotter_check->fetch_assoc()){

        date_default_timezone_set('Asia/Manila');
        $date_incident= date("m/d/Y - h:i A", strtotime($row_blotter_check['date_incident']));
        $date_reported= date("m/d/Y - h:i A", strtotime($row_blotter_check['date_reported']));


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

        // Color-code: 1 (green) if they are the complainant, 2 (black/other) if they are involved/respondent
        if($row_blotter_check['complainant_id'] == $edit_residence_id){
            $color = 1;
        }else{
            $color = 2;
        }

        $subdata = [];

        $subdata[] = $color;
        $subdata[] = $row_blotter_check['gago'];
        $subdata[] = $status_blotter;
        $subdata[] = $remarks_blotter;
        $subdata[] = $row_blotter_check['type_of_incident'];
        $subdata[] = $row_blotter_check['location_incident'];
        $subdata[] = $date_incident;
        $subdata[] = $date_reported;
        
        // The original action buttons were commented out, adding a simple view button
        $subdata[] =   '<i style="cursor: pointer;  color: yellow;  text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" class="fa fa-book-open text-lg px-2 viewRecords" id="'.$row_blotter_check['gago'].'"></i>';

        $data[] = $subdata;
    }

    // Update the JSON data packet
    $json_data['recordsTotal'] = intval($totalDataBlotter);
    $json_data['recordsFiltered'] = intval($totalFilteredBlotter);
    $json_data['data'] = $data;

} catch(Exception $e) {
    // 3. Fix the Catch Block:
    // If there's an error, add it to the JSON response.
    // This is valid JSON and DataTables will show it.
    $json_data['error'] = $e->getMessage();
}

// Always echo valid JSON
echo json_encode($json_data);
$con->close();
?>