<script>
    
    var startTime = performance.now();
    console.log("js.php started running at " + startTime + " ms.");

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                                 customError                                */
    /* ────────────────────────────────────────────────────────────────────────── */
    function customError(message, styles = "") {
        console.error("%c" + message, styles);
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                               customLog                                    */
    /* ────────────────────────────────────────────────────────────────────────── */
    function customLog(message, styles = "") {
        console.log("%c" + message, styles);
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                           checkAudioOutputDevice                           */
    /* ────────────────────────────────────────────────────────────────────────── */
    function checkAudioOutputDevice() {
        var soundEnabled = <?= ($cfg["notification_sound"] !== False ? "true" : "false") ?>;
        if (!soundEnabled) {
            customLog("Not playing sound: Notification sound is disabled.");
            return false;
        }
        if (!navigator.mediaCapabilities.propertyIsEnumerable) {
            customError("No audio output device detected.");
            return false;
        }
        customLog("Playing sound: Notification sound is enabled.");
        return true;
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                            playNotificationSound                           */
    /* ────────────────────────────────────────────────────────────────────────── */
    function playSound(sound = "notification") {
        checkAudioOutputDevice();
        var audioFile = "assets/" + sound + ".mp3";
        var audio = new Audio(audioFile);
        audio.volume = 0.5;
        audio.play().then(() => {
            audio.onended = () => {
                customLog("Notification sound ended.");
                audio.remove();
            };
        }).catch(error => {
            customError("Error playing sound:", error);
        });
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
                    console.error(e);
                    console.error("Invalid JSON response:", response);
                    toast("Invalid JSON response", "danger", "Error");
                    return false;
                }
                var status      = data["status"];
                var message     = data["message"];
                var redirect    = data["redirect"];
                var type        = "info";
                if (status == "OK") {
                    playSound("notification");
                    type = "success";
                } else if (status == "ERROR") {
                    playSound("error");
                    type = "danger";
                } else if (status == "WARNING" || status == "WARN") {
                    playSound("warning");
                    type = "warning";
                }
                console.groupCollapsed("API request successful.");
                customLog("Action: " + action);
                customLog("Data: " + JSON.stringify(data));
                console.groupEnd();

                if (callback != null) {
                    callback = callback(data);
                    if (callback == false) {
                        return true;
                    }
                }
                toast(message, type, status.toUpperCase());

                if (data["redirect"] != null) {
                    window.location.href = data["redirect"];
                }
                return true;
            },
            error: function(response) {
                console.error("API request failed:", response);
                toast("API request failed", "danger", "Error");
                playSound("error");
                return false;
            }
        });
    }

    /* ────────────────────────────────────────────────────────────────────────── */
    /*                           // NOTE: DOCUMENT READY                          */
    /* ────────────────────────────────────────────────────────────────────────── */
    $(document).ready(function() {

        // NOTE: js-utils
        window.utils = new Utils();

        // NOTE: Tooltips (Bootstrap)
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));


        // NOTE: Ace Editor
        const codeInputs = $(".codeInput");
        codeInputs.prop("contenteditable", true);
        codeInputs.each(function() {
            var thisObj   = $(this)[0];
            var inputName = $(this).attr("name");
            customLog("Highlighting code input for " + inputName);

            // Create a hidden textarea
            var hiddenTextarea = $("<textarea></textarea>")
                .attr("name", inputName)
                .css("display", "none");

            // Insert the hidden textarea after the code input
            $(this).after(hiddenTextarea);

            // Ace Options
            aceOpts = {
                mode: "ace/mode/html",
                theme: "ace/theme/monokai",
                showPrintMargin: false,
                tabSize: 4,
                useSoftTabs: true,
                wrap: true,
                autoScrollEditorIntoView: true,
                maxLines: 0,
                minLines: 10,
            };

            // Initialize Ace Editor
            var editor = ace.edit(thisObj, aceOpts);
            editor.name = inputName;

            // Update hidden textarea value when Ace Editor content changes
            editor.getSession().on('change', function() {
                hiddenTextarea.val(editor.getValue());
            });

            // Initial update of hidden textarea
            hiddenTextarea.val(editor.getValue());
        });

        var endTime   = performance.now();
        var timeTaken = endTime - startTime;
        customLog("Time taken for document to ready up: " + timeTaken + " ms.");

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
        // Submit form to `api.php` when the button is clicked
        // $(document).on(".dynamic-form", "submit", function(e) {
        $(".dynamic-form").on("submit", function(e) {
            e.preventDefault();
            var form     = utils.getObject(".dynamic-form");
            var formdata = form.serialize();
            var method   = form.find("[name='method']").val() || form.attr("method") || form.data("method");
            var action   = form.find("[name='action']").val() || form.data("action");
            var output   = form.data("output") || null; // NOTE: Output element to display the response
            var url      = form.attr("action") || "includes/api.php";

            if (!method || !action) {
                customError("Form method or action not specified.");
                return;
            }

            console.groupCollapsed(`%c.dynamic-form submitted`, 'color: cyan;');
                customLog("Method: " + method);
                customLog("Action: " + action);
                customLog("Data: " + formdata);
            console.groupEnd();


            formdata += "&action=" + action;
            
            <?php if (!empty($cfg["form_disable_timeout"]) && $cfg["form_disable_timeout"] > 0) { ?>
                // Recursively disable all form elements
                formElements = form.find("[name]");
                // And re-enable them after the specified timeout
                formElements.prop("disabled", true);
                setTimeout(function() {
                    formElements.prop("disabled", false);
                }, <?= $cfg["form_disable_timeout"] ?>);
            <?php } ?>

            api(method, action, formdata, function(data) {
                if (output != null) {
                    $(output).html(JSON.stringify(data, null, 2));
                }
            });
        });

        /* ───────────────────────────────────────────────────────────────────── */
        /*                             shortTypeInput                            */
        /* ───────────────────────────────────────────────────────────────────── */
        $("#shortTypeInput").on("change", function() {
            inputsToShow = {
                "path"     : ".urlInputRow[data-input=short_path]",
                "subdomain": ".urlInputRow[data-input=short_domain]",
                "custom"   : ".urlInputRow[data-input=short_custom]",
            };
            for (input in inputsToShow) {
                utils.log("hiding input", input);
                utils.hideObject(inputsToShow[input]);
            }
            utils.showObject(inputsToShow[$(this).val()]);
            utils.showObject(".urlInputRow[data-input=dest_type]");
        });

        /* ───────────────────────────────────────────────────────────────────── */
        /*                               shortInput                              */
        /* ───────────────────────────────────────────────────────────────────── */
        $(".shortInput").on("keyup", function() {
            utils.hideObject(".shortInputPreview");
            var shortType  = $("#shortTypeInput").val();
            var shortVal   = $(this).val();
            utils.log("Short input changed:", shortVal);
            if (shortType.length == 0 || shortVal.length == 0) {
                utils.error("Short type or value is empty.");
                return;
            }
            var previewObj = $("<span class='shortInputPreview'></span>");
            if (shortType == "path") {
                var preview = "<?= $cfg["base_url"] ?>/" + shortVal;
            }
            if (shortType == "subdomain") {
                var preview = shortVal + ".<?= $cfg["base_domain"] ?>";
            }
            if (shortType == "custom") {
                var preview = shortVal;
            }
            previewHTML = previewObj.html();
            $(this).closest(".urlInputDescription").append(previewHTML);
        });

        /* ────────────────────────────────────────────────────────────────────────── */
        /*                              destTypeInput                                 */
        /* ────────────────────────────────────────────────────────────────────────── */
        $("#destTypeInput").on("change", function() {
            inputsToShow = {
                "redirect": ".urlInputRow[data-input=dest_redirect]",
                "custom"  : ".urlInputRow[data-input=dest_custom]",
                "alias"   : ".urlInputRow[data-input=dest_alias]",
            };
            for (var input in inputsToShow) {
                utils.hideObject(inputsToShow[input]);
            }
            utils.showObject(inputsToShow[$(this).val()]);
        });
            // utils.hideObject(".urlInputRow");
            // // utils.showObject(".urlInputRow[data-input=short]");
            // utils.hideObject(".urlOptions");
            // var type = $(this).val();
            // if (type == "redirect") {
            //     utils.showObject(".urlInputRow[data-input=redirect]");
            //     utils.showObject(".urlOptions[data-type=redirect]");
            // } else if (type == "custom") {
            //     utils.showObject(".urlInputRow[data-input=custom]");
            //     utils.showObject(".urlOptions[data-type=custom]");
            // } else if (type == "alias") {
            //     utils.showObject(".urlInputRow[data-input=alias]");
            //     utils.showObject(".urlOptions[data-type=alias]");
            // }
        // });

        /* ────────────────────────────────────────────────────────────────────────── */
        /*                                 NOTE: urls                                 */
        /* ────────────────────────────────────────────────────────────────────────── */
        
        var urlsChecked = 0;
        var urls        = [];

        // NOTE: .url-action
        // # WARNING: Buggy event listener
        $(document).on("click", ".url-action", function() {
            if (typeof utils === 'undefined') {
                console.error("window.utils is not defined.");
                return;
            }

            var actionObj = $(this);
            utils.log("URL action clicked.");
            utils.log("Action Object:", actionObj);

            if (actionObj.length == 0) {
                utils.error("Action object not found.");
                return;
            }

            // Log the element's attributes or properties
            utils.log("Action Object HTML:", actionObj.html());
            utils.log("Action Object Attributes:", actionObj[0].attributes);

            var action    = actionObj.attr("data-action");
            utils.log("Action type: " + action);
            var tr        = actionObj.closest("tr");
            var name      = tr.data("name");
            var id        = tr.data("id");
            var type      = tr.data("type");
            var short     = tr.data("shorturl");
            var dest      = tr.data("desturl");
            var protocol  = tr.data("protocol");
            var user      = tr.data("user");

            if (action == "edit") {
                var editUrlForm   = $("#urlForm");
                if (editUrlForm.length == 0) {
                    utils.error("Edit form not found.");
                    return;
                }
                return;
                var editUrlName   = editUrlForm.find(".urlNameInput");
                var editUrlType   = editUrlForm.find(".urlTypeInput");
                var editShortUrl  = editUrlForm.find(".urlShortInput");
                var editProtocol  = editUrlForm.find(".urlProtocolInput");
                var editDestUrl   = editUrlForm.find(".urlDestInput");
                var editUrlId     = editUrlForm.find(".urlIdInput");
                var editCustomUrl = editUrlForm.find(".urlCustomInput");

                utils.hideObject(editDestUrl);
                utils.hideObject(editCustomUrl);
                utils.hideObject(editProtocol);
                if (type == "custom") {
                    utils.showObject(urlCustomInput);
                    utils.showObject(editCustomUrl);
                    editCustomUrl.val(dest);
                } else {
                    utils.showObject(editDestUrl);
                    utils.showObject(editProtocol);
                    editDestUrl.val(dest);
                    editProtocol.val(protocol);
                }

                editUrlName.val(name);
                editUrlType.val(type);
                editShortUrl.val(short);
                // editDestUrl.val(dest);
                editUrlId.val(id);

                customLog("Edit action clicked.");
                return;
            }
            if (action == "delete") {
                var deleteUrlForm = $("#deleteUrlForm");
                customLog("Delete action clicked.");
                $("#deleteUrlShort").text(short);
                $("#confirmDeleteUrl").data("id", id);
                $("#confirmDeleteUrl").show();
                $("#confirmDeleteUrl").prop("disabled", false);
                $("#deleteUrlForm").show();
                return;
            }
            if (action == "bookmark") {
                customLog("Bookmark action clicked.");
                api("POST", "bookmark", "id="+id, function(data) {
                    if (data["status"] == "OK") {
                        var icon = data["icon"];
                        actionObj.html(icon);
                    }
                });
                return;
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

            customLog("Event type: " + e.type);
            customLog("Row: " + row);
            customLog("ID: " + url_id);

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
            var deleteSelectedBtn = $(".deleteSelectedBtn");
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
        $(".deleteSelectedBtn").on("click", function() {
            // variable urls fetched from global scope
            var ids      = urls.join(",");
            customLog("Deleting urls: " + urls);

            if (urls.length == 0) {
                customLog("No URLs selected.");
                return;
            }

            api("POST", "delete", "id="+ids);

            urls.forEach(function(id) {
                customLog("Removing " + id + " from table.");
                $("tr[data-id='" + id + "']").remove();
            });

            // Buttons
            var deleteSelectedBtn = $(".deleteSelectedBtn");
                deleteSelectedBtn.attr("disabled", true);
                deleteSelectedBtn.attr("data-urls", "");
            urls        = [];
            urlsChecked = 0;

            $("#confirmDeleteUrl").hide();
            $("#deleteUrlForm").hide();
        });

    });
    /* ────────────────────────────────────────────────────────────────────────── */
    /*                            End of document.ready                           */
    /* ────────────────────────────────────────────────────────────────────────── */

    $(window).on("load", function() {
        var endTime   = performance.now();
        var timeTaken = endTime - startTime;
        customLog("Time taken for window to load up: " + timeTaken + " ms.");
    });

</script>