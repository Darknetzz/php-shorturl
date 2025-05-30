<?php

do {

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                     do                                     */
    /* ────────────────────────────────────────────────────────────────────────── */
    $do = (isset($_GET['do'])) ? $_GET['do'] : Null;

    if ($do == "logout") {
        session_destroy();
        echo alert("You are now logged out.", "success");
        echo jsRedirect();
        break;
    }

    if (!isset($_SESSION['id'])) {
        require_once("pages/login.php");
        break;
    }

    if (empty($do)) {
        require_once("pages/urls.php");
        break;
    }

    if (!file_exists("pages/$do.php")) {
        echo alert("Invalid action <code>$do</code>. <a href='index.php'>Go back?</a>", "danger", persistent: True);
        break;
    }

    require_once("pages/$do.php");
    break;
    
} while (False);