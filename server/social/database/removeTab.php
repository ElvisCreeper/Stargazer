<?php
$sth = DBHandler::getPDO()->prepare("SELECT topicId FROM Tab WHERE id = ?");
$sth->execute([$_REQUEST["tabId"]]); 
$topic = $sth->fetch()["topicId"];
$sth = DBHandler::getPDO()->prepare("DELETE FROM Tab WHERE id = ?");
$sth->execute([$_REQUEST["tabId"]]); 
header("Location:../userpages/editTopic.php?topicID=$topic");