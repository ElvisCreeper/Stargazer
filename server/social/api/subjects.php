<?php
//GET
function getSubject($subjectId)
{
    $sth = DBHandler::getPDO()->prepare("SELECT id, Name FROM Subject WHERE id = ?");
    $sth->execute([$subjectId]);
    return $sth->fetch();
}

function getSubjects()
{
    $sth = DBHandler::getPDO()->prepare("SELECT id, Name FROM Subject");
    $sth->execute();
    return $sth->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['subjectId'])) {
        $subject = getSubject($_GET['subjectId']);
        if ($subject) {
            echo createResponse(200, 'Successo', $subject);
        } else {
            echo createResponse(404, 'Subject non trovata');
        }
    } else {
        echo createResponse(200, 'Successo', getSubjects());
    }
}