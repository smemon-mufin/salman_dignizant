<?php
require_once __DIR__ . '/../config/db.php';

class Project {
    public $id, $title, $description, $deadline, $created_by;

    public static function getAll() {
        $stmt = DB::get()->query("SELECT * FROM projects");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $stmt = DB::get()->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($title, $desc, $deadline, $created_by) {
        $stmt = DB::get()->prepare("INSERT INTO projects (title, description, deadline, created_by) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $desc, $deadline, $created_by]);
        return DB::get()->lastInsertId();
    }

    public static function update($id, $title, $desc, $deadline) {
        $stmt = DB::get()->prepare("UPDATE projects SET title=?, description=?, deadline=? WHERE id=?");
        $stmt->execute([$title, $desc, $deadline, $id]);
    }

    public static function delete($id) {
        $stmt = DB::get()->prepare("DELETE FROM projects WHERE id=?");
        $stmt->execute([$id]);
    }

    public static function assignMembers($project_id, $user_ids) {
        $stmt = DB::get()->prepare("DELETE FROM project_members WHERE project_id=?");
        $stmt->execute([$project_id]);
        $stmt = DB::get()->prepare("INSERT INTO project_members (project_id, user_id) VALUES (?, ?)");
        foreach ($user_ids as $uid) {
            $stmt->execute([$project_id, $uid]);
        }
    }

    public static function getMembers($project_id) {
        $stmt = DB::get()->prepare("SELECT * FROM project_members WHERE project_id=?");
        $stmt->execute([$project_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>