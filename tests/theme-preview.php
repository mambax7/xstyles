<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    exit('CLI only');
}
require __DIR__ . '/../class/Gallery.php';
use XoopsModules\Xstyles\Gallery;

$check = static function (bool $ok, string $message): void {
    if (! $ok) {
        throw new RuntimeException($message);
    }
};
$themes = Gallery::themes(['xbootstrap5', 'mytheme6', '../default', ['default'], 'missing', 'xbootstrap5'], dirname(__DIR__, 3) . '/themes');
$check($themes === ['xbootstrap5', 'mytheme6'], 'Only installed, unique, safe allowed themes');
foreach ([null, [], '../default', 'missing', 'default'] as $invalid) {
    $check(Gallery::choice($invalid, $themes, '') === '', 'Reject unavailable preview');
}
$state = Gallery::previewState(['section' => 'forms', 'xs_theme' => 'mytheme6', 'xoops_theme_select' => 'default', 'holmes' => 'layout'], $themes);
$check(($state['xs_theme'] ?? '') === 'mytheme6', 'Keep validated request-only theme in frames');
$check(! isset($state['xoops_theme_select'], $state['holmes']), 'Frames must not select a session theme or enable editing');
$check(! isset(Gallery::previewState(['xs_theme' => '../default'], $themes)['xs_theme']), 'Reject unsafe frame theme');
define('XOOPS_ROOT_PATH', dirname(__DIR__, 3));
require XOOPS_ROOT_PATH . '/include/theme_config.php';
$config = ['theme' => 'mytheme6'];
$check(xoops_resolveModuleTheme($config, false, dirname(__DIR__, 3) . '/themes') === 'mytheme6', 'Core accepts module preview theme');
echo "Theme preview: allowlist, installed themes, frame state and core request override passed.\n";
