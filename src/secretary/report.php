<?php 
session_start();
include_once '../connection.php';

try {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'secretary') {
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

    // Fetch barangay info
    $sql_brgy = "SELECT barangay, zone, district, image, image_path FROM barangay_information LIMIT 1";
    $result_brgy = $con->query($sql_brgy);
    $brgy_info = $result_brgy->fetch_assoc();
    $barangay = htmlspecialchars($brgy_info['barangay'] ?? '');
    $zone = htmlspecialchars($brgy_info['zone'] ?? '');
    $district = htmlspecialchars($brgy_info['district'] ?? '');

    $table = '';
    $total_households = 0;
    $total_residents = 0;
    $total_seniors = 0;
    $total_pwd = 0;

    // Initialize filter variables
    $filters = [];
    $params = [];
    $types = '';
    $search_where = '';

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $_GET['search'];
        $search_where = " AND (h.household_number LIKE ? OR h.address LIKE ? OR 
                            CONCAT(ri.first_name, ' ', ri.last_name) LIKE ? OR 
                            ri.last_name LIKE ? OR ri.first_name LIKE ?)";
        $search_param = "%{$search}%";
        $params = array_fill(0, 5, $search_param);
        $types = str_repeat('s', 5);
    }

    // Base query for household monitoring - CORRECTED
    $base_sql = "SELECT 
                    h.id as household_id,
                    h.household_number,
                    h.address,
                    h.barangay,
                    h.municipality,
                    h.zip_code,
                    h.date_created,
                    h.household_head_id,
                    -- Count all household members
                    (SELECT COUNT(*) FROM household_members hm2 WHERE hm2.household_id = h.id) as total_members,
                    -- Count seniors (age >= 60)
                    (SELECT COUNT(*) 
                     FROM household_members hm3 
                     JOIN residence_information ri3 ON hm3.user_id = ri3.residence_id 
                     WHERE hm3.household_id = h.id AND ri3.age >= 60) as senior_count,
                    -- Count PWD
                    (SELECT COUNT(*) 
                     FROM household_members hm4 
                     JOIN residence_status rs4 ON hm4.user_id = rs4.residence_id 
                     WHERE hm4.household_id = h.id AND rs4.pwd = 'YES') as pwd_count,
                    -- Count single parents
                    (SELECT COUNT(*) 
                     FROM household_members hm5 
                     JOIN residence_status rs5 ON hm5.user_id = rs5.residence_id 
                     WHERE hm5.household_id = h.id AND rs5.single_parent = 'YES') as single_parent_count,
                    -- Count voters
                    (SELECT COUNT(*) 
                     FROM household_members hm6 
                     JOIN residence_status rs6 ON hm6.user_id = rs6.residence_id 
                     WHERE hm6.household_id = h.id AND rs6.voters = 'YES') as voters_count,
                    -- Get household head name
                    CONCAT(ri.first_name, ' ', ri.last_name) as head_name
                FROM households h
                LEFT JOIN users u ON h.household_head_id = u.id
                LEFT JOIN residence_information ri ON u.id = ri.residence_id
                WHERE 1=1 {$search_where}
                GROUP BY 
                    h.id, 
                    h.household_number, 
                    h.address, 
                    h.barangay, 
                    h.municipality, 
                    h.zip_code, 
                    h.date_created, 
                    h.household_head_id,
                    ri.first_name,   /* ADDED THIS LINE */
                    ri.last_name     /* ADDED THIS LINE */
                ORDER BY h.date_created DESC";

    // Prepare and execute query
    if (!empty($params)) {
        $stmt = $con->prepare($base_sql);
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result_households = $stmt->get_result();
        }
    } else {
        $result_households = $con->query($base_sql);
    }
    
    if ($result_households && $result_households->num_rows > 0) {
        while ($row = $result_households->fetch_assoc()) {
            $total_households++;
            $total_residents += $row['total_members'];
            $total_seniors += $row['senior_count'];
            $total_pwd += $row['pwd_count'];
            
            $vulnerability_score = 0;
            $vulnerability_class = '';
            
            // Calculate vulnerability score for rescue priority
            if ($row['senior_count'] > 0) $vulnerability_score += 2;
            if ($row['pwd_count'] > 0) $vulnerability_score += 3;
            if ($row['single_parent_count'] > 0) $vulnerability_score += 1;
            if ($row['total_members'] >= 5) $vulnerability_score += 1; // Large families
            
            // Assign priority class
            if ($vulnerability_score >= 3) {
                $vulnerability_class = 'priority-high';
                $priority = 'HIGH';
            } elseif ($vulnerability_score >= 2) {
                $vulnerability_class = 'priority-medium';
                $priority = 'MEDIUM';
            } else {
                $vulnerability_class = 'priority-low';
                $priority = 'LOW';
            }
            
            // Get head name
            $head_name = $row['head_name'] ?? 'No head assigned';
            if (empty(trim($head_name))) {
                $head_name = 'No head assigned';
            }

            $table .= '<tr class="household-row" data-id="' . $row['household_id'] . '">
                <td>
                    <strong>' . htmlspecialchars($row['household_number']) . '</strong>
                    <br><small class="text-muted">' . date('M d, Y', strtotime($row['date_created'])) . '</small>
                </td>
                <td>' . htmlspecialchars($head_name) . '</td>
                <td>' . htmlspecialchars($row['address'] ?? 'N/A') . '<br>
                    <small class="text-muted">' . htmlspecialchars($row['barangay'] ?? '') . '</small>
                </td>
                <td class="text-center">
                    <span class="badge badge-primary" style="font-size:14px; padding:8px;">
                        ' . $row['total_members'] . '
                    </span>
                </td>
                <td class="text-center">
                    ' . ($row['senior_count'] > 0 ? '<span class="badge badge-warning" title="Senior Citizens">' . $row['senior_count'] . ' Senior</span><br>' : '') . '
                    ' . ($row['pwd_count'] > 0 ? '<span class="badge badge-info" title="Persons with Disability">' . $row['pwd_count'] . ' PWD</span>' : '<span class="text-muted">None</span>') . '
                </td>
                <td class="text-center">
                    ' . ($row['voters_count'] > 0 ? '<span class="badge badge-success">' . $row['voters_count'] . ' Voters</span>' : '<span class="text-muted">None</span>') . '
                </td>
                <td class="text-center">
                    <span class="badge ' . $vulnerability_class . '" style="font-size:12px; padding:6px 12px;">
                        ' . $priority . '
                    </span>
                </td>
                <td class="text-center action-buttons">
                    <button class="btn btn-sm btn-info view-members" data-id="' . $row['household_id'] . '" data-number="' . htmlspecialchars($row['household_number']) . '" title="View Members">
                        <i class="fas fa-users"></i>
                    </button>
                </td>
            </tr>';
        }
    } else {
        $table .= '<tr>
            <td colspan="8" class="text-center">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No households found.
                    <a href="newResidence.php" class="alert-link">Add a new household</a>
                </div>
            </td>
        </tr>';
    }

} catch (Exception $e) {
    // This will show you exactly what went wrong
    error_log($e->getMessage());
    echo "<script>alert('Error Details: " . addslashes($e->getMessage()) . "');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Household Monitoring</title>
  <!-- Favicon -->
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
  <link rel="stylesheet" href="../assets/plugins/jquery-ui/jquery-ui.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="../assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Custom CSS -->
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

    /* Body Background */
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

    /* STATS CARDS */
    .stats-card {
      border-radius: 10px;
      border: none;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }

    .stats-card:hover {
      transform: translateY(-5px);
    }

    .stats-card .card-body {
      padding: 20px;
    }

    .stats-icon {
      font-size: 40px;
      opacity: 0.8;
    }

    .stats-number {
      font-size: 28px;
      font-weight: 700;
      margin: 10px 0;
    }

    .stats-label {
      font-size: 14px;
      color: #666;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    /* SEARCH BOX */
    .search-box {
      border: 2px solid #050C9C;
      border-radius: 25px;
      padding: 10px 20px;
    }

    .search-box:focus {
      box-shadow: 0 0 0 0.2rem rgba(5, 12, 156, 0.25);
    }

    /* TABLE STYLING */
    #householdTable thead {
      background-color: #050C9C;
      color: #fff;
      font-weight: 600;
    }

    #householdTable tbody tr {
      transition: all 0.3s;
      border-left: 4px solid transparent;
    }

    #householdTable tbody tr:hover {
      background-color: #f0f6ff;
      border-left: 4px solid #3ABEF9;
      cursor: pointer;
    }

    .household-row:hover {
      transform: scale(1.01);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* PRIORITY BADGES */
    .priority-high {
      background-color: #dc3545 !important;
      animation: pulse 2s infinite;
    }

    .priority-medium {
      background-color: #ffc107 !important;
    }

    .priority-low {
      background-color: #28a745 !important;
    }

    @keyframes pulse {
      0% { opacity: 1; }
      50% { opacity: 0.7; }
      100% { opacity: 1; }
    }

    /* ACTION BUTTONS */
    .action-buttons .btn {
      margin: 2px;
      border-radius: 50%;
      width: 35px;
      height: 35px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    /* MODAL STYLING */
    .modal-header {
      background-color: #050C9C;
      color: white;
    }

    .member-list-item {
      border-left: 3px solid #3ABEF9;
      margin-bottom: 8px;
      padding: 10px;
      background: #f8f9fa;
      border-radius: 5px;
    }

    .member-badges .badge {
      margin-right: 5px;
      font-size: 0.75em;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
      .stats-card .card-body {
        padding: 15px;
      }
      
      .stats-number {
        font-size: 22px;
      }
      
      .stats-icon {
        font-size: 30px;
      }
      
      .action-buttons .btn {
        width: 30px;
        height: 30px;
        font-size: 12px;
      }
    }

        .household-info {
        border-left: 4px solid #3ABEF9;
        background-color: #f8f9fa;
    }
    
    .member-details .badge {
        font-size: 0.8rem;
        padding: 3px 8px;
    }
    
    .member-badges .badge {
        font-size: 0.7rem;
        padding: 3px 6px;
        margin-bottom: 2px;
    }
    
    .members-list {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 10px;
    }
    
    .members-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .members-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .members-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    .members-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    .alert-info .fa-info-circle {
        color: #17a2b8;
    }
    
    .alert-danger .fa-exclamation-triangle {
        color: #dc3545;
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
  <nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
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

  <!-- Main Sidebar Container -->
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

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
          <div class="col-lg-3 col-6">
            <div class="stats-card card">
              <div class="card-body text-center">
                <div class="text-primary">
                  <i class="fas fa-home stats-icon"></i>
                </div>
                <div class="stats-number"><?= $total_households ?></div>
                <div class="stats-label">TOTAL HOUSEHOLDS</div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stats-card card">
              <div class="card-body text-center">
                <div class="text-success">
                  <i class="fas fa-users stats-icon"></i>
                </div>
                <div class="stats-number"><?= $total_residents ?></div>
                <div class="stats-label">TOTAL RESIDENTS</div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stats-card card">
              <div class="card-body text-center">
                <div class="text-warning">
                  <i class="fas fa-wheelchair stats-icon"></i>
                </div>
                <div class="stats-number"><?= $total_seniors ?></div>
                <div class="stats-label">SENIOR CITIZENS</div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stats-card card">
              <div class="card-body text-center">
                <div class="text-danger">
                  <i class="fas fa-accessible-icon stats-icon"></i>
                </div>
                <div class="stats-number"><?= $total_pwd ?></div>
                <div class="stats-label">PWD RESIDENTS</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-search mr-2"></i>Quick Search & Filter</h3>
          </div>
          <div class="card-body">
            <form method="GET" action="report.php" class="row">
              <div class="col-md-8">
                <div class="input-group">
                  <input type="text" name="search" class="form-control search-box" 
                         placeholder="Search by Household No, Address, or Head Name..." 
                         value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                      <i class="fas fa-search"></i> Search
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-4 text-right">
                <a href="newResidence.php?type=household" class="btn btn-success">
                  <i class="fas fa-plus-circle"></i> New Household
                </a>
                <a href="report.php" class="btn btn-secondary">
                  <i class="fas fa-redo"></i> Reset
                </a>
              </div>
            </form>
          </div>
        </div>

        <!-- Main Table -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-2"></i>Household List for Disaster Response</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover table-striped" id="householdTable">
                <thead>
                  <tr>
                    <th>Household No.</th>
                    <th>Head of Household</th>
                    <th>Address</th>
                    <th class="text-center">Members</th>
                    <th class="text-center">Vulnerable Groups</th>
                    <th class="text-center">Voters</th>
                    <th class="text-center">Rescue Priority</th>
                    <th class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?= $table ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer">
            <div class="row">
              <div class="col-md-6">
                <strong>Priority Guide:</strong>
                <span class="badge badge-danger mr-2">HIGH</span> (Seniors/PWD present)
                <span class="badge badge-warning mr-2">MEDIUM</span> (Single parent/large family)
                <span class="badge badge-success">LOW</span> (No special considerations)
              </div>
              <div class="col-md-6 text-right">
              </div>
            </div>
          </div>
        </div>

        <!-- Emergency Info Card -->
        <div class="card">
          <div class="card-header bg-danger text-white">
            <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-2"></i>Emergency Response Information</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <h5><i class="fas fa-phone-alt text-danger"></i> Emergency Contacts</h5>
                <ul class="list-unstyled">
                  <li><strong>Barangay Hall:</strong> (02) 123-4567</li>
                  <li><strong>Emergency Hotline:</strong> 911</li>
                  <li><strong>Disaster Response:</strong> (02) 987-6543</li>
                  <li><strong>Medical Emergency:</strong> 1669</li>
                </ul>
              </div>
              <div class="col-md-6">
                <h5><i class="fas fa-map-marker-alt text-danger"></i> Evacuation Centers</h5>
                <ul class="list-unstyled">
                  <li><strong>Primary:</strong> Barangay Covered Court</li>
                  <li><strong>Secondary:</strong> Kalusugan Elementary School</li>
                  <li><strong>Tertiary:</strong> Community Center</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <strong>&copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')) ?> Barangay Kalusugan Disaster Response System</strong>
    <div class="float-right d-none d-sm-inline-block">
      <span class="badge badge-info">Household Monitoring v1.0</span>
    </div>
  </footer>
</div>

<!-- View Members Modal -->
<div class="modal fade" id="viewMembersModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-users mr-2"></i>Household Members</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="membersList"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- REQUIRED SCRIPTS -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>
<script src="../assets/plugins/select2/js/select2.full.min.js"></script>
<script src="../assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="../assets/plugins/pdfmake/vfs_fonts.js"></script>

<script>
// View Household Members - IMPROVED ERROR HANDLING
// View Household Members - SIMPLIFIED FOR SAME DIRECTORY
$(document).on('click', '.view-members', function(e) {
    e.stopPropagation();
    var householdId = $(this).data('id');
    var householdNumber = $(this).data('number');
    
    console.log('Opening household #' + householdId + ' - ' + householdNumber);
    
    // Show loading message
    $('#membersList').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br><p class="mt-2">Loading household members...</p></div>');
    $('#viewMembersModal').modal('show');
    
    // Since both files are in /admin/, use direct path
    $.ajax({
        url: 'get_household_member.php', // Same directory
        method: 'POST',
        data: {
            household_id: householdId,
            _token: '<?= md5(session_id()) ?>'
        },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('AJAX Response:', response);
            
            if(response.success === true) {
                var html = '<div class="household-info mb-3 p-3 bg-light rounded">';
                html += '<h5><i class="fas fa-home mr-2"></i>Household: <strong>' + householdNumber + '</strong></h5>';
                html += '<p class="mb-0"><i class="fas fa-users mr-2"></i>Total Members: <strong>' + response.total_members + '</strong></p>';
                html += '</div>';
                
                if(response.members && response.members.length > 0) {
                    html += '<div class="members-list">';
                    $.each(response.members, function(index, member) {
                        html += '<div class="member-list-item mb-2">';
                        html += '<div class="d-flex justify-content-between align-items-start">';
                        html += '<div>';
                        html += '<h6 class="mb-1"><strong>' + member.name + '</strong></h6>';
                        html += '<div class="member-details">';
                        html += '<span class="badge badge-light mr-2"><i class="fas fa-user"></i> ' + member.relationship + '</span>';
                        html += '<span class="badge badge-light mr-2"><i class="fas fa-birthday-cake"></i> ' + member.age + ' years</span>';
                        if(member.contact && member.contact !== 'N/A' && member.contact !== '') {
                            html += '<span class="badge badge-light"><i class="fas fa-phone"></i> ' + member.contact + '</span>';
                        }
                        html += '</div>';
                        html += '</div>';
                        
                        html += '<div class="member-badges">';
                        if(member.pwd === 'YES') html += '<span class="badge badge-info mr-1 mb-1" title="Person with Disability"><i class="fas fa-wheelchair"></i> PWD</span>';
                        if(member.senior === 'YES') html += '<span class="badge badge-warning mr-1 mb-1" title="Senior Citizen"><i class="fas fa-user-clock"></i> Senior</span>';
                        if(member.single_parent === 'YES') html += '<span class="badge badge-danger mr-1 mb-1" title="Single Parent"><i class="fas fa-user-friends"></i> Single Parent</span>';
                        if(member.voters === 'YES') html += '<span class="badge badge-success mr-1 mb-1" title="Registered Voter"><i class="fas fa-vote-yea"></i> Voter</span>';
                        html += '</div>';
                        html += '</div>';
                        
                        // Status
                        if(member.status && member.status !== 'ACTIVE') {
                            html += '<div class="mt-1">';
                            html += '<span class="badge badge-secondary">Status: ' + member.status + '</span>';
                            html += '</div>';
                        }
                        
                        html += '</div>';
                    });
                    html += '</div>';
                } else {
                    html += '<div class="alert alert-info text-center">';
                    html += '<i class="fas fa-info-circle fa-2x mb-3"></i>';
                    html += '<h5>No Members Found</h5>';
                    html += '<p>This household currently has no members assigned.</p>';
                    html += '<a href="newResidence.php?household_id=' + householdId + '" class="btn btn-primary btn-sm">';
                    html += '<i class="fas fa-user-plus"></i> Add Member';
                    html += '</a>';
                    html += '</div>';
                }
                
                $('#membersList').html(html);
            } else {
                var errorMsg = response.message || 'Failed to load household members';
                $('#membersList').html('<div class="alert alert-danger text-center">' +
                    '<i class="fas fa-exclamation-triangle fa-2x mb-2"></i>' +
                    '<h5>Error Loading Members</h5>' +
                    '<p>' + errorMsg + '</p>' +
                    '</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error, xhr.responseText);
            
            $('#membersList').html('<div class="alert alert-danger text-center">' +
                '<i class="fas fa-exclamation-triangle fa-2x mb-2"></i>' +
                '<h5>Connection Error</h5>' +
                '<p>Error details: ' + error + '</p>' +
                '<p>Check browser console (F12) for more details.</p>' +
                '</div>');
        }
    });
});
</script>

</body>
</html>