
<?php 

include_once '../connection.php';
session_start();


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
                                echo '<img src="../assets/dist/img"' . $user_image . '" class="img-size-50 mr-3 img-circle" alt="User Image">';
                            } else {
                                echo '<img src="../assets/dist/img/image.png" class="img-size-50 mr-3 img-circle" alt="User   Image">';
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
                        <a href="myRecord.php" class="nav-link active">
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
                        <a href="certificate.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
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

    <div class="container-fluid pt-5">
          <input type="hidden" value="<?=$user_id; ?>" id="edit_residence_id">
        <div class="card mt-5">
            <div class="card-header">
              <div class="card-title">
                <h4>Record List</h4>
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

    blotterPersonTable()

    function blotterPersonTable(){

var edit_residence_id = $("#edit_residence_id").val();
var blotterPersonTable = $("#myRecordTable").DataTable({
 
  processing: true,
  serverSide: true,
  responsive: true,
  order:[],
  searching: false,
  info: false,
  paging: false,
  lengthChange: false,
  autoWidth: false,
  columnDefs:[
    {
      targets: '_all',
      orderable: false,
    },

    {
      targets: 0,
     className: 'd-none',
    }
    
  ],
  ajax:{
    url: 'myRecordTable.php',
    type: 'POST',
    data:{
      edit_residence_id:edit_residence_id
    }
  },
        fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          if ( aData[0] == "1" )  {
          $('td', nRow).css('background-color', '#20c997');
        
        }else {
          $('td', nRow).css('background-color', '#000');
          }
          
      },
   
  

})

  
}


$(document).on('click','.viewRecords', function(){

var record_id = $(this).attr('id');


$("#show_records").html('');

  $.ajax({
    url: 'viewRecordsModal.php',
    type: 'POST',
    data:{
      record_id:record_id,
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


  })
</script>
</body>
</html>
