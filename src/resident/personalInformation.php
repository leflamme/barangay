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


    $sql_resident = "SELECT residence_information.*, residence_status.* FROM residence_information
    INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id
     WHERE residence_information.residence_id = '$user_id'";
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
    
    .custom-control-label::before, .custom-file-label,  .custom-file-label::after, .custom-select, .form-control:not(.form-control-navbar):not(.form-control-sidebar),  .input-group-text {
      background-color: transparent;
    color: #000000ff;
}


    .editInfo {
    background-color:rgba(0, 0, 0, 0);
    color:#fff;
    border: none;
    outline:none;
    width: 100%;
    }
    .editInfo:focus {
      background-color:rgba(0, 0, 0, 0);
      color:#fff;
      border: none;
      outline:none;
      width: 100%;
    }
    #edit_gender, #edit_civil_status, #edit_voters, #edit_pwd, select {
      /* for Firefox */
      -moz-appearance: none;
      /* for Chrome */
      
      border: none;
      width: 100%;
      background-color: transparent;
    color: #fff;
    }
    #edit_gender, #edit_civil_status, #edit_voters, #edit_pwd, option:focus{
      outline:none;
      border:none;
      box-shadow:none;
      background-color: transparent;
    color: #000000ff;
    }

    /* For IE10 */
    #edit_gender, #edit_civil_status, #edit_voters, #edit_pwd, select::-ms-expand {
      display: none;
      background-color: transparent;
    color: #fff;
    }
    select option {

    background: #343a40;
    color: #fff;
    text-shadow: 0 1px 0 rgba(0, 0, 0, 0.4);
}
#display_edit_image_residence{
      height: 120px;
      width:auto;
      max-width:500px;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed  layout-footer-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble " src="../assets/dist/img/loader.gif" alt="AdminLTELogo" height="70" width="70">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <h5><a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></h5>
      </li>
      <li class="nav-item d-none d-sm-inline-block" style="font-variant: small-caps;"><h5 class="nav-link text-white" ><?= $barangay ?></h5></li>
      <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white" >-</h5></li>
      <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white" ><?= $zone ?></h5></li>
      <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white" >-</h5></li>
      <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white" ><?= $district ?></h5>
      </li>
    </ul>

     <!-- Right navbar links -->
     <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-user"></i></a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="myProfile.php" class="dropdown-item">
            <!-- Message Start -->
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
                        <a href="personalInformation.php" class="nav-link active">
                            <i class="nav-icon fas fa-address-book"></i>
                            <p>Personal Information</p>
                        </a>
                    </li>

                    <li class="nav-item">
                      <a href="drrmPlan.php" class="nav-link">
                        <i class="fas fa-clipboard-list nav-icon text-red"></i>
                        <p>Emergency Plan</p>
                      </a>
                    </li>

                    <li class="nav-item">
                        <a href="myRecord.php" class="nav-link">
                            <i class="nav-icon fas fa-server"></i>
                            <p>Blotter Record</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="certificate.php" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Certificate</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="changePassword.php" class="nav-link">
                            <i class="nav-icon fas fa-lock"></i>
                            <p>Change Password</p>
                        </a>
                    </li>
                    
                </ul>
            </nav>
        </div>
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
        <form id="editResidenceForm" method="post" enctype="multipart/form-data">

          <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              <div class="widget-user-header bg-dark pl-5">
                <h3 class="widget-user-username"><?= $row_resident['first_name'] ?> <?= $row_resident['last_name'] ?></h3>
                <h5 class="widget-user-desc"><?= ucfirst($user_type) ?> of <?= $barangay ?></h5>
              </div>
              <div class="widget-user-image tex">
                
              <?php 
                if($row_resident['image_path'] != '' || $row_resident['image_path'] != null || !empty($row_resident['image_path'])){
                  echo '<img src="'.$row_resident['image_path'].'" class="img-circle elevation-2" alt="User Image" id="display_edit_image_residence">';
                }else{
                  echo '<img src="../assets/dist/img/blank_image.png" class="img-circle elevation-2" alt="User Image" id="display_edit_image_residence">';
                }
              ?>

                    <input type="file" name="edit_image_residence" id="edit_image_residence" style="display: none;">

              
              </div>
              <div class="card-footer mt-4">
              <div class="table-responsive">
              <input type="hidden" name="edit_residence_id" value="<?= $row_resident['residence_id'];?>">
                <table  style="font-size:11pt;" class="table table-bordered">
                  <tbody>
                    
                    <tr>
                      <td colspan="3">
                        <div class="d-flex justify-content-between">
                          <div> FIRST NAME<br>
                            <input type="text"  
  class="editInfo form-control form-control-sm" value="<?= $row_resident['first_name'] ?>" id="edit_first_name" name="edit_first_name" size="30"<?= !empty($row_resident['first_name']) ? 'readonly' : '' ?>>

                          </div>
                          <div>MIDDLE NAME<br>
                          <input type="text"  
  class="editInfo form-control form-control-sm"  value="<?= $row_resident['middle_name'] ?>" id="edit_middle_name" name="edit_middle_name" size="30"<?= !empty($row_resident['middle_name']) ? 'readonly' : '' ?>>
                          </div>
                          <div>      
                            LAST NAME<br>
                            <input type="text"   class="editInfo form-control form-control-sm"  value="<?= $row_resident['last_name'] ?>" id="edit_last_name" name="edit_last_name" size="30"<?= !empty($row_resident['last_name']) ? 'readonly' : '' ?>>
                          </div>
                          <div>      
                            SUFFIX<br>
                            <input type="text"   class="editInfo form-control form-control-sm"  value="<?= $row_resident['suffix'] ?>" id="edit_suffix" name="edit_suffix" size="30"<?= !empty($row_resident['suffix']) ? 'readonly' : '' ?>>
                          </div>
                        </div>
                      </td>
                    <td>
                    VOTERS
                      <br>
                      <select name="edit_voters" id="edit_voters" class="form-control"
                      <?= !empty($row_resident['voters']) ? 'disabled' : '' ?>>
                      <option value="Yes" <?= $row_resident['voters'] == 'Yes'? 'selected': '' ?>>Yes</option><option value="No" <?= $row_resident['voters'] == 'No'? 'selected': '' ?>>No</option>
                      </select>

                    </td>
                  </tr>
                  <tr>
                    <td>
                      DATE OF BIRTH
                        <br>
                        
                        <input type="date" class="editInfo  form-control form-control-sm" value="<?= !empty($row_resident['birth_date']) ? date('Y-m-d', strtotime($row_resident['birth_date'])) : '' ?>" name="edit_birth_date" id="edit_birth_date"/>
                                  
                    </td>
                    <td>
                      PLACE OF BIRTH
                        <br>
                      
                  <input type="text"  class="editInfo form-control form-control-sm"  value="<?= $row_resident['birth_place'] ?>" id="edit_birth_place" name="edit_birth_place" size="30"<?= !empty($row_resident['birth_place']) ? 'readonly' : '' ?>>
                    </td>
                    <td >
                      AGE
                        <br>
                    
                      <input type="text" class="editInfo  form-control form-control-sm" value="<?= $row_resident['age'] ?>"  name="edit_age" id="edit_age" disabled> 
                    </td>
                    <td >
                      PWD
                        <br>
                        <select name="edit_pwd" id="edit_pwd" class="form-control"
                        <?= !empty($row_resident['pwd']) ? 'disabled' : '' ?>>
                        <option value="Yes" <?= $row_resident['pwd'] == 'Yes'? 'selected': '' ?>>Yes</option>
                        <option value="No" <?= $row_resident['pwd'] == 'Yes'? 'selected': '' ?>>No</option>
                        </select>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      GENDER
                      <br>
                      <select name="edit_gender" id="edit_gender" class="form-control"
                      <?= !empty($row_resident['gender']) ? 'disabled' : '' ?>>
                      <option value="Male" <?= $row_resident['gender'] == 'Male'? 'selected': '' ?>>Male</option>
                      <option value="Female" <?= $row_resident['gender'] == 'Female'? 'selected': '' ?>>Female</option>
                      </select>

                    </td>
                    <td>
                      CIVIL STATUS
                      <br>
                     <select name="edit_civil_status" id="edit_civil_status" class="form-control"
                      <?= !empty($row_resident['civil_status']) ? 'disabled' : '' ?>>
                      <option value="Single" <?= $row_resident['civil_status'] == 'Single'? 'selected': '' ?>>Single</option>
                      <option value="Married" <?= $row_resident['civil_status'] == 'Married'? 'selected': '' ?>>Married</option>
                      <option value="Widowed" <?= $row_resident['civil_status'] == 'Widowed'? 'selected': '' ?>>Widowed</option>
                      </select>
                    </td>
                    <td >
                      RELIGION
                      <br>
