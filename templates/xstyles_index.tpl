<div id="xs-gallery" class="xs-gallery<{if !$xs_preview_frame|default:false}> xs-workbench<{/if}>"<{if !$xs_preview_frame|default:false}> data-xs-workspace<{/if}> data-xs-theme="<{$xs_theme|escape}>" data-xs-mode="<{$xs_mode|escape}>" data-xs-direction="<{$xs_direction|escape}>"
     data-xs-mode-attributes="<{$xs_mode_attributes|default:'data-bs-theme data-theme data-mt-mode'|escape}>"
     data-xs-copied="<{$smarty.const._MD_XSTYLES_COPIED|escape}>" data-xs-copy-failed="<{$smarty.const._MD_XSTYLES_COPY_FAILED|escape}>"
     data-xs-demo-notice="<{$smarty.const._MD_XSTYLES_DEMO_NOTICE|escape}>">
    <header class="xs-header">
        <p class="xs-kicker">XOOPS / xStyles</p>
        <h1><{if $xs_admin|default:false}><{$smarty.const._AM_XSTYLES_GALLERY|escape}><{else}><{$smarty.const._MD_XSTYLES_TITLE|escape}><{/if}></h1>
        <p><{$smarty.const._MD_XSTYLES_INTRO|escape}></p>
        <details class="xs-metadata"<{if $xs_preview_frame|default:false}> open<{/if}>>
        <summary><{$smarty.const._MD_XSTYLES_PREVIEW_DETAILS|escape}> · <{$xs_theme|escape}></summary>
        <dl class="xs-context">
            <div><dt><{$smarty.const._MD_XSTYLES_THEME|escape}></dt><dd><code><{$xs_theme|escape}></code></dd></div>
            <div><dt><{$smarty.const._MD_XSTYLES_LOCALE|escape}></dt><dd><{$xs_locale|escape}> / <{$xs_actual_direction|escape}></dd></div>
            <div><dt><{$smarty.const._MD_XSTYLES_RENDERER|escape}></dt><dd><code><{$xs_renderer|escape}></code></dd></div>
        </dl>
        </details>
        <{if !$xs_preview_frame|default:false}>
        <div class="xs-view-switch" hidden role="group" aria-label="<{$smarty.const._MD_XSTYLES_WORKSPACE_VIEW|escape}>">
            <button type="button" data-xs-view="preview" aria-pressed="true"><{$smarty.const._MD_XSTYLES_SINGLE_PREVIEW|escape}></button>
            <{if $xs_themes|default:[]}><button type="button" data-xs-view="compare" aria-pressed="false"><{$smarty.const._MD_XSTYLES_COMPARE_THEMES|escape}></button><{/if}>
        </div>
        <{/if}>
    </header>
    <div class="xs-workspace-body">
    <aside class="xs-sidebar">
        <nav class="xs-nav xs-sections" aria-label="<{$smarty.const._MD_XSTYLES_SECTIONS|escape}>">
            <{foreach from=$xs_navigation item=xs_link}>
                <a href="<{$xs_link.url|escape}>"<{if $xs_section == $xs_link.id}> aria-current="page"<{/if}>><{$xs_link.title|escape}></a>
            <{/foreach}>
        </nav>
        <div class="xs-filter-controls" hidden>
            <label for="xs-filter"><{$smarty.const._MD_XSTYLES_FILTER|escape}></label>
            <input id="xs-filter" type="search" autocomplete="off" placeholder="<{$smarty.const._MD_XSTYLES_FILTER_HINT|escape}>">
            <output id="xs-filter-count" for="xs-filter" aria-live="polite" aria-label="<{$smarty.const._MD_XSTYLES_FILTER_COUNT|escape}>"></output>
            <p class="xs-filter-empty" hidden><{$smarty.const._MD_XSTYLES_FILTER_EMPTY|escape}></p>
        </div>
        <{if !$xs_preview_frame|default:false}>
        <nav class="xs-element-list" aria-label="<{$smarty.const._MD_XSTYLES_ELEMENTS|escape}>">
            <{foreach from=$xs_samples item=xs_sample}><a href="#<{$xs_sample.id|escape}>" data-xs-example-link="<{$xs_sample.id|escape}>"><{$xs_sample.title|escape}></a><{/foreach}>
        </nav>
        <{/if}>
    </aside>
    <div class="xs-workspace-main">
    <div class="xs-single-tools">
    <{if !$xs_preview_frame|default:false && $xs_theme_links|default:[]}>
        <nav class="xs-nav" aria-label="<{$smarty.const._MD_XSTYLES_THEME|escape}>">
            <{foreach from=$xs_theme_links item=xs_link}>
                <a href="<{$xs_link.url|escape}>"<{if $xs_theme == $xs_link.id}> aria-current="page"<{/if}>><{$xs_link.id|escape}><{if $xs_link.id == $xs_active_theme}> (<{$smarty.const._MD_XSTYLES_ACTIVE|escape}>)<{/if}></a>
            <{/foreach}>
        </nav>
    <{/if}>
    <{if $xs_theme_preview|default:false}>
        <p class="xs-preview-notice"><{$smarty.const._MD_XSTYLES_THEME_PREVIEW_NOTE|escape}> <a href="<{$xs_reset_url|escape}>"><{$smarty.const._MD_XSTYLES_RETURN_THEME|escape}> (<{$xs_active_theme|escape}>)</a></p>
    <{/if}>
    <{if $xs_mode != 'theme'}><p class="xs-preview-notice"><{$smarty.const._MD_XSTYLES_MODE_NOTE|escape}></p><{/if}>
    <{if $xs_direction != 'theme'}><p class="xs-preview-notice"><{$smarty.const._MD_XSTYLES_DIRECTION_NOTE|escape}></p><{/if}>
    <{if !$xs_preview_frame|default:false && !$xs_admin|default:false}>
    <{if $xs_themes}>
    <div class="xs-theme-quick">
        <label for="xs-theme"><{$smarty.const._MD_XSTYLES_THEME|escape}></label>
        <select id="xs-theme" name="xs_theme" form="xs-preview-settings">
            <{foreach from=$xs_themes item=xs_theme_option}>
                <option value="<{$xs_theme_option|escape}>"<{if $xs_theme_option == $xs_theme}> selected<{/if}>><{$xs_theme_option|escape}></option>
            <{/foreach}>
        </select>
        <button type="submit" form="xs-preview-settings"><{$smarty.const._MD_XSTYLES_APPLY|escape}></button>
    </div>
    <{/if}>
    <details class="xs-controls">
        <summary><{$smarty.const._MD_XSTYLES_PREVIEW_CONTROLS|escape}></summary>
        <form id="xs-preview-settings" class="xs-control-form" action="<{$xs_module_url|escape}>/index.php" method="get">
            <input type="hidden" name="section" value="<{$xs_section|escape}>">
            <label for="xs-renderer"><{$smarty.const._MD_XSTYLES_FORM_RENDERER|escape}></label>
            <select id="xs-renderer" name="renderer">
                <option value="theme"<{if $xs_form_renderer|default:'theme' == 'theme'}> selected<{/if}>><{$smarty.const._MD_XSTYLES_THEME_DEFAULT|escape}></option>
                <option value="legacy"<{if $xs_form_renderer|default:'theme' == 'legacy'}> selected<{/if}>>Legacy</option>
                <option value="bootstrap5"<{if $xs_form_renderer|default:'theme' == 'bootstrap5'}> selected<{/if}>>Bootstrap 5</option>
            </select>
            <label for="xs-mode"><{$smarty.const._MD_XSTYLES_MODE|escape}></label>
            <select id="xs-mode" name="mode">
                <option value="theme"<{if $xs_mode == 'theme'}> selected<{/if}>><{$smarty.const._MD_XSTYLES_THEME_DEFAULT|escape}></option>
                <option value="light"<{if $xs_mode == 'light'}> selected<{/if}>><{$smarty.const._MD_XSTYLES_LIGHT|escape}></option>
                <option value="dark"<{if $xs_mode == 'dark'}> selected<{/if}>><{$smarty.const._MD_XSTYLES_DARK|escape}></option>
                <option value="auto"<{if $xs_mode == 'auto'}> selected<{/if}>><{$smarty.const._MD_XSTYLES_AUTO|escape}></option>
            </select>
            <label for="xs-direction"><{$smarty.const._MD_XSTYLES_DIRECTION_PREVIEW|escape}></label>
            <select id="xs-direction" name="direction">
                <option value="theme"<{if $xs_direction == 'theme'}> selected<{/if}>><{$smarty.const._MD_XSTYLES_THEME_DEFAULT|escape}></option>
                <option value="ltr"<{if $xs_direction == 'ltr'}> selected<{/if}>>LTR</option>
                <option value="rtl"<{if $xs_direction == 'rtl'}> selected<{/if}>>RTL</option>
            </select>
            <label for="xs-zones"><{$smarty.const._MD_XSTYLES_ZONES|escape}></label>
            <select id="xs-zones" name="zones">
                <option value="off"<{if $xs_zones == 'off'}> selected<{/if}>><{$smarty.const._MD_XSTYLES_ZONES_OFF|escape}></option>
                <option value="all"<{if $xs_zones == 'all'}> selected<{/if}>><{$smarty.const._MD_XSTYLES_ZONES_ALL|escape}></option>
                <option value="untitled"<{if $xs_zones == 'untitled'}> selected<{/if}>><{$smarty.const._MD_XSTYLES_ZONES_UNTITLED|escape}></option>
                <option value="empty"<{if $xs_zones == 'empty'}> selected<{/if}>><{$smarty.const._MD_XSTYLES_ZONES_EMPTY|escape}></option>
            </select>
            <button type="submit"><{$smarty.const._MD_XSTYLES_APPLY|escape}></button>
        </form>
        <p><{$smarty.const._MD_XSTYLES_PREVIEW_NOTE|escape}></p>
        <p><{$smarty.const._MD_XSTYLES_RENDERER_NOTE|escape}></p>
        <noscript><p><{$smarty.const._MD_XSTYLES_JS_NOTE|escape}></p></noscript>
    </details>
    <details class="xs-responsive" data-xs-responsive data-xs-preview-url="<{$xs_preview_url|default:''|escape}>"
             data-xs-frame-title="<{$smarty.const._MD_XSTYLES_VIEWPORT|escape}>"
             data-xs-frame-loading="<{$smarty.const._MD_XSTYLES_FRAME_LOADING|escape}>"
             data-xs-frame-ready="<{$smarty.const._MD_XSTYLES_FRAME_READY|escape}>"
             data-xs-frame-error="<{$smarty.const._MD_XSTYLES_FRAME_ERROR|escape}>">
        <summary><{$smarty.const._MD_XSTYLES_RESPONSIVE|escape}></summary>
        <p><{$smarty.const._MD_XSTYLES_RESPONSIVE_NOTE|escape}></p>
        <label for="xs-width"><{$smarty.const._MD_XSTYLES_VIEWPORT|escape}></label>
        <select id="xs-width" data-xs-width>
            <option value="360">360 px</option><option value="768">768 px</option><option value="1280">1280 px</option>
            <option value="all"><{$smarty.const._MD_XSTYLES_COMPARE_WIDTHS|escape}></option>
        </select>
        <button type="button" data-xs-refresh><{$smarty.const._MD_XSTYLES_REFRESH_PREVIEWS|escape}></button>
        <a href="<{$xs_standalone_url|default:$xs_page_url|escape}>" target="_blank" rel="noopener"><{$smarty.const._MD_XSTYLES_OPEN_PREVIEW|escape}></a>
        <div class="xs-viewport-tray" data-xs-frames></div>
    </details>
    <{/if}>
    </div>
    <{if !$xs_preview_frame|default:false && $xs_themes|default:[]}>
    <details class="xs-comparison" data-xs-compare data-xs-preview-url="<{$xs_preview_url|escape}>" data-xs-active-theme="<{$xs_active_theme|default:''|escape}>"
             data-xs-frame-loading="<{$smarty.const._MD_XSTYLES_FRAME_LOADING|escape}>"
             data-xs-frame-ready="<{$smarty.const._MD_XSTYLES_FRAME_READY|escape}>"
             data-xs-frame-error="<{$smarty.const._MD_XSTYLES_FRAME_ERROR|escape}>">
        <summary><{$smarty.const._MD_XSTYLES_COMPARE_THEMES|escape}></summary>
        <p><{$smarty.const._MD_XSTYLES_COMPARE_NOTE|escape}></p>
        <div class="xs-compare-controls">
            <label><{$smarty.const._MD_XSTYLES_SECTIONS|escape}>
                <select data-xs-compare-section><{foreach from=$xs_navigation item=xs_link}><option value="<{$xs_link.id|escape}>"<{if $xs_section == $xs_link.id}> selected<{/if}>><{$xs_link.title|escape}></option><{/foreach}></select>
            </label>
            <label><{$smarty.const._MD_XSTYLES_VIEWPORT|escape}>
                <select data-xs-compare-width><option value="360">360 px</option><option value="768" selected>768 px</option><option value="1280">1280 px</option></select>
            </label>
            <button type="button" data-xs-compare-refresh><{$smarty.const._MD_XSTYLES_REFRESH_PREVIEWS|escape}></button>
        </div>
        <div class="xs-compare-panes">
            <{foreach from=[0, 1] item=xs_pane}>
            <figure data-xs-compare-pane>
                <figcaption>
                    <label class="xs-compare-theme"><{if $xs_pane == 0}><{$smarty.const._MD_XSTYLES_FIRST_THEME|escape}><{else}><{$smarty.const._MD_XSTYLES_SECOND_THEME|escape}><{/if}>
                        <select data-xs-compare-theme="<{$xs_pane}>"><{foreach from=$xs_themes item=xs_option}><option value="<{$xs_option|escape}>"<{if $xs_pane == 0 && $xs_option == $xs_theme}> selected<{/if}>><{$xs_option|escape}></option><{/foreach}></select>
                    </label>
                    <div class="xs-compare-caption"><strong data-xs-compare-caption></strong> <a href="<{$xs_standalone_url|default:$xs_page_url|escape}>" target="_blank" rel="noopener" data-xs-compare-open><{$smarty.const._MD_XSTYLES_OPEN_PREVIEW|escape}></a></div>
                    <span role="status" data-xs-compare-status></span>
                </figcaption>
                <div class="xs-compare-scroll" data-xs-compare-frame></div>
            </figure>
            <{/foreach}>
        </div>
        <noscript><p><{$smarty.const._MD_XSTYLES_JS_NOTE|escape}></p></noscript>
    </details>
    <{/if}>
    <div class="xs-examples-container">
    <{if $xs_section == 'bootstrap5'}><p><{$smarty.const._MD_XSTYLES_BS5_EXPECTS|escape}></p><{/if}>
    <{if $xs_section == 'bootstrap6'}><p><{$smarty.const._MD_XSTYLES_BS6_EXPECTS|escape}></p><{/if}>
    <{if $xs_form_renderer|default:'theme' != 'theme'}><p class="xs-preview-notice"><{$smarty.const._MD_XSTYLES_FORM_RENDERER|escape}>: <code><{$xs_form_renderer|escape}></code></p><{/if}>
    <{if $xs_zones != 'off'}>
        <aside class="xs-zone-notice">
            <p><strong><{$smarty.const._MD_XSTYLES_ZONE_NOTICE|escape}></strong></p>
            <p><{$smarty.const._MD_XSTYLES_ZONE_LIMIT|escape}></p>
            <ul class="xs-zone-list"><{foreach from=$xs_zone_names item=xs_zone_name}><li><code><{$xs_zone_name|escape}></code></li><{/foreach}></ul>
        </aside>
    <{/if}>
    <{if $xs_holmes_links}>
        <nav class="xs-holmes" aria-label="cssHolmes">
            <{foreach from=$xs_holmes_links item=xs_holmes_link}><a href="<{$xs_holmes_link.url|escape}>"><{$xs_holmes_link.title|escape}></a><{/foreach}>
        </nav>
    <{/if}>
    <p class="xs-demo-notice"><{$smarty.const._MD_XSTYLES_DEMO_NOTICE|escape}></p>
    <{if $cssholmes_designer_available|default:false}>
        <p><button type="button" data-cssholmes-designer><{$smarty.const._MD_XSTYLES_OPEN_DESIGNER|escape}></button></p>
    <{/if}>
    <p class="xs-status" role="status" aria-live="polite"></p>
    <{if $xs_theme_errors|default:[]}>
        <aside class="xs-known-issue"><p><{$smarty.const._MD_XSTYLES_THEME_SPECIMEN_ERROR|escape}></p>
            <ul><{foreach from=$xs_theme_errors item=xs_error}><li><{$xs_error|escape}></li><{/foreach}></ul>
        </aside>
    <{/if}>
    <{if $xs_section == 'theme' && !$xs_samples}><p><{$smarty.const._MD_XSTYLES_THEME_SPECIMEN_EMPTY|escape}></p><{/if}>
    <{foreach from=$xs_samples item=xs_sample}>
        <{include file="db:xstyles_example.tpl"}>
    <{/foreach}>
    </div>
    </div>
    </div>
</div>
