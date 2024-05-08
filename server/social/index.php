<?php
session_start();
if(isset($_SESSION["user_id"])){
    header("Location: ./userpages/homepage.php");
}
else{
header("Location: ./userpages/loginForm.php");
}
exit();