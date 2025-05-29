<?php 
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ .'/studentclass.php';
require_once __DIR__ .'/tutorclass.php';


class Rating{

    public string $id;

    public string $tutor;

    public string $student;

    public string $rating;

    public string $comment;

    public string $created_at;

    public function __construct($id, $tutor, $student, $rating, $comment, $created_at) {
        $this->id = $id !== null ? (string)$id : '';
        $this->tutor = $tutor;
        $this->student = $student;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->created_at = $created_at;
    }   

    
    public function create(){
        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO RATING (TUTOR, STUDENT, RATING, COMMENT, TIMESTAMP) 
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $this->tutor,
            $this->student,
            $this->rating,
            $this->comment,
            $this->created_at
        ]);
    }

    public function update(){
        $db = Database::getInstance();
        $stmt = $db->prepare('
            UPDATE RATING 
            SET TUTOR = ?, STUDENT = ?, RATING = ?, COMMENT = ?, TIMESTAMP = ?
            WHERE ID_RATING = ?
        ');
        $stmt->execute([
            $this->tutor,
            $this->student,
            $this->rating,
            $this->comment,
            $this->id,
            $this->created_at
        ]);
    }

    public static function getRatingByTutor($tutor){
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT * FROM RATING 
            WHERE TUTOR = ?
            ORDER BY "TIMESTAMP" DESC
        ');
        $stmt->execute([$tutor]);
        $ratings = [];
        while ($row = $stmt->fetch()) {
            $ratings[] = new Rating($row['ID_RATING'], $row['TUTOR'], $row['STUDENT'], $row['RATING'], $row['COMMENT'], $row['TIMESTAMP'] ?? '');
        }
        return $ratings;
    }

    public static function getRatingByStudent($student){
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT * FROM RATING 
            WHERE STUDENT = ?
            ORDER BY TIMESTAMP DESC
        ');
        $stmt->execute([$student]);
        $ratings = [];
        while ($row = $stmt->fetch()) {
            $ratings[] = new Rating($row['ID_RATING'], $row['TUTOR'], $row['STUDENT'], $row['RATING'], $row['COMMENT'], $row['TIMESTAMP'] ?? '');
        }
        return $ratings;
    }

    public static function deleteById($id) {
        $db = Database::getInstance();
        $stmt = $db -> prepare('
            DELETE FROM RATING
            WHERE ID_RATING = ?
        ');
        $stmt->execute([$id]);
    }

}


?>