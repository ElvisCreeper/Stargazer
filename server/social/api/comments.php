<?php

function verifyUser($userId, $password)
{
    $password = htmlspecialchars($password);

    $sth = DBHandler::getPDO()->prepare("SELECT * FROM user WHERE id = ?");
    $sth->execute([$userId]);

    if ($sth->rowCount() > 0) {
        $rows = $sth->fetchAll();
        $cryptedPassword = $rows[0]['Password'];
        if (password_verify($password, $cryptedPassword)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
//GET
function getComments($userId, $postId)
{
    $sth = DBHandler::getPDO()->prepare("CALL getComments(?,?)");
    $sth->execute([$userId, $postId]);
    return $sth->fetchall();
}

//POST
function insertComment()
{
    if (verifyUser($_REQUEST["userId"], $_REQUEST["password"])) {
        $title = $_REQUEST["title"];
        $text = $_REQUEST["text"];
        $user = $_REQUEST["userId"];
        $tab = $_REQUEST["tabId"];
        $replyId = $_REQUEST["replyId"];
        $sth = DBHandler::getPDO()->prepare("INSERT INTO Post(Title, Body, Time, Edited, TabID, UserId, ReplyID)VALUES(?, ?, ?, FALSE, ?, ?, ?)");
        $sth->execute([htmlspecialchars($title), $text, date("Y-m-d h:i:s"), $tab, $user, $replyId]);
        $sth = DBHandler::getPDO()->prepare('CALL getPost(?,(SELECT LAST_INSERT_ID()))');
        $sth->execute([$user]);
        return $sth->fetch();
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['postId'])) {
        $comments = getComments($_GET['userId'], $_GET['postId']);
        echo createResponse(200, 'Successo', $comments);
    } else {
        echo createResponse(400, 'Parametro postId non presente');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_REQUEST['userId']) && isset($_POST['password']) && isset($_POST['tabId']) && isset($_POST['replyId'])) {
        $comment = insertComment();
        if ($comment) {
            echo createResponse(201, 'Commento creato con successo', $comment);
        } else {
            echo createResponse(401, 'Credenziali utente non valide');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}