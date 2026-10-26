<?php

declare(strict_types=1);

use XoopsModules\Xstyles\Fixtures;
use XoopsModules\Xstyles\Gallery;
use XoopsModules\Xstyles\ThemeSpecimens;

require_once dirname(__DIR__, 2) . '/mainfile.php';
require_once __DIR__ . '/include/common.php';

$input = [];
foreach (['section', 'zones', 'mode', 'direction', 'renderer'] as $key) {
    $input[$key] = \Xmf\Request::getVar($key, null, 'GET');
}
$state = Gallery::state($input);
$themeConfig = function_exists('xoops_resolveThemeConfig') ? xoops_resolveThemeConfig($xoopsConfig) : $xoopsConfig;
$selectableThemes = Gallery::themes((array) ($themeConfig['theme_set_allowed'] ?? []), XOOPS_THEME_PATH);
// Older cores without a request-only module override keep their normal theme.
$canPreviewTheme = function_exists('xoops_resolveModuleTheme');
$previewTheme = $canPreviewTheme ? Gallery::choice(\Xmf\Request::getVar('xs_theme', null, 'GET'), $selectableThemes, '') : '';
if ($previewTheme !== '') {
    $state['xs_theme'] = $previewTheme;
}
$normalTheme = Gallery::choice($_SESSION['xoopsUserTheme'] ?? null, $selectableThemes, (string) $themeConfig['theme_set']);
['section' => $section, 'zones' => $zones, 'mode' => $mode, 'direction' => $direction, 'renderer' => $renderer] = $state;
$previewFrame = \Xmf\Request::getVar('preview_frame', null, 'GET') === '1';
// Set by this controller before header hooks, never by arbitrary URL paths.
define('XSTYLES_PREVIEW_FRAME', $previewFrame);
$moduleUrl = XOOPS_URL . '/modules/' . basename(__DIR__);
if ($previewFrame) {
    $state['preview_frame'] = '1';
}
$pageUrl = $moduleUrl . '/index.php?' . http_build_query($state);

// Specimens contain visitor-specific theme output and must not share a page cache.
$xoopsConfig['module_cache'][(int) $xoopsModule->getVar('mid')] = 0;
$GLOBALS['xoopsOption']['template_main'] = 'xstyles_index.tpl';
$originalModuleConfig = $xoopsModuleConfig ?? [];

try {
    if ($previewTheme !== '') {
        $xoopsModuleConfig['theme'] = $previewTheme;
    }
    include_once XOOPS_ROOT_PATH . '/header.php';
} finally {
    $xoopsModuleConfig = $originalModuleConfig;
}

$xoTheme->addStylesheet($moduleUrl . '/assets/css/gallery.css');
$xoTheme->addScript($moduleUrl . '/assets/js/gallery.js');
$xoTheme->addScript($moduleUrl . '/assets/js/bootstrap5.js');
$xoTheme->addScript($moduleUrl . '/assets/js/bootstrap6.js');
$xoTheme->addScript($moduleUrl . '/assets/js/responsive.js');
$xoTheme->addScript($moduleUrl . '/assets/js/compare.js');
$xoopsTpl->assign('xoops_pagetitle', _MD_XSTYLES_TITLE);

xoops_load('XoopsFormRenderer');
$activeTheme = (string) $xoTheme->folderName;
$themeSpecimens = ThemeSpecimens::load(XOOPS_THEME_PATH . '/' . $activeTheme);
$pageUrl = $moduleUrl . '/index.php?' . http_build_query($state);
$navigation = [];
foreach (['all' => '_MD_XSTYLES_ALL'] + Gallery::sections() as $key => $label) {
    $navigation[] = [
        'id' => $key,
        'title' => constant($label),
        'url' => $moduleUrl . '/index.php?' . http_build_query(array_replace($state, ['section' => $key])),
    ];
}

$holmesLinks = [];
$holmesModule = xoops_getHandler('module')->getByDirname('cssholmes');
if (! $previewFrame && is_object($holmesModule) && $holmesModule->getVar('isactive') && is_object($xoopsUser)
    && is_file(XOOPS_ROOT_PATH . '/modules/cssholmes/preloads/autoloader.php')) {
    $holmesId = (int) $holmesModule->getVar('mid');
    $canOverlay = $xoopsUser->isAdmin($holmesId)
        || xoops_getHandler('groupperm')->checkRight('cssholmes_overlay', 1, $xoopsUser->getGroups(), $holmesId);
    if ($canOverlay) {
        require_once XOOPS_ROOT_PATH . '/modules/cssholmes/preloads/autoloader.php';
        $queryKey = trim((string) \XoopsModules\Cssholmes\Helper::getInstance()->getConfig('holmes_query_key'));
        $queryKey = $queryKey !== '' ? $queryKey : 'holmes';
        foreach (['a11y', 'html5', 'layout'] as $profile) {
            $holmesLinks[] = [
                'title' => 'cssHolmes: ' . $profile,
                'url' => $pageUrl . '&' . http_build_query([$queryKey => $profile]),
            ];
        }
    }
}

