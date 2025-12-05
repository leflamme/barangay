<?php
session_start();
include_once '../connection.php';

try {
  if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin') {

    $user_id = $_SESSION['user_id'];
    $sql_user = "SELECT * FROM `users` WHERE `id` = ? ";
    $stmt_user = $con->prepare($sql_user) or die($con->error);
    $stmt_user->bind_param('s', $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $row_user = $result_user->fetch_assoc();
    $first_name_user = $row_user['first_name'] ?? '';
    $last_name_user = $row_user['last_name'] ?? '';
    $user_type = $row_user['user_type'] ?? '';
    $user_image = $row_user['image'] ?? '';

    $sql = "SELECT * FROM `barangay_information`";
    $query = $con->prepare($sql) or die($con->error);
    $query->execute();
    $result = $query->get_result();
    while ($row = $result->fetch_assoc()) {
      $barangay = $row['barangay'];
      $zone = $row['zone'];
      $district = $row['district'];
      $image = $row['image'];
      $image_path = $row['image_path'];
      $id = $row['id'];
    }
  } else {
    echo '<script>window.location.href = "../login.php";</script>';
  }
} catch (Exception $e) {
  echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>System Logs</title>
  <!-- Website Logo -->
  <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/dist/css/admin.css">

  <style>
   /* Custom Styles */
    .main-header.navbar { background-color: #050C9C !important; border-bottom: none; }
    .navbar .nav-link, .navbar .nav-link:hover { color: #ffffff !important; }
    .main-sidebar { background-color: #050C9C !important; }
    .brand-link { background-color: transparent !important; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .sidebar .nav-link { color: #A7E6FF !important; transition: all 0.3s; }
    .sidebar .nav-link.active, .sidebar .nav-link:hover { background-color: #3572EF !important; color: #ffffff !important; }
    .sidebar .nav-icon { color: #3ABEF9 !important; }
    
    .card { background-color: #ffffff; border-radius: 12px; box-shadow: 0 0 40px rgba(0, 0, 0, 0.05); }
    .card-title span { color: #fff; font-weight: 700; font-size: 18px; }
    .card-header { background-color: #050C9C; border-bottom: 2px solid #3572EF; color: #ffffff; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 15px 20px; }
    .card-body { background-color: #ffffff; padding: 20px; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; }
    
    #systemLogsTable thead { background-color: #3572EF; color: white; text-align: center; }
    #systemLogsTable tbody tr:hover { background-color: #FFF591; color: #000; }
    
    /* Pagination Color Override */
    .page-item.active .page-link { background-color: #050C9C !important; border-color: #050C9C !important; }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-footer-fixed">
  <div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__wobble " src="../assets/dist/img/loader.gif" alt="AdminLTELogo" height="70" width="70">
    </div>

    <!-- Top Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
      <ul class="navbar-nav">
        <li class="nav-item"><h5><a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></h5></li>
        <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= $barangay ?></h5></li>
        <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
        <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= $zone ?></h5></li>
        <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
        <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= $district ?></h5></li>
      </ul>

      <!-- Right Navbar Links -->
      <ul class="navbar-nav ml-auto">
        <!-- Message Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-user"></i></a>

          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <a href="myProfile.php" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                <?php
                if (!empty($user_image)) {
                  echo '<img src="../assets/dist/img/' . $user_image . '" class="img-size-50 mr-3 img-circle" alt="User Image">';
                } else {
                  echo '<img src="../assets/dist/img/image.png" class="img-size-50 mr-3 img-circle" alt="User Image">';
                }
                ?>

                <div class="media-body">
                  <h3 class="dropdown-item-title py-3">
                    <?= ucfirst($first_name_user) . ' ' . ucfirst($last_name_user) ?>
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

    <!-- Main Navbar Container -->
    <aside class="main-sidebar elevation-4 sidebar-no-expand dark-mode">
      <img src="../assets/logo/ksugan.jpg" alt="Logo" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display:block;">
      <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>

            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-users-cog"></i><p>Barangay Official<i class="right fas fa-angle-left"></i></p></a>
              <ul class="nav nav-treeview">
                <li class="nav-item"><a href="newOfficial.php" class="nav-link "><i class="fas fa-circle nav-icon text-red"></i><p>New Official</p></a></li>
                <li class="nav-item"><a href="allOfficial.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>List of Official</p></a></li>
                <li class="nav-item"><a href="officialEndTerm.php" class="nav-link "><i class="fas fa-circle nav-icon text-red"></i><p>Official End Term</p></a></li>
              </ul>
            </li>

            <li class="nav-item"><a href="#" class="nav-link "><i class="nav-icon fas fa-users"></i><p>Residence<i class="right fas fa-angle-left"></i></p></a>
              <ul class="nav nav-treeview">
                <li class="nav-item"><a href="newResidence.php" class="nav-link "><i class="fas fa-circle nav-icon text-red"></i><p>New Residence</p></a></li>
                <li class="nav-item"><a href="allResidence.php" class="nav-link bg-indigo"><i class="fas fa-circle nav-icon text-red"></i><p>All Residence</p></a></li>
                <li class="nav-item"><a href="archiveResidence.php" class="nav-link "><i class="fas fa-circle nav-icon text-red"></i><p>Archive Residence</p></a></li>
              </ul>
            </li>

            <li class="nav-item "><a href="requestCertificate.php" class="nav-link"><i class="nav-icon fas fa-certificate"></i><p>Certificate</p></a></li>
            
            <li class="nav-item "><a href="#" class="nav-link"><i class="nav-icon fas fa-user-shield"></i><p>Users<i class="right fas fa-angle-left"></i></p></a>
              <ul class="nav nav-treeview">
                <li class="nav-item"><a href="usersResident.php" class="nav-link "><i class="fas fa-circle nav-icon text-red"></i><p>Resident</p></a></li>
                <li class="nav-item"><a href="userAdministrator.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>Administrator</p></a></li>
              </ul>
            </li>

            <!-- DRM Part   (START)   -->
            <li class="nav-item has-treeview"><a href="#" class="nav-link"><i class="nav-icon fas fa-exclamation-triangle"></i><p>DRRM<i class="right fas fa-angle-left"></i></p></a> 
              <ul class="nav nav-treeview">
                <li class="nav-item"><a href="drrmHousehold.php" class="nav-link"><i class="fas fa-users nav-icon text-red"></i><p>Household Members</p></a></li>
                <li class="nav-item"><a href="drrmEvacuation.php" class="nav-link"><i class="fas fa-house-damage nav-icon text-red"></i><p>Evacuation Center</p></a></li>
              </ul>
            </li>
            <!-- End of DRM Part -->

            <li class="nav-item"><a href="position.php" class="nav-link"><i class="nav-icon fas fa-user-tie"></i><p>Position</p></a></li>
            <li class="nav-item"><a href="blotterRecord.php" class="nav-link"><i class="nav-icon fas fa-clipboard"></i><p>Blotter Record</p></a></li>
            <li class="nav-item"><a href="report.php" class="nav-link"><i class="nav-icon fas fa-bookmark"></i><p>Reports</p></a></li>
            <li class="nav-item"><a href="systemLog.php" class="nav-link bg-indigo"><i class="nav-icon fas fa-history"></i><p>System Logs</p></a></li>
            
          </ul>
        </nav>
      </div>
    </aside>

    <div class="content-wrapper">
      <div class="content-header"></div>
      
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-12">
              <div class="card">
                <div class="card-header">
                  <div class="card-title"><span>SYSTEM LOGS</span></div>
                </div>
                
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-md-4 col-sm-6">
                       <label for="logTypeFilter">Filter Action:</label>
                       <select id="logTypeFilter" class="form-control">
                          <option value="">All Actions</option>
                          <option value="LOGIN">Login</option>
                          <option value="LOGOUT">Logout</option>
                          <option value="UPDATE">Update / Add</option>
                          <option value="DELETE">Delete</option>
                       </select>
                    </div>
                  </div>

              <!-- UPDATED PART -->
                  <table class="table table-hover text-nowrap" id="systemLogsTable" style="width:100%">
                    <thead>
                      <tr>
                        <th width="5%">#</th>
                        <th width="10%">User Type</th>
                        <th width="20%">User</th>
                        <th width="45%">Message</th>
                        <th width="20%">Date</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
              <!-- END OF UPDATE -->

                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <footer class="main-footer">
      <strong>&copy; <?= date("Y") ?></strong>
    </footer>
  </div>

  <script src="../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <script src="../assets/dist/js/adminlte.js"></script>
  <script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

  <script>
    $(document).ready(function() {
      
      var dataTable = $("#systemLogsTable").DataTable({
          processing: true,
          serverSide: true,
          autoWidth: false,
          order: [[0, "desc"]], // Sort by ID (column 0)
          ajax: {
            url: 'systemLogsTable.php',
            type: 'POST',
            data: function(d) {
              d.log_type_filter = $('#logTypeFilter').val();
            }
          },
          // Define columns to ensure data maps correctly
          columns: [
              { data: 0 }, // ID
              { data: 1 }, // User Type
              { data: 2 }, // User Name
              { data: 3 }, // Message
              { data: 4 }  // Date
          ],
          columnDefs: [
              // Center align the first two columns (ID and Type)
              { className: "text-center", targets: [0, 1] },
              // Vertically align all content
              { className: "align-middle", targets: "_all" }
          ],
          lengthMenu: [[10, 20, 50], [10, 20, 50]], 
          pagingType: "full_numbers",
          dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
              "<'row'<'col-sm-12'tr>>" +
              "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
      });

      $('#logTypeFilter').change(function() {
        dataTable.ajax.reload();
      });

    });
  </script>

</body>
</html>