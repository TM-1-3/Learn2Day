<?php

declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/studentclass.php';
require_once __DIR__ . '/tutorclass.php';

class Qualifications {
    public static function getAllSubjects(): array {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare('SELECT DESIGNATION FROM SUBJECT ORDER BY DESIGNATION');
            if (!$stmt->execute()) {
                error_log("Database error: " . implode(":", $stmt->errorInfo()));
                return [];
            }
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_column($results, 'DESIGNATION');
        } catch (PDOException $e) {
            error_log("Database exception: " . $e->getMessage());
            return [];
        }   
    }

    public static function getAllLanguages(): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT DESIGNATION FROM LANGUAGE ORDER BY DESIGNATION');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public static function getAllDegrees(): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT DESIGNATION FROM DEGREE ORDER BY DESIGNATION');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public static function addStudentLanguage(int $studentId, string $language): bool {
        $user = User::get_user_by_id($studentId);
        if (!$user || $user->type !== 'STUDENT') {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO STUDENT_LANGUAGE (STUDENT, LANGUAGE)
            VALUES (?, ?)
        ');
        return $stmt->execute([$user->username, $language]);
    }

    public static function addStudentSubject(int $studentId, string $subject, int $grade): bool {
        $user = User::get_user_by_id($studentId);
        if (!$user || $user->type !== 'STUDENT') {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO STUDENT_SUBJECT (STUDENT, SUBJECT, GRADE)
            VALUES (?, ?, ?)
        ');
        return $stmt->execute([$user->username, $subject, $grade]);
    }

    public static function addTutorDegree(int $tutorId, string $degree, string $university): bool {
        $user = User::get_user_by_id($tutorId);
        if (!$user || $user->type !== 'TUTOR') {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO TUTOR_DEGREE (TUTOR, DEGREE, UNIVERSITY)
            VALUES (?, ?, ?)
        ');
        return $stmt->execute([$user->username, $degree, $university]);
    }

    public static function getTutorQualifications(int $tutorId): array {
        $user = User::get_user_by_id($tutorId);
        if (!$user || $user->type !== 'TUTOR') {
            return [];
        }

        $db = Database::getInstance();
        
        $stmt = $db->prepare('SELECT SUBJECT, GRADE FROM TUTOR_SUBJECT WHERE TUTOR = ?');
        $stmt->execute([$user->username]);
        $subjects = $stmt->fetchAll();
        
        $stmt = $db->prepare('SELECT DEGREE, UNIVERSITY FROM TUTOR_DEGREE WHERE TUTOR = ?');
        $stmt->execute([$user->username]);
        $degrees = $stmt->fetchAll();
        
        $stmt = $db->prepare('SELECT LANGUAGE FROM TUTOR_LANGUAGE WHERE TUTOR = ?');
        $stmt->execute([$user->username]);
        $languages = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        return [
            'subjects' => $subjects,
            'degrees' => $degrees,
            'languages' => $languages
        ];
    }

    public static function getStudentNeeds(int $studentId): array {
        $user = User::get_user_by_id($studentId);
        if (!$user || $user->type !== 'STUDENT') {
            return [];
        }

        $db = Database::getInstance();
        
        $stmt = $db->prepare('SELECT SUBJECT, GRADE FROM STUDENT_SUBJECT WHERE STUDENT = ?');
        $stmt->execute([$user->username]);
        $subjects = $stmt->fetchAll();
        
        $stmt = $db->prepare('SELECT LANGUAGE FROM STUDENT_LANGUAGE WHERE STUDENT = ?');
        $stmt->execute([$user->username]);
        $languages = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        return [
            'subjects' => $subjects,
            'languages' => $languages
        ];
    }

    public static function getAllTutorLevels(): array {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare('SELECT DESIGNATION FROM TUTOR_LEVEL ORDER BY DESIGNATION');
            if (!$stmt->execute()) {
                error_log("Database error: " . implode(":", $stmt->errorInfo()));
                return [];
            }
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } catch (PDOException $e) {
            error_log("Database exception: " . $e->getMessage());
            return [];
        }
    }

    public static function getAllStudentLevels(): array {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare('SELECT DESIGNATION FROM STUDENT_LEVEL ORDER BY DESIGNATION');
            if (!$stmt->execute()) {
                error_log("Database error: " . implode(":", $stmt->errorInfo()));
                return [];
            }
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } catch (PDOException $e) {
            error_log("Database exception: " . $e->getMessage());
            return [];
        }
    }
}
?>