<?php
//index.php (new)
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

  function make_query($con){

    $sql = "SELECT * FROM carousel";
    $query = $con->query($sql) or die ($con->error);
    return $query;
  }

function make_slide_indicators($con){

    $output = ''; 
    $count = 0;
    $result = make_query($con);
    while($row = $result->fetch_assoc()) {

      if($count == 0) {
     
      $output .= '
      <li data-target="#carouselExampleIndicators" data-slide-to="'.$count.'" class="active"></li>
      ';
     
      }else{
      
      $output .= '
      <li data-target="#carouselExampleIndicators" data-slide-to="'.$count.'"></li>
      ';
      }
      $count = $count + 1;
    }
    return $output;
}

function make_slides($con){

      $output = '';
      $count = 0;
      $result = make_query($con);
      while($row = mysqli_fetch_array($result)) {
     
      if($count == 0)  {
      
        $output .= '<div class="carousel-item active">';
        
      }else{
        
        $output .= '<div class="carousel-item">';
      }
        $output .= '
        <img class="d-block w-100" src="'.$row["banner_image_path"].'" alt="'.$row["banner_title"].'" />
          <div class="carousel-caption">
            <h3>'.$row["banner_title"].'</h3>
          </div>
        </div>
        ';
        $count = $count + 1;
      }
      return $output;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Barangay Kalusugan</title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Source+Sans+Pro:wght@400;600&display=swap" rel="stylesheet">

  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">

  <style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(to bottom, #050C9C, #3ABEF9);
  margin: 0;
  color: #ffffff;
  overflow-x: hidden;
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

.hero-section {
  min-height: 90vh;
  background: linear-gradient(to right, #050C9C, #3572EF);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 50px 20px;
}

.hero-content {
  max-width: 1000px;
  margin: auto;
}

.logo-img {
  height: 240px;
  width: 240px;
  border-radius: 50%;
  border: 8px solid #A7E6FF;
  background-color: #ffffff;
  padding: 8px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  margin-bottom: 25px;
}

.hero-content h1 {
  font-size: 60px;
  font-weight: 800;
  color: #FFF591;
  text-shadow: 2px 2px #050C9C;
}

.hero-content h4 {
  font-size: 24px;
  color: #A7E6FF;
  margin-bottom: 10px;
}

.hero-content p {
  font-size: 16px;
  color: #E0F7FF;
  margin-bottom: 30px;
  font-style: italic;
}

.btn-register {
  background-color: #E41749;
  color: white;
  font-weight: 700;
  padding: 12px 28px;
  border-radius: 40px;
  text-decoration: none;
  margin-right: 10px;
}

.btn-register:hover {
  background-color: #F5587B;
}

.btn-login {
  background-color: transparent;
  border: 2px solid #A7E6FF;
  color: #A7E6FF;
  font-weight: 700;
  padding: 12px 28px;
  border-radius: 40px;
  text-decoration: none;
}

.btn-login:hover {
  background-color: #A7E6FF;
  color: #050C9C;
}

.carousel-section {
  padding: 60px 0;
  background: #ffffff;
  color: #050C9C;
  text-align: center;
}

.carousel-caption h3 {
  color: white;
  font-weight: bold;
  text-shadow: 2px 2px #000;
}

.about-section {
  background: #f5f5f5;
  padding: 60px 30px;
  text-align: center;
  color: #050C9C;
}

.about-section h2 {
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 20px;
}

.about-section p {
  max-width: 800px;
  margin: 0 auto;
  font-size: 18px;
  line-height: 1.8;
}

footer.main-footer {
  background-color: #050C9C;
  border-top: 3px solid #3ABEF9;
  color: #A7E6FF;
  font-weight: 600;
  text-align: center;
  padding: 1rem;
}

@media screen and (max-width: 768px) {
  .hero-content h1 {
    font-size: 36px;
  }

  .hero-content h4 {
    font-size: 18px;
  }

  .logo-img {
    height: 160px;
    width: 160px;
  }

  .btn-register, .btn-login {
    display: block;
    margin: 10px auto;
    width: 80%;
  }
}

/* Loading Spinner */
#preloader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: #050C9C;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.spinner {
  border: 8px solid #A7E6FF;
  border-top: 8px solid #E41749;
  border-radius: 50%;
  width: 70px;
  height: 70px;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Extra Styling */
.hero-content .highlight {
  color: #FFF591;
  text-shadow: 2px 2px #050C9C;
}

/* E-GOVERNANCE SECTION */
.egov-section {
  background: rgba(255,255,255,0.06);
  padding: 40px 20px;
  color: #ffffff;
  text-align: center;
  margin-bottom: 30px;
}

.egov-container {
  max-width: 1100px;
  margin: 0 auto;
}

.egov-title {
  font-size: 38px;
  font-weight: 800;
  color: #050C9C;
  margin-bottom: 6px;
}

.egov-sub {
  margin-bottom: 22px;
  color: #A7E6FF;
  font-size: 15px;
}

/* grid */
.egov-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 18px;
  align-items: stretch;
  grid-auto-rows: 1fr;
}

/* card */
.egov-card {
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-decoration: none;
  padding: 18px 14px;
  border-radius: 12px;
  background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(0,0,0,0.04));
  transition: transform .18s ease, box-shadow .18s ease;
  color: #0a195cff;
  min-height: 200px;
  cursor: default; /* indicate non-clickable */
}

.egov-card:focus,
.egov-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 24px rgba(0,0,0,0.28);
  color: #050C9C;
  background: #A7E6FF;
}

