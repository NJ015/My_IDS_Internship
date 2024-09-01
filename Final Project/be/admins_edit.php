<?php

session_start();

require_once 'dbconfig.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../fe/login.php");
    exit();
}

if (isset($_SESSION['success_msg'])) {
    echo '<div style="background-color: #d4edda; color: #155724; padding: 10px;">';
    echo $_SESSION['success_msg'];
    echo '</div>';
    unset($_SESSION['success_msg']);
}

if (isset($_SESSION['error_msg'])) {
    echo '<div style="background-color: #f8d7da; color: #721c24; padding: 10px;">';
    echo $_SESSION['error_msg'];
    echo '</div>';
    unset($_SESSION['error_msg']);
}

$userID = $role = $firstName = $middleName = $lastName = $email = $profession = $dob = $nationality = $joiningDate = $phoneNb = $emNb = $position = $status = $pic = "";

if (($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) || isset($_SESSION['editing'])) {
    $userID = isset($_POST['id']) ?  $_POST['id'] : $_SESSION['editing'];
    $_SESSION["editing"] = $userID;
    $role = '';

    $roleQuery = "SELECT 'Admin' AS role FROM ADMIN WHERE UserID = ? 
                 UNION SELECT 'Guide' FROM GUIDE WHERE UserID = ? 
                 UNION SELECT 'Member' FROM MEMBER WHERE UserID = ?";

    if ($roleStmt = $conn->prepare($roleQuery)) {
        $roleStmt->bind_param("iii", $userID, $userID, $userID);
        $roleStmt->execute();
        $roleStmt->bind_result($role);
        $roleStmt->fetch();
        $roleStmt->close();
    }

    if ($role === 'Admin') {
        $sql = "SELECT USERS.ID, FirstName, MiddleName, LastName, Email, Profession, DOB, Nationality, Joining_Date, Phone_nb, Emergency_number, Position, Photo
                FROM USERS
                INNER JOIN ADMIN ON USERS.ID = ADMIN.UserID
                WHERE USERS.ID = ?";
    } elseif ($role === 'Guide') {
        $sql = "SELECT USERS.ID, FirstName, MiddleName, LastName, Email, Profession, DOB, Nationality, Joining_Date, Phone_nb, Emergency_number, Status, Photo
                FROM USERS
                INNER JOIN GUIDE ON USERS.ID = GUIDE.UserID
                WHERE USERS.ID = ?";
    } elseif ($role === 'Member') {
        $sql = "SELECT USERS.ID, FirstName, MiddleName, LastName, Email, Profession, DOB, Nationality, Joining_Date, Phone_nb, Emergency_number, Photo
                FROM USERS
                INNER JOIN MEMBER ON USERS.ID = MEMBER.UserID
                WHERE USERS.ID = ?";
    }

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        if ($role === 'Admin') {
            $stmt->bind_result($id, $firstName, $middleName, $lastName, $email, $profession, $dob, $nationality, $joiningDate, $phoneNb, $emNb, $position, $pic);
        } elseif ($role === 'Guide') {
            $stmt->bind_result($id, $firstName, $middleName, $lastName, $email, $profession, $dob, $nationality, $joiningDate, $phoneNb, $emNb, $status, $pic);
        } elseif ($role === 'Member') {
            $stmt->bind_result($id, $firstName, $middleName, $lastName, $email, $profession, $dob, $nationality, $joiningDate, $phoneNb, $emNb, $pic);
        }
        $stmt->fetch();
        $stmt->close();
    }

    if (empty($pic)) {
        $pic = 'default-profile.png';
    }

    $_SESSION["editing"] = $userID;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit as Admin</title>
    <link rel="stylesheet" href="../assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../fe/css/admins_edit.css">
</head>

