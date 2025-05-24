<?php
function connection()
{
    return new PDO('sqlite:../docs/learn2day.db');
}

function getStudents($db)
{
    $query = "SELECT * FROM STUDENT";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getTutors($db)
{
    $query = "SELECT * FROM TUTOR";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}


function getTutorByUsername($db, $username)
{
    $query = "SELECT * FROM TUTOR WHERE ID_TUTOR = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$username]);
    return $stmt->fetch();
}

function getStudentByUsername($db, $username)
{
    $query = "SELECT * FROM STUDENT WHERE ID_STUDENT = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$username]);
    return $stmt->fetch();
}
