<?php
$sth = DBHandler::getPDO()->prepare("SELECT Subject.Name AS subjectName, Subject.id AS subjectId, Topic.Name AS topicName, Topic.id AS topicId FROM Topic INNER JOIN SUBJECT ON SubjectId = Subject.Id WHERE Topic.id = ?");
$sth->execute([$_REQUEST["id"]]);
$info = $sth->fetch();
?>
<script>
    let path = document.getElementById("path");
    path.innerHTML += '<li class="nav - item"><a class="nav-link" href = "../userpages/homepage.php">Universe</a></li>';
    path.innerHTML += '<li class="nav - item"><a class="nav-link" href = "../userpages/subject.php?id=' + <?php echo $info["subjectId"]; ?> + '">' + "<?php echo $info["subjectName"]?>" + '</a></li>';
    path.innerHTML += '<li class="nav - item"><a class="nav-link" href = "../userpages/topic.php?id=' + <?php echo $info["topicId"]; ?> + '">' + "<?php echo $info["topicName"]?>" + '</a></li>';
</script>

<div class="solar-system container mt-0" style="background:transparent;">

    <?php
    //planet
    $topicID = $_REQUEST["id"];
    //$_SESSION["topicID"] = $topicID;
//$_SESSION["topicName"] = $_REQUEST["topicName"];
    $sth = DBHandler::getPDO()->prepare("SELECT Name, id FROM Tab WHERE TopicId = ?");
    $sth->execute([$topicID]);
    $orbitSize = "35%";
    $planetSize = "17%";
    $orbitValue = 35;
    $planetValue = 17;
    $z = $sth->rowCount();
    $buttonZ= $z+1;
    $topic = $info["topicName"];
    echo "<div id='star' class='ratio ratio-1x1'>$topic</div>";
    foreach ($sth->fetchAll() as $row) {
        echo "<div class='orbit planet-orbit ratio ratio-1x1' style='width: $orbitSize;z-index: $z;'></div>";
        echo "<div class='planet-spin ratio ratio-1x1' style='width: $orbitSize;z-index: $z;'>";

        echo "<div id ='planet' class='ratio ratio-1x1' style='width: $planetSize;z-index: $z;'>";
        echo "<a class='btn btn-primary spinning-content' href='../userpages/tab.php?id={$row["id"]}' role='button'>{$row["Name"]}</a>";
        echo "</div></div>";
        $orbitValue += 20;
        $orbitSize = $orbitValue . "%";
        //$planetValue += 5;
        //$planetSize = $planetValue . "%";
        --$z;
    }
    $sth = DBHandler::getPDO()->prepare("SELECT UserId FROM user_topic WHERE TopicId = ? AND UserId = ?");
    $sth->execute([$topicID, $_SESSION["user_id"]]);
    if ($sth->rowCount() > 0) {
        echo "<a type='button'
    class='bx bxs-edit btn btn-primary position-absolute bottom-0 end-0 rounded-circle m-5 p-3' href='editTopic.php?topicID={$_REQUEST["id"]}' style='z-index: $buttonZ;'>
    </a>";
    }
    ?>

</div>