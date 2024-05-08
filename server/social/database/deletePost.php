<?php
$sth = DBHandler::getPDO()->prepare("CALL deletePost(?,?)");
$sth->execute([$_SESSION["user_id"],$_REQUEST["postId"]]); 