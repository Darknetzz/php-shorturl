<?php
    $start_time = microtime(True);
?>

<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap Table -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/bootstrap-table.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/bootstrap-table.min.js"></script>

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

    // echo $debug_card;

    ?>

</body>

<?php require_once("includes/js.php") ?>

<?php
    $end_time        = microtime(True);
    $execution_time  = ($end_time - $start_time);
    echo "<!-- Page generated in $execution_time seconds. -->";
?>



</html>
