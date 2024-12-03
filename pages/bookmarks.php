<div class="container-fluid">
    <div class="card m-3 border border-primary">
                <h1 class="card-header text-bg-primary">Bookmarks</h1>
                <div class="card-body">
<?php

if (empty($_SESSION['id'])) {
    die(alert('You must be logged in to view this page.', 'danger'));
}

$bookmarksJSON = getUser($_SESSION['id'], 'bookmarks');

if (empty($bookmarksJSON)) {
    die(alert('You have no bookmarks.', 'danger'));
}

if (!json_validate($bookmarksJSON)) {
    die(alert('Your bookmarks are not valid JSON.', 'danger'));
}

$currentBookmarks = json_decode($bookmarksJSON, true);
$bookmarks        = [];
foreach ($currentBookmarks as $bookmark) {
    $urlData = getUrl($bookmark);
    if (!empty($urlData)) {
        array_push($bookmarks, $urlData);
        continue;
    }
    die(alert('One of your bookmarks does not exist.', 'danger'));
}

echo listUrls($bookmarks);

?>

        </div>
    </div>
</div>