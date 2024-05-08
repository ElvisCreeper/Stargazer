<?php
session_start();
require_once('DBHandler.php');
$username = htmlspecialchars($_POST['username']);
$password = htmlspecialchars($_POST['password']);

$sth = DBHandler::getPDO()->prepare("SELECT * FROM user WHERE Username = ?");
$sth->execute([$username]);

if ($sth->rowCount() > 0) {
    $rows = $sth->fetchAll();
    $cryptedPassword = $rows[0]['Password'];
    if (password_verify($password, $cryptedPassword)) {
        $_SESSION['user_id'] = $rows[0]['id'];
        $_SESSION['username'] = $rows[0]['Username'];
        $_SESSION['pfp'] = $rows[0]['Image'];
        header('Location:../userpages/homepage.php');
    } else {
        header('Location:../index.php');
    }
} else {
    header('Location:../index.php');
}