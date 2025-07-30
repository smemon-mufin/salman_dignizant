<?php
require_once '../config/auth.php';
require_once '../classes/Task.php';
require_once '../classes/Project.php';
require_once '../classes/User.php';
require_once '../websocket_notify.php';

if (!is_logged_in()) die(json_encode(['error'=>'Not logged in']));
$user = current_user();
$userList = User::getAll();
$users = [];
foreach ($userList as $u) $users[$u->id] = $u;
$projectsList = Project::getAll();
$projects = [];
foreach ($projectsList as $p) $projects[$p['id']] = $p;

// GET: single task for modal edit
if (
    $_SERVER['REQUEST_METHOD'] === 'GET' &&
    isset($_GET['action']) && $_GET['action']=='get' &&
    isset($_GET['id']) && !empty($_GET['id'])
) {
    $t = Task::findById($_GET['id']);
    if ($t) {
        echo json_encode(['success'=>true,'task'=>[
            'id'=>$t->id,
            'project_id'=>$t->project_id,
            'title'=>$t->title,
            'description'=>$t->description,
            'status'=>$t->status,
            'priority'=>$t->priority,
            'assigned_to'=>$t->assigned_to,
            'deadline'=>$t->deadline
        ]]);
    } else {
        echo json_encode(['error'=>'Not found']);
    }
    exit();
}

// POST: create/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $tid = Task::create(
            $_POST['project_id'],
            $_POST['title'],
            $_POST['description'],
            $_POST['status'],
            $_POST['priority'],
            $_POST['assigned_to'],
            $_POST['deadline']
        );
        $t = Task::findById($tid);
        $project_title = isset($projects[$t->project_id]) ? $projects[$t->project_id]['title'] : '[Unknown Project]';
        $assigned_to_name = isset($users[$t->assigned_to]) ? $users[$t->assigned_to]->name : '[Unknown User]';
        
        // Notify assignment
        ws_notify_assignment($t->project_id, $t->id, $t->assigned_to, $t->title, $user->name);

        echo json_encode(['success'=>true,'task'=>[
            'id'=>$t->id,
            'project_title'=>$project_title,
            'title'=>$t->title,
            'description'=>$t->description,
            'status'=>$t->status,
            'priority'=>$t->priority,
            'assigned_to_name'=>$assigned_to_name,
            'deadline'=>$t->deadline
        ]]);
        exit();
    }
    if ($action === 'edit') {
        Task::update(
            $_POST['task_id'],
            $_POST['title'],
            $_POST['description'],
            $_POST['status'],
            $_POST['priority'],
            $_POST['assigned_to'],
            $_POST['deadline']
        );

        $t = Task::findById($_POST['task_id']);
        $project_title = isset($projects[$t->project_id]) ? $projects[$t->project_id]['title'] : '[Unknown Project]';
        $assigned_to_name = isset($users[$t->assigned_to]) ? $users[$t->assigned_to]->name : '[Unknown User]';

        // Broadcast status change
        ws_broadcast_status($t->project_id, $t->id, $t->status, $user->name, $t->title);

        echo json_encode(['success'=>true,'task'=>[
            'id'=>$t->id,
            'project_title'=>$project_title,
            'title'=>$t->title,
            'description'=>$t->description,
            'status'=>$t->status,
            'priority'=>$t->priority,
            'assigned_to_name'=>$assigned_to_name,
            'deadline'=>$t->deadline
        ]]);
        exit();
    }
    if ($action === 'delete') {
        Task::delete($_POST['task_id']);
        echo json_encode(['success'=>true]);
        exit();
    }
}
echo json_encode(['error'=>'Invalid request']);