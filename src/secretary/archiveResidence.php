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
  <title>List: Archived Residents</title>
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
     <link rel="stylesheet" href="../assets/dist/css/secretary.css?v=2">

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
  font-size: 1.2em;
}

.input-group-text {
  background-color: #050C9C !important;
  color: white !important;
  font-weight: 500;
  border-radius: 0.375rem 0 0 0.375rem !important;
}

.input-group .form-control {
  background-color: #ffffff !important;
  color: #000000 !important;
  border: 1px solid #ccc !important;
  border-radius: 0 0.375rem 0.375rem 0 !important;
  box-shadow: none !important;
}

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

.table {
  background-color: #F2F6FF;
  color: #000;
  border-collapse: collapse;
}

.table thead {
  background-color: #050C9C;
  color: #ffffff;
  font-weight: 600;
}

.table-striped tbody tr:nth-of-type(odd) {
  background-color: #F2F6FF;
}

.table-hover tbody tr:hover {
  background-color: #A7E6FF;
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

.dataTables_wrapper .dataTables_paginate .page-item .page-link,
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

.dataTables_info {
  font-size: 13px;
  font-weight: 500;
  color: #050C9C;
}

.select2-container--default .select2-selection--single {
  background-color: #ffffff !important;
  color: #000 !important;
  border: 1px solid #ccc !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
  color: #000000;
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
  .table-responsive {
    overflow-x: auto;
  }
  .table td,
  .table th {
    white-space: nowrap;
  }
}

.dataTables_filter input[type="search"] {
  background-color: #ffffff !important;
  color: #000000 !important;
  border: 1px solid #ccc !important;
  border-radius: 6px !important;
  padding: 6px 10px !important;
}

#archiveResidenceTable {
  background-color: #FAF9F6;
  color: #000;
}

#archiveResidenceTable thead {
  background-color: #050C9C;
  color: #ffffff;
  font-weight: 600;
}

#archiveResidenceTable tbody tr:nth-of-type(odd),
#archiveResidenceTable tbody tr:nth-of-type(even) {
  background-color: #FAF9F6;
}

