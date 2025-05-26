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

    public function __construct(
        string $usernametutor,
        string $usernamestudent,
        bool $accepted,
        string $date_sent,
        string $message
    ) {
        $this->usernametutor = $usernametutor;
        $this->usernamestudent = $usernamestudent;
        $this->accepted = $accepted;
        $this->date_sent = $date_sent;
        $this->message = $message;
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

    public function reject(): void {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            UPDATE REQUEST 
            SET ACCEPTED = ?, DATE_ACCEPTED = ? 
            WHERE STUDENT = ? AND TUTOR = ?
        ');
        $stmt->execute([
            false,
            date('Y-m-d H:i:s'),
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
                $row['MESSAGE']
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
                $row['MESSAGE']
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
}

