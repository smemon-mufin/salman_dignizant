<?php
class DB {
    private static $instance = null;
    public static function get() {
        if (self::$instance === null) {
            $dsn = "pgsql:host=localhost;port=5432;dbname=sam;user=postgres;password=salman_user";
            self::$instance = new PDO($dsn);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}


?>