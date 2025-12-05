<?php 
session_start();
require __DIR__ . '/../vendor/autoload.php';
include_once '../connection.php';

// CONFIG
$MY_TEST_EMAIL = 'lawrencejohnmhinanay@tua.edu.ph'; 
$MY_TEST_PHONE = '09274176508'; 

$vars_status = [
    'PhilSMS API' => '✅ Integrated',
    'Resend Key'  => !empty(getenv('RESEND_API_KEY')) ? '✅ Loaded' : '❌ Missing',
];

function sendCombinedAlert($type, $message, $phone, $email) {
    $log = "";
    
    // 1. PhilSMS
    if (!empty($phone)) {
        $clean_phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($clean_phone, 0, 1) == "0") $final_phone = "63" . substr($clean_phone, 1);
        elseif (substr($clean_phone, 0, 1) == "9") $final_phone = "63" . $clean_phone;
        else $final_phone = $clean_phone;

        $ch = curl_init("https://dashboard.philsms.com/api/v3/sms/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "recipient" => $final_phone, "sender_id" => "PhilSMS", "message" => $message
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer 554|CayRg2wWAqSX68oeKVh7YmEg5MXKVVtemT16dIos75bdf39f", "Content-Type: application/json"]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $log .= ($code >= 200 && $code < 300) ? "✅ SMS Sent via PhilSMS.<br>" : "❌ SMS Failed.<br>";
    }

    // 2. Resend
    if (!empty(getenv('RESEND_API_KEY')) && !empty($email)) {
        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'from' => "Barangay Alert <onboarding@resend.dev>",
            'to' => [$email], 
            'subject' => "🚨 TEST ALERT: {$type}", 
            'html' => "<h1>{$type} ALERT</h1><p>{$message}</p>"
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . getenv('RESEND_API_KEY'), 'Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $log .= ($code == 200) ? "✅ Email Sent via Resend.<br>" : "❌ Email Failed.<br>";
    }
    return $log;
}

