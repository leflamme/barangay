<?php
// get_evacuation_family.php
include_once '../connection.php';

// Check if surname is provided (sent from the button in the template)
if(isset($_POST['surname'])) {
    $surname = $_POST['surname'];

    // We select all residents with this Last Name
    // We also LEFT JOIN with evacuation_status to see if they have arrived
    $sql = "SELECT r.residence_id, r.first_name, r.middle_name, r.last_name, r.age, r.gender, r.contact_number,
            COALESCE(es.status, 'Missing') as status
            FROM residence_information r
            LEFT JOIN evacuation_status es ON r.residence_id = es.residence_id
            WHERE r.last_name = ?
            ORDER BY r.age DESC"; // Show oldest (likely parents) first
            
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $surname);
    $stmt->execute();
    $result = $stmt->get_result();

    // Generate the HTML Table to display inside the Modal
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover">';
    echo '<thead class="bg-light">
            <tr>
                <th>Full Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Status</th>
            </tr>
          </thead>';
    echo '<tbody>';

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $fullname = ucfirst($row['first_name']) . ' ' . ucfirst($row['middle_name']) . ' ' . ucfirst($row['last_name']);
            $status = $row['status'];
            
            // Logic: Green button if Arrived, Red if Missing
            $btn_class = ($status == 'Arrived') ? 'btn-success' : 'btn-danger';
            $btn_icon = ($status == 'Arrived') ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';

            echo '<tr>';
            echo '<td style="vertical-align: middle;">
                    <strong>'.$fullname.'</strong><br>
                    <small class="text-muted"><i class="fas fa-phone"></i> '.$row['contact_number'].'</small>
                  </td>';
            echo '<td style="vertical-align: middle;">'.$row['age'].'</td>';
            echo '<td style="vertical-align: middle;">'.$row['gender'].'</td>';
            echo '<td style="vertical-align: middle;">
                    <button class="btn btn-sm '.$btn_class.' toggle-status-btn" 
                      style="width: 100px;"
                      data-id="'.$row['residence_id'].'" 
                      data-status="'.$status.'">
                      '.$btn_icon.' '.$status.'
                    </button>
                  </td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="4" class="text-center">No members found with this surname.</td></tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}
?>