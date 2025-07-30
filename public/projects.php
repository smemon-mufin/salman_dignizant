<?php
require_once '../config/auth.php';
require_role(['admin', 'manager']);
require_once '../classes/Project.php';
require_once '../classes/User.php';

$user = current_user();

$userList = User::getAll();
$users = [];
foreach ($userList as $u) {
    $users[$u->id] = $u;
}
$projects = Project::getAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Projects</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
    <h1>🚀 Project Management</h1>
    <nav>
        <a href="dashboard.php" class="btn">Dashboard</a>
        <a href="tasks.php" class="btn">Tasks</a>
        <a href="logout.php" class="btn btn-logout">Logout</a>
    </nav>
    <button class="btn" onclick="showModal('modalProject')">Add Project</button>
    <section class="card table-responsive">
        <h2>All Projects</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Title</th>
                <th>Deadline</th>
                <th>Description</th>
                <th>Members</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="projects-tbody">
            <?php foreach ($projects as $p):
                $p_members = Project::getMembers($p['id']);
                $members_html = '';
                foreach ($p_members as $m) {
                    if (isset($users[$m['user_id']])) {
                        $members_html .= htmlspecialchars($users[$m['user_id']]->name) . ', ';
                    } else {
                        $members_html .= '[Unknown User], ';
                    }
                }
            ?>
            <tr class="table-row-animate" data-project-id="<?= $p['id'] ?>">
                <td><?= htmlspecialchars($p['title']) ?></td>
                <td><?= htmlspecialchars($p['deadline']) ?></td>
                <td><?= htmlspecialchars($p['description']) ?></td>
                <td><?= rtrim($members_html, ', ') ?></td>
                <td>
                    <button class="btn btn-edit" onclick="openEditProjectModal(<?= $p['id'] ?>)">Edit</button>
                    <button class="btn btn-delete" onclick="deleteProject(<?= $p['id'] ?>)">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

<!-- Create Project Modal -->
<div id="modalProject" class="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="hideModal('modalProject')">&times;</button>
        <h2>Add Project</h2>
        <form id="project-create-form" class="form modal-form">
            <input type="hidden" name="action" value="create">
            <label>Title</label>
            <input type="text" name="title" required>
            <label>Deadline</label>
            <input type="date" name="deadline" required>
            <label>Description</label>
            <textarea name="description"></textarea>
            <label>Members</label>
            <select name="members[]" multiple style="max-height:120px;overflow-y:auto;">
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u->id ?>"><?= htmlspecialchars($u->name) ?> (<?= $u->role ?>)</option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">Create</button>
        </form>
    </div>
</div>

<!-- Edit Project Modal -->
<div id="modalProjectEdit" class="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="hideModal('modalProjectEdit')">&times;</button>
        <h2>Edit Project</h2>
        <form id="project-edit-form" class="form modal-form">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="project_id">
            <label>Title</label>
            <input type="text" name="title" required>
            <label>Deadline</label>
            <input type="date" name="deadline" required>
            <label>Description</label>
            <textarea name="description"></textarea>
            <label>Members</label>
            <select name="members[]" multiple style="max-height:120px;overflow-y:auto;">
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u->id ?>"><?= htmlspecialchars($u->name) ?> (<?= $u->role ?>)</option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">Update</button>
        </form>
    </div>
</div>

<script src="../assets/app.js"></script>
</body>
</html>