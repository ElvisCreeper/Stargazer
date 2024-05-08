<?php
// read pages.json into $json which is a string
$json = file_get_contents('../include/pages.json');

// get the name of the current page
$pageName = basename($_SERVER['PHP_SELF']);

$obj = json_decode($json);



// in_array(el, arr) checks if el is in array arr
if (in_array($pageName, $obj->loggedInPages)) {
    require '../include/loggedin.php';
}

if (in_array($pageName, $obj->userpages)) {
    require '../include/header.php';
}


if (in_array($pageName, $obj->DBPages)) {
    require '../database/DBHandler.php';
}

if (in_array($pageName, $obj->APIPages)) {
    require '../include/APIinclude.php';
}
