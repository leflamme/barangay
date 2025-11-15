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
  <title>List: Requested Certificates</title>
  <!-- Website Logo -->
  <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">

  <!-- Google Fonts DONT FORGET-->
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
  <!-- DONT FORGET -->
  <link rel="stylesheet" href="../assets/dist/css/admin.css">

  <style>

    
  
/* === White Background and Color Palette Integration === */

body,
.content-wrapper,
.card,
.nav-tabs,
.table,
select.form-control-sm,
input.form-control,
.card-body {
  background-color: #fff !important;
  color: #050C9C !important;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);

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

/* Card border and tab accent */
.card.card-indigo.card-tabs {
  border-top: 4px solid #3572EF;
}

/* Nav Tabs */
.nav-tabs .nav-link.active {
  background-color: #050C9C !important;
  color: #fff !important;
  font-weight: bold;
}

.nav-tabs .nav-link {
  background-color: #A7E6FF;
  color: #050C9C;
  font-weight: 500;
  border: none;
}

.nav-tabs .nav-link:hover {
  background-color: #3ABEF9;
  color: #fff;
}

/* Search Bar */
.input-group-text.bg-indigo {
  background-color: #050C9C !important;
  color: #fff;
}

#reset.input-group-text {
  background-color: #E41749 !important;
  color: #fff;
}

#searching {
  background-color: #fff;
  color: #050C9C;
}

/* Table header */
.table thead th {
  background-color: #050C9C;
  color: #fff;
  text-align: center;
}

/* Table row hover */
.table-hover tbody tr:hover {
  background-color: #A7E6FF;
}

/* Dropdown selects */
select.form-control-sm {
  background-color: #fff;
  border: 1px solid #3572EF;
  color: #050C9C;
}

/* Pagination */
.dataTables_wrapper .dataTables_paginate .page-link {
  background-color: #FFF591;
  color: #050C9C !important;
  border: 1px solid #3ABEF9;
}

.dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
  background-color: #E41749 !important;
  color: #fff !important;
  font-weight: bold;
}

  .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
    background-color: #3572EF !important;
    font-weight: bold;
  }

  .dataTables_length select {
    background: #ffffff;
    border: 1px solid #3ABEF9;
    color: #050C9C;
  }

  /* Pagination buttons (<< < > >>) COPY THIS */
.dataTables_wrapper .dataTables_paginate .page-item .page-link {
  background-color: #3ABEF9 !important; /* bright sky blue */
  color: #000 !important;
  border: 1px solid #A7E6FF !important;
  border-radius: 5px !important;
  margin: 0 2px;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.dataTables_info {
  color: #050C9C;
  font-weight: 500;
}

/* Tooltip arrows */
.first:after,
.last:after,
.next:after,
.previous:after {
  background-color: #050C9C;
  color: #fff;
}

/* Responsive styling */
@media (max-width: 767.98px) {
  .input-group-text {
    font-size: 12px;
    padding: 6px 10px;
  }

  #searching {
    font-size: 14px;
  }

  .nav-tabs .nav-link {
    font-size: 13px;
    padding: 6px;
  }

  select.form-control-sm {
    font-size: 12px;
  }

  .table thead th {
    font-size: 13px;
  }

  .badge.bg-lime {
    font-size: 11px;
  }


}

/* Table base */
#certificateTable {
  background-color: #FAF9F6; /* off-white background */
  color: #000;
}

/* Table header */
#certificateTable thead {
  background-color: #050C9C; /* deep blue header */
  color: #ffffff;
  font-weight: 600;
}

/* Table body rows (odd and even same bg as archiveResidence) */
#certificateTable tbody tr:nth-of-type(odd) {
  background-color: #FAF9F6; /* light gray */
}

#certificateTable tbody tr:nth-of-type(even) {
  background-color: #FAF9F6; /* white */
}

/* Hover effect */
#certificateTable tbody tr:hover {
  background-color: #0047ab; 
  color: #fff;
}



div.dataTables_filter {
  display: none !important;
}

  </style>
 
 
</head>
<body class="hold-transition  sidebar-mini   ">
<div class="wrapper">

