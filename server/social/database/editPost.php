<?php
$title = $_REQUEST["title"];
$text = $_REQUEST["text"];
$user = $_SESSION["user_id"];
$postId  = $_REQUEST["postID"];
$sth = DBHandler::getPDO()->prepare("CALL updatePost(?,?,?,?,?)");
$sth->execute([$user, $postId, htmlspecialchars($title), $text,  date("Y-m-d h:i:s")]); 