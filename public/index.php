<?php
require_once '../config/auth.php';
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit();
} else {
    header('Location: login.php');
    exit();
}
?>