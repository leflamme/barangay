<?php
session_start();
include_once 'connection.php';

if(isset($_SESSION['user_id']) && $_SESSION['user_type']){


  $user_id = $_SESSION['user_id'];
  $sql = "SELECT * FROM users WHERE id = '$user_id'";
  $query = $con->query($sql) or die ($con->error);
  $row = $query->fetch_assoc();
  $account_type = $row['user_type'];
  if ($account_type == 'admin') {
  echo '<script>
          window.location.href="admin/dashboard.php";
      </script>';
  
  } elseif ($account_type == 'secretary') {
      echo '<script>
          window.location.href="secretary/dashboard.php";
      </script>';
  
  } else {
      echo '<script>
      window.location.href="resident/dashboard.php";
  </script>';
  
}
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
      $postal_address = $row['postal_address'];
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Barangay Registration Portal</title>
  <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/plugins/bs-stepper/css/bs-stepper.min.css">
  <link rel="stylesheet" href="assets/plugins/phone code/intlTelInput.min.css">
  <link rel="stylesheet" href="assets/plugins/sweetalert2/css/sweetalert2.min.css">
  <link rel="stylesheet" href="assets/plugins/step-wizard/css/smart_wizard_all.min.css">
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to bottom right, #1d34e0, #ffffff);
      min-height: 100vh;
      margin: 0;
    }

    .navbar {
      background-color: #050C9C;
      padding: 1.2rem 1rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .navbar-brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .navbar-brand img {
      height: 50px;
      width: 50px;
      object-fit: cover;
      border-radius: 50%;
      padding: 5px;
      background-color: white;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
      aspect-ratio: 1 / 1; /* Keeps it a perfect circle */
    }

    .navbar-brand span {
      color: #A7E6FF;
      font-size: 1.5rem;
      font-weight: 700;
      letter-spacing: 1px;
    }

    .nav-link {
      color: #A7E6FF !important;
      font-weight: 600;
      position: relative;
    }

    .nav-link:hover {
      color: #FFF591 !important;
    }

    .nav-link::after {
      content: '';
      position: absolute;
      width: 0%;
      height: 3px;
      left: 0;
      bottom: -5px;
      background-color: #E41749;
      transition: width 0.3s ease;
    }

    .nav-link:hover::after {
      width: 100%;
    }

    .form-section {
      padding: 2rem;
      background: #ffffff;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
      margin: 2rem auto;
      max-width: 1200px;
    }

    .form-section h2 {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
      color: #1d34e0;
    }

    .form-group label {
      font-weight: 600;
      margin-bottom: 0.5rem;
      display: inline-block;
    }

    .form-control {
      border-radius: 6px;
      box-shadow: none;
      transition: border-color 0.3s ease;
    }

    .form-control:focus {
      border-color: #1d34e0;
      box-shadow: 0 0 0 2px rgba(29, 52, 224, 0.2);
    }

    .btn-success {
      background-color: #28a745;
      border-color: #28a745;
      font-weight: 600;
      padding: 0.5rem 2rem;
    }

    .btn-success:hover {
      background-color: #218838;
      border-color: #1e7e34;
    }

    .tab-content .tab-pane {
      padding-top: 1rem;
    }

    .tab-content .lead {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .img-thumbnail {
      border-radius: 8px;
    }

    .profile-username {
      font-weight: 600;
      margin-top: 1rem;
      color: #0037af;
    }
      /* Navbar Design from Homepage */

    .navbar {
      background-color: #050C9C !important;
      padding: 1.2rem 1rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
      z-index: 1000;
    }

    .navbar-brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .navbar-brand img {
      height: 50px;
      width: 50px;
      object-fit: cover;
      border-radius: 50%;
      padding: 5px;
      background-color: white;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
      aspect-ratio: 1 / 1; /* Keeps it a perfect circle */
    }

    .navbar-brand span {
      font-size: 1.7rem;
      font-weight: 800;
      color: #A7E6FF !important;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .navbar-nav .nav-link {
      color: #A7E6FF !important;
      font-size: 18px;
      font-weight: 700;
      margin: 0 14px;
      transition: 0.3s ease;
      position: relative;
    }

    .navbar-nav .nav-link:hover {
      color: #FFF591 !important;
    }

    .navbar-nav .nav-link::after {
      content: '';
      display: block;
      width: 0%;
      height: 3px;
      background: #E41749;
      transition: 0.3s ease;
      position: absolute;
      bottom: -5px;
      left: 0;
    }

    .navbar-nav .nav-link:hover::after {
      width: 100%;
    }

    /* WIZARD TABS STYLING */
    .tab-nav-link {
      background-color: #ffffff !important;  /* Default: white background */
      color: #003366 !important;            /* Default: dark text */
      border: 1px solid #cccccc !important;
      font-weight: 600;
      /* LOCK TABS: Disable pointer events so they can't be clicked directly */
      pointer-events: none;
      cursor: default;
    }

    .tab-nav-link.active {
      background-color: #003366 !important; /* Active: dark blue background */
      color: #ffffff !important;           /* Active: white text */
    }

    .tab-nav-link:hover {
      background-color: #f0f0f0 !important; /* Light gray on hover */
      color: #003366 !important;
    }
  </style>
</head>
<body  class="hold-transition layout-top-nav">


<div class="wrapper">

  <nav class="main-header navbar navbar-expand-md" style="background-color: #0037af">
    <div class="container">
      <a href="" class="navbar-brand">
        <img src="assets/logo/ksugan.jpg" alt="logo">
        <span class="brand-text text-white" style="font-weight: 700">BARANGAY PORTAL</span>
      </a>

      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
      </div>

      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto " >
          <li class="nav-item">
            <a href="index.php" class="nav-link text-white rightBar" >HOME</a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link text-white rightBar" style="  border-bottom: 3px solid red;"><i class="fas fa-user-plus"></i> REGISTER</a>
          </li>
          <li class="nav-item">
            <a href="login.php" class="nav-link text-white rightBar"><i class="fas fa-user-alt"></i> LOGIN</a>
          </li>
      </ul>
    </div>
  </nav>
  <div class="content-wrapper double" id="backGround">
    
    <div class="content" >

      <div class="container-fluid py-5">

      <form id="registerResidentForm" method="POST" enctype="multipart/form-data" autocomplete="off">
      <div class="row mb-3">
        <div class="col-sm-4">
          <div class="card  h-100 transparent-card shadow-card">
            <div class="card-body" >
              <div class="text-center">
                <img class="profile-user-img img-fluid img-thumbnail" src="assets/dist/img/blank_image.png" alt="User profile picture" style="cursor: pointer;" id="image_residence">
                <input type="file" name="add_image_residence" id="add_image_residence" style="display: none;">
              </div>

              <h3 class="profile-username text-center "><span id="keyup_first_name"></span> <span id="keyup_last_name"></span></h3>

              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Voters <span class="text-danger">*</span></label>
                    <select name="add_voters" id="add_voters" class="form-control">
                      <option value=""></option>
                      <option value="NO">NO</option>
                      <option value="YES">YES</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-group ">
                    <label >Gender</label>
                    <select name="add_gender" id="add_gender" class="form-control">
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                
                    </select>
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
                    <label >Place of Birth</label>
                    <input type="text" class="form-control" id="add_birth_place" name="add_birth_place">
                  </div>
                </div>
                <div class="col-sm-12">
                          <div class="form-group ">
                            <label >PWD <span class="text-danger">*</span></label>
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
          <div class="card  card-tabs h-100 transparent-card shadow-card">
            <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
              <li class="nav-item">
                <a class="nav-link tab-nav-link active" id="basic-info-tab" data-toggle="pill" href="#basic-info">Basic Info</a>
              </li>
              <li class="nav-item">
                <a class="nav-link tab-nav-link" id="other-info-tab" data-toggle="pill" href="#other-info">Other Info</a>
              </li>
              <li class="nav-item">
                <a class="nav-link tab-nav-link" id="guardian-tab" data-toggle="pill" href="#guardian">Guardian</a>
              </li>
              <li class="nav-item">
                <a class="nav-link tab-nav-link" id="account-tab" data-toggle="pill" href="#account">Account</a>
              </li>
            </ul>

            </div>
            <div class="card-body" >
              <div class="tab-content" id="custom-tabs-one-tabContent">
                <div class="tab-pane fade active show" id="basic-info" role="tabpanel" aria-labelledby="basic-info-tab">
                    <p class="lead text-center lead-bold">Personal Details</p>
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
                            <label >Civil Status</label>
                            <select name="add_civil_status" id="add_civil_status" class="form-control">
                              <option value="Single">Single</option>
                              <option value="Married">Married</option>
                              <option value="Widowed">Widowed</option>
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
                            <label >Nationality</label>
                            <input type="text" class="form-control" id="add_nationality" name="add_nationality">
                          </div>
                        </div>                              
                      </div>
                </div>
                <div class="tab-pane fade" id="other-info" role="tabpanel" aria-labelledby="other-info-tab">
                      <p class="lead text-center lead-bold">Address</p>
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label>Municipality <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_municipality" name="add_municipality">
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label>Zip</label>
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
                            <label >Contact Number <span class="text-danger">*</span></label>
                            <input type="text" maxlength="11" class="form-control" id="add_contact_number" name="add_contact_number" >
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label>Email Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_email_address" name="add_email_address" >
                          </div>
                        </div>
                      </div>
                </div>
                <div class="tab-pane fade" id="guardian" role="tabpanel" aria-labelledby="guardian-tab">
                 
                    <p class="lead text-center lead-bold">Guardian</p>
                    <div class="row">

                      <div class="col-sm-12">
                        <div class="form-group">
                          <label>Father's Name</label>
                          <input type="text" class="form-control" id="add_fathers_name" name="add_fathers_name" >
                        </div>
                      </div>
                      <div class="col-sm-12">
                        <div class="form-group">
                          <label>Mother's Name</label>
                          <input type="text" class="form-control" id="add_mothers_name" name="add_mothers_name" >
                        </div>
                      </div>
                      <div class="col-sm-12">
                        <div class="form-group">
                          <label>Guardian</label>
                          <input type="text" class="form-control" id="add_guardian" name="add_guardian" >
                        </div>
                      </div>
                      <div class="col-sm-12">
                        <div class="form-group">
                          <label>Guardian Contact</label>
                          <input type="text" class="form-control" maxlength="11" id="add_guardian_contact" name="add_guardian_contact" >
                        </div>
                      </div>

                    </div>
                  
                </div>
                <div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="account-tab">
                 
                    <p class="lead text-center lead-bold">Account</p>
                                <div class="row">
                                  
                                  <div class="col-sm-12 ">
                                    <div class="form-group">
                                      <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text bg-transparent"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" id="add_username" name="add_username" class="form-control" placeholder="USERNAME" >
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-sm-12 ">
                                    <div  class="form-group">
                                      <div class="input-group mb-3" id="show_hide_password">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text bg-transparent"><i class="fas fa-key"></i></span>
                                        </div>
                                        <input type="password"  id="add_password" name="add_password" class="form-control" placeholder="PASSWORD"  style="border-right: none;" >
                                        <div class="input-group-append bg">
                                          <span class="input-group-text bg-transparent"> <a href="" style=" text-decoration:none;"><i class="fas fa-eye-slash" aria-hidden="true"></i></a></span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-sm-12 ">
                                    <div  class="form-group">
                                      <div class="input-group mb-3" id="show_hide_password_confirm">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text bg-transparent"><i class="fas fa-key"></i></span>
                                        </div>
                                        <input type="password"  id="add_confirm_password" name="add_confirm_password" class="form-control" placeholder="CONFIRM PASSWORD"  style="border-right: none;" >
                                        <div class="input-group-append bg">
                                          <span class="input-group-text bg-transparent"> <a href="" style=" text-decoration:none;"><i class="fas fa-eye-slash" aria-hidden="true"></i></a></span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                  
                </div>
              </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
              <button type="button" id="btn-back" class="btn btn-secondary px-4 elevation-2" style="display:none;"> <i class="fas fa-arrow-left"></i> Back</button>
              <button type="button" id="btn-next" class="btn btn-primary px-4 elevation-2"> Next <i class="fas fa-arrow-right"></i></button>
              <button type="button" id="btn-submit" class="btn btn-success px-4 elevation-3" style="display:none;"> <i class="fas fa-user-plus"></i> REGISTER</button>
            </div> 
            </div>

        </div>
      </div>
      </form>

      </div><div class="modal fade" id="dataPrivacyModal" tabindex="-1" aria-labelledby="dataPrivacyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title" id="dataPrivacyModalLabel"><i class="fas fa-user-shield"></i> Data Privacy Agreement</h5>
      </div>
      <div class="modal-body" style="max-height:600px; overflow-y:auto;">
        <p>
          In compliance with the Data Privacy Act of 2012 (R.A. 10173), we are committed to protecting your personal information.
          By proceeding with registration, you consent to the collection, processing, and storage of your data for Barangay records,
          official transactions, and relevant services.
        </p>
        <p>
          Your information will only be accessed by authorized personnel and will not be shared with third parties without your consent,
          unless required by law. You have the right to access, correct, and request deletion of your data.
        </p>
        <p>
          Please read this agreement carefully before continuing your registration.
        </p>
        <h2>1. Introduction</h2>
        <p>The Barangay Kalusugan is committed to protecting the privacy and security of your personal information. This notice outlines how we collect, use, and safeguard your data in compliance with the Data Privacy Act of 2012 (Republic Act No. 10173) and its implementing rules and regulations.</p>

        <h2>9. Acknowledgment</h2>
        <p>By providing your personal information to Barangay Kalusugan, you acknowledge that you have read and understood this Data Privacy Act Notice.</p>

        
      </div>
      <div class="modal-footer">
        <button type="button" id="agreeButton" class="btn btn-success">Agree and Register</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="householdModal" tabindex="-1" aria-labelledby="householdModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title" id="householdModalLabel">
          <i class="fas fa-home"></i> Household Verification
        </h5>
      </div>
      <div class="modal-body">
        <p class="lead">We found an existing household at this address!</p>
        
        <div class="alert alert-info">
          <i class="fas fa-info-circle"></i> 
          <strong>Existing Household:</strong>
          <div id="existingHouseholdInfo" class="mt-2">
            </div>
        </div>
        
        <p>Are you part of this household or are you a new household?</p>
        
        <div class="form-group" id="relationshipField" style="display: none;">
          <label>Your relationship to the household head:</label>
          <select class="form-control" id="relationship_to_head">
            <option value="Spouse">Spouse</option>
            <option value="Child">Child</option>
            <option value="Parent">Parent</option>
            <option value="Sibling">Sibling</option>
            <option value="Relative">Relative</option>
            <option value="Tenant">Tenant</option>
            <option value="Other">Other</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="newHouseholdBtn">
          <i class="fas fa-plus-circle"></i> New Household
        </button>
        <button type="button" class="btn btn-primary" id="joinHouseholdBtn">
          <i class="fas fa-user-plus"></i> Join Household
        </button>
      </div>
    </div>
  </div>
</div>
     
    </div>
    </div>
  <script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/dist/js/adminlte.js"></script>
<script src="assets/plugins/bs-stepper/js/bs-stepper.min.js"></script>
<script src="assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="assets/plugins/jquery-validation/additional-methods.min.js"></script>
<script src="assets/plugins/phone code/intlTelInput.js"></script>
<script src="assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>
<script src="assets/plugins/step-wizard/js/jquery.smartWizard.min.js"></script>
<script>
  $(document).ready(function(){
    
    // Store if we're in household modal mode
    let householdModalShown = false;
    let existingHouseholdData = null;
 
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
 $(function () {
      
      $('#registerResidentForm').validate({
       ignore: ":hidden", 
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
          add_gender: {
            required: true,
          },
          add_contact_number: {
            required: true,
            minlength: 11
          },
          add_email_address: {
            required: true,
            email: true
          },
          add_voters: {
            required: true,
          },
          add_pwd: {
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
            minlength: 8
          },
          add_pwd:{
            required: true,
          },
          add_voters:{
            required: true,
          },
          add_single_parent:{
            required: true,
          },
          add_pwd_info:{
             required: function(){ return $("#add_pwd").val() === 'YES'; }
          },
          add_address: {
            required: true,
          },
          add_municipality: {
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
        
        },
        messages: {
          add_first_name: {
            required: "This Field is required",
            minlength: "First Name must be at least 2 characters long"
          },
          add_last_name: {
            required: "This Field is required",
            minlength: "Last Name must be at least 2 characters long"
          },
        
          add_contact_number: {
            required: "This Field is required",
            minlength: "Input Exact Contact Number"
          },
          add_email_address: {
            required: "This Field is required",
            email: "Please enter a valid email address"
          },
            add_birth_date: {
            required: "This Field is required",
          },
          add_gender: {
            required: "This Field is required",
          },
      
         
          add_pwd: {
            required: "This Field is required",
          },
       
       
         
          add_username: {
            required: "This Field is required",
            minlength: "Username must be at least 8 characters long"
          },
          add_password: {
            required: "This Field is required",
            minlength: "Password must be at least 8 characters long"
          },
          add_confirm_password: { 
            required: "This Field is required",
            minlength: "Confirm Password must be at least 8 characters long"
          },
            
        },
   
     
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        },
      
      });
      
    })

    function togglePasswordVisibility(wrapper){
        $(wrapper + " a").on('click', function(e){
        e.preventDefault();
        var input = $(wrapper + " input");
        var icon = $(wrapper + " i");
        if(input.attr("type") === "text"){
            input.attr('type','password');
            icon.addClass("fa-eye-slash").removeClass("fa-eye");
        } else {
            input.attr('type','text');
            icon.removeClass("fa-eye-slash").addClass("fa-eye");
        }
        });
    }
    togglePasswordVisibility("#show_hide_password");
    togglePasswordVisibility("#show_hide_password_confirm");


    $("#image_residence").click(function(){
          $("#add_image_residence").click();
      });

      function displayImge(input){
      if(input.files && input.files[0]){
        var reader = new FileReader();
        var add_image = $("#add_image_residence").val().split('.').pop().toLowerCase();

        if(add_image != ''){
          if(jQuery.inArray(add_image,['gif','png','jpg','jpeg']) == -1){
            Swal.fire({
              title: '<strong class="text-danger">ERROR</strong>',
              type: 'error',
              html: '<b>Invalid Image File<b>',
              width: '400px',
              confirmButtonColor: '#6610f2',
            })
            $("#add_image_residence").val('');
            $("#image_residence").attr('src', 'assets/dist/img/blank_image.png');
            return false;
          }
        }

        reader.onload = function(e){
          $("#image_residence").attr('src',e.target.result);
          $("#image_residence").hide();
          $("#image_residence").fadeIn(650);
        }

        reader.readAsDataURL(input.files[0]);

      }
    }  

    $("#add_image_residence").change(function(){
      displayImge(this);
    })
    
     // ------------------------------
    // Name display in profile
    // ------------------------------
    $('#add_first_name, #add_last_name').keyup(function() {
        var firstName = $('#add_first_name').val();
        var lastName = $('#add_last_name').val();
        $('#keyup_first_name').text(firstName);
        $('#keyup_last_name').text(lastName);
    });

  });

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

 
  $("#add_contact_number,#add_zip, #add_guardian_contact, #add_age").inputFilter(function(value) {
  return /^-?\d*$/.test(value); 
  
  });


  $("#add_first_name, #add_middle_name, #add_last_name, #add_suffix, #add_religion, #add_nationality, #add_municipality, #add_fathers_name, #add_mothers_name, #add_guardian").inputFilter(function(value) {
  return /^[a-z, ]*$/i.test(value); 
  });
  
  $("#add_street, #add_birth_place, #add_house_number").inputFilter(function(value) {
  return /^[0-9a-z, ,-]*$/i.test(value); 
  });


$(document).ready(function(){

  // ============================================
  // WIZARD NAVIGATION LOGIC
  // ============================================
  var navTabs = ['#basic-info-tab', '#other-info-tab', '#guardian-tab', '#account-tab'];
  var tabPanes = ['#basic-info', '#other-info', '#guardian', '#account'];
  var currentTabIndex = 0;

  function updateButtons() {
    // Hide all buttons first
    $('#btn-back').hide();
    $('#btn-next').hide();
    $('#btn-submit').hide();

    // Show Back button if not on first step
    if(currentTabIndex > 0) {
      $('#btn-back').show();
    }

    // Show Next button if not on last step, otherwise show Submit
    if(currentTabIndex < navTabs.length - 1) {
      $('#btn-next').show();
    } else {
      $('#btn-submit').show();
    }
  }

  $('#btn-next').click(function() {
    // Validate current tab's inputs specifically
    var currentTabId = tabPanes[currentTabIndex];
    var isValid = true;

    // Select all inputs, selects in the current tab pane
    // Also include sidebar inputs if we are on the first tab (Basic Info)
    var inputsToValidate = $(currentTabId).find('input, select, textarea');
    
    if (currentTabIndex === 0) {
      // Add sidebar inputs to validation list for step 1
      var sidebarInputs = $('.col-sm-4').find('input, select');
      inputsToValidate = inputsToValidate.add(sidebarInputs);
    }

    // Check validity
    inputsToValidate.each(function() {
      // This will now respect the ignore: ":hidden" rule, so hidden fields wont block
      if (!$(this).valid()) {
        isValid = false;
      }
    });

    if (isValid) {
      // Move to next tab
      currentTabIndex++;
      $(navTabs[currentTabIndex]).tab('show'); // Bootstrap 4 tab show
      updateButtons();
    } else {
      // Focus on first invalid element
      inputsToValidate.filter('.is-invalid').first().focus();
    }
  });

  $('#btn-back').click(function() {
    if (currentTabIndex > 0) {
      currentTabIndex--;
      $(navTabs[currentTabIndex]).tab('show');
      updateButtons();
    }
  });

  // Handle "Register" Button Click (Account Tab) - WIZARD STEP
  $('#btn-submit').click(function() {
    // Validate the Account tab inputs
    var inputsToValidate = $('#account').find('input, select, textarea');
    var isValid = true;

    inputsToValidate.each(function() {
      if (!$(this).valid()) {
        isValid = false;
      }
    });

    if (isValid) {
      // Show Data Privacy Modal only if valid
      $('#dataPrivacyModal').modal('show');
    } else {
      inputsToValidate.filter('.is-invalid').first().focus();
    }
  });

  // Handle "Agree and Register" Click (Inside Privacy Modal)
  $('#agreeButton').click(function() {
    // Get the form element
    var form = $('#registerResidentForm')[0];
    
    // Close privacy modal
    $('#dataPrivacyModal').modal('hide');

    // If household modal was already shown and we have household data, show modal again
    if (typeof householdModalShown !== 'undefined' && householdModalShown && existingHouseholdData) {
        showHouseholdModal(existingHouseholdData);
        return false;
    }

    // Execute AJAX Registration using the new Function
    submitFormData(new FormData(form));
  });

  // Initialize buttons
  updateButtons();

  // Prevent default tab clicking just in case CSS fails, though pointer-events:none handles it
  $('.nav-link.tab-nav-link').on('click', function(e) {
     e.preventDefault();
     return false;
  });


  // ============================================
  // NEW FUNCTIONS FROM register(new).php
  // ============================================

    // ------------------------------
    // Main form submission function
    // ------------------------------
    function submitFormData(formData, householdAction = null, householdId = null, relationship = null) {
        console.log('submitFormData called with:', { householdAction, householdId, relationship });
        
        // Re-capture form data to ensure we have the latest (including hidden fields added dynamically if any)
        var newFormData = new FormData($('#registerResidentForm')[0]);
        
        // Add household parameters if provided
        if (householdAction) {
            newFormData.append('household_action', householdAction);
        }
        if (householdId) {
            newFormData.append('household_id', householdId);
        }
        if (relationship) {
            newFormData.append('relationship_to_head', relationship);
        }
        
        $.ajax({
            url: 'signup/newResidence.php',
            type: 'POST',
            data: newFormData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function(){
                // Visual feedback on the register button in modal or wizard
                // Since we trigger this from modal mostly, we can use SweetAlert loading or change modal btn
                // But for now, we leave visual feedback to SweetAlert or existing buttons
            },
            success: function(response){
                console.log('Raw Response:', response);
                
                try {
                    var res = (typeof response === 'object') ? response : JSON.parse(response);
                    
                    if(res.status === 'success'){
                        // Registration successful
                        let message = 'Registration Successful!';
                        if (res.action === 'join') {
                            message += '<br>You have joined the existing household.';
                        } else {
                            message += '<br>You are now the head of a new household.';
                        }
                        
                        Swal.fire({
                            title: 'SUCCESS!',
                            html: '<div class="text-center">' +
                                '<i class="fas fa-check-circle text-success fa-3x mb-3"></i>' +
                                '<h4>' + message + '</h4>' +
                                '<p class="text-muted">Your Household Number: <strong>' + (res.household_number || '') + '</strong></p>' +
                                '</div>',
                            type: 'success',
                            confirmButtonText: 'Go to Login',
                            showCancelButton: false,
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.value) { // Sweetalert2 uses result.value or isConfirmed
                                window.location.href = 'login.php';
                            }
                        });
                    } 
                    else if(res.status === 'showHouseholdModal'){
                        // Show household modal
                        householdModalShown = true;
                        existingHouseholdData = res.household;
                        
                        if (res.household) {
                            showHouseholdModal(res.household);
                        }
                    } 
                    else if(res.status === 'errorPassword'){
                        Swal.fire('ERROR','Password does not match','error');
                    } 
                    else if(res.status === 'errorUsername'){
                        Swal.fire('ERROR','Username already taken','error');
                    } 
                    else {
                        Swal.fire('ERROR', res.message || 'An unexpected error occurred', 'error');
                    }
                } catch(e) {
                    // Fallback for non-JSON responses (legacy support if backend sends plain text)
                    if(response == 'errorPassword'){
                         Swal.fire('ERROR','Password does not match','error');
                    } else if (response == 'errorUsername'){
                         Swal.fire('ERROR','Username already taken','error');
                    } else {
                        console.error('JSON Parse Error:', e, 'Response:', response);
                        Swal.fire('ERROR','Invalid server response format','error');
                    }
                }
            },
            error: function(xhr, status, error){
              console.error('AJAX Error:', error);
              console.log('Server Response:', xhr.responseText); // This logs the actual text sent by server
              Swal.fire('ERROR','AJAX request failed. Check console for details.','error');
          }
        });
    }

    // ------------------------------
    // Household Modal Functions
    // ------------------------------
    function showHouseholdModal(household){
        if (!household) return;
        
        console.log('Showing household modal with:', household);
        
        // Format head name
        let headFirstName = household.head_first_name || 'Not assigned';
        let headLastName = household.head_last_name || '';
        let headName = (headFirstName === 'Not assigned' && headLastName === '') ? 
                    'No head assigned yet' : `${headFirstName} ${headLastName}`;
        
        // Create modal content
        let modalContent = `
            <div class="household-details">
                <p><strong>Address:</strong> ${household.address || 'N/A'}</p>
                <p><strong>Head of Household:</strong> ${headName}</p>
        `;
        
        // Add household number if available
        if (household.household_number) {
            modalContent += `<p><strong>Household #:</strong> ${household.household_number}</p>`;
        }
        
        modalContent += `
            </div>
            <input type="hidden" id="existing_household_id" value="${household.id || ''}">
            <input type="hidden" id="existing_household_head_id" value="${household.household_head_id || ''}">
        `;
        
        $('#existingHouseholdInfo').html(modalContent);
        
        // Reset relationship field
        $('#relationshipField').hide();
        $('#relationship_to_head').val('Spouse');
        
        // Show modal
        $('#householdModal').modal({
            backdrop: 'static', 
            keyboard: false,
            show: true
        });
    }

    // Join household button
    $(document).on('click', '#joinHouseholdBtn', function(){
        
        var householdId = $('#existing_household_id').val();
        var headId = $('#existing_household_head_id').val();
        var relationship = $('#relationship_to_head').val();
        
        if (!householdId || householdId === '') {
            Swal.fire('Error', 'No household selected', 'error');
            return;
        }
        
        // Show relationship field first
        $('#relationshipField').show();
        
        // Focus on relationship dropdown
        $('#relationship_to_head').focus();
        
        // Show confirmation with relationship selection
        Swal.fire({
            title: 'Join Household?',
            html: 'You will be added as a member of this household.<br><br>' +
                '<strong>Your Relationship:</strong> ' + 
                '<select id="swalRelationship" class="form-control mt-2">' +
                '<option value="Spouse">Spouse</option>' +
                '<option value="Child">Child</option>' +
                '<option value="Parent">Parent</option>' +
                '<option value="Sibling">Sibling</option>' +
                '<option value="Relative">Relative</option>' +
                '<option value="Tenant">Tenant</option>' +
                '<option value="Other">Other</option>' +
                '</select>',
            type: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Join Household',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            preConfirm: () => {
                return {
                    relationship: document.getElementById('swalRelationship').value
                };
            }
        }).then((result) => {
            if (result.value) {
                var relationshipValue = result.value.relationship;
                
                // Clear modal state
                householdModalShown = false;
                existingHouseholdData = null;
                
                // Submit with join action
                $('#householdModal').modal('hide');
                
                // Create FormData and submit
                var formData = new FormData($('#registerResidentForm')[0]);
                submitFormData(formData, 'join', householdId, relationshipValue);
            }
        });
    });

    // New household button
    $(document).on('click', '#newHouseholdBtn', function(){
        
        Swal.fire({
            title: 'Create New Household?',
            html: 'You will be registered as the <strong>head of a new household</strong>.<br><br>' +
                'This is recommended if you are not related to the existing household.',
            type: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Create New Household',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.value) {
                // Clear modal state
                householdModalShown = false;
                existingHouseholdData = null;
                
                // Submit with new action
                $('#householdModal').modal('hide');
                
                // Create FormData and submit
                var formData = new FormData($('#registerResidentForm')[0]);
                submitFormData(formData, 'new', 0);
            }
        });
    });

});
</script>

</body>
</html>