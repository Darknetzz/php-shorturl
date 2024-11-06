<?php
            /* ────────────────────────────────────────────────────────────────────────── */
            /*                                NOTE: Config                                */
            /* ────────────────────────────────────────────────────────────────────────── */
            $headerClass    = "bg-primary-subtle";
            $activeClass    = "bg-secondary-subtle";
            $listGroupClass = "list-group-item list-group-item-action text-center adminNavLink";
            $listLinkClass  = "link-info text-decoration-none user-select-none pe-none";

            /* ────────────────────────────────────────────────────────────────────────── */
            /*                             # NOTE: API inputs                             */
            /* ────────────────────────────────────────────────────────────────────────── */
            $getUrls = getUrls();
            $urlOptions = [];
            foreach ($getUrls as $id => $url) {

                $id       = (!empty($url['id']) ? $url['id'] : Null);
                $type     = (!empty($url['type']) ? $url['type'] : Null);
                $protocol = (!empty($url['protocol']) ? $url['protocol'] : Null);
                $short    = (!empty($url['short']) ? $url['short'] : Null);
                $dest     = (!empty($url['dest']) ? $url['dest'] : Null);

                $urlOptions[$url['id']] =  
                    [
                        "id"       => $id,
                        "type"     => $type,
                        "protocol" => $protocol,
                        "short"    => $short,
                        "dest"     => $dest,
                    ];
                }
    
                # URL inputs
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
                        "type"  => "text",
                        "name"  => "short",
                        "value" => genStr($cfg["short_default"]),
                    ],
                    "dest" => [
                        "type"  => "textarea",
                        "name"  => "dest",
                        "value" => "",
                    ],
                ];
    
                # Existing URL inputs
                $existingURLInputs = array_merge([
                    "id" => [
                        "type"  => "url",
                        "name"  => "id",
                        "value" => "",
                        "options" => $urlOptions,
                    ],
                ]);

                $allURLInputs = array_merge($existingURLInputs, $urlInputs);
    
                $apiEndpoints = [
                    "test"        => [
                        "description" => "Test API (does nothing except return OK)",
                        "method"      => "POST",
                        "inputs"      => [],
                        "class"       => "text-success",
                    ],

                    "invalid"   => [
                        "description" => "Invalid API Action (does nothing except return warning)",
                        "method"      => "POST",
                        "inputs"      => [],
                        "class"       => "text-warning",
                    ],

                    "error"   => [
                        "description" => "Error API Action (does nothing except return error)",
                        "method"      => "POST",
                        "inputs"      => [],
                        "class"       => "text-danger",
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
                        "inputs"      => $allURLInputs,
                    ],
                        
                    "list"        => [
                        "description" => "List Short URLs",
                        "method"      => "POST",
                        "inputs"      => [],
                    ],
                        
                ];

            ?>

