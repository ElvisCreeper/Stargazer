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


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_REQUEST['userId']) && isset($_POST['password']) && isset($_POST['postId'])) {
        if (verifyUser($_REQUEST['userId'], $_REQUEST['password'])) {
            if ($_REQUEST["action"] == "like") {
                $sth = DBHandler::getPDO()->prepare("CALL likePost(?,?)");
                $sth->execute([$_REQUEST['userId'], $_REQUEST["postId"]]);
                $sth = DBHandler::getPDO()->prepare('CALL getPost(?,?)');
                $sth->execute([$_REQUEST['userId'], $_REQUEST["postId"]]);
                $post = $sth->fetch();
                echo createResponse(200, 'Successo', $post);
            } else if ($_REQUEST["action"] == "dislike") {
                $sth = DBHandler::getPDO()->prepare("CALL dislikePost(?,?)");
                $sth->execute([$_REQUEST['userId'], $_REQUEST["postId"]]);
                $sth = DBHandler::getPDO()->prepare('CALL getPost(?,?)');
                $sth->execute([$_REQUEST['userId'], $_REQUEST["postId"]]);
                $post = $sth->fetch();
                echo createResponse(200, 'Successo', $post);
            } else if ($_REQUEST["action"] == "unlike") {
                $sth = DBHandler::getPDO()->prepare("CALL unlikePost(?,?)");
                $sth->execute([$_REQUEST['userId'], $_REQUEST["postId"]]);
                $sth = DBHandler::getPDO()->prepare('CALL getPost(?,?)');
                $sth->execute([$_REQUEST['userId'], $_REQUEST["postId"]]);
                $post = $sth->fetch();
                echo createResponse(200, 'Successo', $post);
            } else {
                echo createResponse(402, 'Parametro action non valido');
            }
        } else {
            echo createResponse(401, 'Credenziali utente non valide');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}



