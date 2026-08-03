<?php
    $contentWidth = (int) (getUserSettings()["content_width"] ?? 100);
    $contentWidth = max(40, min(100, $contentWidth));
?>
<style>
    :root {
        --content-width: <?= $contentWidth ?>%;
    }

    .toast-container {
        position: fixed;
        bottom  : 1rem;
        right   : 1rem;
        z-index : 9999;
        width   : 30%;
    }

    .tooltip-inner {
        background-color        : #343a40;
        color                   : #fff;
        border                  : 1px solid #ffffff;
        border-radius           : 0.25rem;
        max-width               : 500px;
        max-height              : min(200px, 40vh);
        overflow-y              : auto;
        text-align              : left;
        opacity                 : 1;
    }

    .tooltip.show {
        pointer-events: auto;
    }

    .inline {
        display: inline-flex;
    }

    .codeBox {
        min-height      : 250px;
        max-height      : 100%;
        overflow        : auto;
        background-color: #343a40;
        color           : #fff;
        border          : 1px solid #2f5f8f;
        border-radius   : 0.25rem;
        padding         : 0.5rem;
        margin          : 0.5rem;
        white-space     : pre;
    }

    .dynamic-form {
        /* display: flex; */
        /* flex-wrap: wrap; */
        max-width: 1500px;
    }

    .shortInputPreview {
        border-width: 1px;
        border-style: solid;
        height: 3rem;
        min-height: 3rem;
        max-height: 3rem;
        flex-wrap: nowrap;
        overflow: hidden;
        box-sizing: border-box;
    }

    .shortInputPreview .shortPreviewUrl {
        display: block;
        margin: 0;
        font-size: 1.05rem;
        font-weight: 600;
        line-height: 1.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: default;
        background: transparent;
        border: 0;
        padding: 0;
        user-select: none;
    }

    .shortInputPreview.is-copyable .shortPreviewUrl {
        cursor: pointer;
    }

    .shortInputPreview .shortPreviewCopyBtn {
        flex-shrink: 0;
        white-space: nowrap;
        width: 5.75rem;
        height: 2rem;
        padding-top: 0;
        padding-bottom: 0;
    }

    .url-protocol {
        flex: 0 0 auto;
        width: auto;
        max-width: 8.5rem;
    }

    /* Page content (not the navbar's nested container-fluid) */
    body > .container-fluid {
        max-width: var(--content-width);
        margin-left: auto;
        margin-right: auto;
    }
</style>
