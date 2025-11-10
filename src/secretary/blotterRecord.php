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
  <link rel="stylesheet" href="../assets/plugins/jquery-ui/jquery-ui.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="../assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
     <!-- DONT FORGET -->
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

  /* ======= CARD HEADER ======= */
  .card-header {
    background-color: #050C9C !important;
    color: #ffffff !important;
    border-bottom: 3px solid #A7E6FF !important;
    border-radius: 12px 12px 0 0 !important;
  }

  .card-title {
    font-weight: 600;
    font-size: 1.2rem;
    color: #ffffff !important;
  }

  .card-tools .btn {
    background-color: #3ABEF9 !important;
    color: #ffffff !important;
    border-radius: 8px;
    font-weight: 600;
    transition: 0.3s ease-in-out;
  }

  .card-tools .btn:hover {
    background-color: #3572EF !important;
    color: #ffffff !important;
  }

  /* ======= TABLE STYLING ======= */
  #blotterRecordTable {
    background-color: #FAF9F6;
    color: #000000;
    border-radius: 10px;
    overflow: hidden;
    width: 100% !important;
  }

  #blotterRecordTable thead {
    background-color: #050C9C;
    color: #ffffff;
    font-weight: 600;
  }

  #blotterRecordTable tbody tr:nth-of-type(odd),
  #blotterRecordTable tbody tr:nth-of-type(even) {
    background-color: #FAF9F6;
  }

  #blotterRecordTable tbody tr:hover {
    background-color: #0047ab !important;
  }

  #blotterRecordTable th,
  #blotterRecordTable td {
    vertical-align: middle !important;
    white-space: nowrap;
  }

  /* ======= CARD BODY ======= */
  .card-body {
    background-color: #ffffff;

  }

  /* ======= CARD FOOTER ======= */
  .card-footer {
    background-color: #ffffff;
    border-top: 1px solid #A7E6FF;
    border-radius: 0 0 12px 12px;
  }

  /* ======= DELETE BUTTON ======= */
  #delete_records {
    background-color: #E41749 !important;
    color: white !important;
    font-weight: 600;
    transition: background-color 0.3s ease-in-out;
  }

  #delete_records:hover {
    background-color: #F5587B !important;
  }

  #select_count {
    background-color: #050C9C !important;
    color: #FFF591 !important;
    font-weight: 600;
  }

  /* ======= PAGINATION + DATATABLE UI ======= */
  .dataTables_wrapper .dataTables_paginate .page-link {
    background-color: #3ABEF9 !important;
    color: #fff !important;
    border: 1px solid #A7E6FF !important;
    border-radius: 5px !important;
    margin: 0 2px;
    font-weight: 600;
    transition: background-color 0.3s ease;
  }

  .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
    background-color: #050C9C !important;
    color: #fff !important;
    border-color: #050C9C !important;
  }

  .dataTables_wrapper .dataTables_paginate .page-link:hover {
    background-color: #3572EF !important;
    color: #fff !important;
  }

  .dataTables_length select {
    background: #ffffff;
    border: 1px solid #3ABEF9;
    color: #050C9C;
  }

  .dataTables_info {
    font-size: 13px;
    font-weight: 500;
    color: #050C9C;
  }

  .dataTables_filter input[type="search"] {
    background-color: #ffffff !important;
    color: #000000 !important;
    border: 1px solid #ccc !important;
    border-radius: 6px !important;
    padding: 6px 10px !important;
  }

  /* ======= RESPONSIVE DESIGN ======= */
  @media (max-width: 768px) {
    .card-title {
      font-size: 1rem;
    }

    .card-tools .btn {
      font-size: 0.85rem;
      padding: 5px 10px;
    }

    #delete_records {
      width: 100% !important;
      margin-top: 10px;
    }

    .table-responsive {
      overflow-x: auto;
    }

    #blotterRecordTable td,
    #blotterRecordTable th {
      white-space: nowrap;
    }
  }
</style>


</head>
<body class="hold-transition sidebar-mini layout-footer-fixed">
<div class="wrapper">

   <!-- Preloader -->
   <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble " src="../assets/dist/img/loader.gif" alt="AdminLTELogo" height="70" width="70">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand dark-mode">
    <!-- Left navbar links -->
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

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="myProfile.php" class="dropdown-item">
            <!-- Message Start -->
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
            <!-- Message End -->
          </a>         
          <div class="dropdown-divider"></div>
          <a href="../logout.php" class="dropdown-item dropdown-footer">LOGOUT</a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar elevation-4 sidebar-no-expand dark-mode">
    <!-- Brand Logo -->
    <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display:block;">

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
                <a href="allResidence.php" class="nav-link ">
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

            </ul>
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
            <a href="blotterRecord.php" class="nav-link bg-indigo">
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
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

           
            <div class="card">
              <div class="card-header border-transparent">
                <h3 class="card-title">List of Records</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool bg-black btn-flat" id="addRecord" data-toggle="modal" data-target="#blotterRecordModal">
                    <i class="fas fa-plus"></i> New Record
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body ">
                <table class="table table-striped table-hover " id="blotterRecordTable">             
                  <thead>
                    <tr>
                      <th><input type="checkbox" id="select_all"></th>
                      <th>Blotter Number</th>
                      <th>Status</th>
                      <th>Remarks</th>
                      <th>Incident</th>
                      <th>Location of Incident</th>
                      <th>Date Incident</th>
                      <th>Date Reported</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                  <div class="row ">
                    <div class="col-md-12 ">
                        <a type="button" data-toggle="tooltip" data-placement="right" title="Delete" style="border-radius: 20px; height: 40px; width: 10%;" class="btn btn-app elevation-3 p-1 m-0 bg-danger" id="delete_records">
                          <span style="height: 20px;  font-size: 1.1em; border-radius: 20px;" class="badge bg-indigo mr-2 mb-5" id="select_count"> 0 </span>
                          <i class="fas fa-trash-alt m-0 pt-1" ></i>
                        </a>
                    </div>
                  </div>
              </div>
            </div>

       
       
          
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

 

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; <?php echo date("Y"); ?> - <?php echo date('Y', strtotime('+1 year'));  ?> </strong>
    
    <div class="float-right d-none d-sm-inline-block">
    </div>
  </footer>
