<{* Bootstrap 6.0.0-alpha.1; assets and data API belong to the selected theme. *}>
<{if $xs_fixture_kind == 'bs6-buttons'}>
<div data-xs-bootstrap6="Button">
    <p data-xs-bs6-unavailable role="status"><{$smarty.const._MD_XSTYLES_BS6_UNAVAILABLE|escape}></p>
    <button type="button" class="btn-solid theme-primary"><{$smarty.const._MD_XSTYLES_BS5_PRIMARY|escape}></button>
    <button type="button" class="btn-outline theme-secondary"><{$smarty.const._MD_XSTYLES_BS5_SECONDARY|escape}></button>
    <button type="button" class="btn-solid theme-primary active" data-bs-toggle="button" aria-pressed="true"><{$smarty.const._MD_XSTYLES_BS5_TOGGLE|escape}></button>
    <button type="button" class="btn-solid theme-secondary" disabled><{$smarty.const._MD_XSTYLES_BS5_DISABLED|escape}></button>
    <span class="badge theme-success"><{$smarty.const._MD_XSTYLES_PUBLISHED|escape}></span>
    <span class="badge theme-secondary"><{$smarty.const._MD_XSTYLES_DRAFT|escape}></span>
</div>
<{elseif $xs_fixture_kind == 'bs6-cards'}>
<div class="alert theme-success" role="status"><{$smarty.const._MD_XSTYLES_SUCCESS_TEXT|escape}></div>
<div class="card"><div class="card-header"><{$smarty.const._MD_XSTYLES_NEWS|escape}></div><div class="card-body"><h3 class="card-title"><{$smarty.const._MD_XSTYLES_ARTICLE_TITLE|escape}></h3><p class="card-text"><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p><a class="card-link" href="#xs-bs6-cards"><{$smarty.const._MD_XSTYLES_PREVIEW|escape}></a></div><div class="card-footer"><{$smarty.const._MD_XSTYLES_SAMPLE_FOOTER|escape}></div></div>
<{elseif $xs_fixture_kind == 'bs6-forms'}>
<div class="field">
    <label class="form-label" for="xs-bs6-name"><{$smarty.const._MD_XSTYLES_BS5_USERNAME|escape}></label>
    <input id="xs-bs6-name" class="form-control is-valid" value="alex" aria-describedby="xs-bs6-valid">
    <div class="valid-feedback" id="xs-bs6-valid"><{$smarty.const._MD_XSTYLES_BS5_VALID|escape}></div>
</div>
<div class="field">
    <label class="form-label" for="xs-bs6-title"><{$smarty.const._MD_XSTYLES_BS5_DETAILS|escape}></label>
    <input id="xs-bs6-title" class="form-control is-invalid" required aria-invalid="true" aria-describedby="xs-bs6-invalid">
    <div class="invalid-feedback" id="xs-bs6-invalid"><{$smarty.const._MD_XSTYLES_BS5_INVALID|escape}></div>
</div>
<div class="field"><label class="form-label" for="xs-bs6-disabled"><{$smarty.const._MD_XSTYLES_FIELD_DISABLED|escape}></label><input id="xs-bs6-disabled" class="form-control" value="<{$smarty.const._MD_XSTYLES_DISABLED_VALUE|escape}>" disabled></div>
<{elseif $xs_fixture_kind == 'bs6-tabs'}>
<div data-xs-bootstrap6="Tab">
    <p data-xs-bs6-unavailable role="status"><{$smarty.const._MD_XSTYLES_BS6_UNAVAILABLE|escape}></p>
    <div class="nav nav-tabs" role="tablist" aria-label="<{$smarty.const._MD_XSTYLES_BS5_DETAILS|escape}>">
        <button id="xs-bs6-details-tab" class="nav-link active" data-bs-toggle="tab" data-bs-target="#xs-bs6-details-pane" type="button" role="tab" aria-controls="xs-bs6-details-pane" aria-selected="true"><{$smarty.const._MD_XSTYLES_BS5_DETAILS|escape}></button>
        <button id="xs-bs6-discussion-tab" class="nav-link" data-bs-toggle="tab" data-bs-target="#xs-bs6-discussion-pane" type="button" role="tab" aria-controls="xs-bs6-discussion-pane" aria-selected="false" tabindex="-1"><{$smarty.const._MD_XSTYLES_BS5_DISCUSSION|escape}></button>
    </div>
    <div class="tab-content"><div id="xs-bs6-details-pane" class="tab-pane active show" role="tabpanel" aria-labelledby="xs-bs6-details-tab" tabindex="0"><p><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p></div><div id="xs-bs6-discussion-pane" class="tab-pane" role="tabpanel" aria-labelledby="xs-bs6-discussion-tab" tabindex="0"><p><{$smarty.const._MD_XSTYLES_COMMENT_TEXT|escape}></p></div></div>
