<?php
session_start();
require_once("../be/profile.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Define the tabs for each user type
$tabs = array(
    'Admin' => array(
        'Activity' => 'manage-activities',
        'Members' => 'edit-members',
        'Guides' => 'edit-guides',
        'Events' => 'manage-events',
        'Timeline' => 'view-timeline'
    ),
    'Member' => array(
        'Activity' => 'view-activities',
        'Timeline' => 'view-timeline',
        'Settings' => 'edit-settings'
    ),
    'Guide' => array(
        'Activity' => 'view-guiding-activities',
        'Timeline' => 'view-timeline',
        'Settings' => 'edit-settings'
    )
);

$role = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | User Profile</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../adminlte/dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index.php" class="brand-link">
                <img src="../adminlte/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">AdminLTE 3</span>
            </a>
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="<?php echo $_SESSION["photo"]; ?>" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block"><?php echo $_SESSION["firstName"] . " " . $_SESSION["middleName"] . " " . $_SESSION["lastName"]; ?></a>
                    </div>
                </div>
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <?php foreach ($tabs[$role] as $tab => $url) : ?>
                            <li class="nav-item">
                                <a href="<?php echo $url; ?>" class="nav-link">
                                    <i class="nav-icon fas fa-<?php echo $tab === 'Activity' ? 'calendar' : ($tab === 'Timeline' ? 'history' : 'user'); ?>"></i>
                                    <p><?php echo $tab; ?></p>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Profile</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../be/logout.php">Logout</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <img class="profile-user-img img-fluid img-circle" src="<?php echo $_SESSION["photo"]; ?>" alt="User profile picture">
                                    </div>
                                    <h3 class="profile-username text-center"><?php echo $_SESSION["firstName"] . " " . $_SESSION["middleName"] . " " . $_SESSION["lastName"]; ?></h3>
                                    <p class="text-muted text-center"><?php echo $_SESSION["profession"]; ?></p>
                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>Email</b> <a class="float-right"><?php echo $_SESSION["email"]; ?></a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Phone Number</b> <a class="float-right"><?php echo $_SESSION["phone_nb"]; ?></a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Emergency Number</b> <a class="float-right"><?php echo $_SESSION["em_nb"]; ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <?php foreach ($tabs[$role] as $tab => $url) : ?>
                                            <li class="nav-item">
                                                <a class="nav-link <?php echo $action === $url ? 'active' : ''; ?>" href="?action=<?php echo $url; ?>" data-toggle="tab"><?php echo $tab; ?></a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <?php foreach ($tabs[$role] as $tab => $url) : ?>
                                            <div class="tab-pane <?php echo $action === $url ? 'active' : ''; ?>" id="<?php echo strtolower(str_replace(' ', '-', $tab)); ?>">
                                                <?php if ($action === 'manage-activities') : ?>
                                                    <?php // Call the backend function to manage activities 
                                                    ?>
                                                    <p>Content for Manage Activities</p>
                                                <?php elseif ($action === 'edit-members') : ?>
                                                    <?php // Call the backend function to edit members 
                                                    ?>
                                                    <p>Content for Edit Members</p>
                                                <?php elseif ($action === 'edit-guides') : ?>
                                                    <?php // Call the backend function to edit guides 
                                                    ?>
                                                    <p>Content for Edit Guides</p>
                                                <?php elseif ($action === 'manage-events') : ?>
                                                    <?php // Call the backend function to manage events 
                                                    ?>
                                                    <p>Content for Manage Events</p>
                                                <?php elseif ($action === 'view-timeline') : ?>
                                                    <?php // Call the backend function to view timeline 
                                                    ?>
                                                    <p>Content for View Timeline</p>
                                                <?php elseif ($action === 'view-activities') : ?>
                                                    <?php // Call the backend function to view activities 
                                                    ?>
                                                    <p>Content for View Activities</p>
                                                <?php elseif ($action === 'edit-settings') : ?>
                                                    <?php // Call the backend function to edit settings 
                                                    ?>
                                                    <p>Content for Edit Settings</p>
                                                <?php elseif ($action === 'view-guiding-activities') : ?>
                                                    <?php // Call the backend function to view guiding activities 
                                                    ?>
                                                    <p>Content for View Guiding Activities</p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <script src="../adminlte/plugins/jquery/jquery.min.js"></script>
    <script src="../adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../adminlte/dist/js/adminlte.min.js"></script>
</body>

</html>