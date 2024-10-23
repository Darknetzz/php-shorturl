<?php

// if (__DIR__ !== "includes") {
//     die("This file cannot be accessed directly.");
// }

if (basename(__DIR__) !== "includes") {
    die("This file cannot be accessed directly.");
}

$includes_fullpath = __DIR__ . DIRECTORY_SEPARATOR . "_includes.php";
if (!file_exists($includes_fullpath)) {
    die("Include file not found: " . $includes_fullpath);
}
require_once($includes_fullpath);

do {

    $action = (!empty($_REQUEST["action"]) ? $_REQUEST["action"] : Null);

    if ($action == Null) {
        echo alert("No action specified.", "danger");
        break;
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                   logout                                   */
    /* ────────────────────────────────────────────────────────────────────────── */
    if ($action == "logout") {
        session_destroy();
        echo alert("You are now logged out.", "success");
        echo jsRedirect();
        die();
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                    auth                                    */
    /* ────────────────────────────────────────────────────────────────────────── */
    if ($action == "login") {
        $username = (!empty($_POST['username']) ? $_POST['username'] : Null);
        $password = (!empty($_POST['password']) ? $_POST['password'] : Null);
        $auth     = auth($username, $password);

        if ($auth !== True) {
            echo alert("Invalid username or password.", "danger");
            break;
        }

        if ($auth === True) {
            echo alert("You are now logged in.", "success");
            echo jsRedirect();
            break;
        }
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                 createShort                                */
    /* ────────────────────────────────────────────────────────────────────────── */
    if ($action == "createshort") {

        $type     = (!empty($_POST['type']) ? $_POST['type'] : Null);
        $dest     = (!empty($_POST['dest']) ? $_POST['dest'] : Null);
        $protocol = (!empty($_POST['protocol']) ? $_POST['protocol'] : "https://");
        $short    = (!empty($_POST['short']) ? $_POST['short'] : Null);
        $shortGen = (!empty($_POST['shortgen']) ? $_POST['shortgen'] : genStr($cfg["short_default"]));

        // Check if the user is logged in
        if (empty($_SESSION['id'])) {
            echo alert("You are not logged in.", "danger");
            break;
        }

        // If $short is empty, use $shortGen
        if ($short == Null) {
            $short = $shortGen;
        }

        // Remove all symbols except from short URL
        $short = preg_replace('/[^a-zA-Z0-9]/', '', $short);

        // Check if $type or $dest is empty
        if ($type == Null || $dest == Null) {
            echo alert("Please fill out all fields.", "danger");
            break;
        }

        // Check if the destination URL already exists
        if (urlExists($short)) {
            echo alert("The short URL <a href='$short' target='_blank'>$short</a> already exists.", "info");
            break;
        }

        // Validate and sanitize the short URL
        if (strlen($short) < $cfg["short_min"] || strlen($short) > $cfg["short_max"]) {
            echo alert("The short URL must be between ".$cfg["short_min"]." and ".$cfg["short_max"]." characters long.", "danger");
            break;
        }

        // Check if $dest contains http:// or https:// at the start, if not prepend https://
        if (!preg_match('/^https?:\/\//', $dest)) {
            $dest = $protocol . $dest;
        }

        // Validate and sanitize the destination URL
        $dest = filter_var($dest, FILTER_SANITIZE_URL);

        // Remove duplicate http:// or https:// from the start of the string
        $dest = preg_replace('/^(https?:\/\/)+/', $protocol, $dest);

        // Check if the URL is valid
        if (!filter_var($dest, FILTER_VALIDATE_URL)) {
            echo alert("The destination URL is not valid.", "danger");
            break;
        }

        // Check if short URL and destination URL are the same
        if ($cfg["base_url"].DIRECTORY_SEPARATOR.$short == $dest) {
            echo alert("The short URL and destination URL cannot be the same.", "danger");
            break;
        }

        $insertShort = "INSERT INTO urls (`type`, `short`, `dest`, `userid`) VALUES (?, ?, ?, ?)";
        $insertShort = query($insertShort, [$type, $short, $dest, $_SESSION['id']]);

        echo alert("
                <h4>Your URL was created successfully!</h4>
                <p>Short URL: <a href='$short' class='alert-link' target='_blank'>$short</a></p>
                <p>Destination URL: <a href='$dest' class='alert-link' target='_blank'>$dest</a></p>
        ", "success");
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                deleteShort                                 */
    /* ────────────────────────────────────────────────────────────────────────── */
    if ($action == "delete") {
        $id = (!empty($_POST['id']) ? $_POST['id'] : Null);

        if ($id == Null) {
            echo alert("No ID specified.", "danger");
            break;
        }

        // Check if the user is logged in
        if (empty($_SESSION['id'])) {
            echo alert("You are not logged in.", "danger");
            break;
        }

        // Check if the URL exists
        // if (!urlExists($id)) {
        //     echo alert("The URL does not exist.", "danger");
        //     break;
        // }

        // Check if the user is the owner of the URL
        // if (!isOwner($id)) {
        //     echo alert("You are not the owner of this URL.", "danger");
        //     break;
        // }

        $deleteShort = "DELETE FROM urls WHERE id = ?";
        $deleteShort = query($deleteShort, [$id]);

        echo alert("The URL was deleted successfully.", "success");
    }

} while (False);

?>