<?php
include_once '../connection.php';

// Manage Dashboard
// Check connection
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

/* DELETE IMAGE */
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($con, "DELETE FROM dashboard_images WHERE id='$id'");
    echo "<script>window.location.href='?';</script>";
    exit;
}

/* UPDATE IMAGE */
if (isset($_POST['update_id']) && isset($_FILES['update_image'])) {
    $id = intval($_POST['update_id']);
    $imageData = addslashes(file_get_contents($_FILES['update_image']['tmp_name']));
    mysqli_query($con, "UPDATE dashboard_images SET image='$imageData' WHERE id='$id'");
    echo "<script>window.location.href='?';</script>";
    exit;
}

/* UPLOAD NEW IMAGE */
if (isset($_POST['upload']) && isset($_FILES['dashboard_image'])) {
    $imageData = addslashes(file_get_contents($_FILES['dashboard_image']['tmp_name']));
    mysqli_query($con, "INSERT INTO dashboard_images (image) VALUES ('$imageData')");
    echo "<script>window.location.href='?';</script>";
    exit;
}

/* FETCH ALL IMAGES */
$imagesRes = mysqli_query($con, "SELECT id, image FROM dashboard_images ORDER BY id DESC");
?>

<!-- ================= MODAL CONTENT ================= -->

<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Manage Resident Dashboard Images</h5>
    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
</div>

<div class="modal-body">

    <!-- Upload New Image -->
    <h6>Upload New Image</h6>
    <form method="POST" enctype="multipart/form-data" class="mb-3">
        <input type="file" name="dashboard_image" required class="form-control mb-2">
        <button type="submit" name="upload" class="btn btn-success btn-sm">Upload</button>
    </form>

    <hr>

    <!-- Existing Images -->
    <h6>Existing Images</h6>
    <div class="row">

    <?php while ($row = mysqli_fetch_assoc($imagesRes)): ?>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <img src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>"
                     class="card-img-top" style="height:150px; object-fit:cover; border-radius:5px">

                <div class="card-body text-center">

                    <!-- Update Form -->
                    <form method="POST" enctype="multipart/form-data" class="mb-1">
                        <input type="hidden" name="update_id" value="<?= $row['id'] ?>">
                        <input type="file" name="update_image" required class="form-control mb-2">
                        <button type="submit" class="btn btn-warning btn-sm w-100">Update</button>
                    </form>

                    <!-- Delete -->
                    <a href="?delete_id=<?= $row['id'] ?>" 
                       class="btn btn-danger btn-sm w-100"
                       onclick="return confirm('Delete this image?')">Delete</a>

                </div>
            </div>
        </div>
    <?php endwhile; ?>

    </div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>

<style>
    .modal-body h6 { font-weight: 600; }
</style>
