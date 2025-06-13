<?php

// header("Content-Type: application/json; charset=UTF-8");

do {

    if (basename(__DIR__) !== "includes") {
        $res = ["status" => "ERROR", "message" => "This file cannot be accessed directly."];
        break;
    }

    // $includes_fullpath = basename(__DIR__) . DIRECTORY_SEPARATOR . "_includes.php";
    $includes_fullpath = "_includes.php";
    if (!file_exists($includes_fullpath)) {
        $res = ["status" => "ERROR", "message" => "Include file not found: " . $includes_fullpath];
        break;
    }
    require_once($includes_fullpath);

    $action       = (!empty($_REQUEST["action"]) ? $_REQUEST["action"] : Null);

    if ($action == Null) {
        $res = ["status" => "WARN", "message" => "No action specified."];
        break;
    }

    if (empty($_SESSION['id']) && $action !== "login") {
        $res = ["status" => "ERROR", "message" => "You are not logged in."];
        break;
    }

    writeLog("API Request from user ".$_SESSION['id']." API action: ".$action);

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                    test                                    */
    /* ────────────────────────────────────────────────────────────────────────── */
    if ($action == "test") {
        $res = ["status" => "OK", "message" => "Test successful."];
        break;
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                    error                                   */
    /* ────────────────────────────────────────────────────────────────────────── */
    if ($action == "error") {
        $res = ["status" => "ERROR", "message" => "This is an error message."];
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
        break;
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
    if ($action == "create") {

        $short_type = (!empty($_POST['short_type']) ? $_POST['short_type'] : Null);
        $protocol   = (!empty($_POST['protocol']) ? $_POST['protocol'] : "https://");
        $short      = (!empty($_POST['short']) ? $_POST['short'] : Null);
        $shortGen   = (!empty($_POST['shortgen']) ? $_POST['shortgen'] : genStr($cfg["short_default"]));
        $dest_type  = (!empty($_POST['dest_type']) ? $_POST['dest_type'] : Null);
        $dest       = (!empty($_POST[$type.'_dest']) ? $_POST[$type.'_dest'] : Null);
        $options    = (!empty($_POST['options']) ? $_POST['options'] : Null);

        // Check if the user is logged in
        // if (empty($_SESSION['id'])) {
        //     $res = ["status" => "ERROR", "message" => "You are not logged in."];
        //     break;
        // }

        // If $short is empty, use $shortGen
        if ($short_type == Null) {
            $short = $shortGen;
        }

        // Remove all symbols except from short URL
        $short = preg_replace('/[^a-zA-Z0-9]/', '', $short);

        // Check if $type or $dest is empty
        if ($short_type == Null) {
            $res = ["status" => "ERROR", "message" => "Short URL must have a valid type."];
            break;
        }

        // Verify the destination URL if type != custom
        if ($short_type != "custom") {
            $dest = urlValidate($dest);
        }

        // Check if the $dest URL is empty after validation
        if (empty($dest)) {
            $res = ["status" => "ERROR", "message" => "The destination URL is not valid."];
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

        // Check if short URL and destination URL are the same
        if ($cfg["base_url"].DIRECTORY_SEPARATOR.$short == $dest) {
            $res = ["status" => "ERROR", "message" => "The short URL and destination URL cannot be the same."];
            break;
        }

        // Check if options are empty
        if (!empty($options) && is_array($options)) {
            $options = json_encode($options);
        }

        $insertShort = "INSERT INTO urls (`type`, `short`, `dest`, `userid`, `options`) VALUES (?, ?, ?, ?, ?)";
        $insertShort = query($insertShort, [$type, $short, $dest, $_SESSION['id'], $options]);

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
    /*                                  editShort                                 */
    /* ────────────────────────────────────────────────────────────────────────── */
    if ($action == "edit") {
        $id    = (!empty($_POST['id']) ? $_POST['id'] : Null);
        $type  = (!empty($_POST['type']) ? $_POST['type'] : Null);
        $short = (!empty($_POST['short']) ? $_POST['short'] : Null);
        $dest  = (!empty($_POST['dest']) ? $_POST['dest'] : Null);

        // Check if the user is logged in
        // if (empty($_SESSION['id'])) {
        //     $res = ["status" => "ERROR", "message" => "You are not logged in."];
        //     break;
        // }

        if ($id == Null) {
            $res = ["status" => "ERROR", "message" => "No ID specified."];
            break;
        }

        if ($type == Null) {
            $res = ["status" => "ERROR", "message" => "URL must have a valid type."];
            break;
        }

        // Verify the destination URL if type != custom
        if ($type != "custom") {
            $dest = urlValidate($dest);
        }

        // Check if the $dest URL is empty after validation
        if (empty($dest)) {
            $res = ["status" => "ERROR", "message" => "The destination URL is not valid."];
            break;
        }

        // Validate and sanitize the short URL
        if (strlen($short) < $cfg["short_min"] || strlen($short) > $cfg["short_max"]) {
            $res = ["status" => "ERROR", "message" => "The short URL must be between ".$cfg["short_min"]." and ".$cfg["short_max"]." characters long."];
            break;
        }
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                deleteShort                                 */
    /* ────────────────────────────────────────────────────────────────────────── */
    if ($action == "delete") {
        $id = (!empty($_POST['id']) ? $_POST['id'] : Null);

        // Check if the user is logged in
        // if (empty($_SESSION['id'])) {
        //     $res = ["status" => "ERROR", "message" => "You are not logged in."];
        //     break;
        // }

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


    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                  bookmark                                  */
    /* ────────────────────────────────────────────────────────────────────────── */
    if ($action == "bookmark") {
        $id = (!empty($_POST['id']) ? $_POST['id'] : Null);

        // Check if the user is logged in
        // if (empty($_SESSION['id'])) {
        //     $res = ["status" => "ERROR", "message" => "You are not logged in."];
        //     break;
        // }

        if ($id == Null) {
            $res = ["status" => "ERROR", "message" => "No ID specified."];
            break;
        }

        $currentBookmarks_JSON = getUser($_SESSION['id'], "bookmarks");
        $currentBookmarks = [];
        if (!empty($currentBookmarks_JSON)) {
            $currentBookmarks = json_decode($currentBookmarks_JSON, True);
        }
        if (in_array($id, $currentBookmarks)) {
            unset($currentBookmarks[array_search($id, $currentBookmarks)]);
            $message = "The URL was removed from bookmarks successfully.";
            $icon    = icon("star", 1.5, "grey");
        } else {
            array_push($currentBookmarks, $id);
            $message = "The URL was bookmarked successfully.";
            $icon    = icon("star-fill", 1.5, "gold");
        }
        $newBookmarks = json_encode($currentBookmarks);
        setUser($_SESSION['id'], "bookmarks", $newBookmarks);

        $res = ["status" => "OK", "message" => $message, "icon" => $icon];
        break;
    }



} while (False);

if (!empty($res)) {
    $res["debug"] = $_REQUEST;
    die(json_encode($res));
}

die(json_encode([
    "debug"   => $_REQUEST,
    "status"  => "WARN",
    "message" => "Action <code>".$action."</code> is invalid or empty."
]));


?>