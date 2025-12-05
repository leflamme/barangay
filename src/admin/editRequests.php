<?php 
session_start();
include_once '../connection.php';

try{
  if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){
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
  <title>Edit Requests</title>
<!-- Web Icon Logo -->
<link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg"/>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
  <link rel="stylesheet" href="../assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/dist/css/admin.css">

  <style>
    /* ========== PAGE SPECIFIC STYLES ========== */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #ffffff;
    }
    .wrapper, .content-wrapper, .main-footer, .content, .content-header {
      background-color: #ffffff !important;
      color: #050C9C;
    }
    /* Navbar */
    .main-header.navbar {
      background-color: #050C9C !important;
      border-bottom: none;
    }
    .navbar .nav-link, .navbar .nav-link:hover {
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
    .sidebar .nav-link.active, .sidebar .nav-link:hover {
      background-color: #3572EF !important;
      color: #ffffff !important;
    }
    .sidebar .nav-icon {
      color: #3ABEF9 !important;
    }
    /* Card & Table */
    .card {
        background-color: #ffffff;
        border-color: #A7E6FF !important;
        color: #050C9C;
        border-radius: 12px;
    }
    .card-header {
      background-color: #050C9C !important;
      color: #fff !important;
      border-bottom: 2px solid #3572EF;
      border-radius: 10px 10px 0 0;
    }
    .card-header h3 {
        color: #ffffff;
    }
    #requestsTable thead {
      background-color: #050C9C;
      color: #ffffff;
    }
    #requestsTable tbody tr {
      background-color: #FAF9F6;
      color: #000;
    }
    #requestsTable tbody tr:hover {
      background-color: #e9ecef;
    }
    /* Action Buttons */
    .btn-success {
      background-color: #2E8B57 !important;
      border-color: #2E8B57 !important;
    }
    .btn-danger {
      background-color: #E41749 !important;
      border-color: #E41749 !important;
    }
  </style>

</head>
<body class="hold-transition  sidebar-mini   layout-footer-fixed">
<div class="wrapper">

   <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble " src="../assets/dist/img/loader.gif" alt="AdminLTELogo" height="70" width="70">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
      <li class="nav-item">
        <h5><a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></h5>
      </li>
      <li class="nav-item d-none d-sm-inline-block" style="font-variant: small-caps;">
        <h5 class="nav-link text-white"><?= htmlspecialchars($barangay) ?></h5>
      </li>
      <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
      <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= htmlspecialchars($zone) ?></h5></li>
      <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
      <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= htmlspecialchars($district) ?></h5></li>
    </ul>

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
                <h3 class="dropdown-item-title py-3">
                  <?= htmlspecialchars(ucfirst($first_name_user) .' '. ucfirst($last_name_user)) ?>
                </h3>
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

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>

          <li class="nav-item">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-users-cog"></i><p>Barangay Official<i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="newOfficial.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>New Official</p></a></li>
              <li class="nav-item"><a href="allOfficial.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>List of Official</p></a></li>
              <li class="nav-item"><a href="officialEndTerm.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>Official End Term</p></a></li>
              <li class="nav-item"><a href="position.php" class="nav-link"><i class="nav-icon fas fa-user-tie"></i><p>Position</p></a></li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Residence<i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="newResidence.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>New Residence</p></a></li>
              <li class="nav-item"><a href="allResidence.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>All Residence</p></a></li>
              <li class="nav-item"><a href="archiveResidence.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>Archive Residence</p></a></li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-user-shield"></i><p>Users<i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="usersResident.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>Resident</p></a></li>
              <li class="nav-item"><a href="editRequests.php" class="nav-link bg-indigo"><i class="fas fa-circle nav-icon text-red"></i><p>Edit Requests</p></a></li>
              <li class="nav-item"><a href="userAdministrator.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>Administrator</p></a></li>
            </ul>
          </li>
          <li class="nav-item"><a href="report.php" class="nav-link"><i class="nav-icon fas fa-bookmark"></i><p>Masterlist Report</p></a></li>
          <li class="nav-item"><a href="requestCertificate.php" class="nav-link"><i class="nav-icon fas fa-certificate"></i><p>Certificate</p></a></li>
          <li class="nav-item"><a href="blotterRecord.php" class="nav-link"><i class="nav-icon fas fa-clipboard"></i><p>Blotter Record</p></a></li>
          <li class="nav-item"><a href="systemLog.php" class="nav-link"><i class="nav-icon fas fa-history"></i><p>System Logs</p></a></li>
          
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Resident Edit Requests</h1>
          </div>
        </div></div></div>
    <section class="content">
      <div class="container-fluid">
           
            <div class="card">
              <div class="card-header border-transparent">
                <h3 class="card-title">Pending Requests</h3>
              </div>
              <div class="card-body ">
                <table class="table table-striped table-hover " id="requestsTable">             
                  <thead>
                    <tr>
                      <th>Resident Name</th>
                      <th>Date Requested</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          
      </div></section>
    </div>
  <footer class="main-footer">
  <strong>&copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')) ?></strong>
  <div class="float-right d-none d-sm-inline-block">
  </div>
</footer>
</div>
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>

<script>
  $(document).ready(function(){

    // Initialize the DataTable
    var requestsTable = $("#requestsTable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        order: [[1, 'desc']], // Order by date requested
        ajax: {
            url: 'editRequestsTable.php',
            type: 'POST',
        },
        columnDefs:[
            { targets: 3, orderable: false } // Action column
        ]
    });

    // Handle APPROVE button click
    $('#requestsTable').on('click', '.approveRequest', function() {
        var request_id = $(this).data('id');
        processRequest(request_id, 'approve');
    });

    // Handle DENY button click
    $('#requestsTable').on('click', '.denyRequest', function() {
        var request_id = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This will deny the request and the resident will have to ask again.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, deny it!'
        }).then((result) => {
            if (result.value) {
                processRequest(request_id, 'deny');
            }
        })
    });

    // Function to process the request
    function processRequest(request_id, action) {
        $.ajax({
            url: 'processEditRequest.php',
            type: 'POST',
            data: {
                request_id: request_id,
                action: action
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire(
                        'Success!',
                        response.message,
                        'success'
                    );
                    requestsTable.ajax.reload(); // Refresh the table
                } else {
                    Swal.fire(
                        'Error!',
                        response.message,
                        'error'
                    );
                }
            },
            error: function() {
                Swal.fire(
                    'Error!',
                    'Something went wrong with the AJAX request.',
                    'error'
                );
            }
        });
    }

  });
</script>
</body>
</html>