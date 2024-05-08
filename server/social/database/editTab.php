<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sth = DBHandler::getPDO()->prepare("UPDATE Tab SET Name = ?, Description = ? WHERE id = ?");
    $sth->execute([$_POST["name"], $_POST["description"], $_POST['tabID']]);
    $sth = DBHandler::getPDO()->prepare("SELECT topicId FROM Tab WHERE id = ?");
    $sth->execute([$_POST['tabID']]);
    $topic = $sth->fetch()["topicId"];
    header("Location:../userpages/editTopic.php?topicID=$topic");
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sth = DBHandler::getPDO()->prepare("SELECT * FROM Tab WHERE id = ?");
    $sth->execute([$_GET["tabID"]]);
    if ($sth->rowCount() > 0) {
        $tab = $sth->fetch();
    }
}
?>

<form action="../database/editTab.php" method="post">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $tab['Name']; ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <input type="text" class="form-control" id="description" name="description"
            value="<?php echo $tab['Description']; ?>">
    </div>
    <br>
    <input type="hidden" name="tabID" value="<?php echo $tab['id']; ?>">
    <button type="submit" class="btn btn-primary">Submit</button>
</form>