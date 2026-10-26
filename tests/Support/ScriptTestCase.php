<?php declare(strict_types=1);

namespace XoopsModules\Xstyles\Tests\Support;

use PHPUnit\Framework\TestCase;

abstract class ScriptTestCase extends TestCase
{
    /** @param list<string> $arguments */
    protected function runScript(string $script, array $arguments = []): void
    {
        $root = dirname(__DIR__, 2);
        $process = proc_open(
            [PHP_BINARY, '-d', 'zend.assertions=1', '-d', 'assert.exception=1', $root . '/tests/' . $script, ...$arguments],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['redirect', 1]],
            $pipes,
            $root,
        );
        self::assertIsResource($process);
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        self::assertSame(0, proc_close($process), $script . ': ' . $output);
    }
}
