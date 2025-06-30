<?php
session_start();
require_once 'ppwerk.php'; 


if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to upload a product.";
    $_SESSION['message_type'] = "error";
    header("Location: Trade4US.html"); 
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = trim($_POST['productName']);
    $productDescription = trim($_POST['productDescription']);
    $productPrice = floatval($_POST['productPrice']); 

    $target_dir = "uploads/product_images/"; 

    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($_FILES["productImage"]["name"], PATHINFO_EXTENSION));
    $newFileName = uniqid('product_', true) . '.' . $imageFileType;
    $target_file = $target_dir . $newFileName;

   
    $check = getimagesize($_FILES["productImage"]["tmp_name"]);
    if ($check === false) {
        $_SESSION['message'] = "File is not an image.";
        $_SESSION['message_type'] = "error";
        $uploadOk = 0;
    }

    
    if ($_FILES["productImage"]["size"] > 10000000) { 
        $_SESSION['message'] = "Sorry, your product image file is too large. Max 10MB.";
        $_SESSION['message_type'] = "error";
        $uploadOk = 0;
    }

    
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $_SESSION['message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed for product images.";
        $_SESSION['message_type'] = "error";
        $uploadOk = 0;
    }


    if ($uploadOk == 0) {
        header("Location: dashboard.php");
        exit();
    } else {
        if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $target_file)) {
            
            $imagePath = $target_file; 

            $stmt = $conn->prepare("INSERT INTO Products (UserID, ProductName, Description, Price, ImagePath, SubmissionDate, Status) VALUES (?, ?, ?, ?, ?, NOW(), 'available')");
            $stmt->bind_param("isdss", $userId, $productName, $productDescription, $productPrice, $imagePath);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Product '" . htmlspecialchars($productName) . "' uploaded successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error listing product: " . $stmt->error;
                $_SESSION['message_type'] = "error";
                
                if (file_exists($target_file)) {
                    unlink($target_file);
                }
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Sorry, there was an error uploading your product image.";
            $_SESSION['message_type'] = "error";
        }
    }
} else {
    $_SESSION['message'] = "Invalid request to upload product.";
    $_SESSION['message_type'] = "error";
}

$conn->close();
header("Location: dashboard.php");
exit();
?>