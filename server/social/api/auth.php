<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['username']) && isset($_GET['password'])) { {
            $password = htmlspecialchars($_GET['password']);

            $sth = DBHandler::getPDO()->prepare("SELECT * FROM user WHERE username = ?");
            $sth->execute([$_GET['username']]);

            if ($sth->rowCount() > 0) {
                $rows = $sth->fetchAll();
                $cryptedPassword = $rows[0]['Password'];
                if (password_verify($password, $cryptedPassword)) {
                    $sth = DBHandler::getPDO()->prepare("SELECT id, Username, Bio, registrationDate, Image FROM User WHERE id = ?");
                    $sth->execute([$rows[0]['id']]);
                    echo createResponse(200, 'Successo', $sth->fetch());
                } else {
                    echo createResponse(401, 'Credenziali utente non valide');
                }
            } else {
                echo createResponse(404, 'Utente non trovato');
            }
        }
    } else {
        echo createResponse(400, 'Richiesta non valida');
    }
}

