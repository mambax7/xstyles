<?php declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit('CLI only');
}

$core = $argv[1] ?? '';
require $core . '/xoops_lib/vendor/autoload.php';
require __DIR__ . '/../class/Helper.php';

// Keep the real XMF inheritance while isolating the database connection.
final class XoopsDatabaseFactory
{
    public static function getDatabaseConnection(): object
    {
        static $connection;

        return $connection ??= new stdClass();
    }
}

final class CompatibilityHandler
{
    public function __construct(public object $db)
    {
    }
}
class_alias(CompatibilityHandler::class, 'XoopsModules\\Xstyles\\CompatibilityHandler');

$reflection = new ReflectionClass(XoopsModules\Xstyles\Helper::class);
if (! $reflection->getProperty('debug')->isPublic()) {
    throw new RuntimeException('The legacy debug property must remain public.');
}
$helper = $reflection->newInstanceWithoutConstructor();
$helper->setDebug(true);
if ($helper->debug !== true) {
    throw new RuntimeException('XMF setDebug must update the public property.');
}
$helper->debug = false;
$handler = $helper->getHandler('Compatibility');
if (! $handler instanceof CompatibilityHandler || $handler->db !== XoopsDatabaseFactory::getDatabaseConnection()) {
    throw new RuntimeException('Namespaced handlers must receive the core database connection.');
}
echo "Helper compatibility: public debug state and namespaced handlers passed.\n";
