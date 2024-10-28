<script>
    
    var startTime = performance.now();
    console.log("js.php started running at " + startTime + " ms.");

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                               DOCUMENT READY                               */
    /* ────────────────────────────────────────────────────────────────────────── */
    $(document).ready(function() {

        var endTime   = performance.now();
        var timeTaken = endTime - startTime;
        console.log("Time taken for document to ready up: " + timeTaken + " ms.");

        // Show/hide password when the button is clicked
        $(".password").each(function() {
            // Append a show/hide button to the password field
            var type = $(this).attr("type");
            var icon = (type == "password") ? "<?= icon("eye") ?>" : "<?= icon("eye-slash") ?>";
            var btn  = "<button type='button' class='btn btn-secondary password-toggle'>" + icon + "</button>";
            $(this).wrap("<div class='input-group'></div>");
            $(this).after(btn);
        });

        // Show/hide password when the button is clicked
        $(".password-toggle").on("click", function() {
            var type = $(this).prev().attr("type");
            if (type == "password") {
                $(this).prev().attr("type", "text");
            } else {
                $(this).prev().attr("type", "password");
            }
            var icon = (type == "password") ? "<?= icon("eye") ?>" : "<?= icon("eye-slash") ?>";
            $(this).html(icon);
        });

        // Fade out the alert message after 2 seconds
        $(".alert").not(".alert-persistent").fadeTo(2000, 500).slideUp(500, function(){
            $(this).slideUp(500);
        });

        // Submit form to `formhandler.php` when the button is clicked
        $(".dynamic-form").on("submit", function(e) {
            e.preventDefault();
            console.log("Dynamic form submitted.");
            var method   = $(this).attr("method").toUpperCase();
            var url      = "includes/api.php";
            var action   = $(this).data("action");
            var formdata = $(this).serialize();
            formdata += "&action="+action;
            
            $.ajax({
                type   : method,
                url    : url,
                data   : formdata,
                success: function(data) {
                    console.groupCollapsed("Form submitted successfully.");
                    console.log("Method: " + method);
                    console.log("URL: " + url);
                    console.log("Action: " + action);
                    console.log("Data: " + data);
                    console.groupEnd();
                    $(".dynamic-form-response").html(data);
                }
            });
        });

        /* ────────────────────────────────────────────────────────────────────────── */
        /*                              NOTE: home (add)                              */
        /* ────────────────────────────────────────────────────────────────────────── */
        $(".newUrlInput[name=type]").on("change", function() {
            $(".urlInputRow").hide();
            $(".urlInputRow[data-input=short]").show();
            $(".newUrlInput").attr("disabled", true);

            var type = $(this).val();
            if (type == "redirect") {
                $(".urlInputRow[data-input=redirect]").show();
            } else if (type == "custom") {
                $(".urlInputRow[data-input=custom]").show();
            } else if (type == "alias") {
                $(".urlInputRow[data-input=alias]").show();
            }

            $(".newUrlInput").each(function() {
                if (!$(this).is(":visible")) {
                    $(this).prop("disabled", true);
                } else {
                    $(this).prop("disabled", false);
                }
            });

        });

        /* ────────────────────────────────────────────────────────────────────────── */
        /*                                 NOTE: urls                                 */
        /* ────────────────────────────────────────────────────────────────────────── */
        // NOTE: .url-action
        $(".url-action").on("click", function() {
            var action   = $(this).data("action");
            var tr       = $(this).closest("tr");
            var id       = tr.data("id");
            var type     = tr.data("type");
            var short    = tr.data("shorturl");
            var protocol = short.split("://")[0] + "://";
            var dest     = tr.data("desturl");
            var user     = tr.data("user");

            if (action == "edit") {
                var editUrlForm  = $("#editUrlForm");
                var editUrlType  = editUrlForm.find("#editUrlType");
                var editShortUrl = editUrlForm.find("#editShortUrl");
                var editProtocol = editUrlForm.find("#editDestProtocol");
                var editDestUrl  = editUrlForm.find("#editDestUrl");
                var editUrlId    = editUrlForm.find("#editUrlId");

                editUrlType.val(type);
                editShortUrl.val(short);
                editDestUrl.val(dest);
                editUrlId.val(id);

                console.log("Edit action clicked.");
            }
            if (action == "delete") {
                var deleteUrlForm = $("#deleteUrlForm");
                console.log("Delete action clicked.");
                $("#deleteUrlShort").text(short);
                $("#confirmDeleteUrl").data("id", id);
                $("#confirmDeleteUrl").show();
                $("#deleteUrlForm").show();
            }
        });

        // NOTE: confirmDeleteUrl
        $("#confirmDeleteUrl").on("click", function() {
            var id       = $(this).data("id");
            var formdata = "action=delete&id="+id;
            var url      = "includes/api.php";

            $.ajax({
                type   : "POST",
                url    : url,
                data   : formdata,
                success: function(data) {
                    console.groupCollapsed("URL deleted successfully.");
                    console.log("ID: " + id);
                    console.log("Data: " + data);
                    console.groupEnd();
                    $("#deleteUrlResponse").html(data);
                    $("tr[data-id='" + id + "']").remove();
                    $("#confirmDeleteUrl").hide();
                    $("#deleteUrlForm").hide();
                }
            });
        });

        // NOTE: .urlValidate
        $(".urlValidate").on("input", function() {
            var url = $(this).val();
            var protocol = "";

            if (url.startsWith("https://")) {
                protocol = "https://";
            } else if (url.startsWith("http://")) {
                protocol = "http://";
            } else {
                return;
            }

            url = url.replace(/^(http:\/\/|https:\/\/)/, '');
            $(this).val(url);
            $(this).closest(".input-group").find(".url-protocol").val(protocol);
        });


        // NOTE: urlTable
        var urlsChecked = 0;
        var urls        = [];
        $("#urlTable").on("check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table", function(e, row) {
            
            var url_id = row.id

            console.log("Event type: " + e.type);
            console.log("Row: " + row);
            console.log("ID: " + url_id);

            if (e.type == "check") {
                urlsChecked++;
                urls.push(url_id);
            }
            if (e.type == "uncheck") {
                urlsChecked--;
                urls = urls.filter(function(filterid) {
                    return filterid !== url_id;
                });
            }
            if (e.type == "check-all") {
                urlsChecked = row.length;
                urls = row.map(function(r) {
                    return r.id;
                });
            }
            if (e.type == "uncheck-all") {
                urlsChecked = 0;
                urls = [];
            }

            // Buttons
            var deleteSelectedBtn = $("#deleteSelectedBtn");
            if (urlsChecked > 0) {
                deleteSelectedBtn.removeAttr("disabled");
                deleteSelectedBtn.attr("data-urls", urls);
            } else {
                deleteSelectedBtn.attr("disabled", true);
                deleteSelectedBtn.attr("data-urls", "");
            }
        });



    });
    /* ────────────────────────────────────────────────────────────────────────── */
    /*                            End of document.ready                           */
    /* ────────────────────────────────────────────────────────────────────────── */

    $(window).on("load", function() {
        var endTime   = performance.now();
        var timeTaken = endTime - startTime;
        console.log("Time taken for window to load up: " + timeTaken + " ms.");
    });

</script>