</div>
<!-- ./wrapper -->



<!-- Modal -->
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
<div id="show_residence"></div>
<div id="show_records"></div>




<script>
  $(document).ready(function(){

    blotterRecordTable();


    $(document).on('show.bs.modal', '.modal', function () {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });
  

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

    function blotterRecordTable(){
      var blotterRecordTable = $("#blotterRecordTable").DataTable({

        processing: true,
        serverSide: true,
        order:[],
        autoWidth: false,
        responsive: true,
        ajax:{
          url: 'blotterRecordTable.php',
          type: 'POST',
        },
        columnDefs:[
          {
            targets: 0,
            orderable: false,
          },
          {
            targets: 8,
            orderable: false,
          },
         
        ]


      })
    }

  
   
    $("#complainant_residence, #person_involed").on('select2:select', function(e){
      var residence_id = e.params.data.id;
      $("#show_residence").html('');


      if(residence_id != ''){

        $.ajax({
          url: 'showResidenceInfo.php',
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

     
      
    })


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
              url: 'addNewBlotterRecord.php',
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
                  $("#blotterRecordTable").DataTable().ajax.reload();
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
    })


    $("#addRecord").on('click',function(){
      $("#addNewRecordForm")[0].reset();
      $(".select2-selection__choice").css('display', 'none')
      
    })

 

    
  })
</script>
<script>
  $(document).ready(function(){

    $('#complainant_residence').select2({
      templateResult: formatState,
      templateSelection: formatState,
      theme: 'bootstrap4',
  
      language: {
          noResults: function (params) {
            return "No Record";
          }
        },
      
    }) 
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

      $('#person_involed').select2({
      templateResult: formatState,
      templateSelection: formatState,
      theme: 'bootstrap4',
  
      language: {
          noResults: function (params) {
            return "No Record";
          }
        },
      
    }) 
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

  })
    
</script>


<script>
$(document).ready(function(){
 

  $(document).on('change', '#complainant_residence',function(){
    var subject = [];
  
      	subject.push($(this).val());
      var selected_values = subject.join(",");
       console.log(selected_values);

      $.ajax({
        url: 'showPerson.php',
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
      
    })


});
</script>




<script>
/* delete subject records*/
$(document).ready(function() {
	$('#delete_records').on('click', function() {
		var subject = [];
		$(".sub_checkbox:checked").each(function() {
			subject.push($(this).attr('id'));
     
		});

		if(subject.length <= 0) {
        Swal.fire({
          title: '<strong class="text-info">NOTE</strong>',
          html: '<b>Please Select Record to Delete!<b>',
          type: "info",
          showConfirmButton: false,
          confirmButtonColor: '#6610f2',
          width: '400px',
          showConfirmButton: true,
          allowOutsideClick: false,
        })
    
		} 
		else { 
      Swal.fire({
        title: '<strong class="text-info">ARE YOU SURE?</strong>',
        html: "<b>You want delete selected Record?</b>",
        type: 'info',
        showCancelButton: true,
        confirmButtonColor: '#6610f2',
        width: '400px',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        allowOutsideClick: false,
      }).then((result) => {
        if(result.value){
          var selected_values = subject.join(",");
         
            $.ajax({
              type: "POST",
              url: "deleteBlotterRecord.php",
              cache:false,
              data: 'id='+selected_values,
              success: function(data) {
              
                  Swal.fire({
                    title: '<strong class="text-success">SUCESS</strong>',
                    text: "Deleted Blotter Record Successfully",
                    type: 'success',
                    timer: 1500,
                    width: '400px',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                  }).then(()=>{
                   $("#blotterRecordTable").DataTable().ajax.reload();
                   $("#select_count").text('0');
                   $('#select_all').prop('checked', false);
                  })
                
                
              } 
            }).fail(function(){
              Swal.fire({
                title: 'Ooppss...',
                text: 'Something went wrong with ajax !',
                type: 'error',
                confirmButtonColor: '#6610f2',
                allowOutsideClick: false,
                width: '400px',
              })             
            })
        }
      });								 
		} 
	});
});	

$(document).on('click', '#select_all', function() {
	$(".sub_checkbox").prop("checked", this.checked);;
	$("#select_count").html($("input.sub_checkbox:checked").length);
});
$(document).on('click', '.sub_checkbox', function() {
	if ($('.sub_checkbox:checked').length == $('.sub_checkbox').length) {
	$('#select_all').prop('checked', true);
	} else {
	$('#select_all').prop('checked', false);
	}
	$("#select_count").html($("input.sub_checkbox:checked").length);
});
</script>




</body>
</html>
