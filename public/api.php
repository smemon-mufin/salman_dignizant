<?php
// Simple RESTful API routing starter
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$segments = explode('/', trim($request, '/'));

if ($segments[0] === 'tasks') {
    if ($method === 'GET') {
        // /tasks?search=...&page=...
        // Return filtered and paginated tasks as JSON
        require_once '../classes/Task.php';
        $search = $_GET['search'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 10;
        $tasks = Task::search($search, $page, $perPage);
        echo json_encode($tasks);
    } elseif ($method === 'POST') {
        // /tasks - create new task
    } elseif (isset($segments[1]) && $segments[1] === 'upload' && $method === 'POST') {
        // Handle file upload
        $task_id = $_POST['task_id'];
        if (isset($_FILES['task_file'])) {
            $target = "uploads/" . basename($_FILES['task_file']['name']);
            move_uploaded_file($_FILES['task_file']['tmp_name'], $target);
            // Save info to DB as needed
            echo json_encode(['success'=>true, 'filename'=>basename($target)]);
        } else {
            echo json_encode(['error'=>'No file']);
        }
    }
    exit;
}

?>