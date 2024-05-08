<?php
//planet
$topicID = $_REQUEST["topic"];
$_SESSION["topicID"] = $topicID;
$_SESSION["topicName"] = $_REQUEST["topicName"];
$sth = DBHandler::getPDO()->prepare("SELECT Name, id FROM Tab WHERE TopicId = ?");
$sth->execute([$topicID]);
$orbitSize = "35%";
$planetSize = "17%";
$orbitValue = 35;
$planetValue = 17;
$z = $sth->rowCount();
$topic = $_REQUEST["topicName"];
echo "<div id='star' class='ratio ratio-1x1'>$topic</div>";
foreach ($sth->fetchAll() as $row) {
    echo "<div class='orbit planet-orbit ratio ratio-1x1' style='width: $orbitSize;z-index: $z;'></div>";
    echo "<div class='planet-spin ratio ratio-1x1' style='width: $orbitSize;z-index: $z;'>";

    echo "<div id ='planet' class='ratio ratio-1x1' style='width: $planetSize;z-index: $z;'>";
    echo "<input class='spinning-content' type='button' onclick='getPosts(this.value, {$row["id"]})' value='{$row["Name"]}'/>";
    echo "</div></div>";
    $orbitValue += 20;
    $orbitSize = $orbitValue . "%";
    $planetValue += 5;
    $planetSize = $planetValue . "%";
    --$z;
}
$sth = DBHandler::getPDO()->prepare("SELECT UserId FROM user_topic WHERE TopicId = ? AND UserId = ?");
$sth->execute([$topicID, $_SESSION["user_id"]]);
if ($sth->rowCount() > 0) {
    echo "<a type='button'
    class='bx bxs-edit btn btn-primary position-absolute bottom-0 end-0 rounded-circle m-5 p-3' href='editTopic.php'>
    </a>";
}
