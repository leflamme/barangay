
<?php 

include_once '../connection.php';
session_start();


try{
  if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'resident'){

    $user_id = $_SESSION['user_id'];
    $sql_user = "SELECT * FROM `users` WHERE `id` = ? ";
    $stmt_user = $con->prepare($sql_user) or die ($con->error);
    $stmt_user->bind_param('s',$user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $row_user = $result_user->fetch_assoc();
    $username = $row_user['username'];
    $old_password = $row_user['password'];
    $first_name_user = $row_user['first_name'];
    $middle_name_user = $row_user['middle_name'];
    $last_name_user = $row_user['last_name'];
    $user_type = $row_user['user_type'];
    $user_image = $row_user['image'];


    $sql_resident = "SELECT * FROM residence_information WHERE residence_id = '$user_id'";
    $query_resident = $con->query($sql_resident) or die ($con->error);
    $row_resident = $query_resident->fetch_assoc();
    $contact_number = $row_resident['contact_number'];
    $email = $row_resident['email_address'];


    if($row_resident['image'] != ''){
      $iamge_resident = '<img src="'.$row_resident['image_path'].'" alt="resident Image" id="residentImage">';
    }else{
      $iamge_resident = '<img src="../assets/dist/img/blank_image.png" alt="resident Image" id="residentImage">';
    }



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
        $postal_address = $row['postal_address'];
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
  <title>My Profile</title>

 
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
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
<body class="hold-transition layout-top-nav">

<div class="wrapper">

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
                            if ($user_image != '' || $user_image != null || !empty($user_image)) {
                                echo '<img src="../assets/dist/img"' . $user_image . '" class="img-size-50 mr-3 img-circle" alt="User Image">';
                            } else {
                                echo '<img src="../assets/dist/img/image.png" class="img-size-50 mr-3 img-circle" alt="User   Image">';
                            }
                            ?>
                            <div class="media-body">
                                <h3 class="dropdown-item-title py-3">
                                    <?= ucfirst($first_name_user) . ' ' . ucfirst($last_name_user) ?>
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
        <a href="#" class="brand-link text-center">
            <?php 
            if ($image != '' || $image != null || !empty($image)) {
                echo '<img src="' . $image_path . '" id="logo_image" class="img-circle elevation-5 img-bordered-sm" alt="logo" style="width: 70%;">';
            } else {
                echo '<img src="../assets/logo/ksugan.jpg" id="logo_image" class="img-circle elevation-5 img-bordered-sm" alt="logo" style="width: 70%;">';
            }
            ?>
            <span class="brand-text font-weight-light"></span>
        </a>

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
                        <a href="profile.php" class="nav-link active">
                            <i class="nav-icon fas fa-user"></i>
                            <p>My Profile</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="myInfo.php" class="nav-link">
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
    <!-- /.sidebar -->

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
                                          <!-- ***************************************************************************** -->
                                          <!-- To either fetch the users image from the database or set a default one -->
                                          <!-- ***************************************************************************** -->
                                            <?php if (!empty($user_image)): ?>
                                                <img src="<?= htmlspecialchars($user_image) ?>" id="profileImage" class="profile-image" alt="Profile Image">
                                            <?php else: ?>
                                                <img src="../assets/dist/img/blank_image.png" id="profileImage" class="profile-image" alt="Profile Image">
                                            <?php endif; ?>
                                            <input type="file" id="imageUpload" name="image" class="file-input">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($first_name_user) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Middle Name</label>
                                        <input type="text" class="form-control" name="middle_name" value="<?= htmlspecialchars($middle_name_user) ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($last_name_user) ?>" required>
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


<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.js"></script>
<script src="../assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="../assets/plugins/jquery-validation/additional-methods.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>


<script>
  $(document).ready(function() {
    // Initialize form validation
    $('#profileForm').validate({
        rules: {
            first_name: { required: true, minlength: 2 },
            last_name: { required: true, minlength: 2 },
            contact_number: { required: true, minlength: 10 },
            email: { required: true, email: true }
        },
        messages: {
            first_name: { 
                required: "First name is required",
                minlength: "First name must be at least 2 characters long"
            },
            last_name: {
                required: "Last name is required",
                minlength: "Last name must be at least 2 characters long"
            },
            contact_number: {
                required: "Contact number is required",
                minlength: "Contact number must be at least 10 digits long"
            },
            email: {
                required: "Email is required",
                email: "Please enter a valid email address"
            }
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        },
        submitHandler: function(form) {
            Swal.fire({
                title: 'Updating Profile',
                html: 'Please wait while we update your information...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            $.ajax({
                url: 'changeProfile.php',
                type: 'POST',
                data: new FormData(form),
                processData: false,
                contentType: false,
                success: function(response) {
                  const result = JSON.parse(response);
                  
                  Swal.fire({
                      title: `<strong class="text-${result.status}">${result.status.toUpperCase()}</strong>`,
                      icon: result.status,
                      html: `<b>${result.message}</b>`,
                      confirmButtonColor: '#6610f2'
                  }).then(() => {
                      if (result.status === 'success') {
                          window.location.reload();
                      }
                  });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: '<strong class="text-danger">Error</strong>',
                        icon: 'error',
                        html: '<b>Failed to update profile: ' + error + '</b>',
                        confirmButtonColor: '#6610f2',
                    });
                }
            });
        }
    });
});



  // Image upload preview (optional)
  $('#imageUpload').change(function() {
    if (this.files && this.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#profileImage').attr('src', e.target.result);
      };
      reader.readAsDataURL(this.files[0]);
    }
  });


</script>

</body>
</html>
