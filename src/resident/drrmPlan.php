<?php 
session_start();
include_once '../connection.php';

try {
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'resident') {
        $user_id = $_SESSION['user_id'];
        $sql_user = "SELECT * FROM `users` WHERE `id` = ? ";
        $stmt_user = $con->prepare($sql_user) or die($con->error);
        $stmt_user->bind_param('s', $user_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        $row_user = $result_user->fetch_assoc();
        $first_name_user = $row_user['first_name'];
        $last_name_user = $row_user['last_name'];
        $user_image = $row_user['image'];

        $sql_resident = "SELECT residence_information.*, residence_status.* FROM residence_information
        INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id
        WHERE residence_information.residence_id = '$user_id'";
        $query_resident = $con->query($sql_resident) or die($con->error);
        $row_resident = $query_resident->fetch_assoc();

        $sql_user_resident = "SELECT username FROM users WHERE id = '$user_id'";
        $query_user_resident = $con->query($sql_user_resident) or die($con->error);
        $row_user_resident = $query_user_resident->fetch_assoc();

        $sql = "SELECT * FROM `barangay_information`";
        $query = $con->prepare($sql) or die($con->error);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        $barangay = $row['barangay'];
        $zone = $row['zone'];
        $district = $row['district'];
        $image = $row['image'];
        $image_path = $row['image_path'];

    } else {
        echo '<script>
                window.location.href = "../login.php";
              </script>';
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
  <title></title>

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
  <style>
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
      <li class="nav-item"><h5><a class="nav-link text-white" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></h5></li>
      <li class="nav-item d-none d-sm-inline-block" style="font-variant: small-caps;"><h5 class="nav-link text-white"><?= $barangay ?></h5>
      <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
      <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= htmlspecialchars($zone) ?></h5></li>
      <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
      <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= htmlspecialchars($district) ?></h5></li>
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
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="myProfile.php" class="nav-link">
              <i class="nav-icon fas fa-user"></i>
              <p>My Profile</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="personalInformation.php" class="nav-link">
              <i class="nav-icon fas fa-address-book"></i>
              <p>Personal Information</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="myRecord.php" class="nav-link">
              <i class="nav-icon fas fa-server"></i>
              <p>Blotter Record</p>
            </a>
          </li>
          <li class="nav-item bg-indigo">
            <a href="drrmPlan.php" class="nav-link">
              <i class="fas fa-clipboard-list nav-icon text-red"></i>
              <p>Emergency Plan</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="changePassword.php" class="nav-link">
              <i class="nav-icon fas fa-lock"></i>
              <p>Change Password</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="certificate.php" class="nav-link">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>Certificate</p>
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
        <!-- Emergency Evacuation Plan Interface -->
        <div class="card mb-4">
          <div class="card-header" style="background-color: #007bff; color: #fff;">
            <h3 class="card-title mb-0">Emergency Evacuation Plan</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-7">
                <h5 class="font-weight-bold mb-3">What To Do During Emergencies</h5>
                <ul>
                  <li>Stay calm and listen to official announcements from barangay officials.</li>
                  <li>Prepare your emergency kit (food, water, flashlight, medicines, important documents).</li>
                  <li>Know the nearest evacuation center and the safest route from your home.</li>
                  <li>Assist children, elderly, and persons with disabilities.</li>
                  <li>Follow instructions from emergency personnel and cooperate during evacuation.</li>
                  <li>Keep emergency hotlines handy for quick access.</li>
                </ul>
                <h5 class="font-weight-bold mt-4 mb-3">Barangay Emergency Hotlines</h5>
                <table class="table table-bordered table-sm w-75">
                  <tbody>
                    <tr>
                      <td><strong>Barangay Hall</strong></td>
                      <td>(02) 1234-5678</td>
                    </tr>
                    <tr>
                      <td><strong>Police Station</strong></td>
                      <td>(02) 8765-4321</td>
                    </tr>
                    <tr>
                      <td><strong>Fire Department</strong></td>
                      <td>(02) 1122-3344</td>
                    </tr>
                    <tr>
                      <td><strong>Health Center</strong></td>
                      <td>(02) 5566-7788</td>
                    </tr>
                  </tbody>
                </table>
                <div class="alert alert-info mt-4">
                  <strong>Tip:</strong> Save these numbers on your phone and share with your family members.
                </div>
              </div>
              <div class="col-md-5 text-center">
                <h5 class="font-weight-bold mb-3">Evacuation Map</h5>
                <img src="../assets/logo/brgy_map.png" alt="Barangay Evacuation Map" class="img-fluid rounded shadow" style="max-height: 350px;">
                <p class="mt-2 text-muted">Locate your home and the nearest evacuation center on the map above.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- End Emergency Evacuation Plan Interface -->
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


</body>
</html>
