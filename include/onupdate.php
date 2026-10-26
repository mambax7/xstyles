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
 * @copyright    {@link https://xoops.org/ XOOPS Project}
 * @license      {@link https://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @author       XOOPS Development Team
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

// Historical templates, uploads, and permissions remain untouched on update.
function xoops_module_pre_update_xstyles(\XoopsModule $module): bool
{
    return true;
}

function xoops_module_update_xstyles(\XoopsModule $module, int|string|null $previousVersion = null): bool
{
    return true;
}
