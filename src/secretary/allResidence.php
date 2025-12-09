<?php 
session_start();
include_once '../connection.php';

try{
  if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'secretary'){
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
  } else {
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
  <title>Residents List</title>
  <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">

  <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">

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
  <link rel="stylesheet" href="../assets/dist/css/admin.css?v=2">

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

body {
  font-family: 'Poppins', sans-serif;
  background-color: #ffffff;
}

/* Card & Background */
.card,
.card-body,
fieldset {
  background-color: #ffffff;
  border-color: #A7E6FF !important;
  color: #050C9C;
  border-radius: 12px;
}

legend {
  color: #050C9C;
  font-weight: 600;
}

/* Form Inputs */
.form-control {
  background-color: #ffffff !important;
  color: #000000 !important;
  border: 1px solid #ccc !important;
  border-radius: 0 0.375rem 0.375rem 0 !important;
  box-shadow: none !important;
}

/* Optional: if you want selects to match too */
select.form-control {
  background-color: #ffffff !important;
  color: #000000 !important;
}

.form-control::placeholder {
  color: #999;
}

/* Input group text (labels) */
.input-group-text {
  background-color: #050C9C !important;
  color: white !important;
  font-weight: 500;
  border: 1px solid #ccc !important;
  border-radius: 0.375rem 0 0 0.375rem !important;
}

.input-group {
  border-radius: 0.375rem;
  overflow: hidden;
}

/* Buttons */
.btn-warning {
  background-color: #3ABEF9 !important;
  border-color: #3ABEF9 !important;
  color: #ffffff !important;
  border-radius: 10px;
  font-weight: 600;
}

.btn-warning:hover {
  background-color: #3572EF !important;
  border-color: #3572EF !important;
}

.btn-danger {
  background-color: #E41749 !important;
  border-color: #E41749 !important;
  color: #ffffff !important;
  border-radius: 10px;
  font-weight: 600;
}

.btn-danger:hover {
  background-color: #F5587B !important;
  border-color: #F5587B !important;
}

/* Table */
#allResidenceTable {
  background-color: #F8FBFF;
  color: #000;
}

#allResidenceTable thead {
  background-color: #050C9C;
  color: #ffffff;
  font-weight: 600;
  border-bottom: 3px solid #3572EF;
  box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1);
}

#allResidenceTable thead th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-transform: uppercase;
}

#allResidenceTable tbody tr:nth-of-type(odd) {
  background-color: #FAF9F6;
}

#allResidenceTable tbody tr:nth-of-type(even) {
  background-color: #FAF9F6;
}

#allResidenceTable tbody tr:hover {
  background-color: #0047ab !important;
  cursor: pointer;
}

#allResidenceTable td,
#allResidenceTable th {
  border-color: #A7E6FF;
}

/* Pagination */
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
  background-color: #050C9C !important;
  color: #fff !important;
  border-color: #050C9C !important;
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

.select2-container--default .select2-selection--single {
  background-color: #ffffff !important;
  color: #000 !important;
  border: 1px solid #ccc !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
  color: #000000;
}

table#allResidenceTable thead {
  background-color: #050C9C !important;
  color: #ffffff !important;
}

table#allResidenceTable thead th {
  background-color: #050C9C !important;
  color: #ffffff !important;
  font-weight: 600 !important;
}

div.dataTables_scrollHead table.dataTable thead th {
  background-color: #050C9C !important;
  color: #ffffff !important;
}


@media (max-width: 768px) {
  .input-group .input-group-prepend .input-group-text {
    font-size: 0.85rem;
  }
  legend {
    font-size: 1rem;
  }
  .btn {
    font-size: 0.9rem;
  }
}

