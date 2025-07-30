<?php
require_once '../config/auth.php';
require_once '../classes/Task.php';
session_start();
if (!is_logged_in()) die('Not logged in');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['attachment'])) {
    $task_id = $_POST['task_id'];
    $filename = basename($_FILES['attachment']['name']);
    $target = "../uploads/" . uniqid() . "_" . $filename;
    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
        Task::addAttachment($task_id, $target, $_SESSION['user_id']);
        echo "success";
        exit();
    } else {
        echo "Upload failed";
    }
}
?>