<?php
require_once '../config/auth.php';
require_once '../classes/Task.php';
require_once '../classes/Project.php';
require_once '../classes/User.php';

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset(); session_destroy();
    header('Location: login.php?timeout=1'); exit();
}
$_SESSION['last_activity'] = time();

$user = current_user();
$userList = User::getAll();
$users = [];
foreach ($userList as $u) {
    $users[$u->id] = $u;
}
$projectsList = Project::getAll();
$projects = [];
foreach ($projectsList as $p) {
    $projects[$p['id']] = $p;
}

// Search/filter/pagination
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;

if ($user->role == 'admin') {
    $tasks = Task::search($search, $page, $perPage); // Implement this method for search and pagination
    $totalTasks = Task::count($search); // Implement count method for pagination
} else {
    $tasks = Task::getByUser($user->id, $search, $page, $perPage);
    $totalTasks = Task::countByUser($user->id, $search);
}
$totalPages = ceil($totalTasks / $perPage);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tasks</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/emoji-js@3.6.0/lib/emoji.min.js"></script>
</head>
<body>
<div class="container">
    <h1>📝 Task Management </h1>
    <nav>
        <a href="dashboard.php" class="btn">Dashboard</a>
        <a href="projects.php" class="btn">Projects</a>
        <a href="logout.php" class="btn btn-logout">Logout</a>
    </nav>
    <input id="search-input" placeholder="Search tasks..." style="margin:12px 0;width:250px;">
    <?php if ($user->role == 'admin'): ?>
    <button class="btn" onclick="showModal('modalTask')">Add Task</button>
    <?php endif; ?>
    <section class="card table-responsive">
        <h2>All Tasks</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Project</th>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Assigned To</th>
                <th>Deadline</th>
                <th>Attachment</th>
                <?php if ($user->role == 'admin'): ?><th>Actions</th><?php endif; ?>
            </tr>
            </thead>
            <tbody id="tasks-tbody">
            <?php foreach ($tasks as $task):
                $project = isset($projects[$task['project_id']]) ? $projects[$task['project_id']] : null;
                $assigned = isset($users[$task['assigned_to']]) ? $users[$task['assigned_to']] : null;
                $attachment = Task::getAttachment($task['id']); // Implement getAttachment method
            ?>
            <tr class="table-row-animate" data-task-id="<?= $task['id'] ?>">
                <td><?= htmlspecialchars($project ? $project['title'] : '[Unknown Project]') ?></td>
                <td><?= htmlspecialchars($task['title']) ?></td>
                <td><?= htmlspecialchars($task['description']) ?></td>
                <td><?= htmlspecialchars($task['status']) ?></td>
                <td><?= htmlspecialchars($task['priority']) ?></td>
                <td><?= htmlspecialchars($assigned ? $assigned->name : '[Unknown User]') ?></td>
                <td><?= htmlspecialchars($task['deadline']) ?></td>
                <td>
    <?php if (!empty($attachment['filepath'])): ?>
        <a href="<?= htmlspecialchars($attachment['filepath']) ?>" target="_blank">Download</a>
    <?php else: ?>
        <span style="color:#aaa;">No file</span>
    <?php endif; ?>
    <form class="upload-form" data-task="<?= $task['id'] ?>" style="display:inline;" enctype="multipart/form-data">
        <input type="file" name="attachment" style="width:110px;">
        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
        <button type="submit" class="btn btn-primary" style="padding:2px 10px;font-size:13px;">Upload</button>
        <span class="progress" style="font-size:12px;"></span>
    </form>
</td>
                <?php if ($user->role == 'admin'): ?>
                <td>
                    <button class="btn btn-edit" onclick="openEditTaskModal(<?= $task['id'] ?>)">Edit</button>
                    <button class="btn btn-delete" onclick="deleteTask(<?= $task['id'] ?>)">Delete</button>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div id="pagination">
            <?php for ($i=1;$i<=$totalPages;$i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="btn" style="margin-right:2px;<?= ($i==$page?'background:#21cbf3;':'') ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </section>
    <section>
        <h3>Leave a comment</h3>
        <form id="comment-form">
            <input type="hidden" name="task_id" value="">
            <input type="hidden" name="project_id" value="">
            <input type="hidden" name="task_title" value="">
            <textarea id="comment-text" name="comment" rows="3" placeholder="Type comment..."></textarea>
            <div id="preview"></div>
            <button type="submit" class="btn">Send</button>
        </form>
        <div id="task-comments"></div>
        <div id="toast-container"></div>
    </section>
</div>
<script src="../assets/task-upload.js"></script>
<script src="../assets/task-search.js"></script>
<script src="../assets/markdown-emoji.js"></script>
<script>
    // Modal show/hide stubs for demonstration
    function showModal(id){ document.getElementById(id).style.display='block'; }
    function hideModal(id){ document.getElementById(id).style.display='none'; }
</script>
</body>
</html>