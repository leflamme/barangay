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

    $sql_voters_yes = "SELECT voters, archive FROM residence_status WHERE voters = ? AND archive = ?";
    $query_voters_yes = $con->prepare($sql_voters_yes) or die ($con->error);
    $query_voters_yes->bind_param('ss',$yes,$no);
    $query_voters_yes->execute();
    $query_voters_yes->store_result();
    $count_voters_yes = $query_voters_yes->num_rows;


    $sql_voters_no = "SELECT voters, archive FROM residence_status WHERE voters = ? AND archive = ?";
    $query_voters_no = $con->prepare($sql_voters_no) or die ($con->error);
    $query_voters_no->bind_param('ss',$no,$no);
    $query_voters_no->execute();
    $query_voters_no->store_result();
    $count_voters_no = $query_voters_no->num_rows;

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

    
    $sql_total_residence = "SELECT residence_id FROM residence_status WHERE archive = ?";
    $query_total_residence = $con->prepare($sql_total_residence) or die ($con->error);
    $query_total_residence->bind_param('s',$no);
    $query_total_residence->execute();
    $query_total_residence->store_result();
    $count_total_residence = $query_total_residence->num_rows;

    
    

    $sql_senior = "SELECT age FROM residence_information  INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id WHERE age  >= 60  AND archive = 'NO'";
    $query_senior = $con->query($sql_senior) or die ($con->error);
    $count_senior = $query_senior->num_rows;

    $sql_blotter ="SELECT date_added as yyyy, count(blotter_id) as gago from blotter_record group by date_added order by yyyy";
    $result_blotter = $con->query($sql_blotter) or die ($con->error);
    $count_blotter_result = $result_blotter->num_rows;
    if($count_blotter_result > 0){
      while ($row_blotter = $result_blotter->fetch_array()) { 
        $year[]  = $row_blotter['yyyy']  ;
        $totalBlotter[] = number_format($row_blotter['gago']);
      }

    }else{
      $year[]  = ['0000','1000'];
      $totalBlotter[] = ['100','200'];
    }
    

    $sql_gender ="SELECT COUNT(CASE WHEN gender = 'Male' THEN residence_information.residence_id END) as male,
    COUNT(CASE WHEN gender = 'Female' THEN residence_information.residence_id END) as female
    FROM residence_information
    INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id
    WHERE  archive = 'NO' ";
  
    $result_gender = $con->query($sql_gender) or die ($con->error);


    while ($row_gender = $result_gender->fetch_assoc()) { 
      $genderMale  = $row_gender['male']  ;
      $genderFemale  = $row_gender['female']  ;
    
    }

    $sql_total_blotter = "SELECT blotter_id FROM blotter_record";
    $stmt_total_blotter = $con->prepare($sql_total_blotter) or die ($con->error);
    $stmt_total_blotter->execute();
    $result_total_blotter = $stmt_total_blotter->get_result();
    $count_blotter = $result_total_blotter->num_rows;
    $total_blotter_record = $count_blotter;

  $sql_count_official =  "SELECT COUNT(official_id) AS total_official FROM official_status";
  $stmt_total_official = $con->prepare($sql_count_official) or die ($con->error);
  $stmt_total_official->execute();
  $result_total_official = $stmt_total_official->get_result();
  $row_total_official = $result_total_official->fetch_assoc();

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
  <title>List: Family Household</title>
  <!-- Website Logo -->
  <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">

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
  /* Add this style for better spacing */
