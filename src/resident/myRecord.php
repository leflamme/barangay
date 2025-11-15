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

  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble" src="../assets/dist/img/loader.gif" alt="AdminLTELogo" height="70" width="70">
  </div>

  <nav class="main-header navbar navbar-expand navbar-dark">
      <ul class="navbar-nav">
        <li class="nav-item"><h5><a class="nav-link text-white" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></h5></li>
        <li class="nav-item d-none d-sm-inline-block" style="font-variant: small-caps;"><h5 class="nav-link text-white"><?= $barangay ?></h5>
        <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
        <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= htmlspecialchars($zone) ?></h5></li>
        <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
        <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= htmlspecialchars($district) ?></h5></li>
      </ul>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-user"></i></a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="userProfile.php" class="dropdown-item">
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
  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">

    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="personalInformation.php" class="nav-link"><i class="nav-icon fas fa-address-book"></i><p>Personal Information</p></a></li>
          <li class="nav-item"><a href="drrmPlan.php" class="nav-link"><i class="fas fa-clipboard-list nav-icon text-red"></i><p>Emergency Plan</p></a></li>
          <li class="nav-item"><a href="myRecord.php" class="nav-link active"><i class="nav-icon fas fa-server"></i><p>Blotter Record</p></a></li>
          <li class="nav-item"><a href="certificate.php" class="nav-link"><i class="nav-icon fas fa-file-alt"></i><p>Certificate</p></a></li>
          <li class="nav-item"><a href="changePassword.php" class="nav-link"><i class="nav-icon fas fa-lock"></i><p>Change Password</p></a></li>       
        </ul>
      </nav>
    </div>
  </aside>
  <div class="content-wrapper"  style="background-color: transparent">
    <div class="content  " >

    <div class="container-fluid pt-5">
          <input type="hidden" value="<?=$user_id; ?>" id="edit_residence_id">
        <div class="card mt-5">
            <div class="card-header">
              <div class="card-title">
                <h4>Record List</h4>
              </div>
              <div class="card-tools">
                <button type="button" class="btn btn-primary" id="addRecord" data-toggle="modal" data-target="#blotterRecordModal">
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
          
      </div></div>
    </div>
  <footer class="main-footer">
    <strong>Copyright &copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')); ?></strong>
  </footer>

