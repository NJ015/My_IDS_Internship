<?php
// session_start();
include 'dbconfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../fe/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);

if ($user) {
    $_SESSION['role'] = $user['Role'];
    $_SESSION['firstName'] = $user['FirstName'];
    $_SESSION['middleName'] = $user['MiddleName'];
    $_SESSION['lastName'] = $user['LastName'];
    $_SESSION['email'] = $user['Email'];
    $_SESSION['dob'] = $user['DOB'];
    $_SESSION['age'] = $user['Age'];
    $_SESSION['joining_date'] = $user['Joining_Date'];
    $_SESSION['profession'] = $user['Profession'];
    $_SESSION['photo'] = $user['Photo'];
    $_SESSION['em_nb'] = $user['Emergency_number'];
    $_SESSION['phone_nb'] = $user['Phone_nb'];
    $_SESSION['nationality'] = $user['Nationality'];

    switch ($user['Role']) {
        case 'Admin':
            $_SESSION['position'] = getAdminPosition($user_id);
        case 'Member':
            $_SESSION['joined_events'] = getMemberEvents($user_id);
            $_SESSION['responsible_events'] = null;
            break;
        case 'Guide':
            $_SESSION['joined_events'] = null;
            $_SESSION['responsible_events'] = getGuideEvents($user_id);
            $_SESSION['status'] = getGuideStatus($user_id);
            break;
        default:
            $_SESSION['joined_events'] = null;
            $_SESSION['responsible_events'] = null;
            break;
    }
} else {
    echo "User not found.";
    exit();
}


function getUserById($user_id)
{
    global $conn;
    $sql = "SELECT * FROM USERS WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

function getMemberEvents($user_id)
{
    global $conn;
    $sql = "SELECT Joined_events FROM MEMBER WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc()['Joined_events'] : null;
}

function getGuideEvents($user_id)
{
    global $conn;
    $sql = "SELECT Responsible_events FROM GUIDE WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc()['Responsible_events'] : null;
}

function getGuideStatus($user_id)
{
    global $conn;
    $sql = "SELECT Status FROM GUIDE WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc()['Status'] : null;
}

function getAdminPosition($user_id)
{
    global $conn;
    $sql = "SELECT Position FROM Admin WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc()['Position'] : null;
}

function editAdmins()
{
    global $conn;

    $current_position = $_SESSION['position'];

    $sql = "SELECT USERS.ID, FirstName, LastName, Email
            FROM USERS 
            INNER JOIN ADMIN ON USERS.ID = ADMIN.UserID
            WHERE ADMIN.Position > ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $current_position);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></td>
                        <td><?php echo htmlspecialchars($row['Email']); ?></td>
                        <td>
                            <form method="POST" action="../be/admins_edit.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['ID']); ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                            </form>

                            <form method="POST" action="../be/delete_admins.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['ID']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    <?php
    } else {
    ?>
        <p>No admins found.</p>
    <?php
    }

    $stmt->close();
}



function editMembers()
{
    global $conn;

    $sql = "SELECT USERS.ID, FirstName, LastName, Email
            FROM USERS 
            INNER JOIN MEMBER ON USERS.ID = MEMBER.UserID";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
    ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?php echo $row['FirstName'] . ' ' . $row['LastName']; ?></td>
                        <td><?php echo $row['Email']; ?></td>
                        <td>
                            <form method="POST" action="../be/admins_edit.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                            </form>

                            <form method="POST" action="../be/delete_members.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    <?php
    } else {
    ?>
        <p>No members found.</p>
    <?php
    }
}


function editGuides()
{
    global $conn;

    $sqlAccepted = "SELECT USERS.ID, FirstName, LastName, Email 
                    FROM USERS 
                    INNER JOIN GUIDE ON USERS.ID = GUIDE.UserID
                    WHERE GUIDE.Status = 0";

    $resultAccepted = $conn->query($sqlAccepted);

    $sqlPending = "SELECT USERS.ID, FirstName, LastName, Email 
                   FROM USERS 
                   INNER JOIN GUIDE ON USERS.ID = GUIDE.UserID
                   WHERE GUIDE.Status = 1";

    $resultPending = $conn->query($sqlPending);

    echo "<h4>Pending Guides</h4>";
    if ($resultPending->num_rows > 0) {
    ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $resultPending->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?php echo $row['FirstName'] . ' ' . $row['LastName']; ?></td>
                        <td><?php echo $row['Email']; ?></td>
                        <td>
                            <form method="POST" action="../be/approve_guides.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                            </form>
                            <form method="POST" action="../be/reject_guides.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    <?php
    } else {
    ?>
        <p>No pending guides found.</p>
    <?php
    }


    echo "<h4>Accepted Guides</h4>";
    if ($resultAccepted->num_rows > 0) {
    ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $resultAccepted->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?php echo $row['FirstName'] . ' ' . $row['LastName']; ?></td>
                        <td><?php echo $row['Email']; ?></td>
                        <td>
                            <form method="POST" action="../be/admins_edit.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                            </form>

                            <form method="POST" action="../be/delete_guides.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    <?php
    } else {
    ?>
        <p>No accepted guides found.</p>
    <?php
    }
}


