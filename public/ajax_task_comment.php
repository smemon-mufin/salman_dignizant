<?php
require_once '../config/auth.php';
require_once '../classes/Task.php';
require_once '../classes/User.php';
require_once '../websocket_notify.php';

if (!is_logged_in()) die(json_encode(['error'=>'Not logged in']));
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['task_id'];
    $projectId = $_POST['project_id'];
    $comment = $_POST['comment'];
    // Save the comment in your DB here...
    ws_broadcast_comment($projectId, $taskId, $comment, $user->name, $_POST['task_title']);
    echo json_encode(['success' => true]);
}