<body>
    <div class="container-fluid centered-container">
        <div class="profile-card">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle" src="<?php echo htmlspecialchars($pic); ?>" alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center mb-3"><?php echo htmlspecialchars($firstName . " " . $middleName . " " . $lastName); ?></h3>
                    <ul class="list-group list-group-unbordered table2">
                        <div class="float-left">
                            <li class="list-group-item">
                                <b>Role</b>
                                <span class="float-right ml-4">
                                    <?php echo htmlspecialchars($role); ?>
                                    <form method="POST" action="" class="d-inline">
                                        <button type="submit" class="ml-2 btn btn-link p-0 invisible"><i class="fas fa-edit"></i></button>
                                    </form>
                                </span>
                            </li>

                            <li class="list-group-item">
                                <b>Profession</b>
                                <span class="float-right ml-4">
                                    <?php echo htmlspecialchars($profession); ?>
                                    <form method="POST" action="admin_update.php" class="d-inline">
                                        <input type="hidden" name="field" value="profession">
                                        <button type="submit" class="ml-2 btn btn-link p-0"><i class="fas fa-edit"></i></button>
                                    </form>
                                </span>
                            </li>

                            <li class="list-group-item">
                                <b>Date of Birth</b>
                                <span class="float-right ml-4 ml-5">
                                    <?php echo htmlspecialchars($dob); ?>
                                    <form method="POST" action="admin_update.php" class="d-inline">
                                        <input type="hidden" name="field" value="dob">
                                        <button type="submit" class="ml-2 btn btn-link p-0"><i class="fas fa-edit"></i></button>
                                    </form>
                                </span>
                            </li>

                            <li class="list-group-item">
                                <b>Nationality</b>
                                <span class="float-right ml-4">
                                    <?php echo htmlspecialchars($nationality); ?>
                                    <form method="POST" action="admin_update.php" class="d-inline">
                                        <input type="hidden" name="field" value="nationality">
                                        <button type="submit" class="ml-2 btn btn-link p-0"><i class="fas fa-edit"></i></button>
                                    </form>
                                </span>
                            </li>

                            <li class="list-group-item">
                                <b>Joining Date</b>
                                <span class="float-right ml-4">
                                    <?php echo htmlspecialchars($joiningDate); ?>
                                    <form method="POST" action="" class="d-inline">
                                        <button type="submit" class="ml-2 btn btn-link p-0 invisible"><i class="fas fa-edit"></i></button>
                                    </form>
                                </span>
                            </li>
                        </div>
                        <div class="float-right">
                            <li class="list-group-item">
                                <b>Email</b>
                                <span class="float-right ml-4">
                                    <?php echo htmlspecialchars($email); ?>
                                    <form method="POST" action="admin_update.php" class="d-inline">
                                        <input type="hidden" name="field" value="email">
                                        <button type="submit" class="ml-2 btn btn-link p-0"><i class="fas fa-edit"></i></button>
                                    </form>
                                </span>
                            </li>

                            <li class="list-group-item">
                                <b>Password</b>
                                <span class="float-right ml-4">
                                    *********
                                    <form method="POST" action="admin_update.php" class="d-inline">
                                        <input type="hidden" name="field" value="password">
                                        <button type="submit" class="ml-2 btn btn-link p-0"><i class="fas fa-edit"></i></button>
                                    </form>
                                </span>
                            </li>

                            <li class="list-group-item">
                                <b>Phone Number</b>
                                <span class="float-right ml-4">
                                    <?php echo htmlspecialchars($phoneNb); ?>
                                    <form method="POST" action="admin_update.php" class="d-inline">
                                        <input type="hidden" name="field" value="phone_nb">
                                        <button type="submit" class="ml-2 btn btn-link p-0"><i class="fas fa-edit"></i></button>
                                    </form>
                                </span>
                            </li>

                            <li class="list-group-item">
                                <b>Emergency Number</b>
                                <span class="float-right ml-4">
                                    <?php echo htmlspecialchars($emNb); ?>
                                    <form method="POST" action="admin_update.php" class="d-inline">
                                        <input type="hidden" name="field" value="em_nb">
                                        <button type="submit" class="ml-2 btn btn-link p-0"><i class="fas fa-edit"></i></button>
                                    </form>
                                </span>
                            </li>

                            <?php if ($role === 'Admin'): ?>
                                <li class="list-group-item">
                                    <b>Position</b>
                                    <span class="float-right ml-4">
                                        <?php echo htmlspecialchars($position); ?>
                                        <form method="POST" action="admin_update.php" class="d-inline">
                                            <input type="hidden" name="field" value="position">
                                            <button type="submit" class="ml-2 btn btn-link p-0"><i class="fas fa-edit"></i></button>
                                        </form>
                                    </span>
                                </li>
                            <?php elseif ($role === 'Guide'): ?>
                                <li class="list-group-item">
                                    <b>Status</b>
                                    <span class="float-right ml-4">
                                        <?php echo ($status == 1) ? 'Pending' : (($status == 0) ? 'Accepted' : 'Rejected'); ?>
                                        <form method="POST" action="admin_update.php" class="d-inline">
                                            <input type="hidden" name="field" value="">
                                            <button type="submit" class="ml-2 btn btn-link p-0"><i class="fas fa-edit"></i></button>
                                        </form>
                                    </span>
                                </li>
                            <?php endif; ?>
                    </ul>
                </div>
                <div class="text-center">
                    <a href="profile.php" class="btn btn-primary mb-3">Back to Profile</a>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="../assets/js/adminlte.min.js"></script>
</body>

</html>