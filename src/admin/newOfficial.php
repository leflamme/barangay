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
  <title>New Official</title>
  <!-- Website Logo -->
  <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">

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

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
 
</head>
<div class="wrapper">

<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble " src="../assets/dist/img/loader.gif" alt="AdminLTELogo" height="70" width="70">
  </div>

<style>
body {
  font-family: 'Poppins', sans-serif;
  background-color: #ffffff;
}

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

/* Tabs - form */
.tab-content .lead {
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.tab-nav-link {
  background-color: #ffffff !important;
  color: #003366 !important;
  border: 1px solid #cccccc !important;
  font-weight: 600;
}

.tab-nav-link.active {
  background-color: #003366 !important;
  color: #ffffff !important;
}

.tab-nav-link:hover {
  background-color: #f0f0f0 !important;
  color: #003366 !important;
}

/* NEW: Disabled Tab Style */
.disabled-tab {
    pointer-events: none;
    opacity: 0.6;
    cursor: not-allowed;
}

/* Responsive Nav Links */
@media (max-width: 768px) {
  .navbar-nav .nav-link {
    font-size: 1rem;
    margin: 6px 0;
  }
}

/* Fix dark background issue */
.card,
.card-body,
.card-header,
.card-footer {
  background-color: #ffffff !important;
  color: #050C9C !important;
}

.form-control {
  background-color: #ffffff !important;
  color: #000000 !important; /* input text now black */
  border: 1px solid #dcdfe3 !important; /* lighter border like newofficial */
  border-radius: 6px;
  font-family: 'Poppins', sans-serif;
}

/* Image card on left */
.card-indigo.card-outline {
  border-color: #003366 !important;
  background-color: #ffffff !important;
}

/* Fix image section text */
.box-profile h3 {
  color: #003366 !important;
}

.btn-success {
  background-color: #2E8B57 !important;
  border-color: #2E8B57 !important;
  color: #ffffff !important;
  font-weight: 600;
  border-radius: 10px !important; /* Rounded corners */
  padding: 10px 20px; /* Optional: for a nicer size */
}

.btn-success:hover {
  background-color: #256d47 !important;
  border-color: #256d47 !important;
}

.step-footer {
    display: flex;
    justify-content: flex-end;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

</style>
  
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
    <ul class="navbar-nav ml-auto">
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
  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">

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
                <a href="newOfficial.php" class="nav-link bg-indigo">
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
      </div>
    </aside>

  <div class="content-wrapper">
   
    <section class="content mt-3">
      <div class="container-fluid">

      <form id="newOfficialForm" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="row mb-3">
          <div class="col-sm-4">
            <div class="card card-indigo card-outline h-100">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-thumbnail" src="../assets/dist/img/blank_image.png" alt="User profile picture" style="cursor: pointer;" id="image_official">
                  <input type="file" name="add_image" id="add_image" style="display: none;">
                </div>

                <h3 class="profile-username text-center "><span id="keyup_first_name"></span> <span id="keyup_last_name"></span></h3>

                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Position <span class="text-danger">*</span></label>
                      <select name="add_position" id="add_position" class="form-control text-uppercase">
                        <option value=""></option>
                        <?php 
                        
                        $sql_position = "SELECT position_id, position FROM position";
                        $stmt_position = $con->prepare($sql_position) or die ($con->error);
                        $stmt_position->execute();
                        $result_position = $stmt_position->get_result();
                        while($row_position = $result_position->fetch_assoc()){
                         echo '<option value="'.$row_position['position_id'].'" class="text-uppercase">'.$row_position['position'].'</option>';
                        }
                        
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group ">
                      <label >Start <span class="text-danger">*</span></label>
                      <input type="date" class="form-control" id="add_term_from" name="add_term_from">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group ">
                      <label >End <span class="text-danger">*</span></label>
                      <input type="date" class="form-control" id="add_term_to" name="add_term_to">
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Voters <span class="text-danger">*</span></label>
                      <select name="add_voters" id="add_voters" class="form-control">
                        <option value=""></option>
                        <option value="NO">NO</option>
                        <option value="YES" selected>YES</option> </select>
                    </div>
                  </div>
                  
                  <div class="col-sm-12">
                    <div class="form-group ">
                      <label >Date of Birth <span class="text-danger">*</span></label>
                      <input type="date" class="form-control" id="add_birth_date" name="add_birth_date">
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group ">
                      <label >Place of Birth <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="add_birth_place" name="add_birth_place">
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Pwd <span class="text-danger">*</span></label>
                      <select name="add_pwd" id="add_pwd" class="form-control">
                        <option value=""></option>
                        <option value="NO">NO</option>
                        <option value="YES">YES</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-12" id="pwd_check" style="display: none;">
                    <div class="form-group ">
                      <label >TYPE OF PWD</label>
                        <input type="text" class="form-control" id="add_pwd_info" name="add_pwd_info">
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group ">
                      <label >Single Parent</label>
                      <select name="add_single_parent" id="add_single_parent" class="form-control">
                        <option value=""></option>
                        <option value="NO">NO</option>
                        <option value="YES">YES</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              </div>
          </div>
          <div class="col-sm-8">
            <div class="card card-tabs h-100 transparent-card shadow-card">
              <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link tab-nav-link active" id="basic-info-tab" data-toggle="pill" href="#basic-info">Basic Info</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link tab-nav-link disabled-tab" id="other-info-tab" data-toggle="pill" href="#other-info">Other Info</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link tab-nav-link disabled-tab" id="account-tab" data-toggle="pill" href="#account">Account</a>
                  </li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                  <div class="tab-pane fade active show" id="basic-info" role="tabpanel" aria-labelledby="basic-info-tab">
                      <p class="lead text-center">Personal Details</p>
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="form-group ">
                            <label>First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_first_name" name="add_first_name" >
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="form-group ">
                            <label>Middle Name</label>
                            <input type="text" class="form-control" id="add_middle_name" name="add_middle_name" >
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="form-group ">
                            <label>Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_last_name" name="add_last_name" >
                          </div>  
                        </div>
                      </div>
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="form-group ">
                              <label >Suffix</label>
                              <input type="text" class="form-control" id="add_suffix" name="add_suffix" >
                            </div>
                          </div>
                          
                          <div class="col-sm-6">
                            <div class="form-group ">
                              <label >Gender <span class="text-danger">*</span></label>
                              <select name="add_gender" id="add_gender" class="form-control">
                                <option value=""></option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group ">
                              <label >Civil Status</label>
                              <select name="add_civil_status" id="add_civil_status" class="form-control">
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                              </select>
                            </div>
                          </div>
                        
                          
                          <div class="col-sm-6">
                            <div class="form-group ">
                              <label >Religion</label>
                              <input type="text" class="form-control" id="add_religion" name="add_religion">
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group ">
                              <label >Nationality <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" id="add_nationality" name="add_nationality" value="Filipino"> </div>
                          </div>                              
                        </div>
                        <div class="card-footer step-footer">
                          <button type="button" id="proceed-basic" class="btn btn-success px-4 elevation-3">
                            <i class="fas fa-arrow-right"></i> Proceed to Other Info
                          </button>
                        </div>
                  </div>
                  <div class="tab-pane fade" id="other-info" role="tabpanel" aria-labelledby="other-info-tab">
                        <p class="lead text-center">Address</p>
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Municipality <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" id="add_municipality" name="add_municipality">
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Zip <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" id="add_zip" name="add_zip" >
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Barangay <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" id="add_barangay" name="add_barangay" >
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>House Number <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" id="add_house_number" name="add_house_number" >
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                            <label>Street <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_street" name="add_street" >
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Address <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" id="add_address" name="add_address" >
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Email Address <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" id="add_email_address" name="add_email_address" >
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label >Contact Number <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" maxlength="11" id="add_contact_number" name="add_contact_number">
                            </div>
                          </div>
                        </div>
                        <div class="card-footer step-footer">
                          <button type="button" id="proceed-other" class="btn btn-success px-4 elevation-3">
                            <i class="fas fa-arrow-right"></i> Proceed to Account
                          </button>
                        </div>
                  </div>

                  <div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="account-tab">
                    <p class="lead text-center">Account Credentials</p>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>User Type <span class="text-danger">*</span></label>
                                <select name="add_user_type" id="add_user_type" class="form-control">
                                    <option value=""></option>
                                    <option value="admin">Admin</option>
                                    <option value="secretary">Secretary</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Username <span class="text-danger">*</span></label>
                                <input type="text" id="add_username" name="add_username" class="form-control" placeholder="USERNAME">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Password <span class="text-danger">*</span></label>
                                <div class="input-group" id="show_hide_password">
                                    <input type="password" id="add_password" name="add_password" class="form-control" placeholder="PASSWORD" style="border-right: none;">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-transparent">
                                            <a href="#" style="text-decoration:none;"><i class="fas fa-eye-slash" aria-hidden="true"></i></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group" id="show_hide_password_confirm">
                                    <input type="password" id="add_confirm_password" name="add_confirm_password" class="form-control" placeholder="CONFIRM PASSWORD" style="border-right: none;">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-transparent">
                                            <a href="#" style="text-decoration:none;"><i class="fas fa-eye-slash" aria-hidden="true"></i></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer step-footer">
                        <button type="submit"  class="btn btn-success px-3  elevation-5 btn-flat"> <i class="fas fa-user-plus"></i> ADD NEW OFFICIAL</button>
                    </div>
                  </div>
                </div>
              </div>
              </div>
          </div>
        </div>
        </form>
      </div></section>
    </div>
  <footer class="main-footer">
  <strong>&copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')) ?></strong>
  <div class="float-right d-none d-sm-inline-block">
  </div>
</footer>
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
<script src="../assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<script src="../assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script>
  $(document).ready(function(){
    $("#add_pwd").change(function(){
      var pwd_check = $(this).val();

      if(pwd_check == 'YES'){
        $("#pwd_check").css('display', 'block');
        $("#add_pwd_info").prop('disabled', false);
      }else{
        $("#pwd_check").css('display', 'none');
        $("#add_pwd_info").prop('disabled', true);
      }

    })

    // NEW FUNCTION TO CHECK VALIDITY OF A TAB
    function isCurrentStepValid(tabId) {
        var isValid = true;
        // Find all inputs in the given tab pane, trigger validation on them
        $('#' + tabId).find('input, select, textarea').each(function() {
            if (!$(this).valid()) {
                isValid = false;
            }
        });
        return isValid;
    }

    // NEW: Proceed from Basic Info to Other Info
    $('#proceed-basic').click(function(e) {
        e.preventDefault();
        $('#newOfficialForm').validate().settings.ignore = ":disabled"; // Ensure all fields are checked
        if (isCurrentStepValid('basic-info')) {
            $('#other-info-tab').removeClass('disabled-tab').tab('show');
            $('#basic-info-tab').removeClass('active');
            $('#basic-info').removeClass('active show');
            $('#other-info').addClass('active show');
        } else {
            Swal.fire({
                title: 'Incomplete Information',
                text: 'Please fill out all required fields in the Basic Info tab.',
                icon: 'warning',
                confirmButtonColor: '#050C9C'
            });
        }
    });

    // NEW: Proceed from Other Info to Account
    $('#proceed-other').click(function(e) {
        e.preventDefault();
        $('#newOfficialForm').validate().settings.ignore = ":disabled"; // Ensure all fields are checked
        if (isCurrentStepValid('other-info')) {
            $('#account-tab').removeClass('disabled-tab').tab('show');
            $('#other-info-tab').removeClass('active');
            $('#other-info').removeClass('active show');
            $('#account').addClass('active show');
        } else {
            Swal.fire({
                title: 'Incomplete Information',
                text: 'Please fill out all required fields in the Other Info tab.',
                icon: 'warning',
                confirmButtonColor: '#050C9C'
            });
        }
    });

    
    $(function () {
        $.validator.setDefaults({
          submitHandler: function (form) {
            // 1. Check if the final tab is valid before submitting
            if (!isCurrentStepValid('account')) {
                Swal.fire({
                    title: 'Incomplete Information',
                    text: 'Please fill out all required fields in the Account tab.',
                    icon: 'warning', // 'icon' is correct here, 'type' is for the error popup
                    confirmButtonColor: '#050C9C'
                });
                return; // Stop submission
            }
            
            // 2. If valid, proceed with the AJAX submission
            $.ajax({
                url: 'addNewOfficial.php',
                type: 'POST',
                data: new FormData(form),
                processData: false,
                contentType: false,
                success:function(data){
                    
                    var response = data.trim();

                    if(response == 'error'){
                        Swal.fire({
                            title: '<strong class="text-danger">ERROR</strong>',
                            type: 'error', // 'type' is correct here
                            html: '<b>Position Limit Reached<b>',
                            width: '400px',
                            confirmButtonColor: '#6610f2',
                            allowOutsideClick: false,
                        });

                    } else if(response == 'errorPassword'){
                        Swal.fire({
                            title: '<strong class="text-danger">ERROR</strong>',
                            type: 'error', // 'type' is correct here
                            html: '<b>Passwords do not Match</b>',
                            confirmButtonColor: '#6610f2',
                        });
                    
                    } else if(response == 'errorUsername'){
                        Swal.fire({
                            title: '<strong class="text-danger">ERROR</strong>',
                            type: 'error', // 'type' is correct here
                            html: '<b>Username is Already Taken</b>',
                            confirmButtonColor: '#6610f2',
                        });

                    } else if(response == 'success') { 
                        Swal.fire({
                            title: '<strong class="text-success">SUCCESS</strong>',
                            type: 'success', // 'type' is correct here
                            html: '<b>Added Official Successfully<b>',
                            width: '400px',
                            confirmButtonColor: '#6610f2',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            timer: 2000,
                        }).then(()=>{
                            window.location.reload();
                        })

                    } else { 
                        // THIS IS THE BLOCK WITH THE FIX
                        // This will show the REAL PHP error
                        Swal.fire({
                            title: '<strong class="text-danger">Save Failed!</strong>',
                            type: 'error', // <-- THIS IS THE FIX
                            html: '<b>The server returned an error:</b><br><pre style="text-align: left; background: #eee; padding: 10px; border-radius: 5px;">' + response + '</pre>',
                            confirmButtonColor: '#d33',
                        });
                    }
                    
                }
            }).fail(function(){
                Swal.fire({
                    title: '<strong class="text-danger">Ooppss..</strong>',
                    type: 'error', // 'type' is correct here
                    html: '<b>Something went wrong with ajax !<b>',
                    width: '400px',
                    confirmButtonColor: '#6610f2',
                })
            })
          }
        });
      $('#newOfficialForm').validate({
        ignore: ".disabled-tab", // VALIDATION: Ignore fields in disabled tabs
        rules: {
          add_first_name: {
            required: true,
            minlength: 2
          },
          add_last_name: {
            required: true,
            minlength: 2
          },
          add_birth_date: {
            required: true,
          },
          add_address:{
            required: true,
          },
          add_email_address:{
            required: true, // Added required
            email: true,
          },
          add_term_from:{
            required: true,
          },
          add_term_to:{
            required: true,
          },
          add_position:{
            required: true,
          },
          add_contact_number:{
            required: true,
            minlength: 11,
          },
          add_voters:{
            required: true,
          },
          add_pwd:{
            required: true,
          },
          add_pwd_info:{
            required: function(element) {
                return $("#add_pwd").val() == "YES"; // Only required if PWD is YES
            }
          },
          // NEW REQUIRED FIELDS FROM YOUR LIST
          add_birth_place: {
            required: true,
          },
          add_gender: {
            required: true,
          },
          add_nationality: {
            required: true,
          },
          add_municipality: {
            required: true,
          },
          add_zip: {
            required: true,
          },
          add_barangay: {
            required: true,
          },
          add_house_number: {
            required: true,
          },
          add_street: {
            required: true,
          },
          // ACCOUNT VALIDATION RULES
          add_user_type: {
            required: true,
          },
          add_username:{
            required: true,
            minlength: 8
          },
          add_password:{
            required: true,
            minlength: 8
          },
          add_confirm_password:{
            required: true,
            minlength: 8,
            equalTo: "#add_password"
          },
        },
        messages: {
          add_first_name: {
            required: "Please provide a First Name",
            minlength: "First Name must be at least 2 characters long"
          },
          add_last_name: {
            required: "Please provide a Last Name",
            minlength: "Last Name must be at least 2 characters long"
          },
          add_birth_date: {
            required: "Please provide a Birth Date",
          },
          add_address: {
            required: "Please provide a Address",
          },
          add_term_from: {
            required: "Please provide a Term Form",
          },
          add_term_to: {
            required: "Please provide a Term To",
          },
          add_position: {
            required: "Please provide a Position",
          },
          add_email_address:{
            required: "Please provide an Email Address",
            email:"Enter Valid Email!",
            },
            add_contact_number:{
              required: "Please provide a Contact Number",
              minlength: "Input Exact Contact Number"
            },
            // NEW VALIDATION MESSAGES
            add_birth_place: {
              required: "Please provide a Place of Birth"
            },
            add_gender: {
              required: "Please select a Gender"
            },
            add_nationality: {
              required: "Please provide a Nationality"
            },
            add_municipality: {
              required: "Please provide a Municipality"
            },
            add_zip: {
              required: "Please provide a Zip Code"
            },
            add_barangay: {
              required: "Please provide a Barangay"
            },
            add_house_number: {
              required: "Please provide a House Number"
            },
            add_street: {
              required: "Please provide a Street"
            },
            add_user_type: {
                required: "Please select a user type"
            },
            add_username: {
                required: "Please provide a Username",
                minlength: "Username must be at least 8 characters long"
            },
            add_password: {
                required: "Please provide a Password",
                minlength: "Password must be at least 8 characters long"
            },
            add_confirm_password: {
                required: "Please confirm the Password",
                minlength: "Password must be at least 8 characters long",
                equalTo: "Passwords do not match"
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
   

    $("#add_first_name, #add_last_name").keyup(function(){
      var add_first_name = $("#add_first_name").val();
      var add_last_name = $("#add_last_name").val();
      $("#keyup_first_name").text(add_first_name);
      $("#keyup_last_name").text(add_last_name);
    })

    $("#image_official").click(function(){
          $("#add_image").click();
      });

    function displayImge(input){
      if(input.files && input.files[0]){
        var reader = new FileReader();
        var add_image = $("#add_image").val().split('.').pop().toLowerCase();

        if(add_image != ''){
          if(jQuery.inArray(add_image,['gif','png','jpg','jpeg']) == -1){
            Swal.fire({
              title: '<strong class="text-danger">ERROR</strong>',
              type: 'error',
              html: '<b>Invalid Image File<b>',
              width: '400px',
              confirmButtonColor: '#6610f2',
            })
            $("#add_image").val('');
            $("#image_official").attr('src', '../assets/dist/img/blank_image.png');
            return false;
          }
        }

        reader.onload = function(e){
          $("#image_official").attr('src',e.target.result);
          $("#image_official").hide();
          $("#image_official").fadeIn(650);
        }

        reader.readAsDataURL(input.files[0]);

      }
    }  

    $("#add_image").change(function(){
      displayImge(this);
    })

    // NEW PASSWORD TOGGLE SCRIPT
    $("#show_hide_password a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password input').attr("type") == "text"){
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass( "fa-eye-slash" );
            $('#show_hide_password i').removeClass( "fa-eye" );
        }else if($('#show_hide_password input').attr("type") == "password"){
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass( "fa-eye-slash" );
            $('#show_hide_password i').addClass( "fa-eye" );
        }
    });
    $("#show_hide_password_confirm a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password_confirm input').attr("type") == "text"){
            $('#show_hide_password_confirm input').attr('type', 'password');
            $('#show_hide_password_confirm i').addClass( "fa-eye-slash" );
            $('#show_hide_password_confirm i').removeClass( "fa-eye" );
        }else if($('#show_hide_password_confirm input').attr("type") == "password"){
            $('#show_hide_password_confirm input').attr('type', 'text');
            $('#show_hide_password_confirm i').removeClass( "fa-eye-slash" );
            $('#show_hide_password_confirm i').addClass( "fa-eye" );
        }
    });

    
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

 
  $("#add_contact_number, #add_zip, #add_guardian_contact").inputFilter(function(value) {
  return /^-?\d*$/.test(value); 
  
  });


  $("#add_first_name, #add_middle_name, #add_last_name, #add_suffix, #add_religion, #add_nationality, #add_municipality, #add_fathers_name, #add_mothers_name, #add_guardian").inputFilter(function(value) {
  return /^[a-z, ]*$/i.test(value); 
  });
  
  $("#add_street, #add_birth_place, #add_house_number").inputFilter(function(value) {
  return /^[0-9a-z, ,-]*$/i.test(value); 
  });

</script>
</body>
</html>