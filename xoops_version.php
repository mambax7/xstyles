<?php declare(strict_types=1);

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

$moduleDirName = basename(__DIR__);

$modversion['version'] = '1.5.0';
$modversion['release_date'] = '2026/10/25';
$modversion['module_status'] = 'Beta 2';
$modversion['name'] = _MI_XSTYLES_NAME;
$modversion['description'] = _MI_XSTYLES_DESC;
$modversion['credits'] = 'Chewb - Christian - Dugris - Kris_fr - Mamba ';
$modversion['author'] = 'Burning & Xoops Design Team';
$modversion['help'] = 'page=help';
$modversion['license'] = 'GNU GPL 2.0 or later';
$modversion['official'] = 0;
$modversion['image'] = 'assets/images/logoModule.png';
$modversion['dirname'] = $moduleDirName;
$modversion['modicons16'] = 'assets/images/icons/16';
$modversion['modicons32'] = 'assets/images/icons/32';
$modversion['module_website_url'] = 'https://xoops.org';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php'] = '8.2';
$modversion['min_xoops'] = '2.7.0';
$modversion['min_admin'] = '1.2';
$modversion['system_menu'] = 1;
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';
$modversion['hasMain'] = 1;
$modversion['sqlfile'] = false;
$modversion['tables'] = [];
//Install/Uninstall Functions
$modversion['onInstall'] = 'include/oninstall.php';
$modversion['onUpdate'] = 'include/onupdate.php';
$modversion['onUninstall'] = 'include/onuninstall.php';

// ------------------- Help files ------------------- //
$modversion['helpsection'] = [
    ['name' => _MI_XSTYLES_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_XSTYLES_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_XSTYLES_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_XSTYLES_SUPPORT, 'link' => 'page=support'],
];

// Registered gallery templates.
$modversion['templates'] = [
    ['file' => 'xstyles_index.tpl', 'description' => 'Theme specimen gallery'],
    ['file' => 'xstyles_example.tpl', 'description' => 'Specimen frame and rendered source'],
    ['file' => 'xstyles_zone.tpl', 'description' => 'Request-only block-zone content'],
    ['file' => 'xstyles_fixture_rendered.tpl', 'description' => 'Core-rendered fixture'],
    ['file' => 'xstyles_fixture_typography.tpl', 'description' => 'Typography and stress content'],
    ['file' => 'xstyles_fixture_content.tpl', 'description' => 'Legacy article and messages'],
    ['file' => 'xstyles_fixture_tables.tpl', 'description' => 'Semantic and legacy tables'],
    ['file' => 'xstyles_fixture_comment.tpl', 'description' => 'Core comment wrapper'],
    ['file' => 'xstyles_fixture_form.tpl', 'description' => 'Plain HTML form'],
    ['file' => 'xstyles_fixture_bootstrap5.tpl', 'description' => 'Optional Bootstrap 5 components using theme assets'],
    ['file' => 'xstyles_fixture_bootstrap6.tpl', 'description' => 'Optional Bootstrap 6 Alpha components using theme assets'],
];
