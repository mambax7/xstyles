<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    exit('CLI only');
}
require __DIR__ . '/../class/ThemeSpecimens.php';
use XoopsModules\Xstyles\ThemeSpecimens;

$root = sys_get_temp_dir() . '/xstyles-specimens-' . bin2hex(random_bytes(8));
mkdir($root . '/modules/xstyles/specimens', 0777, true);
$manifest = $root . '/modules/xstyles/specimens.json';
file_put_contents($root . '/modules/xstyles/specimens/card.tpl', '<p>Example</p>');
$check = static function (bool $condition, string $message): void {
    if (! $condition) {
        throw new RuntimeException($message);
    }
};

try {
    $check(ThemeSpecimens::load($root)['samples'] === [], 'Absent descriptor is optional');
    $data = ['version' => 1, 'modeAttributes' => ['data-my-mode'], 'specimens' => [
        ['id' => 'card', 'title' => '<b>Card</b>', 'selector' => '.card', 'template' => 'specimens/card.tpl'],
    ]];
    file_put_contents($manifest, json_encode($data));
    $valid = ThemeSpecimens::load($root);
    $check(count($valid['samples']) === 1 && $valid['errors'] === [], 'Valid descriptor');
    $check($valid['samples'][0]['title'] === '<b>Card</b>', 'Keep metadata raw for one escape at display');
    $check($valid['modeAttributes'] === ['data-my-mode'], 'Explicit mode mapping');
    foreach (['../secret.tpl', 'file:specimens/card.tpl', 'https://example.invalid/card.tpl', 'specimens/../../secret.tpl', 'specimens/card.php', 'specimens/missing.tpl'] as $path) {
        $bad = $data;
        $bad['specimens'][0]['template'] = $path;
        file_put_contents($manifest, json_encode($bad));
        $result = ThemeSpecimens::load($root);
        $check($result['samples'] === [] && $result['errors'] !== [], 'Reject path: ' . $path);
    }
    $bad = $data;
    $bad['specimens'][] = $bad['specimens'][0];
    file_put_contents($manifest, json_encode($bad));
    $check(count(ThemeSpecimens::load($root)['samples']) === 1 && ThemeSpecimens::load($root)['errors'] !== [], 'Duplicate IDs');
    foreach (['{', str_repeat(' ', 65537), json_encode(['version' => 2]), json_encode(['version' => 1, 'modeAttributes' => ['onclick'], 'specimens' => []])] as $badJson) {
        file_put_contents($manifest, $badJson);
        $check(ThemeSpecimens::load($root)['errors'] !== [], 'Invalid descriptor must be visible');
    }
    echo "Theme descriptor: optional, metadata, explicit modes, traversal/URL/extension/missing paths, duplicates and bounds passed.\n";
} finally {
    unlink($root . '/modules/xstyles/specimens/card.tpl');
    if (is_file($manifest)) {
        unlink($manifest);
    }
    rmdir($root . '/modules/xstyles/specimens');
    rmdir($root . '/modules/xstyles');
    rmdir($root . '/modules');
    rmdir($root);
}
