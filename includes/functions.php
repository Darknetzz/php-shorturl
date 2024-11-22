<?php

/* ────────────────────────────────────────────────────────────────────────── */
/*                               FUNCTION alert                               */
/* ────────────────────────────────────────────────────────────────────────── */
function alert($msg, $type = "info", $icon = Null, $persistent = False, $dismissable = True) {
    
    $baseClass        = "alert border-$type text-$type fade show m-3";
    $dismissableClass = "alert-dismissible";
    $persistentClass  = "alert-persistent";
    $dismissBtn       = Null;
    $class            = $baseClass;
    
    # Determine icon
    if ($type == "info" && $icon == Null) {
        $icon = "info-circle";
    } elseif ($type == "success" && $icon == Null) {
        $icon = "check-circle";
    } elseif ($type == "warning" && $icon == Null) {
        $icon = "exclamation-triangle";
    } elseif ($type == "danger" && $icon == Null) {
        $icon = "exclamation-circle";
    }
    $icon = icon($icon, 1.5);

    if ($dismissable) {
        $class      .= " $dismissableClass";
        $dismissBtn  = "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
    }
    if ($persistent) {
        $class .= " $persistentClass";
    }
        
    return "
    <div class='container'>
        <div class='$class' role='alert'>
            <span class='inline' style='align-items: center;'>
                <span class='mx-2'>$icon</span>
                <span class='mx-2'>$msg</span>
            $dismissBtn
        </div>
    </div>
    ";
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                                FUNCTION icon                               */
/* ────────────────────────────────────────────────────────────────────────── */
function icon($icon, $size = 1.2, $color = Null) {
    $style = "style='";
    $style .= ($color == Null ? "" : "color:$color;");
    $style .= "font-size:".$size."rem;'";
    return "<span class='bi bi-$icon' $style></span>";
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                              FUNCTION tooltip                              */
/* ────────────────────────────────────────────────────────────────────────── */
function tooltip($text = "Tooltip", $icon = "question-circle", $html = "true", $placement = "top") {
    global $tooltip;
    return $tooltip($text, $icon, $html, $placement);
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                               FUNCTION navBtn                              */
/* ────────────────────────────────────────────────────────────────────────── */
function navBtn($text, $url = Null, $icon = Null) {
    if ($url == Null) {
        $url = "javascript:void(0);";
    }
    $currentUrl  = $_SERVER['REQUEST_URI'];
    $activeClass = (strpos($currentUrl, $url) !== False) ? 'active link-white' : '';
    return "
        <li class='nav-item mx-2'>
            <a class='nav-link $activeClass' href='$url'>".icon($icon)." $text</a>
        </li>
    ";
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                            FUNCTION navDropdown                            */
/* ────────────────────────────────────────────────────────────────────────── */
function navDropdown($text, $links = [], $icon = Null) {
    $currentUrl  = $_SERVER['REQUEST_URI'];
    $activeClass = (in_array($currentUrl, $links) !== False) ? 'active link-white' : '';
    $dropdown    = "
    <li class='nav-item dropdown'>
        <a class='nav-link dropdown-toggle $activeClass' href='#' id='navbarDropdown' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
            ".icon($icon)."
            ".$text."
        </a>";
    $dropdown   .= "<ul class='dropdown-menu' aria-labelledby='navbarDropdown'>";
    if (!empty($links)) {
        foreach ($links as $link) {
            $icon = (isset($link["icon"]) ? icon($link["icon"]) : "");
            $dropdown .= "<li><a class='dropdown-item' href='".$link["url"]."'>".$icon." ".$link["text"]."</a></li>";
        }
    }
    $dropdown .= "</ul></li>";
    return $dropdown;
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                             FUNCTION jsRedirect                            */
/* ────────────────────────────────────────────────────────────────────────── */
function jsRedirect($url = "index.php", $time = 0) {
    if (!is_numeric($time) || $time == 0) {
        die("<script>window.location.href = '$url';</script>");
    }
    die("<script>
        setTimeout(function(){
            window.location.href = '$url';
        }, $time);
    </script>");
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                                FUNCTION cLog                               */
/* ────────────────────────────────────────────────────────────────────────── */
function cLog($msg) {
    return "<script>console.log('$msg');</script>";
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                          FUNCTION genStr                                   */
/* ────────────────────────────────────────────────────────────────────────── */
function genStr($length = 8) {
    if ($length < 1) {
        return False;
    }
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                               FUNCTION query                               */
/* ────────────────────────────────────────────────────────────────────────── */
function query($query, array $params = []) {
    global $sqlcon;

    if (!$sqlcon || $sqlcon->connect_error) {
        die("Connection failed: " . $sqlcon->connect_error);
    }

    $stmt = $sqlcon->prepare($query);

    $types = "";

    # Declare types
    if (count($params) > 0) {
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= "i";
            } elseif (is_float($param)) {
                $types .= "d";
            } elseif (is_string($param)) {
                $types .= "s";
            } else {
                $types .= "b";
            }
        }
        if ($types !== "" && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === False) {
        return [];
    }
    
    $data   = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (empty($data)) {
        return [];
    }

    return $data;
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                            FUNCTION urlValidate                            */
/* ────────────────────────────────────────────────────────────────────────── */
function urlValidate($url = Null, $protocol = Null) {
    global $cfg;
    if ($protocol == Null) {
        $protocol = $cfg["default_protocol"];
    }
    if (empty($url)) {
        return False;
    }
    # Check if $dest contains http:// or https:// at the start, if not prepend https://
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = $protocol . $url;
    }
    # Check if $url is a valid URL
    $url = filter_var($url, FILTER_SANITIZE_URL);
    if (filter_var($url, FILTER_VALIDATE_URL) === False) {
        return False;
    }
    return $url;
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                         FUNCTION urlExists                                 */
/* ────────────────────────────────────────────────────────────────────────── */
function urlExists($url = Null) {
    if ($url == Null) {
        die("urlExists() requires a URL.");
    }

    $result = query("SELECT * FROM urls WHERE `short` = ?", [$url]);
    $count  = count($result);

    if ($count > 0) {
        return True;
    }
    return False;
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                              FUNCTION getUrls                              */
/* ────────────────────────────────────────────────────────────────────────── */
function getUrls() {
    global $cfg;
    if ($cfg["urls_public"] == True && $_SESSION['id'] && $_SESSION['acl'] < 1) {
        return query("SELECT * FROM urls WHERE `user` = ? ORDER BY `id` DESC", [$_SESSION['id']]);
    }
    return query("SELECT * FROM urls ORDER BY `id` DESC");
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                              FUNCTION getUrl                              */
/* ────────────────────────────────────────────────────────────────────────── */
function getUrl($id, $selector = "id", $return = NULL) {
    $result = query("SELECT * FROM `urls` WHERE BINARY `$selector` = ?", [$id]);

    if (count($result) == 0) {
        return False;
    }

    if (empty($return)) {
        return $result[0];
    }

    if (!isset($result[0][$return])) {
        return False;
    }

    return $result[0][$return];
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                              FUNCTION getUser                              */
/* ────────────────────────────────────────────────────────────────────────── */
function getUser($id = 0, $column = Null) {
    $result = query("SELECT * FROM users WHERE `id` = ?", [$id]);

    if (count($result) == 0) {
        return False;
    }

    if ($column == Null) {
        return $result[0];
    }

    if (!isset($result[0][$column])) {
        return False;
    }

    return $result[0][$column];
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                              FUNCTION setUser                              */
/* ────────────────────────────────────────────────────────────────────────── */
function setUser($id, $column, $value) {
    $update = "UPDATE users SET `$column` = ? WHERE `id` = ?";
    query($update, [$value, $id]);
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                              FUNCTION createURL                            */
/* ────────────────────────────────────────────────────────────────────────── */
function createURL($short, $dest, $type, $user) {
    $insert = "INSERT INTO urls (`short`, `dest`, `type`, `user`) VALUES (?, ?, ?, ?)";
    $insert = query($insert, [$short, $dest, $type, $user]);
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                             FUNCTION deleteURL                             */
/* ────────────────────────────────────────────────────────────────────────── */
function deleteURL($id) {
    $delete = "DELETE FROM urls WHERE `id` = ?";
    $delete = query($delete, [$id]);
}


/* ────────────────────────────────────────────────────────────────────────── */
/*                              FUNCTION redirect                             */
/* ────────────────────────────────────────────────────────────────────────── */
// function redirect($url) {
//     echo "<script type='text/javascript'>window.location.href = '$url';</script>";
//     exit();
// }

/* ────────────────────────────────────────────────────────────────────────── */
/*                            FUNCTION deleteShort                            */
/* ────────────────────────────────────────────────────────────────────────── */
function deleteShort($short) {
    query("DELETE FROM urls WHERE `short` = ?", [$short]);
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                                FUNCTION auth                               */
/* ────────────────────────────────────────────────────────────────────────── */
function auth($user = Null, $pass = Null) {
    if (empty($user) || empty($pass)) {
        return False;
    }

    $result = query("SELECT * FROM users WHERE LOWER(`username`) = LOWER(?)", [$user]);
    $count  = count($result);

    if ($count < 1) {
        return False;
    }

    $salt = $result[0]['salt'];
    $hash = $result[0]['password'];
    if (empty($salt) || empty($hash)) {
        die("Invalid user data for user $user.");
    }

    $inputHash = hash("sha512", $salt.$pass);
    if ($inputHash !== $hash && $pass !== $hash) {
        return False;
    }
    $_SESSION['id']       = $result[0]['id'];
    $_SESSION['acl']      = $result[0]['acl'];
    $_SESSION['username'] = $result[0]['username'];
    return True;
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                              FUNCTION urlInput                             */
/* ────────────────────────────────────────────────────────────────────────── */
function urlInput($name = "url", $placeholder = "Destination URL (ex. example.com)") {
    global $cfg;
    return '
    <div class="input-group m-2">
        <div class="input-group-text p-0">
            <select class="form-select border-0 url-protocol urlInputProtocol" name="protocol">
                <option value="http://" '.($cfg["default_protocol"] == "http://" ? "selected" : "").'>http://</option>
                <option value="https://" '.($cfg["default_protocol"] == "https://" ? "selected" : "").'>https://</option>
            </select>
        </div>
        <input class="form-control urlValidate urlNameInput" type="text" name="'.$name.'"
            placeholder="'.$placeholder.'"
            >
    </div>
';
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                             FUNCTION aclToText                             */
/* ────────────────────────────────────────────────────────────────────────── */
function aclToText($acl = 0) {
    $acl = (int) $acl;
    if ($acl == 0) {
        return "User";
    }
    if ($acl == 1) {
        return "Admin";
    }
    if ($acl == 2) {
        return "Super Admin";
    }
    if ($acl == 3) {
        return "Owner";
    }
    return "Unknown";
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                              FUNCTION writeLog                             */
/* ────────────────────────────────────────────────────────────────────────── */
function writeLog($event = Null) {
    global $cfg;
    if (!$cfg["logging"] || $event == Null) {
        return;
    }
    $forwarded_for = ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? Null);
    $remote_addr   = ($_SERVER['REMOTE_ADDR'] ?? Null);
    $ip            = ($forwarded_for ?? $remote_addr);
    $user          = ($_SESSION['username'] ?? "Guest");
    $query         = "INSERT INTO logs (`event`, `user`, `ip`) VALUES (?, ?, ?)";
    query($query, [$event, $user, $ip]);
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                             FUNCTION urlInputs                             */
/* ────────────────────────────────────────────────────────────────────────── */
function urlForm($action = "create", $values = []) {
    global $urlForm;
    return $urlForm($action, $values);
    // global $cfg;
    // global $tooltip;
    // $shortTypeVal = (isset($values["short_type"]) ? $values["short_type"] : Null);
    // $destTypeVal  = (isset($values["dest_type"]) ? $values["dest_type"] : Null);
    // $shortVal     = (isset($values["short"]) ? $values["short"] : Null);
    // $nameVal      = (isset($values["name"]) ? $values["name"] : Null);
    // $destVal      = (isset($values["dest"]) ? $values["dest"] : Null);
    // $destTypeOpts = "";

    // $shortTypeOpts = "";
    // foreach ($cfg["short_types"] as $type) {
    //     $selected = ($type["value"] == $shortTypeVal ? "selected" : "");
    //     $shortTypeOpts .= "<option value='".$type["value"]."' $selected>".$type["name"]."</option>";
    // }

    // foreach ($cfg["dest_types"] as $type) {
    //     $selected = ($type["value"] == $destTypeVal ? "selected" : "");
    //     $destTypeOpts .= "<option value='".$type["value"]."' $selected>".$type["name"]."</option>";
    // }

    // $submitBtn = '<input class="btn btn-success" name="action" type="submit" value="Submit">';
    // if ($action == "create") {
    //     $submitBtn = '<input class="btn btn-success" name="action" type="submit" value="Create">';
    // } 
    // if ($action == "edit") {
    //     $submitBtn = '
    //         <a class="btn btn-primary" target="_blank" href="'.$shortVal.'">Open</a>
    //         <input class="btn btn-success" name="action" type="submit" value="Update">
    //     ';
    // }
    // $urlForm  = '
    //     <form class="dynamic-form" id="urlForm" action="index.php" method="POST" data-action="'.$action.'">
    //     <input class="urlActionInput" type="hidden" name="action" value="'.$action.'">
    //     <table class="table table-default">

    //         <tr class="urlInputRow" data-input="name">
    //             <td>
    //                 Name
    //             </td>
    //             <td>
    //                 '.$tooltip["name"].'
    //             </td>
    //             <td>
    //                 <div class="input-group m-1">
    //                     <input class="form-control urlNameInput common" type="text" name="name" placeholder="Name" value="'.$nameVal.'">
    //                 </div>
    //                 <p class="form-text">The name of this short url. This is just for identification purposes.</p>
    //             </td>
    //         </tr>

    //         <tr class="urlInputRow" data-input="short_type">
    //             <td>
    //                 <span class="text-danger mx-1">*</span>
    //                 Short type
    //             </td>
    //             <td>
    //                     '.$tooltip["short_type"].'
    //             </td>
    //             <td>
    //                 <div class="input-group m-1">
    //                     <select class="form-select urlTypeInput" name="short_type" id="shortTypeOpts">
    //                         '.$shortTypeOpts.'
    //                     </select>
    //                 </div>
    //                 <p class="form-text">The type of short URL this will be.</p>
    //             </td>
    //         </tr>

    //         <tr class="urlInputRow" data-input="short">
    //             <td>
    //                 Short URL
    //             </td>
    //             <td>
    //                     '.$tooltip["short"].'
    //             </td>
    //             <td>
    //                 <div class="input-group m-1">
    //                     <span class="input-group-text">
    //                         '.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/
    //                     </span>
    //                     <!--<input type="hidden" id="shortgen" name="shortgen" value="'.genStr($cfg["short_default"]).'">-->
    //                     <input class="form-control urlShortInput common" type="text" name="short" placeholder="Short URL" pattern="[A-Za-z0-9]*" title="Only alphanumeric characters are allowed" value="'.$shortVal.'">
    //                     <p class="form-text">The "short" part of the url. This must be unique and will function as the ID.</p>
    //                 </div>
    //             </td>
    //         </tr>

    //         <tr class="urlInputRow" data-input="dest_type">
    //             <td>
    //                 <span class="text-danger mx-1">*</span>
    //                 Destination type
    //             </td>
    //             <td>
    //                     '.$tooltip["dest_type"].'
    //             </td>
    //             <td>
    //                 <div class="input-group m-1">
    //                     <select class="form-select urlTypeInput" name="type" id="destTypeOpts">
    //                         '.$destTypeOpts.'
    //                     </select>
    //                 </div>
    //                 <p class="form-text">The destination type of this shortened URL.</p>
    //             </td>
    //         </tr>

    //         <!--
    //         /* ────────────────────────────────────────────────────────────────────────── */
    //         /*                                  REDIRECT                                  */
    //         /* ────────────────────────────────────────────────────────────────────────── */
    //         -->
    //         <tbody class="urlInputRow" data-input="redirect">
    //             <tr>
    //                 <td>
    //                     <span class="inline">  
    //                         <span class="text-danger mx-1">*</span>
    //                         Redirect URL
    //                     </span>
    //                 </td>
    //                 <td>
    //                     '.$tooltip["redirect"].'
    //                 </td>
    //                 <td>
    //                     <div>
    //                         '.urlInput("redirect_dest").'
    //                     </div>
    //                 </td>
    //             </tr>
    //         </tbody>


    //         <!--
    //         /* ────────────────────────────────────────────────────────────────────────── */
    //         /*                                   ALIAS                                    */
    //         /* ────────────────────────────────────────────────────────────────────────── */
    //         -->
    //         <tr class="urlInputRow" data-input="alias" style="display:none;">
    //             <td>
    //                 <span>
    //                     <span class="text-danger mx-1">*</span>
    //                     Alias URL
    //                 </span>
    //             </td>
    //             <td>
    //                 '.$tooltip["alias"].'
    //             </td>
    //             <td>
    //                 '.urlInput("alias_dest").'
    //             </td>
    //         </tr>

    //         <!--
    //         /* ────────────────────────────────────────────────────────────────────────── */
    //         /*                                  CUSTOM                                   */
    //         /* ────────────────────────────────────────────────────────────────────────── */
    //         -->
    //         <tr class="urlInputRow" data-input="custom" style="display:none;">
    //             <td>
    //                 <span class="text-danger mx-1">*</span>
    //                 Custom Script
    //             </td>
    //             <td>
    //                 '.$tooltip["custom"].'
    //             </td>
    //             <td>
    //                 <div class="urlCustomInput codeBox codeInput" name="custom_dest" placeholder="Custom Script"></div>
    //             </td>
    //         </tr>

    //         <!--
    //         /* ────────────────────────────────────────────────────────────────────────── */
    //         /*                                   OPTIONS                                  */
    //         /* ────────────────────────────────────────────────────────────────────────── */
    //         -->
    //         <tr class="urlOptions" data-type="redirect">
    //             <td>
    //                 Redirect delay (ms)
    //             </td>
    //             <td>
    //                 '.$tooltip["delay"].'
    //             </td>
    //             <td>
    //                 <input class="form-control m-1 urlDelayInput" type="number" name="options[delay]" value="'.$cfg["default_delay"].'">
    //             </td>
    //         </tr>
    //         <tr>
    //             <td></td>
    //             <td colspan="100%">
    //                 '.$submitBtn.'
    //             </td>
    //         </tr>
    //     </table>
    // </form>';
    // return $urlForm;
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                              FUNCTION listUrls                             */
/* ────────────────────────────────────────────────────────────────────────── */
function listUrls(?array $urls = []) {
    global $cfg;
    if (empty($urls)) {
        $urls = getUrls();
    }

    $favorites = [];
    if (!empty($_SESSION['id'])) {
        $favorites = getUser($_SESSION['id'], 'bookmarks');
        $favorites = json_decode($favorites, True);
    }

    $urlsTable = '
        <div id="table-toolbar">
            <div class="btn-group">
                <a href="?do=home" class="btn btn-success"><?= icon("plus-circle") ?> New URL</a>
                <button class="deleteSelectedBtn btn btn-danger" disabled><?= icon("trash") ?> Delete Selected</button>
            </div>
        </div>
        <table id="urlTable" class="table table-default" 
            data-toggle="table"
            data-search="true"
            data-pagination="true"
            data-page-size="25"
            data-page-list="[10, 25, 50, 100, 200, All]"
            data-show-columns="true"
            data-show-columns-toggle-all="true"
            data-show-extended-pagination="true"
            data-show-pagination-switch="true"
            data-show-search-clear-button="true"
            data-show-toggle="true"
            >
            <thead>
                <tr class="table table-primary">
                    <th data-field="state" data-checkbox="true"></th>
                    <th data-field="id" data-sortable="true" data-visible="false">ID</th>
                    <th data-field="type" data-sortable="true">Type</th>
                    <th data-field="name" data-sortable="true">Name</th>
                    <th data-field="shorturl" data-sortable="true">Short URL</th>
                    <th data-field="desturl" data-sortable="true">Destination URL</th>
                    <th data-field="user" data-sortable="true">User</th>
                    <th data-field="options" data-sortable="false" data-visible="false">Options</th>
                    <th>Actions</th>
                </tr>
            </thead>

        <tbody>
        ';
        if (empty($urls)) {
            $urlsTable .= '
                <tr>
                    <td colspan="8" class="text-center">No URLs found.</td>
                </tr>
            ';
        }
        foreach ($urls as $url) {
            if (!is_array($url)) {
                die("listUrls() requires an array of URLs. Input: " . json_encode($url));
            }
            $username = "Unknown";
            if (!empty($url["user"])) {
                $username = getUser($url["user"], "username");
            }

            $urlId       = $url["id"];
            $urlShort    = $url["short"];
            $urlName     = (!empty($url["name"]) ? $url["name"] : $urlShort);
            $urlDest     = $url["dest"];
            $urlType     = $url["type"];
            $urlDestLink = "<a href='$urlDest' target='_blank'>$urlDest</a>";
            $urlOptions  = (!empty($url["options"]) && json_validate($url["options"]) ? json_decode($url["options"]) : []);

            if ($urlType == "custom") {
                $urlDestLink = "Custom";
            }

            $favoriteIcon = icon("star", 1.5, "grey");
            if (is_array($favorites) && in_array($urlId, $favorites)) {
                $favoriteIcon = " ".icon("star-fill", 1.5, "gold");
            }

            $optionsText = "";
            foreach ($urlOptions as $key => $value) {
                $optionsText .= "$key = $value<br>";
            }

            $urlsTable .= '
                <tr
                    data-id="'.$urlId.'"
                    data-type="'.$urlType.'" 
                    data-name="'.$urlName.'"
                    data-shorturl="'.$urlShort.'"
                    data-desturl="'.$urlDest.'"
                    data-user="'.$username.'"
                >
                    <td data-value="'.$urlId.'"></td>
                    <td>
                        '.$urlId.'
                    </td>
                    <td>
                        '.$urlId.'
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="url-action m-3" 
                            data-action="edit"
                            data-bs-toggle="modal"
                            data-bs-target="#editUrlModal">
                                '.$urlName.'
                        </a>
                    </td>
                    <td>
                        <a href="'.$urlShort.'" target="_blank">'.$urlShort.'</a>
                    </td>
                    <td>
                        '.$urlDestLink.'
                    </td>
                    <td>
                        '.$username.'
                    </td>
                    <td>
                        '.$optionsText.'
                    </td>
                    <td>

                        <a href="javascript:void(0);" class="url-action m-3" 
                            data-action="edit"
                            data-bs-toggle="modal"
                            data-bs-target="#editUrlModal">'.icon("pencil", 1.5).'</a>

                        <a href="javascript:void(0)" class="url-action m-3" 
                            data-action="bookmark">'.$favoriteIcon.'</a>

                        <a href="javascript:void(0);" class="url-action m-3 link-danger" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteUrlModal" 
                            data-action="delete">'.icon("trash", 1.5).'</a>

                    </td>
                </tr>
            ';
        }
    $urlsTable .= '
        </tbody>
        </table>

        <div class="btn-group">
            <a href="?do=home" class="btn btn-success"><?= icon("plus-circle") ?> New URL</a>
            <button class="deleteSelectedBtn btn btn-danger" disabled><?= icon("trash") ?> Delete Selected</button>
        </div>

        <div class="modal modal-lg fade" id="editUrlModal" tabindex="-1" aria-labelledby="editUrlModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUrlModalLabel">Edit URL</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        '.urlForm('edit', $url).'
                    </div>
                </div>
            </div>
        </div>
    ';

    // <!-- Edit URL Modal
    // <div class="modal fade" id="editUrlModal" tabindex="-1" aria-labelledby="editUrlModalLabel" aria-hidden="true">
    //     <div class="modal-dialog">
    //         <div class="modal-content">
    //             <div class="modal-header">
    //                 <h5 class="modal-title" id="editUrlModalLabel">Edit URL</h5>
    //                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    //             </div>
    //             <div class="modal-body">
    //                 <form id="editUrlForm" class="dynamic-form" action="includes/api.php" method="POST">
    //                     <div class="mb-3">
    //                         <label for="editUrlType" class="form-label">Type</label>
    //                         <select class="form-select" id="editUrlType" name="type" required>
    //                             ';
    //                                 foreach ($cfg["url_types"] as $type) {
    //                                     $urlsTable .= '<option value="'.$type["value"].'">'.$type["name"].'</option>';
    //                                 }
    //     $urlsTable .= '
    //                         </select>
    //                     </div>
    //                     <div class="mb-3">
    //                         <label for="editNameUrl" class="form-label">Short URL</label>
    //                         <input type="text" class="form-control" id="editNameUrl" name="short" required>
    //                     </div>
    //                     <div class="mb-3">
    //                         <label for="editShortUrl" class="form-label">Short URL</label>
    //                         <input type="text" class="form-control" id="editShortUrl" name="short" required>
    //                     </div>
    //                     <div class="mb-3 protocolURLInput">
    //                         <label for="editDestProtocol" class="form-label">Destination Protocol</label>
    //                         <select class="form-select url-protocol" id="editDestProtocol" name="protocol" required>
    //                             <option value="http://">http://</option>
    //                             <option value="https://" selected>https://</option>
    //                         </select>
    //                     </div>
    //                     <div class="mb-3 destURLInput">
    //                         <label for="editDestUrl" class="form-label">Destination URL</label>
    //                         <input type="text" class="form-control urlValidate" id="editDestUrl" name="dest" required>
    //                     </div>
    //                     <div class="mb-3 customURLInput">
    //                         <label for="editCustomUrl" class="form-label">Custom URL</label>
    //                         <textarea class="form-control codeBox codeInput" id="editCustomUrl" name="custom"></textarea>
    //                     </div>
    //                     <input type="hidden" id="editUrlId" name="id">
    //                     <input type="hidden" name="action" value="edit">
    //                 </form>
    //             </div>
    //             <div class="modal-footer">
    //                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    //                 <button type="button" class="btn btn-primary" id="saveEditUrl">Save changes</button>
    //             </div>
    //         </div>
    //     </div>
    // </div>

    $urlsTable .= '
    <!-- Delete URL Modal -->
    <div class="modal fade" id="deleteUrlModal" tabindex="-1" aria-labelledby="deleteUrlModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUrlModalLabel">Delete URL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-danger">
                    <div id="deleteUrlResponse"></div>
                    <form id="deleteUrlForm">
                        <input type="hidden" id="deleteUrlId" name="id">
                        Are you sure you want to delete short URL <b id="deleteUrlShort"></b>?
                        <br>
                        This action cannot be undone.
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteUrl">Delete</button>
                </div>
            </div>
        </div>
    </div>
    ';
    return $urlsTable;
}

?>