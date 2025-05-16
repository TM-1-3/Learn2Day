<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/userclass.php';

class Student {
    public int $id_student;
    public string $name;
    public string $date_of_birth;
    public string $profile_image;
    public ?string $description;
    public string $school_institution;

    public function __construct(
        int $id_student,
        string $name,
        string $date_of_birth,
        string $profile_image,
        ?string $description,
        string $school_institution
    ) {
        $this->id_student = $id_student;
        $this->name = $name;
        $this->date_of_birth = $date_of_birth;
        $this->profile_image = $profile_image;
        $this->description = $description;
        $this->school_institution = $school_institution;
    }

    public static function create(
        int $id_student,
        string $name,
        string $date_of_birth,
        string $profile_image,
        ?string $description,
        string $school_institution
    ): void {
        $db = Database::getInstance();
        
        // First get the username from the users table
        $user = User::get_user_by_id($id_student);
        if (!$user) {
            throw new Exception('User not found');
        }

        $stmt = $db->prepare('
            INSERT INTO STUDENT 
            (ID_STUDENT, NAME, DATE_OF_BIRTH, PROFILE_IMAGE, DESCRIPTION, SCHOOL_INSTITUTION) 
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $user->username,
            $name,
            $date_of_birth,
            $profile_image,
            $description,
            $school_institution
        ]);
    }

    public static function getById(int $id_student): ?Student {
        $db = Database::getInstance();
        
        // First get the username from the users table
        $user = User::get_user_by_id($id_student);
        if (!$user) {
            return null;
        }

        $stmt = $db->prepare('SELECT * FROM STUDENT WHERE ID_STUDENT = ?');
        $stmt->execute([$user->username]);
        
        if ($row = $stmt->fetch()) {
            return new Student(
                $id_student,
                $row['NAME'],
                $row['DATE_OF_BIRTH'],
                $row['PROFILE_IMAGE'],
                $row['DESCRIPTION'],
                $row['SCHOOL_INSTITUTION']
            );
        }
        
        return null;
    }

    public static function updateProfileImage(int $id_student, string $new_image_path): bool {
        $user = User::get_user_by_id($id_student);
        if (!$user) {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE STUDENT SET PROFILE_IMAGE = ? WHERE ID_STUDENT = ?');
        return $stmt->execute([$new_image_path, $user->username]);
    }

    public static function updateDescription(int $id_student, string $description): bool {
        $user = User::get_user_by_id($id_student);
        if (!$user) {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE STUDENT SET DESCRIPTION = ? WHERE ID_STUDENT = ?');
        return $stmt->execute([$description, $user->username]);
    }
}
?>