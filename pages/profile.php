<div class="container">
    <?php

        if (!isset($_SESSION['id'])) {
            echo alert("You must be logged in to view this page.", "danger");
            echo jsRedirect("index.php");
            die();
        }

    ?>

    <div class="card m-2">
        <h3 class="card-header"><?= $_SESSION['username'] ?></h3>
        <div class="card-body">
            <a class="btn btn-primary" href="?do=urls">View URLs</a>
            <a class="btn btn-danger" href="?do=logout">Logout</a>
        </div>
    </div>
</div>