#householdTable {
  margin-bottom: 0 !important;
}
#householdShowingText {
  display: inline-block;
  margin-bottom: 8px;
}
#householdPagination {
  margin-bottom: 8px !important;
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
    <img src="../assets/logo/ksugan.jpg" alt="" class="brand-image img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">

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
                  <i class="fas fa-user nav-icon text-red"></i>
                  <p>New Official</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="allOfficial.php" class="nav-link">
                  <i class="fas fa-users nav-icon text-red"></i>
                  <p>List of Official</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="officialEndTerm.php" class="nav-link ">
                  <i class="fas fa-users nav-icon text-red"></i>
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
                  <i class="fas fa-user nav-icon text-red"></i>
                  <p>New Residence</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="allResidence.php" class="nav-link ">
                  <i class="fas fa-users nav-icon text-red"></i>
                  <p>All Residence</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="archiveResidence.php" class="nav-link ">
                  <i class="fas fa-users nav-icon text-red"></i>
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
                  <i class="fas fa-users nav-icon text-red"></i>
                  <p>Resident</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="userAdministrator.php" class="nav-link">
                  <i class="fas fa-user-shield nav-icon text-red"></i>
                  <p>Administrator</p>
                </a>
              </li>

            </ul>
          </li>
          

          <li class="nav-item has-treeview menu-open">
  <a href="#" class="nav-link">
    <i class="nav-icon fas fa-exclamation-triangle"></i>
    <p>
      DRRM
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="drrmHousehold.php" class="nav-link bg-indigo">
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




          <li class="nav-item">
            <a href="blotterRecord.php" class="nav-link">
              <i class="nav-icon fas fa-clipboard"></i>
              <p>
                Blotter Record
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="report.php" class="nav-link">
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
        <!-- Household List Interface -->
        <div class="card">
          <div class="card-header" style="background-color: #007bff; color: #fff;">
            <h3 class="card-title mb-0">Households List</h3>
          </div>
          <div class="card-body pb-0">
            <div class="row mb-2 align-items-center">
              <div class="col-md-6 col-12">
                <?php
                  // Get all unique last names (surnames) from residence_information
                  $sql_households = "SELECT DISTINCT last_name FROM residence_information ORDER BY last_name ASC";
                  $result_households = $con->query($sql_households);
                  $household_count = $result_households->num_rows;
                  $shown_count = $household_count; 
                ?>
                <span id="householdCountText">Show <?php echo $shown_count; ?> of <?php echo $household_count; ?> entries</span>
              </div>
              <div class="col-md-6 col-12 text-right">
                <input type="text" id="householdSearch" class="form-control" placeholder="Search surname...">
              </div>
            </div>
            <div class="table-responsive mb-4"><!-- Added margin-bottom here -->
              <table class="table table-bordered table-hover" id="householdTable">
                <thead>
                  <tr style="background-color: #eaf4ff;">
                    <th>Surname</th>
                    <th style="width: 120px;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Get all unique last names (surnames) from residence_information
                  $sql_households = "SELECT DISTINCT last_name FROM residence_information ORDER BY last_name ASC";
                  $result_households = $con->query($sql_households);
                  $household_count = $result_households->num_rows;
                  $shown_count = 0;
                  if ($household_count > 0) {
                    while ($row_household = $result_households->fetch_assoc()) {
                      $surname = ucfirst($row_household['last_name']);
                      echo '<tr>
                              <td>' . htmlspecialchars($surname . ' Family') . '</td>
                              <td>
                                <a href="drrmEvacuation.php?surname=' . urlencode($row_household['last_name']) . '" class="btn btn-primary btn-sm">View</a>
                              </td>
                            </tr>';
                      $shown_count++;
                    }
                  } else {
                    echo '<tr><td colspan="2" class="text-center">No households found.</td></tr>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div class="row mt-3 mb-2 align-items-center"><!-- Increased margin-top and added margin-bottom -->
              <div class="col-md-6 col-12">
                <span id="householdShowingText">Showing <?php echo $shown_count; ?> of <?php echo $household_count; ?> entries</span>
              </div>
              <div class="col-md-6 col-12 text-right">
                <nav>
                  <ul class="pagination pagination-sm mb-0" id="householdPagination" style="justify-content: flex-end;">
                    <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">Previous</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
        <!-- End Household List Interface -->
      </div>
    </section>
    <!-- /.content -->
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
      fontColor: '#fff',
    
    
    },
     legend:{
      display: true,
      fontColor: '#fff',
      labels: {
                fontSize: 15,
                fontColor: '#fff',
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
      fontColor: '#fff',
    
    
    },
     legend:{
      display: true,
      fontColor: '#fff',
      labels: {
                fontSize: 15,
                fontColor: '#fff',
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
<script>
$(document).ready(function(){
  // Simple search filter for household table
  $("#householdSearch").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    var shown = 0;
    $("#householdTable tbody tr").filter(function() {
      var match = $(this).find("td:first").text().toLowerCase().indexOf(value) > -1;
      $(this).toggle(match);
      if(match) shown++;
    });
    var total = $("#householdTable tbody tr").length;
    $("#householdCountText").text("Show " + shown + " of " + total + " entries");
    $("#householdShowingText").text("Showing " + shown + " of " + total + " entries");
  });
});
</script>


</body>
</html>
