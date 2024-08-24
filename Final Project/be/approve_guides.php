<?php
session_start();
require_once 'dbconfig.php'; // Adjust the path to your config file

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Update the guide's status to 'Accepted' (0 or 'Accepted' based on your design)
    $sql = "UPDATE GUIDE SET Status = 'Accepted' WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = 'Guide successfully approved.';
    } else {
        $_SESSION['error_msg'] = 'Error approving guide.';
    }

    $stmt->close();
    $conn->close();

    header('Location: ../fe/profile.php');
    exit;
} else {
    $_SESSION['error_msg'] = 'Invalid request.';
    header('Location: ../fe/profile.php');
    exit;
}
?>
