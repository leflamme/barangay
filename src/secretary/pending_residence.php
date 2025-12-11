<?php
session_start();
include_once '../connection.php'; // Adjusted path assuming this is in secretary/ folder

try {
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'secretary') {
        $user_id = $_SESSION['user_id'];
        $sql_user = "SELECT * FROM `users` WHERE `id` = ?";
        $stmt_user = $con->prepare($sql_user) or die($con->error);
        $stmt_user->bind_param('s', $user_id);
        $stmt_user->execute();
        $row_user = $stmt_user->get_result()->fetch_assoc();
        $first_name_user = $row_user['first_name'];
        $last_name_user = $row_user['last_name'];
        $user_image = $row_user['image'];

        $sql = "SELECT * FROM pending_residents ORDER BY date_submitted DESC";
        $result = $con->query($sql);

        // Fetch Barangay Info
        $row_brg = $con->query("SELECT * FROM `barangay_information`")->fetch_assoc();
        $barangay = $row_brg['barangay'];
        $zone = $row_brg['zone']; 
        $district = $row_brg['district'];
    } else {
        echo '<script>window.location.href = "../login.php";</script>';
        exit();
    }
} catch (Exception $e) { echo $e->getMessage(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pending Residents</title>
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
</head>
<body class="hold-transition sidebar-mini layout-footer-fixed">
<div class="wrapper">
  
  <?php include 'sidebar_template.php'; // simplified for this view ?>

  <div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Pending Residents</h1></div></section>
    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header"><h3 class="card-title">List of Pending Residents</h3></div>
          <div class="card-body">
            <table class="table table-striped table-hover" id="pendingResidentsTable">
              <thead>
                <tr>
                  <th>Pending ID</th>
                  <th>Full Name</th>
                  <th>Household Action</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while($row = $result->fetch_assoc()): 
                  $full_name = $row['last_name'].', '.$row['first_name'];
                  // Show if they want to Join or Create
                  $hh_status = ($row['household_action'] == 'join') ? '<span class="badge badge-info">JOIN HH #'.$row['target_household_id'].'</span>' : '<span class="badge badge-success">NEW HOUSEHOLD</span>';
                ?>
                  <tr id="row-<?= $row['pending_id'] ?>">
                    <td><?= $row['pending_id'] ?></td>
                    <td><?= $full_name ?></td>
                    <td><?= $hh_status ?></td>
                    <td><?= date('M d, Y', strtotime($row['date_submitted'])) ?></td>
                    <td>
                        <button class="btn btn-info btn-sm view-btn" data-id="<?= $row['pending_id'] ?>">View</button>
                        <button class="btn btn-success btn-sm approve-btn" data-id="<?= $row['pending_id'] ?>">Approve</button>
                        <button class="btn btn-danger btn-sm reject-btn" data-id="<?= $row['pending_id'] ?>">Reject</button>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<div class="modal fade" id="viewResidentModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-body" id="residentDetails"></div></div></div></div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function(){
  $("#pendingResidentsTable").DataTable({ "order": [[ 3, "desc" ]] });

  // View Logic
  $('.view-btn').on('click', function(){
    var pending_id = $(this).data('id');
    $('#viewResidentModal').modal('show');
    $.post('view_pending_ajax.php', {pending_id: pending_id}, function(res){ $('#residentDetails').html(res); });
  });

  // Reject Logic
  $('.reject-btn').on('click', function(){
    var pending_id = $(this).data('id');
    if(confirm('Reject this resident?')){
        $.post('reject_pending_ajax.php', {pending_id: pending_id}, function(res){ 
            alert(res); location.reload(); 
        });
    }
  });

  // --- THE MASTER APPROVAL CHAIN ---
  $('.approve-btn').on('click', function(){
    var pending_id = $(this).data('id'); 
    
    Swal.fire({
        title: 'Approve Resident?',
        text: "This will create/join household, fix location, assign evacuation, and send email.",
        type: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Yes, Approve'
    }).then((result) => {
        if (result.value) {
            
            // 1. MIGRATE DATA
            Swal.fire({ title: 'Step 1/4', html: 'Migrating data & Creating Household...', onBeforeOpen: () => { Swal.showLoading() } });
            
            $.ajax({
                url: 'approve_pending_ajax.php', // Same folder
                type: 'POST',
                data: { pending_id: pending_id },
                dataType: 'json',
                success: function(res) {
                    if(res.status !== 'success'){ Swal.fire('Error', res.message, 'error'); return; }
                    var final_id = res.new_id; 

                    // 2. FIX LOCATION
                    Swal.fire({ title: 'Step 2/4', html: 'Geocoding Address...', onBeforeOpen: () => { Swal.showLoading() } });
                    $.post('../signup/fix_locations.php', { request: final_id }, function() { // Note path: ../signup/
                        
                        // 3. ASSIGN EVACUATION
                        Swal.fire({ title: 'Step 3/4', html: 'Assigning Safety Center...', onBeforeOpen: () => { Swal.showLoading() } });
                        $.post('../signup/assign_residents.php', { request: final_id }, function() { // Note path: ../signup/

                            // 4. SEND EMAIL
                            Swal.fire({ title: 'Step 4/4', html: 'Sending Welcome Email...', onBeforeOpen: () => { Swal.showLoading() } });
                            $.post('send_approval_email.php', { user_id: final_id }, function() { // Same folder
                                
                                Swal.fire('Success!', 'Resident Approved Completely.', 'success').then(() => { location.reload(); });
                            });
                        });
                    });
                },
                error: function() { Swal.fire('Error', 'Server Error in Approval.', 'error'); }
            });
        }
    });
  });
});
</script>
</body>
</html>