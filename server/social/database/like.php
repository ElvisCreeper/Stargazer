<?php
if($_REQUEST["action"] == "like"){
    $sth = DBHandler::getPDO()->prepare("CALL likePost(?,?)");
    $sth->execute([$_SESSION['user_id'],$_REQUEST["postId"]]);
    echo "liked";
}else if($_REQUEST["action"] == "dislike"){
    $sth = DBHandler::getPDO()->prepare("CALL dislikePost(?,?)");
    $sth->execute([$_SESSION['user_id'],$_REQUEST["postId"]]);
    echo "disliked";
}
else if($_REQUEST["action"] == "unlike"){
    $sth = DBHandler::getPDO()->prepare("CALL unlikePost(?,?)");
    $sth->execute([$_SESSION['user_id'],$_REQUEST["postId"]]);
    echo "unliked";
}
else{
    echo $_REQUEST["action"];
}