<?php

    $newTooltip = 
    function ($text = "Tooltip", $icon = "question-circle", $html = "true", $placement = "top") {
    return '
        <a class="btn btn-default"
            data-bs-toggle="tooltip" 
            data-bs-title="'.$text.'" 
            data-bs-placement="'.$placement.'" 
            data-bs-html="'.$html.'"
            title="'.$text.'">
            <span class="bi bi-'.$icon.'" style="font-size:1.5rem;"></span>
        </a>';
    };

    $tooltip["name"] = $newTooltip("
        <h4>Name</h4>
        <p class='text-success'>Optional</p>
        The name of the short URL. This is only used for identification purposes.
        If not specified, name will be the same as the short URL.
    ");

    $tooltip["protocol"] = "
        <h4>Protocol</h4>
        <p class='text-danger'>Required</p>
        The protocol of the URL (e.g. <code>http://</code> or <code>https://</code>).<br>
        <b>Note:</b> It is recommended to use <code>http://</code> for compatibility, usually websites will redirect to <code>https://</code>
        automatically if it's available.<br>
        <b>Default:</b> <code>".$cfg["default_protocol"]."</code>
    ";

    $tooltip["short"] = $newTooltip("
        <h4>Short URL</h4>
        <p class='text-success'>Optional</p>
        The short URL that you want to use. Should only consist of alphanumeric characters.<br>
        If left empty, it will be generated for you.<br><br>
        <h5>Examples</h5>
        <ul>
            <li>
                <code>example</code> would become <code>".$cfg["base_url"]."/example</code>
            </li>
            <li>
                <code>example123</code> would become <code>".$cfg["base_url"]."/example123</code><br>
            </li>
            <li>
                <code>123</code> would become <code>".$cfg["base_url"]."/123</code><br>
            </li>
            <li>
                <code>[empty]</code> would become <code>".$cfg["base_url"]."/".substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $cfg["short_default"])."</code><br>
            </li>
        </ul>
    ");

    $tooltip["type"] = $newTooltip("
        <h4>Type</h4>
        <p><span class='text-danger'>Required</span> (Default: <code>Redirect</code>)</p>
        The type of the URL. This will determine how the short URL will behave.

        <hr>

        <h4>Available types</h4>
        <ul>
            <li>
                <h6>Redirect</h6>
                Redirects to a URL. Meaning it will replace the URL in the address field of the browser.
            </li>
            
            <br>

            <li>
                <h6>Alias</h6>
                Attempts to display the destination URL in a fullscreen iframe (might not always work).
                This means it will not change the URL in the address field of the browser.
            </li>
            
            <br>

            <li>
                <h6>Custom</h6>
                Executes a custom script.
            </li>
        </ul>
    ");

    $tooltip["redirect"] = $newTooltip("
        <h4>Redirect URL</h4> 
        <p class='text-danger'>Required</p>
        This is the URL the short URL will redirect to. Any URL should work.
        <br><br>
        <h5>Examples</h5>
        <ul>
            <li>
                <code>example.com</code><br>
            </li>
            <li>
                <code>example.com/path</code><br>
            </li>
            <li>
                <code>http://example.com:1337/path</code><br>
            </li>
            <li>
                <code>https://example.com/?var=val&anothervar=val</code><br>
            </li>
        </ul>

        <hr>

        ".$tooltip["protocol"]);

    $tooltip["custom"] = $newTooltip("
        <h4>Custom Script</h4>
        Enter a custom script (HTML) that will be shown when the short URL is visited.
        <br><br>
        <b class='text-danger'>This is potentially dangerous and can be used to execute malicious code, as script tags are also allowed.</b>
    ");

    $tooltip["alias"] = $newTooltip("
        <h4>Alias URL</h4>
        <p class='text-danger'>Required</p>
        The URL that the short URL will be an alias of. Any URL should work, just like in redirect URL.
        Main difference is that the alias will be displayed in a fullscreen iframe instead of redirected to.
        This means the URL in the address field of the browser will not change.
        <hr>
    ".$tooltip["protocol"]);

    $tooltip["delay"] = $newTooltip("
        <h4>Delay</h4>
        <p class='text-danger'>Required</p>
        The delay in seconds before the redirect or alias is executed.
        <br><br>
        <b>Default:</b> <code>".$cfg["default_delay"]."</code>
    ");
?>