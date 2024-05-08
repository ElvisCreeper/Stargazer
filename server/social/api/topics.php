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
function verifyUserAccount($userId, $password)
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
function getTopics($subjectId)
{
    $sth = DBHandler::getPDO()->prepare("SELECT id, Name, Description, SubjectId FROM Topic WHERE SubjectId = ?");
    $sth->execute([$subjectId]);
    return $sth->fetchAll();
}


//POST
function insertTopic()
{
    if (verifyUserAccount($_POST["userId"], $_POST["password"])) {
        $sth = DBHandler::getPDO()->prepare("CALL createTopic(?, ?, ?,?)");
        $sth->execute([htmlspecialchars($_POST["name"]), $_POST["subjectId"], htmlspecialchars($_POST["description"]), $_POST["userId"]]);
    }

    $sth = DBHandler::getPDO()->prepare('SELECT id, Name, Description, SubjectId FROM Topic WHERE id = (SELECT LAST_INSERT_ID())');
    $sth->execute();
    return $sth->fetch();
}

//PUT
function updateTopic()
{
    $sth = DBHandler::getPDO()->prepare('SELECT id, Name, Description, SubjectId FROM Topic WHERE id = ?');
    $sth->execute([$_REQUEST['topicId']]);
    $topic = $sth->fetch();
    if ($topic == null) {
        return null;
    }
    if (verifyUser($_REQUEST["userId"], $_REQUEST["password"], $_REQUEST["topicId"])) {
        $name = $topic["Name"];
        $description = $topic["Description"];
        $subject = $topic["SubjectId"];
        if (isset($_REQUEST["name"])) {
            $name = htmlspecialchars($_REQUEST["name"]);
        }
        if (isset($_REQUEST["description"])) {
            $description = htmlspecialchars($_REQUEST["description"]);
        }
        if (isset($_REQUEST["subject"])) {
            $subject = $_REQUEST["subject"];
        }
        $sth = DBHandler::getPDO()->prepare("UPDATE Topic SET Name = ?, Description = ?, SubjectId = ? WHERE id = ?");
        $sth->execute([$name, $description, $subject, $topic['id']]);
        $sth = DBHandler::getPDO()->prepare('SELECT id, Name, Description, SubjectId FROM Topic WHERE id = ?');
        $sth->execute([$_REQUEST['topicId']]);
        return $sth->fetch();
    }
    return null;
}

//DELETE
function deleteTopic()
{
    $sth = DBHandler::getPDO()->prepare('SELECT id, Name, Description, SubjectId FROM Topic WHERE id = ?');
    $sth->execute([$_REQUEST['topicId']]);
    $topic = $sth->fetch();
    if (verifyUser($_REQUEST['userId'], $_REQUEST['password'], $_REQUEST["topicId"])) {
        $sth = DBHandler::getPDO()->prepare("DELETE FROM Topic WHERE id = ?");
        $sth->execute([$_REQUEST['topicId']]);
        return $topic;
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['subjectId'])) {
        echo createResponse(200, 'Successo', getTopics($_GET['subjectId']));
    } else {
        echo createResponse(400, 'Parametro subjectId non presente');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['userId']) && isset($_POST['password']) && isset($_REQUEST['subjectId'])) {
        $topic = insertTopic();
        if ($topic) {
            echo createResponse(201, 'Topic creato con successo', $topic);
        } else {
            echo createResponse(401, 'Credenziali utente non valide');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_REQUEST['userId']) && isset($_REQUEST['password']) && isset($_REQUEST['topicId'])) {
        $topic = updateTopic();
        if ($topic) {
            echo createResponse(201, 'Topic aggiornato con successo', $topic);
        } else {
            echo createResponse(404, 'Topic non trovato');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_REQUEST['userId']) && isset($_REQUEST['password']) && isset($_REQUEST['topicId'])) {
        $topic = deleteTopic();
        if ($topic) {
            echo createResponse(200, 'Topic eliminato con successo', $topic);
        } else {
            echo createResponse(404, 'Topic non trovato');
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}