<input type="text"   class="editInfo form-control form-control-sm"  value="<?= $row_resident['religion'] ?>" id="edit_religion" name="edit_religion" size="30"<?= !empty($row_resident['religion']) ? 'readonly' : '' ?>>                    </td> 
                    <td>
                      NATIONALITY
                      <br>
<input type="text"   class="editInfo form-control form-control-sm"  value="<?= $row_resident['nationality'] ?>" id="edit_nationality" name="edit_nationality" size="30"<?= !empty($row_resident['nationality']) ? 'readonly' : '' ?>>                    </td>     
                  </tr>

                  <tr>
                    <td>
                    MUNICIPALITY
                      <br>
<input type="text"  class="editInfo form-control form-control-sm"  value="<?= $row_resident['municipality'] ?>"  id="edit_municipality"  name="edit_municipality"  size="30"  <?= !empty($row_resident['municipality']) ? 'readonly' : '' ?>>
                    </td>
                    <td>
                      ZIP
                      <br>
<input type="text"  class="editInfo form-control form-control-sm"  value="<?= $row_resident['zip'] ?>"  id="edit_zip"  name="edit_zip"  size="30"  <?= !empty($row_resident['zip']) ? 'readonly' : '' ?>>
                    </td>
                    <td colspan="2">
                      BARANGAY
                      <br>
