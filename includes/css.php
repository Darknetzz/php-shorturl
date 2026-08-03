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

    /* Page content (not the navbar's nested container-fluid) */
    body > .container-fluid {
        max-width: var(--content-width);
        margin-left: auto;
        margin-right: auto;
    }
</style>
