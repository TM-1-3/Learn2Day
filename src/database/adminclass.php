<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';

class Admin {
    public string $username;
    public string $name;
    public string $date_of_birth;
    public string $profile_image;
    public ?string $description;

    public function __construct(
        string $username,
        string $name,
        string $date_of_birth,
        string $profile_image,
        ?string $description
    ) {
        $this->username = $username;
        $this->name = $name;
        $this->date_of_birth = $date_of_birth;
        $this->profile_image = $profile_image;
        $this->description = $description;
    }

    public static function create(
        string $username,
        string $name,
        string $date_of_birth,
        string $profile_image,
        ?string $description
    ): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO ADMIN 
            (ID_ADMIN, NAME, DATE_OF_BIRTH, PROFILE_IMAGE, DESCRIPTION) 
            VALUES (?, ?, ?, ?, ?)
        ');
        return $stmt->execute([
            $username,
            $name,
            $date_of_birth,
            $profile_image,
            $description
        ]);
    }

    public static function getByUsername(string $username): ?Admin {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM ADMIN WHERE ID_ADMIN = ?');
        $stmt->execute([$username]);
        if ($row = $stmt->fetch()) {
            return new Admin(
                $username,
                $row['NAME'],
                $row['DATE_OF_BIRTH'],
                $row['PROFILE_IMAGE'],
                $row['DESCRIPTION'] ?? null
            );
        }
        return null;
    }

    public static function delete(string $username): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM ADMIN WHERE ID_ADMIN = ?');
        return $stmt->execute([$username]);
    }

    public static function countAllAdmins(): int{
        $db = Database::getInstance();
        $stmt = $db->query('SELECT COUNT(*) FROM ADMIN');
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}
?>