<?php

# FUNCTION: $listTypes
# This function is used to generate a list of types.
$listTypes = function(?array $types = []) {
    $list = "";
    $list .= "<h5>Available types</h5>";
    if (empty($types)) {
        $list .= "<p>No types available</p>";
        return $list;
    }
    foreach ($types as $type) {
        if (empty($type["value"])) {
            continue;
        }
        $name         = (!empty($type["name"]) ? $type["name"] : "No name");
        $description  = (!empty($type["description"]) ? $type["description"] : "No description");
        $list        .= "
        <h6 class='text-info typeName mt-2'>".$name."</h6>
        <li>".$description."</li><br>";
    }
    return $list;
};

# FUNCTION: $exampleTable
# This function is used to generate a table with examples.
$exampleTable = function(
                array $examples,
                array $cols = [
                    "Input",
                    "Output",
                    "Explanation"
                ]
            ) {
    global $cfg;
    $table = "
    <h6>Examples with <code>$cfg[base_domain]</code> as domain</h6>
    <ul>";
        // foreach ($cols as $col) {
        //     $table .= "<li>$col</li>";
        // }
        foreach ($examples as $row) {
            foreach ($row as $key => $value) {
                $table .= "<li>$cols[$key]: <code>$value</code></li>";
            }
        }
    $table .= "</ul>";
    return $table;
};

/**
* protocol_types
*/
$selectOptions["protocol_types"] = [
    [
        "name"  => "HTTP",
        "value" => "http://",
        "description" => "The HTTP protocol.",
    ],
    [
        "name"  => "HTTPS",
        "value" => "https://",
        "description" => "The HTTPS protocol.",
    ],
];

/**
* short_types
* The types of destinations supported by the application.
*/
$selectOptions["short_types"] = [
    [
        "name"        => "Please select...",
        "value"       => Null,
        "description" => Null,
        "attributes"  => "disabled selected",
    ],
    [
        "name"        => "Path",
        "value"       => "path",
        "description" => "
            Short URL will be a path on the same domain.<br>".
            $exampleTable([
                ["shortname", $cfg['base_url']."/shortname", "The short URL will be appended to the domain."],
            ]),
        "attributes"    => "data-requires='short'",
    ],
    [
        "name"        => "Subdomain",
        "value"       => "subdomain",
        "description" => "
            Short URL will be a subdomain.<br>
        ".
        $exampleTable([
            ["shortname", "shortname.".$cfg['base_domain'], "The short URL will be a subdomain of the domain."],
        ])
    ],
    [
        "name"        => "Custom",
        "value"       => "custom",
        "description" => "
            Custom URL. Can be anything. You <b>must</b> include the protocol (http/https).<br>
        ".
        $exampleTable([
            ["http://example.com", "http://example.com", "The short URL will be the same as the destination URL."],
            ["https://example.com", "https://example.com", "The short URL will be the same as the destination URL."],
            ["http://example.com/path", "http://example.com/path", "The short URL will be the same as the destination URL."],
            ["https://example.com/path", "https://example.com/path", "The short URL will be the same as the destination URL."],
        ])
    ],
];

/**
* dest_types
* The types of destinations supported by the application.
*/
$selectOptions["dest_types"] = [
    [
        "name"        => "Please select...",
        "value"       => "none",
        "description" => "No destination. Used for testing.",
        "attributes"  => "disabled selected",
    ],
    [
        "name"        => "Redirect",
        "value"       => "redirect",
        "description" => "Redirects to a URL. Replaces the URL in the address field of the browser.",
        "attributes"  => "data-requires='protocol,redirect'",
    ],
    [
        "name"        => "Alias",
        "value"       => "alias",
        "description" => "Attempts to display the destination URL in a fullscreen iframe. This might not always work.",
        "attributes"  => "data-requires='protocol,alias'",
    ],
    [
        "name"        => "Custom (HTML)",
        "value"       => "custom",
        "description" => "Custom HTML.",
        "attributes"  => "data-requires='custom'",
    ],
];

