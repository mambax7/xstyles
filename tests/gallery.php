<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit('CLI only');
}

require_once __DIR__ . '/../class/Gallery.php';

use XoopsModules\Xstyles\Gallery;

$checks = 0;
$check = static function (bool $condition, string $message) use (&$checks): void {
    ++$checks;
    if (! $condition) {
        throw new RuntimeException($message);
    }
};

$check(count(Gallery::sections()) === 9, 'Nine independently addressable sections');
foreach (array_keys(Gallery::sections()) as $section) {
    $check(Gallery::section($section) === $section, 'Known section survives');
}
foreach ([null, [], ['forms'], new stdClass(), '../../mainfile.php', '<script>', 'FORMS', 1] as $invalid) {
    $check(Gallery::section($invalid) === 'all', 'Invalid section falls back safely');
    $check(Gallery::zoneMode($invalid) === 'off', 'Invalid zone mode is off');
}
$check(Gallery::zoneMode('1') === 'all', 'Simple zones=1 link supported');
$check(Gallery::zoneMode('empty') === 'empty', 'Empty-zone collapse can be tested');
$check(Gallery::choice(['rtl'], ['theme', 'ltr', 'rtl'], 'theme') === 'theme', 'Array query rejected');
$check(Gallery::choice('rtl', ['theme', 'ltr', 'rtl'], 'theme') === 'rtl', 'Allowed preview selected');
$check(count(Gallery::zoneNames()) === 12, 'All local core zones represented');
$blocks = Gallery::zoneBlocks('all', 'Sample', '<p>Fixed content</p>');
$check(count($blocks) === 12, 'Each zone populated');
$ids = [];
foreach ($blocks as $name => $zone) {
    $check(count($zone) === 1 && str_contains($zone[0]['title'], $name), 'Caption identifies its zone');
    $check(str_starts_with($zone[0]['content'], '<p>Fixed content</p>'), 'Fixed content preserved');
    $ids[] = $zone[0]['id'];
}
$check(count(array_unique($ids)) === 12, 'Synthetic block IDs distinct');
foreach (Gallery::zoneBlocks('untitled', 'Sample', '') as $zone) {
    $check($zone[0]['title'] === '', 'Untitled specimens have no title');
}
foreach (Gallery::zoneBlocks('empty', '', '') as $zone) {
    $check($zone === [], 'Empty preview clears all request zones');
}
$check(Gallery::zoneBlocks('off', '', '') === [], 'Off does not replace host blocks');
echo "Gallery: {$checks} checks passed.\n";
