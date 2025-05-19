<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/userclass.php';

class Tutor {
    public int $id;
    public string $name;
    public string $date_of_birth;
    public string $profile_image;
    public ?string $description;
    public string $school_institution;

    public function __construct(
        int $id,
        string $name,
        string $date_of_birth,
        string $profile_image,
        ?string $description,
        string $school_institution
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->date_of_birth = $date_of_birth;
        $this->profile_image = $profile_image;
        $this->description = $description;
        $this->school_institution = $school_institution;
    }

    public static function create(
        int $id,
        string $name,
        string $date_of_birth,
        string $profile_image,
        ?string $description,
        string $school_institution
    ): void {
        $db = Database::getInstance();
        $user = User::get_user_by_id($id);

        if (!$user) {
            throw new Exception('User not found');
        }

        if ($user->type !== 'TUTOR') {
            throw new Exception('User is not registered as a tutor');
        }

        $stmt = $db->prepare('
            INSERT INTO TUTOR
            (ID_TUTOR, NAME, DATE_OF_BIRTH, PROFILE_IMAGE, DESCRIPTION, SCHOOL_INSTITUTION) 
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

    public static function getById(int $id): ?Tutor {
        $user = User::get_user_by_id($id);
        if (!$user || $user->type !== 'TUTOR') {
            return null;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM TUTOR WHERE ID_TUTOR = ?');
        $stmt->execute([$user->username]);
        
        if ($row = $stmt->fetch()) {
            return new Tutor(
                $id,
                $row['NAME'],
                $row['DATE_OF_BIRTH'],
                $row['PROFILE_IMAGE'],
                $row['DESCRIPTION'],
                $row['SCHOOL_INSTITUTION']
            );
        }
        
        return null;
    }

    public static function getUserIdbyUserName(string $username): ?int {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT ID_USER FROM USERS WHERE USERNAME = ?');
        $stmt->execute([$username]);
        
        if ($row = $stmt->fetch()) {
            return (int)$row['ID_USER'];
        }
        
        return null;
    }

    public static function updateProfileImage(int $id, string $new_image_path): bool {
        $user = User::get_user_by_id($id);
        if (!$user || $user->type !== 'TUTOR') {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE TUTOR SET PROFILE_IMAGE = ? WHERE ID_TUTOR = ?');
        return $stmt->execute([$new_image_path, $user->username]);
    }

    public static function updateDescription(int $id, string $description): bool {
        $user = User::get_user_by_id($id);
        if (!$user || $user->type !== 'TUTOR') {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE TUTOR SET DESCRIPTION = ? WHERE ID_TUTOR = ?');
        return $stmt->execute([$description, $user->username]);
    }



    public static function getAllTutors(): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT t.*, u.ID_USER 
                             FROM TUTOR t 
                             JOIN USERS u ON t.ID_TUTOR = u.USERNAME 
                             WHERE u.TYPE = "TUTOR"');
        $stmt->execute();
        
        $tutors = [];
        while ($row = $stmt->fetch()) {
            $tutors[] = new Tutor(
                (int)$row['ID_USER'],
                $row['NAME'],
                $row['DATE_OF_BIRTH'],
                $row['PROFILE_IMAGE'],
                $row['DESCRIPTION'],
                $row['SCHOOL_INSTITUTION']
            );
        }
        return $tutors;
    }

    public static function addSubject(int $tutorId, string $subject, int $grade): bool {
        $user = User::get_user_by_id($tutorId);
        if (!$user || $user->type !== 'TUTOR') {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO TUTOR_SUBJECT (TUTOR, SUBJECT, GRADE)
            VALUES (?, ?, ?)
        ');
        return $stmt->execute([$user->username, $subject, $grade]);
    }

    public static function getSubjects(int $tutorId): array {
        $user = User::get_user_by_id($tutorId);
        if (!$user || $user->type !== 'TUTOR') {
            return [];
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT SUBJECT, GRADE 
            FROM TUTOR_SUBJECT 
            WHERE TUTOR = ?
        ');
        $stmt->execute([$user->username]);
        return $stmt->fetchAll();
    }

    public function update(): bool {
        $db = Database::getInstance();
        $user = User::get_user_by_id($this->id);
        
        if (!$user || $user->type !== 'TUTOR') {
            return false;
        }
    
        $stmt = $db->prepare('
            UPDATE TUTOR 
            SET NAME = ?, 
                DATE_OF_BIRTH = ?, 
                PROFILE_IMAGE = ?, 
                DESCRIPTION = ?, 
                SCHOOL_INSTITUTION = ? 
            WHERE ID_TUTOR = ?
        ');
    
        return $stmt->execute([
            $this->name,
            $this->date_of_birth,
            $this->profile_image,
            $this->description,
            $this->school_institution,
            $user->username 
        ]);
    }
}
?>