<div class="container-fluid">
    <div class="d-flex justify-content-center">
        <?php
                if (!isset($_SESSION['id'])) {
                    die("Error: You must be logged in to access this page.");
                }

                if ($_SESSION['acl'] < 1) {
                    die("Error: You do not have permission to access this page.");
                }
            ?>

        <!--
            /* ────────────────────────────────────────────────────────────────────────── */
            /*                                NOTE: Sidebar                               */
            /* ────────────────────────────────────────────────────────────────────────── */
        -->
        <div class="sidebar m-3">
            <ul class="flex-column list-group">
                <li class="list-group-item <?= $headerClass ?>">
                    <h3 class="m-3">Admin Panel</h3>
                </li>
                <li class="<?= $listGroupClass ?>  <?= $activeClass ?>" data-action="dashboard">
                    <a class="<?= $listLinkClass ?>" href="javascript:void(0);">Dashboard</a>
                </li>
                <li class="<?= $listGroupClass ?> " data-action="api">
                    <a class="<?= $listLinkClass ?>" href="javascript:void(0);">API Tester</a>
                </li>
                <li class="<?= $listGroupClass ?> " data-action="ace">
                    <a class="<?= $listLinkClass ?>" href="javascript:void(0);">Ace Editor Tester</a>
                </li>
                <li class="<?= $listGroupClass ?> " data-action="users">
                    <a class="<?= $listLinkClass ?>" href="javascript:void(0);">Users</a>
                </li>
                <li class="<?= $listGroupClass ?> " data-action="logs">
                    <a class="<?= $listLinkClass ?>" href="javascript:void(0);">Logs</a>
                </li>
                <li class="<?= $listGroupClass ?> " data-action="settings">
                    <a class="<?= $listLinkClass ?>" href="javascript:void(0);">Settings</a>
                </li>
            </ul>
        </div>



        <div class="flex-fill m-3">

        <!--
        /* ────────────────────────────────────────────────────────────────────────── */
        /*                               NOTE: Dashboard                              */
        /* ────────────────────────────────────────────────────────────────────────── */
        -->
        <div class="card m-2 adminContent" data-action="dashboard">
            <h4 class="card-header <?= $headerClass ?>">
                Dashboard
            </h4>
            <div class="card-body">
                <h5>Welcome to the admin dashboard.</h5>
                <hr>
                <table class="table table-default">
                    <tr>
                        <th class="<?= $headerClass ?>" colspan="100%">
                            User Information
                        </th>
                    <tr>
                        <td>Logged in as</td>
                        <td><?= $_SESSION['username'] ?></td>
                    </tr>
                    <tr>
                        <td>Access Level</td>
                        <td><?= $_SESSION['acl'] ?></td>
                    </tr>
                    <tr>
                        <td>API Key</td>
                        <td><?= $_SESSION['id'] ?></td>
                    </tr>
                    <tr>
                        <th class="<?= $headerClass ?>" colspan="100%">
                            Application Information
                        </th>
                    </tr>

                </table>
            </div>
        </div>

        <!--
        /* ────────────────────────────────────────────────────────────────────────── */
        /*                              NOTE: API Tester                              */
        /* ────────────────────────────────────────────────────────────────────────── */
        -->
        <div class="card m-2 adminContent" data-action="api" style="display:none;">
            <h4 class="card-header <?= $headerClass ?>">
                API Tester
            </h4>
            <div class="card-body">
                <form id="api-test-form" class="dynamic-form" action="includes/api.php" data-output="#api-tester-response">
                    <table class="table table-default">
                        <!-- <tr>
                            <td>
                                API Key
                            </td>
                            <td>
                                <input type="text" class="form-control" id="test_api_key"
                                    value="<?= $_SESSION['id'] ?>" readonly>
                            </td>
                        </tr> -->
                        <tr>
                            <td>
                                Action
                            </td>
                            <td>
                                <select name="action" class="form-select" id="test_api_action">
                                    <?php
                                        foreach ($apiEndpoints as $action => $endpoint) {
                                            echo "<option value='$action' id='test_api_action_$action' data-method='$endpoint[method]'>$endpoint[description]</option>";
                                        }
                                    ?>
                                    <!--
                                    <option value="test">Test API (does nothing except return OK)</option>
                                    <option value="createshort">Create Short URL</option>
                                    <option value="delete">Delete Short URL</option>
                                    <option value="edit">Edit Short URL</option>
                                    <option value="list">List Short URLs</option>
                                    -->
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Method</td>
                            <td>
                                <select name="method" class="form-select" id="test_api_method" readonly>
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                </select>
                            </td>
                        </tr>
                        </table>

                        <div class="allTestAPIInputs m-3 p-3">
                            <?php
                            foreach ($apiEndpoints as $action => $endpoint) {
                                $displayOpts = ($action != "test") ? "style='display:none;'" : "";
                                echo "
                                    <div class='card testAPIInputs' data-action='$action' $displayOpts>
                                        <h4 class='card-header $headerClass'>Inputs for <span class='text-info'>$action</span></h4>
                                        <div class='card-body'>
                                        <p>{$endpoint['description']}</p>
                                ";
                                foreach ($endpoint['inputs'] as $input) {

                                    echo "
                                    <span class='test_api_input_name' data-namefor='{$input['name']}'>
                                        {$input['name']}
                                    </span>";
                                    $inputAttrs = "id='test_api_{$input['name']}' name='{$input['name']}'";
                                    
                                    # URL input
                                    if ($input["type"] == "url") {
                                        echo "<select class='form-select' $inputAttrs>";
                                        foreach ($input['options'] as $id => $url) {
                                            $destText = "Custom";
                                            if ($url['type'] != "custom") {
                                                $destText = $url['dest'];
                                            }
                                            echo "<option value='$id'>[#$id] $url[short] => $destText</option>";
                                        }
                                        echo "</select>";
                                        continue;
                                    }

                                    # Select input
                                    if ($input["type"] == "select") {
                                        echo "<select class='form-select' $inputAttrs>";
                                        foreach ($input['options'] as $name => $val) {
                                            $optionClass = "";
                                            if (!empty($input['class'])) {
                                                $optionClass = " class='{$input['class']}'";
                                            }
                                            echo "<option value='$name' class='$optionClass'>".$val."</option>";
                                        }
                                        echo "</select>";
                                        continue;
                                    }

                                    # Textarea input
                                    if ($input["type"] == "textarea") {
                                        echo "<textarea class='form-control' $inputAttrs>{$input['value']}</textarea>";
                                        continue;
                                    }

                                    # Default input
                                    echo "<input type='text' class='form-control' $inputAttrs value='{$input['value']}'>";
                                }
                                echo "</div>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                                <button name="testAPI" class="btn btn-lg btn-success testAPI m-3"><?= icon("send-fill") ?> Send</button>
                        </form>
                        <hr>
                        <div class="card m-2">
                            <h4 class="card-header <?= $headerClass ?>">API Response</h4>
                            <div class="card-body">
                                <pre id="api-tester-response" class="text-bg-secondary p-3 border border-secondary">
                                    Response will appear here
                                </pre>
                            </div>
                        </div>
                </div>
            </div>


        <!--
          /* ────────────────────────────────────────────────────────────────────────── */
          /*                          NOTE: Ace Editor Tester                          */
          /* ────────────────────────────────────────────────────────────────────────── */
        -->
        <div class="card m-2 adminContent" data-action="ace" style="display: none;">
            <h4 class="card-header <?= $headerClass ?>">
                Ace Editor Tester
            </h4>
            <div class="card-body">

                <div class="card m-2">
                    <h3 class="card-header">Code</h3>
                    <div class="card-body">
                        <pre class="codeInput codeBox"></pre>
                    </div>
                </div>

                <button class="btn btn-lg btn-success m-3"><?= icon("check2") ?> Submit</button>

            </div>
        </div>

        <!--
        /* ────────────────────────────────────────────────────────────────────────── */
        /*                                 NOTE: Logs                                 */
        /* ────────────────────────────────────────────────────────────────────────── */
        -->
        <div class="card m-2 adminContent" data-action="logs" style="display:none;">
            <h4 class="card-header <?= $headerClass ?>">
                Logs
            </h4>
            <div class="card-body">
                <?php
                    $logs = query("SELECT * FROM logs ORDER BY `timestamp` DESC");
                    if (count($logs) == 0) {
                        echo "<p>No logs found.</p>";
                    } else {
                        echo "
                        <table class='table table-default' data-toggle='table' data-pagination='true'>
                        <thead>
                            <tr class='table-info'>
                                <th>Time</th><th>IP</th><th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                        ";
                        foreach ($logs as $log) {
                            $event = str_replace("short", "<span class='text-info'>short</span>", $log);
                            $event = preg_replace('/#(\d+)/', '<span class="text-info">#$1</span>', $log['event']);
                            $event = stripslashes($log['event']);
                            echo "<tr><td>$log[timestamp]</td><td>$log[ip]</td><td>$event</td></tr>";
                        }
                        echo "</table>";
                    }
                ?>
            </div>
        </div>


        <!--
        /* ────────────────────────────────────────────────────────────────────────── */
        /*                               NOTE: Settings                               */
        /* ────────────────────────────────────────────────────────────────────────── */
        -->
        <div class="card m-2 adminContent" data-action="settings" style="display:none;">
            <h4 class="card-header <?= $headerClass ?>">
                Settings
            </h4>
            <div class="card-body">
                <table class="table table-default">
                <?php
                    foreach ($cfg as $key => $val) {
                        if (is_array($val)) {
                            $input = "<pre>".json_encode($val, JSON_PRETTY_PRINT)."</pre>";
                        } else {
                            $input = "<input type='text' class='form-control' value='$val'>";
                        }
                        echo "
                        <tr>
                            <td>$key</td>
                            <td>$input</td>
                        </tr>";
                    }
                ?>
                </table>
            </div>
        </div>


        <!--
        /* ────────────────────────────────────────────────────────────────────────── */
        /*                               NOTE: Users                                  */
        /* ────────────────────────────────────────────────────────────────────────── */
        -->
        <div class="card m-2 adminContent" data-action="users" style="display:none;">
            <h4 class="card-header bg-primary-subtle">
                Users
            </h4>
            <div class="card-body">
                <?php
                    $users = query("SELECT * FROM users");
                    if (count($users) == 0) {
                        echo "<p>No users found.</p>";
                    } else {
                        echo "
                        <table class='table table-default' data-toggle='table' data-pagination='true'>
                        <thead>
                            <tr class='table-info'>
                                <th>ID</th><th>Username</th><th>ACL</th>
                            </tr>
                        </thead>
                        <tbody>
                        ";
                        foreach ($users as $user) {
                            echo "<tr><td>$user[id]</td><td>$user[username]</td><td>$user[acl]</td></tr>";
                        }
                        echo "</table>";
                    }
                ?>
            </div>
        </div>

    </div> <!-- /flex-fill -->

</div> <!-- /d-flex -->

</div> <!-- /container-fluid -->

<script>
$(document).ready(function() {

    // Check for hashtag in URL and simulate click on corresponding .adminNavLink
    var hash = window.location.hash.substring(1);
    if (hash) {
        var targetContent = $(".adminContent[data-action='" + hash + "']");
        if (targetContent.length) {
            $(".adminNavLink").removeClass("<?= $activeClass ?>");
            $(".adminNavLink[data-action='" + hash + "']").addClass("<?= $activeClass ?>");
            $(".adminContent").hide();
            targetContent.show();
        }
    }

    // NOTE: adminNavLink
    $(".adminNavLink").on("click", function() {
        var activeClass = "<?= $activeClass ?>";
        var action = $(this).data("action");
        var target = $(".adminContent[data-action='" + action + "']");
        $(".adminContent").hide();
        $(".adminNavLink").removeClass(activeClass);
        $(this).addClass(activeClass);
        target.show();
        window.location.hash = action;
    });

    // NOTE: Action
    $("#test_api_action").on("change", function() {
        var method = $("#test_api_action option:selected").data("method");
        utils.hideObject(".testAPIInputs");
        utils.showObject(".testAPIInputs[data-action='" + $(this).val() + "']");
        // TODO: Fix
        $("#test_api_method").val(method);
    });

    // NOTE: Type
    $("[name=type]").on("change", function() {
        var type = $(this).val();
        $("#test_api_dest").attr("name", type + "_dest");
    });

    $("#api-test-form").on("submit", function() {
        
    });


});
</script>