<?php
session_start();
require_once 'dbconfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../fe/login.php");
    exit();
}
if (isset($_SESSION['event_id'])) {
    $eventID = $_SESSION['event_id'];
} elseif (isset($_POST['id'])) {
    $eventID = $_POST['id'];
}
if (isset($eventID)) {
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM EVENTS WHERE ID = ?");
    $stmt->bind_param("i", $eventID);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    // Check if the event was found
    if (!$event) {
        $_SESSION['error_msg'] = "Event not found.";
        header("Location: ../fe/profile.php");
        exit();
    }

    $stmt->close();
} else {
    header("Location: ../fe/profile.php");
    exit();
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Event</title>
    <link rel="stylesheet" href="../fe/css/view_event.css">
    <link rel="stylesheet" href="../assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
</head>

<body>
    <div class="event-container card-primary card-outline">
        <div class="column1">
            <?php
            if (isset($_SESSION['success_msg'])) {
                echo '<div class="success_msg">' . htmlspecialchars($_SESSION['success_msg']) . '</div>';
                unset($_SESSION['success_msg']);
            }

            if (isset($_SESSION['error_msg'])) {
                echo '<div class="error_msg">' . htmlspecialchars($_SESSION['error_msg']) . '</div>';
                unset($_SESSION['error_msg']);
            }

            if ($event['Image']) {
                echo '<div class="event-image"><img src="' . htmlspecialchars($event['Image']) . '" alt="Event Image"></div>';
            }
            ?>
        </div>
        <div class="event-details">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <ul class="list-group list-group-unbordered mb-3">
                        <h2>Details</h2>
                        <?php
                        $fields = [
                            'Name' => 'Name',
                            'Category' => 'Category',
                            'Date_from' => 'Date_from',
                            'Date_to' => 'Date_to',
                            'Time_from' => 'Time_from',
                            'Time_to' => 'Time_to',
                            'Destination' => 'Destination',
                            'Description' => 'Description',
                            'Status' => 'Status',
                            'Cost' => 'Cost'
                        ];

                        foreach ($fields as $label => $dbField) {
                            echo '<li class="list-group-item">';
                            echo "<b>$label</b>";
                            echo '<span class="float-right">';
                            if ($dbField == 'Status') {
                                $statuses = ['Active', 'Pending', 'Canceled', 'Completed'];
                                echo htmlspecialchars($statuses[$event['Status']] ?? 'Unknown');
                            } elseif ($dbField == 'Cost') {
                                echo htmlspecialchars($event['Cost']) . " $";
                            } else {
                                echo htmlspecialchars($event[$dbField]);
                            }
                            if ($_SESSION['role'] == 'Admin') {
                        ?>
                                <form method="POST" action="edit_event.php" class="d-inline">
                                    <input type="hidden" name="field" value="<?php echo $dbField; ?>">
                                    <input type="hidden" name="id" value="<?php echo $eventID; ?>">
                                    <button type="submit" class="ml-4 btn btn-link p-0"><i class="fas fa-edit"></i></button>
                                </form>
                        <?php
                            }
                            echo '</span></li>';
                        }
                        ?>
                    </ul>

                    <div class="event-actions text-center">
                        <a href="profile.php" class="btn btn-primary">Back to Events</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>