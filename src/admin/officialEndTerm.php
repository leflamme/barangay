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
    background-color: #E41749;
    color: #fff !important;
    border-radius: 4px;
    margin: 0 2px;
    font-size: 14px;
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

  .dataTables_info {
    color: #050C9C;
    font-weight: 500;
    font-size: 13px;
  }

  @media (max-width: 768px) {
    .table-responsive {
      overflow-x: auto;
    }
  }

  /* Table Background and Text */
#endOfficialTable {
  background-color: #ffffff;
  color: #000;
}

#endOfficialTable thead {
  background-color: #050C9C;
  color: #ffffff;
}

#endOfficialTable tbody tr:hover {
  background-color:rgb(0, 71, 171);
}

/* DataTables Search Field */
.dataTables_filter label,
.dataTables_length label {
  color: #000;
  font-weight: 500;
}

/* Force white search box with dark text */
.dataTables_filter input[type="search"] {
  background-color: #ffffff !important;
  color: #000000 !important;
  border: 1px solid #ccc !important;
  border-radius: 6px;
  padding: 6px 10px !important;
}

/* Label next to search */
.dataTables_filter label {
  color: #000000 !important;
  font-weight: 500;
}

/* Position dropdown */
#position {
  background-color: #ffffff;
  color: #000;
  border: 1px solid #ccc;
}

/* Fix select2 if used */
.select2-container--default .select2-selection--single {
  background-color: #ffffff !important;
  color: #000 !important;
  border: 1px solid #ccc !important;
}

/* Table Pagination */
.dataTables_wrapper .dataTables_paginate .page-item .page-link {
  background-color: #E41749;
  color: #fff !important;
  border-radius: 5px;
}

.dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
  background-color: #3572EF !important;
  color: #fff !important;
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

.dataTables_wrapper .dataTables_paginate .page-item .page-link:hover {
  background-color: #3572EF !important; /* deeper blue on hover */
  color: #fff !important;
}

/* Active page */
.dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
  background-color: #050C9C !important;
  color: #fff !important;
  border-color: #050C9C !important;
}


</style>
 
 
</head>
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
          <li class="nav-item menu-open">
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
                <a href="officialEndTerm.php" class="nav-link bg-indigo">
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
      <div class="card-body">
          <fieldset>
            <legend>NUMBER OF OFFICIAL <span id="total"></span></legend>
              <div class="table-responsive">
                <table class="table table-striped table-hover " id="endOfficialTable" style="width: 100%;">
                  <thead class="bg-black text-uppercase">
                  <tr>
                    <th>Image</th>
                
                    <th>
                      <select name="position" id="position" class="form-control form-control-sm text-uppercase">
                      <option value="">All Position</option>
                        <?php 
                        
                        $sql_position = "SELECT position_id, position FROM position";
                        $stmt = $con->prepare($sql_position) or die ($con->error);
                        $stmt->execute();
                        $result_position = $stmt->get_result();
                        while($row_position = $result_position->fetch_assoc()){
                          echo ' <option value="'.$row_position['position_id'].'">'.$row_position['position'].'</option>';
                        }
                        
                        ?>
                       
                      
                      </select>
                    </th>
                    <th>Official Number</th>
                    <th>Name</th>
                    <th>pwd</th>
                     <th>Single Parent</th>
                    <th>Voters</th>
                    <th>
                      Status
                    </th>
                    <th class="text-center">Action</th>
                  </tr>
                  </thead>

                </table>
              </div>
            
          </fieldset>
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
</div>
<!-- ./wrapper -->

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
<script src="../assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>




<script>
  $(document).ready(function(){


    endOfficialTable()
    deleteOfficial()
    $(document).on('change', '#position',function(){
      var position = $(this).val();
      $('#endOfficialTable').DataTable().destroy();
      if(position != ''){
        
        endOfficialTable()
      }else{
       
        endOfficialTable();
      }

    })

    function endOfficialTable(){
      var position = $("#position").val();
      var endOfficialTable = $("#endOfficialTable").DataTable({

        processing: true,
          serverSide: true,
          scrollY: '665',
          responsive: true,
          autoWidth: false,
          ajax:{
              url: 'endOfficialTable.php',
              type: 'POST',
              data:{
                    position:position
                  },
          },
          order:[],
        columnDefs:[
          {
            targets: 8,
            orderable: false,
            className: 'text-center',
          },
          {
            targets: 0,
            orderable: false,
           
          },
          {
            targets: 1,
            orderable: false,
            className: 'text-center text-lg text-uppercase',
          },
          {
            targets: 5,
            orderable: false,
            className: 'text-center',
           
          },
          {
            targets: 6,
            orderable: false,
            className: 'text-center',
           
          },
          {
            targets: 7,
            orderable: false,
            className: 'text-center',
           
          },
        ],
        dom: "<'row'<'col-sm-12 col-md-12'f><'col-sm-12 col-md-6'>>" +
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
              search: 'SEARCH:',
            },
            drawCallback:function(data)  {
              $('#total').text(data.json.total);
              $('.dataTables_paginate').addClass("mt-2 mt-md-2 pt-1");
              $('.dataTables_paginate ul.pagination').addClass("pagination-md");   
              $('body').find('.dataTables_scrollBody').addClass("scrollbar");                        
            },

      })

    

        
    
    }

    function deleteOfficial(){
  $(document).on('click','.deleteOfficial',function(){
    var official_id = $(this).attr('id');
    Swal.fire({
        title: '<strong class="text-danger">ARE YOU SURE?</strong>',
        html: "<b>You want Undelete this Official?</b>",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        allowOutsideClick: false,
        confirmButtonText: 'Yes, Undelete it!',
        width: '400px',
      }).then((result) => {
        if (result.value) {
          $.ajax({
            url: 'unDeleteOfficial.php',
            type: 'POST',
            data: {
              official_id:official_id,
            },
            cache: false,
            success:function(data){

                if(data == 'error'){

                  Swal.fire({
                      title: '<strong class="text-danger">ERROR</strong>',
                      type: 'error',
                      html: '<b>Position Limited<b>',
                      width: '400px',
                      allowOutsideClick: false,
                    })

                }else{

                  Swal.fire({
                    title: '<strong class="text-success">Success</strong>',
                    type: 'success',
                    html: '<b>Delete Official has Successfully<b>',
                    width: '400px',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    timer: 2000
                  }).then(()=>{
                    $("#endOfficialTable").DataTable().ajax.reload();
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
}

    $(document).on('click', '.pop',function() {
			$('.imagepreview').attr('src', $(this).find('img').attr('src'));
			$('#imagemodal').modal('show');   
		});

  
  })
</script>


</body>
</html>
