<div class="container-fluid">
    <div class="card m-3 border border-primary">
    <div class="card-header text-bg-primary d-flex justify-content-between">
        <div>
            <h1>Created URLs</h1>
        </div>
        <div>
            <button class="btn btn-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUrls" aria-expanded="false" aria-controls="collapseUrls">
                <?= icon("eye") ?> Show/Hide
            </button>
        </div>
    </div>
    <div class="card-body collapse show" id="collapseUrls">
        <div id="table-toolbar">
            <!-- Table Toolbar -->
        </div>
        <table class="table table-default" 
            data-toggle="table"
            data-search="true"
            data-pagination="true"
            data-page-size="25"
            data-showColumnsSearch="true"
            data-showToggle="true"
            data-showColumnsToggleAll="true"
            data-showExtendedPagination="true"
            data-showPaginationSwitch="true"
        >
            <thead>
                <tr class="table table-primary">
                    <th data-field="id" data-sortable="true">ID</th>
                    <th data-field="type" data-sortable="true">Type</th>
                    <th data-field="shorturl" data-sortable="true">Short URL</th>
                    <th data-field="desturl" data-sortable="true">Destination URL</th>
                    <th data-field="user" data-sortable="true">User</th>
                    <th>Actions</th>
                </tr>
            </thead>

        <tbody>
        <?php
            $urls = getUrls();
            foreach ($urls as $url) {
                $username  = (getUser($url["userid"])["username"] ?? "Unknown");
                echo '
                    <tr
                        data-type="'.$url["type"].'" 
                        data-id="'.$url["id"].'"
                        data-shorturl="'.$url["short"].'"
                        data-desturl="'.$url["dest"].'"
                        data-user="'.$username.'"
                    >
                        <td>
                            '.$url["id"].'
                        </td>
                        <td>
                            '.$url["type"].'
                        </td>
                        <td>
                            <a href="'.$url["short"].'" target="_blank">'.$url["short"].'</a></td>
                        <td>
                            <a href="'.$url["dest"].'" target="_blank">'.$url["dest"].'</a>
                        </td>
                        <td>
                            '.$username.'
                        </td>
                        <td>

                            <a href="javascript:void(0);" class="url-action m-3" 
                                data-action="edit"
                                data-bs-toggle="modal"
                                data-bs-target="#editUrlModal">
                                    '.icon("pencil", 2).'
                            </a>

                            <a href="javascript:void(0);" class="url-action m-3" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteUrlModal" 
                                data-action="delete">
                                    '.icon("trash", 2).'
                            </a>

                        </td>
                    </tr>
                ';
            }
        ?>
        </tbody>

        </table>
        </div>
    </div>

    <!-- Edit URL Modal -->
    <div class="modal fade" id="editUrlModal" tabindex="-1" aria-labelledby="editUrlModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUrlModalLabel">Edit URL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUrlForm">
                        <div class="mb-3">
                            <label for="editUrlType" class="form-label">Type</label>
                            <select class="form-select" id="editUrlType" name="type" required>
                                <?php
                                    foreach ($cfg["url_types"] as $type) {
                                        echo '<option value="'.$type["value"].'">'.$type["name"].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editShortUrl" class="form-label">Short URL</label>
                            <input type="text" class="form-control" id="editShortUrl" name="short" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDestUrl" class="form-label">Destination URL</label>
                            <input type="text" class="form-control" id="editDestUrl" name="dest" required>
                        </div>
                        <input type="hidden" id="editUrlId" name="id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveEditUrl">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete URL Modal -->
    <div class="modal fade" id="deleteUrlModal" tabindex="-1" aria-labelledby="deleteUrlModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUrlModalLabel">Delete URL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-danger">

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


</div>