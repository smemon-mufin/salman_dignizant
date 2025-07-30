<?php
require_once __DIR__ . '/vendor/autoload.php';

function ws_broadcast_status($projectId, $taskId, $status, $userName, $taskTitle) {
    ws_send([
        'type' => 'status',
        'projectId' => $projectId,
        'taskId' => $taskId,
        'status' => $status,
        'userName' => $userName,
        'taskTitle' => $taskTitle
    ]);
}

function ws_broadcast_comment($projectId, $taskId, $comment, $userName, $taskTitle) {
    ws_send([
        'type' => 'comment',
        'projectId' => $projectId,
        'taskId' => $taskId,
        'comment' => $comment,
        'userName' => $userName,
        'taskTitle' => $taskTitle
    ]);
}

function ws_notify_assignment($projectId, $taskId, $assignedUserId, $taskTitle, $fromUserName) {
    ws_send([
        'type' => 'assign',
        'projectId' => $projectId,
        'taskId' => $taskId,
        'assignedUserId' => $assignedUserId,
        'taskTitle' => $taskTitle,
        'fromUserName' => $fromUserName
    ]);
}

function ws_send($msg) {
    try {
        $ws = new \WebSocket\Client("ws://localhost:8081"); // Or whatever port your server uses
        $ws->send(json_encode($msg));
        $ws->close();
    } catch (\Exception $ex) {
        // Optionally log error
    }
}