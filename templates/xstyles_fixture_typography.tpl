<{if $xs_fixture_kind == 'headings'}>
<h1><{$smarty.const._MD_XSTYLES_HEADING_ONE|escape}></h1>
<p><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p>
<h2><{$smarty.const._MD_XSTYLES_HEADING_TWO|escape}></h2>
<p><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p>
<h3><{$smarty.const._MD_XSTYLES_HEADING_THREE|escape}></h3>
<p><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p>
<h4><{$smarty.const._MD_XSTYLES_HEADING_FOUR|escape}></h4>
<p><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p>
<h5><{$smarty.const._MD_XSTYLES_HEADING_FIVE|escape}></h5>
<p><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p>
<h6><{$smarty.const._MD_XSTYLES_HEADING_SIX|escape}></h6>
<p><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p>
<{elseif $xs_fixture_kind == 'inline'}>
<p><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p>
<p><strong><{$smarty.const._MD_XSTYLES_STRONG|escape}></strong>, <em><{$smarty.const._MD_XSTYLES_EMPHASIS|escape}></em>, <small><{$smarty.const._MD_XSTYLES_SMALL|escape}></small>, <mark><{$smarty.const._MD_XSTYLES_MARKED|escape}></mark>, <del><{$smarty.const._MD_XSTYLES_DELETED|escape}></del>, <ins><{$smarty.const._MD_XSTYLES_INSERTED|escape}></ins>.</p>
<p><a href="#xs-inline"><{$smarty.const._MD_XSTYLES_LINK|escape}></a> · <abbr title="Content Management System">CMS</abbr> · <code>$theme = 'XOOPS';</code> · <kbd>Ctrl</kbd> + <kbd>S</kbd> · H<sub>2</sub>O · m<sup>2</sup></p>
<hr>
<address><{$smarty.const._MD_XSTYLES_SAMPLE_NAME|escape}><br><{$smarty.const._MD_XSTYLES_SAMPLE_LOCATION|escape}></address>
<{elseif $xs_fixture_kind == 'lists'}>
<ul><li><{$smarty.const._MD_XSTYLES_NEWS|escape}><ul><li><{$smarty.const._MD_XSTYLES_EVENTS|escape}></li><li><{$smarty.const._MD_XSTYLES_MEMBERS|escape}></li></ul></li><li><{$smarty.const._MD_XSTYLES_PUBLISHED|escape}></li></ul>
<ol><li><{$smarty.const._MD_XSTYLES_DRAFT|escape}></li><li><{$smarty.const._MD_XSTYLES_PREVIEW|escape}></li><li><{$smarty.const._MD_XSTYLES_PUBLISHED|escape}></li></ol>
<dl><dt><{$smarty.const._MD_XSTYLES_THEME_TERM|escape}></dt><dd><{$smarty.const._MD_XSTYLES_THEME_DEFINITION|escape}></dd><dt><{$smarty.const._MD_XSTYLES_MODULE_TERM|escape}></dt><dd><{$smarty.const._MD_XSTYLES_MODULE_DEFINITION|escape}></dd></dl>
<{else}>
<h3><{$smarty.const._MD_XSTYLES_LONG_TITLE|escape}></h3>
<p><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}> <{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}> <{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></p>
<p lang="fr">Éléonore prépare une communauté accueillante — déjà, Noël et l’été se rencontrent.</p>
<p lang="ja">読みやすい文字と余白。世界中のコミュニティをつなぐ。</p>
<p lang="ar" dir="rtl">مجتمع مفتوح للجميع <bdi>XOOPS 2.8</bdi> — مرحباً بكم.</p>
<p><code>theme-preview-with-an-intentionally-long-unbroken-identifier-0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ</code></p>
<p><a href="#xs-stress">https://example.invalid/community/long-path/typography-and-layout-preview?sample=0123456789abcdefghijklmnopqrstuvwxyz</a></p>
<{/if}>