<!-- Preloader -->
<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble " src="../assets/dist/img/loader.gif" alt="AdminLTELogo" height="70" width="70">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
   <!-- Left navbar links (COPY LEFT ONLY)  -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <h5><a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></h5>
    </li>
    <li class="nav-item d-none d-sm-inline-block" style="font-variant: small-caps;">
      <h5 class="nav-link text-white"><?= $barangay ?></h5>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <h5 class="nav-link text-white">-</h5>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <h5 class="nav-link text-white"><?= $zone ?></h5>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <h5 class="nav-link text-white">-</h5>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <h5 class="nav-link text-white"><?= $district ?></h5>
    </li>
  </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      <!-- profile_dropdown.php (COPY THIS) -->
<li class="nav-item dropdown">
  <a class="nav-link" data-toggle="dropdown" href="#">
    <i class="far fa-user"></i>
  </a>
  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
    <a href="myProfile.php" class="dropdown-item">
      <div class="media">
        <?php 
          if(!empty($user_image)){
            echo '<img src="../assets/dist/img/'.$user_image.'" class="img-size-50 mr-3 img-circle" alt="User Image">';
          } else {
            echo '<img src="../assets/dist/img/image.png" class="img-size-50 mr-3 img-circle" alt="User Image">';
          }
        ?>
        <div class="media-body">
          <h3 class="dropdown-item-title py-3">
            <?= ucfirst($first_name_user) .' '. ucfirst($last_name_user) ?>
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

  <!-- Main Sidebar Container (COPY THIS ASIDE TO ASIDE) -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
  <!-- Brand Logo -->
    <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users-cog"></i>
              <p>
              Barangay Official
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="newOfficial.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>New Official</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="allOfficial.php" class="nav-link">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>List of Official</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="officialEndTerm.php" class="nav-link">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Official End Term</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link ">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Residence
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="newResidence.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>New Residence</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="allResidence.php" class="nav-link bg">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>All Residence</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="archiveResidence.php" class="nav-link">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Archive Residence</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item ">
            <a href="requestCertificate.php" class="nav-link bg-indigo">
              <i class="nav-icon fas fa-certificate"></i>
              <p>
                Certificate
              </p>
            </a>
          </li>
          <li class="nav-item ">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>
                Users
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="usersResident.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Resident</p>
                </a>
              </li>
              <li class="nav-item"><a href="editRequests.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>Edit Requests</p></a></li>
              <li class="nav-item">
                <a href="userAdministrator.php" class="nav-link">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Administrator</p>
                </a>
              </li>

            </ul>
          </li>
          <li class="nav-item">
            <a href="position.php" class="nav-link">
              <i class="nav-icon fas fa-user-tie"></i>
              <p>
                Position
              </p>
            </a>
          </li>

          <!-- DRM Part   (START)   -->
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-exclamation-triangle"></i>
              <p>
                DRRM
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
              
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="drrmHousehold.php" class="nav-link">
                    <i class="fas fa-users nav-icon text-red"></i>
                    <p>Household Members</p>
                  </a>
                </li>
                
                <li class="nav-item">
                  <a href="drrmEvacuation.php" class="nav-link">
                    <i class="fas fa-house-damage nav-icon text-red"></i>
                    <p>Evacuation Center</p>
                  </a>
                </li>
              </ul>
          </li>
        <!-- End of DRM Part -->

          <li class="nav-item">
            <a href="blotterRecord.php" class="nav-link">
              <i class="nav-icon fas fa-clipboard"></i>
              <p>
                Blotter Record
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="report.php" class="nav-link">
              <i class="nav-icon fas fa-bookmark"></i>
              <p>
                Reports
              </p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="systemLog.php" class="nav-link">
              <i class="nav-icon fas fa-history"></i>
              <p>
                System Logs
              </p>
            </a>
          </li>
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

 

    <!-- Main content -->
    <section class="content mt-3">
      <div class="container-fluid">



            <div class="card card-tabs">
              <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="certificate-tabs" data-toggle="pill" href="#certificate-tabs" role="tab" aria-controls="certificate-tabs" aria-selected="true">Certificate <span class="badge badge-success bg-lime" id="total"></span></a>
                  </li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                  <div class="tab-pane fade show active" id="certificate-tabs" role="tabpanel" aria-labelledby="certificate-tabs">
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text bg-indigo">SEARCH</span>
                            </div>
                            <input type="text" class="form-control" id="searching" autocomplete="off">
                            <div class="input-group-append">
                              <span class="input-group-text bg-red" id="reset" type="button"><i class="fas fa-undo"></i> RESET</span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="table-responsive">
                      <table class="table table-hover table-striped text-sm" id="certificateTable" >
                        <thead>
                          <tr>
                            <th>Resident Number</th>
                            <th>Name</th>
              
                            <th>Purpose</th>
                            <th>

                            <select name="date_request" id="date_request" class="form-control form-control-sm">
                                <option value="">Date Request</option>
                                    <?php 
                                    $blank_request = '';
                                    $sql_date_request = "SELECT date_request FROM certificate_request WHERE  date_request != ? GROUP BY date_request";
                                    $stmt_date_request = $con->prepare($sql_date_request) or die ($con->error);
                                    $stmt_date_request->bind_param('s',$blank_request);
                                    $stmt_date_request->execute();
                                    $result_date_request = $stmt_date_request->get_result();
                                    while($row_date_request = $result_date_request->fetch_assoc()){
                                        echo '<option value="'.$row_date_request['date_request'].'">'.date("m/d/Y", strtotime($row_date_request['date_request'])).'</option>';
                                    }
                                    
                                    ?>
                            </select>

                            </th>
                            <th>
                                      <select name="date_issued" id="date_issued" class="form-control form-control-sm">
                                        <option value="">Date Issued</option>
                                              <?php 
                                              $blank_issued = '';
                                              $sql_date_issued = "SELECT date_issued FROM certificate_request WHERE  date_issued != ? GROUP BY date_issued";
                                              $stmt_date_issued = $con->prepare($sql_date_issued) or die ($con->error);
                                              $stmt_date_issued->bind_param('s',$blank_issued);
                                              $stmt_date_issued->execute();
                                              $result_date_issued = $stmt_date_issued->get_result();
                                              while($row_date_issued = $result_date_issued->fetch_assoc()){
                                                  echo '<option value="'.$row_date_issued['date_issued'].'">'.date("m/d/Y", strtotime($row_date_issued['date_issued'])).'</option>';
                                              }
                                              
                                              ?>
                                      </select>
                            </th>
                            <th>
                                    <select name="date_expired" id="date_expired" class="form-control form-control-sm">
                                      <option value="">Date Expired</option>
                                          <?php 
                                          $blank_expired = '';
                                          $sql_date_expired = "SELECT date_expired FROM certificate_request WHERE date_expired != ? GROUP BY date_expired";
                                          $stmt_date_expired = $con->prepare($sql_date_expired) or die ($con->error);
                                          $stmt_date_expired->bind_param('s',$blank_expired);
                                          $stmt_date_expired->execute();
                                          $result_date_expired = $stmt_date_expired->get_result();
                                          while($row_date_expired = $result_date_expired->fetch_assoc()){
                                              echo '<option value="'.$row_date_expired['date_expired'].'">'.date("m/d/Y", strtotime($row_date_expired['date_expired'])).'</option>';
                                          }
                                          
                                          ?>
                                    </select>
                            </th>
                            <th>
                                  <select name="status" id="status" class="form-control form-control-sm">
                                    <option value="">Status</option>
                                          <?php 
                                        
                                          $sql_status = "SELECT status FROM certificate_request GROUP BY status";
                                          $stmt_status = $con->prepare($sql_status) or die ($con->error);
                                          $stmt_status->execute();
                                          $result_status = $stmt_status->get_result();
                                          while($row_status = $result_status->fetch_assoc()){
                                              echo '<option value="'.$row_status['status'].'">'.$row_status['status'].'</option>';
                                          }
                                          
                                          ?>
                                </select>
                            </th>
                            <th class="text-center">Tools</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
                      </div>
                  </div>
                  
                </div>
              </div>
              <!-- /.card -->
            </div>
  


      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

 

  <!--Main footer (COPY THIS)-->
