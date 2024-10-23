<?php

# ./includes/_includes.php

$includes = [
    "session.php",
    "config.php",
    "sqlcon.php",
    "functions.php",
    // "redirection.php",
    // "navbar.php",
    // "formhandler.php",
    # "js.php", # NOTE: This is directly included in index.php
];

foreach ($includes as $include) {

    $include_basename = basename($include, '.php');
    $include_fullpath = __DIR__ . DIRECTORY_SEPARATOR . $include;
    $include_local    = __DIR__ . DIRECTORY_SEPARATOR . $include_basename . "_local.php";

    if (!file_exists($include_fullpath)) {
        die("Include file not found: " . $include_fullpath);
    }
    if (file_exists($include_local)) {
        require_once($include_local);
        continue;
    }
    require_once($include_fullpath);
}

?>