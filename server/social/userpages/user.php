<?php

$sth = DBHandler::getPDO()->prepare("SELECT * FROM User WHERE id = ?");
if (isset($_REQUEST['id'])) {
    $sth->execute([$_REQUEST['id']]);
}else{
    $sth->execute([$_SESSION['user_id']]);
}
if ($sth->rowCount() > 0) {
    $user = $sth->fetch();
}
?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <span>User Information</span>
                    <?php
                    if ($user['id'] == $_SESSION['user_id']) {
                        echo '<a href="../userpages/editUser.php" class="float-right text-decoration-none"><i class="bx bxs-edit"></i></a>';
                    }
                    ?>

                </div>
                <div class="card-body">
                    <img src="<?php echo $user['Image']; ?>" alt="Profile Image" class="img-fluid rounded-circle mb-3"
                        style="width: 150px; height: 150px;">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Username:</strong>
                            <?php echo $user['Username']; ?>
                        </li>
                        <li class="list-group-item"><strong>Bio:</strong>
                            <?php echo $user['Bio']; ?>
                        </li>
                        <li class="list-group-item"><strong>Registration date:</strong>
                            <?php echo $user['registrationDate']; ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>