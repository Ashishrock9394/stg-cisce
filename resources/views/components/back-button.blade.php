<style type="text/css">
    .back-button-heading {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 14px;
    }

    .back-button-heading .back-button {
        position: static;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 36px;
        width: 36px;
        height: 36px;
        margin: 0;
        padding: 0;
        border: 2px solid var(--org-color);
        border-radius: 50%;
        background-color: transparent;
        color: var(--org-color);
    }

    .back-button-heading .heading-rw {
        display: flex;
        align-items: center;
        min-width: 0;
        margin: 0 !important;
        padding: 0;
    }

    .back-button-heading .heading-rw h1 {
        white-space: nowrap;
        margin: 0;
    }

    .back-button-heading .back-button > span.arrow,
    .back-button-heading .back-button > span.arrow svg {
        display: block;
        flex: none;
    }

    .back-button-heading .back-button > span.arrow svg {
        width: 18px;
        height: 18px;
        color: var(--org-color);
        fill: none;
    }

    .back-button-heading .back-button:hover {
        border-color: var(--org-color);
        color: var(--org-color);
    }
</style>

<div class="col-12 back-button-heading">
    <a href="#a" onclick="history.back()" class="back-button">
        <span class="arrow">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" class="bi bi-arrow-left-short" viewBox="0 0 24 24" aria-hidden="true" focusable="false" style="display:block; flex:none;">
                <path fill="none" d="M19 12H5m0 0 7-7m-7 7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </span>
    </a>
    <div class="heading-rw mt-2 mb-0 pt-0">
        <h1>{{$title}}</h1>
    </div>

    <div class="col-auto col-md-auto ml-auto px-0">
    @isset($actionsOutside)
            <div class="datatable-actions-outside">
                {{ $actionsOutside }}
            </div>
        @endisset
    </div>

</div>
