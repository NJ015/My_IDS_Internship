<?php
session_start();
include 'dbconfig.php';

if (!isset($_POST['event_id']) && !isset($_SESSION['event_id'])) {
    header("Location: ../fe/login.php");
    exit();
}

$event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : intval($_SESSION['event_id']);
$_SESSION['event_id'] = $event_id;

// Fetch event details
$event_query = "SELECT * FROM events WHERE ID = ?";
$event_stmt = $conn->prepare($event_query);
$event_stmt->bind_param('i', $event_id);
$event_stmt->execute();
$event_result = $event_stmt->get_result();
$event = $event_result->fetch_assoc();

// Fetch guides for the event
$guides_query = "SELECT g.UserID, CONCAT(u.FirstName, ' ', u.LastName) AS Name 
                 FROM guide g
                 JOIN guide_event ge ON g.UserID = ge.GuideID
                 JOIN users u ON g.UserID = u.ID
                 WHERE ge.EventID = ?";
$guides_stmt = $conn->prepare($guides_query);
$guides_stmt->bind_param('i', $event_id);
$guides_stmt->execute();
$guides_result = $guides_stmt->get_result();

// Fetch members for the event
$members_query = "SELECT m.UserID, CONCAT(u.FirstName, ' ', u.LastName) AS Name 
                  FROM member m
                  JOIN member_event me ON m.UserID = me.MemberID
                  JOIN users u ON m.UserID = u.ID
                  WHERE me.EventID = ?";
$members_stmt = $conn->prepare($members_query);
$members_stmt->bind_param('i', $event_id);
$members_stmt->execute();
$members_result = $members_stmt->get_result();

// Fetch available guides
$available_guides_query = "SELECT g.UserID, CONCAT(u.FirstName, ' ', u.LastName) AS Name 
                           FROM guide g
                           JOIN users u ON g.UserID = u.ID
                           WHERE g.UserID NOT IN (SELECT GuideID FROM guide_event WHERE EventID = ?)";
$available_guides_stmt = $conn->prepare($available_guides_query);
$available_guides_stmt->bind_param('i', $event_id);
$available_guides_stmt->execute();
$available_guides_result = $available_guides_stmt->get_result();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_guide'])) {
        $guide_id = intval($_POST['guide_id']);
        $add_guide_query = "INSERT INTO guide_event (EventID, GuideID) VALUES (?, ?)";
        $add_guide_stmt = $conn->prepare($add_guide_query);
        $add_guide_stmt->bind_param('ii', $event_id, $guide_id);
        if ($add_guide_stmt->execute()) {
            $_SESSION['success_msg'] = "Guide added successfully.";
        } else {
            $_SESSION['error_msg'] = "Failed to add guide.";
        }
        header('Location: manage_event.php');
        exit();
    }

    if (isset($_POST['remove_guide'])) {
        $guide_id = intval($_POST['guide_id']);
        $remove_guide_query = "DELETE FROM guide_event WHERE EventID = ? AND GuideID = ?";
        $remove_guide_stmt = $conn->prepare($remove_guide_query);
        $remove_guide_stmt->bind_param('ii', $event_id, $guide_id);
        if ($remove_guide_stmt->execute()) {
            $_SESSION['success_msg'] = "Guide removed successfully.";
        } else {
            $_SESSION['error_msg'] = "Failed to remove guide.";
        }
        header('Location: manage_event.php');
        exit();
    }

    if (isset($_POST['remove_member'])) {
        $member_id = intval($_POST['member_id']);
        $remove_member_query = "DELETE FROM member_event WHERE EventID = ? AND MemberID = ?";
        $remove_member_stmt = $conn->prepare($remove_member_query);
        $remove_member_stmt->bind_param('ii', $event_id, $member_id);
        if ($remove_member_stmt->execute()) {
            $_SESSION['success_msg'] = "Member removed successfully.";
        } else {
            $_SESSION['error_msg'] = "Failed to remove member.";
        }
        header('Location: manage_event.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Event - <?php echo htmlspecialchars($event['Name']); ?></title>
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../fe/css/manage_event.css">
</head>

<body>
    <div class="content-wrapper" style="margin: 0px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col">
                        <h1>Manage Event - <?php echo htmlspecialchars($event['Name']); ?></h1>
                    </div>
                    <div class="event-actions text-center">
                        <a href="profile.php" class="btn btn-primary">Back</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Display success or error messages -->
                <?php if (isset($_SESSION['success_msg'])) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_SESSION['success_msg']; ?>
                    </div>
                    <?php unset($_SESSION['success_msg']); ?>
                <?php } ?>
                <?php if (isset($_SESSION['error_msg'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['error_msg']; ?>
                    </div>
                    <?php unset($_SESSION['error_msg']); ?>
                <?php } ?>

                <div class="row">
                    <div class="col">
                        <div class="card card-custom">
                            <div class="card-header">
                                <h3 class="card-title">Assign Guide</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <?php while ($guide = $available_guides_result->fetch_assoc()) { ?>
                                        <li class="list-group-item">
                                            <?php echo htmlspecialchars($guide['Name']); ?>
                                            <form action="manage_event.php" method="post" style="display:inline;">
                                                <input type="hidden" name="guide_id" value="<?php echo $guide['UserID']; ?>">
                                                <button type="submit" name="add_guide" class="btn btn-custom btn-sm">Add</button>
                                            </form>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <!-- Card to manage assigned guides -->
                        <div class="card card-custom">
                            <div class="card-header">
                                <h3 class="card-title">Assigned Guides</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <?php while ($guide = $guides_result->fetch_assoc()) { ?>
                                        <li class="list-group-item">
                                            <?php echo htmlspecialchars($guide['Name']); ?>
                                            <form action="manage_event.php" method="post" style="display:inline;">
                                                <input type="hidden" name="guide_id" value="<?php echo $guide['UserID']; ?>">
                                                <button type="submit" name="remove_guide" class="btn btn-danger btn-sm">Remove</button>
                                            </form>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <!-- Card to manage members -->
                        <div class="card card-custom">
                            <div class="card-header">
                                <h3 class="card-title">Members</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <?php while ($member = $members_result->fetch_assoc()) { ?>
                                        <li class="list-group-item">
                                            <?php echo htmlspecialchars($member['Name']); ?>
                                            <form action="manage_event.php" method="post" style="display:inline;">
                                                <input type="hidden" name="member_id" value="<?php echo $member['UserID']; ?>">
                                                <button type="submit" name="remove_member" class="btn btn-danger btn-sm">Remove</button>
                                            </form>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/adminlte.min.js"></script>
</body>

</html>