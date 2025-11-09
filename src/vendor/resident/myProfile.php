<?php 
include_once '../connection.php';
session_start();

try {
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'resident') {

        $user_id = $_SESSION['user_id'];
        
        // Get user account info
        $sql_user = "SELECT * FROM `users` WHERE `id` = ?";
        $stmt_user = $con->prepare($sql_user) or die($con->error);
        $stmt_user->bind_param('s', $user_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        $row_user = $result_user->fetch_assoc();
        
        // Get resident information
        $sql_resident = "SELECT * FROM `residence_information` WHERE `residence_id` = ?";
        $stmt_resident = $con->prepare($sql_resident) or die($con->error);
        $stmt_resident->bind_param('s', $user_id);
        $stmt_resident->execute();
        $result_resident = $stmt_resident->get_result();
        $row_resident = $result_resident->fetch_assoc();
        
        // Get barangay info
        $sql_barangay = "SELECT * FROM `barangay_information` LIMIT 1";
        $stmt_barangay = $con->prepare($sql_barangay) or die($con->error);
        $stmt_barangay->execute();
        $result_barangay = $stmt_barangay->get_result();
        $row_barangay = $result_barangay->fetch_assoc();
        
        // Set variables
        $username = $row_user['username'];
        $first_name = $row_resident['first_name'];
        $middle_name = $row_resident['middle_name'];
        $last_name = $row_resident['last_name'];
        $contact_number = $row_resident['contact_number'];
        $email = $row_resident['email_address'];
        $image = $row_resident['image_path'] ?: '../assets/dist/img/image.png';
        $barangay = $row_barangay['barangay'];
        $zone = $row_barangay['zone'];
        $district = $row_barangay['district'];

    } else {
        header("Location: ../login.php");
        exit();
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
    <title>My Profile</title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
    <style>
        .profile-image-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }
        .profile-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            cursor: pointer;
        }
        .file-input {
            display: none;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-footer-fixed">
<div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__wobble" src="../assets/dist/img/loader.gif" alt="AdminLTELogo" height="70" width="70">
    </div>

    <!-- Navbar -->
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

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="#" class="dropdown-item">
                        <div class="media">
                            <?php 
                            if ($row_resident['image_path'] != '' || $row_resident['image_path'] != null || !empty($row_resident['image_path'])) {
                                echo '<img src="' . $row_resident['image_path'] . '" class="img-size-50 mr-3 img-circle" alt="User   Image">';
                            } else {
                                echo '<img src="../assets/dist/img/blank_image.png" class="img-size-50 mr-3 img-circle" alt="User   Image">';
                            }
                            ?>
                            <div class="media-body">
                                <h3 class="dropdown-item-title py-3">
                                    <?= ucfirst($row_resident['first_name']) . ' ' . ucfirst($row_resident['last_name']) ?>
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

    <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
        <!-- Barangay Logo-->
        <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">

        <!-- Sidebar -->
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="myProfile.php" class="nav-link active">
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

                    <li class="nav-item">
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
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Certificate</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">My Profile</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 mx-auto">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Personal Information</h3>
                            </div>
                            <form id="profileForm" method="post" enctype="multipart/form-data">
                                <div class="card-body">
                                    <div class="text-center">
                                        <div class="profile-image-container">
                                            <img src="<?= $image ?>" id="profileImage" class="profile-image" alt="Profile Image">
                                            <input type="file" id="imageUpload" name="image" class="file-input">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Middle Name</label>
                                        <input type="text" class="form-control" name="middle_name" value="<?= htmlspecialchars($middle_name) ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Contact Number</label>
                                        <input type="text" class="form-control" name="contact_number" value="<?= htmlspecialchars($contact_number) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($username) ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')) ?></strong>
    </footer>
</div>

<!-- REQUIRED SCRIPTS -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>
<script src="../assets/dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
    // Profile image click handler
    $('#profileImage').click(function() {
        $('#imageUpload').click();
    });
    
    // Display selected image
    $('#imageUpload').change(function(e) {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#profileImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Form submission
    $('#profileForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: 'updateProfile.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Profile updated successfully',
                    timer: 2000
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'There was a problem updating your profile'
                });
            }
        });
    });
});
</script>
</body>
</html>
