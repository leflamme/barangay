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
        // --- PREPARE DATA (Handle Nulls) ---
        // We use ( ?? '' ) to prevent the "Passing null" error
        $full_name = htmlspecialchars($row['first_name'] ?? '') . ' ' . 
                     htmlspecialchars($row['middle_name'] ?? '') . ' ' . 
                     htmlspecialchars($row['last_name'] ?? '') . ' ' . 
                     htmlspecialchars($row['suffix'] ?? '');
                     
        $hh_action = $row['household_action'] ?? 'N/A';
        $hh_target = $row['target_household_id'] ?? 'None';
        $hh_display = ($hh_action === 'new') ? "Create New Household" : "Join Household #".$hh_target;

        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered">';
        
        // Basic Info
        echo '<tr><th width="30%">Pending ID</th><td>'.htmlspecialchars($row['pending_id'] ?? '').'</td></tr>';
        echo '<tr><th>Full Name</th><td>'.$full_name.'</td></tr>';
        echo '<tr><th>Request Type</th><td><span class="badge badge-primary">'.$hh_display.'</span></td></tr>';
        echo '<tr><th>Relationship</th><td>'.htmlspecialchars($row['relationship_to_head'] ?? '').'</td></tr>';
        
        // Demographics
        echo '<tr><th>Gender</th><td>'.htmlspecialchars($row['gender'] ?? '').'</td></tr>';
        echo '<tr><th>Civil Status</th><td>'.htmlspecialchars($row['civil_status'] ?? '').'</td></tr>';
        echo '<tr><th>Birth Date</th><td>'.htmlspecialchars($row['birth_date'] ?? '').'</td></tr>';
        echo '<tr><th>Address</th><td>'.htmlspecialchars(($row['house_number'] ?? '').' '.($row['street'] ?? '')).'</td></tr>';
        
        // Contact
        echo '<tr><th>Contact #</th><td>'.htmlspecialchars($row['contact_number'] ?? '').'</td></tr>';
        echo '<tr><th>Email</th><td>'.htmlspecialchars($row['email_address'] ?? '').'</td></tr>';
        
        // Submission Info
        echo '<tr><th>Date Submitted</th><td>'.date('M d, Y h:i A', strtotime($row['date_submitted'])).'</td></tr>';

        // --- IMAGES SECTION ---
        echo '</table>';
        echo '</div>';

        echo '<div class="row mt-3">';
        
        // 1. Profile Picture
        if(!empty($row['image_name'])){
            echo '<div class="col-md-6 text-center">';
            echo '<h6>Profile Picture</h6>';
            echo '<img src="../permanent-data/pending_images/'.htmlspecialchars($row['image_name']).'" 
                 alt="Profile Photo" class="img-thumbnail" style="max-height:200px;">';
            echo '</div>';
        }

        // 2. Valid ID Picture (Newly Added)
        if(!empty($row['valid_id_name'])){
            echo '<div class="col-md-6 text-center">';
            echo '<h6>Valid ID Submitted</h6>';
            echo '<img src="../permanent-data/pending_images/'.htmlspecialchars($row['valid_id_name']).'" 
                 alt="Valid ID" class="img-thumbnail" style="max-height:200px;">';
            echo '</div>';
        } else {
            echo '<div class="col-md-6 text-center text-muted"><br>No ID Uploaded</div>';
        }
        
        echo '</div>'; // End Row

        // Approve/Reject Buttons
        echo '<div class="mt-4 text-right border-top pt-3">';
        echo '<button class="btn btn-success approve-btn" data-id="'.$row['pending_id'].'"> <i class="fas fa-check"></i> Approve</button> ';
        echo '<button class="btn btn-danger reject-btn" data-id="'.$row['pending_id'].'"> <i class="fas fa-times"></i> Reject</button>';
        echo '</div>';

    } else {
        echo '<div class="alert alert-danger">Resident record not found.</div>';
    }
}
?>