<input type="text"  class="editInfo form-control form-control-sm"  value="<?= $row_resident['barangay'] ?>"  id="edit_barangay"  name="edit_barangay"  size="30"  <?= !empty($row_resident['barangay']) ? 'readonly' : '' ?>>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      HOUSE NUMBER
                      <br>
<input type="text"  class="editInfo form-control form-control-sm"  value="<?= $row_resident['house_number'] ?>"  id="edit_house_number"  name="edit_house_number"  size="30"  <?= !empty($row_resident['house_number']) ? 'readonly' : '' ?>>
                    </td>
                    <td>
                      STREET
                      <br>
<input type="text"  
  class="editInfo form-control form-control-sm"  value="<?= $row_resident['street'] ?>"  id="edit_street"  name="edit_street"  size="30"  <?= !empty($row_resident['street']) ? 'readonly' : '' ?>>
                    </td>
                    <td colspan="2">
                      ADDRESS
                      <br>
<input type="text"  
  class="editInfo form-control form-control-sm"  value="<?= $row_resident['address'] ?>"  id="edit_address"  name="edit_address"  size="30"  <?= !empty($row_resident['address']) ? 'readonly' : '' ?>>
                    </td>      
                  </tr>

                  <tr>
                    <td colspan="2">
                      EMAIL ADDRESS
                      <br>
<input type="text"  
  class="editInfo form-control form-control-sm"  value="<?= $row_resident['email_address'] ?>"  id="edit_email_address"  name="edit_email_address"  size="30" <?= !empty($row_resident['email_address']) ? 'readonly' : '' ?>>
                    </td>
                    <td colspan="2">
                      CONTACT NUMBER
                      <br>
<input type="text"  class="editInfo form-control form-control-sm"  value="<?= $row_resident['contact_number'] ?>"  id="edit_contact_number"  name="edit_contact_number"  size="30"  <?= !empty($row_resident['contact_number']) ? 'readonly' : '' ?>>
                    </td>         
                  </tr>

                  <tr>
                    <td colspan="2">
                      FATHER'S NAME
                      <br>
<input type="text"  class="editInfo form-control form-control-sm"  value="<?= $row_resident['fathers_name'] ?>"  id="edit_fathers_name"  name="edit_fathers_name"  size="30"  <?= !empty($row_resident['fathers_name']) ? 'readonly' : '' ?>>
                    </td>
                    <td colspan="2">
                      MOTHER'S NAME
                      <br>
<input type="text"  class="editInfo form-control form-control-sm"  value="<?= $row_resident['mothers_name'] ?>"  id="edit_mothers_name"  name="edit_mothers_name"  size="30"  <?= !empty($row_resident['mothers_name']) ? 'readonly' : '' ?>>
                    </td>         
                  </tr>

                  <tr>
                    <td colspan="2">
                      GUARDIAN
                      <br>
<input type="text"  class="editInfo form-control form-control-sm"  value="<?= $row_resident['guardian'] ?>"  id="edit_guardian"  name="edit_guardian"  size="30"  <?= !empty($row_resident['guardian']) ? 'readonly' : '' ?>>
                    </td>
                    <td colspan="2">
                      GUARDIAN CONTACT
                      <br>
