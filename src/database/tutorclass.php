<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/userclass.php';

class Tutor {
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
    ): void {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO TUTOR
            (ID_TUTOR, NAME, DATE_OF_BIRTH, PROFILE_IMAGE, DESCRIPTION) 
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $username,
            $name,
            $date_of_birth,
            $profile_image,
            $description
        ]);
    }

    public static function getByUsername(string $username): ?Tutor {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM TUTOR WHERE ID_TUTOR = ?');
        $stmt->execute([$username]);
        if ($row = $stmt->fetch()) {
            return new Tutor(
                $username,
                $row['NAME'],
                $row['DATE_OF_BIRTH'],
                $row['PROFILE_IMAGE'],
                $row['DESCRIPTION'] ?? null
            );
        }
        return null;
    }

    public static function updateProfileImage(string $username, string $new_image_path): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE TUTOR SET PROFILE_IMAGE = ? WHERE ID_TUTOR = ?');
        return $stmt->execute([$new_image_path, $username]);
    }

    public static function updateDescription(string $username, string $description): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE TUTOR SET DESCRIPTION = ? WHERE ID_TUTOR = ?');
        return $stmt->execute([$description, $username]);
    }

    public function update(): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            UPDATE TUTOR 
            SET NAME = ?, 
                DATE_OF_BIRTH = ?, 
                PROFILE_IMAGE = ?, 
                DESCRIPTION = ?
            WHERE ID_TUTOR = ?
        ');
        return $stmt->execute([
            $this->name,
            $this->date_of_birth,
            $this->profile_image,
            $this->description,
            $this->username
        ]);
    }
}
?>