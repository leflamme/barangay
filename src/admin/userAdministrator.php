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
  <title>List: Administrators (Brgy. Officials)</title>

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
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <!-- DONT FORGET -->
  <link rel="stylesheet" href="../assets/dist/css/admin.css">
  
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

/* CARD & HEADER */
.card {
  background-color: #ffffff;
  border: 2px solid #A7E6FF !important;
  border-radius: 15px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.card-header {
  background-color: #050C9C;
  color: white;
  border-radius: 15px 15px 0 0;
}

.card-header .card-title .btn {
  background-color: #3ABEF9 !important;
  color: #ffffff !important;
  font-weight: 600;
  border-radius: 10px;
  border: none;
}

.card-header .card-title .btn:hover {
  background-color: #3572EF !important;
}

/* FIELDSET & LEGEND */
fieldset {
  background-color: #ffffff;
}

legend {
  color: #050C9C;
  font-weight: 600;
  font-size: 1.1em;
}

/* TABLE STYLING */
#userTableAdministrator {
  background-color: #ffffff;
  color: #000;
  width: 100% !important;
}

#userTableAdministrator thead {
  background-color: #050C9C;
  color: #ffffff;
  font-weight: 600;
}

#userTableAdministrator tbody tr:nth-of-type(odd) {
  background-color: #F2F6FF;
}

#userTableAdministrator tbody tr:nth-of-type(even) {
  background-color: #ffffff;
}

#userTableAdministrator tbody tr:hover {
  background-color: #A7E6FF;
}

/* DATATABLE PAGINATION */
.dataTables_wrapper .dataTables_paginate .page-link {
  background-color: #3ABEF9 !important;
  color: #FFF !important;
  border-radius: 15px !important;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.dataTables_wrapper .dataTables_paginate .page-link:hover {
  background-color: #3572EF !important;
  color: #fff !important;
}

.dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
  background-color: #FFF591 !important;
  color: #fff !important;
  border-color: #FFF591 !important;
}




/* DATATABLE INFO & LENGTH */
.dataTables_info {
  font-size: 13px;
  font-weight: 500;
  color: #050C9C;
}

.dataTables_length select {
  background: #ffffff;
  border: 1px solid #3ABEF9;
  color: #050C9C;
}

/* SEARCH BOX */
.dataTables_filter input[type="search"] {
  background-color: #ffffff !important;
  color: #000000 !important;
  border: 1px solid #ccc !important;
  border-radius: 6px !important;
  padding: 6px 10px !important;
}

/* MOBILE RESPONSIVENESS */
@media (max-width: 768px) {
  legend {
    font-size: 1rem;
  }

  .btn {
    font-size: 0.9rem;
  }

  .table-responsive {
    overflow-x: auto;
  }

  #userTableAdministrator td,
  #userTableAdministrator th {
    white-space: nowrap;
  }
}

.thead-blue {
  background-color: #050C9C;
  color: white;
  font-weight: 600;
}

.btn-blue {
  background-color: #3ABEF9 !important;
  color: #ffffff !important;
  font-weight: 600;
  border-radius: 10px;
  border: none;
}

.btn-blue:hover {
  background-color: #3572EF !important;
}

thead.bg-black {
  background-color: #fff !important;
}
.card-header,
.btn.bg-black {
  background-color: #fff !important;
}


  </style>
 
 
</head>


<body class="hold-transition  sidebar-mini  layout-footer-fixed">
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
                <a href="officialEndTerm.php" class="nav-link ">
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
                <a href="allResidence.php" class="nav-link">
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
            <a href="requestCertificate.php" class="nav-link">
              <i class="nav-icon fas fa-certificate"></i>
              <p>
                Certificate
              </p>
            </a>
          </li>
          <li class="nav-item menu-open">
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
              <li class="nav-item">
                <a href="userAdministrator.php" class="nav-link bg-indigo">
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
            <a href="settings.php" class="nav-link">
              <i class="nav-icon fas fa-cog"></i>
              <p>
                Settings
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
          <li class="nav-item">
            <a href="backupRestore.php" class="nav-link">
              <i class="nav-icon fas fa-database"></i>
              <p>
                Backup/Restore
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

              <div class="card">
                  <div class="card-header">
                    <div class="card-title">
                      <button type="button" id="openModal" data-toggle="modal" data-target="#newAdministratorModal" class="btn btn-blue btn-flat elevation-5 px-3"><i class="fas fa-user-plus"></i>  NEW ADMINISTRATOR </button>
                    </div>
                  </div>
                <div class="card-body">
                    <fieldset>
                      <legend>NUMBER OF USERS ADMINISTRATOR <span id="total"></span></legend>
                        
                  
                      <div class="table-responsive">
  <table class="table table-striped table-hover" id="userTableAdministrator">
    <thead class="bg-black">
      <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Username</th>
        <th>Password</th>
        <th class="text-center">Action</th>
      </tr>
    </thead>
  </table>
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
    
    <div class="float-right d-none d-sm-inline-block">
    </div>
  </footer>
