<?php
require_once '../config/auth.php';
if (!is_logged_in()) header('Location: login.php');
$user = current_user();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
    <h1> Welcome, <?= htmlspecialchars($user->name) ?> <span class="role">(<?= ucfirst($user->role) ?>)</span></h1>
    <nav>
        <a href="projects.php" class="btn">Projects</a>
        <a href="tasks.php" class="btn">Tasks</a>
        <a href="logout.php" class="btn btn-logout">Logout</a>
    </nav>
    <section class="card">
        <h2>Quick Stats</h2>
        <?php
        require_once '../classes/Project.php';
        require_once '../classes/Task.php';
        $projects = Project::getAll();
        $tasks = ($user->role === 'employee') ? Task::getByUser($user->id) : DB::get()->query("SELECT * FROM tasks")->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <ul class="stats">
            <li><strong>Total Projects:</strong> <?= count($projects) ?></li>
            <li><strong>Total Tasks:</strong> <?= count($tasks) ?></li>
            <li><strong>Role:</strong> <?= ucfirst($user->role) ?></li>
        </ul>
    </section>
    
</div>
</body>
</html>