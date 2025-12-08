<?php
session_start();
include_once 'connection.php';

// Redirect if already logged in
if(isset($_SESSION['user_id']) && $_SESSION['user_type']){
  $user_id = $_SESSION['user_id'];
  $sql = "SELECT * FROM users WHERE id = '$user_id'";
  $query = $con->query($sql) or die ($con->error);
  $row = $query->fetch_assoc();
  $account_type = $row['user_type'];
  
  if ($account_type == 'admin') {
      echo '<script>window.location.href="admin/dashboard.php";</script>';
  } elseif ($account_type == 'secretary') {
      echo '<script>window.location.href="secretary/dashboard.php";</script>';
  } else {
      echo '<script>window.location.href="resident/dashboard.php";</script>';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Barangay Registration Portal</title>
  
  <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/plugins/bs-stepper/css/bs-stepper.min.css">
  <link rel="stylesheet" href="assets/plugins/phone_code/intlTelInput.min.css">
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
      aspect-ratio: 1 / 1;
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
    .form-section {
      padding: 2rem;
      background: #ffffff;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
      margin: 2rem auto;
      max-width: 1200px;
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
    .disabled-tab {
       pointer-events: none;
       opacity: 0.5;
       cursor: not-allowed;
     }
  </style>
</head>

<body class="hold-transition layout-top-nav">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand-md">
    <div class="container">
      <a href="" class="navbar-brand">
        <img src="assets/logo/ksugan.jpg" alt="logo">
        <span class="brand-text text-white">BARANGAY PORTAL</span>
      </a>
      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse order-3" id="navbarCollapse"></div>
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
          <li class="nav-item">
            <a href="index.php" class="nav-link text-white rightBar">HOME</a>
          </li>
          <li class="nav-item">
            <a href="register.php" class="nav-link text-white rightBar" style="border-bottom: 3px solid red;"><i class="fas fa-user-plus"></i> REGISTER</a>
          </li>
          <li class="nav-item">
            <a href="login.php" class="nav-link text-white rightBar"><i class="fas fa-user-alt"></i> LOGIN</a>
          </li>
      </ul>
    </div>
  </nav>

  <div class="content-wrapper double" id="backGround">
    <div class="content">
      <div class="container-fluid py-5">
        <form id="registerResidentForm" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="row mb-3">
          <div class="col-sm-4">
            <div class="card h-100 transparent-card shadow-card">
              <div class="card-body">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-thumbnail" src="assets/dist/img/blank_image.png" alt="User profile picture" style="cursor: pointer;" id="image_residence">
                  <input type="file" name="add_image_residence" id="add_image_residence" style="display: none;">
                </div>
                <h3 class="profile-username text-center"><span id="keyup_first_name"></span> <span id="keyup_last_name"></span></h3>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Residency Type <span class="text-danger">*</span></label>
                      <select name="add_residency_type" id="add_residency_type" class="form-control">
                        <option value=""></option>
                        <option value="Resident">Resident</option>
                        <option value="Tenant">Tenant</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Gender</label>
                      <select name="add_gender" id="add_gender" class="form-control">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Date of Birth <span class="text-danger">*</span></label>
                      <input type="date" class="form-control" id="add_birth_date" name="add_birth_date">
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Place of Birth</label>
                      <input type="text" class="form-control" id="add_birth_place" name="add_birth_place">
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>PWD <span class="text-danger">*</span></label>
                      <select name="add_pwd" id="add_pwd" class="form-control">
                        <option value="NO" selected>NO</option>
                        <option value="YES">YES</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-12" id="pwd_check" style="display: none;">
                    <div class="form-group">
                      <label>TYPE OF PWD</label>
                      <input type="text" class="form-control" id="add_pwd_info" name="add_pwd_info">
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Single Parent <span class="text-danger">*</span></label>
                      <select name="add_single_parent" id="add_single_parent" class="form-control">
                        <option value="NO" selected>NO</option>
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
                  <li class="nav-item"><a class="nav-link tab-nav-link active" id="basic-info-tab" data-toggle="pill" href="#basic-info">Basic Info</a></li>
                  <li class="nav-item"><a class="nav-link tab-nav-link disabled-tab" id="other-info-tab" data-toggle="pill" href="#other-info">Other Info</a></li>
                  <li class="nav-item"><a class="nav-link tab-nav-link disabled-tab" id="guardian-tab" data-toggle="pill" href="#guardian">Guardian</a></li>
                  <li class="nav-item"><a class="nav-link tab-nav-link disabled-tab" id="account-tab" data-toggle="pill" href="#account">Account</a></li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                  
                  <div class="tab-pane fade active show" id="basic-info">
                    <p class="lead text-center lead-bold">Personal Details</p>
                    <div class="row">
                      <div class="col-sm-12"><div class="form-group"><label>First Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="add_first_name" name="add_first_name"></div></div>
                      <div class="col-sm-12"><div class="form-group"><label>Middle Name</label><input type="text" class="form-control" id="add_middle_name" name="add_middle_name"></div></div>
                      <div class="col-sm-12"><div class="form-group"><label>Last Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="add_last_name" name="add_last_name"></div></div>
                    </div>
                    <div class="row">
                      <div class="col-sm-6"><div class="form-group"><label>Suffix</label><input type="text" class="form-control" id="add_suffix" name="add_suffix"></div></div>
                      <div class="col-sm-6">
                        <div class="form-group"><label>Civil Status</label>
                          <select name="add_civil_status" id="add_civil_status" class="form-control">
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-sm-6"><div class="form-group"><label>Religion</label><input type="text" class="form-control" id="add_religion" name="add_religion"></div></div>
                      <div class="col-sm-6"><div class="form-group"><label>Nationality</label><input type="text" class="form-control" id="add_nationality" name="add_nationality" value="Filipino"></div></div>
                    </div>
                    <div class="card-footer step-footer">
                      <button type="button" id="proceed-basic" class="btn btn-success px-4 elevation-3"><i class="fas fa-arrow-right"></i> Proceed to Other Info</button>
                    </div>
                  </div>

                  <div class="tab-pane fade" id="other-info">
                    <p class="lead text-center lead-bold">Address</p>
                    <div class="row">
                      <div class="col-sm-6"><div class="form-group"><label>Municipality</label><input type="text" class="form-control" id="add_municipality" name="add_municipality" value="Quezon City" readonly></div></div>
                      <div class="col-sm-6"><div class="form-group"><label>Zip</label><input type="text" class="form-control" id="add_zip" name="add_zip" value="1112" readonly></div></div>
                      <div class="col-sm-6"><div class="form-group"><label>Barangay</label><input type="text" class="form-control" id="add_barangay" name="add_barangay" value="Barangay Kalusugan" readonly></div></div>
                      
                      <div class="col-sm-6"><div class="form-group"><label>House Number</label><input type="text" class="form-control" id="add_house_number" name="add_house_number"></div></div>
                      <div class="col-sm-6"><div class="form-group"><label>Street</label><input type="text" class="form-control" id="add_street" name="add_street"></div></div>
                      <div class="col-sm-6"><div class="form-group"><label>Contact Number <span class="text-danger">*</span></label><input type="text" maxlength="11" class="form-control" id="add_contact_number" name="add_contact_number"></div></div>
                      <div class="col-sm-6"><div class="form-group"><label>Email Address <span class="text-danger">*</span></label><input type="text" class="form-control" id="add_email_address" name="add_email_address"></div></div>
                    </div>
                    <div class="card-footer step-footer">
                      <button type="button" id="proceed-other" class="btn btn-success px-4 elevation-3"><i class="fas fa-arrow-right"></i> Proceed to Guardian</button>
                    </div>
                  </div>

                  <div class="tab-pane fade" id="guardian">
                    <p class="lead text-center lead-bold">Guardian</p>
                    <div class="row">
                      <div class="col-sm-12"><div class="form-group"><label>Father's Name</label><input type="text" class="form-control" id="add_fathers_name" name="add_fathers_name"></div></div>
                      <div class="col-sm-12"><div class="form-group"><label>Mother's Name</label><input type="text" class="form-control" id="add_mothers_name" name="add_mothers_name"></div></div>
                      <div class="col-sm-12"><div class="form-group"><label>Guardian</label><input type="text" class="form-control" id="add_guardian" name="add_guardian"></div></div>
                      <div class="col-sm-12"><div class="form-group"><label>Guardian Contact</label><input type="text" class="form-control" maxlength="11" id="add_guardian_contact" name="add_guardian_contact"></div></div>
                    </div>
                    <div class="card-footer step-footer">
                      <button type="button" id="proceed-guardian" class="btn btn-success px-4 elevation-3"><i class="fas fa-arrow-right"></i> Proceed to Account</button>
                    </div>
                  </div>

                  <div class="tab-pane fade" id="account">
                    <p class="lead text-center lead-bold">Account</p>
                    <div class="row">
                      <div class="col-sm-12">
                        <div class="form-group"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text bg-transparent"><i class="fas fa-user"></i></span></div><input type="text" id="add_username" name="add_username" class="form-control" placeholder="USERNAME"></div></div>
                      </div>
                      <div class="col-sm-12">
                        <div class="form-group"><div class="input-group mb-3" id="show_hide_password"><div class="input-group-prepend"><span class="input-group-text bg-transparent"><i class="fas fa-key"></i></span></div><input type="password" id="add_password" name="add_password" class="form-control" placeholder="PASSWORD" style="border-right: none;"><div class="input-group-append bg"><span class="input-group-text bg-transparent"><a href="" style="text-decoration:none;"><i class="fas fa-eye-slash" aria-hidden="true"></i></a></span></div></div></div>
                      </div>
                      <div class="col-sm-12">
                        <div class="form-group"><div class="input-group mb-3" id="show_hide_password_confirm"><div class="input-group-prepend"><span class="input-group-text bg-transparent"><i class="fas fa-key"></i></span></div><input type="password" id="add_confirm_password" name="add_confirm_password" class="form-control" placeholder="CONFIRM PASSWORD" style="border-right: none;"><div class="input-group-append bg"><span class="input-group-text bg-transparent"><a href="" style="text-decoration:none;"><i class="fas fa-eye-slash" aria-hidden="true"></i></a></span></div></div></div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <button type="submit" class="btn btn-success px-4 elevation-3"><i class="fas fa-user-plus"></i> REGISTER</button>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
        </form>
      </div>
    </div>
  </div>

<div class="modal fade" id="dataPrivacyModal" tabindex="-1" aria-labelledby="dataPrivacyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title" id="dataPrivacyModalLabel"><i class="fas fa-user-shield"></i> Data Privacy Agreement</h5>
      </div>
      <div class="modal-body" style="max-height:600px; overflow-y:auto;">
        <p>In compliance with the Data Privacy Act of 2012 (R.A. 10173), we are committed to protecting your personal information. By proceeding with registration, you consent to the collection, processing, and storage of your data for Barangay records, official transactions, and relevant services.</p>
        <p>Your information will only be accessed by authorized personnel and will not be shared with third parties without your consent, unless required by law. You have the right to access, correct, and request deletion of your data.</p>
        <p>Please read this agreement carefully before continuing your registration.</p>
        <h2>1. Introduction</h2>
        <p>The Barangay Kalusugan is committed to protecting the privacy and security of your personal information. This notice outlines how we collect, use, and safeguard your data in compliance with the Data Privacy Act of 2012 (Republic Act No. 10173) and its implementing rules and regulations.</p>
        <h2>2. Personal Information We Collect</h2>
        <p>We may collect the following types of personal information:</p>
        <ul><li>Full Name</li><li>Address</li><li>Date of Birth</li><li>Contact Information (e.g., phone number, email address)</li><li>Identification Documents (e.g., government-issued ID)</li><li>Other relevant information necessary for barangay services</li></ul>
        <h2>3. Purpose of Data Collection</h2>
        <p>Your personal information is collected for the following purposes:</p>
        <ul><li>To provide and improve barangay services</li><li>To maintain accurate records of residents</li><li>To facilitate communication regarding barangay activities and announcements</li></ul>
        <h2>4. Data Sharing and Disclosure</h2>
        <p>We may share your personal information with:</p>
        <ul><li>Government agencies as required by law</li><li>Third-party service providers who assist us in delivering services (with appropriate safeguards)</li><li>Other entities with your consent</li></ul>
        <h2>5. Data Security</h2>
        <p>We implement reasonable and appropriate security measures to protect your personal information from unauthorized access, disclosure, alteration, and destruction. These measures include:</p>
        <ul><li>Secure storage of physical and electronic records</li><li>Access controls to limit data access to authorized personnel only</li><li>Regular training for staff on data privacy and security</li></ul>
        <h2>6. Your Rights</h2>
        <p>As a data subject, you have the following rights under the Data Privacy Act:</p>
        <ul><li>The right to be informed about the collection and processing of your personal data</li><li>The right to access your personal data</li><li>The right to correct any inaccuracies in your personal data</li><li>The right to object to the processing of your personal data</li><li>The right to data portability</li><li>The right to erasure or blocking of your personal data</li></ul>
        <h2>7. How to Exercise Your Rights</h2>
        <div class="contact-info">
            <p>To exercise your rights or for any inquiries regarding your personal information, please contact:</p>
            <p><strong>Barangay Kalusugan Data Protection Officer or Secretay</strong><br>
        </div>
        <h2>8. Changes to This Notice</h2>
        <p>We may update this Data Privacy Act Notice from time to time. Any changes will be posted on our official bulletin board and website. We encourage you to review this notice periodically.</p>
        <h2>9. Acknowledgment</h2>
        <p>By providing your personal information to Barangay Kalusugan, you acknowledge that you have read and understood this Data Privacy Act Notice.</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="agreeButton" class="btn btn-success">I Agree</button>
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
<script src="assets/plugins/phone_code/intlTelInput.js"></script>
<script src="assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>
<script src="assets/plugins/step-wizard/js/jquery.smartWizard.min.js"></script>

<script>
$(document).ready(function(){

    // --- TAB NAVIGATION ---
    function isCurrentStepValid(tabId) {
        var isValid = true;
        $('#' + tabId + ' :input').each(function() {
            if (!$(this).valid()) isValid = false;
        });
        return isValid;
    }

    $('#proceed-basic').click(function(e) {
        e.preventDefault();
        if (isCurrentStepValid('basic-info')) {
            $('#other-info-tab').removeClass('disabled-tab').tab('show');
            $('#basic-info-tab').removeClass('active');
            $('#basic-info').removeClass('active show');
            $('#other-info').addClass('active show');
        } else {
            Swal.fire({ title: 'Missing Information', text: 'Please complete required fields.', type: 'warning', confirmButtonColor: '#28a745' });
        }
    });

    $('#proceed-other').click(function(e) {
        e.preventDefault();
        if (isCurrentStepValid('other-info')) {
            $('#guardian-tab').removeClass('disabled-tab').tab('show');
            $('#other-info-tab').removeClass('active');
            $('#other-info').removeClass('active show');
            $('#guardian').addClass('active show');
        } else {
            Swal.fire({ title: 'Missing Information', text: 'Please complete required fields.', type: 'warning', confirmButtonColor: '#28a745' });
        }
    });

    $('#proceed-guardian').click(function(e) {
        e.preventDefault();
        if (isCurrentStepValid('guardian')) {
            $('#account-tab').removeClass('disabled-tab').tab('show');
            $('#guardian-tab').removeClass('active');
            $('#guardian').removeClass('active show');
            $('#account').addClass('active show');
        } else {
            Swal.fire({ title: 'Missing Information', text: 'Please complete required fields.', type: 'warning', confirmButtonColor: '#28a745' });
        }
    });

    // --- PWD TOGGLE ---
    $("#add_pwd").change(function(){
      if($(this).val() == 'YES'){
        $("#pwd_check").css('display', 'block');
        $("#add_pwd_info").prop('disabled', false);
      }else{
        $("#pwd_check").css('display', 'none');
        $("#add_pwd_info").prop('disabled', true);
      }
    });

    // --- FORM VALIDATION ---
    $('#registerResidentForm').validate({
       ignore: [], 
       rules: {
          add_first_name: { required: true, minlength: 2 },
          add_last_name: { required: true, minlength: 2 },
          // Removed relationship rule here since it's now in popup
          add_birth_date: { required: true },
          add_gender: { required: true },
          add_contact_number: { required: true, minlength: 11 },
          add_email_address: { required: true, email: true },
          add_residency_type: { required: true },
          add_pwd: { required: true },
          add_username:{ required: true, minlength: 8 },
          add_password:{ required: true, minlength: 8 },
          add_confirm_password:{ required: true, minlength: 8, equalTo: "#add_password" }
          // Removed 'add_address' rule
       },
       messages: {
          add_first_name: { required: "Required", minlength: "At least 2 chars" },
          add_last_name: { required: "Required", minlength: "At least 2 chars" },
          add_confirm_password: { equalTo: "Passwords do not match" }
       },
       errorElement: 'span',
       errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
       },
       highlight: function (element) { $(element).addClass('is-invalid'); },
       unhighlight: function (element) { $(element).removeClass('is-invalid'); },
       
       submitHandler: function (form) {
            $('#dataPrivacyModal').modal('show');
            $('#agreeButton').off('click').on('click', function() {
                $('#dataPrivacyModal').modal('hide');
                var formData = new FormData(form);
                submitRegistration(formData); 
            });
            return false;
        }
    });

    // --- AJAX HELPER WITH LEGACY SWEETALERT CHAINING ---
    function submitRegistration(formData) {
        // We do NOT have relationship here yet. We will ask in popup.
        
        $.ajax({
            url: 'signup/newResidence.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function(data) {
                var response;
                try {
                    response = (typeof data === 'object') ? data : JSON.parse(data);
                } catch (e) {
                    response = { status: 'error', message: data };
                }

                if (response.status === 'success') {
                    Swal.fire({
                        title: 'SUCCESS',
                        type: 'success',
                        html: '<b>Registered Successfully!</b><br>Household #: ' + response.household_number,
                        timer: 2000,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = 'login.php';
                    });

                } else if (response.status === 'showHouseholdModal') {
                    // STEP 1: Household Found. Join or Create?
                    Swal.fire({
                        title: 'Existing Household Found',
                        type: 'question',
                        html: `
                            <p>We found a household at this address:</p>
                            <ul style="text-align:left; list-style:none;">
                                <li><strong>Head:</strong> ${response.household.head_first_name} ${response.household.head_last_name}</li>
                                <li><strong>Household #:</strong> ${response.household.household_number}</li>
                            </ul>
                            <br><b>Do you want to JOIN this household?</b>`,
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Join It',
                        cancelButtonText: 'No, Create New', // This button now serves as the "Create" trigger
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33'
                    }).then((result) => {
                        
                        if (result.value) {
                            // --- JOIN FLOW ---
                            askRelationshipAndSubmit('join', response.household.id);

                        } else {
                            // --- CREATE NEW FLOW ---
                            // Check if they really want to create new (since they clicked "No" to Join)
                             Swal.fire({
                                title: 'Create New Household?',
                                text: 'Do you want to create a brand new household record instead?',
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, Create New',
                                cancelButtonText: 'Cancel'
                            }).then((res2) => {
                                if (res2.value) {
                                    // Even if Creating New, we ask relationship (e.g. Son of a new household)
                                    askRelationshipAndSubmit('new', null);
                                }
                            });
                        }
                    });

                } else if (response.status === 'errorPassword') {
                    Swal.fire('Error', 'Passwords do not match', 'error');
                } else if (response.status === 'errorUsername') {
                    Swal.fire('Error', 'Username is already taken', 'error');
                } else {
                    Swal.fire({
                        title: 'Registration Failed',
                        type: 'error',
                        html: 'Server Message: ' + (response.message || 'Unknown Error')
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('System Error', 'Check console.', 'error');
            }
        });
    }

    // Helper function to ask relationship inside SweetAlert
    function askRelationshipAndSubmit(action, id) {
        Swal.fire({
            title: 'Select Relationship',
            text: 'What is your relationship to the Household Head?',
            input: 'select',
            inputOptions: {
                'Head': 'Head of Household',
                'Wife': 'Wife',
                'Husband': 'Husband',
                'Son': 'Son',
                'Daughter': 'Daughter',
                'Relative': 'Relative',
                'Tenant': 'Tenant',
                'Worker': 'Worker'
            },
            inputPlaceholder: 'Select relationship',
            showCancelButton: true,
            inputValidator: (value) => {
                return !value && 'You need to choose an option!'
            }
        }).then((relResult) => {
            if (relResult.value) {
                // User selected a relationship, now submit
                var form = $('#registerResidentForm')[0];
                var formData = new FormData(form);
                
                formData.set('household_action', action);
                if(id) formData.set('household_id', id);
                formData.set('relationship_to_head', relResult.value);
                
                // Call main function again, but this time it will succeed because action is set
                // Actually, let's call the recursive part manually to avoid validation loops
                submitFinalData(formData);
            }
        });
    }

    function submitFinalData(formData) {
        $.ajax({
            url: 'signup/newResidence.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            success: function(data) {
                 var response;
                try { response = (typeof data === 'object') ? data : JSON.parse(data); } 
                catch (e) { response = { status: 'error', message: data }; }

                if (response.status === 'success') {
                    Swal.fire({
                        title: 'SUCCESS',
                        type: 'success',
                        html: '<b>Registered Successfully!</b><br>Household #: ' + response.household_number,
                        timer: 2000,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
                } else {
                     Swal.fire('Error', response.message || 'Unknown Error', 'error');
                }
            }
        });
    }

    // --- UTILS ---
    $("#show_hide_password a, #show_hide_password_confirm a").on('click', function(event) {
        event.preventDefault();
        var input = $(this).closest('.input-group').find('input');
        var icon = $(this).find('i');
        if(input.attr("type") == "text"){
            input.attr('type', 'password');
            icon.addClass( "fa-eye-slash" ).removeClass( "fa-eye" );
        } else {
            input.attr('type', 'text');
            icon.removeClass( "fa-eye-slash" ).addClass( "fa-eye" );
        }
    });
    
    $("#image_residence").click(function(){ $("#add_image_residence").click(); });
    $("#add_image_residence").change(function(){
      if(this.files && this.files[0]){
        var reader = new FileReader();
        var add_image = $(this).val().split('.').pop().toLowerCase();
        if($.inArray(add_image,['gif','png','jpg','jpeg']) == -1){
            Swal.fire('Error', 'Invalid Image File', 'error');
            $(this).val('');
            return false;
        }
        reader.onload = function(e){
          $("#image_residence").attr('src',e.target.result).hide().fadeIn(650);
        }
        reader.readAsDataURL(this.files[0]);
      }
    });

}); 
</script>

<script>
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
$("#add_contact_number,#add_zip, #add_guardian_contact").inputFilter(function(value) { return /^-?\d*$/.test(value); });
$("#add_first_name, #add_middle_name, #add_last_name").inputFilter(function(value) { return /^[a-z, ]*$/i.test(value); });
</script>
</body>
</html>