#archiveResidenceTable tbody tr:hover {
  background-color: #0047ab;
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
                <a href="allOfficial.php" class="nav-link ">
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
          <li class="nav-item menu-open">
            <a href="#" class="nav-link">
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
                <a href="allResidence.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>All Residence</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="archiveResidence.php" class="nav-link bg-indigo">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Archive Residence</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item"><a href="editRequests.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>Edit Requests</p></a></li>
       
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
                  <a href="drrmEvacuation.php" class="nav-link">
                    <i class="fas fa-house-damage nav-icon text-red"></i>
                    <p>Evacuation Center</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="report.php" class="nav-link">
                    <i class="nav-icon fas fa-bookmark"></i>
                    <p>
                      Masterlist Report
                    </p>
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
          <li class="nav-item">
            <a href="blotterRecord.php" class="nav-link">
              <i class="nav-icon fas fa-clipboard"></i>
              <p>
                Blotter Record
              </p>
            </a>
          </li>
         
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
            <legend>NUMBER OF ARCHIVE RESIDENCE <span id="total"></span></legend>
          
                  <div class="row mb-2">
                    <div class="col-sm-4">
                      <div div class="input-group input-group-md mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" >FIRST NAME</span>
                        </div>
                        <input type="text" class="form-control" id="first_name" name="first_name">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div div class="input-group input-group-md mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" >MIDDLE NAME</span>
                        </div>
                        <input type="text" class="form-control" id="middle_name" name="middle_name">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div div class="input-group input-group-md mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" >LAST NAME</span>
                        </div>
                        <input type="text" class="form-control" id="last_name" name="last_name">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div div class="input-group input-group-md mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" >RESIDENT NUMBER</span>
                        </div>
                        <input type="text" class="form-control" id="resident_id" name="resident_id">
                      </div>
                    </div>
                    
                    <div class="col-sm-4 text-center">
                      <button type="button" class="btn btn-warning  elevation-5 px-3 text-white" id="search"><i class="fas fa-search"></i> SEARCH</button>
                      <button type="button" class="btn btn-danger  elevation-5 px-3 text-white" id="reset"><i class="fas fa-undo"></i> RESET</button>
                    </div>

                    <div class="col-sm-4">
                      <div class="input-group input-group-md mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text">RESIDENCY TYPE</span>
                        </div>
                        <select name="residency_type" id="residency_type" class="form-control">
                          <option value="">--SELECT TYPE--</option>
                          <option value="RESIDENT">RESIDENT</option>
                          <option value="TENANT">TENANT</option>
                        </select>
                      </div>
                    </div>
                  </div>
              
            <div class="table-responsive">
            <table class="table table-striped table-hover " id="archiveResidenceTable">
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
            </div>
            
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

    archiveResidence();
    viewResidence();
    unArchiveResidence();

    $(document).on('click', '#search',function(){
      var first_name = $("#first_name").val();
      var middle_name = $("#middle_name").val();
      var last_name = $("#last_name").val();
      var resident_id = $("#resident_id").val();
      var residency_type = $("#residency_type").val(); // NEW

      // Check if any filter is set
      if(first_name != '' ||  middle_name != '' || last_name != '' || resident_id != '' || residency_type != ''){
        $("#archiveResidenceTable").DataTable().destroy();
        archiveResidence();
      }
    })
    $(document).on('click', '#reset',function(){
      var first_name = $("#first_name").val();
      var middle_name = $("#middle_name").val();
      var last_name = $("#last_name").val();
      var resident_id = $("#resident_id").val();
      var residency_type = $("#residency_type").val(); // NEW

      if(first_name != '' || middle_name != '' || last_name != '' || resident_id != '' || residency_type != ''){
        $("#first_name").val('');
        $("#middle_name").val('');
        $("#last_name").val('');
        $("#resident_id").val('');
        $("#residency_type").val(''); // RESET NEW FIELD
        $("#archiveResidenceTable").DataTable().destroy();
        archiveResidence();
      }else{
        $("#archiveResidenceTable").DataTable().destroy();
        archiveResidence();
      }
    })

    $(document).on('click', '.pop',function() {
			$('.imagepreview').attr('src', $(this).find('img').attr('src'));
			$('#imagemodal').modal('show');   
		});


    function archiveResidence(){
      var first_name = $("#first_name").val();
      var middle_name = $("#middle_name").val();
      var last_name = $("#last_name").val();
      var resident_id = $("#resident_id").val();
      var residency_type = $("#residency_type").val(); // NEW VARIABLE

      var archiveResidenceTable = $("#archiveResidenceTable").DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      scrollY: '650',
      ajax:{
        url: 'archiveResidenceTable.php',
        type: 'POST',
        data:{
          first_name:first_name,
          middle_name:middle_name,
          last_name:last_name,
          resident_id:resident_id,
          residency_type:residency_type, // PASS TO BACKEND
        }
      },
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
              targets: 5,
              className: "text-center",
            },
            {
              targets: 6,
              className: "text-center",
            },
            {
              targets: 7,
              className: "text-center",
            },
      ],
      dom: "<'row'<'col-sm-12 '>f>" +
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
      drawCallback:function(data){
        $('#total').text(data.json.total);
        $('.dataTables_paginate').addClass("mt-2 mt-md-2 pt-1 ");
        $('.dataTables_paginate ul.pagination').addClass("pagination-md ");
      }
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

    function unArchiveResidence(){
      $(document).on('click','.unArchiveResidence',function(){
        var residence_id = $(this).attr('id');
        Swal.fire({
            title: '<strong class="text-danger">Are you sure?</strong>',
            html: "You want Unarchive this Resident?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            allowOutsideClick: false,
            confirmButtonText: 'Yes, Unarchive it!',
            width: '400px',
          }).then((result) => {
            if (result.value) {
              $.ajax({
                url: 'unArchiveResidence.php',
                type: 'POST',
                data: {
                  residence_id:residence_id,
                },
                cache: false,
                success:function(data){
                  Swal.fire({
                    title: '<strong class="text-success">Success</strong>',
                    type: 'success',
                    html: '<b>Unarchive Resident has Successfully<b>',
                    width: '400px',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    timer: 2000
                  }).then(()=>{
                    $("#archiveResidenceTable").DataTable().ajax.reload();
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

 
  $("#resident_id").inputFilter(function(value) {
  return /^-?\d*$/.test(value); 
  
  });


  $("#first_name, #middle_name, #last_name").inputFilter(function(value) {
  return /^[a-z, ]*$/i.test(value); 
  });
  


</script>


</body>
</html>