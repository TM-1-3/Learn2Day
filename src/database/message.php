<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ .'/studentclass.php';
require_once __DIR__ .'/tutorclass.php';

class Message{

    public string $id;

    public string $sender;

    public string $receiver;

    public string $date_sent;

    public string $content;

    public function __construct(string $sender,
                                string $receiver, string $date_sent,
                                string $content) {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->date_sent = $date_sent;
        $this->content = $content;
    }

    public static function create(string $sender, string $receiver, string $date_sent, string $content) : Message{
        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO MESSAGE (SENDER, RECEIVER, CONTENT, TIMESTAMP) 
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([
            $sender,
            $receiver,
            $content,
            $date_sent
        ]);
        
        return new Message($sender, $receiver, $date_sent, $content);
    }

    public static function getMessagesSent(string $username): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT * FROM MESSAGE WHERE SENDER = ? ORDER BY TIMESTAMP DESC
        ');
        $stmt->execute([$username]);
        $messages = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = new Message(
                $row['SENDER'],
                $row['RECEIVER'],
                $row['TIMESTAMP'] ?? '', // Use TIMESTAMP as date_sent
                $row['CONTENT']
            );
        }
        return $messages;
    }

    public static function getMessagesReceived(string $username): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT * FROM MESSAGE WHERE RECEIVER = ? ORDER BY TIMESTAMP DESC
        ');
        $stmt->execute([$username]);
        $messages = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = new Message(
                $row['SENDER'],
                $row['RECEIVER'],
                $row['TIMESTAMP'] ?? '', // Use TIMESTAMP as date_sent
                $row['CONTENT']
            );
        }
        return $messages;
    }
    public static function countMessagesSent(string $username): int {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT COUNT(*) FROM MESSAGE WHERE SENDER = ?
        ');
        $stmt->execute([$username]);
        return (int)$stmt->fetchColumn();
    }
}



?>