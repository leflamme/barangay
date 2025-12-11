<?php
session_start();
include_once '../connection.php';

try {
    // Check if user is logged in and is a secretary
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'secretary') {

        $user_id = $_SESSION['user_id'];

        // Get user info
        $sql_user = "SELECT * FROM `users` WHERE `id` = ?";
        $stmt_user = $con->prepare($sql_user) or die($con->error);
        $stmt_user->bind_param('s', $user_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        $row_user = $result_user->fetch_assoc();

        $first_name_user = $row_user['first_name'];
        $last_name_user = $row_user['last_name'];
        $user_type = $row_user['user_type'];
        $user_image = $row_user['image'];

        // Fetch pending residents
        // [UPDATED] We need to see the household details if they exist
        $sql = "SELECT * FROM pending_residents ORDER BY date_submitted DESC";
        $stmt = $con->prepare($sql) or die($con->error);
        $stmt->execute();
        $result = $stmt->get_result();

        // Optional: fetch barangay info (if needed)
        $sql_brg = "SELECT * FROM `barangay_information`";
        $query_brg = $con->query($sql_brg) or die($con->error);
        $row_brg = $query_brg->fetch_assoc();
        $barangay = $row_brg['barangay'];
        $zone = $row_brg['zone']; 
        $district = $row_brg['district'];

    } else {
        echo '<script>
                window.location.href = "../login.php";
              </script>';
        exit();
    }

} catch (Exception $e) {
    echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pending Residents</title>
  <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
  <link rel="stylesheet" href="../assets/dist/css/secretary.css?v=2">

   <style>
    /* Navbar */
    .main-header.navbar {
      background-color: #050C9C !important;
      border-bottom: none;
    }

    .navbar .nav-link,
    .navbar .nav-link:hover {
      color: #ffffff !important;
    }

    /* Sidebar */
    .main-sidebar {
      background-color: #050C9C !important;
    }

    .brand-link {
      background-color: transparent !important;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar .nav-link {
      color: #A7E6FF !important;
      transition: all 0.3s;
    }

    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
      background-color: #3572EF !important;
      color: #ffffff !important;
    }

    .sidebar .nav-icon {
      color: #3ABEF9 !important;
    }

    .dropdown-menu {
      border-radius: 10px;
      border: none;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .dropdown-item {
      font-weight: 600;
      transition: 0.2s ease-in-out;
    }

    .dropdown-item:hover {
      background-color: #F5587B;
      color: white;
    }


    .nav-tabs .nav-link-dark {
      color: #003366 !important;
      background-color: #A7E6FF !important;
      border: 1px solid #3572EF;
      font-weight: bold;
      transition: background-color 0.3s ease, color 0.3s ease;
      border-radius: 5px 5px 0 0;
    }

    .nav-tabs .nav-link-dark.active {
      color: #fff !important;
      background-color: #050C9C !important;
      border-color: #050C9C #050C9C #fff;
    }

    .nav-tabs .nav-link-dark:hover {
      background-color: #3ABEF9 !important;
      color: #fff !important;
    }

    .card {
      border: 1px solid #3ABEF9;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(5, 12, 156, 0.1);
    }

    .card-body {
      background-color: #ffffff;
      border-radius: 10px;
    }

    fieldset {
      border: 2px solid #050C9C !important;
      border-radius: 10px;
      padding: 1em;
      background-color: #F2F6FF;
    }

    legend {
      font-size: 1.1em;
      font-weight: bold;
      color: #050C9C;
      padding: 0 10px;
      border-bottom: none;
      width: auto;
    }

    table thead {
      background: linear-gradient(to right, #050C9C, #3572EF);
      color: #fff;
      font-weight: 600;
    }

    .table-hover tbody tr:hover {
      background-color: #A7E6FF;
    }

    #position {
      background-color: #ffffff;
      border: 1px solid #3ABEF9;
      border-radius: 5px;
      color: #050C9C;
      font-weight: 500;
    }

    .dataTables_wrapper .dataTables_paginate .page-link {
      background-color: #3ABEF9 !important;
      color: #000 !important;
      border: 1px solid #A7E6FF !important;
      border-radius: 5px !important;
      margin: 0 2px;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
      background-color: #050C9C !important;
      color: #fff !important;
      border-color: #050C9C !important;
    }

    .dataTables_wrapper .dataTables_paginate .page-item .page-link:hover {
      background-color: #3572EF !important;
      color: #fff !important;
    }

    .dataTables_length select {
      background: #ffffff;
      border: 1px solid #3ABEF9;
      color: #050C9C;
    }

    .dataTables_info {
      color: #050C9C;
      font-weight: 500;
      font-size: 13px;
    }

    /* Table Background and Text */
    #officialTable {
      background-color: #ffffff;
      color: #000;
    }

    #officialTable thead {
      background-color: #050C9C;
      color: #ffffff;
    }

    #officialTable tbody tr:hover {
      background-color: rgb(0, 71, 171);
    }

    /* DataTables Search Field */
    .dataTables_filter label,
    .dataTables_length label {
      color: #000;
      font-weight: 500;
    }

    .dataTables_filter input[type="search"] {
      background-color: #ffffff !important;
      color: #000000 !important;
      border: 1px solid #ccc !important;
      border-radius: 6px;
      padding: 6px 10px !important;
    }

    .select2-container--default .select2-selection--single {
      background-color: #ffffff !important;
      color: #000 !important;
      border: 1px solid #ccc !important;
    }

    @media (max-width: 768px) {
      .table-responsive {
        overflow-x: auto;
      }
    }
  </style>
  
