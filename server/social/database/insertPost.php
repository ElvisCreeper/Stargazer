<?php
$title = $_REQUEST["title"];
$text = $_REQUEST["text"];
$user = $_SESSION["user_id"];
$tab = $_REQUEST["tab"];
$replyId = NULL;
if(isset($_REQUEST["replyID"])){
    $replyId = $_REQUEST["replyID"];
}
$sth = DBHandler::getPDO()->prepare("INSERT INTO Post(Title, Body, Time, Edited, TabID, UserId, ReplyID)VALUES(?, ?, ?, FALSE, ?, ?, ?)");
$sth->execute([htmlspecialchars($title), $text,  date("Y-m-d h:i:s"), $tab, $user, $replyId]); 