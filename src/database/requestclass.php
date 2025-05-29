<?php

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ .'/studentclass.php';
require_once __DIR__ .'/tutorclass.php';


class Request{
    public string $usernametutor;
    public string $usernamestudent;

    public bool $accepted;
    
    public string $date_sent;
    public string $date_accepted;

    public string $message;

    public ?int $id = null;

    public function __construct(
        string $usernametutor,
        string $usernamestudent,
        bool $accepted,
        string $date_sent,
        string $message,
        ?int $id = null
    ) {
        $this->usernametutor = $usernametutor;
        $this->usernamestudent = $usernamestudent;
        $this->accepted = $accepted;
        $this->date_sent = $date_sent;
        $this->message = $message;
        $this->id = $id;
    }
    
    public function create(): void {
        $db = Database::getInstance();
        // Check if a request already exists for this student-tutor pair
        $stmt = $db->prepare('
            SELECT COUNT(*) FROM REQUEST WHERE STUDENT = ? AND TUTOR = ?
        ');
        $stmt->execute([
            $this->usernamestudent,
            $this->usernametutor
        ]);
        $exists = $stmt->fetchColumn();
        if ($exists > 0) {
            header('Location: /profile.php?id=' . $this->usernametutor);
            throw new Exception('A request already exists for this student and tutor.');
        }
        $stmt = $db->prepare('
            INSERT INTO REQUEST 
            (STUDENT, TUTOR, REQUEST_DATE, MESSAGE) 
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([
            $this->usernamestudent,
            $this->usernametutor,
            $this->date_sent,
            $this->message
        ]);
    }

    public function accept(): void{
        $db = Database::getInstance();
        $stmt = $db->prepare('
            UPDATE REQUEST 
            SET ACCEPTED = ?, DATE_ACCEPTED = ? 
            WHERE STUDENT = ? AND TUTOR = ?
        ');
        $stmt->execute([
            true,
            date('Y-m-d H:i:s'),
            $this->usernamestudent,
            $this->usernametutor
        ]);

        // Only insert if not already present
        $stmt = $db->prepare('SELECT COUNT(*) FROM STUDENT_TUTOR WHERE STUDENT = ? AND TUTOR = ?');
        $stmt->execute([$this->usernamestudent, $this->usernametutor]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $db->prepare('
                INSERT INTO STUDENT_TUTOR
                (STUDENT, TUTOR)
                VALUES (?, ?)
            ');
            $stmt->execute([
                $this->usernamestudent,
                $this->usernametutor
            ]);
        }
    }

    public function deny(): void {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            DELETE FROM REQUEST 
            WHERE STUDENT = ? AND TUTOR = ?
        ');
        $stmt->execute([
            $this->usernamestudent,
            $this->usernametutor
        ]);
    }

    public static function getByStudent(string $username): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM REQUEST WHERE STUDENT = ?');
        $stmt->execute([$username]);
        $requests = [];
        while ($row = $stmt->fetch()) {
            $requests[] = new Request(
                $row['TUTOR'],
                $row['STUDENT'],
                (bool)$row['ACCEPTED'],
                $row['REQUEST_DATE'],
                $row['MESSAGE'],
                $row['ID_REQUEST'] ?? null
            );
        }
        return $requests;
    }

    public static function getByTutor(string $username): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM REQUEST WHERE TUTOR = ?');
        $stmt->execute([$username]);
        $requests = [];
        while ($row = $stmt->fetch()) {
            $requests[] = new Request(
                $row['TUTOR'],
                $row['STUDENT'],
                (bool)$row['ACCEPTED'],
                $row['REQUEST_DATE'],
                $row['MESSAGE'],
                $row['ID_REQUEST'] ?? null
            );
        }
        return $requests;
    }

    public static function exists(string $usernamestudent, string $usernametutor): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT COUNT(*) FROM REQUEST 
            WHERE STUDENT = ? AND TUTOR = ?
        ');
        $stmt->execute([$usernamestudent, $usernametutor]);
        return $stmt->fetchColumn() > 0;
    }

    public static function isApproved(string $usernamestudent, string $usernametutor): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT COUNT(*) FROM REQUEST 
            WHERE STUDENT = ? AND TUTOR = ? AND ACCEPTED = 1
        ');
        $stmt->execute([$usernamestudent, $usernametutor]);
        return $stmt->fetchColumn() > 0;
    }

    public static function getById($id): ?Request {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM REQUEST WHERE ID_REQUEST = ?');
        $stmt->execute([$id]);
        if ($row = $stmt->fetch()) {
            $req = new Request(
                $row['TUTOR'],
                $row['STUDENT'],
                (bool)$row['ACCEPTED'],
                $row['REQUEST_DATE'],
                $row['MESSAGE'],
                $row['ID_REQUEST']
            );
            return $req;
        }
        return null;
    }

    public static function countAllRequestsA() {
        $db = Database::getInstance();
        $stmt = $db->query('
            SELECT COUNT(*) FROM REQUEST
            WHERE ACCEPTED = 1
        ');
        return (int)$stmt->fetchColumn();
    }

    public static function countAllRequestsP() {
        $db = Database::getInstance();
        $stmt = $db->query('
            SELECT COUNT(*) FROM REQUEST
            WHERE ACCEPTED = 0
        ');
        return (int)$stmt->fetchColumn();
    }

}

