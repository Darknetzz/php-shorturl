<?php

/* ────────────────────────────────────────────────────────────────────────── */
/*                                     CFG                                    */
/* ────────────────────────────────────────────────────────────────────────── */
$cfg["title"]            = "php-shorturl";
$cfg["protocol"]         = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
$cfg["base_domain"]      = $_SERVER['HTTP_HOST'];
$cfg["base_url"]         = $cfg["protocol"] . "://" . $cfg["base_domain"];
$cfg["base_path"]        = dirname(__DIR__);
$cfg["short_min"]        = 1;
$cfg["short_max"]        = 20;
$cfg["short_default"]    = 5;
$cfg["include_dir"]      = $cfg["base_path"] . DIRECTORY_SEPARATOR . "includes";
$cfg["logging"]          = True;
$cfg["default_protocol"] = "http://";
$cfg["url_types"]        = [
    [
        "name"  => "Redirect",
        "value" => "redirect"
    ],
    [
        "name"  => "Alias",
        "value" => "alias"
    ],
    [
        "name"  => "Custom",
        "value" => "custom"
    ],
];
