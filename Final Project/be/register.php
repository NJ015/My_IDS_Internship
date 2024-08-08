<?php

//add l validation lal inputs important!!!!!!!!!!!!

require_once 'dbconfig.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $gender = $_POST['gender'];
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dob = $_POST['dob'];
    $joining_date = date('Y-m-d');
    $profession = isset($_POST['profession']) ? $_POST['profession'] : null;
$photo = isset($_POST['photo']) && !empty($_POST['photo']) ? $_POST['photo'] : "https://robohash.org/" . urlencode($email);
    $emergency_number = isset($_POST['emergency_number']) ? $_POST['emergency_number'] : null;
    $nationality = isset($_POST['nationality']) ? $_POST['nationality'] : null;

    $genderID = ($gender == 'M') ? 1 : ($gender == 'F' ? 2 : null);

    if ($genderID === null) {
        die("Invalid gender");
    }

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
            header("Location:../fe/login.php");
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
