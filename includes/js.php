<script>
    
    var startTime = performance.now();
    console.log("js.php started running at " + startTime + " ms.");

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                            playNotificationSound                           */
    /* ────────────────────────────────────────────────────────────────────────── */
    function playNotificationSound() {
        var audio = new Audio('assets/notification.mp3');
            audio.volume = 0.5;
            audio.play();
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                    toast                                   */
    /* ────────────────────────────────────────────────────────────────────────── */
    function toast(message = "Toast", type = "primary", title = null, icon = "exclamation-circle") {
        if (title == null) {
            title = type.charAt(0).toUpperCase() + type.slice(1);
        }
        if (icon != null) {
            title = `
            <span style='display: flex; align-items: center;'>
                <span class='bi bi-${icon}' style='font-size:1.5rem; margin-right: 0.5rem;'></span> ${title}
            </span>
            `;
        }
        var container = $(".toast-container");
        var toast = $(`
            <div class="toast border-${type} w-100" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header text-bg-${type}">
                    <strong class="me-auto">${title}</strong>
                    <small class="text-${type}">just now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body text-${type}">
                    ${message}
                </div>
            </div>
        `);
        
        container.append(toast);
        $(".toast").toast("show").on("hidden.bs.toast", function() {
            $(this).remove();
        });

        <?php if ($cfg["notification_sound"] !== False) { ?>
            playNotificationSound();
        <?php } ?>
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                     api                                    */
    /* ────────────────────────────────────────────────────────────────────────── */
    function api(method, action, data, callback = null) {
        var url      = "includes/api.php";
        var formdata = "action=" + action + "&" + data;
        $.ajax({
            type   : method,
            url    : url,
            data   : formdata,
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                } catch (e) {
                    console.error("Invalid JSON response:", response);
                    toast("Invalid JSON response", "danger", "Error");
                    return false;
                }
                var status      = data["status"];
                var message     = data["message"];
                var redirect    = data["redirect"];
                var type        = "info";
                if (status == "OK") {
                    type = "success";
                } else if (status == "ERROR") {
                    type = "danger";
                } else if (status == "WARNING" || status == "WARN") {
                    type = "warning";
                }
                console.groupCollapsed("API request successful.");
                console.log("Action: " + action);
                console.log("Data: " + JSON.stringify(data));
                console.groupEnd();

                if (callback != null) {
                    callback(data);
                    return true;
                }
                toast(message, type, status.toUpperCase());

                if (data["redirect"] != null) {
                    window.location.href = data["redirect"];
                }
                return true;
            },
            error: function() {
                return false;
            }
        });
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                           // NOTE: DOCUMENT READY                          */
    /* ────────────────────────────────────────────────────────────────────────── */
    $(document).ready(function() {

        // NOTE: Tooltips (Bootstrap)
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

        var endTime   = performance.now();
        var timeTaken = endTime - startTime;
        console.log("Time taken for document to ready up: " + timeTaken + " ms.");

        // NOTE: .password
        // Show/hide password when the button is clicked
        $(".password").each(function() {
            // Append a show/hide button to the password field
            var type = $(this).attr("type");
            var icon = (type == "password") ? "<?= icon("eye") ?>" : "<?= icon("eye-slash") ?>";
            var btn  = "<button type='button' class='btn btn-secondary password-toggle'>" + icon + "</button>";
            $(this).wrap("<div class='input-group'></div>");
            $(this).after(btn);
        });

        // NOTE: .password-toggle
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

        // NOTE: .alert
        // Fade out the alert message after 2 seconds
        $(".alert").not(".alert-persistent").fadeTo(2000, 500).slideUp(500, function(){
            $(this).slideUp(500);
        });

        // NOTE: .dynamic-form
        // Submit form to `formhandler.php` when the button is clicked
        $(".dynamic-form").on("submit", function(e) {
            e.preventDefault();
            console.log("Dynamic form submitted.");
            var method   = $(this).attr("method").toUpperCase();
            var url      = "includes/api.php";
            var action   = $(this).data("action");
            var formdata = $(this).serialize();
            formdata += "&action="+action;
            
            api(method, action, formdata);

            // $.ajax({
            //     type   : method,
            //     url    : url,
            //     data   : formdata,
            //     success: function(data) {
            //         console.groupCollapsed("Form submitted successfully.");
            //         console.log("Method: " + method);
            //         console.log("URL: " + url);
            //         console.log("Action: " + action);
            //         console.log("Data: " + data);
            //         console.groupEnd();
            //         $(".dynamic-form-response").html(data);
            //     }
            // });
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
        
        var urlsChecked = 0;
        var urls        = [];

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
                $("#confirmDeleteUrl").prop("disabled", false);
                $("#deleteUrlForm").show();
            }
        });

        // NOTE: confirmDeleteUrl
        $("#confirmDeleteUrl").on("click", function() {
            api("POST", "delete", "id="+$(this).data("id"));
            $(this).attr("disabled", true);
            $("#deleteUrlModal").modal("hide");
            $("tr[data-id='" + $(this).data("id") + "']").remove();
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


        // NOTE: #urlTable
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
                urls        = [];
                urlsChecked = 0;
            }
        });

        // NOTE: #deleteSelectedBtn
        $("#deleteSelectedBtn").on("click", function() {
            // variable urls fetched from global scope
            var ids      = urls.join(",");
            console.log("Deleting urls: " + urls);

            if (urls.length == 0) {
                console.log("No URLs selected.");
                return;
            }

            api("POST", "delete", "id="+ids);

            urls.forEach(function(id) {
                console.log("Removing " + id + " from table.");
                $("tr[data-id='" + id + "']").remove();
            });

            // Buttons
            var deleteSelectedBtn = $("#deleteSelectedBtn");
                deleteSelectedBtn.attr("disabled", true);
                deleteSelectedBtn.attr("data-urls", "");
            urls        = [];
            urlsChecked = 0;

            $("#confirmDeleteUrl").hide();
            $("#deleteUrlForm").hide();
        });

        // NOTE: .testAPI
        $(".profile-api-form").on("submit", function() {
            var method = $(this).find("#api_method").val();
            var action = $(this).find("#api_action").val();
            var data   = $(this).serialize();
            api(method, action, data);
        });
        $(".profile-api-card").on("change", "#api_action", function() {
            var action = $(this).val();
            $(".testAPIInputs").hide();
            $(".testAPIInputs[data-action='"+action+"']").show();
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