<?php
include_once '../connection.php';
session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'resident') {
        $user_id = $_SESSION['user_id'];
        
        // Handle file upload
        $image_path = null;
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "../assets/dist/img/residents/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = "resident_".$user_id."_".time().".".$file_ext;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            }
        }
        
        // Update residence information
        $stmt = $con->prepare("UPDATE residence_information SET 
            first_name = ?,
            middle_name = ?,
            last_name = ?,
            contact_number = ?,
            email_address = ?" . 
            ($image_path ? ", image_path = ?" : "") . "
            WHERE residence_id = ?");
        
        if ($image_path) {
            $stmt->bind_param("sssssss", 
                $_POST['first_name'],
                $_POST['middle_name'],
                $_POST['last_name'],
                $_POST['contact_number'],
                $_POST['email'],
                $image_path,
                $user_id);
        } else {
            $stmt->bind_param("ssssss", 
                $_POST['first_name'],
                $_POST['middle_name'],
                $_POST['last_name'],
                $_POST['contact_number'],
                $_POST['email'],
                $user_id);
        }
        
        $stmt->execute();
        
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    }
} catch(Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