$page_message = "";
try {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
        echo '<script>window.location.href = "../login.php";</script>'; exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['trigger'])) {
        $trigger = $_POST['trigger'];
        $brgy = getenv('BARANGAY_NAME');

        if ($trigger == 'earthquake') {
            $msg = "ALERT: Mag 6.5 Earthquake detected in TEST REGION. Distance: 50.0km from {$brgy}. Prepare for shaking.";
            $page_message = "<strong>Simulated Earthquake Triggered.</strong><br>" . sendCombinedAlert("EARTHQUAKE", $msg, $MY_TEST_PHONE, $MY_TEST_EMAIL);
        } else {
            // WEATHER LOGIC
            $stmt_info = $con->prepare("SELECT flood_history FROM barangay_information LIMIT 1");
            $stmt_info->execute();
            $hist = $stmt_info->get_result()->fetch_assoc()['flood_history'] ?? 'rare';

            $data = ['flood_history' => $hist, 'rainfall_category' => 'light', 'rainfall_amount_mm' => 0];
            if ($trigger == 'red') $data = ['rainfall_category' => 'heavy', 'rainfall_amount_mm' => 70.0, 'flood_history' => $hist];
            if ($trigger == 'orange') $data = ['rainfall_category' => 'moderate', 'rainfall_amount_mm' => 35.0, 'flood_history' => $hist];

            $ch = curl_init('http://barangay_api.railway.internal:8080/predict');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $ai_resp = json_decode(curl_exec($ch), true);
            curl_close($ch);
            
            $status = $ai_resp['prediction'] ?? 'Error';
            if ($status != 'Error') {
                $con->query("UPDATE current_alert_status SET status = '$status' WHERE id = 1");
                $page_message = "Triggered: <strong>$trigger</strong>. Result: <strong>$status</strong>.<br>";
                
                if ($status == 'evacuate' || $status == 'warn') {
                    $msg = ($status == 'evacuate') ? "URGENT {$brgy}: EVACUATE NOW." : "WARNING {$brgy}: Heavy rain detected.";
                    $page_message .= sendCombinedAlert("WEATHER", $msg, $MY_TEST_PHONE, $MY_TEST_EMAIL);
                }
            }
        }
    }
} catch(Exception $e) { echo $e->getMessage(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Profile</title>
    <!-- Website Logo -->
    <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
        <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">

        <!-- Sidebar -->
        <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"><a href="dashboard.php" class="nav-link "><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-users-cog"></i><p>Barangay Official<i class="right fas fa-angle-left"></i></p></a>
                <ul class="nav nav-treeview">
                <li class="nav-item"><a href="newOfficial.php" class="nav-link "><i class="fas fa-circle nav-icon text-red"></i><p>New Official</p></a></li>
                <li class="nav-item"><a href="allOfficial.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>List of Official</p></a></li>
                <li class="nav-item"><a href="officialEndTerm.php" class="nav-link "><i class="fas fa-circle nav-icon text-red"></i><p>Official End Term</p></a></li>
                <li class="nav-item"><a href="position.php" class="nav-link"><i class="nav-icon fas fa-user-tie"></i><p>Position</p></a></li>
                </ul>
            </li>
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Residence<i class="right fas fa-angle-left"></i></p></a>
                <ul class="nav nav-treeview">
                <li class="nav-item"><a href="newResidence.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>New Residence</p></a></li>
                <li class="nav-item"><a href="allResidence.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>All Residence</p></a></li>
                <li class="nav-item"><a href="archiveResidence.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>Archive Residence</p></a></li>
                </ul>
            </li>
            <li class="nav-item "><a href="#" class="nav-link"><i class="nav-icon fas fa-user-shield"></i><p>Users<i class="right fas fa-angle-left"></i></p></a>
                <ul class="nav nav-treeview">
                <li class="nav-item"><a href="usersResident.php" class="nav-link "><i class="fas fa-circle nav-icon text-red"></i><p>Resident</p></a></li>
                <li class="nav-item"><a href="editRequests.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>Edit Requests</p></a></li>
                <li class="nav-item"><a href="userAdministrator.php" class="nav-link"><i class="fas fa-circle nav-icon text-red"></i><p>Administrator</p></a></li>
                </ul>
            </li>
            <li class="nav-item"><a href="report.php" class="nav-link"><i class="nav-icon fas fa-bookmark"></i><p>Masterlist Report</p></a></li>
            <li class="nav-item"><a href="requestCertificate.php" class="nav-link"><i class="nav-icon fas fa-certificate"></i><p>Certificate</p></a></li>
            <li class="nav-item"><a href="blotterRecord.php" class="nav-link"><i class="nav-icon fas fa-clipboard"></i><p>Blotter Record</p></a></li>
            <li class="nav-item"><a href="forceTrigger.php" class="nav-link bg-indigo"><i class="nav-icon fas fa-exclamation-triangle"></i><p>Force Trigger Emergency</p></a></li>
            <li class="nav-item"><a href="systemLog.php" class="nav-link"><i class="nav-icon fas fa-history"></i><p>System Logs</p></a></li>
            
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <section class="content-header"><h1>Force Trigger (Weather & Earthquake)</h1></section>
    <section class="content">
      <div class="container-fluid">
        
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">System Health Check</h3></div>
            <div class="card-body">
                <div class="row">
                    <?php foreach($vars_status as $key => $val): ?>
                        <div class="col-md-3"><strong><?= $key ?>:</strong> <?= $val ?></div>
                    <?php endforeach; ?>
                </div>
                <small class="text-danger mt-2 d-block">Test Mode: Alerts sent to Admin Only.</small>
            </div>
        </div>

        <?php if (!empty($page_message)): ?>
            <div class="alert alert-warning"><?= $page_message ?></div>
        <?php endif; ?>

        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Weather Simulations</h3></div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-3"><button name="trigger" value="normal" class="btn btn-success btn-block">Normal</button></div>
                        <div class="col-md-3"><button name="trigger" value="yellow" class="btn btn-warning btn-block">Yellow</button></div>
                        <div class="col-md-3"><button name="trigger" value="orange" class="btn btn-block" style="background:#fd7e14;color:white">Orange</button></div>
                        <div class="col-md-3"><button name="trigger" value="red" class="btn btn-danger btn-block">Red (Evacuate)</button></div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card-danger">
            <div class="card-header"><h3 class="card-title">Seismic Simulation</h3></div>
            <div class="card-body">
                <p>Simulate a Magnitude 6.5 Earthquake occurring 50km from Barangay Kalusugan.</p>
                <form method="POST">
                    <button name="trigger" value="earthquake" class="btn btn-danger btn-block btn-lg">
                        <i class="fas fa-house-damage"></i> FORCE EARTHQUAKE ALERT
                    </button>
                </form>
            </div>
        </div>

      </div>
    </section>
  </div>
</div>
</body>
</html>