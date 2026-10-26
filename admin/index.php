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

require_once __DIR__ . '/admin_header.php';

xoops_cp_header();
$adminObject->displayNavigation(basename(__FILE__));

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
echo '<h2>' . $escape(_AM_XSTYLES_OVERVIEW) . '</h2>';
echo '<p>' . $escape(_AM_XSTYLES_OVERVIEW_DESC) . '</p>';

$xoTheme->addStylesheet($helper->url('assets/css/admin-home.css'));
echo '<div id="xs-admin-home">';
$adminObject->displayIndex();
echo '</div>';
$adminObject->addInfoBox($escape(_AM_XSTYLES_SERVER_STATUS));
foreach ([
    _AM_XSTYLES_GD => function_exists('gd_info') ? _AM_XSTYLES_ENABLED : _AM_XSTYLES_DISABLED,
    _AM_XSTYLES_UPLOADS => ini_get('file_uploads') ? _AM_XSTYLES_ENABLED : _AM_XSTYLES_DISABLED,
    _AM_XSTYLES_UPLOAD_LIMIT => (string) ini_get('upload_max_filesize'),
    _AM_XSTYLES_POST_LIMIT => (string) ini_get('post_max_size'),
    _AM_XSTYLES_MEMORY_LIMIT => ini_get('memory_limit'),
    _AM_XSTYLES_SERVER_PATH => XOOPS_ROOT_PATH,
] as $label => $value) {
    $adminObject->addInfoBoxLine($escape($label) . ': <strong>' . $escape($value) . '</strong>', 'information');
}
$adminObject->displayInfoBox();

require_once __DIR__ . '/admin_footer.php';
