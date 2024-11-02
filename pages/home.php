<?php
$protocol_tooltip = "
    <h4>Protocol</h4>
    <p class='text-danger'>Required</p>
    The protocol of the URL (e.g. <code>http://</code> or <code>https://</code>).<br>
    <b>Note:</b> It is recommended to use <code>http://</code> for compatibility, usually websites will redirect to <code>https://</code>
    automatically if it's available.<br>
    <b>Default:</b> <code>".$cfg["default_protocol"]."</code>
";
?>

<div class="container-fluid d-flex justify-content-center">
    <div class="card m-3 border border-primary">
                <h1 class="card-header text-bg-primary">URL Shortener</h1>
                <div class="card-body">
                    <p class="text-muted">
                        Here you can create new short URLs. Hover over the question mark icons for more information.
                    </p>
                    <br>
                    <form class="dynamic-form" action="index.php" method="POST" data-action="createshort">
                        <table class="table table-default">

                            <tr class="urlInputRow" data-input="short">
                                <td>
                                    Short URL
                                    <?= tooltip("
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
                                                <code>[empty]</code> would become <code>".$cfg["base_url"]."/".genStr($cfg["short_default"])."</code><br>
                                            </li>
                                        </ul>
                                    ")
                                    ?>
                                </td>
                                <td>
                                    <div class="input-group m-1">
                                        <span class="input-group-text">
                                            <?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') ?>://<?= $_SERVER['HTTP_HOST'] ?>/
                                        </span>
                                        <!--<input type="hidden" id="shortgen" name="shortgen" value="'.$shortGen.'">-->
                                        <input class="form-control newUrlInput" type="text" name="short" placeholder="Short URL" pattern="[A-Za-z0-9]*" title="Only alphanumeric characters are allowed">
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="text-danger mx-1">*</span>
                                    Type
                                    <?= tooltip("
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
                                    ") ?>
                                </td>
                                <td>
                                    <div class="form-group m-1">
                                        <select class="form-select newUrlInput" name="type">
                                            <?php
                                                foreach ($cfg["url_types"] as $type) {
                                                    echo '<option value="'.$type["value"].'">'.$type["name"].'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>

                            <!--
                            /* ────────────────────────────────────────────────────────────────────────── */
                            /*                                  REDIRECT                                  */
                            /* ────────────────────────────────────────────────────────────────────────── */
                            -->
                            <tr class="urlInputRow" data-input="redirect">
                                <td>
                                    <span class="inline">  
                                        <span class="text-danger mx-1">*</span>
                                        Redirect URL <?= tooltip("
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

                                        $protocol_tooltip
                                        ") ?>
                                    </span>
                                </td>
                                <td>
                                    <?= urlInput("redirect_dest") ?>
                                </td>
                            </tr>

                            <!--
                            /* ────────────────────────────────────────────────────────────────────────── */
                            /*                                   ALIAS                                    */
                            /* ────────────────────────────────────────────────────────────────────────── */
                            -->
                            <tr class="urlInputRow" data-input="alias" style="display:none;">
                                <td>
                                    <span>
                                        <span class="text-danger mx-1">*</span>
                                        Alias URL
                                        <?= tooltip("
                                            <h4>Alias URL</h4>
                                            <p class='text-danger'>Required</p>
                                            The URL that the short URL will be an alias of. Any URL should work, just like in redirect URL.
                                            Main difference is that the alias will be displayed in a fullscreen iframe instead of redirected to.
                                            This means the URL in the address field of the browser will not change.
                                            <hr>
                                            $protocol_tooltip
                                        ") ?>
                                    </span>
                                </td>
                                <td>
                                    <?= urlInput("alias_dest") ?>
                                </td>
                            </tr>

                            <!--
                            /* ────────────────────────────────────────────────────────────────────────── */
                            /*                                  CUSTOM                                   */
                            /* ────────────────────────────────────────────────────────────────────────── */
                            -->
                            <tr class="urlInputRow" data-input="custom" style="display:none;">
                                <td>
                                    <span class="text-danger mx-1">*</span>
                                    Custom Script
                                    <?= tooltip("
                                        <h4>Custom Script</h4>
                                        Enter a custom script (HTML) that will be shown when the short URL is visited.
                                        <br><br>
                                        <b class='text-danger'>This is potentially dangerous and can be used to execute malicious code, as script tags are also allowed.</b>
                                    ") ?>
                                </td>
                                <td>
                                    <div class="newUrlInput codeBox codeInput" name="custom_dest" placeholder="Custom Script"></div>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td colspan="100%">
                                    <input class="btn btn-success" name="createshort" type="submit" value="Create">
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
</div>