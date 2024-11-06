<div class="container-fluid">
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
            <h5><?= $_SESSION['username'] ?></h5>
            <p class="text-muted"><?= aclToText($_SESSION['acl']) ?></p>
            
            <div class="btn-group profileBtns" role="group">
                <a class="btn btn-outline-info" href="?do=bookmarks">Bookmarks</a>
                <a class="btn btn-outline-primary" href="?do=changepw">Change Password</a>
                <a class="btn btn-outline-danger" href="?do=logout">Logout</a>
            </div>

        </div>
    </div>
</div>