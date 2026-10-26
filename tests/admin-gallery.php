<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    exit('CLI only');
}
ob_start();
require __DIR__ . '/fixtures.php';
ob_end_clean();
require XSTYLES_TEST_MODULE . '/language/english/admin.php';
$frame->assign([
    'xs_admin' => true, 'xs_samples' => [],
    'xs_navigation' => [], 'xs_preview_frame' => false,
]);
$html = $frame->fetch('db:xstyles_index.tpl');
check(! str_contains($html, 'data-xs-responsive'), 'Admin must not create frontend frames');
check(! str_contains($html, 'name="xoops_theme_select"') && ! str_contains($html, 'xs-control-form'), 'Admin must not offer frontend controls');
check(str_contains($html, _AM_XSTYLES_GALLERY), 'Admin context title missing');
check(str_contains($html, 'xs-filter'), 'Admin keeps specimen filter');
// Execute the production controller with real renderers/templates and an isolated admin bootstrap.
// Authentication remains the core cp_header.php responsibility; this does not impersonate a live user.
require_once XSTYLES_TEST_MODULE . '/class/Gallery.php';
$directory = XOOPS_VAR_PATH . '/admin-controller';
mkdir($directory);
copy(XSTYLES_TEST_MODULE . '/admin/gallery.php', $directory . '/gallery.php');
file_put_contents($directory . '/admin_header.php', '<?php');
file_put_contents($directory . '/admin_footer.php', '<?php');
// Stub only the authenticated shell; selection and rendered page use the real controller.
class XoopsSystemCpanel
{
    public object $gui;

    public function __construct()
    {
        $this->gui = (object) ['foldername' => 'modern'];
    }

    public static function getInstance(): self
    {
        static $instance;

        return $instance ??= new self();
    }

    public static function getGuis(): array
    {
        return ['modern', 'default', 'dark', 'transition'];
    }

    public function loadGui(string $theme): void
    {
        check(in_array($theme, self::getGuis(), true), 'Unsafe admin theme reached loader');
        $this->gui->foldername = $theme;
    }
}
class_alias(XoopsSystemCpanel::class, 'cpanel');
function xoops_cp_header(): void
{
    $GLOBALS['xoTheme']->folderName = XoopsSystemCpanel::getInstance()->gui->foldername;
}
$helper = new class () {
    public function url(): string
    {
        return XOOPS_URL . '/modules/xstyles/';
    }
};
$adminObject = new class () {
    public function displayNavigation(string $file): void
    {
    }
};
$xoopsTpl = new XoopsTpl();
$xoTheme->folderName = 'modern';
// The preceding integration suite intentionally ends on Tailwind; choose this harness's admin renderer explicitly.
XoopsFormRenderer::getInstance()->set(new XoopsFormRendererLegacy());
$rendererBefore = XoopsFormRenderer::getInstance()->get();
$pages = [];
foreach (['all', 'forms', ['bad'], 'theme'] as $requested) {
    $_GET['section'] = $requested;
    ob_start();
    require $directory . '/gallery.php';
    $page = ob_get_clean();
    check(XoopsFormRenderer::getInstance()->get() === $rendererBefore, 'Admin changed the active renderer');
    check(! str_contains($page, 'data-xs-responsive') && ! str_contains($page, 'xs-control-form'), 'Controller leaked frontend controls');
    check(! str_contains($page, '/admin/assets/'), 'Admin specimen images use the module asset directory');
    check(! str_contains($page, 'XOOPS_TOKEN_REQUEST'), 'Admin specimen minted a security token');
    check(str_contains($page, 'admin/gallery.php?section=forms'), 'Admin section links leave the admin gallery');
    check(substr_count($page, 'class="xs-example"') === ($requested === 'forms' ? 5 : 21), 'Unexpected admin specimen count');
    $pages[is_string($requested) ? $requested : 'malformed'] = $page;
}
unset($_GET['section']);
foreach (['modern', 'default', 'dark', 'transition'] as $requestedTheme) {
    foreach (['all', 'forms'] as $requested) {
        foreach ([false, true] as $child) {
            XoopsSystemCpanel::getInstance()->loadGui('modern');
            $_GET = ['section' => $requested, 'xs_theme' => $requestedTheme];
            if ($child) {
                $_GET['preview_frame'] = '1';
            }
            ob_start();
            require $directory . '/gallery.php';
            $page = ob_get_clean();
            check(str_contains($page, '<code>' . $requestedTheme . '</code>'), 'Selected admin theme not reflected');
            check(str_contains($page, 'data-xs-compare') !== $child, 'Comparison must not recurse');
            if ($child) {
                check(str_contains($page, '?section=' . $requested . '&amp;preview_frame=1">Return to current theme'), 'Child reset must remain a child preview');
            }
            check(str_contains($page, 'section=forms&amp;xs_theme=' . $requestedTheme), 'Theme lost on section link');
            check(! str_contains($page, 'name="xoops_theme_select"'), 'Admin must never select frontend theme');
            $pages[$requestedTheme . ':' . $requested . ':' . ($child ? 'child' : 'parent')] = $page;
        }
    }
}
foreach ([['bad'], '../modern', 'missing'] as $invalid) {
    XoopsSystemCpanel::getInstance()->loadGui('modern');
    $_GET = ['xs_theme' => $invalid];
    ob_start();
    require $directory . '/gallery.php';
    ob_end_clean();
    check($xoTheme->folderName === 'modern', 'Invalid admin choice changed theme');
}
$_GET = [];
echo in_array('--json', $argv, true) ? json_encode($pages, JSON_THROW_ON_ERROR)
    : "Admin gallery: real controller, 21 samples, 5 forms, malformed sections, assets, renderer restoration, no tokens or frontend controls passed.\n";
