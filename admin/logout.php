<?php
include 'includes/autoload.inc.php';

session_unset();
session_destroy();

setcookie("UserID", "", time() - 3600, '/');
setcookie("TypeUser", "", time() - 3600, '/');
ob_end_flush(header("Location: ../"));
