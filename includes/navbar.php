<nav class="navbar navbar-expand-lg navbar-dark bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="?do=home"><?= $cfg["title"] ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                    <?= navBtn("Generate", "?do=home", "house") ?>
                    <?= navBtn("URLs", "?do=urls", "bookmarks") ?>
                    <?php
                    $navUserBtn = navBtn("Login", "?do=login", "person");
                    if (!empty($_SESSION['id'])) {
                        $userid      = $_SESSION['id'];
                        $userName    = getUser($userid)['username'];
                        if (empty($userName)) {
                            die("Error: User with ID $userid not found.");
                        }
                        $navUserBtn = navDropDown($userName, [
                            ["url" => "?do=profile", "text" => "Profile"],
                            ["url" => "?do=logout", "text" => "Logout"]
                        ], "person");
                    }
                    echo $navUserBtn;
                    ?>
            </ul>
        </div>
    </div>
</nav>

<div class="dynamic-form-response"></div>