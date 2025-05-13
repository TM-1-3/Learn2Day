#NOTE THIS MIGHT BE USELESS

<?php 
    function connection(){
        return new PDO('sqlite:../docs/learn2day.db');
    }

    function getStudents($db){
        $query = "SELECT * FROM STUDENT";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function getTutors($db){
        $query = "SELECT * FROM TUTOR";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
?>
