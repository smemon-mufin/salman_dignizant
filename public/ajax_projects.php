<?php
require_once '../config/auth.php';
require_once '../classes/Project.php';
require_once '../classes/User.php';

if (!is_logged_in()) die(json_encode(['error'=>'Not logged in']));
$user = current_user();
$userList = User::getAll();
$users = [];
foreach ($userList as $u) $users[$u->id] = $u;

// GET: return project for modal edit
if (
    $_SERVER['REQUEST_METHOD'] === 'GET' &&
    isset($_GET['action']) && $_GET['action'] === 'get' &&
    isset($_GET['id']) && !empty($_GET['id'])
) {
    $p = Project::findById($_GET['id']);
    if ($p) {
        $members = array_map(
            function($m){ return intval($m['user_id']); },
            Project::getMembers($p['id'])
        );
        echo json_encode(['success'=>true,'project'=>[
            'id'=>$p['id'],
            'title'=>$p['title'],
            'deadline'=>$p['deadline'],
            'description'=>$p['description'],
            'members'=>$members
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
        $pid = Project::create(
            $_POST['title'],
            $_POST['description'],
            $_POST['deadline'],
            $user->id
        );
        Project::assignMembers($pid, $_POST['members'] ?? []);
        $p = Project::findById($pid);
        $members_html = '';
        foreach (Project::getMembers($pid) as $m) {
            if (isset($users[$m['user_id']])) {
                $members_html .= htmlspecialchars($users[$m['user_id']]->name) . ', ';
            } else {
                $members_html .= '[Unknown User], ';
            }
        }
        echo json_encode(['success'=>true,'project'=>[
            'id'=>$p['id'],
            'title'=>$p['title'],
            'deadline'=>$p['deadline'],
            'description'=>$p['description'],
            'members_html'=>rtrim($members_html, ', ')
        ]]);
        exit();
    }
    if ($action === 'edit') {
        Project::update(
            $_POST['project_id'],
            $_POST['title'],
            $_POST['description'],
            $_POST['deadline']
        );
        Project::assignMembers($_POST['project_id'], $_POST['members'] ?? []);
        $p = Project::findById($_POST['project_id']);
        $members_html = '';
        foreach (Project::getMembers($p['id']) as $m) {
            if (isset($users[$m['user_id']])) {
                $members_html .= htmlspecialchars($users[$m['user_id']]->name) . ', ';
            } else {
                $members_html .= '[Unknown User], ';
            }
        }
        echo json_encode(['success'=>true,'project'=>[
            'id'=>$p['id'],
            'title'=>$p['title'],
            'deadline'=>$p['deadline'],
            'description'=>$p['description'],
            'members_html'=>rtrim($members_html, ', ')
        ]]);
        exit();
    }
    if ($action === 'delete') {
        Project::delete($_POST['project_id']);
        echo json_encode(['success'=>true]);
        exit();
    }
}
echo json_encode(['error'=>'Invalid request']);