</div>
<!-- ./wrapper -->





<!-- Modal -->
<div class="modal fade" id="newAdministratorModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    <form id="addUserAdministratorForm" method="post" enctype="multipart/form-data" autocomplete="off">

          <div class="modal-header">
              <h5 class="modal-title">Administrator</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
          <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-12 text-center">
                <img src="../assets/dist/img/image.png" style="cursor: pointer;" class="img-circle " alt="adminImage" id="display_image">
                <input type="file" id="image" name="image" style="display: none;">
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  <label>First Name</label>
                  <input type="text" name="first_name" id="first_name" class="form-control" >
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Middle Name</label>
                  <input type="text" name="middle_name" id="middle_name" class="form-control" >
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Last Name</label>
                  <input type="text" name="last_name" id="last_name" class="form-control" >
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Username</label>
                  <input type="text" name="username" id="username" class="form-control" >
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Password</label>
                  <input type="text" name="password" id="password" class="form-control" >
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Contact Number</label>
                  <input type="text" name="contact_number" maxlength="11" id="contact_number" class="form-control" >
                </div>
              </div>
            </div>
          </div>
          </div>
          <div class="modal-footer">
          <button type="button" class="btn btn-secondary elevation-5 px-3 btn-flat" data-dismiss="modal"><i class="fas fa-times  "></i> CLOSE</button>
          <button type="submit" class="btn btn-success elevation-5 px-3 btn-flat"><i class="fas fa-plus"></i> ADD</button>
          </div>

          </form>

    </div>
  </div>
</div>




