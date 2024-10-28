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
        $icon = icon("info-circle");
    } elseif ($type == "success" && $icon == Null) {
        $icon = icon("check-circle");
    } elseif ($type == "warning" && $icon == Null) {
        $icon = icon("exclamation-triangle");
    } elseif ($type == "danger" && $icon == Null) {
        $icon = icon("exclamation-circle");
    }

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
            <span>
                $icon
                $msg
            </span>
            $dismissBtn
        </div>
    </div>
    ";
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                                FUNCTION icon                               */
/* ────────────────────────────────────────────────────────────────────────── */
function icon($icon, $size = 1, $color = Null) {
    $style = "style='";
    $style .= ($color == Null ? "" : "color:$color;");
    $style .= "font-size:".$size."rem;";
    $style .= "'";
    return "<span class='bi bi-$icon' $style></span>";
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
        <li class='nav-item'>
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
function jsRedirect($url = "index.php", $time = 1000) {
    $script = "
    <script>
        setTimeout(function(){
            window.location.href = '$url';
        }, $time);
    </script>";
    if (headers_sent()) {
        echo $script;
    } else {
        return $script;
    }
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
    return query("SELECT * FROM urls");
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                              FUNCTION getUrl                              */
/* ────────────────────────────────────────────────────────────────────────── */
function getUrl($short, $return = NULL) {
    $result = query("SELECT * FROM `urls` WHERE BINARY `short` = ?", [$short]);

    if (count($result) == 0) {
        return False;
    }

    if (empty($return)) {
        return $result[0];
    }

    if (!isset($result[0][$return])) {
        die("getUrl: Invalid return value $return for $short.");
    }

    return $result[0][$return];
}

/* ────────────────────────────────────────────────────────────────────────── */
/*                              FUNCTION getUser                              */
/* ────────────────────────────────────────────────────────────────────────── */
function getUser($id = 0) {
    $result = query("SELECT * FROM users WHERE `id` = ?", [$id]);

    if (count($result) == 0) {
        return False;
    }

    return $result[0];
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
function redirect($url) {
    echo "<script type='text/javascript'>window.location.href = '$url';</script>";
    exit();
}

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

    $result = query("SELECT * FROM users WHERE `username` = ?", [$user]);
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
    $_SESSION['username'] = $user;
    return True;
}

?>