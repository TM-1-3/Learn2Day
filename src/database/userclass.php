<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';

class User {
    public int $id;
    public string $username;

    public function __construct(int $id, string $username) {
        $this->id = $id;
        $this->username = $username;
    }

    public static function create(string $username, string $password, string $email, string $type): void {
        if (empty($password)) {
            throw new InvalidArgumentException("Password cannot be empty");
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO users (username, password, email, type) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $username, 
            sha1($password),
            $email, 
            $type
        ]);
    }

    public static function get_user_by_username_password(string $username, string $password): ?array {
        if (empty($username) || empty($password)) {
            throw new InvalidArgumentException("Username and password cannot be empty");
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ? AND password = ?');
        $stmt->execute([
            $username, 
            sha1($password)
        ]);

        return $stmt->fetch() ?: null;
    }
}
?>