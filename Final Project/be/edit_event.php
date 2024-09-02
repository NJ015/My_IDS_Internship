<?php
session_start();
require_once 'dbconfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../fe/login.php");
    exit();
}

$field = isset($_SESSION['field']) ? $_SESSION['field'] : '';
$label = '';
$eventID = isset($_SESSION['id']) ? $_SESSION['id'] : null;

$_SESSION['event_id'] = $eventID;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['field']) && isset($_POST['id'])) {
    $field = $_POST['field'];
    $eventID = $_POST['id'];
    $label = '';
    $db_field = '';
    $error_message = '';

    switch ($field) {
        case 'Name':
            $label = 'Event Name';
            $db_field = 'Name';
            break;
        case 'Category':
            $label = 'Event Category';
            $db_field = 'Category';
            break;
        case 'Date_from':
            $label = 'Event Start Date';
            $db_field = 'Date_from';
            break;
        case 'Date_to':
            $label = 'Event End Date';
            $db_field = 'Date_to';
            break;
        case 'Time_from':
            $label = 'Event Start Time';
            $db_field = 'Time_from';
            break;
        case 'Time_to':
            $label = 'Event End Time';
            $db_field = 'Time_to';
            break;
        case 'Destination':
            $label = 'Event Location';
            $db_field = 'Destination';
            break;
        case 'Description':
            $label = 'Event Description';
            $db_field = 'Description';
            break;
        case 'Status':
            $label = 'Event Status';
            $db_field = 'Status';
            break;
        case 'Cost':
            $label = 'Event Cost';
            $db_field = 'Cost';
            break;
        default:
            $_SESSION['error_msg'] = "Error updating.";
            header("Location: view_event.php");
            exit();
    }
    $_SESSION['field'] = $field;
    $_SESSION['id'] = $eventID;

    if (isset($_POST['new_value'])) {
        $new_value = $_POST['new_value'];

        if ($field === 'Cost' && !is_numeric($new_value)) {
            $error_message = "Invalid cost format. Please enter a numeric value.";
        }

        if ($error_message === '') {
            $sql = "UPDATE EVENTS SET $db_field = ? WHERE ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_value, $eventID);

            if ($stmt->execute()) {
                $_SESSION['success_msg'] = "$label updated successfully.";
            } else {
                $_SESSION['error_msg'] = "Error updating $label.";
            }

            $stmt->close();
            $conn->close();

            unset($_SESSION['field']);
            header("Location: view_event.php");
            exit();
        } else {
            $_SESSION['error_msg'] = $error_message;
            header("Location: edit_event.php");
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
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
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
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($eventID); ?>">

                        <?php if ($field === 'Date_from') { ?>
                            <div class="input-group mb-3">
                                <input type="date" name="new_value" class="form-control" placeholder="Start Date" required>
                            </div>
                        <?php } elseif ($field === 'Date_to') { ?>
                            <div class="input-group mb-3">
                                <input type="date" name="new_value" class="form-control" placeholder="End Date" required>
                            </div>
                        <?php } elseif ($field === 'Time_from') { ?>
                            <div class="input-group mb-3">
                                <input type="time" name="new_value" class="form-control" placeholder="Start Time" required>
                            </div>
                        <?php } elseif ($field === 'Time_to') { ?>
                            <div class="input-group mb-3">
                                <input type="time" name="new_value" class="form-control" placeholder="End Time" required>
                            </div>
                        <?php } else { ?>
                            <div class="input-group mb-3">
                                <input type="text" name="new_value" class="form-control" placeholder="Enter new <?php echo htmlspecialchars($label); ?>" required>
                            </div>
                        <?php } ?>


                        <div class="row">
                            <div class="col-8">
                                <a href="view_event.php" class="btn btn-secondary btn-block">Cancel</a>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary btn-block">Save</button>
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
