<?php declare(strict_types=1);

namespace XoopsModules\Xstyles\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsModules\Xstyles\Tests\Support\ScriptTestCase;

final class CoreScriptsTest extends ScriptTestCase
{
    /** @return iterable<string, array{string}> */
    public static function scripts(): iterable
    {
        foreach (['fixtures.php', 'admin-gallery.php', 'admin-home.php', 'admin-theme-loader.php'] as $script) {
            yield $script => [$script];
        }
    }

    #[DataProvider('scripts')]
    public function testCoreRegression(string $script): void
    {
        $core = getenv('XSTYLES_CORE');
        if ($core === false || $core === '') {
            self::markTestSkipped('Set XSTYLES_CORE to a compatible XOOPS core tree for isolated fixture tests.');
        }
        self::assertFileExists($core . '/class/template.php');
        self::assertFileExists($core . '/xoops_lib/vendor/autoload.php');
        $this->runScript($script, [$core]);
    }
}
