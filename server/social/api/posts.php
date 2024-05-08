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
function verifyAuthor($userId, $password, $postId)
{
    $password = htmlspecialchars($password);

    $sth = DBHandler::getPDO()->prepare("SELECT * FROM user WHERE id = ?");
    $sth->execute([$userId]);

    if ($sth->rowCount() > 0) {
        $rows = $sth->fetchAll();
        $cryptedPassword = $rows[0]['Password'];
        if (password_verify($password, $cryptedPassword)) {
            $sth = DBHandler::getPDO()->prepare("SELECT * FROM post WHERE id= ?");
            $sth->execute([$postId]);
            if ($sth->rowCount() > 0) {
                if ($sth->fetch()["UserId"] == $userId) {
                    return true;
                }
            }
        }
    }
    return false;
}

//GET
function getPosts($userId, $tabId)
{
    $sth = DBHandler::getPDO()->prepare("CALL getPosts(?,?)");
    $sth->execute([$userId, $tabId]);
    return $sth->fetchall();
}

//POST
function insertPost()
{
    if (verifyUser($_REQUEST["userId"], $_REQUEST["password"])) {
        $title = $_REQUEST["title"];
        $text = $_REQUEST["text"];
        $user = $_REQUEST["userId"];
        $tab = $_REQUEST["tabId"];
        $replyId = NULL;
        $sth = DBHandler::getPDO()->prepare("INSERT INTO Post(Title, Body, Time, Edited, TabID, UserId, ReplyID)VALUES(?, ?, ?, FALSE, ?, ?, ?)");
        $sth->execute([htmlspecialchars($title), $text, date("Y-m-d h:i:s"), $tab, $user, $replyId]);
        $sth = DBHandler::getPDO()->prepare('CALL getPost(?,(SELECT LAST_INSERT_ID()))');
        $sth->execute([$user]);
        return $sth->fetch();
    }
    return null;
}

//PUT
function updatePost()
{
    if (verifyAuthor($_REQUEST['userId'], $_REQUEST['password'], $_REQUEST['postId'])) {
        $user = $_REQUEST["userId"];
        $postId = $_REQUEST["postId"];
        $sth = DBHandler::getPDO()->prepare('CALL getPost(?,?)');
        $sth->execute([$user, $postId]);
        $post = $sth->fetch();
        if ($post == null) {
            return null;
        }
        $title = $post["title"];
        $text = $post["body"];
        if (isset($_REQUEST["title"])) {
            $title = htmlspecialchars($_REQUEST["title"]);
        }
        if (isset($_REQUEST["text"])) {
            $text = $_REQUEST["text"];
        }
        $sth = DBHandler::getPDO()->prepare("CALL updatePost(?,?,?,?,?)");
        $sth->execute([$user, $postId, $title, $text, date("Y-m-d h:i:s")]);
        $sth = DBHandler::getPDO()->prepare('CALL getPost(?,?)');
        $sth->execute([$user, $postId]);
        return $sth->fetch();
    }
    return null;
}

//DELETE
function deleteUser()
{
    if (verifyAuthor($_REQUEST['userId'], $_REQUEST['password'], $_REQUEST['postId'])) {
        $sth = DBHandler::getPDO()->prepare('CALL getPost(?,?)');
        $sth->execute([$_REQUEST['userId'], $_REQUEST['postId']]);
        $post = $sth->fetch();
        $sth = DBHandler::getPDO()->prepare("CALL deletePost(?,?)");
        $sth->execute([$_REQUEST["userId"], $_REQUEST["postId"]]);
        return $post;
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['tabId'])) {
        $posts = getPosts($_GET['userId'], $_GET['tabId']);
        echo createResponse(200, 'Successo', $posts);
    } else {
        echo createResponse(400, 'Parametro tabId non presente');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_REQUEST['userId']) && isset($_POST['password']) && isset($_POST['tabId'])) {
        $post = insertPost();
        if ($post) {
            echo createResponse(201, 'Post creato con successo', $post);
        } else {
            echo createResponse(401, 'Credenziali utente non valide');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_REQUEST['userId']) && isset($_REQUEST['password']) && isset($_REQUEST['postId'])) {
        $post = updatePost();
        if ($post) {
            echo createResponse(201, 'Post modificato con successo', $post);
        } else {
            echo createResponse(404, 'Post non trovato');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_REQUEST['userId']) && isset($_REQUEST['password']) && isset($_REQUEST['postId'])) {
        $post = deleteUser();
        if ($post) {
            echo createResponse(200, 'Post eliminato con successo', $post);
        } else {
            echo createResponse(404, 'Post non trovato');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}