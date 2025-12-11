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

    $yes= 'YES';
    $no = 'NO';

    // ========== ENHANCED POPULATION DATA FETCHING ==========

    // Total Residents
    $sql_total_residence = "SELECT residence_id FROM residence_status WHERE archive = ?";
    $query_total_residence = $con->prepare($sql_total_residence) or die ($con->error);
    $query_total_residence->bind_param('s',$no);
    $query_total_residence->execute();
    $query_total_residence->store_result();
    $count_total_residence = $query_total_residence->num_rows;

    // Gender Count
    $sql_gender = "SELECT 
        COUNT(CASE WHEN gender = 'Male' THEN residence_information.residence_id END) as male,
        COUNT(CASE WHEN gender = 'Female' THEN residence_information.residence_id END) as female
        FROM residence_information
        INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id
        WHERE archive = 'NO'";
    $result_gender = $con->query($sql_gender) or die ($con->error);
    while ($row_gender = $result_gender->fetch_assoc()) { 
        $genderMale = $row_gender['male'];
        $genderFemale = $row_gender['female'];
    }

    // Senior Citizens
    $sql_senior = "SELECT age FROM residence_information INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id WHERE age >= 60 AND archive = 'NO'";
    $query_senior = $con->query($sql_senior) or die ($con->error);
    $count_senior = $query_senior->num_rows;

    // NEW: Household Count (estimate based on average household size)
    $estimated_households = $count_total_residence ? ceil($count_total_residence / 4) : 0;

    // NEW: Age Group Distribution
    try {
        $sql_age_groups = "SELECT 
            COUNT(CASE WHEN age < 18 THEN 1 END) as children,
            COUNT(CASE WHEN age BETWEEN 18 AND 59 THEN 1 END) as adults,
            COUNT(CASE WHEN age >= 60 THEN 1 END) as seniors
            FROM residence_information 
            INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id 
            WHERE archive = 'NO'";
        $query_age_groups = $con->prepare($sql_age_groups);
        $query_age_groups->execute();
        $result_age_groups = $query_age_groups->get_result();
        $age_groups = $result_age_groups->fetch_assoc();
        
        $count_children = $age_groups['children'] ?? 0;
        $count_adults = $age_groups['adults'] ?? 0;
        $count_seniors = $age_groups['seniors'] ?? 0;
    } catch (Exception $e) {
        $count_children = 0;
        $count_adults = 0;
        $count_seniors = $count_senior;
    }

    // NEW: PWD Count
    try {
        $sql_pwd = "SELECT COUNT(*) as pwd_count FROM residence_status WHERE pwd = 'YES' AND archive = 'NO'";
        $query_pwd = $con->prepare($sql_pwd);
        $query_pwd->execute();
        $result_pwd = $query_pwd->get_result();
        $row_pwd = $result_pwd->fetch_assoc();
        $count_pwd = $row_pwd['pwd_count'] ?? 0;
    } catch (Exception $e) {
        $count_pwd = 0;
    }

    // NEW: Single Parent Count
    try {
        $sql_single_parent = "SELECT COUNT(*) as single_parent_count FROM residence_status WHERE single_parent = 'YES' AND archive = 'NO'";
        $query_single_parent = $con->prepare($sql_single_parent);
        $query_single_parent->execute();
        $result_single_parent = $query_single_parent->get_result();
        $row_single_parent = $result_single_parent->fetch_assoc();
        $count_single_parent = $row_single_parent['single_parent_count'] ?? 0;
    } catch (Exception $e) {
        $count_single_parent = 0;
    }

    // 1. Count RESIDENTS
    try {
        // Count those explicitly marked as 'Resident'
        $sql_residents = "SELECT COUNT(residence_id) AS residents_count FROM residence_status WHERE residency_type = 'Resident' AND archive = 'NO'";
        $query_residents = $con->prepare($sql_residents);
        $query_residents->execute();
        $row_residents = $query_residents->get_result()->fetch_assoc();
        $count_residents = $row_residents['residents_count'] ?? 0;
    } catch (Exception $e) {
        $count_residents = 0;
    }

    // 2. Count WORKERS
    try {
        // Count those explicitly marked as 'Worker'
        $sql_workers = "SELECT COUNT(residence_id) AS workers_count FROM residence_status WHERE residency_type = 'Worker' AND archive = 'NO'";
        $query_workers = $con->prepare($sql_workers);
        $query_workers->execute();
        $row_workers = $query_workers->get_result()->fetch_assoc();
        $count_workers = $row_workers['workers_count'] ?? 0;
    } catch (Exception $e) {
        $count_workers = 0;
    }

    // ========== DRRM DATA FETCHING WITH ERROR HANDLING ==========
    
    // Fire Hydrants Count
    try {
        $sql_fire_hydrants = "SELECT COUNT(*) as total_hydrants FROM fire_hydrants WHERE status = 'Operational'";
        $query_fire_hydrants = $con->prepare($sql_fire_hydrants);
        $query_fire_hydrants->execute();
        $result_fire_hydrants = $query_fire_hydrants->get_result();
        $row_fire_hydrants = $result_fire_hydrants->fetch_assoc();
        $count_fire_hydrants = $row_fire_hydrants['total_hydrants'] ?? 0;
        
        // Get fire hydrants details for modal
        $sql_fire_hydrants_details = "SELECT * FROM fire_hydrants ORDER BY hydrant_number";
        $query_fire_details = $con->prepare($sql_fire_hydrants_details);
        $query_fire_details->execute();
        $result_fire_details = $query_fire_details->get_result();
        $fire_hydrants_data = [];
        while($row = $result_fire_details->fetch_assoc()) {
            $fire_hydrants_data[] = $row;
        }
    } catch (Exception $e) {
        $count_fire_hydrants = 0;
        $fire_hydrants_data = [];
    }

    // Rescue Boats Count
    try {
        $sql_rescue_boats = "SELECT COUNT(*) as total_boats FROM rescue_boats WHERE status = 'Available' AND `condition` IN ('Good', 'Fair')";
        $query_rescue_boats = $con->prepare($sql_rescue_boats);
        $query_rescue_boats->execute();
        $result_rescue_boats = $query_rescue_boats->get_result();
        $row_rescue_boats = $result_rescue_boats->fetch_assoc();
        $count_rescue_boats = $row_rescue_boats['total_boats'] ?? 0;
        
        // Get rescue boats details for modal
        $sql_rescue_boats_details = "SELECT * FROM rescue_boats ORDER BY boat_number";
        $query_boats_details = $con->prepare($sql_rescue_boats_details);
        $query_boats_details->execute();
        $result_boats_details = $query_boats_details->get_result();
        $rescue_boats_data = [];
        while($row = $result_boats_details->fetch_assoc()) {
            $rescue_boats_data[] = $row;
        }
    } catch (Exception $e) {
        $count_rescue_boats = 0;
        $rescue_boats_data = [];
    }

    // First Aid Officers Count
    try {
        $sql_first_aid = "SELECT COUNT(*) as total_officers FROM first_aid_officers WHERE status = 'Active'";
        $query_first_aid = $con->prepare($sql_first_aid);
        $query_first_aid->execute();
        $result_first_aid = $query_first_aid->get_result();
        $row_first_aid = $result_first_aid->fetch_assoc();
        $count_first_aid = $row_first_aid['total_officers'] ?? 0;
        
        // Get first aid officers details for modal
        $sql_first_aid_details = "SELECT * FROM first_aid_officers WHERE status = 'Active' ORDER BY last_name, first_name";
        $query_first_aid_details = $con->prepare($sql_first_aid_details);
        $query_first_aid_details->execute();
        $result_first_aid_details = $query_first_aid_details->get_result();
        $first_aid_data = [];
        while($row = $result_first_aid_details->fetch_assoc()) {
            $first_aid_data[] = $row;
        }
    } catch (Exception $e) {
        $count_first_aid = 0;
        $first_aid_data = [];
    }

    // Rescue Equipment Count
    try {
        $sql_rescue_equip = "SELECT SUM(quantity) as total_equipment FROM rescue_equipment WHERE `condition` IN ('Good', 'Fair')";
        $query_rescue_equip = $con->prepare($sql_rescue_equip);
        $query_rescue_equip->execute();
        $result_rescue_equip = $query_rescue_equip->get_result();
        $row_rescue_equip = $result_rescue_equip->fetch_assoc();
        $count_rescue_equip = $row_rescue_equip['total_equipment'] ?? 0;
        
        // Get rescue equipment details for modal
        $sql_rescue_equip_details = "SELECT * FROM rescue_equipment ORDER BY equipment_type";
        $query_equip_details = $con->prepare($sql_rescue_equip_details);
        $query_equip_details->execute();
        $result_equip_details = $query_equip_details->get_result();
        $rescue_equip_data = [];
        while($row = $result_equip_details->fetch_assoc()) {
            $rescue_equip_data[] = $row;
        }
    } catch (Exception $e) {
        $count_rescue_equip = 0;
        $rescue_equip_data = [];
    }

    // Ambulance Units Count
    try {
        $sql_ambulance = "SELECT COUNT(*) as total_ambulance FROM ambulance_units WHERE status = 'Available'";
        $query_ambulance = $con->prepare($sql_ambulance);
        $query_ambulance->execute();
        $result_ambulance = $query_ambulance->get_result();
        $row_ambulance = $result_ambulance->fetch_assoc();
        $count_ambulance = $row_ambulance['total_ambulance'] ?? 0;
        
        // Get ambulance details for modal
        $sql_ambulance_details = "SELECT * FROM ambulance_units ORDER BY ambulance_number";
        $query_ambulance_details = $con->prepare($sql_ambulance_details);
        $query_ambulance_details->execute();
        $result_ambulance_details = $query_ambulance_details->get_result();
        $ambulance_data = [];
        while($row = $result_ambulance_details->fetch_assoc()) {
            $ambulance_data[] = $row;
        }
    } catch (Exception $e) {
        $count_ambulance = 0;
        $ambulance_data = [];
    }

    // Mobile HQ Units Count
    try {
        $sql_mobile_hq = "SELECT COUNT(*) as total_mobile_hq FROM mobile_hq_units WHERE status = 'Available'";
        $query_mobile_hq = $con->prepare($sql_mobile_hq);
        $query_mobile_hq->execute();
        $result_mobile_hq = $query_mobile_hq->get_result();
        $row_mobile_hq = $result_mobile_hq->fetch_assoc();
        $count_mobile_hq = $row_mobile_hq['total_mobile_hq'] ?? 0;
        
        // Get mobile HQ details for modal
        $sql_mobile_hq_details = "SELECT * FROM mobile_hq_units ORDER BY unit_number";
        $query_mobile_hq_details = $con->prepare($sql_mobile_hq_details);
        $query_mobile_hq_details->execute();
        $result_mobile_hq_details = $query_mobile_hq_details->get_result();
        $mobile_hq_data = [];
        while($row = $result_mobile_hq_details->fetch_assoc()) {
            $mobile_hq_data[] = $row;
        }
    } catch (Exception $e) {
        $count_mobile_hq = 0;
        $mobile_hq_data = [];
    }

    

    $sql_single_parent_yes = "SELECT single_parent, archive FROM residence_status WHERE single_parent = ? AND archive = ?";
    $query_single_parent_yes = $con->prepare($sql_single_parent_yes) or die ($con->error);
    $query_single_parent_yes->bind_param('ss',$yes,$no);
    $query_single_parent_yes->execute();
    $query_single_parent_yes->store_result();
    $count_single_parent_yes = $query_single_parent_yes->num_rows;

    $sql_pwd_yes = "SELECT pwd, archive FROM residence_status WHERE pwd = ? AND archive = ?";
    $query_pwd_yes = $con->prepare($sql_pwd_yes) or die ($con->error);
    $query_pwd_yes->bind_param('ss',$yes,$no);
    $query_pwd_yes->execute();
    $query_pwd_yes->store_result();
    $count_pwd_yes = $query_pwd_yes->num_rows;

    // Blotter Records
    $sql_blotter ="SELECT date_added as yyyy, count(blotter_id) as blotter_count from blotter_record group by date_added order by yyyy";
    $result_blotter = $con->query($sql_blotter) or die ($con->error);
    $count_blotter_result = $result_blotter->num_rows;
    if($count_blotter_result > 0){
      while ($row_blotter = $result_blotter->fetch_array()) { 
        $year[]  = $row_blotter['yyyy']  ;
        $totalBlotter[] = number_format($row_blotter['blotter_count']);
      }
    }else{
      $year[]  = ['0000','1000'];
      $totalBlotter[] = ['100','200'];
    }

    // Total Blotter Count
    $sql_total_blotter = "SELECT blotter_id FROM blotter_record";
    $stmt_total_blotter = $con->prepare($sql_total_blotter) or die ($con->error);
    $stmt_total_blotter->execute();
    $result_total_blotter = $stmt_total_blotter->get_result();
    $count_blotter = $result_total_blotter->num_rows;
    $total_blotter_record = $count_blotter;

    // Official Count
    $sql_count_official =  "SELECT COUNT(official_id) AS total_official FROM official_status";
    $stmt_total_official = $con->prepare($sql_count_official) or die ($con->error);
    $stmt_total_official->execute();
    $result_total_official = $stmt_total_official->get_result();
    $row_total_official = $result_total_official->fetch_assoc();

    // Official Position Data
    $sql_official_position = "SELECT COUNT(*) AS dis,  position.color, position.position AS official_position, position.color, official_status.position FROM position
    INNER JOIN official_status ON position.position_id = official_status.position GROUP BY official_status.position,position.position, position.color";
    $stmt_official_position = $con->prepare($sql_official_position) or die ($con->error);
    $stmt_official_position->execute();
    $result_official_position = $stmt_official_position->get_result();
    $count_result_official = $result_official_position->num_rows;
    if($count_result_official > 0){
        while($row_official_position = $result_official_position->fetch_assoc()){
            $official_postition[] = strtoupper($row_official_position['official_position']);
            $position_color[] = $row_official_position['color'];
            $total_per_official[] = $row_official_position['dis'];
        }
    }else{
        $official_postition[] = ['BLANK'];
        $position_color[] = ['red'];
        $total_per_official[] = ['1'];
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
  <title>Secretary - Dashboard</title>
  <!-- Website Logo -->
  <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">

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
  <!-- DONT FORGET -->
<link rel="stylesheet" href="../assets/dist/css/secretary.css?v=2">
  <style>

/* General Font and Background */
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

/* Small Boxes */
.small-box {
  border-radius: 15px;
  color: #ffffff !important;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
}

.small-box.bg-info {
  background-color: #3572EF !important;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
}

.small-box.bg-success {
  background-color: #3ABEF9 !important;
  color: #ffffff;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
}

.small-box.bg-warning {
  background-color: #FF8A5C !important;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
}

.small-box.bg-danger {
  background-color: #E41749 !important;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
}

.small-box.bg-blue {
  background-color: #050C9C !important;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
}

.small-box.bg-indigo {
  background-color: #3ABEF9 !important;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
}

.small-box.bg-fuchsia {
  background-color: #F5587B !important;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
}

/* Cards */
.card.card-indigo {
  border-top: 4px solid #3572EF;
  background-color: #ffffff;
  color: #050C9C;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
}

.card.card-indigo .card-header {
  background-color: #A7E6FF;
  border-bottom: 1px solid #3572EF;
  color: #050C9C;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
}

/* Users list */
.users-list > li > img {
  border: 3px solid #3572EF;
}

.users-list > li > a {
  color: #050C9C;
  font-weight: bold;
}

.badge-secondary {
  background-color: #FFF591 !important;
  color: #050C9C;
}

/* Scrollbar */
.scrollOfficial::-webkit-scrollbar-thumb {
  background-color: #3572EF;
}

/* Responsive tweaks */
@media (max-width: 768px) {
  .small-box {
    margin-bottom: 20px;
  }

  .card.card-indigo {
    margin-top: 20px;
  }

  .navbar .nav-link {
    font-size: 1rem;
  }
}

   #official_body .scrollOfficial{
    height: 52vh;
    overflow-y: auto;
    }
   #official_body .scrollOfficial::-webkit-scrollbar {
        width: 5px;
    }                                                    
                            
  #official_body  .scrollOfficial::-webkit-scrollbar-thumb {
        background: #6c757d; 
        --webkit-box-shadow: inset 0 0 6px #6c757d; 
    }
  #official_body  .scrollOfficial::-webkit-scrollbar-thumb:window-inactive {
      background: #6c757d; 
    }

    .population-stats .card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.population-stats .card-title {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.population-stats h4 {
    margin-bottom: 0.2rem;
}

.population-stats .text-muted {
    font-size: 0.8rem;
}

.badge-pill {
    font-size: 0.8rem;
    padding: 0.4em 0.8em;
}

  </style>

</head>
<body class="hold-transition sidebar-mini   layout-footer-fixed">
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
  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <!-- Brand Logo -->
    <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display:block;">

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link bg-indigo">
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
          <li class="nav-item">
                <a href="editRequests.php" class="nav-link">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Edit Requests</p>
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
        <!-- End of DRM Part -->
         
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
          <li class="nav-item"><a href="pending_residence.php" class="nav-link"><i class="nav-icon fas fa-hourglass-half"></i><p>Pending Residence</p></a></li>
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

                
      <div class="row">

<div class="col-sm-4">
  <div class="row">
      <!-- Population Box -->
      <div class="col-sm-12">
        <div class="small-box bg-info info-box" data-type="population">
          <div class="inner">
            <h3><?= number_format($count_total_residence); ?></h3>
            <p>POPULATION</p>
          </div>
          <div class="icon">
            <i class="fas fa-users"></i>
          </div>
        </div>
      </div>
      <!-- ./col -->

      <!-- Fire Hydrants Box -->
      <div class="col-sm-12">
        <div class="small-box bg-danger info-box" data-type="firehydrants">
          <div class="inner">
            <h3><?= number_format($count_fire_hydrants ?? 0) ?></h3>
            <p>FIRE HYDRANTS</p>
          </div>
          <div class="icon">
            <i class="fas fa-fire-extinguisher"></i>
          </div>
        </div>
      </div>
      <!-- ./col -->

      <!-- Rescue Boats Box -->
      <div class="col-sm-12">
        <div class="small-box bg-primary info-box" data-type="rescueboats">
          <div class="inner">
            <h3 class="text-white"><?= number_format($count_rescue_boats ?? 0); ?></h3>
            <p class="text-white">RESCUE BOATS</p>
          </div>
          <div class="icon">
            <i class="fas fa-ship"></i>
          </div>
        </div>
      </div>
      <!-- ./col -->

      <!-- First Aid Officers Box -->
      <div class="col-sm-12">
        <div class="small-box bg-success info-box" data-type="firstaid">
          <div class="inner">
            <h3><?= number_format($count_first_aid ?? 0) ?></h3>
            <p>FIRST AID OFFICER</p>
          </div>
          <div class="icon">
            <i class="fas fa-user-nurse"></i>
          </div>
        </div>
      </div>
      <!-- ./col -->

      <!-- Rescue Equipment Box -->
      <div class="col-sm-12">
        <div class="small-box bg-warning info-box" data-type="rescueequip">
          <div class="inner">
            <h3><?= number_format($count_rescue_equip ?? 0) ?></h3>
            <p>RESCUE EQUIPMENT</p>
          </div>
          <div class="icon">
            <i class="fas fa-toolbox"></i>
          </div>
        </div>
      </div>
      <!-- ./col -->  

      <!-- Ambulance Box -->
      <div class="col-sm-12">
        <div class="small-box bg-indigo info-box" data-type="ambulance">
          <div class="inner">
            <h3><?= number_format($count_ambulance ?? 0) ?></h3>
            <p>AMBULANCE</p>
          </div>
          <div class="icon">
            <i class="fas fa-ambulance"></i>
          </div>
        </div>
      </div>
      <!-- ./col -->  

      <!-- Mobile HQ Box -->
      <div class="col-sm-12">
        <div class="small-box bg-fuchsia info-box" data-type="mobilehq">
          <div class="inner">
            <h3><?= number_format($count_mobile_hq ?? 0) ?></h3>
            <p>MOBILE HQ</p>
          </div>
          <div class="icon">
            <i class="fas fa-broadcast-tower"></i>
          </div>
        </div>
      </div>
      <!-- ./col -->  

  </div>
</div>
<div class="col-sm-8">

  <div class="row">
    <div class="col-sm-12">

              <!-- USERS LIST -->
          <div class="card card-outline card-indigo"  id="official_body">
            <div class="card-header">
            <h1 class="card-title" style="font-weight:  700;"> <i class="fas fa-users-cog"></i> OFFICIAL MEMBERS <span class="badge badge-secondary text-lg"><?= $row_total_official['total_official']?></span></h1>   

              <div class="card-tools">
              
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0 text-white">
              <div class="row">
                <div class="col-sm-6 scrollOfficial">

                    <ul class="users-list clearfix ">

                          <?php 

                          $sql_official = "SELECT position.color, position.position AS position_official, official_information.first_name, official_information.last_name, official_information.image_path, official_status.status,official_status.official_id FROM  official_status 
                          INNER JOIN official_information ON  official_status.official_id = official_information.official_id
                          INNER JOIN position ON  official_status.position = position.position_id ORDER BY position.position";
                          $stmt_official = $con->prepare($sql_official) or die ($con->error);
                          $stmt_official->execute();
                          $result_official = $stmt_official->get_result();
                          while($row_official = $result_official->fetch_assoc()){

                          if($row_official['image_path'] != ''){

                          if($row_official['status'] == 'ACTIVE'){
                            $official_image = '  <img src="'.$row_official['image_path'].'" class="w-50" style="border: 3px solid lime" alt="Official Image">';
                          }else{
                            $official_image = '  <img src="'.$row_official['image_path'].'" class="w-50" style="border: 3px solid red" alt="Official Image">';
                          }


                          }else{
                          if($row_official['status'] == 'ACTIVE'){
                            $official_image = '  <img src="../assets/dist/img/image.png" class="w-50" style="border: 3px solid lime" alt="Official Image">';
                          }else{
                            $official_image = '  <img src="../assets/dist/img/image.png" class="w-50" style="border: 3px solid red" alt="Official Image">';
                          }


                          }


                          ?>

                          <li id="<?= $row_official['official_id'] ?>" class="viewOfficial" style="cursor: pointer">
                            <?= $official_image; ?>
                            <p class="users-list-name m-0 text-dark" ><?= $row_official['first_name'].' '. $row_official['last_name'] ?> </p>
                            <span class="users-list-date text-dark" style="font-weight: 900"><?= strtoupper($row_official['position_official']) ?></span>
                          </li>

                          <?php
                          }



                          ?>


                          </ul>
                          <!-- /.users-list -->

                </div>
                <div class="col-sm-6">
              
                  <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                
                
                </div>
              </div>
            
            </div>
            <!-- /.card-body -->
          
          </div>
          <!--/.card -->
      
    </div>
    <div class="col-sm-12">
        <div class="card card-outline card-indigo">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-6">
                <p class="text-center">
                <strong>BLOTTER YEARLY</strong>
                </p>
                  <canvas id="myChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>

              <div class="col-sm-6">
              <p class="text-center">
                <strong>GENDER</strong>
                </p>
                <canvas  id="genderChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
            </div>
          </div>
        </div>
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

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.js"></script>

<script src="../assets/plugins/chart.js/Chart.min.js"></script>

<div id="showOfficial"></div>



<script>


let myChart = document.getElementById('myChart').getContext('2d');

let massPopChart = new Chart(myChart,{
  type: 'line',
  data:{
    labels:<?php echo json_encode($year) ?>,
    datasets:[{
      label:'Record',
      fill: true,
      data: <?php echo json_encode($totalBlotter)?>,
      pointBorderColor: "aqua",
      borderWidth: 4,

      borderColor: 'red',
      hoverBorderWith: 4,
      hoverBorderColor: '#fff',
      borderDash: [2, 2],
      backgroundColor:  "rgba(255, 0, 0, 0.4)",

      
    }]
  },
  options:{
    responsive: true,
    
    title:{
      display:false,
      text: "Blotter",
      fontSize: 35,
      fontColor: '#fff',
    },
   
    legend:{
      display: false,
    },
    scales: {
        yAxes: [{
            ticks: {
                fontSize: 15,
                fontColor: '#fff',
                userCallback: function(label, index, labels) {
                     // when the floored value is the same as the value we have a whole number
                     if (Math.floor(label) === label) {
                         return label;
                     }
                 },
            },
            gridLines: {
                color: "#000",
            },
           
        }],
        xAxes: [{
            ticks: {
                fontSize: 15,
                fontColor: '#fff',
            },
            gridLines: {
                color: "#000",
            }
        }]
        
    }

  }
})
</script>

<script>
  new Chart("genderChart", {
  type: "doughnut",
  data: {
    labels: [
      'Male',
      'Female'
    ],
    datasets: [{
      backgroundColor: [
      "blue",
      "#00aba9",  
      ], 
      data: [<?= $genderMale ?>, <?= $genderFemale ?>]
    }]
  },
  options: {
    responsive: true,
    title: {
      display: false,
      text: "Gender",
      fontSize: 35,
      fontColor: '#000',
    
    
    },
     legend:{
      display: true,
      fontColor: '#000',
      labels: {
                fontSize: 15,
                fontColor: '#000',
            }
    },
  
  }
});
</script>

<script>

new Chart("donutChart", {
  type: "pie",
  data: {
    labels: <?php echo json_encode($official_postition)?>,
      datasets: [
        {
          data: <?php echo json_encode($total_per_official)?>,
          backgroundColor : <?php echo json_encode($position_color)?>,
        }
      ]
  },
  options: {
    responsive: true,
    title: {
      display: false,
    
      fontSize: 35,
      fontColor: '#000',
    
    
    },
     legend:{
      display: true,
      fontColor: '#000',
      labels: {
                fontSize: 15,
                fontColor: '#000',
            },
          
    },
  
  }
});
  
</script>
<script>
  $(document).ready(function(){

    $(document).on('click','.viewOfficial', function(){
      

      var official_id = $(this).attr('id');

      $("#showOfficial").html('');

      $.ajax({
          url: 'viewOfficialModal.php',
          type: 'POST',
          dataType: 'html',
          cache: false,
          data: {
            official_id:official_id
          },
          success:function(data){
            $("#showOfficial").html(data);
            $("#viewOfficialModal").modal('show');              
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
    

  })
</script>

<!-- Info Modal -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-gradient-primary text-white">
        <h5 class="modal-title" id="infoTitle"></h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Search bar -->
      <div class="px-4 pt-3">
        <div class="input-group mb-3">
          <input type="text" id="infoSearch" class="form-control" placeholder="Search within details…">
          <div class="input-group-append">
            <span class="input-group-text bg-primary text-white">
              <i class="fas fa-search"></i>
            </span>
          </div>
        </div>
      </div>

      <!-- Scrollable body -->
      <div class="modal-body pt-0" id="infoContent">
        <!-- dynamic list injected here -->
      </div>
    </div>
  </div>
</div>

<script>
$(function () {
  // Dynamic data from PHP
  const infoDetails = {
    population: {
    title: "Population Demographics",
    content: `
        <div class="population-stats">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body p-3">
                            <h6 class="card-title">Total Population</h6>
                            <h4 class="text-primary"><?= number_format($count_total_residence ?? 0) ?></h4>
                            <small class="text-muted">Registered Residents</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body p-3">
                            <h6 class="card-title">Households</h6>
                            <h4 class="text-info"><?= number_format($estimated_households) ?></h4>
                            <small class="text-muted">Estimated Households</small>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="font-weight-bold mt-4">Gender Distribution</h6>
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Male
                    <span class="badge badge-primary badge-pill"><?= number_format($genderMale ?? 0) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Female
                    <span class="badge badge-danger badge-pill"><?= number_format($genderFemale ?? 0) ?></span>
                </li>
            </ul>

            <h6 class="font-weight-bold mt-4">Age Groups</h6>
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Children (0-17)
                    <span class="badge badge-warning badge-pill"><?= number_format($count_children) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Adults (18-59)
                    <span class="badge badge-success badge-pill"><?= number_format($count_adults) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Senior Citizens (60+)
                    <span class="badge badge-info badge-pill"><?= number_format($count_seniors) ?></span>
                </li>
            </ul>

            <h6 class="font-weight-bold mt-4">Special Groups</h6>
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Persons with Disability (PWD)
                    <span class="badge badge-secondary badge-pill"><?= number_format($count_pwd) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Single Parents
                    <span class="badge badge-secondary badge-pill"><?= number_format($count_single_parent) ?></span>
                </li>

                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Residents
                    <span class="badge badge-primary badge-pill"><?= number_format($count_residents) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Workers
                    <span class="badge badge-info badge-pill"><?= number_format($count_workers) ?></span>
                </li>
            </ul>

            <h6 class="font-weight-bold mt-4">Residency Status</h6>
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Residents
                    <span class="badge badge-primary badge-pill"><?= number_format($count_residents) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Workers
                    <span class="badge badge-info badge-pill"><?= number_format($count_workers) ?></span>
                </li>
            </ul>
        </div>`
},
    firehydrants: {
      title: "Fire Hydrants (<?= $count_fire_hydrants ?> Operational)",
      content: `
        <ul class="list-group list-group-flush">
          <?php if(!empty($fire_hydrants_data)): ?>
            <?php foreach($fire_hydrants_data as $hydrant): ?>
            <li class="list-group-item">
              <strong><?= htmlspecialchars($hydrant['hydrant_number']) ?></strong> – <?= htmlspecialchars($hydrant['location']) ?><br>
              Last Inspection: <?= !empty($hydrant['last_inspection_date']) ? date('M d, Y', strtotime($hydrant['last_inspection_date'])) : 'Not set' ?><br>
              Status: <span class="badge badge-<?= $hydrant['status'] == 'Operational' ? 'success' : ($hydrant['status'] == 'Under Maintenance' ? 'warning' : 'danger') ?>"><?= $hydrant['status'] ?></span>
              <?php if(!empty($hydrant['notes'])): ?><br>Notes: <?= htmlspecialchars($hydrant['notes']) ?><?php endif; ?>
            </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="list-group-item text-muted">No fire hydrants data available. Please add data to the database.</li>
          <?php endif; ?>
        </ul>`
    },
    rescueboats: {
      title: "Rescue Boats (<?= $count_rescue_boats ?> Available)",
      content: `
        <ul class="list-group list-group-flush">
          <?php if(!empty($rescue_boats_data)): ?>
            <?php foreach($rescue_boats_data as $boat): ?>
            <li class="list-group-item">
              <strong><?= htmlspecialchars($boat['boat_number']) ?></strong><br>
              Condition: <span class="badge badge-<?= $boat['condition'] == 'Good' ? 'success' : ($boat['condition'] == 'Fair' ? 'warning' : 'danger') ?>"><?= $boat['condition'] ?></span><br>
              Status: <span class="badge badge-<?= $boat['status'] == 'Available' ? 'success' : 'warning' ?>"><?= $boat['status'] ?></span><br>
              Storage: <?= htmlspecialchars($boat['storage_location'] ?? 'Not specified') ?><br>
              Capacity: <?= $boat['capacity'] ?? 'Not specified' ?> persons
              <?php if(!empty($boat['specifications'])): ?><br>Specs: <?= htmlspecialchars($boat['specifications']) ?><?php endif; ?>
            </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="list-group-item text-muted">No rescue boats data available. Please add data to the database.</li>
          <?php endif; ?>
        </ul>`
    },
    firstaid: {
      title: "First Aid Officers (<?= $count_first_aid ?> Active)",
      content: `
        <ul class="list-group list-group-flush">
          <?php if(!empty($first_aid_data)): ?>
            <?php foreach($first_aid_data as $officer): ?>
            <li class="list-group-item">
              <strong>Name:</strong> <?= htmlspecialchars($officer['first_name']) ?> <?= htmlspecialchars($officer['last_name']) ?><br>
              <strong>Position:</strong> <?= htmlspecialchars($officer['position'] ?? 'Not specified') ?><br>
              <strong>Contact:</strong> <?= htmlspecialchars($officer['contact_number'] ?? 'Not specified') ?><br>
              <strong>In Service:</strong> <?= $officer['years_of_service'] ?? '0' ?> year<?= ($officer['years_of_service'] ?? 1) != 1 ? 's' : '' ?>
              <?php if(!empty($officer['specialization'])): ?><br><strong>Specialization:</strong> <?= htmlspecialchars($officer['specialization']) ?><?php endif; ?>
            </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="list-group-item text-muted">No first aid officers data available. Please add data to the database.</li>
          <?php endif; ?>
        </ul>`
    },
    rescueequip: {
      title: "Rescue Equipment (<?= $count_rescue_equip ?> Items)",
      content: `
        <ul class="list-group list-group-flush">
          <?php if(!empty($rescue_equip_data)): ?>
            <?php foreach($rescue_equip_data as $equipment): ?>
            <li class="list-group-item">
              <strong><?= $equipment['quantity'] ?> <?= htmlspecialchars($equipment['equipment_type']) ?></strong><br>
              Condition: <span class="badge badge-<?= $equipment['condition'] == 'Good' ? 'success' : ($equipment['condition'] == 'Fair' ? 'warning' : 'danger') ?>"><?= $equipment['condition'] ?></span><br>
              Location: <?= htmlspecialchars($equipment['location'] ?? 'Not specified') ?>
              <?php if(!empty($equipment['last_maintenance_date'])): ?><br>Last Maintenance: <?= date('M d, Y', strtotime($equipment['last_maintenance_date'])) ?><?php endif; ?>
              <?php if(!empty($equipment['notes'])): ?><br>Notes: <?= htmlspecialchars($equipment['notes']) ?><?php endif; ?>
            </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="list-group-item text-muted">No rescue equipment data available. Please add data to the database.</li>
          <?php endif; ?>
        </ul>`
    },
    ambulance: {
      title: "Ambulance Units (<?= $count_ambulance ?> Available)",
      content: `
        <ul class="list-group list-group-flush">
          <?php if(!empty($ambulance_data)): ?>
            <?php foreach($ambulance_data as $ambulance): ?>
            <li class="list-group-item">
              <strong><?= htmlspecialchars($ambulance['ambulance_number']) ?></strong><br>
              Location: <?= htmlspecialchars($ambulance['location'] ?? 'Not specified') ?><br>
              Contact: <?= htmlspecialchars($ambulance['contact_number'] ?? 'Not specified') ?><br>
              Driver: <?= htmlspecialchars($ambulance['driver_name'] ?? 'Not specified') ?><br>
              Status: <span class="badge badge-<?= $ambulance['status'] == 'Available' ? 'success' : 'warning' ?>"><?= $ambulance['status'] ?></span>
              <?php if(!empty($ambulance['specifications'])): ?><br>Specs: <?= htmlspecialchars($ambulance['specifications']) ?><?php endif; ?>
            </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="list-group-item text-muted">No ambulance units data available. Please add data to the database.</li>
          <?php endif; ?>
        </ul>`
    },
    mobilehq: {
      title: "Mobile HQ Units (<?= $count_mobile_hq ?> Available)",
      content: `
        <ul class="list-group list-group-flush">
          <?php if(!empty($mobile_hq_data)): ?>
            <?php foreach($mobile_hq_data as $unit): ?>
            <li class="list-group-item">
              <strong><?= htmlspecialchars($unit['unit_number']) ?></strong><br>
              Location: <?= htmlspecialchars($unit['location'] ?? 'Not specified') ?><br>
              Contact: <?= htmlspecialchars($unit['contact_number'] ?? 'Not specified') ?><br>
              Officer in Charge: <?= htmlspecialchars($unit['officer_in_charge'] ?? 'Not specified') ?><br>
              Status: <span class="badge badge-<?= $unit['status'] == 'Available' ? 'success' : 'warning' ?>"><?= $unit['status'] ?></span>
              <?php if(!empty($unit['equipment_included'])): ?><br>Equipment: <?= htmlspecialchars($unit['equipment_included']) ?><?php endif; ?>
            </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="list-group-item text-muted">No mobile HQ units data available. Please add data to the database.</li>
          <?php endif; ?>
        </ul>`
    }
  };

  // ====== Show Modal on Box Click ======
  $('.info-box').on('click', function () {
    const type = $(this).data('type');
    const detail = infoDetails[type];
    if (detail) {
      $('#infoTitle').html(detail.title);
      $('#infoContent').html(detail.content);
      $('#infoModal').modal('show');

      // reset search field when opening
      $('#infoSearch').val('');
      $('#infoContent .list-group-item').show();
    }
  });

  // ====== Live Search ======
  $('#infoSearch').on('keyup', function () {
    const term = $(this).val().toLowerCase();
    $('#infoContent .list-group-item').each(function () {
      const text = $(this).text().toLowerCase();
      $(this).toggle(text.indexOf(term) > -1);
    });
  });
});
</script>

</body>
</html>


              