<input type="text"  class="editInfo form-control form-control-sm"  value="<?= $row_resident['guardian_contact'] ?>"  id="edit_guardian_contact"  name="edit_guardian_contact"  size="30"  <?= !empty($row_resident['guardian_contact']) ? 'readonly' : '' ?>>
                    </td>         
                  </tr>
                
                </tbody>
              </table>
                <button type="submit" class="btn btn-success elevation-5 px-3"><i class="fas fa-edit"></i>  UPDATE</button>
            </div>

        </div>





          
      




        
        </form>  
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

   <!-- Main Footer -->
   <footer class="main-footer">
    <strong>Copyright &copy; <?php echo date("Y"); ?> - <?php echo date('Y', strtotime('+1 year'));  ?> </strong>
    
    <div class="float-right d-none d-sm-inline-block">
    </div>
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
<script src="../assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="../assets/plugins/jquery-validation/additional-methods.min.js"></script>
<script src="../assets/plugins/jquery-validation/jquery-validate.bootstrap-tooltip.min.js"></script>

<script>
  $(document).ready(function(){



    $(function () {
        $.validator.setDefaults({
          submitHandler: function (form) {
            Swal.fire({
              title: '<strong class="text-warning">Are you sure?</strong>',
              html: "<b>You want edit your Information?</b>",
              type: 'info',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, edit it!',
              allowOutsideClick: false,
              width: '400px',
            }).then((result) => {
              if (result.value) {
                  $.ajax({
                    url: 'editResidence.php',
                    type: 'POST',
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success:function(data){
                      Swal.fire({
                        title: '<strong class="text-success">SUCCESS</strong>',
                        type: 'success',
                        html: '<b>Updated Information has Successfully<b>',
                        width: '400px',
                        confirmButtonColor: '#6610f2',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        timer: 2000,
                      }).then(()=>{
                        window.location.reload();
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
            
          }
        });
      $('#editResidenceForm').validate({
        rules: {
          edit_first_name: {
            required: true,
            minlength: 2
          },
          edit_last_name: {
            required: true,
            minlength: 2
          },
          edit_birth_date: {
            required: true,
          },
          edit_address:{
            required: true,
          },
          edit_email_address:{
            email: true,
          },
        },
        messages: {
          edit_first_name: {
            required: "<span class='text-danger text-bold'>First Name is Required</span>",
            minlength: "<span class='text-danger'>First Name must be at least 2 characters long</span>"
          },
          edit_last_name: {
            required: "<span class='text-danger text-bold'>Last Name is Required</span>",
            minlength: "<span class='text-danger'>Last Name must be at least 2 characters long</span>"
          },
          edit_birth_date: {
            required: "<span class='text-danger text-bold'>Birth Date is Required</span>",
          },
          edit_address: {
            required: "<span class='text-danger text-bold'>Address is Required</span>",
          },
          edit_email_address:{
            email:"<span class='text-danger text-bold'>Enter Valid Email!</span>",
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









    $('#display_edit_image_residence').on('click',function(){
      $("#edit_image_residence").click();
    })
    $("#edit_image_residence").change(function(){
        editDsiplayImage(this);
      })

    function editDsiplayImage(input){
        if(input.files && input.files[0]){
          var reader = new FileReader();
          var edit_image_residence = $("#edit_image_residence").val().split('.').pop().toLowerCase();

          if(edit_image_residence != ''){
            if(jQuery.inArray(edit_image_residence, ['gif','png','jpeg','jpg']) == -1){
              Swal.fire({
                title: '<strong class="text-danger">ERROR</strong>',
                type: 'error',
                html: '<b>Invalid Image File<b>',
                width: '400px',
                confirmButtonColor: '#6610f2',
              })
              $("#edit_image_residence").val('');
              $("#display_edit_image_residence").attr('src', '<?= $row_resident['image_path'] ?>');
              return false;
            }
          }
            reader.onload = function(e){
              $("#display_edit_image_residence").attr('src', e.target.result);
              $("#display_edit_image_residence").hide();
              $("#display_edit_image_residence").fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
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

 
  $("#edit_contact_number, #edit_zip, #edit_guardian_contact, #edit_age").inputFilter(function(value) {
  return /^-?\d*$/.test(value); 
  
  });


  $("#edit_first_name, #edit_middle_name, #edit_last_name, #edit_suffix, #edit_religion, #edit_nationality, #edit_municipality, #edit_fathers_name, #edit_mothers_name, #edit_guardian").inputFilter(function(value) {
  return /^[a-z, ]*$/i.test(value); 
  });
  
  $("#edit_street, #edit_birth_place, #edit_house_number").inputFilter(function(value) {
  return /^[0-9a-z, ,-]*$/i.test(value); 
  });

</script>
</body>
</html>
