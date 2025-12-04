<?php 
include_once '../connection.php';
session_start();

// --- AUTHENTICATION & USER DATA ---
try {
    // Check login
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
        echo '<script>window.location.href = "../login.php";</script>';
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Fetch user info
    $sql_user = "SELECT * FROM users WHERE id = ?";
    $stmt_user = $con->prepare($sql_user);
    $stmt_user->bind_param('s', $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $row_user = $result_user->fetch_assoc();

    $first_name_user = htmlspecialchars($row_user['first_name'] ?? '');
    $last_name_user = htmlspecialchars($row_user['last_name'] ?? '');
    $user_image = $row_user['image'] ?? '';

    // Fetch barangay info
    $sql_brgy = "SELECT * FROM barangay_information LIMIT 1";
    $result_brgy = $con->query($sql_brgy);
    $brgy_info = $result_brgy->fetch_assoc();
    $barangay = htmlspecialchars($brgy_info['barangay'] ?? '');
    $zone = htmlspecialchars($brgy_info['zone'] ?? '');
    $district = htmlspecialchars($brgy_info['district'] ?? '');

    // =================================================================================
    // LOGIC 1: HOUSEHOLD MONITORING (From report-secre.php)
    // =================================================================================
    
    // Initialize filter variables for households
    $search_where = '';
    $params = [];
    $types = '';

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $_GET['search'];
        $search_where = " AND (h.household_number LIKE ? OR h.address LIKE ? OR 
                            CONCAT(ri.first_name, ' ', ri.last_name) LIKE ? OR 
                            ri.last_name LIKE ? OR ri.first_name LIKE ?)";
        $search_param = "%{$search}%";
        $params = array_fill(0, 5, $search_param);
        $types = str_repeat('s', 5);
    }

    // Household Query
    $base_sql = "SELECT 
                    h.id as household_id,
                    h.household_number,
                    h.address,
                    h.barangay,
                    h.municipality,
                    h.date_created,
                    h.household_head_id,
                    (SELECT COUNT(*) FROM household_members hm2 WHERE hm2.household_id = h.id) as total_members,
                    (SELECT COUNT(*) FROM household_members hm3 JOIN residence_information ri3 ON hm3.user_id = ri3.residence_id WHERE hm3.household_id = h.id AND ri3.age >= 60) as senior_count,
                    (SELECT COUNT(*) FROM household_members hm4 JOIN residence_status rs4 ON hm4.user_id = rs4.residence_id WHERE hm4.household_id = h.id AND rs4.pwd = 'YES') as pwd_count,
                    (SELECT COUNT(*) FROM household_members hm5 JOIN residence_status rs5 ON hm5.user_id = rs5.residence_id WHERE hm5.household_id = h.id AND rs5.single_parent = 'YES') as single_parent_count,
                    (SELECT COUNT(*) FROM household_members hm6 JOIN residence_status rs6 ON hm6.user_id = rs6.residence_id WHERE hm6.household_id = h.id AND rs6.voters = 'YES') as voters_count,
                    CONCAT(ri.first_name, ' ', ri.last_name) as head_name
                FROM households h
                LEFT JOIN users u ON h.household_head_id = u.id
                LEFT JOIN residence_information ri ON u.id = ri.residence_id
                WHERE 1=1 {$search_where}
                GROUP BY h.id, h.household_number, h.address, h.barangay, h.municipality, h.date_created, h.household_head_id
                ORDER BY h.date_created DESC";

    if (!empty($params)) {
        $stmt = $con->prepare($base_sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result_households = $stmt->get_result();
    } else {
        $result_households = $con->query($base_sql);
    }

    $household_rows = '';
    $total_households = 0;
    $total_residents = 0;
    $total_seniors = 0;
    $total_pwd = 0;
    
    if ($result_households && $result_households->num_rows > 0) {
        while ($row = $result_households->fetch_assoc()) {
            $total_households++;
            $total_residents += $row['total_members'];
            $total_seniors += $row['senior_count'];
            $total_pwd += $row['pwd_count'];
            
            // Vulnerability Logic
            $vulnerability_score = 0;
            if ($row['senior_count'] > 0) $vulnerability_score += 2;
            if ($row['pwd_count'] > 0) $vulnerability_score += 3;
            if ($row['single_parent_count'] > 0) $vulnerability_score += 1;
            if ($row['total_members'] >= 5) $vulnerability_score += 1;
            
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
            
            $head_name = !empty(trim($row['head_name'] ?? '')) ? $row['head_name'] : 'No head assigned';

            $household_rows .= '<tr class="household-row" data-id="' . $row['household_id'] . '">
                <td><strong>' . htmlspecialchars($row['household_number']) . '</strong><br><small class="text-muted">' . date('M d, Y', strtotime($row['date_created'])) . '</small></td>
                <td>' . htmlspecialchars($head_name) . '</td>
                <td>' . htmlspecialchars($row['address'] ?? 'N/A') . '</td>
                <td class="text-center"><span class="badge badge-primary" style="font-size:14px; padding:5px 10px;">' . $row['total_members'] . '</span></td>
                <td class="text-center">
                    ' . ($row['senior_count'] > 0 ? '<span class="badge badge-warning">'.$row['senior_count'].' Senior</span><br>' : '') . '
                    ' . ($row['pwd_count'] > 0 ? '<span class="badge badge-info">'.$row['pwd_count'].' PWD</span>' : '') . '
                    ' . ($row['senior_count'] == 0 && $row['pwd_count'] == 0 ? '<span class="text-muted">-</span>' : '') . '
                </td>
                <td class="text-center">' . ($row['voters_count'] > 0 ? '<span class="badge badge-success">'.$row['voters_count'].' Voters</span>' : '-') . '</td>
                <td class="text-center"><span class="badge ' . $vulnerability_class . '">' . $priority . '</span></td>
                <td class="text-center action-buttons">
                    <button class="btn btn-sm btn-info view-members" data-id="' . $row['household_id'] . '" data-number="' . htmlspecialchars($row['household_number']) . '" title="View Members"><i class="fas fa-users"></i></button>
                </td>
            </tr>';
        }
    } else {
        $household_rows .= '<tr><td colspan="8" class="text-center text-muted">No households found.</td></tr>';
    }

    // =================================================================================
    // LOGIC 2: RESIDENT MASTERLIST FILTERING (From old report.php)
    // =================================================================================
    
    $resident_rows = '';
    $resident_query = "SELECT residence_information.*, residence_status.* FROM residence_information 
                       INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id 
                       WHERE archive = 'NO'";

    // Check if Filter Form was Submitted
    if(isset($_POST['submit'])){
        $whereClause = [];
        
        // Retain values for inputs
        $filter_voters = $con->real_escape_string($_POST['voters'] ?? '');
        $filter_age = $con->real_escape_string($_POST['age'] ?? '');
        $filter_status = $con->real_escape_string($_POST['status'] ?? '');
        $filter_pwd = $con->real_escape_string($_POST['pwd'] ?? '');
        $filter_senior = $con->real_escape_string($_POST['senior'] ?? '');
        $filter_single_parent = $con->real_escape_string($_POST['single_parent'] ?? '');

        if(!empty($filter_voters)) $whereClause[] = "residence_status.voters='$filter_voters'";
        if(!empty($filter_age)) $whereClause[] = "residence_information.age='$filter_age'";
        if(!empty($filter_status)) $whereClause[] = "residence_status.status='$filter_status'";
        if(!empty($filter_pwd)) $whereClause[] = "residence_status.pwd='$filter_pwd'";
        if(!empty($filter_single_parent)) $whereClause[] = "residence_status.single_parent='$filter_single_parent'";
        if(!empty($filter_senior)) $whereClause[] = "residence_status.senior='$filter_senior'"; 

        if(count($whereClause) > 0){
            $resident_query .= ' AND ' . implode(' AND ', $whereClause);
        }
    }

    $query_report = $con->query($resident_query) or die ($con->error);
    
    if($query_report->num_rows > 0){
        while($row_report = $query_report->fetch_assoc()){
            $middle_name = ($row_report['middle_name'] != '') ? ucfirst($row_report['middle_name'])[0].'.' : '';
            
            $resident_rows .= '<tr>
                    <td>'.ucfirst($row_report['last_name']).', '.ucfirst($row_report['first_name']).'  '.$middle_name.' </td>
                    <td>'.$row_report['age'].'</td>
                    <td>'.$row_report['pwd'].'</td> <td>'.$row_report['single_parent'].'</td>
                    <td>'.$row_report['voters'].'</td>
                    <td>'.$row_report['status'].'</td>
                    <td>'.$row_report['senior'].'</td>
                </tr>';
        }
    } else {
        $resident_rows .= '<tr><td colspan="7" class="text-center">No residents found matching criteria.</td></tr>';
    }

} catch (Exception $e) {
    error_log($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reports & Monitoring</title>
  
  <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
  <link rel="stylesheet" href="../assets/dist/css/admin.css">

  <style>
    body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
    
    /* Navbar & Sidebar */
    .main-header.navbar, .main-sidebar { background-color: #050C9C !important; }
    .nav-link { color: #fff !important; }
    .sidebar .nav-link { color: #A7E6FF !important; }
    .sidebar .nav-link.active { background-color: #3572EF !important; color: white !important; }
    
    /* Stats Cards */
    .stats-card {
      border-radius: 10px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    .stats-card:hover { transform: translateY(-5px); }
    .stats-number { font-size: 28px; font-weight: 700; margin: 10px 0; }
    .stats-icon { font-size: 40px; opacity: 0.8; }

    /* Tables */
    .card-header { background-color: #050C9C; color: white; }
    thead { background-color: #050C9C; color: white; }
    
    /* Priority Badges */
    .priority-high { background-color: #dc3545; color: white; animation: pulse 2s infinite; padding: 5px 10px; border-radius: 4px; }
    .priority-medium { background-color: #ffc107; color: black; padding: 5px 10px; border-radius: 4px; }
    .priority-low { background-color: #28a745; color: white; padding: 5px 10px; border-radius: 4px; }
    
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.7; } 100% { opacity: 1; } }

    /* Input Group for Filters */
    .input-group-text.bg-indigo { background-color: #050C9C !important; color: white; border: none; }
    .btn-flat { border-radius: 0; }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-footer-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><span class="nav-link"><?= $barangay . ' - ' . $district ?></span></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-user"></i></a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="../logout.php" class="dropdown-item dropdown-footer">LOGOUT</a>
        </div>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <img src="../assets/logo/ksugan.jpg" alt="Logo" class="img-circle elevation-5" style="width: 70%; margin: 10px auto; display: block;">
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="newResidence.php" class="nav-link"><i class="nav-icon fas fa-user-plus"></i><p>New Residence</p></a></li>
          <li class="nav-item"><a href="allResidence.php" class="nav-link"><i class="nav-icon fas fa-users"></i><p>All Residents</p></a></li>
          <li class="nav-item"><a href="report.php" class="nav-link active bg-indigo"><i class="nav-icon fas fa-bookmark"></i><p>Reports</p></a></li>
          </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1>Reports & Monitoring</h1></div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">

        <div class="row mb-4">
          <div class="col-lg-3 col-6">
            <div class="stats-card card">
              <div class="card-body text-center">
                <div class="text-primary"><i class="fas fa-home stats-icon"></i></div>
                <div class="stats-number"><?= $total_households ?></div>
                <div class="stats-label">TOTAL HOUSEHOLDS</div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stats-card card">
              <div class="card-body text-center">
                <div class="text-success"><i class="fas fa-users stats-icon"></i></div>
                <div class="stats-number"><?= $total_residents ?></div>
                <div class="stats-label">TOTAL RESIDENTS</div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stats-card card">
              <div class="card-body text-center">
                <div class="text-warning"><i class="fas fa-wheelchair stats-icon"></i></div>
                <div class="stats-number"><?= $total_seniors ?></div>
                <div class="stats-label">SENIOR CITIZENS</div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stats-card card">
              <div class="card-body text-center">
                <div class="text-danger"><i class="fas fa-accessible-icon stats-icon"></i></div>
                <div class="stats-number"><?= $total_pwd ?></div>
                <div class="stats-label">PWD RESIDENTS</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card shadow mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title"><i class="fas fa-list mr-2"></i>Household Monitoring (Disaster Response)</h3>
            
            <form method="GET" action="report.php" class="form-inline ml-auto">
                <div class="input-group input-group-sm">
                  <input type="text" name="search" class="form-control" placeholder="Search Household..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                  <div class="input-group-append">
                    <button class="btn btn-warning" type="submit"><i class="fas fa-search"></i></button>
                  </div>
                </div>
            </form>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
              <table class="table table-hover table-striped">
                <thead>
                  <tr>
                    <th>Household No.</th>
                    <th>Head</th>
                    <th>Address</th>
                    <th class="text-center">Members</th>
                    <th class="text-center">Vulnerable</th>
                    <th class="text-center">Voters</th>
                    <th class="text-center">Priority</th>
                    <th class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?= $household_rows ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header bg-secondary">
                <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Resident Masterlist & Printing</h3>
            </div>
            <div class="card-body">
                <form action="report.php" method="post">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend"><span class="input-group-text bg-indigo">VOTERS</span></div>
                                <select name="voters" class="form-control">
                                    <option value="">ALL</option>
                                    <option value="YES" <?= (isset($_POST['voters']) && $_POST['voters']=='YES')?'selected':''?>>YES</option>
                                    <option value="NO" <?= (isset($_POST['voters']) && $_POST['voters']=='NO')?'selected':''?>>NO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend"><span class="input-group-text bg-indigo">AGE</span></div>
                                <input type="number" name="age" class="form-control" value="<?= $_POST['age'] ?? '' ?>"> 
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend"><span class="input-group-text bg-indigo">STATUS</span></div>
                                <select name="status" class="form-control">
                                    <option value="">ALL</option>
                                    <option value="ACTIVE" <?= (isset($_POST['status']) && $_POST['status']=='ACTIVE')?'selected':''?>>ACTIVE</option>
                                    <option value="INACTIVE" <?= (isset($_POST['status']) && $_POST['status']=='INACTIVE')?'selected':''?>>INACTIVE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend"><span class="input-group-text bg-indigo">PWD</span></div>
                                <select name="pwd" class="form-control">
                                    <option value="">ALL</option>
                                    <option value="YES" <?= (isset($_POST['pwd']) && $_POST['pwd']=='YES')?'selected':''?>>YES</option>
                                    <option value="NO" <?= (isset($_POST['pwd']) && $_POST['pwd']=='NO')?'selected':''?>>NO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend"><span class="input-group-text bg-indigo">SENIOR</span></div>
                                <select name="senior" class="form-control">
                                    <option value="">ALL</option>
                                    <option value="YES" <?= (isset($_POST['senior']) && $_POST['senior']=='YES')?'selected':''?>>YES</option>
                                    <option value="NO" <?= (isset($_POST['senior']) && $_POST['senior']=='NO')?'selected':''?>>NO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend"><span class="input-group-text bg-indigo">SINGLE PARENT</span></div>
                                <select name="single_parent" class="form-control">
                                    <option value="">ALL</option>
                                    <option value="YES" <?= (isset($_POST['single_parent']) && $_POST['single_parent']=='YES')?'selected':''?>>YES</option>
                                    <option value="NO" <?= (isset($_POST['single_parent']) && $_POST['single_parent']=='NO')?'selected':''?>>NO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-info px-4" name="submit"><i class="fas fa-filter"></i> Filter Results</button>
                            <a href="report.php" class="btn btn-danger px-4"><i class="fas fa-undo"></i> Reset</a>
                            
                            <?php 
                                $print_params = [];
                                if(isset($_POST['submit'])){
                                    if(!empty($_POST['voters'])) $print_params[] = "voters=".$_POST['voters'];
                                    if(!empty($_POST['age'])) $print_params[] = "age=".$_POST['age'];
                                    if(!empty($_POST['status'])) $print_params[] = "status=".$_POST['status'];
                                    if(!empty($_POST['pwd'])) $print_params[] = "pwd=".$_POST['pwd'];
                                    if(!empty($_POST['senior'])) $print_params[] = "senior=".$_POST['senior'];
                                    if(!empty($_POST['single_parent'])) $print_params[] = "single_parent=".$_POST['single_parent'];
                                }
                                $print_query_string = implode('&', $print_params);
                            ?>
                            <a href="printReport.php?<?= $print_query_string ?>" target="_blank" class="btn btn-warning px-4 elevation-2"><i class="fas fa-print"></i> Print Masterlist</a>
                        </div>
                    </div>
                </form>

                <hr>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="tableReport">             
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Age</th>
                          <th>PWD</th>
                          <th>Single Parent</th>
                          <th>Voters</th>
                          <th>Status</th>
                          <th>Senior</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?= $resident_rows ?>
                      </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-3">
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
                </ul>
              </div>
              <div class="col-md-6">
                <h5><i class="fas fa-map-marker-alt text-danger"></i> Evacuation Centers</h5>
                <ul class="list-unstyled">
                  <li><strong>Primary:</strong> Barangay Covered Court</li>
                  <li><strong>Secondary:</strong> Kalusugan Elementary School</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

      </div></section>
  </div>

  <footer class="main-footer">
    <strong>Copyright &copy; <?= date("Y") ?>.</strong> Barangay Kalusugan.
  </footer>
</div>

<div class="modal fade" id="viewMembersModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-indigo text-white">
        <h5 class="modal-title"><i class="fas fa-users mr-2"></i>Household Members</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div id="membersList"></div>
      </div>
    </div>
  </div>
</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function(){
    // Initialize Resident Data Table
    $("#tableReport").DataTable({
        searching: false,
        info: true,
        ordering: true,
        lengthChange: false,
        pageLength: 5
    });

    // View Members Logic (From report-secre.php)
    $(document).on('click', '.view-members', function(e) {
        e.preventDefault();
        var householdId = $(this).data('id');
        var householdNumber = $(this).data('number');
        
        $('#membersList').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading members...</div>');
        $('#viewMembersModal').modal('show');
        
        $.ajax({
            url: 'get_household_member.php',
            method: 'POST',
            data: { household_id: householdId },
            dataType: 'json',
            success: function(response) {
                if(response.success === true) {
                    var html = '<div class="alert alert-info">Household: <strong>' + householdNumber + '</strong></div>';
                    
                    if(response.members && response.members.length > 0) {
                        html += '<ul class="list-group">';
                        $.each(response.members, function(index, member) {
                            html += '<li class="list-group-item">';
                            html += '<strong>' + member.name + '</strong> ';
                            html += '<span class="badge badge-secondary">' + member.relationship + '</span>';
                            html += '<br><small>Age: ' + member.age + ' | Status: ' + member.status + '</small>';
                            html += '</li>';
                        });
                        html += '</ul>';
                    } else {
                        html += '<p class="text-center">No members found.</p>';
                    }
                    $('#membersList').html(html);
                } else {
                    $('#membersList').html('<p class="text-danger text-center">Error loading data.</p>');
                }
            },
            error: function() {
                $('#membersList').html('<p class="text-danger text-center">Connection failed.</p>');
            }
        });
    });
});
</script>

</body>
</html>