# FUNCTION: $newTooltip
# This function is used to generate a new tooltip.
$newTooltip = 
function ($text = "Tooltip", $icon = "question-circle", $html = True, $placement = "top") {
if ($html !== "false") {
    $html = "true";
}
return '
    <a class="btn btn-default"
        data-bs-toggle="tooltip" 
        data-bs-title="'.$text.'" 
        data-bs-placement="'.$placement.'" 
        data-bs-custom-class="input-tooltip" 
        data-bs-html="'.$html.'"
        title="'.$text.'">
        <span class="bi bi-'.$icon.'" style="font-size:1.5rem;"></span>
    </a>';
};

# NOTE: Input tooltips
$tooltip["name"] = "
    <h4>Name</h4>
    <p class='text-success'>Optional</p>
    The name of the short URL. This is only used for identification purposes.
    If not specified, name will be the same as the short URL.
";

$tooltip["protocol"] = "
    <h4>Destination Protocol</h4>
    <p class='text-danger'>Required</p>
    The protocol of the URL (e.g. <code>http://</code> or <code>https://</code>).<br>
    <b>Note:</b> It is recommended to use <code>http://</code> for compatibility, usually websites will redirect to <code>https://</code>
    automatically if it's available.<br>
    <b>Default:</b> <code>".$cfg["default_protocol"]."</code>
";

$tooltip["short_type"] = "
    <h4>Short URL Type</h4>
    <p class='text-danger'>Required</p>
    The type of the short URL. This determines how the short URL will behave.
    <br><br>
    ".$listTypes($selectOptions["short_types"]);

$tooltip["short"] = "
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
";

$tooltip["dest_type"] = "
    <h4>Destination Type</h4>
    <p><span class='text-danger'>Required</span> (Default: <code>Redirect</code>)</p>
    Specifies how the short URL will behave.

    <hr>
".$listTypes($selectOptions["dest_types"]);
    // <h4>Available types</h4>
    // <ul>
    //     <li>
    //         <h6>Redirect</h6>
    //         Redirects to a URL. Meaning it will replace the URL in the address field of the browser.
    //     </li>
        
    //     <br>

    //     <li>
    //         <h6>Alias</h6>
    //         Attempts to display the destination URL in a fullscreen iframe (might not always work).
    //         This means it will not change the URL in the address field of the browser.
    //     </li>
        
    //     <br>

    //     <li>
    //         <h6>Custom</h6>
    //         Executes a custom script.
    //     </li>
    // </ul>


$tooltip["redirect"] = "
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

    ".$tooltip["protocol"];

$tooltip["custom"] = "
    <h4>Custom Script</h4>
    Enter a custom script (HTML) that will be shown when the short URL is visited.
    <br><br>
    <b class='text-danger'>This is potentially dangerous and can be used to execute malicious code, as script tags are also allowed.</b>
";

$tooltip["alias"] = "
    <h4>Alias URL</h4>
    <p class='text-danger'>Required</p>
    The URL that the short URL will be an alias of. Any URL should work, just like in redirect URL.
    Main difference is that the alias will be displayed in a fullscreen iframe instead of redirected to.
    This means the URL in the address field of the browser will not change.
    <hr>
".$tooltip["protocol"];

$tooltip["delay"] = "
    <h4>Delay</h4>
    <p class='text-danger'>Required</p>
    The delay in seconds before the redirect or alias is executed.
    <br><br>
    <b>Default:</b> <code>".$cfg["default_delay"]."</code>
";

# NOTE: $urlInputs
# This array contains all the inputs for the URL form.
# id         : The ID of the input.
# name       : The name of the input.
# type       : The type of the input.
    # options: The options of the input (only for select).
# class      : The class of the input.
# placeholder: The placeholder of the input.
# default    : The default value of the input.
# required   : Whether the input is required or not.
# tooltip    : The tooltip of the input.
# description: The description of the input.
# script     : The script of the input.
$urlInputs = [
    "name"       => [
        "id"          => "nameInput",
        "title"       => "Name",
        "name"        => "name",
        "type"        => "text",
        "class"       => "form-control urlInput common",
        "placeholder" => "Name",
        "default"     => "",
        "required"    => False,
        "tooltip"     => $tooltip["name"],
        "description" => "The name of the short URL. This is only used for identification purposes.",
    ],
    "short_type" => [
        "id"          => "shortTypeInput",
        "title"       => "Short URL Type",
        "name"        => "short_type",
        "type"        => "select",
        "options"     => $selectOptions["short_types"],
        "class"       => "form-select urlInput",
        "default"     => "path",
        "required"    => True,
        "tooltip"     => $tooltip["short_type"],
        "description" => "The type of the short URL. This determines how the short URL looks.",
    ],
    "short_path"      => [
        "id"          => "shortPathInput",
        "title"       => "Path",
        "name"        => "short_path",
        "type"        => "text",
        "class"       => "form-control urlInput shortInput",
        "placeholder" => "",
        "default"     => "",
        "required"    => False,
        "hidden"      => True,
        "tooltip"     => $tooltip["short"],
        "description" => "The path that you want to use. Should only consist of alphanumeric characters. If not specified, it will be generated for you.",
    ],
    "short_domain"    => [
        "id"          => "shortDomainInput",
        "title"       => "Subdomain",
        "name"        => "short_domain",
        "type"        => "text",
        "class"       => "form-control urlInput shortInput",
        "placeholder" => "",
        "default"     => "",
        "required"    => True,
        "hidden"      => True,
        "tooltip"     => $tooltip["short"],
        "description" => "<b class='text-warning'>Requires additional DNS setup.</b> The subdomain that you want to use. Should only consist of alphanumeric characters and period (.).",
    ],
    "short_custom"    => [
        "id"          => "shortCustomInput",
        "title"       => "Custom URL",
        "name"        => "short_custom",
        "type"        => "text",
        "class"       => "form-control urlInput shortInput",
        "placeholder" => "",
        "default"     => "",
        "required"    => True,
        "hidden"      => True,
        "tooltip"     => $tooltip["short"],
        "description" => "<b class='text-warning'>Requires the custom URL to point at this webserver.</b> Custom URL that you want to use.",
    ],
    "dest_type"  => [
        "id"          => "destTypeInput",
        "title"       => "Destination Type",
        "name"        => "dest_type",
        "type"        => "select",
        "options"     => $selectOptions["dest_types"],
        "class"       => "form-control urlInput",
        "default"     => "redirect",
        "required"    => True,
        "hidden"      => True,
        "tooltip"     => $tooltip["dest_type"],
        "description" => "Specifies how the destination URL will behave.",
    ],
    "protocol"   => [
        "id"          => "protocolInput",
        "title"       => "Destination Protocol",
        "name"        => "protocol",
        "type"        => "select",
        "options"     => $selectOptions["protocol_types"],
        "class"       => "form-select urlInput destInput",
        "placeholder" => "http://",
        "default"     => $cfg["default_protocol"],
        "required"    => True,
        "hidden"      => True,
        "tooltip"     => $tooltip["protocol"],
        "description" => "The protocol of the destination URL (e.g. <code>http://</code> or <code>https://</code>).",
    ],
    "redirect"   => [
        "id"          => "redirectInput",
        "title"       => "Redirect URL",
        "name"        => "dest_redirect",
        "type"        => "text",
        "class"       => "form-control urlInput destInput",
        "placeholder" => "http://example.com",
        "default"     => "",
        "required"    => False,
        "hidden"      => True,
        "tooltip"     => $tooltip["redirect"],
        "description" => "The URL the short URL will redirect to.",
    ],
    "alias"      => [
        "id"          => "aliasInput",
        "title"       => "Alias URL",
        "name"        => "dest_alias",
        "type"        => "text",
        "class"       => "form-control urlInput destInput",
        "placeholder" => "http://example.com",
        "default"     => "",
        "required"    => False,
        "hidden"      => True,
        "tooltip"     => $tooltip["alias"],
        "description" => "The URL that the short URL will be an alias of.",
    ],
    "custom"     => [
        "id"          => "customInput",
        "title"       => "Custom Script",
        "name"        => "dest_custom",
        "type"        => "textarea",
        "class"       => "form-control urlInput destInput",
        "placeholder" => "",
        "default"     => "",
        "required"    => False,
        "hidden"      => True,
        "tooltip"     => $tooltip["custom"],
        "description" => "Enter a custom script (HTML) that will be shown when the short URL is visited.",
    ],
    "delay"      => [
        "id"          => "delayInput",
        "name"        => "Delay",
        "type"        => "number",
        "class"       => "form-control urlInput",
        "placeholder" => "100",
        "default"     => $cfg["default_delay"],
        "required"    => False,
        "hidden"      => True,
        "tooltip"     => $tooltip["delay"],
        "description" => "The delay in seconds before the redirect",
    ],
];

# FUNCTION: $urlForm
$urlForm = function($action = "create", $values = []) {
    global $newTooltip;
    global $urlInputs;
    $form = '
    <div class="d-flex justify-content-center">
        <form class="dynamic-form" id="urlForm" action="index.php" method="POST" data-action="'.$action.'">
            <p class="text-muted">
                Here you can create new short URLs. Hover over the question mark icons for more information.
            </p>
            <input class="urlActionInput" type="hidden" name="action" value="'.$action.'">
            <table class="table table-default">
                <tbody>
    ';
    foreach ($urlInputs as $inputName => $input) {
        foreach ($input as $key => $value) {
            $i[$key] = (!empty($value) ? $value : Null);
        }

        $rowid         = $i["id"]."Row";
        $i["required"] = ($i["required"] !== False ? '<span class="form-text text-danger">*</span>' : '');
        $i["style"]    = ($i["hidden"] != False ? 'display:none;' : '');
        $i["data"]     = 'id="'.$i["id"].'" class="'.$i["class"].'" name="'.$inputName.'" data-input="'.$inputName.'" placeholder="'.$i["placeholder"].'"';

        # NOTE: Any input
        $thisInput = '<input '.$i["data"].' type="'.$i["type"].'" value="'.$i["value"].'" '.$i["attributes"].'>';

        # NOTE: Select input
        if ($i["type"] == "select") {
            $thisInput = '<select '.$i["data"].'>';
            foreach ($i["options"] as $option) {
                $selected = ($option["value"] == $i["value"] ? "selected" : "");
                $thisInput .= '<option value="'.$option["value"].'" '.$selected.'>'.$option["name"].'</option>';
            }
            $thisInput .= '</select>';
        } 
        # NOTE: Textarea input
        if ($i["type"] == "textarea") {
            $thisInput = '<textarea '.$i["data"].'>'.$i["value"].'</textarea>';
        }

        $form .= '
        <tr id="'.$rowid.'" class="urlInputRow" data-input="'.$i["name"].'" style="'.$i["style"].'">
                <td>
                    '.$i["title"].'
                    '.$i["required"].'
                </td>
                <td>
                    '.$newTooltip($i["tooltip"]).'
                </td>
                <td>
                    <div class="input-group m-1">
                        '.$thisInput.'
                    </div>
                    <p class="form-text urlInputDescription">'.$i["description"].'</p>
                </td>
                <td>

            </tr>
        ';
    }
    $submitBtn = '<input class="btn btn-success" name="action" type="submit" value="Submit">';
    if ($action == "create") {
        $submitBtn = '<input class="btn btn-success" name="action" type="submit" value="Create">';
    } 
    if ($action == "edit") {
        $submitBtn = '
            <a class="btn btn-primary" target="_blank" href="'.$i["value"].'">Open</a>
            <input class="btn btn-success" name="action" type="submit" value="Update">
        ';
    }
    $form .= '
            </tbody>
        </table>
        '.$submitBtn.'
    </form>
    </div>
    ';
    return $form;
}
?>