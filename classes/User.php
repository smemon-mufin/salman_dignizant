<?php
require_once __DIR__ . '/../config/db.php';

class User {
    public $id, $username, $password, $role, $name, $email;

    public static function findById($id) {
        $stmt = DB::get()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) return self::fromRow($row);
        return null;
    }

    public static function findByUsername($username) {
        $stmt = DB::get()->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) return self::fromRow($row);
        return null;
    }

    public static function getAll() {
        $stmt = DB::get()->query("SELECT * FROM users");
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = self::fromRow($row);
        }
        return $users;
    }

    public static function fromRow($row) {
        $u = new self();
        $u->id = $row['id'];
        $u->username = $row['username'];
        $u->password = $row['password'];
        $u->role = $row['role'];
        $u->name = $row['name'];
        $u->email = $row['email'];
        return $u;
    }
}
?>