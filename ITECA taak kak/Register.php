<?php
session_start(); 

require_once 'ppwerk.php'; 


$firstname = $_POST['firstname'] ?? '';
$lastname = $_POST['lastname'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

$errors = [];


if (empty($firstname)) $errors[] = "First name is required.";
if (empty($lastname)) $errors[] = "Last name is required.";
if (empty($username)) $errors[] = "Username is required.";
if (empty($email)) $errors[] = "Email is required.";
if (empty($password)) $errors[] = "Password is required.";
if ($password !== $confirmPassword) $errors[] = "Passwords do not match.";

if (!empty($errors)) {
    $_SESSION['message'] = "Error: " . implode(" ", $errors);
    $_SESSION['message_type'] = "error";
    header("Location: dashboard.php"); 
    exit();
}


$hashedPassword = password_hash($password, PASSWORD_DEFAULT);


$checkSql = "SELECT User_ID FROM Users WHERE Username = ? OR Email = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("ss", $username, $email);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    $_SESSION['message'] = "Error: Username or Email already exists.";
    $_SESSION['message_type'] = "error";
    header("Location: dashboard.php");
    exit();
}
$checkStmt->close();


$sql = "INSERT INTO Users (Username, Password, Email, Firstname, Lastname, RegisterDate) VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    $_SESSION['message'] = "Database prepare failed: " . $conn->error;
    $_SESSION['message_type'] = "error";
    header("Location: dashboard.php");
    exit();
}

$stmt->bind_param("sssss", $username, $hashedPassword, $email, $firstname, $lastname);

if ($stmt->execute()) {
    $_SESSION['message'] = "Registration successful! You can now log in.";
    $_SESSION['message_type'] = "success";
    header("Location: dashboard.php"); 
    exit();
} else {
    $_SESSION['message'] = "Error registering user: " . $stmt->error;
    $_SESSION['message_type'] = "error";
    header("Location: dashboard.php");
    exit();
}

$stmt->close();
$conn->close();
?>