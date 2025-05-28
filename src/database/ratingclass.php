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

    public function __construct($id, $tutor, $student, $rating, $comment) {
        $this->id = $id;
        $this->tutor = $tutor;
        $this->student = $student;
        $this->rating = $rating;
        $this->comment = $comment;
    }   

    
    public function create(){
        $db = Database::getInstance();
        $stmt = $db->prepare('
            INSERT INTO RATING (TUTOR, STUDENT, RATING, COMMENT) 
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([
            $this->tutor,
            $this->student,
            $this->rating,
            $this->comment
        ]);
    }

    public function update(){
        $db = Database::getInstance();
        $stmt = $db->prepare('
            UPDATE RATING 
            SET TUTOR = ?, STUDENT = ?, RATING = ?, COMMENT = ? 
            WHERE ID = ?
        ');
        $stmt->execute([
            $this->tutor,
            $this->student,
            $this->rating,
            $this->comment,
            $this->id
        ]);
    }

    public static function getRatingByTutor($tutor){
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT * FROM RATING 
            WHERE TUTOR = ?
        ');
        $stmt->execute([$tutor]);
        $ratings = [];
        while ($row = $stmt->fetch()) {
            $ratings[] = new Rating($row['ID'], $row['TUTOR'], $row['STUDENT'], $row['RATING'], $row['COMMENT']);
        }
        return $ratings;
    }

    public static function getRatingByStudent($student){
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT * FROM RATING 
            WHERE STUDENT = ?
        ');
        $stmt->execute([$student]);
        $ratings = [];
        while ($row = $stmt->fetch()) {
            $ratings[] = new Rating($row['ID'], $row['TUTOR'], $row['STUDENT'], $row['RATING'], $row['COMMENT']);
        }
        return $ratings;
    }

}


?>