.egov-icon {
  font-size: 36px;
  background: rgba(255,255,255,0.06);
  padding: 18px;
  border-radius: 50%;
  margin-bottom: 12px;
  width: 78px;
  height: 78px;
  display:flex;
  align-items:center;
  justify-content:center;
}

/* label */
.egov-label {
  font-weight: 800;
  font-size: 14px;
  text-transform: none;
  color: inherit;
  text-align: center;
  letter-spacing: .2px;
}

.egov-desc {
  margin-top: auto;
  font-size: 13px;
  line-height: 1.45;
  color: rgba(10,25,92,0.85);
  text-align: center;
}

/* responsive */
@media screen and (max-width: 992px) {
  .egov-grid { grid-template-columns: repeat(3, 1fr); }
}
@media screen and (max-width: 540px) {
  .egov-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
  .egov-title { font-size: 22px; }
  .egov-icon { width: 64px; height: 64px; font-size: 30px; padding: 14px; }
}

.about-section {
  background: #f5f5f5;
  padding: 60px 30px;
  text-align: center;
  color: #050c9c;
}

.about-section h2 {
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 20px;
}

.about-section p {
  max-width: 800px;
  margin: 0 auto;
  font-size: 18px;
  line-height: 1.8;
}

footer.main-footer {
  background-color: #050c9c;
  border-top: 3px solid #3ABEF9;
  color: #A7E6FF;
  font-weight: 600;
  text-align: center;
  padding: 1rem;
}

/* BARANGAY OFFICIALS SECTION */
.officials-section {
  background: rgba(255,255,255,0.06); /* same light transparent bg */
  padding: 60px 20px;
  color: #ffffff;
  text-align: center;
  margin-bottom: 30px;
}

.officials-title {
  font-size: 38px;
  font-weight: 800;
  color: #050C9C; /* same dark blue as egov-section title */
  margin-bottom: 25px;
}

.officials-container {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(4, 1fr); /* 4 cards per row */
  gap: 18px;
}

/* Card Styling — matched to e-governance cards */
.official-card {
  background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(0,0,0,0.04));
  padding: 18px 14px;
  border-radius: 12px;
  text-align: center;
  color: #0a195cff; /* same deep navy text */
  min-height: 160px;
  display: flex;
  flex-direction: column;
  justify-content: center;

  transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
}

.official-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 24px rgba(0,0,0,0.28);
  background: #A7E6FF; /* SAME hover blue color */
  color: #050C9C; /* same hover text color */
}

.official-name {
  font-size: 18px;
  font-weight: 800;
  margin-bottom: 6px;
}

.official-title {
  font-size: 14px;
  font-weight: 600;
  color: rgba(10,25,92,0.85);
}

