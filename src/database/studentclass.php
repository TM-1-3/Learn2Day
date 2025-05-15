<?php
    declare(strict_types=1);
    require_once __DIR__ . '/../includes/database.php';

    class Student{
        public int $id;
        public string $name;
        public string $enrollment;
        public string $course;
        public string $language;
        public string $image;

        public function __construct(int $id, string $name, string $enrollment, string $course, string $language, string $image) {
            $this->id = $id;
            $this->name = $name;
            $this->enrollment = $enrollment;
            $this->course = $course;
            $this->language = $language;
            $this->image = $image;
        }

        public static function create(string $name, string $enrollment, string $course, string $language, string $image): void {
            if (empty($name) || empty($enrollment) || empty($course) || empty($language) || empty($image)) {
                throw new InvalidArgumentException("All fields are required");
            }
            $db = Database::getInstance();

            $stmt = $db->prepare('INSERT INTO STUDENTS (name, enrollment, course, language, image) VALUES (?, ?, ?, ?, ?)');

            if (!$stmt->execute([$name, $enrollment, $course, $language, $image])) {
                throw new Exception("Failed to create student profile");
            }
        }

        public static function get_student_by_id(int $id): ?array {
            if (empty($id)) {
                throw new InvalidArgumentException("ID cannot be empty");
            }

            $db = Database::getInstance();
            $stmt = $db->prepare('SELECT * FROM STUDENTS WHERE id = ?');
            $stmt->execute([$id]);

            return $stmt->fetch() ?: null;
        }

        public static function get_user_by_id(int $id): ?array{
            if (empty($id)) {
                throw new InvalidArgumentException("ID cannot be empty");
            }
            
            $db = Database::getInstance();
            $stmt = $db->prepare('SELECT * FROM USERS WHERE id = ?');
            $stmt->execute([$id]);

            return $stmt->fetch() ?: null;
        }
    }

?>