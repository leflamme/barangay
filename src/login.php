<?php 
// loginnew.php
include_once 'connection.php';
session_start();

try{

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

}catch(Exception $e){
  echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Barangay Kalusugan</title>
  <!-- Website Icon -->
  <link rel="icon" type="image/png" href="assets/logo/ksugan.jpg">
  
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/plugins/sweetalert2/css/sweetalert2.min.css">
 

 <style>
/* ---------- Layout ---------- */
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}
body {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 0;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: linear-gradient(to right, #eef6ff, #f7fbff);
}

/* ---------- Login container ---------- */
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  position: relative;
  z-index: 1;
}

.login-box {
  display: flex;
  flex-direction: row;
  max-width: 950px;
  width: 100%;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 15px 45px rgba(0, 0, 0, 0.12);
  background: rgba(255, 255, 255, 1);
}

/* Left panel */
.login-left {
  background: linear-gradient(to right, #0037af, #85b6ff);
  width: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
}
.login-left img {
  height: 300px;
  width: 300px;
  object-fit: cover;
  border-radius: 50%;
  padding: 5px;
  background-color: white;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.12);
  aspect-ratio: 1 / 1;
}

/* Right panel */
.login-right {
  width: 50%;
  padding: 50px 40px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.login-right h3 {
  font-weight: 700;
  font-size: 28px;
  color: #0037af;
  margin-bottom: 25px;
  text-align: center;
}

/* ---------- Floating label form controls ---------- */
.form-group {
  position: relative;
  margin-bottom: 18px;
}

/* Inputs: use placeholder=" " so :placeholder-shown works consistently */
.form-control-float {
  width: 100%;
  border-radius: 10px;
  background-color: #f0f4f8;
  border: 1px solid #ccd6dd;
  padding: 18px 14px 12px 14px; /* extra top padding so label sits inside */
  font-size: 15px;
  transition: border-color .2s ease, box-shadow .2s ease;
}

/* Hide default placeholder text visually but keep it for :placeholder-shown detection */
.form-control-float::placeholder { color: transparent; }

/* Floating label */
.form-label {
  position: absolute;
  left: 14px;
  top: 18px;
  font-size: 15px;
  color: #6b7280;
  pointer-events: none;
  transition: transform .16s ease, font-size .16s ease, top .16s ease, color .16s ease;
  transform-origin: left top;
  background: transparent;
  padding: 0 6px;
}

/* When input is focused or has content - move label up */
.form-control-float:focus {
  outline: none;
  border-color: #0056d2;
  box-shadow: 0 0 0 3px rgba(0, 85, 210, 0.12);
}

.form-control-float:focus + .form-label,
.form-control-float:not(:placeholder-shown) + .form-label {
  top: -9px;
  transform: translateY(0);
  font-size: 12px;
  color: #0037af;
  background: white; /* small white background to avoid overlap with rounded input */
}

/* Password wrapper to include toggle */
.password-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.password-toggle {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 6px;
  color: #4b5563;
  outline: none;
  display: flex;
  align-items: center;
}

/* ensure toggle doesn't cover label */
.password-wrapper .form-control-float {
  padding-right: 46px;
}

/* login footer & buttons */
.login-footer {
  text-align: right;
  margin-bottom: 15px;
}
.login-footer a {
  font-size: 14px;
  color: #0056d2;
  text-decoration: none;
}

.btn-login {
  background: linear-gradient(to right, #0037af, #005bea);
  border: none;
  color: white;
  font-weight: bold;
  padding: 12px;
  font-size: 16px;
  border-radius: 50px;
  width: 100%;
  transition: all 0.18s ease;
}
.btn-login:hover {
  transform: scale(1.02);
  color: white;
  background: rgb(16, 57, 183);
}

/* responsive */
@media (max-width: 768px) {
  .login-box { flex-direction: column; }
  .login-left, .login-right { width: 100%; }
  .login-left { padding: 20px; }
  .login-right { padding: 30px 20px; }
}

/* Navbar styles */
.navbar {
  background-color: #050C9C !important;
  padding: 1.2rem 1rem;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
  z-index: 1000;
}
.navbar-brand { display: flex; align-items: center; gap: 12px; }
.navbar-brand img{ height:50px; width:50px; object-fit:cover; border-radius:50%; padding:5px; background:#fff; box-shadow:0 0 8px rgba(0,0,0,0.12);}
.navbar-brand span { font-size:1.7rem; font-weight:800; color:#A7E6FF !important; text-transform:uppercase; letter-spacing:1px;}
.navbar-nav .nav-link { color:#A7E6FF !important; font-size:18px; font-weight:700; margin:0 14px; transition:0.3s ease; position:relative;}
.navbar-nav .nav-link:hover { color:#FFF591 !important; }
</style>
</head>
<body  class="hold-transition layout-top-nav">

<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md " style="background-color: #0037af">
    <div class="container">
      <a href="" class="navbar-brand">
<img src="assets/logo/ksugan.jpg" alt="logo">
        <span class="brand-text  text-white" style="font-weight: 700">BARANGAY PORTAL</span>
      </a>

      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse order-3" id="navbarCollapse"></div>

      <!-- Right navbar links -->
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto " >
          <li class="nav-item">
            <a href="index.php" class="nav-link text-white rightBar" >HOME</a>
          </li>
          <li class="nav-item">
            <a href="register.php" class="nav-link text-white rightBar"><i class="fas fa-user-plus"></i> REGISTER</a>
          </li>
          <li class="nav-item">
            <a href="login.php" class="nav-link text-white rightBar" style="  border-bottom: 3px solid red;"><i class="fas fa-user-alt"></i> LOGIN</a>
          </li>
      </ul>
    </div>
  </nav>
  <!-- /.navbar -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" >
    <!-- Main content -->
<div class="login-container">
  <div class="login-box">

    <!-- Left Logo Panel -->
    <div class="login-left">
      <img src="assets/logo/ksugan.jpg" alt="Barangay Logo">
    </div>

    <!-- Right Login Form Panel -->
    <div class="login-right">
      <h3>Welcome Back</h3>
      <form id="loginForm" method="post" autocomplete="off">
        <!-- Username (floating label) -->
        <div class="form-group">
          <input type="text" id="username" name="username" class="form-control-float" placeholder=" " autocomplete="username" />
          <label for="username" class="form-label">Enter Email</label>
        </div>

        <!-- Password (floating label + toggle) -->
        <div class="form-group password-wrapper">
          <input type="password" id="password" name="password" class="form-control-float" placeholder=" " autocomplete="current-password" />
          <label for="password" class="form-label">Enter Password</label>
          <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
            <i class="fas fa-eye" id="toggleIcon"></i>
          </button>
        </div>

        <div class="login-footer">
          <a href="forgot.php">Forgot Password?</a>
        </div>
        <button type="submit" class="btn btn-login">Log In</button>
      </form>
    </div>

  </div>
</div>

      </div>

    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

</div>
<!-- ./wrapper -->

<footer class="main-footer text-white" style="background-color: #0037af">
    <div class="float-right d-none d-sm-block"></div>
  <i class="fas fa-map-marker-alt"></i> <?= !empty($postal_address) ? htmlspecialchars($postal_address) : ' New Manila, Quezon City, 1112' ?> 
</footer>

<!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.js"></script>
<script src="assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>

<script>
  $(document).ready(function() {

    // Toggle password visibility (works with floating label markup)
    $('#togglePassword').on('click', function() {
      const input = $('#password');
      const icon = $('#toggleIcon');
      if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
      } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
      }
      // keep focus on input after toggling
      input.focus();
    });

    // Login form submit
    $("#loginForm").submit(function(e){
      e.preventDefault();
      var username = $("#username").val();
      var password = $("#password").val();
      if(username == '' || password == ''){
        Swal.fire({
          title: '<strong class="text-danger">WARNING</strong>',
          icon: 'warning',
          html: '<b>Username and Password is Required</b>',
          width: '400px',
        })
      }else{
        $.ajax({
          url: 'loginForm.php',
          type: 'POST',
          data: $(this).serialize(),
          success:function(data){
              if(data == 'errorUsername'){
                Swal.fire({
                  title: '<strong class="text-danger">ERROR</strong>',
                  icon: 'error',
                  html: '<b>Incorrect Username or Password</b>',
                  width: '400px',
                })
              }else if(data =='errorPassword'){
                Swal.fire({
                  title: '<strong class="text-danger">ERROR</strong>',
                  icon: 'error',
                  html: '<b>Incorrect Username or Password</b>',
                  width: '400px',
                })
              }else if(data == 'admin'){
                Swal.fire({
                  title: '<strong class="text-success">SUCCESS</strong>',
                  type: 'success',
                  html: '<b>Login Successfully</b>',
                  width: '400px',
                  showConfirmButton:  false,
                  allowOutsideClick: false,
                  timer: 1200
                }).then(()=>{ window.location.href = 'admin/dashboard.php'; })
              }else if(data == 'secretary'){
                Swal.fire({
                  title: '<strong class="text-success">SUCCESS</strong>',
                  type: 'success',
                  html: '<b>Login Successfully</b>',
                  width: '400px',
                  showConfirmButton:  false,
                  allowOutsideClick: false,
                  timer: 1200
                }).then(()=>{ window.location.href = 'secretary/dashboard.php'; })
              }else if(data == 'resident'){
                Swal.fire({
                  title: '<strong class="text-success">SUCCESS</strong>',
                  type: 'success',
                  html: '<b>Login Successfully</b>',
                  width: '400px',
                  showConfirmButton:  false,
                  allowOutsideClick: false,
                  timer: 1200
                }).then(()=>{ window.location.href = 'resident/dashboard.php'; })
              }
          }
        })
      }
    });

    // Optional: ensure labels float correctly if browser autocomplete fills fields
    // Trigger label update on page load for prefilled values:
    $('.form-control-float').each(function(){
      if ($(this).val().length) {
        $(this).trigger('blur'); // triggers CSS :not(:placeholder-shown) behavior
      }
    });

  });
</script>

</body>
</html>
