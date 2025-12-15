<?php
// get_evacuation_family.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../connection.php';

if(isset($_POST['surname'])) {
    $surname = $_POST['surname'];

    // UPDATED SQL:
    // 1. INNER JOIN residence_status to filter by 'Active'
    // 2. Select 'residency_type' to identify Tenants
    $sql = "SELECT 
                r.residence_id, 
                r.first_name, 
                r.middle_name, 
                r.last_name, 
                r.age, 
                r.gender,
                s.residency_type,  
                COALESCE(es.status, 'Missing') as status
            FROM residence_information r
            INNER JOIN residence_status s ON r.residence_id = s.residence_id
            LEFT JOIN evacuation_status es ON r.residence_id = es.residence_id
            WHERE r.last_name = ? 
            AND s.status = 'Active'  /* This hides Inactive users like Ren Li */
            ORDER BY r.age DESC"; 
            
    if($stmt = $con->prepare($sql)){
        $stmt->bind_param('s', $surname);
        $stmt->execute();
        $result = $stmt->get_result();

        echo '<div class="table-responsive">';
        echo '<table class="table table-hover">';
        echo '<thead class="bg-light">
                <tr>
                    <th>Full Name</th>
                    <th>Age</th>
                    <th>Type</th> <th>Gender</th>
                    <th>Status</th>
                </tr>
              </thead>';
        echo '<tbody>';

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $fullname = ucfirst($row['first_name']) . ' ' . ucfirst($row['middle_name']) . ' ' . ucfirst($row['last_name']);
                $status = $row['status'];
                $type = $row['residency_type']; 
                
                // Button Logic
                $btn_class = ($status == 'Arrived') ? 'btn-success' : 'btn-danger';
                $btn_icon = ($status == 'Arrived') ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';
                
                // Badge Logic
                if($type == 'Tenant'){
                    $badge = '<span class="badge badge-info">Tenant</span>';
                } else {
                    $badge = '<span class="badge badge-primary">Resident</span>';
                }

                echo '<tr>';
                echo '<td style="vertical-align: middle;"><strong>'.$fullname.'</strong></td>';
                echo '<td style="vertical-align: middle;">'.$row['age'].'</td>';
                echo '<td style="vertical-align: middle;">'.$badge.'</td>'; // Display Badge
                echo '<td style="vertical-align: middle;">'.$row['gender'].'</td>';
                echo '<td style="vertical-align: middle;">
                        <button class="btn btn-sm '.$btn_class.' toggle-status-btn" 
                          style="width: 110px;"
                          data-id="'.$row['residence_id'].'" 
                          data-status="'.$status.'">
                          '.$btn_icon.' '.$status.'
                        </button>
                      </td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5" class="text-center text-muted">No active members found with surname: <strong>'.htmlspecialchars($surname).'</strong></td></tr>';
        }
        echo '</tbody></table></div>';
    } else {
        echo '<div class="alert alert-danger">Database Error: ' . $con->error . '</div>';
    }
} else {
    echo '<div class="alert alert-warning">No surname provided.</div>';
}
?>