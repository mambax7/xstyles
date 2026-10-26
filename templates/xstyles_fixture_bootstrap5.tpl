<{if $xs_fixture_kind == 'bs5-buttons'}>
<div data-xs-bootstrap5="Button">
    <p data-xs-bs5-unavailable role="status"><{$smarty.const._MD_XSTYLES_BS5_UNAVAILABLE|escape}></p>
    <button type="button" class="btn btn-primary"><{$smarty.const._MD_XSTYLES_BS5_PRIMARY|escape}></button>
    <button type="button" class="btn btn-outline-secondary"><{$smarty.const._MD_XSTYLES_BS5_SECONDARY|escape}></button>
    <button type="button" class="btn btn-primary active" data-bs-toggle="button" aria-pressed="true"><{$smarty.const._MD_XSTYLES_BS5_TOGGLE|escape}></button>
    <button type="button" class="btn btn-secondary" disabled><{$smarty.const._MD_XSTYLES_BS5_DISABLED|escape}></button>
    <span class="badge bg-success"><{$smarty.const._MD_XSTYLES_PUBLISHED|escape}></span>
    <span class="badge rounded-pill bg-secondary"><{$smarty.const._MD_XSTYLES_DRAFT|escape}></span>
</div>
<{elseif $xs_fixture_kind == 'bs5-alerts'}>
<div data-xs-bootstrap5="Alert">
    <p data-xs-bs5-unavailable role="status"><{$smarty.const._MD_XSTYLES_BS5_UNAVAILABLE|escape}></p>
    <div class="alert alert-success" role="alert"><{$smarty.const._MD_XSTYLES_SUCCESS_TEXT|escape}></div>
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><{$smarty.const._MD_XSTYLES_ERROR_TEXT|escape}><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<{$smarty.const._MD_XSTYLES_BS5_CLOSE|escape}>"></button></div>
    <div class="card"><div class="card-header"><{$smarty.const._MD_XSTYLES_NEWS|escape}></div><div class="card-body"><h3 class="card-title"><{$smarty.const._MD_XSTYLES_ARTICLE_TITLE|escape}></h3><p class="card-text"><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p><a class="card-link" href="#xs-bs5-alerts"><{$smarty.const._MD_XSTYLES_PREVIEW|escape}></a></div><div class="card-footer"><{$smarty.const._MD_XSTYLES_SAMPLE_FOOTER|escape}></div></div>
</div>
<{elseif $xs_fixture_kind == 'bs5-navigation'}>
<nav aria-label="<{$smarty.const._MD_XSTYLES_BS5_NAVIGATION|escape}>"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="#xs-bs5-navigation"><{$smarty.const._MD_XSTYLES_HOME|escape}></a></li><li class="breadcrumb-item"><a href="#xs-bs5-navigation"><{$smarty.const._MD_XSTYLES_NEWS|escape}></a></li><li class="breadcrumb-item active" aria-current="page"><{$smarty.const._MD_XSTYLES_ARTICLE_TITLE|escape}></li></ol></nav>
<nav aria-label="<{$smarty.const._MD_XSTYLES_PAGINATION|escape}>"><ul class="pagination flex-wrap"><li class="page-item disabled"><span class="page-link" aria-disabled="true"><{$smarty.const._MD_XSTYLES_BS5_PREVIOUS|escape}></span></li><li class="page-item active" aria-current="page"><span class="page-link">1</span></li><li class="page-item"><a class="page-link" href="#xs-bs5-navigation">2</a></li><li class="page-item"><a class="page-link" href="#xs-bs5-navigation"><{$smarty.const._MD_XSTYLES_BS5_NEXT|escape}></a></li></ul></nav>
<{elseif $xs_fixture_kind == 'bs5-inputs'}>
<div>
    <label class="form-label" for="xs-bs5-username"><{$smarty.const._MD_XSTYLES_BS5_USERNAME|escape}></label>
    <div class="input-group has-validation"><span class="input-group-text" id="xs-bs5-username-prefix">@</span><input id="xs-bs5-username" type="text" class="form-control is-valid" value="alex" aria-describedby="xs-bs5-username-prefix xs-bs5-valid"><div class="valid-feedback" id="xs-bs5-valid"><{$smarty.const._MD_XSTYLES_BS5_VALID|escape}></div></div>
    <label class="form-label" for="xs-bs5-title"><{$smarty.const._MD_XSTYLES_BS5_DETAILS|escape}></label>
    <input id="xs-bs5-title" type="text" class="form-control is-invalid" required aria-invalid="true" aria-describedby="xs-bs5-invalid">
    <div class="invalid-feedback" id="xs-bs5-invalid"><{$smarty.const._MD_XSTYLES_BS5_INVALID|escape}></div>
    <label class="form-label" for="xs-bs5-disabled"><{$smarty.const._MD_XSTYLES_FIELD_DISABLED|escape}></label><input id="xs-bs5-disabled" class="form-control" value="<{$smarty.const._MD_XSTYLES_DISABLED_VALUE|escape}>" disabled>
