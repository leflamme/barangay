<?php
include_once '../connection.php';

try {
    $col = ['id', 'message', 'date'];
    $sql = "SELECT * FROM activity_log";
    $whereClauses = [];

    
    if (isset($_POST['log_type_filter']) && !empty($_POST['log_type_filter'])) {
        $filter = $_POST['log_type_filter'];
        switch ($filter) {
            case 'LOGIN':
                // Matches messages like "admin logged in" or "resident logged in"
                $whereClauses[] = "message LIKE '%logged in%'";
                break;
            case 'LOGOUT':
                // Matches messages like "admin logged out"
                $whereClauses[] = "message LIKE '%logged out%'";
                break;
            case 'UPDATE':
                // Matches messages with "updated" or "added"
                $whereClauses[] = "(message LIKE '%updated%' OR message LIKE '%added%')";
                break;
            case 'DELETE':
                // Matches messages with "deleted"
                $whereClauses[] = "message LIKE '%deleted%'";
                break;
        }
    }

   
    if (isset($_REQUEST['search']['value']) && !empty($_REQUEST['search']['value'])) {
        $searchValue = $_REQUEST['search']['value'];
        $whereClauses[] = "(message LIKE '%" . $searchValue . "%' OR date LIKE '%" . $searchValue . "%')";
    }
    
 
    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(' AND ', $whereClauses);
    }

   
    $stmt_filtered = $con->prepare($sql) or die($con->error);
    $stmt_filtered->execute();
    $result_filtered = $stmt_filtered->get_result();
    $totalFiltered = $result_filtered->num_rows;
    

    if (isset($_REQUEST['order'])) {
        $sql .= ' ORDER BY ' . $col[$_REQUEST['order']['0']['column']] . ' ' . $_REQUEST['order']['0']['dir'] . ' ';
    } else {
        $sql .= ' ORDER BY id DESC ';
    }
    
   
    if ($_REQUEST['length'] != -1) {
        $sql .= ' LIMIT ' . $_REQUEST['start'] . ' ,' . $_REQUEST['length'] . ' ';
    }
    
   
    $stmt = $con->prepare($sql) or die($con->error);
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

  
    $total_query = "SELECT COUNT(*) as total FROM `activity_log`";
    $total_stmt = $con->prepare($total_query);
    $total_stmt->execute();
    $total_result = $total_stmt->get_result()->fetch_assoc();
    $totalData = $total_result['total'];

    $json_data = [
        'draw' => intval($_REQUEST['draw']),
        'recordsTotal' => intval($totalData), // Total records without any filters
        'recordsFiltered' => intval($totalFiltered), // Total records after applying filters
        'data' => $data,
    ];

    echo json_encode($json_data);

} catch (Exception $e) {
    echo $e->getMessage();
}

?>