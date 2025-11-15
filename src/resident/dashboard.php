<?php
session_start();
include_once '../connection.php';

  // Resident Dashboard
try {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'resident') {
        echo '<script>window.location.href = "../login.php";</script>';
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Users
    $sql_user = "SELECT id, first_name, last_name, image FROM users WHERE id = ?";
    $stmt_user = $con->prepare($sql_user) or die($con->error);
    $stmt_user->bind_param('s', $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $row_user = $result_user->fetch_assoc();
    $first_name_user = $row_user['first_name'] ?? '';
    $last_name_user  = $row_user['last_name'] ?? '';
    $user_image      = $row_user['image'] ?? '';

    // Residence info
    $sql_resident = "SELECT ri.*, rs.* 
                     FROM residence_information ri
                     INNER JOIN residence_status rs ON ri.residence_id = rs.residence_id
                     WHERE ri.residence_id = ?";
    $stmt_resident = $con->prepare($sql_resident) or die($con->error);
    $stmt_resident->bind_param('s', $user_id);
    $stmt_resident->execute();
    $row_resident = $stmt_resident->get_result()->fetch_assoc();

    // Barangay info
    $sql = "SELECT * FROM barangay_information LIMIT 1";
    $query = $con->prepare($sql) or die($con->error);
    $query->execute();
    $row = $query->get_result()->fetch_assoc();
    $barangay = $row['barangay'] ?? '';
    $zone     = $row['zone'] ?? '';
    $district = $row['district'] ?? '';

    // Fetch all dashboard images (BLOB)
    $imagesRes = $con->query("SELECT * FROM dashboard_images ORDER BY id DESC") or die($con->error);
    $dashboard_images = [];
    while($r = $imagesRes->fetch_assoc()) { $dashboard_images[] = $r; }

} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Resident Dashboard</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<!-- Plugins & Styles -->
<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">

<!-- Slideshow Styling -->
<style>
body, .wrapper, .content-wrapper {
    background-color: #ffffff !important;
    font-family: 'Poppins', sans-serif;
}

/* Navbar */
.main-header.navbar {
    background-color: #050C9C !important;
}
.navbar .nav-link { color: #ffffff !important; }

/* Sidebar */
.main-sidebar { background-color: #050C9C !important; }
.sidebar .nav-link { color: #A7E6FF !important; }
.sidebar .nav-link.active, .sidebar .nav-link:hover { background-color: #3572EF !important; color: #ffffff !important; }
.sidebar .nav-icon { color: #3ABEF9 !important; }

/* Dropdown */
.dropdown-menu { border-radius: 10px; border: none; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
.dropdown-item { font-weight: 600; transition: 0.2s ease-in-out; }
.dropdown-item:hover { background-color: #F5587B; color: white; }

/* Fullscreen Slideshow */
.slideshow-container { 
    width: 100%; 
    height: calc(100vh - 60px); 
    position: relative; 
    overflow: hidden;
}

.mySlides { display: none; }

.mySlides img { 
  width: 100%;
  height: auto;
  max-height: calc(100vh - 60px);
  object-fit: contain;
}

.dot { height: 15px; width: 15px; margin: 0 2px; background-color: #bbb; border-radius: 50%; display: inline-block; transition: background-color 0.6s ease; }
.active { background-color: #717171; }
.fade { animation-name: fade; animation-duration: 4.5s; }
@keyframes fade { from {opacity: .7} to {opacity: 1} }
</style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-footer-fixed">
<div class="wrapper">

<!-- Preloader -->
<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble" src="../assets/dist/img/loader.gif" alt="Loading" height="70" width="70">
</div>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark">
  <ul class="navbar-nav">
    <li class="nav-item"><h5><a class="nav-link text-white" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></h5></li>
    <li class="nav-item d-none d-sm-inline-block" style="font-variant: small-caps;"><h5 class="nav-link text-white"><?= $barangay ?></h5>
    <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
    <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= htmlspecialchars($zone) ?></h5></li>
    <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
    <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= htmlspecialchars($district) ?></h5></li>
  </ul>

  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-user"></i></a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <a href="myProfile.php" class="dropdown-item">
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
        </a>
        <div class="dropdown-divider"></div>
        <a href="../logout.php" class="dropdown-item dropdown-footer">LOGOUT</a>
      </div>
    </li>
  </ul>
</nav>

<!-- Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
  <!-- Barangay Logo -->
  <img src="../assets/logo/ksugan.jpg" alt="Barangay Logo" class="img-circle elevation-5 img-bordered-sm" style="width:70%; margin:10px auto; display:block;">
  
  <!-- Sidebar -->
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item"><a href="dashboard.php" class="nav-link active"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        <li class="nav-item"><a href="personalInformation.php" class="nav-link"><i class="nav-icon fas fa-id-card"></i><p>Personal Information</p></a></li>
        <li class="nav-item"><a href="myRecord.php" class="nav-link"><i class="nav-icon fas fa-user-tie"></i><p>Blotter Record</p></a></li>
        <li class="nav-item"><a href="drrmPlan.php" class="nav-link"><i class="fas fa-clipboard-list nav-icon text-red"></i><p>Emergency Plan</p></a></li>
        <li class="nav-item"><a href="changePassword.php" class="nav-link"><i class="nav-icon fas fa-lock"></i><p>Change Password</p></a></li>
        <li class="nav-item"><a href="certificate.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Certificate</p></a></li>
      </ul>
    </nav>
  </div>
</aside>

<!-- Content -->
<div class="content-wrapper p-0">
  <section class="content">
    <div class="container-fluid p-0">

      <!-- Fullscreen Slideshow -->
      <div class="slideshow-container">
        <?php foreach ($dashboard_images as $img): ?>
          <?php if (!empty($img['image'])): ?>
            <?php $base64 = base64_encode($img['image']); ?>
            <div class="mySlides fade">
              <img src="data:image/jpeg;base64,<?= $base64 ?>">
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <!-- Dots -->
      <div style="text-align:center; position:absolute; bottom:20px; width:100%;">
        <?php foreach ($dashboard_images as $img): ?>
          <?php if (!empty($img['image'])): ?>
            <span class="dot"></span>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

    </div>
  </section>
</div>

<footer class="main-footer">
  <strong>Copyright &copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')); ?></strong>
</footer>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>

<!-- Slideshow Script -->
<script>
let slideIndex = 0;
showSlides();
function showSlides() {
  const slides = document.getElementsByClassName("mySlides");
  const dots = document.getElementsByClassName("dot");

  for (let i = 0; i < slides.length; i++) { slides[i].style.display = "none"; }
  slideIndex++;
  if (slideIndex > slides.length) { slideIndex = 1; }

  for (let i = 0; i < dots.length; i++) { dots[i].className = dots[i].className.replace(" active", ""); }

  if (slides.length) {
    slides[slideIndex-1].style.display = "block";
    if(dots.length) dots[slideIndex-1].className += " active";
  }

  setTimeout(showSlides, 5000);
}
</script>
</body>
</html>
