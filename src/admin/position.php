<?php 
session_start();
include_once '../connection.php';

if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){
  $user_id = $_SESSION['user_id'];
  $sql_user = "SELECT * FROM `users` WHERE `id` = ? ";
  $stmt_user = $con->prepare($sql_user) or die ($con->error);
  $stmt_user->bind_param('s',$user_id);
  $stmt_user->execute();
  $result_user = $stmt_user->get_result();
  $row_user = $result_user->fetch_assoc();
  $first_name_user = $row_user['first_name'] ?? '';
  $last_name_user = $row_user['last_name']?? '';
  $user_type = $row_user['user_type'] ?? '';
  $user_image = $row_user['image'] ?? '';
}else{
 echo '<script>
        window.location.href = "../login.php";
      </script>';
}

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

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title></title>

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

.card {
  background-color: #ffffff;
  border: 2px solid #A7E6FF !important;
  border-radius: 10px;
  box-shadow: 0 0 30px rgba(5, 12, 156, 0.1);
  color: #000;
}

/* CARD HEADER */
.card-header {
  background-color: #050C9C !important;
  color: #fff !important;
  border-bottom: 2px solid #3572EF;
  border-radius: 10px 10px 0 0;
  padding: 15px 20px;
}

.card-title .btn {
  background-color: #3ABEF9; 
  color: #fff;
  font-weight: 600;
   border-radius: 10px;
  border: none;
}

.card-title .btn:hover {
  background-color: #3572EF;
  color: #fff;
}

.card-body {
  background-color: #fff;
  color: #000;
  border-bottom-left-radius: 10px;
  border-bottom-right-radius: 10px;
}

.input-group-text {
  background-color: #050C9C !important;
  color: #fff !important;
  border: none;
}

#searching {
  border: 1px solid #A7E6FF;
}

.table thead {
  background-color: #050C9C;
  color: #fff;
}

.table td, .table th {
  vertical-align: middle;
}

.modal-content {
  border: 2px solid #3ABEF9;
}

.modal-header, .modal-footer {
  background-color: #050C9C;
  color: #fff;
}

.modal-body label {
  font-weight: 600;
  color: #050C9C;
}

#newPositionForm input,
#newPositionForm textarea {
  border: 1px solid #3572EF;
}

#newPositionForm input:focus,
#newPositionForm textarea:focus {
  border-color: #3ABEF9;
  box-shadow: none;
}

.modal-footer .btn {
  font-weight: 600;
}

.modal-footer .btn.bg-black {
  background-color: #E41749;
  color: #fff;
}

.modal-footer .btn.bg-black:hover {
  background-color: #F5587B;
}

.modal-footer .btn.bg-success {
  background-color: #3572EF;
  color: #fff;
}

.modal-footer .btn.bg-success:hover {
  background-color: #3ABEF9;
}

/* Responsive Fix */
@media (max-width: 768px) {
  .table-responsive {
    overflow-x: auto;
  }
  .table td, .table th {
    white-space: nowrap;
  }
}

 /* Pagination buttons (<< < > >>) COPY THIS */
 .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
    background-color: #3572EF !important;
    font-weight: bold;
  }

  .dataTables_length select {
    background: #ffffff;
    border: 1px solid #3ABEF9;
    color: #050C9C;
  }

.dataTables_wrapper .dataTables_paginate .page-item .page-link {
  background-color: #3ABEF9 !important; /* bright sky blue */
  color: #000 !important;
  border: 1px solid #A7E6FF !important;
  border-radius: 5px !important;
  margin: 0 2px;
  font-weight: 600;
  transition: background-color 0.3s ease;
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

.dataTables_wrapper .dataTables_paginate .page-link:hover {
  background-color: #3572EF !important;
  color: #fff !important;
}

.dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
  background-color: #FFF591 !important;
  color: #fff !important;
  border-color: #FFF591 !important;
}

</style>

<!-- Include this style block inside your <head> tag after admin.css --> 
</head>
<!-- END OF DESIGN AND HEADER PART -->

