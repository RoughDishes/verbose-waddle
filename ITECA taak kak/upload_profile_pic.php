<?php
session_start();
require_once 'ppwerk.php'; 


if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to upload a profile picture.";
    $_SESSION['message_type'] = "error";
    header("Location: Trade4US.html"); 
    exit();
}

$userId = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT ProfilePicture FROM Users WHERE User_ID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($currentProfilePic);
$stmt->fetch();
$stmt->close();

if (!empty($currentProfilePic) && $currentProfilePic != 'loginavatar.png') {
    $_SESSION['message'] = "You already have a profile picture set. It cannot be changed.";
    $_SESSION['message_type'] = "error";
    $conn->close();
    header("Location: dashboard.php");
    exit();
}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profilePic"])) {
    $target_dir = "uploads/profile_pics/"; 
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); 
    }

    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($_FILES["profilePic"]["name"], PATHINFO_EXTENSION));
    $newFileName = uniqid('profile_', true) . '.' . $imageFileType; 
    $target_file = $target_dir . $newFileName;

    
    $check = getimagesize($_FILES["profilePic"]["tmp_name"]);
    if ($check !== false) {
        
    } else {
        $_SESSION['message'] = "File is not an image.";
        $_SESSION['message_type'] = "error";
        $uploadOk = 0;
    }

    
    if ($_FILES["profilePic"]["size"] > 5000000) { 
        $_SESSION['message'] = "Sorry, your file is too large. Max 5MB.";
        $_SESSION['message_type'] = "error";
        $uploadOk = 0;
    }

    
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $_SESSION['message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $_SESSION['message_type'] = "error";
        $uploadOk = 0;
    }

    
    if ($uploadOk == 0) {
        header("Location: dashboard.php"); 
        exit();
    } else {
        
        if (move_uploaded_file($_FILES["profilePic"]["tmp_name"], $target_file)) {
            
            

           
            $newProfilePicPath = $target_file; 
            $stmt = $conn->prepare("UPDATE Users SET ProfilePicture = ? WHERE User_ID = ?");
            $stmt->bind_param("si", $newProfilePicPath, $userId);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Profile picture added successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error updating database: " . $stmt->error;
                $_SESSION['message_type'] = "error";
                
                if (file_exists($target_file)) {
                    unlink($target_file);
                }
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Sorry, there was an error uploading your file.";
            $_SESSION['message_type'] = "error";
        }
    }
} else {
    $_SESSION['message'] = "No file selected or invalid request method.";
    $_SESSION['message_type'] = "error";
}

$conn->close();
header("Location: dashboard.php"); 
exit();
?>