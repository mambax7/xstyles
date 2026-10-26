<?php
declare(strict_types=1);

use XoopsModules\Xstyles\Fixtures;
use XoopsModules\Xstyles\Gallery;

require_once __DIR__ . '/admin_header.php';
// The core admin bootstrap checks module_admin before any specimens are rendered.
xoops_load('cpanel', 'system');
$cpanel = \XoopsSystemCpanel::getInstance();
$activeAdminTheme = (string) $cpanel->gui->foldername;
$adminThemes = array_values(\XoopsSystemCpanel::getGuis());
$previewTheme = Gallery::choice(\Xmf\Request::getVar('xs_theme', null, 'GET'), $adminThemes, '');
$previewFrame = \Xmf\Request::getVar('preview_frame', null, 'GET') === '1';
if (! defined('XSTYLES_PREVIEW_FRAME')) {
    define('XSTYLES_PREVIEW_FRAME', $previewFrame);
}
if ($previewTheme !== '') {
    $cpanel->loadGui($previewTheme);
}
xoops_cp_header();
$adminObject->displayNavigation(basename(__FILE__));
$moduleUrl = rtrim($helper->url(), '/') . '/';
$pageUrl = $moduleUrl . 'admin/gallery.php';
$sections = array_intersect_key(Gallery::sections(), array_flip(['typography', 'content', 'tables', 'forms', 'comments']));
$section = Gallery::choice(\Xmf\Request::getVar('section', null, 'GET'), array_keys($sections), 'all');
$state = ['section' => $section];
if ($previewTheme !== '') {
    $state['xs_theme'] = $previewTheme;
}
if ($previewFrame) {
    $state['preview_frame'] = '1';
}
$themeLinks = [];
foreach ($adminThemes as $theme) {
    $themeLinks[] = ['id' => $theme, 'url' => $pageUrl . '?' . http_build_query(array_replace($state, ['xs_theme' => $theme]))];
}
$navigation = [];
foreach (['all' => '_MD_XSTYLES_ALL'] + $sections as $key => $label) {
    $navigation[] = ['id' => $key, 'title' => constant($label), 'url' => $pageUrl . '?' . http_build_query(array_replace($state, ['section' => $key]))];
}
$samples = [];
foreach ($section === 'all' ? array_keys($sections) : [$section] as $group) {
    $samples = array_merge($samples, Fixtures::build($group, $xoopsTpl, rtrim($moduleUrl, '/')));
}
foreach ($samples as &$sample) {
    $sample['url'] = $pageUrl . '?' . http_build_query(array_replace($state, ['section' => $sample['section']])) . '#' . $sample['id'];
    // Keep native form fallbacks in this read-only admin page, preserving specimen asset URLs.
    $sample['html'] = str_replace($moduleUrl . 'index.php', $pageUrl, $sample['html']);
}
unset($sample);
xoops_load('XoopsFormRenderer');
$xoopsTpl->assign([
    'xs_admin' => true, 'xs_section' => $section, 'xs_navigation' => $navigation, 'xs_samples' => $samples,
    'xs_module_url' => rtrim($moduleUrl, '/'), 'xs_page_url' => $pageUrl,
    'xs_theme' => (string) $xoTheme->folderName,
    'xs_locale' => (string) $xoopsConfig['language'],
    'xs_actual_direction' => (string) ($xoopsTpl->getTemplateVars('xoops_text_direction') ?? 'ltr'),
    'xs_renderer' => \XoopsFormRenderer::getInstance()->get()::class,
    'xs_mode' => 'theme', 'xs_direction' => 'theme', 'xs_form_renderer' => 'theme',
    'xs_zones' => 'off', 'xs_zone_names' => [], 'xs_holmes_links' => [], 'xs_themes' => $adminThemes,
    'xs_preview_frame' => $previewFrame, 'xs_theme_errors' => [], 'cssholmes_designer_available' => false,
    'xs_theme_links' => $themeLinks, 'xs_active_theme' => $activeAdminTheme,
    'xs_theme_preview' => $previewTheme !== '', 'xs_reset_url' => $pageUrl . '?' . http_build_query(array_diff_key($state, ['xs_theme' => true])),
    'xs_preview_url' => $pageUrl . '?' . http_build_query(array_replace($state, ['preview_frame' => '1'])),
    'xs_standalone_url' => $pageUrl . '?' . http_build_query(array_diff_key($state, ['preview_frame' => true])),
]);
$xoTheme->addStylesheet($moduleUrl . 'assets/css/gallery.css');
$xoTheme->addScript($moduleUrl . 'assets/js/gallery.js');
$xoTheme->addScript($moduleUrl . 'assets/js/compare.js');
$xoopsTpl->display('db:xstyles_index.tpl');
require_once __DIR__ . '/admin_footer.php';