<!-- START OF MAIN BODY AND CONTENTS -->
<body class="hold-transition sidebar-mini   ">
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
              <li class="nav-item">
                <a href="userAdministrator.php" class="nav-link">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Administrator</p>
                </a>
              </li>

            </ul>
          </li>
          <li class="nav-item">
            <a href="position.php" class="nav-link bg-indigo">
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
    <section class="content mt-5">
      <div class="container-fluid">

          <div class="card">
              <div class="card-header">
                <div class="card-title">
                  <button type="button" class="btn bg-black elevation-5 px-3 btn-flat" id="buttonPosition" data-toggle="modal" data-target="#newModalPosition"><i class="fas fa-plus"></i> ADD POSITION</button>
                </div>
              </div>
            <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text ">SEARCH</span>
                    </div>
                    <input type="text" class="form-control" id="searching" autocomplete="off">
                  
                  </div>
                </div>
              </div>
              <table class="table" id="positionTable">
                <thead>
                  <tr>
                    <th>Position</th>
                    <th>Limit</th>
                    <th class="text-center">Action</th>
                  </tr>
                </thead>
              </table>
            </div>
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
<div class="modal fade" id="newModalPosition" data-backdrop="static" data-keybaord="false" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form id="newPositionForm" method="post">  
      <div class="modal-body">
        <div class="container-fluid">
         <div class="row">
           <div class="col-sm-12">
             <div class="form-group">
               <label>Position</label>
               <input type="text" name="add_position"  id="add_position" class="form-control text-uppercase">
             </div>
           </div>
           <div class="col-sm-12">
             <div class="form-group">
               <label>Limit</label>
               <input type="text" maxlength="2"  name="limit" id="limit" class="form-control">
             </div>
           </div>
           <div class="col-sm-12">
             <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" name="add_description" id="add_description" rows="3"></textarea>
              </div>
           </div>
         </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-black px-3 elevation-5 btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> CLOSE</button>
        <button type="submit" class="btn bg-success btn-flat px-3 elevation-5"><i class="fas fa-share-square"></i> SAVE</button>
      </div>

      </form>   
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
<script src="../assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<script src="../assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<div id="displayPosition"></div>
<script>
  $(document).ready(function(){

    positionTable();
   
    viewStatusPosition();
    function positionTable(){
      var positionTable = $("#positionTable").DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        order:[],
        ajax:{
          url: 'positionTable.php',
          type: 'POST',
        },
        columnDefs:[
          {
            targets: 2,
            orderable: false,
            className: 'text-center'
          },
        
        ],
        dom: "<'row'<'col-sm-12 col-md-2'><'col-sm-12 col-md-6'>>" +
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
            drawCallback:function(data)  {
              $('.dataTables_paginate').addClass("mt-2 mt-md-2 pt-1");
              $('.dataTables_paginate ul.pagination').addClass("pagination-md");               
            }
      })
      $('#searching').keyup(function(){
        positionTable.search($(this).val()).draw() ;
      })
    }

    function editStatusPosition(){
      $(document).on('click','.editStatusPosition',function(){
      var status_position = $(this).attr('id');

        $.ajax({
            url: 'editStatusposition.php',
            type: 'POST',
            cache: false,
            data: {
              status_position:status_position
            },
            success:function(data){

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
    }

    function viewStatusPosition(){
      $(document).on('click','.viewPosition',function(){
      var position_id = $(this).attr('id');

        $("#displayPosition").html('');
        
        $.ajax({
          url: 'viewPositionModal.php',
          type: 'POST',
          dataType: 'html',
          cache: false,
          data: {
            position_id:position_id
          },
          success:function(data){
            $("#displayPosition").html(data);
            $("#viewPositionModal").modal('show');
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
    }


    $(function () {
        $.validator.setDefaults({
          submitHandler: function (form) {
            $.ajax({
              url: 'addNewPosition.php',
              type: 'POST',
              data: $(form).serialize(),
              cache: false,
              success:function(data){
                if(data == 'error'){

                  Swal.fire({
                    title: '<strong class="text-danger"ERROR</strong>',
                    type: 'error',
                    html: '<b>Position is already Exist<b>',
                    width: '400px',
                    confirmButtonColor: '#6610f2',
                  })
                  
                }else{

             
                  
                  Swal.fire({
                    title: '<strong class="text-success">Success</strong>',
                    type: 'success',
                    html: '<b>Added Position has Successfully<b>',
                    width: '400px',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    timer: 2000
                  }).then(()=>{
                    $("#positionTable").DataTable().ajax.reload();
                    $("#newModalPosition").modal('hide');
                    $("#newPositionForm")[0].reset();
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
        });
      $('#newPositionForm').validate({
        rules: {
          add_position: {
            required: true,
            minlength: 2
          },
          limit: {
            required: true,
    
          },
         
        },
        messages: {
          add_position: {
            required: "Please provide a Position",
            minlength: "Position must be at least 2 characters long"
          },
            limit: {
            required: "Please provide a Limit",
          },
         
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
          element.closest('.form-group-sm').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
      });
    })


    $(document).on('click','#buttonPosition', function(){
      $("#newPositionForm")[0].reset();
    })



    $(document).on('click','.deletePosition',function(){

      var position_id = $(this).attr('id');

      Swal.fire({
        title: '<strong class="text-danger">ARE YOU SURE?</strong>',
        html: "<b>You want delete this Position?</b>",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        allowOutsideClick: false,
        confirmButtonText: 'Yes, Delete it!',
        width: '400px',
      }).then((result) => {
        if (result.value) {
          $.ajax({
            url: 'deletePosition.php',
            type: 'POST',
            data: {
              position_id:position_id,
            },
            cache: false,
            success:function(data){

              if(data == 'error'){

                Swal.fire({
                  title: '<strong class="text-danger">ERROR</strong>',
                  type: 'error',
                  html: '<b>This Position have been Used<b>',
                  width: '400px',
                  allowOutsideClick: false,
                })

              }else{

                  Swal.fire({
                    title: '<strong class="text-success">Success</strong>',
                    type: 'success',
                    html: '<b>Deleted Position has Successfully<b>',
                    width: '400px',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    timer: 2000
                  }).then(()=>{
                    $("#positionTable").DataTable().ajax.reload();
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

$("#add_description").inputFilter(function(value) {
  return /^[a-z, ]*$/i.test(value); 
  });

  
$("#add_position").inputFilter(function(value) {
  return /^[a-z, ]*$/i.test(value); 
  });
  $("#limit").inputFilter(function(value) {
  return /^[0-9]*$/i.test(value); 
  });



  $("#limit").on("input", function() {
      if (/^0/.test(this.value)) {
        this.value = this.value.replace(/^0/, "")
      }
    })

</script>




</body>
</html>
