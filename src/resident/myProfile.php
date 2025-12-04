<?php 
session_start();
include_once '../connection.php';

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
        $house_number = $row_resident['house_number'] ?? ''; // <-- Added Part
        $street = $row_resident['street'] ?? '';
        $barangay_name = $row_resident['barangay'] ?? '';
        $municipality = $row_resident['municipality'] ?? '';
        $full_address = $house_number . ', ' . $street . ', ' . $barangay_name . ', ' . $municipality;

        $contact_number = $row_resident['contact_number'];
        $email = $row_resident['email_address'];
        $image = $row_resident['image_path'] ?: '../assets/dist/img/image.png'; // <-- Added Part
        $barangay = $row_barangay['barangay'] ?? '';
        $zone = $row_barangay['zone'] ?? '';
        $district = $row_barangay['district'] ?? '';
        
        // --- FIXED IMAGE PATH ---
        $image_to_display = '../assets/dist/img/image.png'; // Default
        if (!empty($row_user['image'])) {
             // Use the filename from 'users' table and build the permanent path
            $image_to_display = '../permanent-data/images/' . htmlspecialchars($row_user['image']);
        }
        // --- END FIX ---
        
        $barangay = $row_barangay['barangay'];
        $zone = $row_barangay['zone'];
        $district = $row_barangay['district'];

        // --- NEW LOGIC TO CHECK EDIT STATUS ---
        $edit_status = 'LOCKED'; // Default state
        $is_editable = false;
        
        $sql_check_request = "SELECT * FROM `edit_requests` WHERE `user_id` = ? AND `status` IN ('PENDING', 'APPROVED') ORDER BY `request_date` DESC LIMIT 1";
        $stmt_check = $con->prepare($sql_check_request);
        $stmt_check->bind_param('s', $user_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if($result_check->num_rows > 0) {
            $request = $result_check->fetch_assoc();
            if ($request['status'] == 'APPROVED') {
                $edit_status = 'APPROVED';
                $is_editable = true;
            } else {
                $edit_status = 'PENDING';
            }
        }
        // We use this to set readonly/disabled in the HTML
        $edit_attr = $is_editable ? '' : 'readonly';
        // --- END NEW LOGIC ---

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
    <!-- Website Logo -->
    <link rel="icon" type="image/png" href="../assets/logo/ksugan.jpg">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
    <link rel="stylesheet" href="../assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
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
        /* Style for readonly inputs */
        .form-control[readonly] {
            background-color: #e9ecef !important;
            opacity: 1;
        }
        .card-primary .card-header {
            background-color: #050C9C;
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
            <li class="nav-item"><h5><a class="nav-link text-white" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></h5></li>
            <li class="nav-item d-none d-sm-inline-block" style="font-variant: small-caps;"><h5 class="nav-link text-white"><?= $barangay ?></h5>
            <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
            <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= htmlspecialchars($zone) ?></h5></li>
            <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white">-</h5></li>
            <li class="nav-item d-none d-sm-inline-block"><h5 class="nav-link text-white"><?= htmlspecialchars($district) ?></h5></li>
        </ul>

        <!-- User Account Menu / Right Navbar Links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-user"></i></a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="myProfile.php" class="dropdown-item">
                        <div class="media">
                            <img src="<?= htmlspecialchars($image_to_display) ?>" class="img-size-50 mr-3 img-circle" alt="User Image">
                            <div class="media-body">
                                <h3 class="dropdown-item-title py-3"><?= htmlspecialchars(ucfirst($first_name) . ' ' . ucfirst($last_name)) ?></h3>
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
        <!-- Barangay Logo -->
        <img src="../assets/logo/ksugan.jpg" alt="Barangay Kalusugan Logo" id="logo_image" class="img-circle elevation-5 img-bordered-sm" style="width: 70%; margin: 10px auto; display: block;">

        <!-- Sidebar -->
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
                  <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
                  <li class="nav-item"><a href="personalInformation.php" class="nav-link"><i class="nav-icon fas fa-address-book"></i><p>Personal Information</p></a></li>
                  <li class="nav-item"><a href="drrmPlan.php" class="nav-link"><i class="fas fa-clipboard-list nav-icon text-red"></i><p>Emergency Plan</p></a></li>
                  <li class="nav-item"><a href="myRecord.php" class="nav-link"><i class="nav-icon fas fa-server"></i><p>Blotter Record</p></a></li>
                  <li class="nav-item"><a href="certificate.php" class="nav-link"><i class="nav-icon fas fa-file-alt"></i><p>Certificate</p></a></li>
                  <li class="nav-item"><a href="changePassword.php" class="nav-link"><i class="nav-icon fas fa-lock"></i><p>Change Password</p></a></li>       
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
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
                                <input type="text" class="form-control" name="first_name" 
                                    value="<?= htmlspecialchars($first_name) ?>" 
                                    <?= !empty($first_name) ? 'readonly' : '' ?> required>
                            </div>
                            
                            <div class="form-group">
                                <label>Middle Name</label>
                                <input type="text" class="form-control" name="middle_name" 
                                    value="<?= htmlspecialchars($middle_name) ?>" 
                                    <?= !empty($middle_name) ? 'readonly' : '' ?>>
                            </div>
                            
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="last_name" 
                                    value="<?= htmlspecialchars($last_name) ?>" 
                                    <?= !empty($last_name) ? 'readonly' : '' ?> required>
                            </div>

                            <div class="form-group">
                                <label>Full Address</label>
                                <input type="text" class="form-control" name="address" 
                                    value="<?= htmlspecialchars($full_address ?? '') ?>" 
                                    <?= !empty($full_address) ? 'readonly' : '' ?> required>
                            </div>

                            
                            <div class="form-group">
                                <label>Contact Number</label>
                                <input type="text" class="form-control" name="contact_number" 
                                    value="<?= htmlspecialchars($contact_number) ?>" 
                                    <?= !empty($contact_number) ? 'readonly' : '' ?> required>
                            </div>
                            
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" 
                                    value="<?= htmlspecialchars($email) ?>" 
                                    <?= !empty($email) ? 'readonly' : '' ?>>
                            </div>
                            
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" class="form-control" 
                                    value="<?= htmlspecialchars($username) ?>" readonly>
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

    <!--
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Confirm Update</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            I hereby declare that all information given is true and up to date.
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            <button type="button" class="btn btn-success" id="confirmUpdate">Yes</button>
            </div>
        </div>
        </div>
    </div>
    -->

    <footer class="main-footer">
        <strong>Copyright &copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')) ?></strong>
    </footer>
</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>
<script src="../assets/dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {

    // --- NEW: REQUEST EDIT ACCESS BUTTON ---
    $('#requestEditButton').on('click', function() {
        $.ajax({
            url: 'requestEditAccess.php', // This is the new file from our previous step
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        title: 'Request Submitted!',
                        text: 'Your request to edit has been sent to the admin. You will be notified upon approval.',
                        type: 'success', // Use 'type' for SweetAlert2
                        confirmButtonColor: '#050C9C'
                    }).then(() => {
                        window.location.reload(); // Reload to show the "Pending" button
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Could not submit request.',
                        type: 'error',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'AJAX Error',
                    text: 'Something went wrong with the request.',
                    type: 'error',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });

    // Profile image click handler
    $('#profileImage').click(function() {
        // Only trigger click if the form is editable
        if(<?= $is_editable ? 'true' : 'false' ?>) {
            $('#imageUpload').click();
        }
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
        
        // Show the confirmation modal first
        $('#confirmationModal').modal('show');

        // Handle the 'Yes' button click
        $('#confirmUpdate').off('click').on('click', function() {
            $('#confirmationModal').modal('hide');
            
            var formData = new FormData($('#profileForm')[0]);
        
            $.ajax({
                url: 'updateProfile.php', // The new backend script
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.trim() === 'success') {
                        Swal.fire({
                            type: 'success',
                            title: 'Success!',
                            text: 'Profile updated successfully',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                         Swal.fire({
                            type: 'error',
                            title: 'Error!',
                            text: response || 'There was a problem updating your profile'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        type: 'error',
                        title: 'Error!',
                        text: 'There was a problem updating your profile'
                    });
                }
            });
        });
    });
});
</script>
</body>
</html>