/* Responsiveness — same behavior as e-gov cards */
@media screen and (max-width: 992px) {
  .officials-container {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media screen and (max-width: 540px) {
  .officials-container {
    grid-template-columns: 1fr;
  }
}


  </style>
</head>

<body class="hold-transition layout-top-nav">

<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md" style="background-color: #0037af">
    <div class="container">
      <a href="" class="navbar-brand">
        <img src="assets/logo/ksugan.jpg" alt="logo">
        <span class="brand-text text-white" style="font-weight: 700">BARANGAY PORTAL</span>
      </a>

      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse order-3" id="navbarCollapse"></div>

      <!-- Right navbar links -->
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
          <li class="nav-item">
            <a href="index.php" class="nav-link text-white rightBar" style="  border-bottom: 3px solid red;">HOME</a>
          </li>
          <li class="nav-item">
            <a href="register.php" class="nav-link text-white rightBar"><i class="fas fa-user-plus"></i> REGISTER</a>
          </li>
          <li class="nav-item">
            <a href="login.php" class="nav-link text-white rightBar" ><i class="fas fa-user-alt"></i> LOGIN</a>
        </li>
      </ul>
    </div>
  </nav>
  <!-- /.navbar -->

  <!-- Content Wrapper -->
<div class="content-wrapper">

<!-- LOADING SCREEN -->
<div id="preloader">
  <div class="spinner"></div>
</div>

<!-- HERO SECTION -->
<section class="hero-section">
  <div class="hero-content">
    <img src="assets/logo/ksugan.jpg" alt="Barangay Logo" class="logo-img">
    <h1>Welcome to <span class="highlight">Brgy Kalusugan</span></h1>
    <h4>New Manila, Quezon City </h4>
    <p>📍Metro Manila, 1112 <br>🚨 "Working together for a safer, healthier, and more prepared community."</p>
  </div>
</section>

<!-- E-GOVERNANCE SECTION -->
<section class="egov-section">
  <div class="container egov-container">
    <h2 class="egov-title">E-Governance Services</h2>
    <p class="egov-sub"></p>

    <div class="egov-grid">
      <div class="egov-card" title="Barangay People System">
        <div class="egov-icon"><i class="fas fa-certificate"></i></div>
        <div class="egov-label">Brgy People System</div>
        <p class="egov-desc">Resident Profiling, Household Records and basic demographic information.</p>
      </div>

      <div class="egov-card" title="Permits & Clearances">
        <div class="egov-icon"><i class="fas fa-file-alt"></i></div>
        <div class="egov-label">Permits & Clearances</div>
        <p class="egov-desc">Request barangay clearance, business permits and other certifications.</p>
      </div>

      <div class="egov-card" title="Blotter Reports">
        <div class="egov-icon"><i class="fas fa-wallet"></i></div>
        <div class="egov-label">Blotter Reports</div>
        <p class="egov-desc">Log and track incident reports and mediation records handled by barangay officials.</p>
      </div>

      <div class="egov-card" title="Emergency Alerts">
        <div class="egov-icon"><i class="fas fa-exclamation-circle"></i></div>
        <div class="egov-label">Emergency Alerts</div>
        <p class="egov-desc">Real-time advisories for disasters, weather and urgent community-wide notifications.</p>
      </div>

      <div class="egov-card" title="Barangay Monitoring">
        <div class="egov-icon"><i class="fas fa-bullhorn"></i></div>
        <div class="egov-label">Barangay Monitoring</div>
        <p class="egov-desc">Project updates, maintenance schedules and activity monitoring for transparency.</p>
      </div>
    </div>
  </div>
</section>

<!-- ABOUT SECTION -->
<section class="about-section">
  <h2>About Barangay Kalusugan</h2>
  <p>
    Barangay Kalusugan is a vibrant and compact urban community located in the 4th District of Quezon City. With a land area of approximately 0.4266 km² and a population of 4,786 as of the 2020 Census, it represents about 0.16% of Quezon City’s total population.
    Situated in the New Manila/St. Luke’s medical district corridor, Barangay Kalusugan enjoys strategic access to key roads such as E. Rodriguez Sr. Avenue and is near major public transport links.
  </p>
</section>

<!-- BARANGAY OFFICIALS SECTION -->
<section class="officials-section">
  <h2 class="officials-title">Barangay Officials</h2>

  <div class="officials-container">

    <div class="official-card">
      <h3 class="official-name">Hon. Rocky DC. Rabanal</h3>
      <p class="official-title">Punong Barangay</p>
    </div>

    <div class="official-card">
      <h3 class="official-name">Kgd. Roderick M. Hara</h3>
      <p class="official-title">Barangay Kagawad</p>
    </div>

    <div class="official-card">
      <h3 class="official-name">Kgd. Christopher C. Serrano</h3>
      <p class="official-title">Barangay Kagawad</p>
    </div>

    <div class="official-card">
      <h3 class="official-name">Kgd. Margaret Lyra Maruzzo</h3>
      <p class="official-title">Barangay Kagawad</p>
    </div>

    <div class="official-card">
      <h3 class="official-name">Kgd. Ferdinan D. Barbon</h3>
      <p class="official-title">Barangay Kagawad</p>
    </div>

    <div class="official-card">
      <h3 class="official-name">Kgd. Elissa R. Payumo</h3>
      <p class="official-title">Barangay Kagawad</p>
    </div>

    <div class="official-card">
      <h3 class="official-name">Kgd. Robin C. Porlaje</h3>
      <p class="official-title">Barangay Kagawad</p>
    </div>

    <div class="official-card">
      <h3 class="official-name">Kgd. Reynaldo SJ. Seva</h3>
      <p class="official-title">Barangay Kagawad</p>
    </div>

    <div class="official-card">
      <h3 class="official-name">Corazon L. Prado</h3>
      <p class="official-title">Secretary</p>
    </div>

    <div class="official-card">
      <h3 class="official-name">Fritzie F. Ulpindo</h3>
      <p class="official-title">Treasurer</p>
    </div>

    <div class="official-card">
      <h3 class="official-name">Elmer Z. Pinca</h3>
      <p class="official-title">BPSO Ex-O</p>
    </div>

    <div class="official-card">
      <h3 class="official-name">John Vincent D. Abaño</h3>
      <p class="official-title">SK Chairman</p>
    </div>

  </div>
</section>

<!-- FOOTER -->
<footer class="main-footer">
  <i class="fas fa-map-marker-alt"></i> <?= '1112 Quezon City' ?>
</footer>

</div>
  
        </div>
      </div>
    </div>
  </div>

</div>

<!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.js"></script>

<script>
  // Hide loading screen after page loads
  window.addEventListener('load', function () {
    document.getElementById('preloader').style.display = 'none';
  });
</script>

</body>
</html>