<footer class="main-footer">
  <strong>&copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')) ?></strong>
  <div class="float-right d-none d-sm-inline-block">
  </div>
</footer>
    

      
      
    </div>
  </footer>
</div>
<!-- ./wrapper -->



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
<script src="../assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="../assets/plugins/jquery-validation/additional-methods.min.js"></script>
<script src="../assets/plugins/jquery-validation/jquery-validate.bootstrap-tooltip.min.js"></script>
<script src="../assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<script src="../assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

<div id="show_status"></div>

<script>
  $(document).ready(function(){

    certificateTable();

    function certificateTable(){
      var date_request = $("#date_request").val();
      var date_issued = $("#date_issued").val();
      var date_expired = $("#date_expired").val();
      var status = $("#status").val();
      var certificateTable = $("#certificateTable").DataTable({
        processing: true,
        serverSide: true,
        order:[],
        autoWidth: false,
        ordering: false,
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'d-flex flex-sm-row-reverse flex-column border-top '<'px-2 'p><'px-2'i> <'px-2'l> >",
            pagingType: "full_numbers",
            language: {
              paginate: {
                next: '<i class="fas fa-angle-right text-white"></i>',
                previous: '<i class="fas fa-angle-left text-white"></i>', 
                first: '<i class="fa fa-angle-double-left text-white"></i>',
                last: '<i class="fa fa-angle-double-right text-white"  ></i>'        
              }, 
              lengthMenu: '<div class="mt-3 pr-2"> <span class="text-sm mb-3 pr-2">Rows per page:</span> <select>'+
                          '<option value="10">10</option>'+
                          '<option value="20">20</option>'+
                          '<option value="30">30</option>'+
                          '<option value="40">40</option>'+
                          '<option value="50">50</option>'+
                          '<option value="-1">All</option>'+
                          '</select></div>',
              info:  " _START_ - _END_ of _TOTAL_ ",
            },
        ajax:{
          url: 'certificateTable.php',
          type: 'POST',
          data:{
            date_request:date_request,
            date_issued:date_issued,
            date_expired:date_expired,
            status:status
          }
        },
        drawCallback: function (data) {
            $('#total').text(data.json.total);
            $('[data-toggle="tooltip"]').tooltip();
            $('.dataTables_paginate').addClass("mt-2 mt-md-2 pt-1");
            $('.dataTables_paginate ul.pagination').addClass("pagination-md");   
               
          },
          
      })
      $('#searching').keyup(function(){
        certificateTable.search($(this).val()).draw() ;
      })
   
    }

    $(document).on('change',"#date_request, #date_issued, #date_expired, #status",function(){
          $("#certificateTable").DataTable().destroy();
          certificateTable();
          $("#searching").keyup();
    })
    $(document).on('click','#reset',function(){

        if($("#date_request").val() != '' ||  $("#date_issued").val() !=  '' || $("#date_expired").val() != '' ||  $("#status").val() != '' ||  $("#searching").val() != ''){
            $("#date_request").val('');
            $("#date_issued").val('');
            $("#date_expired").val('');
            $("#status").val('');
            $("#searching").val('');
            $("#certificateTable").DataTable().destroy();
                certificateTable();
            $("#searching").keyup();
        }

    })


    $(document).on('click','.acceptStatus',function(){

      $("#show_status").html('');

      var residence_id = $(this).attr('id');
      var certificate_id = $(this).data('id');

      $.ajax({
        url: 'certificateRequestStatus.php',
        type: 'POST',
        data:{
          residence_id:residence_id,
          certificate_id:certificate_id,
        },
        success:function(data){
          $("#show_status").html(data);
          $("#showStatusRequestModal").modal('show');
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


 


  })
</script>

<script>
// Restricts input for each element in the set of matched elements to the given inputFilter.
(function($) {
  $.fn.inputFilter = function(inputFilter) {
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
      if (inputFilter(this.value)) {
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      } else {
        this.value = "";
      }
    });
  };
}(jQuery));

 
 
  $("#searching").inputFilter(function(value) {
  return /^[0-9a-z, ]*$/i.test(value); 
  });

</script>


</body>
</html>
