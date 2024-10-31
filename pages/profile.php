<div class="container">
    <?php

        if (!isset($_SESSION['id'])) {
            echo alert("You must be logged in to view this page.", "danger");
            echo jsRedirect("index.php");
            die();
        }

        $getUrls = getUrls();
        $urlOptions = [];
        foreach ($getUrls as $id => $url) {
            $urlOptions[$url['id']] =  
            [
                "id"       => $url['id'],
                "type"     => $url['type'],
                "protocol" => $url['protocol'],
                "short"    => $url['short'],
                "dest"     => $url['dest'],
            ];
        }

        $urlInputs = [
            "type" => [
                "type"  => "select",
                "name"  => "type",
                "value" => "",
                "options" => [
                    "redirect" => "Redirect",
                    "alias"    => "Alias",
                    "custom"   => "Custom",
                ],
            ],
            "protocol" => [
                "type"  => "select",
                "name"  => "protocol",
                "value" => $cfg["default_protocol"],
                "options" => [
                    "http://"  => "http://",
                    "https://" => "https://",
                ],
            ],
            "short" => [
                "name"  => "short",
                "value" => genStr($cfg["short_default"]),
            ],
            "dest" => [
                "type"  => "textarea",
                "name"  => "dest",
                "value" => "",
            ],
        ];


        $existingURLInputs = array_merge([
            "id" => [
                "type"  => "select",
                "name"  => "id",
                "value" => "",
                "options" => $urlOptions,
            ],
        ], $urlInputs);

        $apiEndpoints = [
            "test"        => [
                "description" => "Test API (does nothing except return OK)",
                "method"      => "POST",
                "inputs"      => []
            ],
                
            "createshort" => [
                "description" => "Create Short URL",
                "method"      => "POST",
                "inputs"      => $urlInputs,
            ],
                
            "delete"      => [
                "description" => "Delete Short URL",
                "method"      => "POST",
                "inputs"      => $existingURLInputs,
            ],
                
            "edit"        => [
                "description" => "Edit Short URL",
                "method"      => "POST",
                "inputs"      => $existingURLInputs,
            ],
                
            "list"        => [
                "description" => "List Short URLs",
                "method"      => "POST",
                "inputs"      => [],
            ],
                
        ]

    ?>

    <div class="card m-2">
        <h3 class="card-header">Profile</h3>
        <div class="card-body">
            <h5 class="card-title"><?= $_SESSION['username'] ?></h5>
            <p class="card-text">Role: <?= aclToText($_SESSION['acl']) ?></p>


            <h5 class="card-title">Admin</h5>
            <div class="btn-group adminBtns" role="group">
                <a class="btn btn-outline-primary" href="?do=edit">Edit Profile</a>
            </div>

            <hr>

            <div class="card profile-api-card">
                <h5 class="card-header">API</h5>
                <div class="card-body">
                    <form class="dynamic-form">
                        <table class="table table-default">
                            <tr>
                                <td><label for="api_key">API Key</label></td>
                                <td><input type="text" class="form-control" id="api_key" value="<?= $_SESSION['id'] ?>" readonly></td>
                            </tr>
                            <tr>
                                <td><label for="api_method">Method</label></td>
                                <td>
                                    <select name="method" class="form-select" id="api_method">
                                        <option value="GET">GET</option>
                                        <option value="POST">POST</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="api_action">Action</label></td>
                                <td>
                                    <select name="action" class="form-select" id="api_action">
                                        <option value="test">Test API (does nothing except return OK)</option>
                                        <option value="createshort">Create Short URL</option>
                                        <option value="delete">Delete Short URL</option>
                                        <option value="edit">Edit Short URL</option>
                                        <option value="list">List Short URLs</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Inputs</td>
                                <td>
                                <?php
                                    foreach ($apiEndpoints as $action => $endpoint) {
                                        $displayOpts = ($action != "test") ? "style='display:none;'" : "";
                                        echo "
                                            <div class='testAPIInputs text-bg-secondary p-2 m-2' data-action='$action' $displayOpts>
                                            <h6>$action</h6>
                                            <p>{$endpoint['description']}</p>
                                        ";
                                        foreach ($endpoint['inputs'] as $input) {
                                            if ($input["type"] == "select") {
                                                echo "<label for='{$input['id']}'>{$input['name']}</label>";
                                                echo "<select name='{$input['name']}' class='form-select'>";
                                                foreach ($input['options'] as $name => $val) {
                                                    echo "<option value='$name'>".$val."</option>";
                                                }
                                                echo "</select>";
                                                continue;
                                            }
                                            if ($input["type"] == "textarea") {
                                                echo "<label for='{$input['name']}'>{$input['name']}</label>";
                                                echo "<textarea name='{$input['name']}' class='form-control'>{$input['value']}</textarea>";
                                                continue;
                                            }
                                            echo "<label for='{$input['name']}'>{$input['name']}</label>";
                                            echo "<input type='text' class='form-control' name='{$input['name']}' value='{$input['value']}'>";
                                        }
                                        echo "</div>";
                                    }
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button class="btn btn-success testAPI">Send</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>

            
            <hr>

            <h5 class="card-title">User</h5>
            <div class="btn-group profileBtns" role="group">
                <a class="btn btn-outline-primary" href="?do=resetpw">Change Password</a>
                <a class="btn btn-outline-primary" href="?do=urls">View URLs</a>
                <a class="btn btn-outline-danger" href="?do=logout">Logout</a>
            </div>
        </div>
    </div>
</div>