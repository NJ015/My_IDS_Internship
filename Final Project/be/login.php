<?php
session_start();
require_once('dbconfig.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["email_error"] = "Invalid email format.";
        header("Location: ../fe/login.php");
        exit();
    }

    $sql = "SELECT ID, Password, Role FROM USERS WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['Password'])) {
            // session_start();
            $_SESSION['user_id'] = $row['ID'];
            $_SESSION['role'] = $row['Role'];
            header("Location: ../fe/profile.php");
            exit();
        } else {
            $_SESSION["pass"] = "Incorrect password";
            header("Location: ../fe/login.php");
            exit();
        }
    } else {
        $_SESSION["email_error"] = "No user found with this email.";
        header("Location: ../fe/login.php");
        exit();
    }
    
    $stmt->close();
}

$conn->close();
