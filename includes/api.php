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
        $res = ["status" => "OK", "message" => "No action specified."];
        break;
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                    test                                    */
    /* ────────────────────────────────────────────────────────────────────────── */
    if ($action == "test") {
        $res = ["status" => "OK", "message" => "Test successful."];
        break;
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                   logout                                   */
    /* ────────────────────────────────────────────────────────────────────────── */
    if ($action == "logout") {
        session_destroy();
        $res = [
            "status" => "OK", 
            "message" => "You are now logged out.", 
            "redirect" => "index.php"
        ];
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
            $res = ["status" => "ERROR", "message" => "Invalid username or password."];
            break;
        }

        if ($auth === True) {
            $res = [
                "status" => "OK", 
                "message" => "You are now logged in.",
                "redirect" => "index.php"
            ];
            break;
        }
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                 createShort                                */
    /* ────────────────────────────────────────────────────────────────────────── */
    if ($action == "createshort") {

        $type     = (!empty($_POST['type']) ? $_POST['type'] : Null);
        $protocol = (!empty($_POST['protocol']) ? $_POST['protocol'] : "https://");
        $short    = (!empty($_POST['short']) ? $_POST['short'] : Null);
        $shortGen = (!empty($_POST['shortgen']) ? $_POST['shortgen'] : genStr($cfg["short_default"]));

        // Check if the user is logged in
        if (empty($_SESSION['id'])) {
            $res = ["status" => "ERROR", "message" => "You are not logged in."];
            break;
        }

        // If $short is empty, use $shortGen
        if ($short == Null) {
            $short = $shortGen;
        }

        // Remove all symbols except from short URL
        $short = preg_replace('/[^a-zA-Z0-9]/', '', $short);

        // Check if $type or $dest is empty
        if ($type == Null) {
            echo json_encode($_POST, JSON_PRETTY_PRINT);
            $res = ["status" => "ERROR", "message" => "URL must have a valid type."];
            break;
        }

        if ($type == "alias") {
            $validateURL = True;
            $dest = (!empty($_POST['alias_dest']) ? $_POST['alias_dest'] : Null);
        }
        if ($type == "custom") {
            $validateURL = False;
            $dest = (!empty($_POST['custom_dest']) ? $_POST['custom_dest'] : Null);
        } 
        if ($type == "redirect") {
            $validateURL = True;
            $dest = (!empty($_POST['redirect_dest']) ? $_POST['redirect_dest'] : Null);
        }

        if ($dest == Null) {
            if ($type == "custom") {
                $res = ["status" => "ERROR", "message" => "Custom URL must have content."];
                break;
            }
            $res = ["status" => "ERROR", "message" => "URL must have a destination."];
            break;
        }

        // Check if the destination URL already exists
        if (urlExists($short)) {
            $res = ["status" => "ERROR", "message" => "The short URL <a href='$short' target='_blank'>$short</a> already exists."];
            break;
        }

        // Validate and sanitize the short URL
        if (strlen($short) < $cfg["short_min"] || strlen($short) > $cfg["short_max"]) {
            $res = ["status" => "ERROR", "message" => "The short URL must be between ".$cfg["short_min"]." and ".$cfg["short_max"]." characters long."];
            break;
        }

        if ($validateURL) {
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
                $res = ["status" => "ERROR", "message" => "The destination URL is not valid."];
                break;
            }

            // Check if short URL and destination URL are the same
            if ($cfg["base_url"].DIRECTORY_SEPARATOR.$short == $dest) {
                $res = ["status" => "ERROR", "message" => "The short URL and destination URL cannot be the same."];
                break;
            }
        }

        $insertShort = "INSERT INTO urls (`type`, `short`, `dest`, `userid`) VALUES (?, ?, ?, ?)";
        $insertShort = query($insertShort, [$type, $short, $dest, $_SESSION['id']]);

        $destLink = "<p>Destination URL: <a href='$dest' class='alert-link' target='_blank'>$dest</a></p>";
        if ($type == "custom") {
            $destLink = "<p>Destination URL: Custom</p>";
        }

        $res = ["status" => "OK", "message" => "
            <h4>Your URL was created successfully!</h4>
            <p>Type: ".ucfirst($type)."</p>
            <p>Short URL: <a href='$short' class='alert-link' target='_blank'>$short</a></p>
            $destLink
        "];
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                deleteShort                                 */
    /* ────────────────────────────────────────────────────────────────────────── */
    if ($action == "delete") {
        $id = (!empty($_POST['id']) ? $_POST['id'] : Null);

        // Check if the user is logged in
        if (empty($_SESSION['id'])) {
            $res = ["status" => "ERROR", "message" => "You are not logged in."];
            break;
        }

        if ($id == Null) {
            $res = ["status" => "ERROR", "message" => "No ID specified."];
            break;
        }

        $id = explode(",", $id);

        foreach ($id as $i) {
            if (!is_numeric($i)) {
                $res = ["status" => "ERROR", "message" => "Invalid ID '$i' specified."];
                break;
            }
            deleteURL($i);
        }
        
        $res = ["status" => "OK", "message" => "The URL was deleted successfully."];
    }

} while (False);

if (!empty($res)) {
    echo json_encode($res, JSON_PRETTY_PRINT);
    die();
}

echo json_encode(["status" => "OK", "message" => "No action specified."], JSON_PRETTY_PRINT);
?>