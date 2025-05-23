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

    public static function addStudentLanguage(string $studentUsername, string $language): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO STUDENT_LANGUAGE (STUDENT, LANGUAGE)
            VALUES (?, ?)
        ');
        return $stmt->execute([$studentUsername, $language]);
    }

    public static function addStudentSubject(string $studentUsername, string $subject): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO STUDENT_SUBJECT (STUDENT, SUBJECT)
            VALUES (?, ?)
        ');
        return $stmt->execute([$studentUsername, $subject]);
    }


    public static function addTutorSubject(string $tutorUsername, string $subject): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO TUTOR_SUBJECT (TUTOR, SUBJECT)
            VALUES (?, ?)
        ');
        return $stmt->execute([$tutorUsername, $subject]);
    }

    public static function addTutorLanguage(string $tutorUsername, string $language): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO TUTOR_LANGUAGE (TUTOR, LANGUAGE)
            VALUES (?, ?)
        ');
        return $stmt->execute([$tutorUsername, $language]);
    }

    public static function getTutorQualifications(string $tutorUsername): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT SUBJECT FROM TUTOR_SUBJECT WHERE TUTOR = ?');
        $stmt->execute([$tutorUsername]);
        $subjects = $stmt->fetchAll();
        $stmt = $db->prepare('SELECT LANGUAGE FROM TUTOR_LANGUAGE WHERE TUTOR = ?');
        $stmt->execute([$tutorUsername]);
        $languages = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return [
            'subjects' => $subjects,
            'languages' => $languages
        ];
    }

    public static function getStudentNeeds(string $studentUsername): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT SUBJECT FROM STUDENT_SUBJECT WHERE STUDENT = ?');
        $stmt->execute([$studentUsername]);
        $subjects = $stmt->fetchAll();
        $stmt = $db->prepare('SELECT LANGUAGE FROM STUDENT_LANGUAGE WHERE STUDENT = ?');
        $stmt->execute([$studentUsername]);
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

    public static function deleteStudentSubjects(string $username): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM STUDENT_SUBJECT WHERE STUDENT = ?');
        return $stmt->execute([$username]);
    }
    
    public static function deleteStudentLanguages(string $username): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM STUDENT_LANGUAGE WHERE STUDENT = ?');
        return $stmt->execute([$username]);
    }
    
    public static function deleteTutorSubjects(string $username): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM TUTOR_SUBJECT WHERE TUTOR = ?');
        return $stmt->execute([$username]);
    }
    
    public static function deleteTutorLanguages(string $username): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM TUTOR_LANGUAGE WHERE TUTOR = ?');
        return $stmt->execute([$username]);
    }
}
?>