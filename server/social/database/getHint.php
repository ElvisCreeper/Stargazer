<?php
// get the q parameter from URL
$q = $_REQUEST["q"];

$queryStringSelect = "SELECT * FROM Subject WHERE Name LIKE :q LIMIT 3";

$sth = DBHandler::getPDO()->prepare($queryStringSelect);
$sth->bindValue(':q', $q . '%', PDO::PARAM_STR);
$sth->execute();

$result = $sth->fetchAll();

$hint = "";

foreach ($result as $row) {
    $hint .= "<li class='m-0'><a class='dropdown-item d-flex gap-2 align-items-center' href='../userpages/subject.php?id={$row['id']}'><i class='bx bxs-trash'></i>{$row["Name"]}</a></li>";
}




$queryStringSelect = "SELECT * FROM Topic WHERE Name LIKE :q LIMIT 3";
$sth = DBHandler::getPDO()->prepare($queryStringSelect);
$sth->bindValue(':q', $q . '%', PDO::PARAM_STR);
$sth->execute();
if($sth->rowCount()>0 && $hint != ""){
    $hint .= "<hr>";
}
$result =  $sth->fetchAll();
foreach ($result as $row) {
    $hint .= "<li class='m-0'><a class='dropdown-item d-flex gap-2 align-items-center' href='../userpages/topic.php?id={$row['id']}'><i class='bx bxs-trash'></i>{$row["Name"]}</a></li>";
}

$queryStringSelect = "SELECT * FROM User WHERE Username LIKE :q LIMIT 3";
$sth = DBHandler::getPDO()->prepare($queryStringSelect);
$sth->bindValue(':q', $q . '%', PDO::PARAM_STR);
$sth->execute();
if($sth->rowCount()>0 && $hint != ""){
    $hint .= "<hr>";
}
$result =  $sth->fetchAll();
foreach ($result as $row) {
    $hint .= "<li class='m-0'><a class='dropdown-item d-flex gap-2 align-items-center' href='../userpages/user.php?id={$row['id']}'><i class='bx bxs-user'></i>{$row["Username"]}</a></li>";
}



echo $hint;