function manageEvents()
{
    global $conn;

    // Get the current date
    $current_date = date('Y-m-d');

    // Update events to "Completed" if the status is "Active" and the Date_to has passed
    $update_sql = "UPDATE EVENTS SET Status = 3 WHERE Status = 0 AND Date_to < ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("s", $current_date);
    $update_stmt->execute();
    $update_stmt->close();

    ?>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../be/add_event.php">Add Event</a></li>
        </ol>
    </div>

    <div class="accordion" id="eventAccordion">
        <?php
        $statuses = [
            0 => 'Active',
            1 => 'Pending',
            2 => 'Canceled',
            3 => 'Completed'
        ];

        foreach ($statuses as $status_code => $status_name) {
            $stmt = $conn->prepare("SELECT * FROM EVENTS WHERE Status = ?");
            $stmt->bind_param("i", $status_code);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
        ?>
                <div class="card">
                    <div class="card-header" id="heading<?php echo $status_code; ?>">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse<?php echo $status_code; ?>" aria-expanded="true" aria-controls="collapse<?php echo $status_code; ?>">
                                <?php echo $status_name; ?> Events
                            </button>
                        </h2>
                    </div>
                    <div id="collapse<?php echo $status_code; ?>" class="collapse" aria-labelledby="heading<?php echo $status_code; ?>" data-parent="#eventAccordion">
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Event Name</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['Name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['Date_from']) . " --> " . htmlspecialchars($row['Date_to']); ?></td>
                                            <td>
                                                <form method="POST" action="../be/view_event.php" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                                    <button type="submit" class="btn btn-info btn-sm">View</button>
                                                </form>

                                                <?php if ($status_code == 0) { // Active 
                                                ?>
                                                    <form method="POST" action="../be/manage_event.php" style="display:inline;">
                                                        <input type="hidden" name="event_id" value="<?php echo $row['ID']; ?>">
                                                        <button type="submit" class="btn btn-warning btn-sm">Manage</button>
                                                    </form>
                                                    <form method="POST" action="../be/cancel_event.php" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                                    </form>
                                                <?php } ?>

                                                <?php if ($status_code == 1 || $status_code == 2) { // Pending or Canceled 
                                                ?>
                                                    <form method="POST" action="../be/activate_event.php" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                                        <button type="submit" class="btn btn-success btn-sm">Activate</button>
                                                    </form>
                                                    <form method="POST" action="../be/delete_event.php" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                    </form>
                                                <?php } ?>

                                                <?php if ($status_code == 3) { // Completed 
                                                ?>
                                                    <form method="POST" action="../be/delete_event.php" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                    </form>
                                                <?php } ?>

                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <div class="card">
                    <div class="card-header" id="heading<?php echo $status_code; ?>">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse<?php echo $status_code; ?>" aria-expanded="true" aria-controls="collapse<?php echo $status_code; ?>">
                                <?php echo $status_name; ?> Events
                            </button>
                        </h2>
                    </div>

                    <div id="collapse<?php echo $status_code; ?>" class="collapse" aria-labelledby="heading<?php echo $status_code; ?>" data-parent="#eventAccordion">
                        <div class="card-body">
                            <p>No events found under <?php echo $status_name; ?>.</p>
                        </div>
                    </div>
                </div>
        <?php
            }

            $stmt->close();
        }
        ?>
    </div>

    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<?php
}


function manageActivities()
{
    echo '<h1>Manage Activities</h1>';
}

function viewTimeline()
{
    echo '<h1>View Timeline</h1>';
}

function viewActivities()
{
    echo '<h1>View Activities</h1>';
}

function editSettings()
{
    echo '<h1>Edit Settings</h1>';
}

function viewGuidingActivities()
{
    echo '<h1>View Guiding Activities</h1>';
}

function unauthorizedAccess()
{
    echo '<h1>Unauthorized Access</h1>';
}
