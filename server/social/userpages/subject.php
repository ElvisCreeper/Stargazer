<?php
$sth = DBHandler::getPDO()->prepare("SELECT Subject.Name AS subjectName, Subject.id FROM Subject WHERE id = ?");
$sth->execute([$_REQUEST["id"]]);
$info = $sth->fetch();
?>
<script>
    document.getElementById("path").innerHTML += '<li class="nav - item"><a class="nav-link" href = "../userpages/homepage.php">Universe</a></li>';
    document.getElementById("path").innerHTML += '<li class="nav - item"><a class="nav-link" href = "../userpages/subject.php?id=' + <?php echo $info["id"]; ?> + '">' + "<?php echo $info["subjectName"]?>" + '</a></li>';
</script>

<script>
    updateSearchbar("<a type='button' class='bx bx-plus-circle btn btn-primary rounded-circle p-3 mr-3' href='topicCreation.php' id='createTopicButton'></a>");
    function updateSearchbar(el) {
        console.log(el);
        document.getElementById("searchbar").innerHTML = el + document.getElementById("searchbar").innerHTML;
    }
</script>

<?php
//solar system
$subjectID = $_REQUEST["id"];
//$_SESSION["subjectID"] = $subjectID;
//$_SESSION["subjectName"] = $_REQUEST["subjectName"];
$sth = DBHandler::getPDO()->prepare("SELECT Name, id FROM Topic WHERE SubjectId = $subjectID");
$sth->execute();
echo ('<table>');
foreach ($sth->fetchAll() as $row) {
    echo "<tr>";
    echo "<td><a class='btn btn-primary' href='../userpages/topic.php?id={$row["id"]}' role='button'>{$row["Name"]}</a>";
    echo '</tr>';
}
echo ('</table>');
