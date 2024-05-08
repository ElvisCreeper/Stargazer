<?php
session_start();
require_once('DBHandler.php');
$username = htmlspecialchars($_POST['username']);
$password = htmlspecialchars($_POST['password']);

$sth = DBHandler::getPDO()->prepare('SELECT * FROM user WHERE username = ?');
$sth->execute([$username]);

if ($sth->rowCount() == 0) {
    $sth = DBHandler::getPDO()->prepare("INSERT INTO user(Username, Password, registrationDate)VALUES(?, ?, ?)");

    $sth->execute([$username, password_hash($password, PASSWORD_DEFAULT), date("y-m-d")]);
    $sth = DBHandler::getPDO()->prepare("SELECT * FROM user WHERE Username=?");
    $sth->execute([$username]);
    if ($sth->rowCount() > 0) {$rows = $sth->fetchAll();
        if (password_verify($password, $rows[0]['Password'])) {
            $_SESSION['user_id'] = $rows[0]['id'];
            $_SESSION['username'] = $rows[0]['Username'];
            $_SESSION['pfp'] = $rows[0]['Image'];
            header('Location:../index.php');
            exit();
        }
    }
}
header('Location:../userpages/registration.php');
