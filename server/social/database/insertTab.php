<?php
$name = $_REQUEST["name"];
$description = $_REQUEST["description"];
$topic = $_REQUEST["topicId"];

$sth = DBHandler::getPDO()->prepare("INSERT INTO Tab(Name, Description, TopicId)VALUES(?, ?, ?)");
$sth->execute([$name, $description, $topic]); 
header("Location:../userpages/editTopic.php?topicID=$topic");