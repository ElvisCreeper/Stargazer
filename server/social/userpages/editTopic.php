<div class="container">
    <h1>Edit your planetary system:</h1>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $sth = DBHandler::getPDO()->prepare("UPDATE Topic SET Name = ?, Description = ?, SubjectId = ? WHERE id = ?");
        $sth->execute([$_POST["name"], $_POST["description"], $_POST["subject"], $_REQUEST["topicID"]]);
    }
    $sth = DBHandler::getPDO()->prepare("SELECT * FROM Topic WHERE id = ?");
    $sth->execute([$_REQUEST["topicID"]]);
    if ($sth->rowCount() > 0) {
        $topic = $sth->fetch();
    }
    ?>
    <form method="post">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $topic['Name']; ?>"
                required>
        </div>
        <div class="form-group">
            <label for="subject">Galaxy:</label>
            <select class="form-select" id="subject" name="subject" required>
                <?php
                $sth = DBHandler::getPDO()->prepare("SELECT id, name FROM Subject");
                $sth->execute();
                if ($sth->rowCount() > 0) {
                    foreach ($sth->fetchAll() as $row) {
                        echo "<option value='" . $row["id"] . "' " . (($row["id"] == $topic['SubjectId']) ? "selected" : "") . ">{$row['name']}</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <input type="text" class="form-control" id="description" name="description"
                value="<?php echo $topic['Description']; ?>">
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Edit</button>
    </form>
    <br>
    <hr>
    <h1>Add a planet:</h1>
    <form action="../database/insertTab.php" method="post">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <input type="text" class="form-control" id="description" name="description">
        </div>
        <br>
        <input type="hidden" name="topicId" value="<?php echo $topic['id']; ?>">
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
    <br>
    <hr>
    <h1>Remove a planet:</h1>
    <form action="../database/removeTab.php" method="post">
        <label for="tabId">Planet:</label>
        <select class="form-select" id="tabId" name="tabId" required>
            <?php
            $sth = DBHandler::getPDO()->prepare("SELECT id, name FROM Tab WHERE TopicId = ?");
            $sth->execute([$topic['id']]);
            if ($sth->rowCount() > 0) {
                foreach ($sth->fetchAll() as $row) {
                    echo "<option value='" . $row["id"] . "'>{$row['name']}</option>";
                }
            }
            ?>
        </select>
        <br>
        <button type="submit" class="btn btn-danger">Remove</button>
    </form>
    <br>
    <hr>
    <h1>Edit a planet:</h1>
    <label for="tabIdEdit">Planet:</label>
    <select class="form-select" id="tabIdEdit" name="tabId" required>
        <?php
        $sth = DBHandler::getPDO()->prepare("SELECT id, name FROM Tab WHERE TopicId = ?");
        $sth->execute([$topic['id']]);
        if ($sth->rowCount() > 0) {
            foreach ($sth->fetchAll() as $row) {
                echo "<option value='" . $row["id"] . "'>{$row['name']}</option>";
            }
        }
        ?>
    </select>
    <br>
    <button type="button" class="btn btn-primary" data-bs-toggle='modal' data-bs-target='#planetModal'
        onclick='editPlanetModal(this)'>Edit</button>

    <div class="modal fade" id="planetModal" tabindex="-1" aria-labelledby="planetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="planetModalLabel">Edit planet: </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id='planetModalBody'>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editPlanetModal(button) {
            var modal = document.getElementById('planetModal');
            var e = document.getElementById("tabIdEdit");
            var modalBody = modal.querySelector('#planetModalBody');
            var modalLabel = modal.querySelector('#planetModalLabel');
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    modalBody.innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "../database/editTab.php?tabID=" + e.value, true);
            xmlhttp.send();

            modalLabel.innerHTML = "<p>Edit planet: " + e.options[e.selectedIndex].text + "</p>";
        }
    </script>
</div>