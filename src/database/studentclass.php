<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/userclass.php';

class Student {
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
            INSERT INTO STUDENT 
            (ID_STUDENT, NAME, DATE_OF_BIRTH, PROFILE_IMAGE, DESCRIPTION) 
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

    public static function getByUsername(string $username): ?Student {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM STUDENT WHERE ID_STUDENT = ?');
        $stmt->execute([$username]);
        if ($row = $stmt->fetch()) {
            return new Student(
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
        $stmt = $db->prepare('UPDATE STUDENT SET PROFILE_IMAGE = ? WHERE ID_STUDENT = ?');
        return $stmt->execute([$new_image_path, $username]);
    }

    public static function updateDescription(string $username, string $description): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE STUDENT SET DESCRIPTION = ? WHERE ID_STUDENT = ?');
        return $stmt->execute([$description, $username]);
    }

    public function update(): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            UPDATE STUDENT 
            SET NAME = ?, 
                DATE_OF_BIRTH = ?, 
                PROFILE_IMAGE = ?, 
                DESCRIPTION = ?
            WHERE ID_STUDENT = ?
        ');
        return $stmt->execute([
            $this->name,
            $this->date_of_birth,
            $this->profile_image,
            $this->description,
            $this->username
        ]);
    }

    public static function getAllStudents(): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT s.*, u.ID_USER 
                             FROM STUDENT s 
                             JOIN USERS u ON s.ID_STUDENT = u.USERNAME 
                             WHERE u.TYPE = "STUDENT"');
        $stmt->execute();
        $students = [];
        while ($row = $stmt->fetch()) {
            $students[] = new Student(
                $row['ID_STUDENT'],
                $row['NAME'],
                $row['DATE_OF_BIRTH'],
                $row['PROFILE_IMAGE'],
                $row['DESCRIPTION'] ?? null
            );
        }
        return $students;
    }
}
?>