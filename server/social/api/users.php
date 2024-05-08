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
function getUser($userId)
{
    $sth = DBHandler::getPDO()->prepare("SELECT id, Username, Bio, registrationDate, Image FROM User WHERE id = ?");
    $sth->execute([$userId]);
    return $sth->fetch();
}

//POST
function insertUser()
{
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    $sth = DBHandler::getPDO()->prepare('SELECT * FROM user WHERE username = ?');
    $sth->execute([$username]);

    if ($sth->rowCount() == 0) {
        $sth = DBHandler::getPDO()->prepare("INSERT INTO user(Username, Password, registrationDate)VALUES(?, ?, ?)");
        $sth->execute([$username, password_hash($password, PASSWORD_DEFAULT), date("y-m-d")]);
        $sth = DBHandler::getPDO()->prepare('SELECT id, Username, Bio, registrationDate, Image FROM user WHERE username = ?');
        $sth->execute([$username]);
        return $sth->fetch();
    }
}

//PUT
function updateUser()
{
    if (verifyUser($_REQUEST['userId'], $_REQUEST['password'])) {
        $user = getUser($_REQUEST['userId']);
        $username = $user["Username"];
        $bio = $user["Bio"];
        $image = $user["Image"];
        if (isset($_REQUEST["Username"])) {
            $username = htmlspecialchars($_REQUEST["Username"]);
        }
        if (isset($_REQUEST["Bio"])) {
            $bio = htmlspecialchars($_REQUEST["Bio"]);
        }
        if (isset($_REQUEST["Image"])) {
            $image = $_REQUEST["Image"];
        }
        $sth = DBHandler::getPDO()->prepare("UPDATE User SET Username = ?, Bio = ?, Image = ? WHERE id = ?");
        $sth->execute([$username, $bio, $image, $_REQUEST['userId']]);
        return getUser($_REQUEST['userId']);
    }
    return null;
}

//DELETE
function deleteUser()
{
    if (verifyUser($_REQUEST['userId'], $_REQUEST['password'])) {
        $user = getUser($_REQUEST['userId']);
        $sth = DBHandler::getPDO()->prepare("DELETE FROM User WHERE id = ?");
        $sth->execute([$_REQUEST['userId']]);
        return $user;
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['userId'])) {
        $user = getUser($_GET['userId']);
        if ($user) {
            echo createResponse(200, 'Successo', $user);
        } else {
            echo createResponse(404, 'Utente non trovato');
        }
    } else {
        echo createResponse(400, 'Parametro userId non presente');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_REQUEST['username']) && isset($_POST['password'])) {
        $user = insertUser();
        if ($user) {
            echo createResponse(201, 'Utente creato con successo', $user);
        } else {
            echo createResponse(401, 'Nome utente non valido');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_REQUEST['userId']) && isset($_REQUEST['password'])) {
        $user = updateUser();
        if ($user) {
            echo createResponse(201, 'Utente aggiornato con successo', $user);
        } else {
            echo createResponse(404, 'Utente non trovato');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_REQUEST['userId']) && isset($_REQUEST['password'])) {
        $user = deleteUser();
        if ($user) {
            echo createResponse(200, 'Utente eliminato con successo', $user);
        } else {
            echo createResponse(404, 'Utente non trovato');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}