</div>
<div class="modal hide fade" id="blotterRecordModal" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <form id="addNewRecordForm" method="post">

      <div class="modal-body">
        <div class="container-fluid">

          <div class="row">
                <div class="col-sm-12">
                    <div class="form-group form-group-sm">
                        <label>Complainant Resident</label>
                      <select name="complainant_residence[]" multiple="multiple" id="complainant_residence" class="select2bs4"  style="width: 100%;">
                        <option value="" ></option>
                        <?php 
                          $sql_residence_id = "SELECT
                          residence_information.residence_id,
                          residence_information.first_name, 
                          residence_information.middle_name,
                          residence_information.last_name,
                          residence_information.image,   
                          residence_information.image_path
                          FROM residence_information
                          INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id WHERE archive = 'NO'
                         ORDER BY last_name ASC ";
                          $query_residence_id = $con->query($sql_residence_id) or die ($con->error);
                          while($row_residence_id = $query_residence_id->fetch_assoc()){
                            if($row_residence_id['middle_name'] != ''){
                              $middle_name = $row_residence_id['middle_name'][0].'.'.' '; 
                            }else{
                              $middle_name = $row_residence_id['middle_name'].' '; 
                            }
                            ?>
                              <option value="<?= $row_residence_id['residence_id'] ?>" <?php 
                              if($row_residence_id['image_path'] != '' || $row_residence_id['image_path'] != null || !empty($row_residence_id['image_path'])){
                                  echo 'data-image="'.$row_residence_id['image_path'].'"';
                              }else{
                                echo 'data-image="../assets/dist/img/blank_image.png"';
                              }
                             
                            ?> >
                            <?= $row_residence_id['last_name'] .' '. $row_residence_id['first_name'] .' '.  $middle_name  ?></option>
                            <?php
                          }   
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-12 ">
                    <div class="form-group form-group-sm">
                      <label>Complainant Not Resident</label>
                      <textarea name="complainant_not_residence" id="complainant_not_residence" cols="57"  class="bg-transparent text-white form-control"></textarea>
                    </div>
                  </div>
                  <div class="col-sm-12 ">
                    <div class="form-group form-group-sm">
                      <label>Complainant Statement</label>
                      <textarea name="complainant_statement" id="complainant_statement" cols="57" rows="3" class="bg-transparent text-white form-control"></textarea>
                    </div>
                  </div>
                  <div class="col-sm-12 ">
                    <div class="form-group form-group-sm">
                      <label>Respondent</label>
                        <input name="respodent" id="respodent"  class=" form-control">
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group form-group-sm">
                        <label>Person Involved Resident</label>
                      <select name="person_involed[]" multiple="multiple" id="person_involed" class="select2bs4"  style="width: 100%;">
            
                  
                      <option value="" ></option>
                        <?php 
                          $sql_person_add = "SELECT
                          residence_information.residence_id,
                          residence_information.first_name, 
                          residence_information.middle_name,
                          residence_information.last_name,
                          residence_information.image,   
                          residence_information.image_path
                          FROM residence_information
                          INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id WHERE archive = 'NO'
                         ORDER BY last_name ASC ";
                          $query_person_add = $con->query($sql_person_add) or die ($con->error);
                          while($row_person_add = $query_person_add->fetch_assoc()){
                            if($row_person_add['middle_name'] != ''){
                              $middle_name_add = $row_person_add['middle_name'][0].'.'.' '; 
                            }else{
                              $middle_name_add = $row_person_add['middle_name'].' '; 
                            }
                            ?>
                              <option value="<?= $row_person_add['residence_id'] ?>" <?php 
                              if($row_person_add['image_path'] != '' || $row_person_add['image_path'] != null || !empty($row_person_add['image_path'])){
                                  echo 'data-image="'.$row_person_add['image_path'].'"';
                              }else{
                                echo 'data-image="../assets/dist/img/blank_image.png"';
                              }
                             
                            ?> >
                            <?= $row_person_add['last_name'] .' '. $row_person_add['first_name'] .' '.  $middle_name_add  ?></option>
                            <?php
                          }   
                        ?>


                      </select>
                    </div>
                  </div>
                  <div class="col-sm-12 ">
                    <div class="form-group form-group-sm">
                      <label>Person Involved Not Resident</label>
                      <textarea name="person_involevd_not_resident" id="person_involevd_not_resident" cols="57"  class="bg-transparent text-white form-control"></textarea>
                    </div>
                  </div> 
                  <div class="col-sm-12 ">
                    <div class="form-group form-group-sm">
                      <label>Person Involved Statement</label>
                      <textarea name="person_statement" id="person_statement" cols="57" rows="3" class="bg-transparent text-white form-control"></textarea>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group form-group-sm">
                      <label>Location of Incident</label>
                        <input name="location_incident" id="location_incident"  class=" form-control">
                    </div>
                  </div>   
                  <div class="col-sm-6">
                    <div class="form-group form-group-sm">
                      <label>Date of Incident</label>
                        <input type="datetime-local" name="date_of_incident" id="date_of_incident"  class=" form-control">
                    </div>
                  </div>  
                  <div class="col-sm-6">
                    <div class="form-group form-group-sm">
                      <label>Incident</label>
                        <input name="incident" id="incident"  class=" form-control">
                    </div>
                  </div>   
                  <div class="col-sm-6">
                    <div class="form-group form-group-sm">
                      <label>Status</label>
                        <select name="status" id="status" class="form-control">
                          <option value="NEW">NEW</option>
                          <option value="ONGOING">ONGOING</option>
                        </select>
                    </div>
                  </div> 
                  <div class="col-sm-6">
                    <div class="form-group form-group-sm">
                      <label>Date Reported</label>
                        <input  type="datetime-local" name="date_reported" id="date_reported"  class=" form-control">
                    </div>
                  </div>   
                  <div class="col-sm-6">
                    <div class="form-group form-group-sm">
                      <label>Remarks</label>
                        <select name="remarks" id="remarks" class="form-control">
                          <option value="OPEN">OPEN</option>
                          <option value="CLOSED">CLOSED</option>
                        </select>
                    </div>
                  </div>    
                  
         

          </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-black elevation-5 px-3" data-dismiss="modal"><i class="fas fa-times"></i> CLOSE</button>
        <button type="submit" class="btn btn-primary elevation-5 px-3 btn-flat"><i class="fa fa-book-dead"></i> NEW RECORD</button>
      </div>
      
      </form>
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

<div id="show_residence"></div>
<div id="show_records"></div>