</style>


 
 
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble " src="../assets/dist/img/loader.gif" alt="AdminLTELogo" height="70" width="70">
  </div>

  <nav class="main-header navbar navbar-expand dark-mode">
    <ul class="navbar-nav">
      <li class="nav-item">
        <h5><a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></h5>
      </li>
      <li class="nav-item d-none d-sm-inline-block" style="font-variant: small-caps;">
        <h5 class="nav-link text-white" ><?= $barangay ?></h5>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <h5 class="nav-link text-white" >-</h5>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <h5 class="nav-link text-white" ><?= $zone ?></h5>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <h5 class="nav-link text-white" >-</h5>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <h5 class="nav-link text-white" ><?= $district ?></h5>
      </li>
    </ul>


    <ul class="navbar-nav ml-auto">

       <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="myProfile.php" class="dropdown-item">
            <div class="media">
              <?php 
                if($user_image != '' || $user_image != null || !empty($user_image)){
                  echo '<img src="../assets/dist/img/'.$user_image.'" class="img-size-50 mr-3 img-circle alt="User Image">';
                }else{
                  echo '<img src="../assets/dist/img/image.png" class="img-size-50 mr-3 img-circle alt="User Image">';
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
  <aside class="main-sidebar elevation-4 sidebar-no-expand dark-mode">
    <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display:block;">

    <div class="sidebar">

      <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-users-cog"></i><p>Barangay Official<i class="right fas fa-angle-left"></i></p></a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="allOfficial.php" class="nav-link "><i class="fas fa-circle nav-icon text-red"></i><p>List of Official</p></a></li>
            <li class="nav-item"><a href="officialEndTerm.php" class="nav-link "><i class="fas fa-circle nav-icon text-red"></i><p>Official End Term</p></a></li>
          </ul>
        </li>

        <li class="nav-item menu-open"><a href="#" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Residence<i class="right fas fa-angle-left"></i></p></a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="newResidence.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>New Residence</p></a></li>
            <li class="nav-item"><a href="allResidence.php" class="nav-link bg-indigo"><i class="fas fa-circle nav-icon text-red"></i><p>All Residence</p></a></li>
            <li class="nav-item"><a href="archiveResidence.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>Archive Residence</p></a></li>
          </ul>
        </li>
          
        <li class="nav-item "><a href="#" class="nav-link"><i class="nav-icon fas fa-user-shield"></i><p>Users<i class="right fas fa-angle-left"></i></p></a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="usersResident.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>Resident</p></a></li>
            <li class="nav-item"><a href="editRequests.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>Edit Requests</p></a></li>
          </ul>
        </li>
       
        <li class="nav-item has-treeview"><a href="#" class="nav-link"><i class="nav-icon fas fa-exclamation-triangle"></i><p>DRRM<i class="right fas fa-angle-left"></i></p></a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="drrmEvacuation.php" class="nav-link"><i class="fas fa-house-damage nav-icon text-red"></i><p>Evacuation Center</p></a></li>
            <li class="nav-item"><a href="report.php" class="nav-link"><i class="nav-icon fas fa-bookmark"></i><p>Masterlist Report</p></a></li>
          </ul>
        </li>

        <li class="nav-item "><a href="requestCertificate.php" class="nav-link"><i class="nav-icon fas fa-certificate"></i><p>Certificate</p></a></li>
        <li class="nav-item"><a href="blotterRecord.php" class="nav-link"><i class="nav-icon fas fa-clipboard"></i><p>Blotter Record</p></a></li>

        </ul>
      </nav>
      </div>
    </aside>

  <div class="content-wrapper">
   

    <section class="content mt-3">
      <div class="container-fluid">

    <div class="card">
      <div class="card-body">
          <fieldset>
            <legend>NUMBER OF RESIDENCE <span id="total"></span></legend>
            <div class="row">
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">FIRST NAME</span>
                    </div>
                        <input type="search" name="first_name" id="first_name" class="form-control"> 
                      </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">MIDDLE NAME</span>
                    </div>
                        <input type="search" name="middle_name" id="middle_name" class="form-control"> 
                      </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">LAST NAME</span>
                    </div>
                        <input type="search" name="last_name" id="last_name" class="form-control"> 
                      </select>
                  </div>
                </div>
                
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">RESIDENCY TYPE</span>
                    </div>
                      <select name="residency_type" id="residency_type" class="form-control">
                       <option value="">--SELECT RESIDENCY TYPE--</option> 
                       <option value="RESIDENT">RESIDENT</option> 
                       <option value="TENANT">TENANT</option>
                      </select>
                  </div>
                </div>
                
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text ">AGE</span>
                    </div>
                        <input type="number" name="age" id="age" class="form-control"> 
                      </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text ">STATUS</span>
                    </div>
                      <select name="status" id="status" class="form-control">
                        <option value="">--SELECT STATUS--</option>
                        <option value="ACTIVE">ACTIVE</option>
                        <option value="INACTIVE">INACTIVE</option>
                      </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">PWD</span>
                    </div>
                      <select name="pwd" id="pwd" class="form-control">
                        <option value="">--SELECT PWD--</option>
                        <option value="YES">YES</option>
                        <option value="NO">NO</option>
                      </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">SINGLE PARENT</span>
                    </div>
                      <select name="single_parent" id="single_parent" class="form-control">
                        <option value="">--SELECT PARENT STATUS--</option>
                        <option value="YES">YES</option>
                        <option value="NO">NO</option>
                      </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">SENIOR</span>
                    </div>
                      <select name="senior" id="senior" class="form-control">
                        <option value="">--SELECT SENIOR--</option>
                        <option value="YES">YES</option>
                        <option value="NO">NO</option>
                      </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">RESIDENT NUMBER</span>
                    </div>
                        <input type="text" name="resident_id" id="resident_id" class="form-control"> 
                      </select>
                  </div>
                </div>
                <div class="col-sm-4 text-center mb-4">
                  <button type="button" class="btn btn-warning px-3 elevation-3 text-white" id="search"><i class="fas fa-search"></i> SEARCH</button>
                  <button type="button" class="btn btn-danger px-3 elevation-3" id="reset"><i class="fas fa-undo"></i> RESET</button>
                </div>
                
              </div>
                
                
              
            <table class="table table-striped table-hover " id="allResidenceTable">
              <thead class="bg-black text-uppercase">
                <tr>
                  <th>Image</th>
                  <th>Resident Number</th>
                  <th>Name</th>
                  <th>Age</th>
                  <th>Pwd</th>
                  <th>Single Parent</th>
                  <th>Residency Type</th>
                  <th>Status</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
            </table>
          </fieldset>
        </div>
      </div>   


      </div></section>
    </div>
  <footer class="main-footer">
    <strong>Copyright &copy; <?php echo date("Y"); ?> - <?php echo date('Y', strtotime('+1 year'));  ?> </strong>
 
    <div class="float-right d-none d-sm-inline-block">
    </div>
  </footer>

</div>
<div id="imagemodal" class="modal " tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="background-color: #000">
      <div class="modal-body">
      <button type="button" class="close" data-dismiss="modal" style="color: #fff;"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      <img src="" class="imagepreview img-circle" style="width: 100%;" >
      </div>
    </div>
  </div>
</div>



<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
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
<div id="displayResidence"></div>
<script>
  $(document).ready(function(){

    filterData();
    editStatus();
    viewResidence();
    deleteResidence();

    function deleteResidence(){
      $(document).on('click','.deleteResidence',function(){
        var residence_id = $(this).attr('id');
        Swal.fire({
            title: '<strong class="text-danger">Are you sure?</strong>',
            html: "You want archive this Resident?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            allowOutsideClick: false,
            confirmButtonText: 'Yes, archive it!',
            width: '400px',
          }).then((result) => {
            if (result.value) {
              $.ajax({
                url: 'deleteResidence.php',
                type: 'POST',
                data: {
                  residence_id:residence_id,
                },
                cache: false,
                success:function(data){
                  Swal.fire({
                    title: '<strong class="text-success">Success</strong>',
                    type: 'success',
                    html: '<b>Resident has been successfully archived<b>',
                    width: '400px',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    timer: 2000
                  }).then(()=>{
                    $("#allResidenceTable").DataTable().ajax.reload();
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
    }


    function editStatus(){
      $(document).on('click','.editStatus',function(){
      var status_residence = $(this).attr('id');
      var data_status = $(this).attr('data-status');

        $.ajax({
            url: 'editStatusResidence.php',
            type: 'POST',
            cache: false,
            data: {
              status_residence:status_residence,data_status:data_status,
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


    function viewResidence(){
      $(document).on('click','.viewResidence',function(){
      var residence_id = $(this).attr('id');

        $("#displayResidence").html('');
        
        $.ajax({
          url: 'viewResidenceModal.php',
          type: 'POST',
          dataType: 'html',
          cache: false,
          data: {
            residence_id:residence_id
          },
          success:function(data){
            $("#displayResidence").html(data);
            $("#viewResidenceModal").modal('show');
               
               
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

    function filterData(){
        var status = $("#status").val();
        var residency_type = $("#residency_type").val();
        var age = $("#age").val();
        var pwd = $("#pwd").val();
        var senior = $("#senior").val();
        var first_name = $("#first_name").val();
        var middle_name = $("#middle_name").val();
        var last_name = $("#last_name").val();
        var single_parent = $("#single_parent").val();
        var resident_id = $("#resident_id").val();
        
        var allResidenceTable = $("#allResidenceTable").DataTable({
          processing: true,
          serverSide: true,
          responsive: true,
          searching: false,
          scrollY: '665',
          ajax:{
            url: 'allResidenceTable.php',
            type: 'POST',
            data:{
              // CHANGED: sent as residency_type
              residency_type: residency_type,
              status:status,
              age:age,
              pwd:pwd,
              senior:senior,
              first_name:first_name,
              middle_name:middle_name,
              last_name:last_name,
              single_parent:single_parent,
              resident_id:resident_id,
            },
          },
          dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'d-flex flex-sm-row-reverse flex-column border-top '<'px-2 'p><'px-2'i> <'px-2'l> >",
          order:[],
          columnDefs:[
            {
              orderable: false,
              targets: "_all",
            },
            {
              targets: 8,
              className: "text-center",
            },
            {
              targets: 7,
              className: "text-center",
            },
            {
              targets: 6,
              className: "text-center",
            },
            {
              targets: 5,
              className: "text-center",
            },
          ],
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
              $('#total').text(data.json.total);
              $('.dataTables_paginate').addClass("mt-2 mt-md-2 pt-1");
              $('.dataTables_paginate ul.pagination').addClass("pagination-md");
              $('body').find('.dataTables_scrollBody').addClass("scrollbar");                         
            }
            
      })
    }

    $(document).on('click', '#search',function(){
      var status = $("#status").val();
      // UPDATED: Use residency_type consistently
      var residency_type = $("#residency_type").val();
      var age = $("#age").val();
      var pwd = $("#pwd").val();
      var senior = $("#senior").val();
      var first_name = $("#first_name").val();
      var middle_name = $("#middle_name").val();
      var last_name = $("#last_name").val();
      var resident_id = $("#resident_id").val();
      var single_parent = $("#single_parent").val();
      
      if(status != '' || residency_type != '' || age != '' || first_name != '' ||  middle_name != '' || last_name != '' || pwd != '' || senior != '' || resident_id != '' || single_parent != ''){
        $("#allResidenceTable").DataTable().destroy();
        filterData();
      }
    })

    $(document).on('click', '#reset',function(){
      var status = $("#status").val();
      // UPDATED: Use residency_type consistently
      var residency_type = $("#residency_type").val();
      var age = $("#age").val();
      var pwd = $("#pwd").val();
      var senior = $("#senior").val();
      var first_name = $("#first_name").val()
      var middle_name = $("#middle_name").val()
      var last_name = $("#last_name").val()
      var resident_id = $("#resident_id").val();
      var single_parent = $("#single_parent").val();
      
      if(status != '' || residency_type != '' || age != '' || first_name != '' || middle_name != '' || last_name != '' || pwd != '' || senior != '' || resident_id != '' || single_parent != ''){
        $("#status").val('');
        $("#residency_type").val(''); 
        $("#age").val('');
        $("#pwd").val('');
        $("#senior").val('');
        $("#first_name").val('');
        $("#middle_name").val('');
        $("#last_name").val('');
        $("#resident_id").val('');
        $("#single_parent").val('');
        $("#allResidenceTable").DataTable().destroy();
        filterData();
      }else{
        $("#allResidenceTable").DataTable().destroy();
        filterData();
      }
    })
    
    $(document).on('click', '.pop',function() {
			$('.imagepreview').attr('src', $(this).find('img').attr('src'));
			$('#imagemodal').modal('show');   
		});

    $("#age").on("input", function() {
      if (/^0/.test(this.value)) {
        this.value = this.value.replace(/^0/, "")
      }
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

  $("#first_name, #middle_name, #last_name").inputFilter(function(value) {
  return /^[a-z, ]*$/i.test(value); 
  });
  $("#resident_id").inputFilter(function(value) {
  return /^-?\d*$/.test(value); 
  
  });

</script>


</body>
</html>