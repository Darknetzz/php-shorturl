<?php

/* ────────────────────────────────────────────────────────────────────────── */
/*                                     CFG                                    */
/* ────────────────────────────────────────────────────────────────────────── */

/**
 * title
 * The title of the application.
 * Default: "php-shorturl"
 */
$cfg["title"] = "php-shorturl";


/**
 * protocol
 * The protocol used by the application, determined by the server's HTTPS setting.
 * Default: 'http' or 'https' based on the server's HTTPS setting.
 */
$cfg["protocol"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');


/**
 * base_domain
 * The base domain of the application, derived from the server's HTTP_HOST.
 * Default: Value of $_SERVER['HTTP_HOST']
 */
$cfg["base_domain"] = $_SERVER['HTTP_HOST'];


/**
 * base_url
 * The base URL of the application, constructed from the protocol and base domain.
 * Default: Constructed from $cfg["protocol"] and $cfg["base_domain"]
 */
$cfg["base_url"] = $cfg["protocol"] . "://" . $cfg["base_domain"];


/**
 * base_path
 * The base path of the application, derived from the directory of the parent directory.
 * Default: Directory path of the parent directory, or dirname(__DIR__)
 */
$cfg["base_path"] = dirname(__DIR__);


/**
 * short_min
 * The minimum length of the shortened URL.
 * Default: 1
 */
$cfg["short_min"] = 1;


/**
 * short_max
 * The maximum length of the shortened URL.
 * Default: 20
 */
$cfg["short_max"] = 20;


/**
 * short_default
 * The default length of the shortened URL.
 * Default: 5
 */
$cfg["short_default"] = 5;


/**
 * include_dir
 * The directory where includes are stored, constructed from the base path.
 * Default: Constructed from $cfg["base_path"] and "includes"
 */
$cfg["include_dir"] = $cfg["base_path"] . DIRECTORY_SEPARATOR . "includes";


/**
 * logging
 * Whether logging is enabled.
 * Default: True
 */
$cfg["logging"] = True;


/**
 * default_protocol
 * The default protocol to use for URLs.
 * Default: "http://"
 */
$cfg["default_protocol"] = "http://";


/**
 * notification_sound
 * Whether notification sound is enabled.
 * Default: True
 */
$cfg["notification_sound"] = True;


/**
 * form_disable_timeout
 * The timeout for disabling the form after submission, in milliseconds.
 * Default: 1000
 */
$cfg["form_disable_timeout"] = 1000;


/**
 * url_types
 * The types of URLs supported by the application.
 * Default: Array of URL types with "name" and "value" keys
 */
$cfg["url_types"] = [
    [
        "name"  => "Redirect",
        "value" => "redirect"
    ],
    [
        "name"  => "Alias",
        "value" => "alias",
    ],
    [
        "name"  => "Custom",
        "value" => "custom",
    ],
];

/**
 * urls_public
 * Whether shortened URLs are public. Everyone can see it in the URL list.
 * Default: True
 */
$cfg["urls_public"] = True;


/**
 * default_delay
 * The default delay for redirection, in milliseconds.
 * Default: 100
 */
$cfg["default_delay"] = 100;