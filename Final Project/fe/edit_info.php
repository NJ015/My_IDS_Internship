<?php
session_start();
include '../be/dbconfig.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../fe/login.php");
    exit();
}

$field = isset($_SESSION['field']) ? $_SESSION['field'] : '';
$label = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['field'])) {
    $field = $_POST['field'];
    $label = '';
    $db_field = '';
    $error_message = '';

    switch ($field) {
        case 'dob':
            $label = 'Date of Birth';
            $db_field = 'DOB';
            break;
        case 'nationality':
            $label = 'Nationality';
            $db_field = 'Nationality';
            break;
        case 'joining_date':
            $label = 'Joining Date';
            $db_field = 'Joining_Date';
            break;
        case 'email':
            $label = 'Email';
            $db_field = 'Email';
            break;
        case 'phone_nb':
            $label = 'Phone Number';
            $db_field = 'Phone_nb';
            break;
        case 'em_nb':
            $label = 'Emergency Number';
            $db_field = 'Emergency_number';
            break;
        case 'profession':
            $label = 'Profession';
            $db_field = 'Profession';
            break;
        case 'password':
            $label = 'Password';
            break;
        default:
            header("Location: ../fe/profile.php");
            exit();
    }
    $_SESSION['field'] = $field;
    if ($field === 'password') {
        if (isset($_POST['old_password'], $_POST['new_password'], $_POST['confirm_password'])) {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Fetch the current password from the database
            $sql = "SELECT Password FROM USERS WHERE ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $stmt->bind_result($current_password);
            $stmt->fetch();
            $stmt->close();

            // Validate old password
            if (!password_verify($old_password, $current_password)) {
                $error_message = "Old password is incorrect.";
            } elseif ($new_password !== $confirm_password) {
                $error_message = "New passwords do not match.";
            } else {
                // Update the password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE USERS SET Password = ? WHERE ID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $hashed_password, $_SESSION['user_id']);

                if ($stmt->execute()) {
                    $_SESSION['success_msg'] = "Password updated successfully.";
                    header("Location: ../fe/profile.php");
                    exit();
                } else {
                    $error_message = "Error updating password.";
                }

                $stmt->close();
                $conn->close();
            }
        } else {
            // $error_message = "All password fields are required.";
        }

        if ($error_message !== '') {
            $_SESSION['error_msg'] = $error_message;
            header("Location: edit_info.php");
            exit();
        }
    } elseif (isset($_POST['new_value'])) {
        $new_value = $_POST['new_value'];

        if ($field === 'dob') {
            if (!DateTime::createFromFormat('Y-m-d', $new_value)) {
                $error_message = "Invalid date format. Use YYYY-MM-DD.";
            }
        } elseif ($field === 'email') {
            if (!filter_var($new_value, FILTER_VALIDATE_EMAIL)) {
                $error_message = "Invalid email format.";
            }
        } elseif ($field === 'phone_nb' || $field === 'em_nb') {
            if (!preg_match('/^\+?(\d)+$/', $new_value)) {
                $error_message = "Phone numbers must contain only numbers.";
            }
        }

        if ($error_message === '') {
            $sql = "UPDATE USERS SET $db_field = ? WHERE ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_value, $_SESSION['user_id']);

            if ($stmt->execute()) {
                $_SESSION[$field] = $new_value;
                $_SESSION['success_msg'] = "$label updated successfully.";
            } else {
                $_SESSION['error_msg'] = "Error updating $label.";
            }

            $stmt->close();
            $conn->close();

            unset($_SESSION['field']);
            
            header("Location: ../fe/profile.php");
            exit();
        } else {
            $_SESSION['error_msg'] = $error_message;
            header("Location: edit_info.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit <?php echo htmlspecialchars($label); ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <b>Edit <?php echo htmlspecialchars($label); ?></b>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error_msg'])) { ?>
                    <div class="alert alert-danger">
                        <?php
                        echo $_SESSION['error_msg'];
                        unset($_SESSION['error_msg']);
                        ?>
                    </div>
                <?php } ?>

                <?php if ($field !== '') { ?>
                    <form action="" method="post">
                        <input type="hidden" name="field" value="<?php echo htmlspecialchars($field); ?>">

                        <?php if ($field === 'password') { ?>
                            <div class="input-group mb-3">
                                <input type="password" name="old_password" class="form-control" placeholder="Old Password" required>
                            </div>
                            <div class="input-group mb-3">
                                <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
                            </div>
                            <div class="input-group mb-3">
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                            </div>
                        <?php } elseif ($field === 'dob') { ?>
                            <div class="input-group mb-3">
                                <input type="date" name="new_value" class="form-control" placeholder="Enter new <?php echo htmlspecialchars($label); ?>" required>
                            </div>
                        <?php } elseif ($field === 'phone_nb' || $field === 'em_nb') { ?>
                            <div class="input-group mb-3">
                                <input type="text" name="new_value" class="form-control" placeholder="Enter new <?php echo htmlspecialchars($label); ?>" required>
                            </div>
                        <?php } else { ?>
                            <div class="input-group mb-3">
                                <input type="text" name="new_value" class="form-control" placeholder="Enter new <?php echo htmlspecialchars($label); ?>" required>
                            </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-6">
                                <a href="../fe/profile.php" class="btn btn-secondary btn-block">Cancel</a>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary btn-block">Update</button>
                            </div>
                        </div>
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>
    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/adminlte.min.js"></script>
</body>

</html>