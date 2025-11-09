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
  <title></title>

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
  
    .wrapper{
      width: 100%;
      height: auto;
      animation-name: example;
      animation-duration: 5s;   
    }


@keyframes example {
  from {opacity: 0;}
  to {opacity: 1.5;}
}

.dataTables_wrapper .dataTables_paginate .page-link {
      
      border: none;
  }
  .dataTables_wrapper .dataTables_paginate .page-item .page-link{
      color: #222 !important;
      border-color: transparent;
      
      
    }
 
  .dataTables_wrapper .dataTables_paginate .page-item.active .page-link{
    color: #fff !important;
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
    color: #222 !important;

  }
  .dataTables_length span{
    color: #222 !important;
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
      color: #222 !important;
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
    color: #495057 !important; /* Changed from #fff to default text color */
  }
  #tableRequest_filter{
      display: none;
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
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="#" class="dropdown-item">
                        <div class="media">
                            <?php 
                            if ($user_image != '' || $user_image != null || !empty($user_image)) {
                                echo '<img src="../assets/dist/img/' . $user_image . '" class="img-size-50 mr-3 img-circle" alt="User Image">';
                            } else {
                                echo '<img src="../assets/dist/img/image.png" class="img-size-50 mr-3 img-circle" alt="User Image">';
                            }
                            ?>
                            <div class="media-body">
                                <h3 class="dropdown-item-title py-3">
                                    <?= ucfirst($first_name_user) . ' ' . ucfirst($last_name_user) ?>
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

    <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
      <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">

      <!-- Sidebar -->
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
                        <a href="myProfile.php" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>My Profile</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="personalInformation.php" class="nav-link">
                            <i class="nav-icon fas fa-address-book"></i>
                            <p>Personal Information</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="myRecord.php" class="nav-link">
                            <i class="nav-icon fas fa-server"></i>
                            <p>Blotter Record</p>
                        </a>
                    </li>

                    <li class="nav-item">
                      <a href="drrmPlan.php" class="nav-link">
                        <i class="fas fa-clipboard-list nav-icon text-red"></i>
                        <p>Emergency Plan</p>
                      </a>
                    </li>

                    <li class="nav-item">
                        <a href="changePassword.php" class="nav-link">
                            <i class="nav-icon fas fa-lock"></i>
                            <p>Change Password</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="certificate.php" class="nav-link active">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Certificate</p>
                        </a>
                    </li>
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
  



    <div class="container-fluid " >


                  <div class="card mt-5">
                      <div class="card-header">
                        <div class="card-title">
                          <h4 style="font-variant: small-caps">List of Request <span class="badge bg-lime" id="total"></span></h4>
                        </div>
                        <div class="card-tools">
                          <button type="button" class="btn bg-black elevation-5 px-3 btn-flat newRequest" data-toggle="modal" data-target="#newRequestModal"><i class="fas fa-plus"></i> New Request</button>
                        </div>
                      </div>
                    <div class="card-body">
                            <div class="row">
                              <div class="col-sm-6">
                                <div class="input-group input-group-md mb-3">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text bg-indigo">SEARCH</span>
                                  </div>
                                  <input type="text" class="form-control" id="searching" autocomplete="off">
                                  <div class="input-group-append">
                                    <span class="input-group-text bg-red" id="reset" type="button"><i class="fas fa-undo"></i>   RESET</span>
                                  </div>
                                </div>
                              </div>
                            </div>
                              <div class="table-responsive">
                              <table class="table table-striped table-hover text-sm" id="tableRequest">
                                <thead>
                                  <tr>
                                    <th>Purpose</th>
                                    <th>
                                      <select name="date_request" id="date_request" class="custom-select custom-select-sm">
                                          <option value="">Date Request</option>
                                              <?php 
                                              $blank_request = '';
                                              $sql_date_request = "SELECT date_request FROM certificate_request WHERE residence_id = ? AND date_request != ? GROUP BY date_request";
                                              $stmt_date_request = $con->prepare($sql_date_request) or die ($con->error);
                                              $stmt_date_request->bind_param('ss',$user_id,$blank_request);
                                              $stmt_date_request->execute();
                                              $result_date_request = $stmt_date_request->get_result();
                                              while($row_date_request = $result_date_request->fetch_assoc()){
                                                  echo '<option value="'.$row_date_request['date_request'].'">'.date("m/d/Y", strtotime($row_date_request['date_request'])).'</option>';
                                              }
                                              
                                              ?>
                                      </select>
                                    </th>
                                    <th>
                                        <select name="date_issued" id="date_issued" class="custom-select custom-select-sm">
                                                <option value="">Date Issued</option>
                                              <?php 
                                              $blank_issued = '';
                                              $sql_date_issued = "SELECT date_issued FROM certificate_request WHERE residence_id = ? AND date_issued != ? GROUP BY date_issued";
                                              $stmt_date_issued = $con->prepare($sql_date_issued) or die ($con->error);
                                              $stmt_date_issued->bind_param('ss',$user_id,$blank_issued);
                                              $stmt_date_issued->execute();
                                              $result_date_issued = $stmt_date_issued->get_result();
                                              while($row_date_issued = $result_date_issued->fetch_assoc()){
                                                  echo '<option value="'.$row_date_issued['date_issued'].'">'.date("m/d/Y", strtotime($row_date_issued['date_issued'])).'</option>';
                                              }
                                              
                                              ?>
                                      </select>
                                    </th>
                                    <th>
                                        <select name="date_expired" id="date_expired" class="custom-select custom-select-sm">
                                                <option value="">Date Expired</option>
                                              <?php 
                                              $blank_expired = '';
                                              $sql_date_expired = "SELECT date_expired FROM certificate_request WHERE residence_id = ? AND date_expired != ? GROUP BY date_expired";
                                              $stmt_date_expired = $con->prepare($sql_date_expired) or die ($con->error);
                                              $stmt_date_expired->bind_param('ss',$user_id,$blank_expired);
                                              $stmt_date_expired->execute();
                                              $result_date_expired = $stmt_date_expired->get_result();
                                              while($row_date_expired = $result_date_expired->fetch_assoc()){
                                                  echo '<option value="'.$row_date_expired['date_expired'].'">'.$row_date_expired['date_expired'].'</option>';
                                              }
                                              
                                              ?>
                                      </select>
                                    </th>
                                    <th>
                                        <select name="status" id="status" class="custom-select custom-select-sm">
                                                <option value="">Status</option>
                                              <?php 
                                            
                                              $sql_status = "SELECT status FROM certificate_request WHERE residence_id = ? GROUP BY status";
                                              $stmt_status = $con->prepare($sql_status) or die ($con->error);
                                              $stmt_status->bind_param('s',$user_id);
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
</div><!--/. container-fluid -->

    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <!-- Modal -->
  <div class="modal fade" id="newRequestModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form id="requestForm" method="post">

          <div class="modal-header">
              <h5 class="modal-title">Fill-up Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              <input type="hidden" name="user_id" id="user_id" value="<?= $user_id;?>">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Purpose</label>
                  <select name="purpose" id="purpose" class="form-control text-uppercase" required>
                    <option value="Barangay Clearance">Barangay Clearance</option>
                    <option value="Barangay Certificate of Residency">Barangay Certificate of Residency</option>
                    <option value="Barangay Certificate of Indigency">Barangay Certificate of Indigency</option>
                    <option value="Barangay Business Clearance/Permit">Barangay Business Clearance/Permit</option>
                    <option value="Barangay Certificate for Solo Parents">Barangay Certificate for Solo Parents</option>
                    <option value="Barangay Certificate of Unemployment">Barangay Certificate of Unemployment</option>
                    <option value="Barangay Certificate of First-time Job Seekers">Barangay Certificate of First-time Job Seekers</option>
                    <option value="Barangay Certificate of No Pending Case/No Derogatory Record">Barangay Certificate of No Pending Case/No Derogatory Record</option>
                    <option value="Others">Others</option>
                  </select>
                  <!-- Others input, hidden by default -->
                  <div id="otherPurposeDiv" style="display:none;" class="mt-2">
                    <input type="text" name="other_purpose" id="other_purpose" class="form-control" placeholder="Please specify other certificate type">
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-black btn-flat elevation-5 px-3" data-dismiss="modal"><i class="fas fa-times"></i> CLOSE</button>
          <button type="submit" class="btn btn-success btn-flat elevation-5 px-3"><i class="fas fa-sign-in-alt"></i> SUBMIT</button>
        </div>

        </form>
      </div>
    </div>
  </div>
 
  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')); ?></strong>
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

