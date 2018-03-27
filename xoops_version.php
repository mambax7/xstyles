<?php

include __DIR__ . '/preloads/autoloader.php';
$moduleDirName = basename(__DIR__);

$modversion['version']             = 1.03;
$modversion['release_date']        = '2018/03/25';
$modversion['module_status']       = 'Beta 1';
$modversion['name']                = 'xstyles';
$modversion['description']         = 'Faux module Xoops Documentation';
$modversion['credits']             = 'Chewb - Christian - Dugris - Kris_fr';
$modversion['author']              = 'Burning & Xoops Design Team';
$modversion['help']                = 'page=help';
$modversion['license']             = 'GPL see LICENSE';
$modversion['official']            = 0;
$modversion['image']               = 'assets/images/logoModule.png';
$modversion['dirname']             = $moduleDirName;
$modversion['modicons16']          = 'assets/images/icons/16';
$modversion['modicons32']          = 'assets/images/icons/32';
$modversion['image']               = 'assets/images/logoModule.png';
$modversion['module_website_url']  = 'https://xoops.org';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php']             = '5.5';
$modversion['min_xoops']           = '2.5.9';
$modversion['min_admin']           = '1.2';
$modversion['min_db']              = ['mysql' => '5.5'];
$modversion['system_menu']         = 1;
$modversion['hasAdmin']            = 1;
$modversion['adminindex']          = 'admin/index.php';
$modversion['adminmenu']           = 'admin/menu.php';
$modversion['hasMain']             = 1;
//Install/Uninstall Functions
$modversion['onInstall']   = 'include/oninstall.php';
$modversion['onUpdate']    = 'include/onupdate.php';
$modversion['onUninstall'] = 'include/onuninstall.php';

// ------------------- Help files ------------------- //
$modversion['helpsection'] = [
    ['name' => _MI_XSTYLES_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_XSTYLES_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_XSTYLES_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_XSTYLES_SUPPORT, 'link' => 'page=support'],
];
