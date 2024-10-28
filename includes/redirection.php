<?php

do {

    # REVIEW: What is this doing here?
    // $shortGen = (!empty($_POST['shortgen']) ? $_POST['shortgen'] : genStr($cfg["short_default"]));
    $p        = (!empty($_GET['p']) ? $_GET['p'] : Null);
    
    if (empty($p)) {
        break;
    }
    
    // Ignore if it contains .php
    if (strpos($p, '.php') !== false) {
        break;
    }

    // Check if it contains / - if it does, remove everything before it
    if (strpos($p, '/') !== false) {
        $p = substr(strrchr($p, '/'), 1);
        $p = preg_replace('/[^a-zA-Z0-9]/', '', $p);
    }


    $url = getUrl($p);
    $type = $url["type"];
    $dest = $url["dest"];

    if (empty($type) || !is_string($type)) {
        echo alert("The type <b>$type</b> does not exist.", "danger");
        die();
    }
    if ($type === "alias") {
        die("<iframe src='$dest' style='position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;'></iframe>");
    }
    if ($type === "custom") {
        die($dest);
    }
    if ($dest === False || empty($dest)) {
        echo alert("The short URL <b>$p</b> does not exist.", "danger");
        jsRedirect();
        die();
    }
    echo alert("Redirecting to <a href='$dest'>$dest</a>...", "info");
    redirect($dest);
    die();

} while (False);


?>