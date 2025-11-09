<?php 
session_start();
include_once '../connection.php';

try {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        echo '<script>window.location.href = "../login.php";</script>';
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Fetch admin user info
    $sql_user = "SELECT first_name, last_name, user_type, image FROM users WHERE id = ?";
    $stmt_user = $con->prepare($sql_user);
    $stmt_user->bind_param('i', $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $row_user = $result_user->fetch_assoc();

    $first_name_user = htmlspecialchars($row_user['first_name'] ?? '');
    $last_name_user = htmlspecialchars($row_user['last_name'] ?? '');
    $user_image = $row_user['image'] ?? '';

    // Fetch barangay info (should be one row)
    $sql_brgy = "SELECT barangay, zone, district, image, image_path FROM barangay_information LIMIT 1";
    $result_brgy = $con->query($sql_brgy);
    $brgy_info = $result_brgy->fetch_assoc();
    $barangay = htmlspecialchars($brgy_info['barangay'] ?? '');
    $zone = htmlspecialchars($brgy_info['zone'] ?? '');
    $district = htmlspecialchars($brgy_info['district'] ?? '');

    // Initialize filter variables
    $filters = [];
    $params = [];
    $types = '';

    if (isset($_POST['submit'])) {
        $voters = trim($_POST['voters'] ?? '');
        $age = trim($_POST['age'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $pwd = trim($_POST['pwd'] ?? '');
        $senior = trim($_POST['senior'] ?? '');
        $single_parent = trim($_POST['single_parent'] ?? '');

        if ($voters === 'YES' || $voters === 'NO') {
            $filters[] = "residence_status.voters = ?";
            $params[] = $voters;
            $types .= 's';
        }

        if (is_numeric($age) && $age > 0) {
            $filters[] = "residence_information.age = ?";
            $params[] = (int)$age;
            $types .= 'i';
        }

        if ($status === 'ACTIVE' || $status === 'INACTIVE') {
            $filters[] = "residence_status.status = ?";
            $params[] = $status;
            $types .= 's';
        }

        if ($pwd === 'YES' || $pwd === 'NO') {
            $filters[] = "residence_status.pwd = ?";
            $params[] = $pwd;
            $types .= 's';
        }

        if ($senior === 'YES' || $senior === 'NO') {
            $filters[] = "residence_status.senior = ?";
            $params[] = $senior;
            $types .= 's';
        }

        if ($single_parent === 'YES' || $single_parent === 'NO') {
            $filters[] = "residence_status.single_parent = ?";
            $params[] = $single_parent;
            $types .= 's';
        }
    }

    // Build WHERE clause
    $where_sql = '';
    if (!empty($filters)) {
        $where_sql = ' AND ' . implode(' AND ', $filters);
    }

    $base_sql = "SELECT 
                    residence_information.*, 
                    residence_status.* 
                 FROM residence_information 
                 INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id 
                 WHERE residence_status.archive = 'NO' {$where_sql}";

    // Prepare and execute query
    $stmt = $con->prepare($base_sql);
    if ($stmt && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result_report = $stmt->get_result();

    $table = '';
    if ($result_report->num_rows > 0) {
        while ($row = $result_report->fetch_assoc()) {
            $middle_name = !empty($row['middle_name']) 
                ? ucfirst(substr(trim($row['middle_name']), 0, 1)) . '.' 
                : '';

            $table .= '<tr>
                <td>' . htmlspecialchars(ucfirst($row['last_name'] ?? '') . ' ' . ucfirst($row['first_name'] ?? '') . ' ' . $middle_name) . '</td>
                <td>' . htmlspecialchars($row['age'] ?? '') . '</td>
                <td>' . htmlspecialchars($row['pwd_info'] ?? '') . '</td>
                <td>' . htmlspecialchars($row['single_parent'] ?? '') . '</td>
                <td>' . htmlspecialchars($row['voters'] ?? '') . '</td>
                <td>' . htmlspecialchars($row['status'] ?? '') . '</td>
                <td>' . htmlspecialchars($row['senior'] ?? '') . '</td>
            </tr>';
        }
    } else {
        $table .= '<tr>
            <td colspan="7" class="text-center">No records found.</td>
        </tr>';
    }

    // Build query string for print URL
    $print_params = [];
    if (isset($voters) && ($voters === 'YES' || $voters === 'NO')) $print_params['voters'] = $voters;
    if (isset($age) && is_numeric($age)) $print_params['age'] = $age;
    if (isset($status) && in_array($status, ['ACTIVE', 'INACTIVE'])) $print_params['status'] = $status;
    if (isset($pwd) && ($pwd === 'YES' || $pwd === 'NO')) $print_params['pwd'] = $pwd;
    if (isset($senior) && ($senior === 'YES' || $senior === 'NO')) $print_params['senior'] = $senior;
    if (isset($single_parent) && ($single_parent === 'YES' || $single_parent === 'NO')) $print_params['single_parent'] = $single_parent;

    $print_query_string = !empty($print_params) ? http_build_query($print_params) : '';

} catch (Exception $e) {
    error_log($e->getMessage());
    echo "<script>alert('An error occurred. Please try again.');</script>";
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
  <link rel="stylesheet" href="../assets/dist/css/admin.css">

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

/* BODY BACKGROUND */
.content-wrapper,
.card,
.card-body {
  background-color: #fff !important;
  color: #000 !important;
}



/* CARD HEADER */
.card-header {
  background-color: #050C9C !important;
  color: #fff !important;
  border-bottom: 2px solid #3572EF;
  border-radius: 10px 10px 0 0;
  padding: 15px 20px;
}

/* CARD TITLE */
.card-title {
  font-weight: 600;
  font-size: 20px;
  font-family: 'Poppins', sans-serif;
}

/* FORM LABELS (INPUT GROUP TEXT) */
.input-group-text.bg-indigo {
  background-color: #050C9C !important;
  color: #fff;
  font-weight: 500;
  border: none;
}

/* FORM INPUTS & SELECTS */
input.form-control,
select.form-control {
  background-color: #f0f6ff !important;
  color: #000;
  font-weight: 500;
}

input.form-control:focus,
select.form-control:focus {
  border-color: #050C9C !important;
  box-shadow: none;
  background-color: #eaf6ff !important;
}

/* FILTER BUTTON */
#search {
  background-color: #3ABEF9 !important;
  border-color: #3ABEF9 !important;
  color: #000 !important;
}

#search:hover {
  background-color: #3572EF !important;
  color: #fff !important;
}

/* RESET BUTTON */
#reset {
  background-color: #E41749 !important;
  border-color: #E41749 !important;
  color: #fff !important;
}

#reset:hover {
  background-color: #F5587B !important;
  color: #fff !important;
}

/* PRINT BUTTON */
.btn-warning {
  background-color: #FF8A5C !important;
  color: #000 !important;
}

.btn-warning:hover {
  background-color: #FFF591 !important;
  color: #000 !important;
}

/* TABLE HEADER */
#tableReport thead {
  background-color: #050C9C;
  color: #fff;
  font-weight: 600;
}

