<?php declare(strict_types=1);
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    2000-2026 XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author       XOOPS Development Team
 */

use XoopsModules\Xstyles\Helper;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

require_once __DIR__ . '/../preloads/autoloader.php';

$moduleDirName = basename(dirname(__DIR__));
$helper = Helper::getInstance();
$helper->loadLanguage('main');
$helper->loadLanguage('fixtures');
$helper->loadLanguage('bootstrap5');