<div id="show_status"></div>

<script>
  $(document).ready(function(){

    tableRequest()

    function tableRequest(){

      var date_request =  $("#date_request").val();
      var date_issued  =  $("#date_issued").val();
      var date_expired =  $("#date_expired").val();
      var status       =  $("#status").val();
      var user_id      = $("#user_id").val();
      var tableRequest = $("#tableRequest").DataTable({
        processing: true,
        serverSide: true,
        order:[],
        autoWidth: false,
        ordering: false,
        columnDefs:[{
              targets: 5,
              className: 'text-center'
        }],
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'d-flex flex-sm-row-reverse flex-column border-top '<'px-2 'p><'px-2'i> <'px-2'l> >",
            pagingType: "full_numbers",
            language: {
              paginate: {
                next: '<i class="fas fa-angle-right text-dark"></i>',
                previous: '<i class="fas fa-angle-left text-dark"></i>', 
                first: '<i class="fa fa-angle-double-left text-dark"></i>',
                last: '<i class="fa fa-angle-double-right text-dark"></i>'        
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
            url: 'userRequestTable.php',
            type: 'POST',
            data:{
              user_id:user_id,
              date_request:date_request,
              date_issued:date_issued,
              date_expired:date_expired,
              status:status,
            }
        },
            drawCallback:function(data)  {
              $('#total').text(data.json.total);
              $('.dataTables_paginate').addClass("mt-2 mt-md-2 pt-1");
              $('.dataTables_paginate ul.pagination').addClass("pagination-md");   
              $('[data-toggle="tooltip"]').tooltip();
                               
            },
       
      })
      $('#searching').keyup(function(){
        tableRequest.search($(this).val()).draw() ;
        })

    }
    

    $(document).on('change',"#date_request, #date_issued, #date_expired, #status",function(){
      $("#tableRequest").DataTable().destroy();
      tableRequest()
      $('#searching').keyup();
    })

    



  $("#requestForm").submit(function(e){
    e.preventDefault();

    Swal.fire({
        title: '<strong class="text-info">ARE YOU SURE?</strong>',
        html: "<b>You want Submit this Request?</b>",
        type: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        allowOutsideClick: false,
        confirmButtonText: 'Yes, Submit it!',
        width: '400px',
      }).then((result) => {
        if (result.value) {
            $.ajax({
              url: 'requestCertificate.php',
              type: 'POST',
              data: $(this).serialize(),
              success:function(){

                  Swal.fire({
                    title: '<strong class="text-success">Success</strong>',
                    type: 'success',
                    html: '<b>Request Submitted Successfully</b>',
                    width: '400px',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    timer: 2000
                  }).then(()=>{
                    $("#requestForm")[0].reset();
                    // Reinitialize Select2 after reset to show default value
                    $('#purpose').val('Barangay Clearance').trigger('change');
                    $("#tableRequest").DataTable().ajax.reload();
                    $("#newRequestModal").modal('hide')
                  })

                  
              }
            }).fail(function(){
              Swal.fire({
                title: '<strong class="text-danger">Ooppss..</strong>',
                type: 'error',
                html: '<b>Something went wrong with ajax!</b>',
                width: '400px',
                confirmButtonColor: '#6610f2',
              })
            })
        }
      })

  })



    $(document).on('click','#reset',function(){

        if($("#date_request").val() != '' ||  $("#date_issued").val() !=  '' || $("#date_expired").val() != '' ||  $("#status").val() != '' ||  $("#searching").val() != ''){
            $("#date_request").val('');
            $("#date_issued").val('');
            $("#date_expired").val('');
            $("#status").val('');
            $("#searching").val('');
            $("#tableRequest").DataTable().destroy();
            tableRequest();
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
            html: '<b>Something went wrong with ajax!</b>',
            width: '400px',
            confirmButtonColor: '#6610f2',
          })
        })

    })




  })