<script>
  $(document).ready(function(){

    // Refresh button functionality
    $('#refreshRecords').on('click', function() {
      $('#myRecordTable').DataTable().ajax.reload();
    });

    // Handle stacking modals
    $(document).on('show.bs.modal', '.modal', function () {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

    // --- START: JS from blotterRecord.php (FOR NEW RECORD MODAL) ---

    // Form validation and submit
    $(function () {
        $.validator.setDefaults({
          submitHandler: function (form) {
     
          var complainant = $("#complainant_residence").val();
          var complainant_not_residence = $("#complainant_not_residence").val();
          var complainant_statement = $("#complainant_statement").val();
          var person_statement = $("#person_statement").val();
          var person_involed = $("#person_involed").val();
          var person_involevd_not_resident = $("#person_involevd_not_resident").val();
          
            if(complainant == '' && complainant_not_residence == ''){
              Swal.fire({
                title: '<strong class="text-danger">Ooppss..</strong>',
                type: 'error',
                html: '<b>Complainant is Required<b>',
                width: '400px',
                confirmButtonColor: '#6610f2',
              })
              return false;
            }
            
            if(complainant_statement == ''){
              Swal.fire({
                title: '<strong class="text-danger">Ooppss..</strong>',
                type: 'error',
                html: '<b>Complainant is Statement Required<b>',
                width: '400px',
                confirmButtonColor: '#6610f2',
              })
              return false;
            }

            if(person_involed == '' && person_involevd_not_resident == ''){
              Swal.fire({
                title: '<strong class="text-danger">Ooppss..</strong>',
                type: 'error',
                html: '<b>Person Involved is Required<b>',
                width: '400px',
                confirmButtonColor: '#6610f2',
              })
              return false;
            }

            if(person_statement == ''){
              Swal.fire({
                title: '<strong class="text-danger">Ooppss..</strong>',
                type: 'error',
                html: '<b>Person Involved Statement is Required<b>',
                width: '400px',
                confirmButtonColor: '#6610f2',
              })
              return false;
            }

            $.ajax({
              url: 'addNewBlotterRecord.php', // This file must exist in the resident folder
              type: 'POST',
              data: $(form).serialize(),
              cache: false,
              success:function(){
                Swal.fire({
                  title: '<strong class="text-success">SUCCESS</strong>',
                  type: 'success',
                  html: '<b>Added Record Blotter has Successfully<b>',
                  width: '400px',
                  confirmButtonColor: '#6610f2',
                  allowOutsideClick: false,
                  showConfirmButton: false,
                  timer: 2000,
                }).then(()=>{
                  $("#addNewRecordForm")[0].reset();
                  $('#myRecordTable').DataTable().ajax.reload(); // MODIFIED
                  $("#blotterRecordModal").modal('hide');
                  $("#complainant_residence").val([]).trigger("change")
                  $("#person_involed").val([]).trigger("change")
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
        });

      $('#addNewRecordForm').validate({
        ignore: "",
        rules: {
          date_reported: {
            required: true,
          },
          incident: {
            required: true,
          },
          date_of_incident: {
            required: true,
          },
        },
        messages: {
          date_reported: {
            required: "Please provide a Date Reported is Required",
          },
          incident: {
            required: "Incident is Required",
          },
          date_of_incident: {
            required: "Date Incident is Required",
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
    });

    // Reset form on modal open
    $("#addRecord").on('click',function(){
      $("#addNewRecordForm")[0].reset();
      $(".select2-selection__choice").css('display', 'none')
    });

    // Select2 Initializer
    function formatState (opt) {
        if (!opt.id) {
            return opt.text.toUpperCase();
        } 
        var optimage = $(opt.element).attr('data-image'); 
        if(!optimage){
          return opt.text.toUpperCase();
        } else {                    
            var $opt = $(
              '<span><img class="img-circle  pb-1" src="' + optimage + '" width="20px" /> ' + opt.text.toUpperCase() + '</span>'
            );
            return $opt;
        }
    };

    $('#complainant_residence').select2({
      templateResult: formatState,
      templateSelection: formatState,
      theme: 'bootstrap4',
      dropdownParent: $('#blotterRecordModal'), // Fix for select2 in modal
      language: {
          noResults: function (params) {
            return "No Record";
          }
        },
    });

    $('#person_involed').select2({
      templateResult: formatState,
      templateSelection: formatState,
      theme: 'bootstrap4',
      dropdownParent: $('#blotterRecordModal'), // Fix for select2 in modal
      language: {
          noResults: function (params) {
            return "No Record";
          }
        },
    });

    // Show person info on select
    $("#complainant_residence, #person_involed").on('select2:select', function(e){
      var residence_id = e.params.data.id;
      $("#show_residence").html('');
      if(residence_id != ''){
        $.ajax({
          url: 'showResidenceInfo.php', // This file must exist in the resident folder
          type: 'POST',
          data:{
            residence_id:residence_id,
          },
          cache: false,
          success:function(data){
            $("#show_residence").html(data);
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
      }
    });

    // Filter person involved list based on complainant
    $(document).on('change', '#complainant_residence',function(){
      var subject = [];
        subject.push($(this).val());
      var selected_values = subject.join(",");
       console.log(selected_values);

      $.ajax({
        url: 'showPerson.php', // This file must exist in the resident folder
            type: 'POST',
            data:  {
              selected_values:selected_values
            },
          cache: false,
          success:function(data){
            $("#person_involed").html(data);
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
    });

    // --- END: JS from blotterRecord.php ---


    // --- START: JS for resident's existing functions ---

    // Load main table
    blotterPersonTable();

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

    // View record details
    $(document).on('click','.viewRecords', function(){
      var record_id = $(this).attr('id');
      $("#show_records").html('');

      $.ajax({
        url: 'viewRecordsModal.php',
        type: 'POST',
        data: {
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
    });

    // --- END: JS for resident's existing functions ---

  });
</script>
</body>
</html>