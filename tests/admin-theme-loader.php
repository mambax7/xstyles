<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    exit('CLI only');
}
ob_start();
require __DIR__ . '/fixtures.php';
ob_end_clean();
require_once XOOPS_ROOT_PATH . '/modules/system/class/cpanel.php';
// Avoid the constructor's database lookup; exercise the production catalog and GUI loader.
$panel = (new ReflectionClass(XoopsSystemCpanel::class))->newInstanceWithoutConstructor();
$before = $GLOBALS['xoopsConfig'];
$_SESSION = ['xoopsUserTheme' => 'xbootstrap5'];
$themes = XoopsSystemCpanel::getGuis();
check(count($themes) >= 2, 'At least two installed admin themes required');
foreach ($themes as $theme) {
    $panel->loadGui($theme);
    check($panel->gui->foldername === $theme, 'Admin GUI failed to load ' . $theme);
    check($GLOBALS['xoopsConfig'] === $before, 'GUI selection changed config');
    check($_SESSION === ['xoopsUserTheme' => 'xbootstrap5'], 'GUI selection changed visitor theme');
}
// Theme discovery must not load another theme's constants before the selected theme renders.
$localization = new xos_opal_Theme();
$localization->path = XOOPS_ADMINTHEME_PATH . '/default';
$localization->addLanguage('main', 'english');
$localization->addLanguage('admin', 'english');
check(defined('_OXYGEN_SYSOPTIONS'), 'Selected admin theme language did not load');
echo 'Actual admin GUI catalog/loading without persistence: ' . implode(', ', $themes) . ".\n";
