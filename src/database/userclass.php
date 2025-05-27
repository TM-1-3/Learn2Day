<?php

declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';

class User
{
    public int $id;
    public string $username;
    public string $email;
    public string $type;

    public function __construct(int $id, string $username, string $email, string $type)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->type = $type;
    }

    public static function create(string $username, string $password, string $email, string $type): int
    {
        $db = Database::getInstance();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare('
            INSERT INTO USERS (USERNAME, PASSWORD, EMAIL, TYPE) 
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$username, $hashed_password, $email, $type]);

        return (int)$db->lastInsertId();
    }

    public static function get_user_by_username_password(string $username, string $password): ?User
    {
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

    public static function get_user_by_username(string $username): ?User
    {
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

    public static function get_user_by_id(int $id): ?User
    {
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

    public static function get_user_by_email(string $email): ?User
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM USERS WHERE EMAIL = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if ($row) {
            return new User(
                (int)$row['ID_USER'],
                $row['USERNAME'],
                $row['EMAIL'],
                $row['TYPE']
            );
        }
        return null;
    }

    public function update(string $username, string $email, string $type): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            UPDATE USERS 
            SET USERNAME = ?, 
                EMAIL = ?, 
                TYPE = ? 
            WHERE ID_USER = ?
        ');
        return $stmt->execute([$username, $email, $type, $this->id]);
    }

    public static function updatePassword(int $id, string $new_password): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            UPDATE USERS
            SET PASSWORD = ? 
            WHERE ID_USER = ?');

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        return $stmt->execute([$hashed_password, $id]);
    }

    public static function countAllUsers(): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM USERS');
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }


    public function delete(): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM USERS WHERE ID_USER = ?');
        return $stmt->execute([$this->id]);
    }

    public static function friendship(string $username1, string $username2): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT COUNT(*) FROM STUDENT_TUTOR 
            WHERE (STUDENT = ? AND TUTOR = ?)
            OR (STUDENT = ? AND TUTOR = ?)
        ');
        $stmt->execute([$username1, $username2, $username2, $username1]);
        return (bool)$stmt->fetchColumn();
    }
}
