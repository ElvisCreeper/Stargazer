<?php
function verifyUser($userId, $password, $topicId)
{
    $password = htmlspecialchars($password);

    $sth = DBHandler::getPDO()->prepare("SELECT * FROM user WHERE id = ?");
    $sth->execute([$userId]);

    if ($sth->rowCount() > 0) {
        $rows = $sth->fetchAll();
        $cryptedPassword = $rows[0]['Password'];
        if (password_verify($password, $cryptedPassword)) {
            $sth = DBHandler::getPDO()->prepare("SELECT * FROM user_topic WHERE UserId = ? AND TopicId= ?");
            $sth->execute([$userId, $topicId]);
            if ($sth->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}
//GET
function getTabs($topicId)
{
    $sth = DBHandler::getPDO()->prepare("SELECT id, Name, Description, TopicId FROM Tab WHERE TopicId = ?");
    $sth->execute([$topicId]);
    return $sth->fetchAll();
}


//POST
function insertTab()
{
    if (verifyUser($_POST["userId"], $_POST["password"], $_POST["topicId"])) {
        $name = htmlspecialchars($_REQUEST["name"]);
        $description = htmlspecialchars($_REQUEST["description"]);
        $topic = $_REQUEST["topicId"];

        $sth = DBHandler::getPDO()->prepare("INSERT INTO Tab(Name, Description, TopicId)VALUES(?, ?, ?)");
        $sth->execute([$name, $description, $topic]);
    }

    $sth = DBHandler::getPDO()->prepare('SELECT id, Name, Description, TopicId FROM Tab WHERE id = (SELECT LAST_INSERT_ID())');
    $sth->execute();
    return $sth->fetch();
}

//PUT
function updateTab()
{
    $sth = DBHandler::getPDO()->prepare('SELECT id, Name, Description, TopicId FROM Tab WHERE id = ?');
    $sth->execute([$_REQUEST['tabId']]);
    $tab = $sth->fetch();
    if ($tab == null) {
        return null;
    }
    if (verifyUser($_REQUEST["userId"], $_REQUEST["password"], $tab["TopicId"])) {
        $name = $tab["Name"];
        $description = $tab["Description"];
        if (isset($_REQUEST["name"])) {
            $name = htmlspecialchars($_REQUEST["name"]);
        }
        if (isset($_REQUEST["description"])) {
            $description = htmlspecialchars($_REQUEST["description"]);
        }
        $sth = DBHandler::getPDO()->prepare("UPDATE Tab SET Name = ?, Description = ? WHERE id = ?");
        $sth->execute([$name, $description, $_REQUEST['tabId']]);
        $sth = DBHandler::getPDO()->prepare('SELECT id, Name, Description, TopicId FROM Tab WHERE id = ?');
        $sth->execute([$_REQUEST['tabId']]);
        return $sth->fetch();
    }
    return null;
}

//DELETE
function deleteTab()
{
    $sth = DBHandler::getPDO()->prepare('SELECT id, Name, Description, TopicId FROM Tab WHERE id = ?');
    $sth->execute([$_REQUEST['tabId']]);
    $tab = $sth->fetch();
    if (verifyUser($_REQUEST['userId'], $_REQUEST['password'], $tab["TopicId"])) {
        
        $sth = DBHandler::getPDO()->prepare("DELETE FROM Tab WHERE id = ?");
        $sth->execute([$_REQUEST['tabId']]);
        return $tab;
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['topicId'])) {
        echo createResponse(200, 'Successo', getTabs($_GET['topicId']));
    } else {
        echo createResponse(400, 'Parametro topicId non presente');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['userId']) && isset($_POST['password']) && isset($_POST['topicId'])) {
        $tab = insertTab();
        if ($tab) {
            echo createResponse(201, 'Tab creata con successo', $tab);
        } else {
            echo createResponse(401, 'Credenziali utente non valide');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_REQUEST['userId']) && isset($_REQUEST['password']) && isset($_REQUEST['tabId'])) {
        $tab = updateTab();
        if ($tab) {
            echo createResponse(201, 'Tab aggiornata con successo', $tab);
        } else {
            echo createResponse(404, 'Tab non trovata');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_REQUEST['userId']) && isset($_REQUEST['password']) && isset($_REQUEST['tabId'])) {
        $tab = deleteTab();
        if ($tab) {
            echo createResponse(200, 'Tab eliminata con successo', $tab);
        } else {
            echo createResponse(404, 'Tab non trovata');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}