<?php
session_start();
require_once 'dbconfig.php';

if (isset($_SESSION["role"]) && $_SESSION["role"] === "Admin") {
    $isAdmin = true;
} elseif (!isset($_SESSION['user_id'])) {
    header("Location: ../fe/profile.php");
    exit();
}

function unsetValues()
{
    unset($_SESSION["eventCategory"]);
    unset($_SESSION["eventName"]);
    unset($_SESSION["eventDescription"]);
    unset($_SESSION["eventImage"]);
    unset($_SESSION["eventDestination"]);
    unset($_SESSION["dateFrom"]);
    unset($_SESSION["dateTo"]);
    unset($_SESSION["timeFrom"]);
    unset($_SESSION["timeTo"]);
    unset($_SESSION["eventCost"]);
}

function validateInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $eventCategory = validateInput($_POST["eventCategory"]);
    $eventName = validateInput($_POST["eventName"]);
    $eventDescription = validateInput($_POST["eventDescription"]);
    $eventDestination = validateInput($_POST["eventDestination"]);
    $dateFrom = validateInput($_POST["dateFrom"]);
    $dateTo = validateInput($_POST["dateTo"]);
    $timeFrom = validateInput($_POST["timeFrom"]);
    $timeTo = validateInput($_POST["timeTo"]);
    $eventCost = validateInput($_POST["eventCost"]);

    $target_dir = "../assets/images/";
    $eventImage = $target_dir . basename($_FILES["eventImage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($eventImage, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["eventImage"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $_SESSION['error_msg'] = "File is not an image.";
        $uploadOk = 0;
    }

    if (file_exists($eventImage)) {
        $_SESSION['error_msg'] = "Sorry, file already exists.";
        $uploadOk = 0;
    }

    if ($_FILES["eventImage"]["size"] > 50000000) {
        $_SESSION['error_msg'] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $_SESSION['error_msg'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        header("Location: ../fe/profile.php");
        exit();
    } else {
        if (move_uploaded_file($_FILES["eventImage"]["tmp_name"], $eventImage)) {
            $stmt = $conn->prepare("INSERT INTO EVENTS (Category, Name, Description, Image, Destination, Date_From, Date_To, Time_From, Time_To, Cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssd", $eventCategory, $eventName, $eventDescription, $eventImage, $eventDestination, $dateFrom, $dateTo, $timeFrom, $timeTo, $eventCost);

            // Execute the query
            if ($stmt->execute()) {
                $_SESSION['success_msg'] = "Event successfully added!";
            } else {
                $_SESSION['error_msg'] = "Error: " . $stmt->error;
            }

            $stmt->close();
            $conn->close();
            header("Location: ../fe/profile.php");
            exit();
        } else {
            $_SESSION['error_msg'] = "Sorry, there was an error uploading your file.";
            header("Location: ../fe/profile.php");
            exit();
        }
    }
} else {
    // header("Location: ../fe/profile.php");
    // exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>New Event</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <link rel="stylesheet" href="../adminlte/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="../adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css" />
    <link rel="stylesheet" href="../adminlte/dist/css/adminlte.min.css" />
    <link rel="stylesheet" href="../assets/css/register.css" />
</head>

<body class="hold-transition register-page">
    <div class="manage-events-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <b>Add Events</b>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Add events</p>

                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="eventCategory">Category</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="eventCategory" id="eventCategory" placeholder="Adventure" required value="<?php echo isset($_SESSION['eventCategory']) ? $_SESSION['eventCategory'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-list"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="eventName">Event Name</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="eventName" id="eventName" placeholder="Hiking Trip" required value="<?php echo isset($_SESSION['eventName']) ? $_SESSION['eventName'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-calendar"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="eventDescription">Description</label>
                        <div class="input-group mb-3">
                            <textarea class="form-control" name="eventDescription" id="eventDescription" placeholder="A brief description of the event" required><?php echo isset($_SESSION['eventDescription']) ? $_SESSION['eventDescription'] : ''; ?></textarea>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-info"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="eventImage">Event Image</label>
                        <div class="input-group mb-3">
                            <input type="file" class="form-control" name="eventImage" id="eventImage" required />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-image"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="eventDestination">Destination</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="eventDestination" id="eventDestination" placeholder="Mountain X" required value="<?php echo isset($_SESSION['eventDestination']) ? $_SESSION['eventDestination'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-map-marker-alt"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dateFrom">Date From</label>
                        <div class="input-group mb-3">
                            <input type="date" class="form-control" name="dateFrom" id="dateFrom" required value="<?php echo isset($_SESSION['dateFrom']) ? $_SESSION['dateFrom'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-calendar-day"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dateTo">Date To</label>
                        <div class="input-group mb-3">
                            <input type="date" class="form-control" name="dateTo" id="dateTo" required value="<?php echo isset($_SESSION['dateTo']) ? $_SESSION['dateTo'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-calendar-day"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="timeFrom">Time From</label>
                        <div class="input-group mb-3">
                            <input type="time" class="form-control" name="timeFrom" id="timeFrom" required value="<?php echo isset($_SESSION['timeFrom']) ? $_SESSION['timeFrom'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-clock"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="timeTo">Time To</label>
                        <div class="input-group mb-3">
                            <input type="time" class="form-control" name="timeTo" id="timeTo" required value="<?php echo isset($_SESSION['timeTo']) ? $_SESSION['timeTo'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-clock"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="eventCost">Cost</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" name="eventCost" id="eventCost" placeholder="100" required value="<?php echo isset($_SESSION['eventCost']) ? $_SESSION['eventCost'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-dollar-sign"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </form>

                <div class="mt-1 text-center">
                    <a href="profile.php">View All Events</a>
                </div>
            </div>
        </div>
    </div>

    <?php unsetValues(); ?>

    <script src="../adminlte/plugins/jquery/jquery.min.js"></script>
    <script src="../adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../adminlte/dist/js/adminlte.min.js"></script>
</body>

</html>