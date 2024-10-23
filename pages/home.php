<div class="container">
    <div class="card m-3 border border-primary">
                <h1 class="card-header text-bg-primary">URL Shortener</h1>
                <div class="card-body">
                    <p>Enter a short URL and a destination URL to create a new short URL.</p>
                    <form class="dynamic-form" action="index.php" method="POST" data-action="createshort">
                        <table class="table table-default">

                            <tr>
                                <td>
                                    <span class="text-danger">*</span>
                                    Type
                                </td>
                                <td>
                                    <div class="form-group m-1">
                                        <select class="form-select" name="type">
                                            <?php
                                                foreach ($cfg["url_types"] as $type) {
                                                    echo '<option value="'.$type["value"].'">'.$type["name"].'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Short URL</td>
                                <td>
                                    <div class="input-group m-1">
                                        <span class="input-group-text">
                                            <?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') ?>://<?= $_SERVER['HTTP_HOST'] ?>/
                                        </span>
                                        <!--<input type="hidden" id="shortgen" name="shortgen" value="'.$shortGen.'">-->
                                        <input class="form-control" type="text" name="short" placeholder="Short URL" pattern="[A-Za-z0-9]*" title="Only alphanumeric characters are allowed">
                                    </div>
                                    <span class="text-muted">
                                        <b>Optional</b> The short URL that you want to use.
                                        If left empty, it will be generated for you.</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="text-danger">*</span>
                                    Destination URL
                                </td>
                                <td>
                                    <div class="input-group m-1">
                                        <div class="input-group-text p-0">
                                            <select class="form-select border-0 url-protocol" name="protocol">
                                                <option value="http://">http://</option>
                                                <option value="https://" selected>https://</option>
                                            </select>
                                        </div>
                                        <input class="form-control url-input" type="text" name="dest"
                                            placeholder="Destination URL (ex. example.com)"
                                            required>
                                    </div>
                                    <span class="text-muted">
                                        <b>Required</b>
                                        The URL that the short URL should redirect to.
                                    </span>
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