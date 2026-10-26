<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    exit('CLI only');
}
require __DIR__ . '/../class/Gallery.php';
use XoopsModules\Xstyles\Gallery;

$state = Gallery::previewState(['section' => 'forms', 'renderer' => 'bootstrap5', 'mode' => 'dark', 'direction' => 'rtl', 'zones' => 'all', 'xoops_theme_select' => 'other', 'token' => 'secret', 'url' => 'https://example.invalid']);
if ($state !== ['section' => 'forms', 'zones' => 'all', 'mode' => 'dark', 'direction' => 'rtl', 'renderer' => 'bootstrap5', 'preview_frame' => '1']) {
    throw new RuntimeException('Preview state must preserve only validated request previews');
}
$invalid = Gallery::previewState(['renderer' => ['legacy'], 'mode' => 'invalid', 'section' => '../private']);
if ($invalid['renderer'] !== 'theme' || $invalid['mode'] !== 'theme' || $invalid['section'] !== 'all') {
    throw new RuntimeException('Malformed preview values must fall back safely');
}
echo "Preview state: passed.\n";
