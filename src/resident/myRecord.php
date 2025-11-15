<?php 
session_start();
include_once '../connection.php';

try{
  if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'resident'){

    $user_id = $_SESSION['user_id'];
    $sql_user = "SELECT * FROM `users` WHERE `id` = ? ";
    $stmt_user = $con->prepare($sql_user) or die ($con->error);
    $stmt_user->bind_param('s',$user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $row_user = $result_user->fetch_assoc();
    $first_name_user = $row_user['first_name'];
    $last_name_user = $row_user['last_name'];
    $user_type = $row_user['user_type'];
    $user_image = $row_user['image'];

    $sql_resident = "SELECT * FROM residence_information WHERE residence_id = '$user_id'";
    $query_resident = $con->query($sql_resident) or die ($con->error);
    $row_resident = $query_resident->fetch_assoc();

    $sql = "SELECT * FROM `barangay_information`";
    $query = $con->prepare($sql) or die ($con->error);
    $query->execute();
    $result = $query->get_result();
    while($row = $result->fetch_assoc()){
        $barangay = $row['barangay'];
        $zone = $row['zone'];
        $district = $row['district'];
        $image = $row['image'];
        $image_path = $row['image_path'];
        $id = $row['id'];
        $postal_address = $row['postal_address'];
    }

  }else{
   echo '<script>
          window.location.href = "../login.php";
        </script>';
  }

}catch(Exception $e){
  echo $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Blotter Record</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="../assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <style>
      body {
      font-family: 'Poppins', sans-serif;
  background-color: #ffffff; /* Changed to white */
}

/* Added for white background */
.wrapper,
.content-wrapper,
.main-footer,
.content,
.content-header {
  background-color: #ffffff !important;
  color: #050C9C;
}
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

    .rightBar:hover{
      border-bottom: 3px solid red;
    }
    
    #barangay_logo{
      height: 150px;
      width:auto;
      max-width:500px;
    }

    .logo{
      height: 150px;
      width:auto;
      max-width:500px;
    }

    .dataTables_wrapper .dataTables_paginate .page-link {  
      border: none;
    }
  
    .dataTables_wrapper .dataTables_paginate .page-item .page-link{
      color: #fff ;
      border-color: transparent;    
    }
 
    .dataTables_wrapper .dataTables_paginate .page-item.active .page-link{
      color: #fff ;
      border: transparent;
      background: none;
      font-weight: bold;
      background-color: #000;
    }
  
    .page-link:focus{
      border-color:#CCC;
      outline:0;
      -webkit-box-shadow:none;
      box-shadow:none;
    }

    .dataTables_length select{
      border: 1px solid #fff;
      border-top: none;
      border-left: none;
      border-right: none;
      cursor: pointer;
      color: #fff;
    }
  
    .dataTables_length span{
      color: #fff;
      font-weight: 500; 
    }

    .last:after{
      display:none;
      width: 70px;
      background-color: black;
      color: #fff;
      text-align: center;
      border-radius: 6px;
      padding: 5px 0;
      position: absolute;
      font-size: 10px;
      z-index: 1;
      margin-left: -20px;
    }
    
    .last:hover:after{
        display: block;
    }
    
    .last:after{
        content: "Last Page";
    } 

    .first:after{
      display:none;
      width: 70px;
      background-color: black;
      color: #fff;
      text-align: center;
      border-radius: 6px;
      padding: 5px 0;
      position: absolute;
      font-size: 10px;
      z-index: 1;
      margin-left: -20px;
    }
    
    .first:hover:after{
        display: block;
    }
    
    .first:after{
        content: "First Page";
    } 

    .last:after{
        content: "Last Page";
    } 

    .next:after{
      display:none;
      width: 70px;
      background-color: black;
      color: #fff;
      text-align: center;
      border-radius: 6px;
      padding: 5px 0;
      position: absolute;
      font-size: 10px;
      z-index: 1;
      margin-left: -20px;
    }
    
    .next:hover:after{
      display: block;
    }
    
    .next:after{
      content: "Next Page";
    } 

    .previous:after{
      display:none;
      width: 80px;
      background-color: black;
      color: #fff;
      text-align: center;
      border-radius: 6px;
      padding: 5px 5px;
      position: absolute;
      font-size: 10px;
      z-index: 1;
      margin-left: -20px;
    }
    
    .previous:hover:after{
        display: block;
    }
    
    .previous:after{
        content: "Previous Page";
    } 
    
    .dataTables_info{
      font-size: 13px;
      margin-top: 8px;
      font-weight: 500;
      color: #fff;
    }
    
    .dataTables_scrollHeadInner, .table{ 
      table-layout: auto;
      width: 100% !important; 
    }

    .select2-container--default .select2-selection--single{
      background-color: transparent;
      height: 38px;
    }
  
    .select2-container--default .select2-selection--single .select2-selection__rendered{
      color: #fff;
    }
  
    #tableRequest_filter{
      display: none;
    }
    
    /* Custom styles for report modal */
    .report-modal .modal-header {
      background-color: #050C9C;
      color: white;
    }
    
    .report-modal .modal-body {
      padding: 20px;
    }
    
    .report-modal .form-group {
      margin-bottom: 1.5rem;
    }
    
    .report-modal label {
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: #050C9C;
    }
    
    .report-modal .btn-primary {
      background-color: #050C9C;
      border-color: #050C9C;
    }
    
    .report-modal .btn-primary:hover {
      background-color: #040a7a;
      border-color: #040a7a;
    }
    
    .report-modal .btn-secondary {
      background-color: #6c757d;
      border-color: #6c757d;
    }
    
    .report-modal .file-input-wrapper {
      position: relative;
      overflow: hidden;
      display: inline-block;
      cursor: pointer;
    }
    
    .report-modal .file-input-wrapper input[type=file] {
      position: absolute;
      left: 0;
      top: 0;
      opacity: 0;
      cursor: pointer;
    }
    
    .report-modal .file-input-label {
      display: inline-block;
      padding: 8px 16px;
      background-color: #e9ecef;
      color: #495057;
      border-radius: 4px;
      border: 1px solid #ced4da;
    }
    
    .report-modal .file-name {
      margin-top: 5px;
      font-size: 0.875rem;
      color: #6c757d;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-footer-fixed">

<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble" src="../assets/dist/img/loader.gif" alt="AdminLTELogo" height="70" width="70">
  </div>

  <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
      <ul class="navbar-nav">
        <li class="nav-item"><h5><a class="nav-link text-white" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></h5></li>
        <li class="nav-item d-none d-sm-inline-block" style="font-variant: small-caps;"><h5 class="nav-link text-white"><?= $barangay ?></h5>
        <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
        <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= htmlspecialchars($zone) ?></h5></li>
        <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
        <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= htmlspecialchars($district) ?></h5></li>
      </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-user"></i></a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="myProfile.php" class="dropdown-item">
            <div class="media">
              <?php if (!empty($user_image)) : ?>
                <img src="<?= '../assets/dist/img/' . htmlspecialchars($user_image) ?>" class="img-size-50 mr-3 img-circle" alt="User Image">
              <?php else: ?>
                <img src="../assets/dist/img/image.png" class="img-size-50 mr-3 img-circle" alt="User Image">
              <?php endif; ?>
              <div class="media-body">
                <h3 class="dropdown-item-title py-3"><?= htmlspecialchars(ucfirst($first_name_user) . ' ' . ucfirst($last_name_user)) ?></h3>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a href="../logout.php" class="dropdown-item dropdown-footer">LOGOUT</a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <!-- Barangay Logo -->
    <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">

    <!-- Sidebar -->
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="personalInformation.php" class="nav-link active"><i class="nav-icon fas fa-address-book"></i><p>Personal Information</p></a></li>
          <li class="nav-item"><a href="drrmPlan.php" class="nav-link"><i class="fas fa-clipboard-list nav-icon text-red"></i><p>Emergency Plan</p></a></li>
          <li class="nav-item"><a href="myRecord.php" class="nav-link"><i class="nav-icon fas fa-server"></i><p>Blotter Record</p></a></li>
          <li class="nav-item"><a href="certificate.php" class="nav-link"><i class="nav-icon fas fa-file-alt"></i><p>Certificate</p></a></li>
          <li class="nav-item"><a href="changePassword.php" class="nav-link"><i class="nav-icon fas fa-lock"></i><p>Change Password</p></a></li>       
        </ul>
      </nav>
    </div>
  </aside>
  <!-- /.sidebar -->
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper"  style="background-color: transparent">
    <!-- Content Header (Page header) -->
  
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content  " >

    <div class="container-fluid pt-5">
          <input type="hidden" value="<?=$user_id; ?>" id="edit_residence_id">
        <div class="card mt-5">
            <div class="card-header">
              <div class="card-title">
                <h4>Record List</h4>
              </div>
              <div class="card-tools">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#reportBlotterModal">
                  <i class="fas fa-plus"></i> Report Blotter
                </button>
                <button type="button" class="btn btn-secondary" id="refreshRecords">
                <i class="fas fa-sync-alt"></i> Refresh
              </button>
              </div>
            </div>

          <div class="card-body">
        
            <table class="table table-striped table-hover" id="myRecordTable" >
              <thead>
                <tr>
                  <th class="d-none test">Color</th>
                  <th>Blotter Number</th>
                  <th>Status</th>
                  <th>Remarks</th>
                  <th>Incident</th>
                  <th>Location of Incident</th>
                  <th>Date Incident</th>
                  <th>Date Reported</th>
                  <th>View</th>
                </tr>
              </thead>
            </table>
           
          </div>
        </div>
          
      </div><!--/. container-fluid -->
     
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 
  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')); ?></strong>
  </footer>

</div>
<!-- ./wrapper -->

<!-- Report Blotter Modal -->
<div class="modal fade report-modal" id="reportBlotterModal" tabindex="-1" role="dialog" aria-labelledby="reportBlotterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reportBlotterModalLabel">Report Blotter</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="reportBlotterForm" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group">
            <label for="personName">Name of Person Being Reported <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="personName" name="personName" required placeholder="Enter full name">
          </div>
          
          <div class="form-group">
            <label for="location">Location of Incident <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="location" name="location" required placeholder="Where did the incident happen?">
          </div>
          
          <div class="form-group">
            <label for="incidentDate">Date and Time of Incident <span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control" id="incidentDate" name="incidentDate" required>
          </div>
          
          <div class="form-group">
            <label for="reason">Reason for Reporting <span class="text-danger">*</span></label>
            <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Briefly describe what happened (e.g., loitering, noise disturbance, etc.)"></textarea>
          </div>
          
          <div class="form-group">
            <label for="justification">Justification of the Report <span class="text-danger">*</span></label>
            <textarea class="form-control" id="justification" name="justification" rows="4" required placeholder="Provide details and justification for your report"></textarea>
          </div>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit Report</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade report-modal" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>I hereby declare that the information provided in this report is true, correct, and up to date to the best of my knowledge.</p>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="truthDeclaration" required>
          <label class="form-check-label" for="truthDeclaration">
            I confirm that the information is true and correct
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmReport">Confirm Report</button>
      </div>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade report-modal" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Report Submitted Successfully</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="text-center">
          <i class="fas fa-check-circle text-success" style="font-size: 48px; margin-bottom: 15px;"></i>
          <p>Your blotter report has been successfully submitted.</p>
          <p>You will be notified once your report has been processed.</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.js"></script>
<script src="../assets/plugins/popper/umd/popper.min.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/plugins/jszip/jszip.min.js"></script>
<script src="../assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="../assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>
<script src="../assets/plugins/select2/js/select2.full.min.js"></script>
<script src="../assets/plugins/moment/moment.min.js"></script>
<script src="../assets/plugins/chart.js/Chart.min.js"></script>

<div id="show_records"></div>

<script>
  $(document).ready(function(){

    // Refresh button functionality
    $('#refreshRecords').on('click', function() {
      $('#myRecordTable').DataTable().ajax.reload();
    });

    // Handle form submission
$('#reportBlotterForm').on('submit', function(e) {
  e.preventDefault();
  
  // Validate required fields
  let isValid = true;
  const requiredFields = ['personName', 'location', 'incidentDate', 'reason', 'justification'];
  
  requiredFields.forEach(field => {
    const element = $(`#${field}`);
    if (!element.val().trim()) {
      isValid = false;
      element.addClass('is-invalid');
    } else {
      element.removeClass('is-invalid');
    }
  });
  
  if (!isValid) {
    Swal.fire({
      title: 'Incomplete Form',
      text: 'Please fill in all required fields.',
      icon: 'warning',
      confirmButtonColor: '#050C9C'
    });
    return;
  }
  
  // Validate date is not in the future
  const incidentDate = new Date($('#incidentDate').val());
  const now = new Date();
  if (incidentDate > now) {
    Swal.fire({
      title: 'Invalid Date',
      text: 'Incident date cannot be in the future.',
      icon: 'warning',
      confirmButtonColor: '#050C9C'
    });
    return;
  }
  
  // Show confirmation modal
  $('#reportBlotterModal').modal('hide');
  $('#confirmationModal').modal('show');
});

// Handle confirmation
$('#confirmReport').on('click', function() {
  if (!$('#truthDeclaration').is(':checked')) {
    Swal.fire({
      title: 'Declaration Required',
      text: 'You must confirm that the information is true and correct.',
      icon: 'warning',
      confirmButtonColor: '#050C9C'
    });
    return;
  }
  
  $('#confirmationModal').modal('hide');
  
  Swal.fire({
    title: 'Submitting Report...',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });
  
  const formData = new FormData($('#reportBlotterForm')[0]);
  
  $.ajax({
    url: 'submitReport.php',
    type: 'POST',
    data: formData, // Fixed AJAX call for missing data property
    processData: false,
    contentType: false,
    success: function(response) {
      Swal.close();
      try {
        const result = JSON.parse(response);
        if (result.success) {
          // Refresh the DataTable
          $('#myRecordTable').DataTable().ajax.reload(null, false);
          
          $('#successModal').modal('show');
          $('#reportBlotterForm')[0].reset();
          $('#fileName').text('No file chosen');
          $('#truthDeclaration').prop('checked', false);
          $('#confirmReport').prop('disabled', true);
        } else {
          Swal.fire({
            title: 'Submission Failed',
            text: result.message || 'An error occurred while submitting your report.',
            icon: 'error',
            confirmButtonColor: '#050C9C'
          });
        }
      } catch (e) {
        console.error('JSON parse error:', e);
        Swal.fire({
          title: 'Submission Failed',
          text: 'Received invalid response from server.',
          icon: 'error',
          confirmButtonColor: '#050C9C'
        });
      }
    },
    error: function(xhr, status, error) {
      Swal.close();
      console.error('AJAX error:', error);
      Swal.fire({
        title: 'Submission Failed',
        text: 'An error occurred while submitting your report. Please try again.',
        icon: 'error',
        confirmButtonColor: '#050C9C'
      });
    }
  });
});

    blotterPersonTable()

    function blotterPersonTable(){
  var edit_residence_id = $("#edit_residence_id").val();
  var blotterPersonTable = $("#myRecordTable").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    order: [[7, 'desc']], // Order by Date Reported (column index 7) descending
    searching: false,
    info: false,
    paging: false,
    lengthChange: false,
    autoWidth: false,
    columnDefs: [
      {
        targets: '_all',
        orderable: false,
      },
      {
        targets: 0,
        className: 'd-none',
      }
    ],
    ajax: {
      url: 'myRecordTable.php',
      type: 'POST',
      data: {
        edit_residence_id: edit_residence_id
      }
    },
    fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      if (aData[0] == "1") {
        $('td', nRow).css('background-color', '#20c997');
      } else {
        $('td', nRow).css('background-color', '#000');
      }
    }
  });
}

    $(document).on('click','.viewRecords', function(){
      var record_id = $(this).attr('id');
      $("#show_records").html('');

      $.ajax({
        url: 'viewRecordsModal.php',
        type: 'POST',
        data: {  // Fixed: added 'data' property
          record_id: record_id,
        },
        cache: false,
        success:function(data){
          $("#show_records").html(data);
          $("#viewBlotterRecordModal").modal('show');
        }
      }).fail(function(){
        Swal.fire({
          title: '<strong class="text-danger">Ooppss..</strong>',
          type: 'error',
          html: '<b>Something went wrong with ajax !<b>',
          width: '400px',
          confirmButtonColor: '#6610f2',
        })
      })
    })
  });
</script>
</body>
</html>