if ($zones !== 'off') {
    $xoopsTpl->assign('xs_module_url', $moduleUrl);
    $zoneContent = $xoopsTpl->fetch('db:xstyles_zone.tpl');
    $previewBlocks = Gallery::zoneBlocks($zones, _MD_XSTYLES_ZONE_BLOCK, $zoneContent);
    $builder = $xoTheme->plugins['xos_logos_PageBuilder'] ?? null;
    if (is_object($builder)) {
        foreach ($previewBlocks as $key => $value) {
            $builder->blocks[$key] = $value;
        }
        $xoopsTpl->assignByRef('xoBlocks', $builder->blocks);
    } else {
        $xoopsTpl->assign('xoBlocks', $previewBlocks);
    }
    foreach ([
        'xoops_lblocks' => 'canvas_left', 'xoops_rblocks' => 'canvas_right',
        'xoops_clblocks' => 'page_topleft', 'xoops_ccblocks' => 'page_topcenter',
        'xoops_crblocks' => 'page_topright', 'xoops_blblocks' => 'page_bottomleft',
        'xoops_bcblocks' => 'page_bottomcenter', 'xoops_brblocks' => 'page_bottomright',
    ] as $alias => $zone) {
        $xoopsTpl->assign($alias, $previewBlocks[$zone]);
    }
    $populated = $zones !== 'empty';
    $xoopsTpl->assign('xoops_showlblock', $populated);
    $xoopsTpl->assign('xoops_showrblock', $populated);
    $xoopsTpl->assign('xoops_showcblock', $populated);
}

$samples = Fixtures::build($section, $xoopsTpl, $moduleUrl, $renderer);
$themeErrors = $themeSpecimens['errors'];
if (in_array($section, ['all', 'theme'], true)) {
    $themeOutput = Fixtures::theme($themeSpecimens['samples'], $moduleUrl, XOOPS_THEME_URL . '/' . rawurlencode($activeTheme));
    $samples = array_merge($samples, $themeOutput['samples']);
    $themeErrors = array_merge($themeErrors, $themeOutput['errors']);
}
foreach ($samples as &$sample) {
    $sample['url'] = $moduleUrl . '/index.php?'
        . http_build_query(array_replace($state, ['section' => $sample['section']])) . '#' . $sample['id'];
}
unset($sample);
$xoopsTpl->assign([
    'xs_section' => $section,
    'xs_navigation' => $navigation,
    'xs_samples' => $samples,
    'xs_module_url' => $moduleUrl,
    'xs_page_url' => $pageUrl,
    'xs_themes' => $canPreviewTheme ? $selectableThemes : [],
    'xs_active_theme' => $normalTheme,
    'xs_theme_preview' => $previewTheme !== '',
    'xs_reset_url' => $moduleUrl . '/index.php?' . http_build_query(array_diff_key($state, ['xs_theme' => true])),
    'xs_theme' => $activeTheme,
    'xs_locale' => (string) ($xoopsTpl->getTemplateVars('xoops_langcode') ?? $xoopsConfig['language']),
    'xs_actual_direction' => (string) ($xoopsTpl->getTemplateVars('xoops_text_direction') ?? 'ltr'),
    'xs_renderer' => \XoopsFormRenderer::getInstance()->get()::class,
    'xs_zones' => $zones,
    'xs_zone_names' => Gallery::zoneNames(),
    'xs_mode' => $mode,
    'xs_mode_attributes' => implode(' ', $themeSpecimens['modeAttributes']),
    'xs_theme_errors' => $themeErrors,
    'xs_direction' => $direction,
    'xs_form_renderer' => $renderer,
    'xs_preview_frame' => $previewFrame,
    'xs_preview_url' => $moduleUrl . '/index.php?' . http_build_query(Gallery::previewState($state, $selectableThemes)),
    'xs_standalone_url' => $moduleUrl . '/index.php?' . http_build_query(array_diff_key($state, ['preview_frame' => true])),
    'xs_holmes_links' => $holmesLinks,
]);

include_once XOOPS_ROOT_PATH . '/footer.php';
