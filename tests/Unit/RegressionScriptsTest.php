<?php declare(strict_types=1);

namespace XoopsModules\Xstyles\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsModules\Xstyles\Tests\Support\ScriptTestCase;

final class RegressionScriptsTest extends ScriptTestCase
{
    /** @return iterable<string, array{string}> */
    public static function scripts(): iterable
    {
        foreach (['gallery.php', 'preview.php', 'markup.php', 'theme-specimens.php', 'lifecycle.php'] as $script) {
            yield $script => [$script];
        }
    }

    #[DataProvider('scripts')]
    public function testStandaloneRegression(string $script): void
    {
        $this->runScript($script);
    }
}
