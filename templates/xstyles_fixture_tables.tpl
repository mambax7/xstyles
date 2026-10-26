<{if $xs_fixture_kind == 'semantic-table'}>
<table>
    <caption><{$smarty.const._MD_XSTYLES_TABLE_CAPTION|escape}></caption>
    <thead><tr><th scope="col"><{$smarty.const._MD_XSTYLES_FIELD_NAME|escape}></th><th scope="col"><{$smarty.const._MD_XSTYLES_STATUS|escape}></th><th scope="col"><{$smarty.const._MD_XSTYLES_POSTS|escape}></th></tr></thead>
    <tbody><tr><th scope="row"><{$smarty.const._MD_XSTYLES_NEWS|escape}></th><td><{$smarty.const._MD_XSTYLES_PUBLISHED|escape}></td><td>128</td></tr><tr><th scope="row"><{$smarty.const._MD_XSTYLES_EVENTS|escape}></th><td><{$smarty.const._MD_XSTYLES_DRAFT|escape}></td><td>0</td></tr><tr><th scope="row"><{$smarty.const._MD_XSTYLES_LONG_TITLE|escape}></th><td><{$smarty.const._MD_XSTYLES_PUBLISHED|escape}></td><td>12,480</td></tr></tbody>
    <tfoot><tr><th scope="row" colspan="2"><{$smarty.const._MD_XSTYLES_TOTAL|escape}></th><td>12,608</td></tr></tfoot>
</table>
<{else}>
<table class="outer">
    <caption><{$smarty.const._MD_XSTYLES_LEGACY_TABLE_CAPTION|escape}></caption>
    <thead><tr><th class="head" scope="col"><{$smarty.const._MD_XSTYLES_FIELD_NAME|escape}></th><th class="head" scope="col"><{$smarty.const._MD_XSTYLES_STATUS|escape}></th><th class="head" scope="col"><{$smarty.const._MD_XSTYLES_POSTS|escape}></th></tr></thead>
    <tbody><tr class="odd"><th scope="row"><{$smarty.const._MD_XSTYLES_NEWS|escape}></th><td><{$smarty.const._MD_XSTYLES_PUBLISHED|escape}></td><td>128</td></tr><tr class="even"><th scope="row"><{$smarty.const._MD_XSTYLES_EVENTS|escape}></th><td><{$smarty.const._MD_XSTYLES_DRAFT|escape}></td><td>0</td></tr><tr class="odd"><th scope="row"><{$smarty.const._MD_XSTYLES_MEMBERS|escape}></th><td><{$smarty.const._MD_XSTYLES_PUBLIC|escape}></td><td>42</td></tr></tbody>
    <tfoot><tr class="foot"><th scope="row" colspan="2"><{$smarty.const._MD_XSTYLES_TOTAL|escape}></th><td>170</td></tr></tfoot>
</table>
<{/if}>
