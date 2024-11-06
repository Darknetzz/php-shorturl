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
        <?= listUrls() ?>
        </div>
    </div>
</div>