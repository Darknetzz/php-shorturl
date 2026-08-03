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
                    if (data["content_width"] != null) {
                        document.documentElement.style.setProperty("--content-width", data["content_width"] + "%");
                    }
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

        // NOTE: Tooltips (Bootstrap) — click to pin, click outside to dismiss
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => {
            const tip = new bootstrap.Tooltip(tooltipTriggerEl, { trigger: 'click', html: true });
            tooltipTriggerEl.addEventListener('show.bs.tooltip', () => {
                tooltipTriggerList.forEach(other => {
                    if (other !== tooltipTriggerEl) {
                        bootstrap.Tooltip.getInstance(other)?.hide();
                    }
                });
            });
            return tip;
        });

        document.addEventListener('click', (e) => {
            const target = e.target;
            if (!(target instanceof Element)) {
                return;
            }
            if (target.closest('[data-bs-toggle="tooltip"]') || target.closest('.tooltip')) {
                return;
            }
            tooltipTriggerList.forEach(el => {
                bootstrap.Tooltip.getInstance(el)?.hide();
            });
        });


        // NOTE: Ace Editor
        const codeInputs = $(".codeInput");
        codeInputs.each(function() {
            var $el       = $(this);
            var el        = this;
            var inputName = $el.attr("name");
            var initialValue = "";
            var $syncTarget;
            var editorHost;

            customLog("Highlighting code input for " + inputName);

            if (el.tagName === "TEXTAREA") {
                initialValue = $el.val();
                $el.hide();
                $syncTarget = $el;
                var $editorDiv = $("<div></div>")
                    .addClass("codeBox aceEditorHost")
                    .css({ width: "100%", minHeight: "250px" });
                $el.after($editorDiv);
                editorHost = $editorDiv[0];
            } else {
                initialValue = $el.text();
                $syncTarget = $("<textarea></textarea>")
                    .attr("name", inputName)
                    .css("display", "none");
                $el.after($syncTarget);
                if (inputName) {
                    $el.removeAttr("name");
                }
                editorHost = el;
            }

            var aceOpts = {
                mode: "ace/mode/html",
                theme: "ace/theme/monokai",
                showPrintMargin: false,
                tabSize: 4,
                useSoftTabs: true,
                wrap: true,
                autoScrollEditorIntoView: true,
                maxLines: Infinity,
                minLines: 10,
            };

            var editor = ace.edit(editorHost, aceOpts);
            editor.setValue(initialValue || "", -1);
            editor.session.on("change", function() {
                $syncTarget.val(editor.getValue());
            });
            $syncTarget.val(editor.getValue());
            $(editorHost).data("aceEditor", editor);
        });

        function resizeVisibleAceEditors($scope) {
            ($scope || $(document)).find(".ace_editor").each(function() {
                var editor = ace.edit(this);
                editor.resize();
            });
        }

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
        /*                    URL form field visibility (DRY)                     */
        /* ───────────────────────────────────────────────────────────────────── */
        var shortTypeRows = {
            "path"     : "short_path",
            "subdomain": "short_domain",
            "custom"   : "short_custom",
        };

        function urlFormRow(inputName) {
            return ".urlInputRow[data-input='" + inputName + "']";
        }

        function setUrlFormRows(rows, visible) {
            rows.forEach(function(name) {
                var $row = $(urlFormRow(name));
                if (visible) {
                    utils.showObject($row);
                    resizeVisibleAceEditors($row);
                } else {
                    utils.hideObject($row);
                }
            });
        }

        function updateShortTypeRows() {
            var shortType = $("#shortTypeInput").val();

            setUrlFormRows(Object.values(shortTypeRows), false);

            if (shortTypeRows[shortType]) {
                utils.showObject(urlFormRow(shortTypeRows[shortType]));
                utils.showObject(urlFormRow("dest_type"));
            } else {
                utils.hideObject(urlFormRow("dest_type"));
                setUrlFormRows(["dest_redirect", "dest_alias", "dest_custom"], false);
            }
        }

        function updateDestTypeRows() {
            var destType = $("#destTypeInput").val();
            var destRows = {
                "redirect": ["dest_redirect"],
                "alias"   : ["dest_alias"],
                "custom"  : ["dest_custom"],
            };

            setUrlFormRows(["dest_redirect", "dest_alias", "dest_custom"], false);

            if ($(urlFormRow("dest_type")).is(":hidden")) {
                return;
            }

            if (destRows[destType]) {
                setUrlFormRows(destRows[destType], true);
            }
        }

        function updateExpireFormRows() {
            var timeEnabled  = $("#enableExpireTimeInput").is(":checked");
            var clicksEnabled = $("#enableMaxClicksInput").is(":checked");
            var mode = $("#expireTimeModeInput").val() || "relative";

            setUrlFormRows([
                "expire_time_mode",
                "expire_relative_value",
                "expire_absolute",
                "max_clicks",
                "on_expire",
            ], false);

            if (timeEnabled) {
                utils.showObject(urlFormRow("expire_time_mode"));
                if (mode === "relative") {
                    utils.showObject(urlFormRow("expire_relative_value"));
                }
                if (mode === "absolute") {
                    utils.showObject(urlFormRow("expire_absolute"));
                }
            }

            if (clicksEnabled) {
                utils.showObject(urlFormRow("max_clicks"));
            }

            if (timeEnabled || clicksEnabled) {
                utils.showObject(urlFormRow("on_expire"));
            }
        }

        function updateConfirmFormRows() {
            setUrlFormRows(["confirm_message"], $("#enableConfirmInput").is(":checked"));
        }

        function updateUrlFormRows() {
            updateShortTypeRows();
            updateDestTypeRows();
            updateExpireFormRows();
            updateConfirmFormRows();
        }

        $("#shortTypeInput").on("change", function() {
            updateShortTypeRows();
            updateDestTypeRows();
            updateShortPreview();
        });

        $("#destTypeInput").on("change", updateDestTypeRows);

        $("#enableExpireTimeInput, #enableMaxClicksInput").on("change", updateExpireFormRows);
        $("#expireTimeModeInput").on("change", updateExpireFormRows);
        $("#enableConfirmInput").on("change", updateConfirmFormRows);

        updateUrlFormRows();

        /* ───────────────────────────────────────────────────────────────────── */
        /*                         short URL live preview                         */
        /* ───────────────────────────────────────────────────────────────────── */
        var shortPreviewBaseUrl    = <?= json_encode($cfg["base_url"]) ?>;
        var shortPreviewBaseDomain = <?= json_encode($cfg["base_domain"]) ?>;

        function escapeHtml(value) {
            return $("<div>").text(value == null ? "" : String(value)).html();
        }

        function buildShortPreview(shortType, shortVal) {
            shortVal = String(shortVal || "").trim();
            if (shortType === "path") {
                if (!shortVal) {
                    return {
                        text: shortPreviewBaseUrl + "/<auto>",
                        href: null,
                        muted: true,
                    };
                }
                var pathUrl = shortPreviewBaseUrl + "/" + shortVal;
                return { text: pathUrl, href: pathUrl, muted: false };
            }
            if (shortType === "subdomain") {
                if (!shortVal) {
                    return {
                        text: "<subdomain>." + shortPreviewBaseDomain,
                        href: null,
                        muted: true,
                    };
                }
                var host = shortVal + "." + shortPreviewBaseDomain;
                return { text: host, href: "<?= $cfg["protocol"] ?>://" + host, muted: false };
            }
            if (shortType === "custom") {
                if (!shortVal) {
                    return {
                        text: "<custom-url>",
                        href: null,
                        muted: true,
                    };
                }
                var href = /^(https?:)?\/\//i.test(shortVal) ? shortVal : null;
                return { text: shortVal, href: href, muted: false };
            }
            return {
                text: "Select a short URL type",
                href: null,
                muted: true,
            };
        }

        function updateShortPreview($form) {
            $form = $form && $form.length ? $form : $("#urlForm");
            if (!$form.length) {
                return;
            }

            var $preview = $form.find(".shortInputPreview").first();
            if (!$preview.length) {
                return;
            }

            var shortType = $form.find("#shortTypeInput").val();
            var inputName = shortTypeRows[shortType];
            var shortVal  = "";
            if (inputName) {
                shortVal = $form.find(urlFormRow(inputName) + " .shortInput").val();
            }

            var preview   = buildShortPreview(shortType, shortVal);
            // Only real URLs are copyable — placeholders like <auto> are not.
            var copyValue = preview.muted ? "" : (preview.href || preview.text || "");
            var $url      = $preview.find(".shortPreviewUrl");
            var $copy     = $preview.find(".shortPreviewCopyBtn");

            $url.text(preview.text).toggleClass("text-muted", !!preview.muted);

            if (copyValue) {
                $preview.attr("data-copy", copyValue).addClass("is-copyable");
                $copy.attr("data-copy", copyValue).prop("disabled", false);
            } else {
                $preview.removeAttr("data-copy").removeClass("is-copyable");
                $copy.removeAttr("data-copy").prop("disabled", true);
            }
        }

        function copyShortPreviewUrl(text) {
            if (!text) {
                return;
            }

            var onDone = function() {
                toast("URL copied to clipboard", "success", "Copied", "clipboard-check");
            };

            var onFail = function() {
                toast("Could not copy URL", "danger", "Error", "clipboard-x");
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(onDone).catch(onFail);
                return;
            }

            var $temp = $("<textarea>").val(text).css({ position: "fixed", left: "-9999px" }).appendTo("body").select();
            try {
                if (document.execCommand("copy")) {
                    onDone();
                } else {
                    onFail();
                }
            } catch (e) {
                onFail();
            }
            $temp.remove();
        }

        $(document).on("input", ".shortInput", function() {
            updateShortPreview($(this).closest("form"));
        });

        $(document).on("click", ".shortInputPreview.is-copyable", function(e) {
            e.preventDefault();
            copyShortPreviewUrl($(this).attr("data-copy"));
        });

        updateShortPreview();

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
                editUrlForm.find(".openUrlBtn").attr("href", short || "#");

                // Existing shorts are stored as path slugs; show them in the live preview.
                var shortType = "path";
                if (/^(https?:)?\/\//i.test(short || "")) {
                    shortType = "custom";
                } else if ((short || "").indexOf(".") !== -1) {
                    shortType = "subdomain";
                }
                editUrlForm.find("#shortTypeInput").val(shortType);
                updateShortTypeRows();
                updateDestTypeRows();

                var shortInputName = shortTypeRows[shortType];
                if (shortInputName) {
                    editUrlForm.find(urlFormRow(shortInputName) + " .shortInput").val(short || "");
                }
                updateShortPreview(editUrlForm);
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

        // NOTE: .urlValidate — peel http(s):// into the protocol prefix select
        function syncUrlProtocols(protocol, $scope) {
            var $protocols = $scope && $scope.length
                ? $scope.find(".url-protocol")
                : $(".url-protocol");
            $protocols.val(protocol);
        }

        $(document).on("input", ".urlValidate", function() {
            var $input = $(this);
            var url    = String($input.val() || "");
            var match  = url.match(/^(https?:\/\/)/i);

            if (!match) {
                return;
            }

            var protocol = match[1].toLowerCase();
            $input.val(url.slice(match[1].length));
            syncUrlProtocols(protocol, $input.closest("form"));
        });

        $(document).on("change", ".url-protocol", function() {
            syncUrlProtocols($(this).val(), $(this).closest("form"));
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