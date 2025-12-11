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
        // --- PREPARE DATA ---
        $full_name = htmlspecialchars($row['first_name'] ?? '') . ' ' . 
                     htmlspecialchars($row['middle_name'] ?? '') . ' ' . 
                     htmlspecialchars($row['last_name'] ?? '') . ' ' . 
                     htmlspecialchars($row['suffix'] ?? '');
                     
        $hh_action = $row['household_action'] ?? 'N/A';
        $hh_target = $row['target_household_id'] ?? 'None';
        $hh_display = "Resident Account Request Approval (" . (($hh_action === 'new') ? "New Household" : "Join Request") . ")";

        echo '<div class="table-responsive"><table class="table table-bordered">';
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
        echo '<tr><th>Date Submitted</th><td>'.date('M d, Y h:i A', strtotime($row['date_submitted'])).'</td></tr>';
        echo '</table></div>';

        echo '<div class="row mt-3">';
        
        // --- 1. PROFILE PICTURE (File Based) ---
        // This still uses the folder system (since we didn't change this part to BLOB yet)
        if(!empty($row['image_name'])){
            echo '<div class="col-md-6 text-center">';
            echo '<h6>Profile Picture</h6>';
            echo '<img src="../permanent-data/residence_photos/'.htmlspecialchars($row['image_name']).'" 
                 alt="Profile Photo" class="img-thumbnail" style="max-height:200px;">';
            echo '</div>';
        }

        // --- 2. VALID ID (Dual Method: BLOB or File) ---
        echo '<div class="col-md-6 text-center">';
        echo '<h6>Valid ID Submitted</h6>';
        
        if (!empty($row['valid_id_blob'])) {
            // METHOD A: Database BLOB (The new Railway-safe way)
            $blob_data = base64_encode($row['valid_id_blob']);
            echo '<img src="data:image/jpeg;base64,'.$blob_data.'" alt="Valid ID (DB)" class="img-thumbnail" style="max-height:200px;">';
            echo '<br><small class="text-success">Source: Database Secure Storage</small>';
            
        } elseif (!empty($row['valid_id_name'])) {
            // METHOD B: Old File Path (Fallback for older records)
            echo '<img src="../permanent-data/residence_photos/'.htmlspecialchars($row['valid_id_name']).'" 
                 alt="Valid ID (File)" class="img-thumbnail" style="max-height:200px;">';
            echo '<br><small class="text-muted">Source: File System</small>';
            
        } else {
            // No Image Found
            echo '<div class="alert alert-warning p-2 mt-2">No Valid ID Uploaded</div>';
        }
        
        echo '</div>'; // End Col
        echo '</div>'; // End Row

        // Buttons
        echo '<div class="mt-4 text-right border-top pt-3">';
        echo '<button class="btn btn-success approve-btn" data-id="'.$row['pending_id'].'"> <i class="fas fa-check"></i> Approve</button> ';
        echo '<button class="btn btn-danger reject-btn" data-id="'.$row['pending_id'].'"> <i class="fas fa-times"></i> Reject</button>';
        echo '</div>';

    } else {
        echo '<div class="alert alert-danger">Resident record not found.</div>';
    }
}
?>