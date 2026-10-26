<{if $xs_fixture_comment_rows}>
<table class="outer">
    <caption><{$smarty.const._MD_XSTYLES_COMMENT_CAPTION|escape}></caption>
    <tbody><{$xs_fixture_html}></tbody>
</table>
<{else}>
<div class="xs-comment-frame"><{$xs_fixture_html}></div>
<{/if}>
