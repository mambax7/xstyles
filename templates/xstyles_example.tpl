<section id="<{$xs_sample.id|escape}>" class="xs-example" data-xs-section="<{$xs_sample.section|escape}>" aria-labelledby="<{$xs_sample.id|escape}>-title">
    <header class="xs-example-header">
        <h2 id="<{$xs_sample.id|escape}>-title"><a href="<{$xs_sample.url|escape}>"><{$xs_sample.title|escape}></a></h2>
        <p class="xs-selector"><code><{$xs_sample.selector|escape}></code></p>
        <{if $xs_sample.section == 'blocks'}><p><{$smarty.const._MD_XSTYLES_MENU_ID_NOTE|escape}></p><{/if}>
    </header>
    <{if $xs_sample.issues|default:[]}>
        <aside class="xs-known-issue"><p><{$smarty.const._MD_XSTYLES_CORE_MARKUP_ISSUE|escape}></p>
            <ul><{foreach from=$xs_sample.issues item=xs_issue}><li><code><{$xs_issue|escape}></code></li><{/foreach}></ul>
        </aside>
    <{/if}>
    <div class="xs-specimen" data-xs-specimen><{$xs_sample.html}></div>
    <details class="xs-source">
        <summary><{$smarty.const._MD_XSTYLES_SOURCE|escape}></summary>
        <p><{$smarty.const._MD_XSTYLES_SOURCE_NOTE|escape}></p>
        <div class="xs-source-toolbar"><button type="button" data-xs-copy="<{$xs_sample.id|escape}>-source" hidden><{$smarty.const._MD_XSTYLES_COPY|escape}></button><span class="xs-copy-status" role="status"></span></div>
        <pre tabindex="0"><code id="<{$xs_sample.id|escape}>-source"><{$xs_sample.html|escape}></code></pre>
    </details>
</section>