</head>
<body class="hold-transition sidebar-mini layout-footer-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand dark-mode">
    <ul class="navbar-nav">
      <li class="nav-item">
        <h5><a class="nav-link text-white" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></h5>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <h5 class="nav-link text-white"><?= $barangay ?> - <?= $zone ?> - <?= $district ?></h5>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-user"></i></a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="myProfile.php" class="dropdown-item">
            <div class="media">
              <?php 
                $img = (!empty($user_image)) ? $user_image : 'image.png';
                echo '<img src="../assets/dist/img/'.$img.'" class="img-size-50 mr-3 img-circle" alt="User Image">';
              ?>
              <div class="media-body">
                <h3 class="dropdown-item-title py-3"><?= ucfirst($first_name_user) .' '. ucfirst($last_name_user) ?></h3>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a href="../logout.php" class="dropdown-item dropdown-footer">LOGOUT</a>
        </div>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar elevation-4 sidebar-no-expand dark-mode">

    <img src="../assets/logo/ksugan.jpg" alt="Logo" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin:10px auto; display:block;">
    <div class="sidebar">

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users-cog"></i>
              <p>Barangay Official <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="allOfficial.php" class="nav-link">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>List of Official</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="officialEndTerm.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Official End Term</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="pending_residence.php" class="nav-link bg-indigo">
               <i class="nav-icon fas fa-check-circle"></i>
              <p>Approval of residents</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link ">
              <i class="nav-icon fas fa-users"></i>
              <p>Residence <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="newResidence.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>New Residence</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="allResidence.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>All Residence</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="archiveResidence.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Archive Residence</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item ">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>Users <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="usersResident.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Resident</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="editRequests.php" class="nav-link">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Edit Requests</p>
                </a>
              </li>
            </ul>
          </li>
       
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-exclamation-triangle"></i>
              <p>DRRM <i class="right fas fa-angle-left"></i></p>
            </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="drrmEvacuation.php" class="nav-link">
                    <i class="fas fa-house-damage nav-icon text-red"></i>
                    <p>Evacuation Center</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="report.php" class="nav-link">
                    <i class="nav-icon fas fa-bookmark"></i>
                    <p>Masterlist Report</p>
                  </a>
                </li>
              </ul>
          </li>
          <li class="nav-item ">
            <a href="requestCertificate.php" class="nav-link">
              <i class="nav-icon fas fa-certificate"></i>
              <p>Certificate</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="blotterRecord.php" class="nav-link">
              <i class="nav-icon fas fa-clipboard"></i>
              <p>Blotter Record</p>
            </a>
          </li>
         
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid"><h1>Pending Residents</h1></div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header border-transparent">
            <h3 class="card-title">List of Pending Residents</h3>
          </div>
          <div class="card-body">
            <table class="table table-striped table-hover" id="pendingResidentsTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Full Name</th>
                  <th>Household Request</th> <th>Submitted Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while($row = $result->fetch_assoc()): 
                  $full_name = $row['last_name'].', '.$row['first_name'].' '.$row['middle_name'];
                  
                  // LOGIC: Display if they want to join or create
                  $hh_status = '';
                  if ($row['household_action'] == 'join') {
                      $hh_status = '<span class="badge badge-info">JOIN HH #'.$row['target_household_id'].'</span>';
                  } else if ($row['household_action'] == 'new') {
                      $hh_status = '<span class="badge badge-success">NEW HOUSEHOLD</span>';
                  } else {
                      $hh_status = '<span class="badge badge-secondary">Pending</span>';
                  }
                ?>
                  <tr id="row-<?= $row['pending_id'] ?>">
                    <td><?= $row['pending_id'] ?></td>
                    <td><?= $full_name ?></td>
                    <td><?= $hh_status ?></td> <td><?= date('M d, Y', strtotime($row['date_submitted'])) ?></td>
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

  <footer class="main-footer">
    <strong>&copy; <?= date("Y") ?></strong>
  </footer>
