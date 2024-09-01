<?php
session_start();
if (isset($_SESSION["role"]) && $_SESSION["role"] === "Admin") {
    $isAdmin = true;
} elseif (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

function unsetValues()
{
    unset($_SESSION["role1"]);
    unset($_SESSION["gender1"]);
    unset($_SESSION["firstName1"]);
    unset($_SESSION["middleName1"]);
    unset($_SESSION["lastName1"]);
    unset($_SESSION["email1"]);
    unset($_SESSION["dob1"]);
    unset($_SESSION["profession1"]);
    unset($_SESSION["photo1"]);
    unset($_SESSION["emergency_number1"]);
    unset($_SESSION["phone_nb1"]);
    unset($_SESSION["nationality1"]);
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registration Page</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <link rel="stylesheet" href="../adminlte/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="../adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css" />
    <link rel="stylesheet" href="../adminlte/dist/css/adminlte.min.css" />
    <link rel="stylesheet" href="../assets/css/register.css" />
</head>

<body class="hold-transition register-page">
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <b>Register</b>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Register a new membership</p>

                <form action="../be/register.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="role">Role</label>
                        <div class="input-group mb-3">
                            <select class="form-control" name="role" id="role" required value="<?php echo isset($_SESSION['role1']) ? $_SESSION['role1'] : ''; ?>">
                                <option value="">Select Role</option>
                                <option value="Member">Member</option>
                                <option value="Guide">Guide</option>
                                <?php if ($isAdmin) { ?>
                                    <option value="Admin">Admin</option>
                                <?php } ?>
                            </select>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user-tag"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <div class="input-group mb-3">
                            <select class="form-control" name="gender" id="gender" required value="<?php echo isset($_SESSION['gender1']) ? $_SESSION['gender1'] : ''; ?>">
                                <option value="">Select Gender</option>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                            </select>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-venus-mars"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="firstName" id="firstName" placeholder="John" required value="<?php echo isset($_SESSION['firstName1']) ? $_SESSION['firstName1'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="middleName">Middle Name</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="middleName" id="middleName" placeholder="A." value="<?php echo isset($_SESSION['middleName1']) ? $_SESSION['middleName1'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="lastName" id="lastName" placeholder="Doe" required value="<?php echo isset($_SESSION['lastName1']) ? $_SESSION['lastName1'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" name="email" id="email" placeholder="example@example.com" required value="<?php echo isset($_SESSION['email1']) ? $_SESSION['email1'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <span id="emailError" class="error"><?php echo isset($_SESSION['e_error']) ? $_SESSION['e_error'] : '';
                                                            unset($_SESSION['e_error']); ?></span>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" name="password" id="password" placeholder="******" required />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="******" required />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <span id="passwordError" class="error"><?php echo isset($_SESSION['cpass_error']) ? $_SESSION['cpass_error'] : '';
                                                                unset($_SESSION['cpass_error']); ?></span>
                    </div>

                    <div class="form-group">
                        <label for="phoneNb">Phone Number</label>
                        <div class="input-group mb-3">
                            <input type="tel" class="form-control" name="phone_nb" id="phoneNb" placeholder="123-456-7890" required value="<?php echo isset($_SESSION['phone_nb1']) ? $_SESSION['phone_nb1'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-phone"></span>
                                </div>
                            </div>
                        </div>
                        <span id="PhoneError" class="error"><?php echo isset($_SESSION['phone_error']) ? $_SESSION['phone_error'] : '';
                                                            unset($_SESSION['phone_error']); ?></span>
                    </div>

                    <div class="form-group">
                        <label for="emergencyNumber">Emergency Number</label>
                        <div class="input-group mb-3">
                            <input type="tel" class="form-control" name="emergency_number" id="emergencyNumber" placeholder="123-456-7890" required value="<?php echo isset($_SESSION['emergency_number1']) ? $_SESSION['emergency_number1'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-exclamation-triangle"></span>
                                </div>
                            </div>
                        </div>
                        <span id="EmPhoneError" class="error"><?php echo isset($_SESSION['em_nb_error']) ? $_SESSION['em_nb_error'] : '';
                                                                unset($_SESSION['em_nb_error']); ?></span>
                    </div>

                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <div class="input-group mb-3">
                            <input type="date" class="form-control" name="dob" id="dob" required value="<?php echo isset($_SESSION['dob1']) ? $_SESSION['dob1'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-calendar-alt"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="profession">Profession</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="profession" id="profession" placeholder="Engineer" required value="<?php echo isset($_SESSION['profession1']) ? $_SESSION['profession1'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-briefcase"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="photo">Upload Photo</label>
                        <div class="input-group mb-3">
                            <input type="file" class="form-control" name="photo" id="photo" accept="image/*" required value="<?php echo isset($_SESSION['photo1']) ? $_SESSION['photo1'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-file-image"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nationality">Nationality</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="nationality" id="nationality" placeholder="Lebanese" required value="<?php echo isset($_SESSION['nationality1']) ? $_SESSION['nationality1'] : ''; ?>" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-briefcase"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Register</button>
                </form>

                <div class="mt-1">
                    <a href="login.php" class="text-center">I already have a membership</a>
                </div>
            </div>
        </div>
    </div>

    <?php unsetValues(); ?>

    <script src="../adminlte/plugins/jquery/jquery.min.js"></script>
    <script src="../adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../adminlte/dist/js/adminlte.min.js"></script>
    <!-- <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            if (password !== confirmPassword) {
                event.preventDefault();
                document.getElementById('passwordError').textContent = "Passwords do not match.";
            }
        });
    </script> -->
</body>

</html>