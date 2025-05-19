<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';

class User {
    public int $id;
    public string $username;
    public string $email;
    public string $type;

    public function __construct(int $id, string $username, string $email, string $type) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->type = $type;
    }

    public static function create(string $username, string $password, string $email, string $type): int {
        $db = Database::getInstance();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare('
            INSERT INTO USERS (USERNAME, PASSWORD, EMAIL, TYPE) 
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$username, $hashed_password, $email, $type]);
        
        return (int)$db->lastInsertId();
    }

    public static function get_user_by_username_password(string $username, string $password): ?User {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM USERS WHERE USERNAME = ?');
        $stmt->execute([$username]);
        
        if ($row = $stmt->fetch()) {
            if (password_verify($password, $row['PASSWORD'])) {
                return new User(
                    (int)$row['ID_USER'],
                    $row['USERNAME'],
                    $row['EMAIL'],
                    $row['TYPE']
                );
            }
        }
        return null;
    }

    public static function get_user_by_username(string $username): ?User {
        if (empty($username)) {
            throw new InvalidArgumentException("Username cannot be empty");
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM USERS WHERE USERNAME = ?');
        $stmt->execute([$username]);

        if ($row = $stmt->fetch()) {
            return new User(
                (int)$row['ID_USER'],
                $row['USERNAME'],
                $row['EMAIL'],
                $row['TYPE']
            );
        }
        return null;
    }

    public static function get_user_by_id(int $id): ?User {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM USERS WHERE ID_USER = ?');
        $stmt->execute([$id]);
        
        if ($row = $stmt->fetch()) {
            return new User(
                (int)$row['ID_USER'],
                $row['USERNAME'],
                $row['EMAIL'],
                $row['TYPE']
            );
        }
        return null;
    }
}
?>