/* TABLE BODY ROWS */
#tableReport tbody tr {
  background-color: #FAF9F6;
  transition: background-color 0.3s ease;
}

#tableReport tbody tr:hover {
  background-color: #050C9C;
}

#tableReport td {
  color: #000;
  font-weight: 500;
  vertical-align: middle;
}

/* INFO TEXT IN DATATABLE */
.dataTables_info {
  color: #000 !important;
}

/* PAGINATION LINKS */
.dataTables_wrapper .dataTables_paginate .page-item .page-link {
 background-color: #3ABEF9 !important;
  color: #FFF !important;
  border-radius: 15px !important;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
  background-color: #3572EF !important;
  color: #fff !important;
  font-weight: bold;
}



/* RESPONSIVE MOBILE VIEW */
@media (max-width: 768px) {
  .card-title {
    font-size: 16px;
    text-align: center;
  }

  .form-group a.btn,
  .btn-flat {
    width: 100%;
    margin-bottom: 10px;
  }

  .input-group.mb-3 {
    flex-direction: column;
  }

  .input-group-prepend {
    margin-bottom: 5px;
  }

  .input-group-text {
    width: 100%;
    justify-content: center;
  }

  input.form-control,
  select.form-control {
    width: 100%;
  }

  table.table {
    font-size: 12px;
  }
}

/* Age Input Styling */
input[type="number"] {
  background-color: #fff !important;
  color: #000 !important;
  font-weight: 500;
}

input[type="number"]:focus {
  background-color: #fff !important;
  color: #000 !important;
  box-shadow: none;
}


/* DROPDOWNS - SELECT ELEMENTS */
select.form-control {
  background-color: #fff !important;
  color: #000 !important;
  font-weight: 500;
}

/* When focused */
select.form-control:focus {
  background-color: #fff !important;
  color: #000 !important;
  box-shadow: none;
}

/* Dropdown arrows color fix */
select.form-control option {
  color: #000;
  background-color: #fff;
}


  </style>

</head>

