<nav class="navbar navbar-expand-lg navbar-dark bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="?do=create"><?= $cfg["title"] ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                    <?= navBtn("Create", "?do=create", "bookmark-plus") ?>
                    <?= navBtn("URLs", "?do=urls", "bookmarks") ?>
                    <?= navBtn("Bookmarks", "?do=bookmarks", "star-fill text-warning") ?>
                    <?php

                    # NOTE: Admin
                    if (isset($_SESSION['acl']) && $_SESSION['acl'] > 0) {
                        echo navBtn("Admin", "?do=admin", "gear");
                    }

                    # NOTE: User
                    $navUserBtn = navBtn("Login", "?do=login", "person");
                    if (!empty($_SESSION['id'])) {
                        $userid      = $_SESSION['id'];
                        $userName    = getUser($userid)['username'];
                        if (empty($userName)) {
                            die("Error: User with ID $userid not found.");
                        }
                        $navUserBtn = navDropDown($userName, [
                            ["url" => "?do=profile", "text" => "Profile", "icon" => "person-vcard"],
                            // ["url" => "?do=bookmarks", "text" => "Bookmarks", "icon" => "star-fill text-warning"],
                            ["url" => "?do=logout", "text" => "Logout", "icon" => "power text-danger"],
                        ], "person");
                    }
                    echo $navUserBtn;
                    ?>
            </ul>
        </div>
    </div>
</nav>

<div class="dynamic-form-response"></div>