<div id="imagemodal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="background-color: #000">
      <div class="modal-body">
      <button type="button" class="close" data-dismiss="modal" style="color: #fff;"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      <img src="" class="imagepreview img-circle" style="width: 100%;" >
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
<script src="../assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="../assets/plugins/jquery-validation/additional-methods.min.js"></script>
<script src="../assets/plugins/jquery-validation/jquery-validate.bootstrap-tooltip.min.js"></script>
<script src="../assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<div id="displayUserAdministrator"></div>
<script>
  $(document).ready(function(){

    userTableAdministrator()




    $("#openModal").on('click',function(){
      $("#addUserAdministratorForm")[0].reset();
      $("#display_image").attr('src', '../assets/dist/img/image.png');
    })

    $('#display_image').on('click',function(){
      $("#image").click();
    })
    $("#image").change(function(){
        editDsiplayImage(this);
      })


      $(function () {
        $.validator.setDefaults({
          submitHandler: function (form) {
            Swal.fire({
              title: '<strong class="text-warning">Are you sure?</strong>',
              html: "<b>You want add this user?</b>",
              type: 'info',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, add it!',
              allowOutsideClick: false,
              width: '400px',
            }).then((result) => {
              if (result.value) {
                  $.ajax({
                    url: 'addAdministrator.php',
                    type: 'POST',
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success:function(data){


                      if(data == 'error'){

                        Swal.fire({
                          title: '<strong class="text-danger">ERROR</strong>',
                          type: 'error',
                          html: '<b>Username is Already Exist<b>',
                          width: '400px',
                          confirmButtonColor: '#6610f2',
                        })
                      }else{
                        Swal.fire({
                          title: '<strong class="text-success">SUCCESS</strong>',
                          type: 'success',
                          html: '<b>Added Admistator has Successfully<b>',
                          width: '400px',
                          confirmButtonColor: '#6610f2',
                          allowOutsideClick: false,
                          showConfirmButton: false,
                          timer: 2000,
                        }).then(()=>{
                          
                        
                          $("#userTableAdministrator").DataTable().ajax.reload();
                          $("#addUserAdministratorForm")[0].reset();
                          $("#display_image").attr('src', '../assets/dist/img/image.png');
                          $("#newAdministratorModal").modal('hide');
                          
                      })
                    }

                     
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
              }
            })
            
          }
        });
      $('#addUserAdministratorForm').validate({
        rules: {
          first_name: {
            required: true,
            minlength: 2
          },
          last_name: {
            required: true,
            minlength: 2
          },
          username: {
            required: true,
            minlength: 6
          },
          password: {
            required: true,
            minlength: 6
          },
          contact_number: {
            required: true,
            minlength: 11
           
          },
       
        },
        messages: {
          first_name: {
            required: "<span class='text-danger text-bold'>First Name is Required</span>",
            minlength: "<span class='text-danger'>First Name must be at least 2 characters long</span>"
          },
          last_name: {
            required: "<span class='text-danger text-bold'>Last Name is Required</span>",
            minlength: "<span class='text-danger'>Last Name must be at least 2 characters long</span>"
          },
          username: {
            required: "<span class='text-danger text-bold'>Username is Required</span>",
            minlength: "<span class='text-danger'>Username must be at least 6 characters long</span>"
          },
          password: {
            required: "<span class='text-danger text-bold'>Password is Required</span>",
            minlength: "<span class='text-danger'>Password must be at least 6 characters long</span>"
          },
          contact_number: {
            required: "<span class='text-danger text-bold'>Contact Number is Required</span>",
            minlength: "<span class='text-danger'>Input Exact Contact Number</span>"
          },
  
        },
        tooltip_options: {
          '_all_': {
            placement: 'bottom',
            html:true,
          },
          
        },
      });
    })


      function editDsiplayImage(input){
        if(input.files && input.files[0]){
          var reader = new FileReader();
          var image = $("#image").val().split('.').pop().toLowerCase();

          if(image != ''){
            if(jQuery.inArray(image, ['gif','png','jpeg','jpg']) == -1){
              Swal.fire({
                title: '<strong class="text-danger">ERROR</strong>',
                type: 'error',
                html: '<b>Invalid Image File<b>',
                width: '400px',
                confirmButtonColor: '#6610f2',
              })
              $("#image").val('');
              $("#display_image").attr('src', '../assets/dist/img/image.png');
              return false;
            }
          }
            reader.onload = function(e){
              $("#display_image").attr('src', e.target.result);
              $("#display_image").hide();
              $("#display_image").fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
      }




    $(document).on('click','.viewUserAdministrator',function(){
      var user_id = $(this).attr('id');
     
      $("#displayUserAdministrator").html('');

      $.ajax({
        url: 'viewUserAdministrator.php',
        type: 'POST',
        data:{
          user_id:user_id
        },
        cache: false,
        success:function(data){
          $("#displayUserAdministrator").html(data);
          $("#editUserAdministratorModal").modal('show');
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
     
  
      
    


    function userTableAdministrator(){
      var userTableAdministrator = $("#userTableAdministrator").DataTable({

        processing: true,
        serverSide: true,
        autoWith: false,
        responsive: true,
        ajax:{
          url: 'userTableAdministrator.php',
          type: 'POST',
        },
        order:[],
        columnDefs:[
          {
            orderable: false,
            targets: 0,
          },
          {
            orderable: false,
            targets: 4,
          },
          {
          
            targets: 4,
            className: 'text-center',
          },
        ],
        drawCallback:function(data){
          $('#total').text(data.json.total);
        }
      })
   
    }
    $(document).on('click', '.pop',function() {
			$('.imagepreview').attr('src', $(this).find('img').attr('src'));
			$('#imagemodal').modal('show');   
		});


    $(document).on('click','.deleteUserAdministrator',function(){
    var user_id = $(this).attr('id');
    Swal.fire({
        title: '<strong class="text-danger">ARE YOU SURE?</strong>',
        html: "<b>You want delete this User?</b>",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        allowOutsideClick: false,
        confirmButtonText: 'Yes, delete it!',
        width: '400px',
      }).then((result) => {
        if (result.value) {
          $.ajax({
            url: 'deleteUserAdministrator.php',
            type: 'POST',
            data: {
              user_id:user_id,
            },
            cache: false,
            success:function(data){
              Swal.fire({
                title: '<strong class="text-success">Success</strong>',
                type: 'success',
                html: '<b>Deleted User has Successfully<b>',
                width: '400px',
                showConfirmButton: false,
                allowOutsideClick: false,
                timer: 2000
              }).then(()=>{
                $("#userTableAdministrator").DataTable().ajax.reload();
              })
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
        }
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

 
  $("#contact_number").inputFilter(function(value) {
  return /^-?\d*$/.test(value); 
  
  });


  $("#first_name,#middle_name,#last_name, #username").inputFilter(function(value) {
  return /^[a-z, ]*$/i.test(value); 
  });

  $("#password").inputFilter(function(value) {
    return /^[0-9a-z, ,-]*$/i.test(value); 
  });
  
 

</script>


</body>
</html>
