<div class="container-fluid">
    <?php

        if (!isset($_SESSION['id'])) {
            echo alert("You must be logged in to view this page.", "danger");
            echo jsRedirect("index.php");
            die();
        }

        $settings     = getUserSettings($_SESSION['id']);
        $contentWidth = (int) $settings["content_width"];

    ?>

    <div class="card m-2">
        <h3 class="card-header">Settings</h3>
        <div class="card-body">
            <form class="dynamic-form" method="POST" data-action="saveSettings">
                <div class="mb-3">
                    <label for="contentWidthInput" class="form-label">
                        Content width
                        <span id="contentWidthValue" class="text-muted"><?= $contentWidth ?>%</span>
                    </label>
                    <input
                        type="range"
                        class="form-range"
                        id="contentWidthInput"
                        name="content_width"
                        min="40"
                        max="100"
                        step="1"
                        value="<?= $contentWidth ?>"
                    >
                    <div class="form-text">Controls how wide page content is relative to the browser window.</div>
                </div>
                <button type="submit" class="btn btn-primary"><?= icon("floppy2") ?> Save</button>
            </form>
        </div>
    </div>
</div>

<script>
    (function() {
        var input = document.getElementById("contentWidthInput");
        var label = document.getElementById("contentWidthValue");
        if (!input || !label) {
            return;
        }
        input.addEventListener("input", function() {
            label.textContent = this.value + "%";
        });
    })();
</script>
