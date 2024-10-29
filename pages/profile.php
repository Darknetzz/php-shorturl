<div class="container">
    <?php

        if (!isset($_SESSION['id'])) {
            echo alert("You must be logged in to view this page.", "danger");
            echo jsRedirect("index.php");
            die();
        }

    ?>

    <div class="card m-2">
        <h3 class="card-header">Profile</h3>
        <div class="card-body">
            <h5 class="card-title"><?= $_SESSION['username'] ?></h5>
            <p class="card-text">Role: <?= aclToText($_SESSION['acl']) ?></p>


            <h5 class="card-title">Admin</h5>
            <div class="btn-group adminBtns" role="group">
                <a class="btn btn-outline-primary" href="?do=edit">Edit Profile</a>
                <a class="btn btn-outline-success testAPI">Test API</a>
            </div>

            <hr>

            <h5 class="card-title">User</h5>
            <div class="btn-group profileBtns" role="group">
                <a class="btn btn-outline-primary" href="?do=resetpw">Change Password</a>
                <a class="btn btn-outline-primary" href="?do=urls">View URLs</a>
                <a class="btn btn-outline-danger" href="?do=logout">Logout</a>
            </div>
        </div>
    </div>
</div>