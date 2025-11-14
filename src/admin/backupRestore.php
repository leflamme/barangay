<?php 
session_start();
include_once '../connection.php';

try{
  if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){
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
  <title>Backup & Restore</title>

 <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
  <link rel="stylesheet" href="../assets/plugins/jquery-ui/jquery-ui.min.css">
  <link rel="stylesheet" href="../assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
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

  .dataTables_processing {
    position: absolute;
    top: 0px;
    left: 50%;
    width: 250px;
    margin-left: -125px;
    border: 1px solid #ddd;
    text-align: center;
    color: #999;
    font-size: 11px;
    padding: 2px 0;
    display: none;
}
</style>


</head>
<body class="hold-transition  sidebar-mini   layout-footer-fixed">
<div class="wrapper">

   <div class="preloader flex-column justify-content-center align-items-center">
          <img class="animation__wobble " src="../assets/dist/img/loader.gif" alt="AdminLTELogo" height="70" width="70">
          <br>
          <div class="container text-lime " id="showResult">

                          <?php 

                          if(isset($_POST['restore'])){
                            
                            // --- START OF FIX ---
                            // Get the uploaded file name
                            $test = $_POST['fileRestore'];

                            // Get credentials from Railway Environment Variables
                            define("DB_USER", getenv('MYSQL_USER'));
                            define("DB_PASSWORD", getenv('MYSQL_PASSWORD'));
                            define("DB_NAME", getenv('MYSQL_DATABASE'));
                            define("DB_HOST", getenv('MYSQL_HOST'));
                            define("DB_PORT", getenv('MYSQL_PORT')); // Get the Railway port
                            
                            // Use dirname(__DIR__) to get the parent directory of /admin
                            define("BACKUP_DIR", dirname(__DIR__) . '/permanent-data/backup');
                            // --- END OF FIX ---

                            define("BACKUP_FILE", $test); // Script will autodetect if backup file is gzipped based on .gz extension
                            define("CHARSET", 'utf8');
                            define("DISABLE_FOREIGN_KEY_CHECKS", true); // Set to true if you are having foreign key constraint fails
  
                            /**
                            * The Restore_Database class
                            */
                            class Restore_Database {
                            var $host;
                            var $username;
                            var $passwd;
                            var $dbName;
                            var $charset;
                            var $conn;
                            var $disableForeignKeyChecks;
  
                            function __construct($host, $username, $passwd, $dbName, $charset = 'utf8') {
                            $this->host                    = $host;
                            $this->username                = $username;
                            $this->passwd                  = $passwd;
                            $this->dbName                  = $dbName;
                            $this->charset                 = $charset;
                            $this->disableForeignKeyChecks = defined('DISABLE_FOREIGN_KEY_CHECKS') ? DISABLE_FOREIGN_KEY_CHECKS : true;
                            $this->conn                    = $this->initializeDatabase();
                            $this->backupDir               = defined('BACKUP_DIR') ? BACKUP_DIR : '.';
                            $this->backupFile              = defined('BACKUP_FILE') ? BACKUP_FILE : null;
                            }
  
                            function __destructor() {
                            if ($this->disableForeignKeyChecks === true) {
                              mysqli_query($this->conn, 'SET foreign_key_checks = 1');
                            }
                            }
  
                            protected function initializeDatabase() {
                            try {
                              // --- FIX: ADDED DB_PORT ---
                              $conn = mysqli_connect($this->host, $this->username, $this->passwd, $this->dbName, DB_PORT);
                              if (mysqli_connect_errno()) {
                                  throw new Exception('ERROR connecting database: ' . mysqli_connect_error());
                                  die();
                              }
                              if (!mysqli_set_charset($conn, $this->charset)) {
                                  mysqli_query($conn, 'SET NAMES '.$this->charset);
                              }
  
                              if ($this->disableForeignKeyChecks === true) {
                                  mysqli_query($conn, 'SET foreign_key_checks = 0');
                              }
  
                            } catch (Exception $e) {
                              print_r($e->getMessage());
                              die();
                            }
  
                            return $conn;
                            }
  
                            public function restoreDb() {
                            try {
                              $sql = '';
                              $multiLineComment = false;
  
                              $backupDir = $this->backupDir;
                              $backupFile = $this->backupFile;
  
                              $backupFileIsGzipped = substr($backupFile, -3, 3) == '.gz' ? true : false;
                              if ($backupFileIsGzipped) {
                                  if (!$backupFile = $this->gunzipBackupFile()) {
                                      throw new Exception("ERROR: couldn't gunzip backup file " . $backupDir . '/' . $backupFile);
                                  }
                              }
  
                              $handle = fopen($backupDir . '/' . $backupFile, "r");
                              if ($handle) {
                                  while (($line = fgets($handle)) !== false) {
                                      $line = ltrim(rtrim($line));
                                      if (strlen($line) > 1) { // avoid blank lines
                                          $lineIsComment = false;
                                          if (preg_match('/^\/\*/', $line)) {
                                              $multiLineComment = true;
                                              $lineIsComment = true;
                                          }
                                          if ($multiLineComment or preg_match('/^\/\//', $line)) {
                                              $lineIsComment = true;
                                          }
                                          if (!$lineIsComment) {
                                              $sql .= $line;
                                              if (preg_match('/;$/', $line)) {
                                                  // execute query
                                                  if(mysqli_query($this->conn, $sql)) {
                                                      if (preg_match('/^CREATE TABLE `([^`]+)`/i', $sql, $tableName)) {
                                                          $this->obfPrint("Table succesfully created: `" . $tableName[1] . "`");
                                                      }
                                                      $sql = '';
                                                  } else {
                                                      throw new Exception("ERROR: SQL execution error: " . mysqli_error($this->conn));
                                                  }
                                              }
                                          } else if (preg_match('/\*\/$/', $line)) {
                                              $multiLineComment = false;
                                          }
                                      }
                                  }
                                  fclose($handle);
                              } else {
                                  throw new Exception("ERROR: couldn't open backup file " . $backupDir . '/' . $backupFile);
                              } 
                            } catch (Exception $e) {
                              print_r($e->getMessage());
                              return false;
                            }
  
                            if ($backupFileIsGzipped) {
                              unlink($backupDir . '/' . $backupFile);
                            }
  
                            return true;
                            }
  
                            protected function gunzipBackupFile() {
                            $bufferSize = 4096; 
                            $error = false;
  
                            $source = $this->backupDir . '/' . $this->backupFile;
                            $dest = $this->backupDir . '/' . date("Ymd_His", time()) . '_' . substr($this->backupFile, 0, -3);
  
                            $this->obfPrint('Gunzipping backup file ' . $source . '... ', 1, 1);
  
                            if (file_exists($dest)) {
                              if (!unlink($dest)) {
                                  return false;
                              }
                            }
  
                            if (!$srcFile = gzopen($this->backupDir . '/' . $this->backupFile, 'rb')) {
                              return false;
                            }
                            if (!$dstFile = fopen($dest, 'wb')) {
                              return false;
                            }
  
                            while (!gzeof($srcFile)) {
                              if(!fwrite($dstFile, gzread($srcFile, $bufferSize))) {
                                  return false;
                              }
                            }
  
                            fclose($dstFile);
                            gzclose($srcFile);
  
                            return str_replace($this->backupDir . '/', '', $dest);
                            }
  
                            public function obfPrint ($msg = '', $lineBreaksBefore = 0, $lineBreaksAfter = 1) {
                            if (!$msg) {
                              return false;
                            }
  
                            $msg = date("Y-m-d H:i:s") . ' - ' . $msg;
                            $output = '';
  
                            if (php_sapi_name() != "cli") {
                              $lineBreak = "<br />";
                            } else {
                              $lineBreak = "\n";
                            }
  
                            if ($lineBreaksBefore > 0) {
                              for ($i = 1; $i <= $lineBreaksBefore; $i++) {
                                  $output .= $lineBreak;
                              }                
                            }
  
                            $output .= $msg;
  
                            if ($lineBreaksAfter > 0) {
                              for ($i = 1; $i <= $lineBreaksAfter; $i++) {
                                  $output .= $lineBreak;
                              }                
                            }
  
                            if (php_sapi_name() == "cli") {
                              $output .= "\n";
                            }
  
                            echo $output;
  
                            if (php_sapi_name() != "cli") {
                              ob_flush();
                            }
  
                            flush();
                            }
                            }
  
                            error_reporting(E_ALL);
  
                            if (php_sapi_name() != "cli") {
                            echo '<div style="font-family: monospace;">';
                            }
  
                            $restoreDatabase = new Restore_Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                            $result = $restoreDatabase->restoreDb(BACKUP_DIR, BACKUP_FILE) ? 'OK' : 'KO';
                            $restoreDatabase->obfPrint("Restoration result: ".$result, 1);
  
                            if (php_sapi_name() != "cli") {
                            echo '</div>';
                            }
  
                          }
                          ?>

</div>

        </div>

 

  <nav class="main-header navbar navbar-expand navbar-dark">
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
  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">

    <div class="sidebar">
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
          <li class="nav-item">
            <a href="backupRestore.php" class="nav-link bg-indigo">
              <i class="nav-icon fas fa-database"></i>
              <p>
                Backup/Restore
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
          <div class="col-sm-6">
            
          </div><div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              
            </ol>
          </div></div></div></div>
    <section class="content">
      <div class="container-fluid">

      <div class="row mt-5">
        <!-- Changed upload backup logic design -->
        <div class="col-sm-6">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <h5>Backup & Restore</h5>
            </div>
        </div>
        <div class="card-body">

            <button type="button" class="btn btn-flast btn-danger elevation-5 px-3 btn-flat mb-3" id="generateBackup"><i class="fas fa-file-export"></i> Generate Backup</button>
            
            <hr>

            <h6>Restore from Generated List</h6>
            <form action="backupRestore.php" method="post" onsubmit="return confirm('Are you sure you want to restore this backup? This will overwrite all current data.');">
                <div class="form-group">
                    <label for="fileRestoreSelect">Select Backup to Restore:</label>
                    <select name="fileRestore" id="fileRestoreSelect" class="form-control" required>
                        <option value="" disabled selected>-- Select a backup --</option>
                        <?php
                            // Fetch list of backups from DB
                            $sql_list = "SELECT id, path FROM backup ORDER BY id DESC";
                            $result_list = $con->query($sql_list);
                            if ($result_list && $result_list->num_rows > 0) {
                                while($row_list = $result_list->fetch_assoc()) {
                                    // The 'value' will be the filename, e.g., "BackupFile-11142025_223000.sql"
                                    echo '<option value="'.htmlspecialchars($row_list['path']).'">'.htmlspecialchars($row_list['path']).'</option>';
                                }
                            } else {
                                echo '<option value="" disabled>No backups found. Generate one first.</option>';
                            }
                        ?>
                    </select>
                </div>
                <button type="submit" name="restore" class="btn btn-info btn-flat elevation-5 px-3 mt-3"><i class="fas fa-recycle"></i> Restore from Selected</button>
            </form>

            <hr>

            <h6>Restore from Manual Upload</h6>
            <form action="backupRestore.php" method="post" enctype="multipart/form-data"> 
                <div class="p-5 text-center container elevation-5" style="border: 2px solid gray; border-style: dashed; cursor: pointer;" id="uploadFile">
                    Click Here to Upload <i class="fas fa-upload"></i> <br>
                    <span id="showValue"></span>
                </div>
                <input type="file" class="d-none" id="backup_file" name="fileRestore_upload" disabled> <input type="text" class="d-none" id="fileRestore" name="fileRestore"> <button type="submit" name="restore" id="restore" class="btn btn-secondary btn-flat elevation-5 px-3 mt-3 "><i class="fas fa-upload"></i> Restore from Upload</button>
            </form>
        </div>
    </div>
</div>

        <div class="col-sm-6">
          <div class="card">
              <div class="card-header">
                <div class="card-title">
                  <h5>Backup File</h5>
                </div>
              </div>
            <div class="card-body">

                <table class="table " id="backupTable">
                  <thead>
                    <tr>
                      <th>File Name</th>
                      <th>Action</th>
                    </tr>
                    </thead>
                </table>
              
            </div>
          </div>
        </div>
      </div>
           
      
        
   
       
       
          
      </div></section>
    </div>
  <footer class="main-footer">
  <strong>&copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')) ?></strong>
  <div class="float-right d-none d-sm-inline-block">
  </div>
</footer>
    
    <div class="float-right d-none d-sm-inline-block">
    </div>
  </footer>
</div>
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
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
<script src="../assets."/plugins/jquery-validation/additional-methods.min.js"></script>


<script>
  $(document).ready(function(){

    // --- NEW SCRIPT FOR FILE UPLOAD ---
    // When the user clicks the upload box
    $("#uploadFile").on('click',function(){
      $("#backup_file_upload").click(); // Trigger the hidden file input
    });

    // When the hidden file input changes
    $("#backup_file_upload").change(function(){
        // Get the actual file object
        var file = $(this)[0].files[0];
        if (file) {
            var fileName = file.name;
            var backupID = fileName.split('.').pop().toLowerCase();
            
            // Validate for .sql
            if(jQuery.inArray(backupID, ['sql'])  == -1){
                Swal.fire({
                    title: '<strong class="text-danger">ERROR</strong>',
                    type: 'error',
                    html: '<b>Invalid File. Only .sql files are allowed.</b>',
                    width: '400px',
                    showConfirmButton: true,
                    allowOutsideClick: false,
                });
                $(this).val(''); // Clear the file input
                $("#showValue").text('');
                $("#fileRestore").val(''); // Clear the hidden text input
                return false;
            }

            // Show the file name to the user
            $("#showValue").text(fileName);
            // Put the file name into the hidden text input that gets submitted
            $("#fileRestore").val(fileName); 
        }
    });

    // We must rename the file input for the upload form
    // Find the original file input and give it a new ID
    $('input[name="fileRestore"][type="file"]').attr('name', 'fileRestore_upload').attr('id', 'backup_file_upload');


    // --- ORIGINAL SCRIPT (No changes needed below) ---

    $(document).on('click','#generateBackup',function(){

      $.ajax({
        url: 'backup.php',
        type: 'POST',
        success:function(data){
            // Check if the response contains an error
            if(data.includes("ERROR") || data.includes("Exception")){
                 Swal.fire({
                    title: '<strong class="text-danger">Backup Failed!</strong>',
                    type: 'error', // <-- FIX: 'type' instead of 'icon'
                    html: '<b>The server returned an error:</b><br><pre style="text-align: left; background: #eee; padding: 10px; border-radius: 5px;">' + data + '</pre>',
                    confirmButtonColor: '#d33',
                });
            } else {
                // This is the real success
                Swal.fire({
                  title: '<strong class="text-success">SUCCESS</strong>',
                  type: 'success', // <-- FIX: 'type' instead of 'icon'
                  html: '<b>Generate Backup Successfully</b>',
                  width: '400px',
                  showConfirmButton: false,
                  allowOutsideClick: false,
                  timer: 2000,
                }).then(()=>{
                  $("#backupTable").DataTable().ajax.reload();
                  // --- NEW: Reload the page to update the dropdown ---
                  location.reload(); 
                })
            }
        }
      }).fail(function(xhr, status, error){
          Swal.fire({
            title: '<strong class="text-danger">Ooppss..</strong>',
            type: 'error', // <-- FIX: 'type' instead of 'icon'
            html: '<b>AJAX Request Failed!</b><br>' + error,
            width: '400px',
            confirmButtonColor: '#6610f2',
          })
      })

    })

    // This script is now redundant because of the new one above, but we leave it
    // in case you had other logic. The new script is more specific.
    $("#uploadFile").on('click',function(){
      $("#backup_file_upload").click();
    })


    var backupTable = $("#backupTable").DataTable({
      processing: false,
      serverSide: true,
      searching: false,
      info: false,
      paginate: false,
      ordering: false,
      autoWidth: false,
      scrollY: '200',

      ajax:{ "url": "backupTable.php", "type": "POST" },

      drawCallback: function (settings) {
      $('[data-toggle="tooltip"]').tooltip();
    }


    })


    $(document).on('click','.deleteFile',function(){
    var file_id = $(this).attr('id');
    Swal.fire({
        title: '<strong class="text-danger">ARE YOU SURE?</strong>',
        html: "You want delete this File?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        allowOutsideClick: false,
        confirmButtonText: 'Yes, Delete it!',
        width: '400px',
      }).then((result) => {
        if (result.value) {
          $.ajax({
            url: 'deleteFile.php',
            type: 'POST',
            data: {
              file_id:file_id,
            },
            cache: false,
            success:function(data){
              Swal.fire({
                title: '<strong class="text-success">Success</strong>',
                type: 'success',
                html: '<b>Delete File has Successfully<b>',
                width: '400px',
                showConfirmButton: false,
                allowOutsideClick: false,
                timer: 2000
              }).then(()=>{
                $("#backupTable").DataTable().ajax.reload();
                // --- NEW: Reload the page to update the dropdown ---
                location.reload(); 
              })
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
        }
      })

  })

  })
</script>

</body>
</html>