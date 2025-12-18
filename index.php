<?php
    $start_time = microtime(True);
?>

<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Bootstrap Table -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.26.0/dist/bootstrap-table.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.26.0/dist/bootstrap-table.min.js"></script>

    <!-- Highlight.js -->
    <!-- <link rel="stylesheet" href="assets/highlight/styles/dark.min.css">
    <script src="assets/highlight/highlight.min.js"></script>
    <script src="assets/highlight/languages/php.min.js"></script>
    <script src="assets/highlight/languages/javascript.min.js"></script>
    <script src="assets/highlight/languages/http.min.js"></script>
    <script src="assets/highlight/languages/plaintext.min.js"></script>
    <script src="assets/highlight/languages/css.min.js"></script>
    <script src="assets/highlight/languages/xml.min.js"></script> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.36.3/ace.min.js" integrity="sha512-faieT+YRcxd+aQZbK6m2iaKhYEKkDvwbn7n2WToge2+k6+YBlxbT/Wii5bVPUWpnXm186SPynzVfc+ME8a/a3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <script src="assets/jsutils/utils.js"></script>

</head>

<body data-bs-theme="dark">

    <?php

    require_once("_includes.php");

    echo "<title>".$cfg["title"]."</title>";

    $debug_card = '
        <!-- Debug REQUEST -->
        <div class="card m-3 border border-warning">
            <div class="card-header text-bg-warning d-flex justify-content-between">
                <h1>Debug</h1>
                <div>
                    <button class="btn btn-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDebug" aria-expanded="false" aria-controls="collapseDebug">
                        '.icon("eye").' Show/Hide
                    </button>
                </div>
            </div>
            <div class="card-body collapse" id="collapseDebug">
                <pre class="text-bg-dark p-2">'.json_encode($_REQUEST, JSON_PRETTY_PRINT).'</pre>
            </div>
        </div>
    ';



    ?>


<?php

if ($config["debug"]) {
    echo $debug_card;
}

?>

</body>


<?php
    // require_once("includes/js.php")
    $end_time          = microtime(True);
    $execution_time    = ($end_time - $start_time);         // Seconds
    $execution_time_ms = round($execution_time * 1000, 2);  // Convert to milliseconds
    echo "<!-- Page generated in $execution_time_ms ms. -->";
?>



</html>
