<?php
require_once __DIR__ . '/../classes/User.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function current_user() {
    if (!is_logged_in()) return null;
    $user = User::findById($_SESSION['user_id']);
    return $user; // Could be null if user has been deleted
}

function require_role($roles) {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
    $user = current_user();
    if (!$user) {
        // Session exists but user not found in DB (deleted?)
        header('Location: login.php');
        exit();
    }
    if (is_array($roles)) {
        if (!in_array($user->role, $roles)) {
            header('Location: login.php');
            exit();
        }
    } else {
        if ($user->role !== $roles) {
            header('Location: login.php');
            exit();
        }
    }
}
?>