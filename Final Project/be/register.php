<?php
session_start();
//add l validation lal inputs important!!!!!!!!!!!!
//fix the date-diff/age stuff important!!!!!!!!!!!!

require_once 'dbconfig.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $gender = $_POST['gender'];

    $genderID = ($gender == 'M') ? 1 : ($gender == 'F' ? 2 : null);

    if ($genderID === null) {
        die("Invalid gender");
    }

    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];

    $checkEmailSql = "SELECT * FROM USERS WHERE Email = ?";
    $stmt = $conn->prepare($checkEmailSql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['e_error'] = "This email is already registered.";
        setvalues();
        header("Location: ../fe/register.php");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['e_error'] = "Invalid email format";
        setvalues();
        header("Location: ../fe/register.php");
        exit();
    }
    

    if ($_POST["password"] !== $_POST["confirmPassword"]) {
        $_SESSION['cpass_error'] = "Passwords do not match";
        setvalues();
        header("Location:../fe/register.php");
        exit();
    }

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dob = $_POST['dob'];
    $joining_date = date('Y-m-d');
    $profession = isset($_POST['profession']) ? $_POST['profession'] : null;
    $photo = "https://robohash.org/" . urlencode($email);
    
    $emergency_number = isset($_POST['emergency_number']) ? $_POST['emergency_number'] : null;
    $phone_nb = $_POST['phone_nb'];
    if (!preg_match('/^\+?(\d)+$/', $phone_nb)) {
        $error_message = "Phone numbers must contain only numbers or (+) sign.";
        $_SESSION['phone_error'] = "Invalid phone number";
        setvalues();
        header("Location: ../fe/register.php");
        exit();
    }
    if (!preg_match('/^\+?(\d)+$/', $emergency_number)) {
        $error_message = "Phone numbers must contain only numbers or (+) sign.";
        $_SESSION['em_nb_error'] = "Invalid phone number";
        setvalues();
        header("Location: ../fe/register.php");
        exit();
    }

    $nationality = isset($_POST['nationality']) ? $_POST['nationality'] : null;



    $dobDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($dobDate)->y;

    $dobFormatted = $dobDate->format('Y-m-d');

    $query = "INSERT INTO USERS (Role, GenderID, FirstName, MiddleName, LastName, Email, Password, DOB, Age, Joining_Date, Profession, Photo, Emergency_number, Phone_nb, Nationality) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sissssssissssss", $role, $genderID, $firstName, $middleName, $lastName, $email, $password, $dobFormatted, $age, $joining_date, $profession, $photo, $emergency_number, $phone_nb, $nationality);

    if ($stmt->execute()) {
        $userID = $conn->insert_id;

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
            header("Location:../fe/login.php");
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}



function setvalues()
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

