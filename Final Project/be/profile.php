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

// $action = isset($_GET['action']) ? $_GET['action'] : '';

// switch ($action) {
//     case 'edit-admins':
//         if ($_SESSION['role'] === 'Admin') {
//             editAdmins();
//         } else {
//             unauthorizedAccess();
//         }
//         break;
//     case 'manage-activities':
//         if ($_SESSION['role'] === 'Admin') {
//             manageActivities();
//         } else {
//             unauthorizedAccess();
//         }
//         break;
//     case 'edit-members':
//         if ($_SESSION['role'] === 'Admin') {
//             editMembers();
//         } else {
//             unauthorizedAccess();
//         }
//         break;
//     case 'edit-guides':
//         if ($_SESSION['role'] === 'Admin') {
//             editGuides();
//         } else {
//             unauthorizedAccess();
//         }
//         break;
//     case 'manage-events':
//         if ($_SESSION['role'] === 'Admin') {
//             manageEvents();
//         } else {
//             unauthorizedAccess();
//         }
//         break;
//     case 'view-timeline':
//         viewTimeline();
//         break;
//     case 'view-activities':
//         if ($_SESSION['role'] === 'Member') {
//             viewActivities();
//         } else {
//             unauthorizedAccess();
//         }
//         break;
//     case 'edit-settings':
//         if ($_SESSION['role'] === 'Member' || $_SESSION['role'] === 'Guide') {
//             editSettings();
//         } else {
//             unauthorizedAccess();
//         }
//         break;
//     case 'view-guiding-activities':
//         if ($_SESSION['role'] === 'Guide') {
//             viewGuidingActivities();
//         } else {
//             unauthorizedAccess();
//         }
//         break;
//         // default:
//         //     echo "Invalid action.";
//         //     break;
// }

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

    $sql = "SELECT USERS.ID, FirstName, LastName, Email
            FROM USERS 
            INNER JOIN ADMIN ON USERS.ID = ADMIN.UserID";

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

                            <form method="POST" action="../be/delete_admins.php" style="display:inline;">
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
        <p>No admins found.</p>
    <?php
    }
}

function manageActivities()
{
    echo '<h1>Manage Activities</h1>';
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
    echo '<h1>Manage Events</h1>';
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
