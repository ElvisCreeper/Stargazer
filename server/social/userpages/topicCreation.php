<div class="container">
    <h1>Create your planetary system:</h1>
    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $sth = DBHandler::getPDO()->prepare("CALL createTopic(?, ?, ?,?)");
            $sth->execute([htmlspecialchars($_POST["name"]),$_POST["subject"],htmlspecialchars($_POST["description"]),$_SESSION["user_id"]]);
        }
    ?>
    <form method="post">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="subject">Galaxy:</label>
            <select class="form-select" id="subject" name="subject" required>
                <?php
                    $sth = DBHandler::getPDO()->prepare("SELECT id, name FROM Subject");
                    $sth->execute();
                    if ($sth->rowCount() > 0) {
                        foreach ($sth->fetchAll() as $row) {
                            echo "<option value='".$row["id"]."'>{$row['name']}</option>";
                        }
                    }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <input type="text" class="form-control" id="description" name="description">
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>