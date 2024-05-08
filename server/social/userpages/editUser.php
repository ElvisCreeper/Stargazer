<div class="container">
    <h1>User Information</h1>
    <?php
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($_FILES['image']['size'] > 0) {
            $target_dir = "../userImages/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        }
        $sth = DBHandler::getPDO()->prepare("UPDATE User SET Username = ?, Bio = ?, Image = ? WHERE id = ?");
        $sth->execute([htmlspecialchars($_POST['Username']),htmlspecialchars($_POST['Bio']), $target_file, $_SESSION['user_id']]);
        $_SESSION['pfp']=$target_file;
    }
    $sth = DBHandler::getPDO()->prepare("SELECT * FROM User WHERE id = ?");
    $sth->execute([$_SESSION['user_id']]);
    if ($sth->rowCount() > 0) {
        $user = $sth->fetch();
    }

    
    ?>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="Username" name="Username"
                value="<?php echo $user['Username']; ?>">
        </div>
        <div class="form-group">
            <label for="Bio">Bio:</label>
            <input type="text" class="form-control" id="bio" name="Bio" value="<?php echo $user['Bio']; ?>">
        </div>
        <div class="form-group">
            <label for="image">Image:</label>
            <input type="file" class="form-control-file" id="image" name="image">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
    <?php
    // Mostra l'immagine dell'utente
    echo "<img src='{$user['Image']}' class='img-fluid rounded-circle mb-3' style='width: 150px; height: 150px;' alt='User Image'>";
    ?>
</div>