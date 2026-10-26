<?php declare(strict_types=1);

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * uninstall.php - cleanup on module uninstall
 *
 * @author          XOOPS Module Development Team
 * @copyright       {@link https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@link https://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2.0 or later}
 * @link            https://xoops.org XOOPS
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

// The stateless gallery owns no persistent data or upload directory.
function xoops_module_pre_uninstall_xstyles(\XoopsModule $module): bool
{
    return true;
}

function xoops_module_uninstall_xstyles(\XoopsModule $module): bool
{
    return true;
}
