<?php
require_once __DIR__ . '/../config/db.php';

class Task {
    public $id, $project_id, $title, $description, $status, $priority, $assigned_to, $deadline;

    public static function getByProject($project_id) {
        $stmt = DB::get()->prepare("SELECT * FROM tasks WHERE project_id = ?");
        $stmt->execute([$project_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByUser($user_id) {
        $stmt = DB::get()->prepare("SELECT * FROM tasks WHERE assigned_to = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($project_id, $title, $desc, $status, $priority, $assigned_to, $deadline) {
        $stmt = DB::get()->prepare("INSERT INTO tasks (project_id, title, description, status, priority, assigned_to, deadline) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$project_id, $title, $desc, $status, $priority, $assigned_to, $deadline]);
        return DB::get()->lastInsertId();
    }

    public static function update($id, $title, $desc, $status, $priority, $assigned_to, $deadline) {
        $stmt = DB::get()->prepare("UPDATE tasks SET title=?, description=?, status=?, priority=?, assigned_to=?, deadline=? WHERE id=?");
        $stmt->execute([$title, $desc, $status, $priority, $assigned_to, $deadline, $id]);
    }

    public static function delete($id) {
        $stmt = DB::get()->prepare("DELETE FROM tasks WHERE id=?");
        $stmt->execute([$id]);
    }

    public static function updateStatus($id, $status) {
        $stmt = DB::get()->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    

    public static function getAttachment($task_id) {
    $db = DB::get();
    $stmt = $db->prepare("SELECT * FROM task_attachments WHERE task_id = :task_id ORDER BY uploaded_at DESC LIMIT 1");
    $stmt->bindValue(':task_id', $task_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // returns array or false
}
    
    public static function addAttachment($task_id, $filepath, $user_id) {
    $db = DB::get();
    $stmt = $db->prepare("INSERT INTO task_attachments (task_id, filepath, uploaded_by, uploaded_at) VALUES (:task_id, :filepath, :uploaded_by, NOW())");
    $stmt->bindValue(':task_id', $task_id, PDO::PARAM_INT);
    $stmt->bindValue(':filepath', $filepath, PDO::PARAM_STR);
    $stmt->bindValue(':uploaded_by', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $db->lastInsertId();
}
    public static function findById($id) {
    $db = DB::get();
    $stmt = $db->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) return null;
    $task = new self();
    foreach ($row as $key => $value) {
        $task->$key = $value;
    }
    return $task;
}

public static function search($search = '', $page = 1, $perPage = 10) {
    $db = DB::get();
    $offset = ($page - 1) * $perPage;
    $sql = "SELECT * FROM tasks WHERE title LIKE :search OR description LIKE :search LIMIT :perPage OFFSET :offset";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':search', '%'.$search.'%', PDO::PARAM_STR);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public static function count($search = '') {
    $db = DB::get();
    $sql = "SELECT COUNT(*) FROM tasks WHERE title LIKE :search OR description LIKE :search";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':search', '%'.$search.'%', PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}
}
?>