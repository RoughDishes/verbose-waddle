<?php
session_start(); 

require_once 'ppwerk.php';


$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';


$errors = [];
if (empty($username)) {
    $errors[] = "Username is required.";
}
if (empty($password)) {
    $errors[] = "Password is required.";
}


if (!empty($errors)) {
    $_SESSION['message'] = "Error: " . implode(" ", $errors);
    $_SESSION['message_type'] = "error";
    header("Location: dashboard.php"); 
    exit();
}


$sql = "SELECT User_ID, Username, Password, Firstname, Lastname FROM Users WHERE Username = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    $_SESSION['message'] = "Database prepare failed: " . $conn->error;
    $_SESSION['message_type'] = "error";
    header("Location: dashboard.php");
    exit();
}


$stmt->bind_param("s", $username); 
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if ($password === $user['Password']) { 
       
        $_SESSION['user_id'] = $user['User_ID'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['firstname'] = $user['Firstname'];
        $_SESSION['lastname'] = $user['Lastname'];

      

        $_SESSION['message'] = "Login successful! Welcome!";
        $_SESSION['message_type'] = "success";
        
        
        header("Location: dashboard.php");
        exit();
    } else {
        
        $_SESSION['message'] = "Error: Invalid username or password.";
        $_SESSION['message_type'] = "error";
        header("Location: dashboard.php");
        exit();
    }
} else {
    
    $_SESSION['message'] = "Error: Invalid username or password.";
    $_SESSION['message_type'] = "error";
    header("Location: dashboard.php");
    exit();
}

$stmt->close();
$conn->close();
?>