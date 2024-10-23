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


    // Ignore $p if it is equal to BASE_URL/index.php
    if (strlen($p) > 0) {
        $dest = getDest($p);
    
        if (empty($dest)) {
            echo "
                <div class='alert alert-danger m-3'>
                    The short URL <b>$p</b> does not exist.
                </div>
            ";
            jsRedirect();
            die();
        }
        echo "Redirecting to <a href='$dest'>$dest</a>...";
        redirect($dest);
        die();
    }

} while (False);


?>