</div>
<{elseif $xs_fixture_kind == 'bs6-menu'}>
<div data-xs-bootstrap6="Menu">
    <p data-xs-bs6-unavailable role="status"><{$smarty.const._MD_XSTYLES_BS6_UNAVAILABLE|escape}></p>
    <button id="xs-bs6-menu-trigger" class="btn-solid theme-secondary" type="button" data-bs-toggle="menu" aria-expanded="false" aria-controls="xs-bs6-menu-items"><{$smarty.const._MD_XSTYLES_BS5_MORE|escape}></button>
    <div id="xs-bs6-menu-items" class="menu" aria-labelledby="xs-bs6-menu-trigger"><a class="menu-item" href="#xs-bs6-menu"><{$smarty.const._MD_XSTYLES_HOME|escape}></a><a class="menu-item" href="#xs-bs6-menu"><{$smarty.const._MD_XSTYLES_NEWS|escape}></a></div>
</div>
<{elseif $xs_fixture_kind == 'bs6-dialog'}>
<div data-xs-bootstrap6="Dialog">
    <p data-xs-bs6-unavailable role="status"><{$smarty.const._MD_XSTYLES_BS6_UNAVAILABLE|escape}></p>
    <button id="xs-bs6-dialog-trigger" type="button" class="btn-solid theme-primary" data-bs-toggle="dialog" data-bs-target="#xs-bs6-dialog-window"><{$smarty.const._MD_XSTYLES_BS5_OPEN_MODAL|escape}></button>
    <dialog class="dialog" id="xs-bs6-dialog-window" aria-labelledby="xs-bs6-dialog-heading">
        <div class="dialog-header"><h3 class="dialog-title" id="xs-bs6-dialog-heading"><{$smarty.const._MD_XSTYLES_BS5_DETAILS|escape}></h3><button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="<{$smarty.const._MD_XSTYLES_BS5_CLOSE|escape}>"></button></div>
        <div class="dialog-body"><p><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p></div>
        <div class="dialog-footer"><button type="button" class="btn-solid theme-secondary" data-bs-dismiss="dialog"><{$smarty.const._MD_XSTYLES_BS5_CLOSE|escape}></button></div>
    </dialog>
</div>
<{elseif $xs_fixture_kind == 'bs6-drawer'}>
<div data-xs-bootstrap6="Drawer">
    <p data-xs-bs6-unavailable role="status"><{$smarty.const._MD_XSTYLES_BS6_UNAVAILABLE|escape}></p>
    <button id="xs-bs6-drawer-trigger" type="button" class="btn-solid theme-primary" data-bs-toggle="drawer" data-bs-target="#xs-bs6-drawer-window"><{$smarty.const._MD_XSTYLES_BS6_OPEN_DRAWER|escape}></button>
    <dialog class="drawer drawer-start" id="xs-bs6-drawer-window" aria-labelledby="xs-bs6-drawer-heading">
        <div class="drawer-header"><h3 class="drawer-title" id="xs-bs6-drawer-heading"><{$smarty.const._MD_XSTYLES_BS5_DETAILS|escape}></h3><button type="button" class="btn-close" data-bs-dismiss="drawer" aria-label="<{$smarty.const._MD_XSTYLES_BS5_CLOSE|escape}>"></button></div>
        <div class="drawer-body"><p><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p></div>
    </dialog>
</div>
<{/if}>
