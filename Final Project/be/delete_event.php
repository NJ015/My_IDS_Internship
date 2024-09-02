<?php
session_start();
require_once 'dbconfig.php'; // Adjust the path to your config file

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../fe/login.php");
    exit();
}

if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes' && isset($_POST['id'])) {
    $event_id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM EVENTS WHERE ID = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success_msg'] = "Event deleted successfully.";
    } else {
        $_SESSION['error_msg'] = "Error deleting event.";
    }

    $stmt->close();
    $conn->close();

    header('Location: ../be/manage_events.php'); // Redirect to your manage events page
    exit();
} elseif (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'no') {
    header('Location: ../be/manage_events.php'); // Redirect to your manage events page
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Confirm Deletion</title>
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../fe/css/del_confirm.css">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <b>Are you sure you want to delete this event?</b>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($_POST['id']); ?>">
                    <div class="delb">
                        <div class="row">
                            <div class="col-4">
                                <button type="submit" name="confirm_delete" value="no" class="btn btn-primary btn-block">No</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <button type="submit" name="confirm_delete" value="yes" class="btn btn-primary btn-block">Yes</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../assets/js/adminlte.min.js"></script>
</body>

</html>