</script>

<!-- FIXED: Removed the inputFilter script that was conflicting with Select2 -->
<!-- FIXED: Moved Select2 initialization to modal shown event -->

<script>
$(document).ready(function() {
  // Initialize Select2 when modal is shown
  $('#newRequestModal').on('shown.bs.modal', function () {
    // Destroy if already initialized
    if ($('#purpose').data('select2')) {
      $('#purpose').select2('destroy');
    }
    
    // Initialize Select2
    $('#purpose').select2({
      minimumResultsForSearch: -1,
      width: '100%',
      dropdownParent: $('#newRequestModal')
    });
    
    // Set default value and trigger change
    $('#purpose').val('Barangay Clearance').trigger('change');
    
    // Handle "Others" toggle
    $('#purpose').off('change.purpose').on('change.purpose', function() {
      if ($(this).val() === 'Others') {
        $('#otherPurposeDiv').show();
        $('#other_purpose').prop('required', true);
      } else {
        $('#otherPurposeDiv').hide();
        $('#other_purpose').prop('required', false);
        $('#other_purpose').val('');
      }
    });
  });
  
  // Destroy Select2 when modal is hidden to prevent memory leaks
  $('#newRequestModal').on('hidden.bs.modal', function () {
    if ($('#purpose').data('select2')) {
      $('#purpose').select2('destroy');
    }
  });
});
</script>


</body>
</html>