</div>


<div class="modal fade" id="viewResidentModal" tabindex="-1" aria-labelledby="viewResidentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="viewResidentModalLabel">Resident Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="residentDetails">
        <div class="text-center">
          <i class="fas fa-spinner fa-spin fa-2x"></i>
          <p>Loading...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script> <script>
$(document).ready(function(){
  $("#pendingResidentsTable").DataTable({
    responsive: true,
    autoWidth: false,
    order: [[3,'desc']], // Order by Date
  });

  // Open modal on View button click
  $('.view-btn').on('click', function(){
    var pending_id = $(this).data('id');
    $('#viewResidentModal').modal('show');
    $('#residentDetails').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading...</p></div>');

    $.ajax({
      url: 'view_pending_ajax.php',
      method: 'POST',
      data: {pending_id: pending_id},
      success: function(response){
        $('#residentDetails').html(response);
      },
      error: function(){
        $('#residentDetails').html('<div class="text-danger text-center">Failed to load data</div>');
      }
    });
  });

  // Reject Logic
  $(document).on('click', '.reject-btn', function(){
      var pending_id = $(this).data('id');
      
      Swal.fire({
          title: 'Reject Resident?',
          text: "You won't be able to revert this!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, reject it!'
      }).then((result) => {
          if (result.value) {
              $.post('reject_pending_ajax.php', {pending_id: pending_id}, function(response){
                  Swal.fire('Rejected!', response, 'success');
                  $('#viewResidentModal').modal('hide');
                  $('#row-' + pending_id).fadeOut();
              });
          }
      });
  });

  // --- THE NEW ADVANCED APPROVAL CHAIN ---
  $(document).on('click', '.approve-btn', function(){
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
              
              // 1. MIGRATE DATA (Create Households, Move to Active)
              Swal.fire({ 
                  title: 'Step 1/4', 
                  html: 'Migrating data & Creating Household...', 
                  allowOutsideClick: false,
                  showConfirmButton: false,
                  onBeforeOpen: () => { Swal.showLoading() } 
              });
              
              $.ajax({
                  url: 'approve_pending_ajax.php', 
                  type: 'POST',
                  data: { pending_id: pending_id },
                  dataType: 'json', // Expect JSON response
                  success: function(res) {
                      
                      if(res.status !== 'success'){ 
                          Swal.fire('Error', res.message, 'error'); 
                          return; 
                      }
                      
                      var final_id = res.new_id; 

                      // 2. FIX LOCATION (Run map script)
                      Swal.fire({ 
                          title: 'Step 2/4', 
                          html: 'Geocoding Address...', 
                          allowOutsideClick: false,
                          onBeforeOpen: () => { Swal.showLoading() } 
                      });

                      // Note: Updated path to ../signup/
                      $.post('../signup/fix_locations.php', { request: final_id }, function() { 
                          
                          // 3. ASSIGN EVACUATION (Run safety script)
                          Swal.fire({ 
                              title: 'Step 3/4', 
                              html: 'Assigning Safety Center...', 
                              allowOutsideClick: false,
                              onBeforeOpen: () => { Swal.showLoading() } 
                          });

                          // Note: Updated path to ../signup/
                          $.post('../signup/assign_residents.php', { request: final_id }, function() { 

                              // 4. SEND EMAIL
                              Swal.fire({ 
                                  title: 'Step 4/4', 
                                  html: 'Sending Welcome Email...', 
                                  allowOutsideClick: false,
                                  onBeforeOpen: () => { Swal.showLoading() } 
                              });

                              $.post('send_approval_email.php', { user_id: final_id }, function() { 
                                  
                                  Swal.fire({
                                      title: 'Success!',
                                      text: 'Resident has been fully approved and notified.',
                                      type: 'success'
                                  }).then(() => { 
                                      location.reload(); 
                                  });
                              });
                          });
                      });
                  },
                  error: function(xhr, status, error) { 
                      console.log(xhr.responseText); // For debugging
                      Swal.fire('Error', 'Server Error in Approval. Check console.', 'error'); 
                  }
              });
          }
      });
  });

});
</script>
</body>
</html>