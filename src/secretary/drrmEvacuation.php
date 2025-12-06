<?php 
session_start();
include_once '../connection.php';

try{
  if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'secretary'){
    $user_id = $_SESSION['user_id'];
    
    // 1. Get User Info
    $sql_user = "SELECT * FROM `users` WHERE `id` = ? ";
    $stmt_user = $con->prepare($sql_user) or die ($con->error);
    $stmt_user->bind_param('s',$user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $row_user = $result_user->fetch_assoc();
    $first_name_user = $row_user['first_name'];
    $last_name_user = $row_user['last_name'];
    $user_image = $row_user['image'];

    // 2. Get Barangay Info
    $sql = "SELECT * FROM `barangay_information`";
    $query = $con->prepare($sql) or die ($con->error);
    $query->execute();
    $result = $query->get_result();
    while($row = $result->fetch_assoc()){
        $barangay = $row['barangay'];
        $zone = $row['zone'];
        $district = $row['district'];
    }

    // 3. Helper Function: Get Families by Center Name
    function getFamiliesByCenter($con, $centerName) {
        // We use LIKE here just in case of small spaces differences, but exact match is better
        // This looks for the exact string saved in 'assigned_center'
        $sql = "SELECT 
                  last_name,
                  COUNT(*) as total_members,
                  (SELECT COUNT(*) 
                   FROM residence_information r2 
                   INNER JOIN evacuation_status es ON r2.residence_id = es.residence_id 
                   WHERE r2.last_name = residence_information.last_name 
                   AND es.status = 'Arrived') as arrived_count
                FROM residence_information 
                WHERE assigned_center = ? 
                GROUP BY last_name
                ORDER BY last_name ASC";
        
        $stmt = $con->prepare($sql);
        $stmt->bind_param('s', $centerName);
        $stmt->execute();
        return $stmt->get_result();
    }

  }else{
   echo '<script>window.location.href = "../login.php";</script>';
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
  <title>Evacuation Command Center</title>
  <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">
  
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
  
  <style>
    .main-header.navbar, .main-sidebar { background-color: #050C9C !important; }
    .nav-link, .nav-link:hover { color: #ffffff !important; }
    .sidebar .nav-link.active { background-color: #3572EF !important; }
    
    /* Tabs Styling */
    .nav-tabs .nav-link.active {
        background-color: #007bff;
        color: white !important;
        border-top: 3px solid #0056b3;
    }
    .nav-tabs .nav-link {
        color: #495057 !important;
        font-weight: bold;
    }
    .status-fraction { font-size: 1.2rem; font-weight: bold; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-footer-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-dark">
     <ul class="navbar-nav">
      <li class="nav-item"><h5><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></h5></li>
      <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link"><?= $barangay ?> - Evacuation Monitor</h5></li>
     </ul>

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

  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <!-- Brand Logo -->
    <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">

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
                <a href="editRequests.php" class="nav-link">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Edit Requests</p>
                </a>
              </li>
            </ul>
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
                  <a href="drrmEvacuation.php" class="nav-link bg-indigo">
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
         
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0 text-dark">Evacuation Center Management</h1></div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        
        <div class="card card-primary card-outline">
          <div class="card-header p-0 pt-1 border-bottom-0">
            <ul class="nav nav-tabs" id="evacuationTabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="tab-a" data-toggle="pill" href="#center-a" role="tab">Elem. School</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="tab-b" data-toggle="pill" href="#center-b" role="tab">Basketball Court</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="tab-c" data-toggle="pill" href="#center-c" role="tab">High School Annex</a>
              </li>
            </ul>
          </div>
          
          <div class="card-body">
            <div class="tab-content">
              
              <div class="tab-pane fade show active" id="center-a" role="tabpanel">
                <h4>Barangay Kalusugan Elementary School</h4>
                <?php 
                  // MUST MATCH DATABASE NAME EXACTLY
                  $resultFamilies = getFamiliesByCenter($con, 'Barangay Kalusugan Elementary School'); 
                  include 'evacuation_table_template.php'; 
                ?>
              </div>

              <div class="tab-pane fade" id="center-b" role="tabpanel">
                <h4>Kalusugan Open Basketball Court</h4>
                <?php 
                  $resultFamilies = getFamiliesByCenter($con, 'Kalusugan Open Basketball Court'); 
                  include 'evacuation_table_template.php'; 
                ?>
              </div>

              <div class="tab-pane fade" id="center-c" role="tabpanel">
                <h4>Quezon City High School Annex</h4>
                <?php 
                  $resultFamilies = getFamiliesByCenter($con, 'Quezon City High School Annex'); 
                  include 'evacuation_table_template.php'; 
                ?>
              </div>

            </div>
          </div>
        </div>

      </div>
    </section>
  </div>

  <div class="modal fade" id="familyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title">Family Checklist</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="text-white">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="familyModalBody">
           </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>Copyright &copy; <?php echo date("Y"); ?>.</strong></footer>
</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.min.js"></script>

<script>
$(document).ready(function() {
    $('.table').DataTable();

    // Open Modal
    $(document).on('click', '.view-family-btn', function() {
        var surname = $(this).data('surname');
        
        $('.modal-title').text('Family: ' + surname);
        $('#familyModalBody').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i> Loading...</div>');
        $('#familyModal').modal('show');

        // Calls the NEW file we created
        $.ajax({
            url: 'get_evacuation_family.php',
            type: 'POST',
            data: { surname: surname }, 
            success: function(response) {
                $('#familyModalBody').html(response);
            }
        });
    });

    // Toggle Status Logic
    $(document).on('click', '.toggle-status-btn', function() {
        var btn = $(this);
        var residence_id = btn.data('id');
        var current_status = btn.data('status');
        var new_status = (current_status === 'Arrived') ? 'Missing' : 'Arrived';

        btn.prop('disabled', true);

        $.ajax({
            url: 'updateEvacStatus.php',
            type: 'POST',
            data: { residence_id: residence_id, status: new_status },
            success: function(response) {
                btn.prop('disabled', false);
                btn.data('status', new_status);
                btn.text(new_status);
                // Update button color immediately
                btn.html( (new_status === 'Arrived' ? '<i class="fas fa-check"></i> ' : '<i class="fas fa-times"></i> ') + new_status );
                
                if(new_status === 'Arrived') {
                    btn.removeClass('btn-danger').addClass('btn-success');
                } else {
                    btn.removeClass('btn-success').addClass('btn-danger');
                }
            }
        });
    });
});
</script>
</body>
</html>