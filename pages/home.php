<div class="container">
    <div class="card m-3 border border-primary">
                <h1 class="card-header text-bg-primary">URL Shortener</h1>
                <div class="card-body">
                    <p>Enter a short URL and a destination URL to create a new short URL.</p>
                    <form class="dynamic-form" action="index.php" method="POST" data-action="createshort">
                        <table class="table table-default">

                            <tr class="urlInputRow" data-input="short">
                                <td>Short URL</td>
                                <td>
                                    <div class="input-group m-1">
                                        <span class="input-group-text">
                                            <?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') ?>://<?= $_SERVER['HTTP_HOST'] ?>/
                                        </span>
                                        <!--<input type="hidden" id="shortgen" name="shortgen" value="'.$shortGen.'">-->
                                        <input class="form-control newUrlInput" type="text" name="short" placeholder="Short URL" pattern="[A-Za-z0-9]*" title="Only alphanumeric characters are allowed">
                                    </div>
                                    <span class="text-muted">
                                        <b>Optional</b> The short URL that you want to use.
                                        If left empty, it will be generated for you.
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="text-danger">*</span>
                                    Type
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
                            /*                                   ALIAS                                    */
                            /* ────────────────────────────────────────────────────────────────────────── */
                            -->
                            <tr class="urlInputRow" data-input="alias" style="display:none;">
                                <td>
                                    <span class="text-danger">*</span>
                                    Alias
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
                                    <span class="text-danger">*</span>
                                    Custom Script
                                </td>
                                <td>
                                    <div class="form-group-text">
                                        <textarea class="form-control newUrlInput" name="custom_dest" placeholder="Custom Script" rows="3"></textarea>
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
                                    <span class="text-danger">*</span>
                                    Destination URL
                                </td>
                                <td>
                                    <?= urlInput("redirect_dest") ?>
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