</div>
<{elseif $xs_fixture_kind == 'bs5-tabs'}>
<div data-xs-bootstrap5="Tab Collapse">
    <p data-xs-bs5-unavailable role="status"><{$smarty.const._MD_XSTYLES_BS5_UNAVAILABLE|escape}></p>
    <div class="nav nav-tabs" role="tablist" aria-label="<{$smarty.const._MD_XSTYLES_BS5_DETAILS|escape}>">
        <button id="xs-bs5-details-tab" class="nav-link active" data-bs-toggle="tab" data-bs-target="#xs-bs5-details-pane" type="button" role="tab" aria-controls="xs-bs5-details-pane" aria-selected="true"><{$smarty.const._MD_XSTYLES_BS5_DETAILS|escape}></button>
        <button id="xs-bs5-discussion-tab" class="nav-link" data-bs-toggle="tab" data-bs-target="#xs-bs5-discussion-pane" type="button" role="tab" aria-controls="xs-bs5-discussion-pane" aria-selected="false" tabindex="-1"><{$smarty.const._MD_XSTYLES_BS5_DISCUSSION|escape}></button>
    </div>
    <div class="tab-content"><div class="tab-pane fade show active" id="xs-bs5-details-pane" role="tabpanel" aria-labelledby="xs-bs5-details-tab" tabindex="0"><p><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p></div><div class="tab-pane fade" id="xs-bs5-discussion-pane" role="tabpanel" aria-labelledby="xs-bs5-discussion-tab" tabindex="0"><p><{$smarty.const._MD_XSTYLES_QUOTE_TEXT|escape}></p></div></div>
    <div class="accordion" id="xs-bs5-accordion"><div class="accordion-item"><h3 class="accordion-header" id="xs-bs5-accordion-heading"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#xs-bs5-accordion-body" aria-expanded="false" aria-controls="xs-bs5-accordion-body"><{$smarty.const._MD_XSTYLES_BS5_MORE|escape}></button></h3><div id="xs-bs5-accordion-body" class="accordion-collapse collapse" aria-labelledby="xs-bs5-accordion-heading" data-bs-parent="#xs-bs5-accordion"><div class="accordion-body"><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></div></div></div></div>
</div>
<{elseif $xs_fixture_kind == 'bs5-dropdown'}>
<div data-xs-bootstrap5="Dropdown">
    <p data-xs-bs5-unavailable role="status"><{$smarty.const._MD_XSTYLES_BS5_UNAVAILABLE|escape}></p>
    <div class="dropdown"><button class="btn btn-secondary dropdown-toggle" type="button" id="xs-bs5-dropdown-trigger" data-bs-toggle="dropdown" aria-expanded="false"><{$smarty.const._MD_XSTYLES_FIELD_ACTIONS|escape}></button><ul class="dropdown-menu" aria-labelledby="xs-bs5-dropdown-trigger"><li><button class="dropdown-item" type="button"><{$smarty.const._MD_XSTYLES_PREVIEW|escape}></button></li><li><button class="dropdown-item active" type="button" aria-current="true"><{$smarty.const._MD_XSTYLES_BS5_DETAILS|escape}></button></li><li><button class="dropdown-item disabled" type="button" disabled><{$smarty.const._MD_XSTYLES_BS5_DISABLED|escape}></button></li></ul></div>
</div>
<{elseif $xs_fixture_kind == 'bs5-modal'}>
<div data-xs-bootstrap5="Modal">
    <p data-xs-bs5-unavailable role="status"><{$smarty.const._MD_XSTYLES_BS5_UNAVAILABLE|escape}></p>
    <button type="button" id="xs-bs5-modal-trigger" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#xs-bs5-dialog"><{$smarty.const._MD_XSTYLES_BS5_OPEN_MODAL|escape}></button>
    <div class="modal fade" id="xs-bs5-dialog" tabindex="-1" aria-labelledby="xs-bs5-dialog-title" aria-describedby="xs-bs5-dialog-description" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h3 class="modal-title" id="xs-bs5-dialog-title"><{$smarty.const._MD_XSTYLES_CONFIRM_TITLE|escape}></h3><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<{$smarty.const._MD_XSTYLES_BS5_CLOSE|escape}>"></button></div><div class="modal-body"><p id="xs-bs5-dialog-description"><{$smarty.const._MD_XSTYLES_CONFIRM_TEXT|escape}></p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><{$smarty.const._MD_XSTYLES_BS5_CLOSE|escape}></button></div></div></div></div>
</div>
<{elseif $xs_fixture_kind == 'bs5-toast'}>
<div data-xs-bootstrap5="Toast">
    <p data-xs-bs5-unavailable role="status"><{$smarty.const._MD_XSTYLES_BS5_UNAVAILABLE|escape}></p>
    <button type="button" class="btn btn-primary" data-xs-bs5-toast aria-controls="xs-bs5-notification"><{$smarty.const._MD_XSTYLES_BS5_SHOW_TOAST|escape}></button>
    <div aria-live="polite" aria-atomic="true"><div class="toast" id="xs-bs5-notification" role="status" aria-atomic="true" data-bs-autohide="false"><div class="toast-header"><strong class="me-auto"><{$smarty.const._MD_XSTYLES_NEWS|escape}></strong><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="<{$smarty.const._MD_XSTYLES_BS5_CLOSE|escape}>"></button></div><div class="toast-body"><{$smarty.const._MD_XSTYLES_NOTICE_TEXT|escape}></div></div></div>
</div>
<{/if}>
