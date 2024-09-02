<?php
session_start();
require_once 'dbconfig.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $gender = $_POST['gender'];

    // Map gender to ID
    $genderID = ($gender == 'M') ? 1 : ($gender == 'F' ? 2 : null);

    if ($genderID === null) {
        die("Invalid gender");
    }

    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];

    // Check if email is already registered
    $checkEmailSql = "SELECT * FROM USERS WHERE Email = ?";
    $stmt = $conn->prepare($checkEmailSql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['e_error'] = "This email is already registered.";
        setValues();
        header("Location: ../fe/register.php");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['e_error'] = "Invalid email format";
        setValues();
        header("Location: ../fe/register.php");
        exit();
    }

    // Check if passwords match
    if ($_POST["password"] !== $_POST["confirmPassword"]) {
        $_SESSION['cpass_error'] = "Passwords do not match";
        setValues();
        header("Location: ../fe/register.php");
        exit();
    }

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dob = $_POST['dob'];
    $joining_date = date('Y-m-d');
    $profession = isset($_POST['profession']) ? $_POST['profession'] : null;

    // Handle photo upload
    $target_dir = "../assets/images/";
    $photo = $target_dir . basename($_FILES["photo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($photo, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $_SESSION['error_msg'] = "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($photo)) {
        $_SESSION['error_msg'] = "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size (limit to 5MB)
    if ($_FILES["photo"]["size"] > 5000000) {
        $_SESSION['error_msg'] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $_SESSION['error_msg'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // If file upload failed
    if ($uploadOk == 0) {
        setValues();
        header("Location: ../fe/register.php");
        exit();
    } else {
        // If everything is ok, try to upload file
        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $photo)) {
            $_SESSION['error_msg'] = "Sorry, there was an error uploading your file.";
            setValues();
            header("Location: ../fe/register.php");
            exit();
        }
    }

    $emergency_number = isset($_POST['emergency_number']) ? $_POST['emergency_number'] : null;
    $phone_nb = $_POST['phone_nb'];

    // Validate phone numbers
    if (!preg_match('/^\+?(\d)+$/', $phone_nb)) {
        $_SESSION['phone_error'] = "Invalid phone number";
        setValues();
        header("Location: ../fe/register.php");
        exit();
    }
    if (!preg_match('/^\+?(\d)+$/', $emergency_number)) {
        $_SESSION['em_nb_error'] = "Invalid emergency number";
        setValues();
        header("Location: ../fe/register.php");
        exit();
    }

    $nationality = isset($_POST['nationality']) ? $_POST['nationality'] : null;

    // Calculate age
    $dobDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($dobDate)->y;

    $dobFormatted = $dobDate->format('Y-m-d');

    // Insert data into USERS table
    $query = "INSERT INTO USERS (Role, GenderID, FirstName, MiddleName, LastName, Email, Password, DOB, Age, Joining_Date, Profession, Photo, Emergency_number, Phone_nb, Nationality) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sissssssissssss", $role, $genderID, $firstName, $middleName, $lastName, $email, $password, $dobFormatted, $age, $joining_date, $profession, $photo, $emergency_number, $phone_nb, $nationality);

    if ($stmt->execute()) {
        $userID = $conn->insert_id;

        // Insert into respective role table
        if ($role == 'Admin') {
            $query = "INSERT INTO ADMIN (UserID) VALUES (?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $userID);
        } elseif ($role == 'Member') {
            $query = "INSERT INTO MEMBER (UserID, Joined_events) VALUES (?, ?)";
            $joined_events = null;
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $userID, $joined_events);
        } elseif ($role == 'Guide') {
            $query = "INSERT INTO GUIDE (UserID, Responsible_events) VALUES (?, ?)";
            $responsible_events = null;
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $userID, $responsible_events);
        } else {
            die("Invalid role");
        }

        if ($stmt->execute()) {
            echo "Registration successful!";
            unsetValues();
            header("Location: ../fe/login.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Functions to set and unset form values
function setValues()
{
    $_SESSION["role1"] = $_POST['role'];
    $_SESSION["gender1"] = $_POST['gender'];
    $_SESSION["firstName1"] = $_POST['firstName'];
    $_SESSION["middleName1"] = $_POST['middleName'];
    $_SESSION["lastName1"] = $_POST['lastName'];
    $_SESSION["email1"] = $_POST['email'];
    $_SESSION["dob1"] = $_POST['dob'];
    $_SESSION["profession1"] = $_POST['profession'];
    $_SESSION["photo1"] = $_POST['photo'];
    $_SESSION["emergency_number1"] = $_POST['emergency_number'];
    $_SESSION["phone_nb1"] = $_POST['phone_nb'];
    $_SESSION["nationality1"] = $_POST['nationality'];
}

function unsetValues()
{
    unset($_SESSION["role1"]);
    unset($_SESSION["gender1"]);
    unset($_SESSION["firstName1"]);
    unset($_SESSION["middleName1"]);
    unset($_SESSION["lastName1"]);
    unset($_SESSION["email1"]);
    unset($_SESSION["dob1"]);
    unset($_SESSION["profession1"]);
    unset($_SESSION["photo1"]);
    unset($_SESSION["emergency_number1"]);
    unset($_SESSION["phone_nb1"]);
    unset($_SESSION["nationality1"]);
}
