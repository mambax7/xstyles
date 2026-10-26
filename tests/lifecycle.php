<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit('CLI only');
}

// Run with: php -d zend.assertions=1 -d assert.exception=1 tests/lifecycle.php
if ((int) ini_get('zend.assertions') !== 1) {
    throw new RuntimeException('Run this test with -d zend.assertions=1.');
}

final class XoopsModule
{
    public function getInfo(string $name): mixed
    {
        return ['tables' => [], 'min_xoops' => '2.7.0', 'min_php' => '8.2'][$name] ?? false;
    }

    public function getVar(string $name, string $format = 's'): mixed
    {
        return ['dirname' => 'xstyles', 'mid' => 42][$name] ?? null;
    }
}

final class XoopsDatabaseFactory
{
    public static function getDatabaseConnection(): never
    {
        throw new RuntimeException('Stateless lifecycle callbacks must not request a database.');
    }
}

$root = sys_get_temp_dir() . '/xstyles-lifecycle-' . bin2hex(random_bytes(8));
mkdir($root . '/modules/xstyles/templates', 0777, true);
mkdir($root . '/uploads', 0777, true);
file_put_contents($root . '/modules/xstyles/templates/historical.html', 'Keep this template.');
define('XOOPS_ROOT_PATH', $root);
define('XOOPS_UPLOAD_PATH', $root . '/uploads');
define('XOOPS_VERSION', 'XOOPS 2.8.0-Alpha1');


$snapshot = static function () use ($root): array {
    $files = [];
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $file) {
        $files[substr($file->getPathname(), strlen($root))] = $file->isDir() ? 'directory' : hash_file('sha256', $file->getPathname());
    }
    ksort($files);

    return $files;
};

try {
    $before = $snapshot();
    require __DIR__ . '/../include/oninstall.php';
    require __DIR__ . '/../include/onupdate.php';
    require __DIR__ . '/../include/onuninstall.php';
    $module = new XoopsModule();
    $callbacks = [
        'xoops_module_pre_install_xstyles',
        'xoops_module_install_xstyles',
        'xoops_module_pre_update_xstyles',
        'xoops_module_update_xstyles',
        'xoops_module_pre_uninstall_xstyles',
        'xoops_module_uninstall_xstyles',
    ];
    foreach ($callbacks as $callback) {
        assert($callback($module), $callback . ' should succeed.');
        assert($snapshot() === $before, $callback . ' must leave files and directories untouched.');
    }
    mkdir($root . '/uploads/xstyles');
    file_put_contents($root . '/uploads/xstyles/historical.txt', 'Keep historical uploads.');
    $before = $snapshot();
    foreach ($callbacks as $callback) {
        assert($callback($module));
        assert($snapshot() === $before, $callback . ' must preserve historical uploads.');
    }
    echo "Lifecycle checks passed.\n";
} finally {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($root);
}
