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

    public static function create(string $username, string $password, string $email, string $type): int {
        $db = Database::getInstance();
        $hashed_password = sha1($password);
        
        $stmt = $db->prepare('
            INSERT INTO users (username, password, email, type) 
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$username, $hashed_password, $email, $type]);
        
        return (int)$db->lastInsertId();
    }

    public static function get_user_by_username_password(string $username, string $password): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        
        if ($user = $stmt->fetch()) {
            if (password_verify($password, $user['password'])) {
                return [
                    'id' => (int)$user['ID_USER'],
                    'username' => $user['USERNAME'],
                    'email' => $user['EMAIL'],
                    'type' => $user['TYPE']
                ];
            }
        }
        return null;
    }

    public static function get_user_by_username(string $username): ?array {
        if (empty($username)) {
            throw new InvalidArgumentException("Username cannot be empty");
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);

        return $stmt->fetch() ?: null;
    }

    public static function get_user_by_id(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}
?>