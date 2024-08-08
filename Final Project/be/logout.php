<?php
session_start();

session_destroy();

header("Location: ../fe/login.php");
exit();
?>
