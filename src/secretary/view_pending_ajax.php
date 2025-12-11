<?php
include_once '../connection.php';

if(isset($_POST['pending_id'])){
    $pending_id = $_POST['pending_id'];

    $stmt = $con->prepare("SELECT * FROM pending_residents WHERE pending_id = ?");
    $stmt->bind_param("s", $pending_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if($row){
        echo '<table class="table table-bordered">';
        echo '<tr><th>Pending ID</th><td>'.htmlspecialchars($row['pending_id']).'</td></tr>';
        echo '<tr><th>Full Name</th><td>'.htmlspecialchars($row['first_name'].' '.$row['middle_name'].' '.$row['last_name'].' '.$row['suffix']).'</td></tr>';
        echo '<tr><th>Household ID</th><td>'.htmlspecialchars($row['house_hold_id']).'</td></tr>';
        echo '<tr><th>Gender</th><td>'.htmlspecialchars($row['gender']).'</td></tr>';
        echo '<tr><th>Civil Status</th><td>'.htmlspecialchars($row['civil_status']).'</td></tr>';
        echo '<tr><th>Address</th><td>'.htmlspecialchars($row['house_number'].' '.$row['street']).'</td></tr>';
        echo '<tr><th>Date Submitted</th><td>'.date('M d, Y', strtotime($row['date_submitted'])).'</td></tr>';
        echo '<tr><th>Remarks</th><td>'.htmlspecialchars($row['remarks']).'</td></tr>';
        

      
if(!empty($row['photo'])){
    echo '<tr><th>ID Photo</th><td>
            <img src="../permanent-data/residence_photos/'.htmlspecialchars($row['photo']).'" 
                 alt="Residence Photo" style="max-width:150px; max-height:150px; object-fit:cover; border-radius:10px;">
          </td></tr>';
}

        // Add Approve/Reject buttons inside modal
        // Add Approve/Reject buttons inside modal
echo '<div class="mt-3 text-right">';
echo '<button class="btn btn-success btn-sm approve-btn" data-id="'.$row['pending_id'].'">Approve</button> ';
echo '<button class="btn btn-danger btn-sm reject-btn" data-id="'.$row['pending_id'].'">Reject</button>';
echo '</div>';

    } else {
        echo '<div class="text-danger">Resident not found</div>';
    }
}
?>
