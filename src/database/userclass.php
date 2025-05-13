<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';

class User{
    public int $id;
    public string $username;


    public function __construct(int $id, string $username){
        $this->id = $id;
        $this->username = $username;
    }

    public static function create($username, $password, $email, $type){
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO users (username, password, email, type) VALUES (?,?,?,?) ');
        $stmt->execute([$username, sha1($password), $email, $type]);
    }

    public static function get_customer_by_username_password($username, $password){
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ? AND password = ?');
        $stmt->execute([$username, sha1($password)]);

        return $stmt->fetch();
    }
}

?>