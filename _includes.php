<?php

# ./_includes.php

$include_dir = __DIR__ . DIRECTORY_SEPARATOR . "includes";

$includes = [
    "session.php",
    "config.php",
    "sqlcon.php",
    "functions.php",
    "redirection.php",
    "navbar.php",
    "navigation.php",
    "custom.php",
    # "js.php", # NOTE: This is directly included in index.php
];

foreach ($includes as $include) {

    $include_fullpath = $include_dir . DIRECTORY_SEPARATOR . $include;
    $include_basename = basename($include, '.php');
    $include_local    = $include_dir . DIRECTORY_SEPARATOR . $include_basename . "_local.php";

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