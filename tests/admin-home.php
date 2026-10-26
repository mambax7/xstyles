<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    exit('CLI only');
}
ob_start();
require __DIR__ . '/fixtures.php';
ob_end_clean();
require XSTYLES_TEST_MODULE . '/language/english/admin.php';
require_once XOOPS_ROOT_PATH . '/modules/system/language/english/admin.php';
check(_MI_XSTYLES_OVERVIEW === 'Home', 'First tab should be Home');
check(_MI_XSTYLES_ADMIN_GALLERY === 'Admin styles', 'Admin tab identifies its theme context');
check(_MD_XSTYLES_ALL === 'All elements', 'All-elements filter uses plain wording');
check(_MI_XSTYLES_DESC === 'A living style guide for XOOPS themes.', 'Plain-language module description');
check(_MD_XSTYLES_INTRO === 'Preview how the current theme styles every common page element.', 'Plain-language subtitle');
$xoopsModule = new class ($modversion) {
    public array $adminmenu = [];

    public function __construct(private array $info)
    {
    }

    public function getVar(string $name): mixed
    {
        return ['dirname' => 'xstyles', 'mid' => 42][$name] ?? '';
    }

    public function getInfo(string $name): mixed
    {
        return $this->info[$name] ?? false;
    }

    public function loadAdminMenu(): void
    {
        require XSTYLES_TEST_MODULE . '/admin/menu.php';
        $this->adminmenu = $adminmenu;
    }

    public function versionCompare(string $a, string $b, string $operator): bool
    {
        return version_compare($a, $b, $operator);
    }
};
require_once XOOPS_ROOT_PATH . '/Frameworks/moduleclasses/moduleadmin/moduleadmin.php';
$adminObject = Xmf\Module\Admin::getInstance();
$helper = new class () {
    public function url(string $path = ''): string
    {
        return XOOPS_URL . '/modules/xstyles/' . $path;
    }
};
function xoops_cp_header(): void
{
}
$directory = XOOPS_VAR_PATH . '/home-controller';
mkdir($directory);
copy(XSTYLES_TEST_MODULE . '/admin/index.php', $directory . '/index.php');
file_put_contents($directory . '/admin_header.php', '<?php');
file_put_contents($directory . '/admin_footer.php', '<?php');
ob_start();
require $directory . '/index.php';
$html = ob_get_clean();
check(str_contains($html, 'xo-modadmin-index'), 'Home restores the standard dashboard');
check(str_contains($html, _AM_MODULEADMIN_CONFIG), 'Home shows configuration checks');
check(str_contains($html, _AM_XSTYLES_SERVER_STATUS), 'Home shows server status');
check(str_contains($html, htmlspecialchars(ini_get('memory_limit'), ENT_QUOTES, 'UTF-8')), 'Server status uses actual configuration');
check(str_contains($html, _AM_XSTYLES_OVERVIEW_DESC), 'Home retains the module explanation');
check(array_column($xoopsModule->adminmenu, 'link') === ['admin/index.php', 'admin/gallery.php', 'admin/about.php'], 'About must be the last module tab, before the core Help tab');
check(! str_contains($html, 'Open the gallery'), 'Use the standard Go to module link');
check(! str_contains($html, 'uploads/xstyles'), 'Home does not invent upload-folder requirements');
check(! is_dir(XOOPS_VAR_PATH . '/uploads'), 'Home is read-only');
echo in_array('--html', $argv, true) ? $html : "Home dashboard: plain language, real ModuleAdmin tiles/configuration, server status and no upload requirements passed.\n";