<body class="hold-transition  sidebar-mini   layout-footer-fixed">
<div class="wrapper">

   <!-- Preloader -->
   <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble " src="../assets/dist/img/loader.gif" alt="AdminLTELogo" height="70" width="70">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links (COPY LEFT ONLY)  -->
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

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      <!-- profile_dropdown.php (COPY THIS) -->
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
  <!-- /.navbar -->

  <!-- Main Sidebar Container (COPY THIS ASIDE TO ASIDE) -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <!-- Brand Logo -->
    <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">

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
                <a href="newOfficial.php" class="nav-link ">
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
                <a href="allResidence.php" class="nav-link">
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
            <a href="blotterRecord.php" class="nav-link">
              <i class="nav-icon fas fa-clipboard"></i>
              <p>
                Blotter Record
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="report.php" class="nav-link bg-indigo">
              <i class="nav-icon fas fa-bookmark"></i>
              <p>
                Reports
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="settings.php" class="nav-link">
              <i class="nav-icon fas fa-cog"></i>
              <p>
                Settings
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
          <li class="nav-item">
            <a href="backupRestore.php" class="nav-link">
              <i class="nav-icon fas fa-database"></i>
              <p>
                Backup/Restore
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header border-transparent">
            <h3 class="card-title">Resident Report</h3>
          </div>
          <div class="card-body">
            <form action="report.php" method="post">
              <div class="row">
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text bg-indigo">VOTERS</span></div>
                    <select name="voters" class="form-control">
                      <option value="">--SELECT VOTERS--</option>
                      <option value="YES" <?= (isset($voters) && $voters === 'YES') ? 'selected' : '' ?>>YES</option>
                      <option value="NO" <?= (isset($voters) && $voters === 'NO') ? 'selected' : '' ?>>NO</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text bg-indigo">AGE</span></div>
                    <input type="number" name="age" class="form-control" value="<?= isset($age) ? (int)$age : '' ?>" min="1">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text bg-indigo">STATUS</span></div>
                    <select name="status" class="form-control">
                      <option value="">--SELECT STATUS--</option>
                      <option value="ACTIVE" <?= (isset($status) && $status === 'ACTIVE') ? 'selected' : '' ?>>ACTIVE</option>
                      <option value="INACTIVE" <?= (isset($status) && $status === 'INACTIVE') ? 'selected' : '' ?>>INACTIVE</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text bg-indigo">PWD</span></div>
                    <select name="pwd" class="form-control">
                      <option value="">--SELECT PWD--</option>
                      <option value="YES" <?= (isset($pwd) && $pwd === 'YES') ? 'selected' : '' ?>>YES</option>
                      <option value="NO" <?= (isset($pwd) && $pwd === 'NO') ? 'selected' : '' ?>>NO</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text bg-indigo">SINGLE PARENT</span></div>
                    <select name="single_parent" class="form-control">
                      <option value="">--SELECT PARENT STATUS--</option>
                      <option value="YES" <?= (isset($single_parent) && $single_parent === 'YES') ? 'selected' : '' ?>>YES</option>
                      <option value="NO" <?= (isset($single_parent) && $single_parent === 'NO') ? 'selected' : '' ?>>NO</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text bg-indigo">SENIOR</span></div>
                    <select name="senior" class="form-control">
                      <option value="">--SELECT SENIOR--</option>
                      <option value="YES" <?= (isset($senior) && $senior === 'YES') ? 'selected' : '' ?>>YES</option>
                      <option value="NO" <?= (isset($senior) && $senior === 'NO') ? 'selected' : '' ?>>NO</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-12 text-center">
                  <button type="submit" name="submit" id="search" class="btn btn-flat bg-info px-3 elevation-3 text-white"><i class="fas fa-filter"></i> FILTER</button>
                  <a href="report.php" class="btn btn-flat btn-danger px-3 elevation-3" id="reset"><i class="fas fa-undo"></i> RESET</a>
                </div>
              </div>
            </form>

            <div class="form-group mt-3 text-right">
              <a href="printReport.php?<?= $print_query_string ?>" target="_blank" class="btn btn-warning btn-flat elevation-5 px-3">
                <i class="fas fa-print"></i> PRINT
              </a>
            </div>

            <table class="table table-striped table-hover table-sm" id="tableReport">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Age</th>
                  <th>Pwd</th>
                  <th>Single Parent</th>
                  <th>Voters</th>
                  <th>Status</th>
                  <th>Senior</th>
                </tr>
              </thead>
              <tbody>
                <?= $table ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>

 

  <!--Main footer (COPY THIS)-->
<footer class="main-footer">
  <strong>&copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')) ?></strong>
  <div class="float-right d-none d-sm-inline-block"></div>
</footer>
    
    <div class="float-right d-none d-sm-inline-block">
    </div>
  </footer>
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



<script>
 
$(document).ready(function(){
    $("#tableReport").DataTable({
      searching: false,
      info: false,
      ordering: false,
      lengthChange: false,
    });

    $("#age").on("input", function() {
      if (/^0/.test(this.value)) {
        this.value = this.value.replace(/^0/, "");
      }
    });
  });

  //   $(document).on('click','.print',function(){
 

  //   var printContents = $("#printReport").html();
    
  //     var originalContents = document.body.innerHTML;
  //     document.body.innerHTML = printContents;
  //     window.print();
  //     document.body.innerHTML = originalContents;
  //     window.location.reload();
  // })
</script>

</body>
</html>
