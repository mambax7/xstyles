<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit('CLI only');
}

// Real core classes, with isolated caches and a staging-only template resource: no site/session bootstrap.
$core = isset($argv[1]) ? realpath($argv[1]) : false;
if ($core === false || ! is_file($core . '/class/template.php') || ! is_file($core . '/xoops_lib/vendor/autoload.php')) {
    fwrite(STDERR, "Usage: php tests/fixtures.php /path/to/core/htdocs\n");
    exit(1);
}
define('XSTYLES_TEST_MODULE', dirname(__DIR__));
define('XOOPS_ROOT_PATH', str_replace('\\', '/', $core));
define('XOOPS_PATH', XOOPS_ROOT_PATH . '/xoops_lib');
define('XOOPS_TRUST_PATH', XOOPS_PATH);
$temporary = realpath(sys_get_temp_dir());
define('XOOPS_VAR_PATH', str_replace('\\', '/', $temporary) . '/xstyles-fixtures-' . bin2hex(random_bytes(8)));
register_shutdown_function(static function () use ($temporary): void {
    $root = realpath(XOOPS_VAR_PATH);
    if ($root === false || ! str_starts_with(str_replace('\\', '/', $root), str_replace('\\', '/', $temporary) . '/xstyles-fixtures-')) {
        return;
    }
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($root);
});
define('XOOPS_URL', 'https://example.invalid');
define('XOOPS_MAINFILE_INCLUDED', 1);
define('NWLINE', "\n");
define('DS', DIRECTORY_SEPARATOR);
foreach (['caches/smarty_cache', 'caches/smarty_compile', 'caches/xoops_cache', 'configs/textsanitizer'] as $directory) {
    if (! is_dir(XOOPS_VAR_PATH . '/' . $directory)) {
        mkdir(XOOPS_VAR_PATH . '/' . $directory, 0777, true);
    }
}
require XOOPS_PATH . '/vendor/autoload.php';
require XOOPS_ROOT_PATH . '/include/defines.php';
require XOOPS_ROOT_PATH . '/include/version.php';
require XOOPS_ROOT_PATH . '/include/functions.php';
require XOOPS_ROOT_PATH . '/class/xoopsload.php';
XoopsLoad::load('xoopskernel');
$GLOBALS['xoops'] = new xos_kernel_Xoops2();
$GLOBALS['xoops']->pathTranslation();
$GLOBALS['xoopsConfig'] = ['language' => 'english', 'debug_mode' => 0, 'theme_set' => 'default', 'template_set' => 'default', 'theme_fromfile' => 1, 'default_TZ' => 0, 'server_TZ' => 0];
$GLOBALS['xoopsUser'] = null;
xoops_loadLanguage('global');
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRenderer');
XoopsLoad::load('preload');
XoopsLoad::load('xoopslogger');
$GLOBALS['xoopsLogger'] = XoopsLogger::getInstance();
set_exception_handler(static function (Throwable $error): void {
    fwrite(STDERR, $error::class . ': ' . $error->getMessage() . "\n" . $error->getTraceAsString() . "\n");
    exit(1);
});
set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
require XOOPS_ROOT_PATH . '/class/theme.php';
$GLOBALS['xoTheme'] = new xos_opal_Theme();

// Smarty renamed its custom resource base in version 5; use the installed core's API.
class_alias(class_exists(Smarty\Resource\CustomPlugin::class) ? Smarty\Resource\CustomPlugin::class : Smarty_Resource_Custom::class, 'FixtureResourceBase');
final class FixtureStagingResource extends FixtureResourceBase
{
    protected function fetch($name, &$source, &$mtime): void
    {
        $root = str_starts_with($name, 'xstyles_') ? XSTYLES_TEST_MODULE . '/templates/' : XOOPS_ROOT_PATH . '/modules/system/templates/';
        if (str_starts_with($name, 'system_block_')) {
            $root .= 'blocks/';
        }
        $path = $root . basename($name);
        if (FixtureIntegrationHook::$mainMenuOverride && $name === 'system_block_mainmenu.tpl') {
            $path = XOOPS_ROOT_PATH . '/themes/xbootstrap5/modules/system/blocks/system_block_mainmenu.tpl';
        }
        if (FixtureIntegrationHook::$blockFormOverride && in_array($name, ['system_block_login.tpl', 'system_block_search.tpl'], true)) {
            $path = XOOPS_ROOT_PATH . '/themes/xbootstrap5/modules/system/blocks/' . $name;
        }
        if (FixtureIntegrationHook::$notificationOverride && $name === 'system_notification_select.tpl') {
            $override = XOOPS_ROOT_PATH . '/themes/xbootstrap5/modules/system/system_notification_select.tpl';
            if (is_file($override)) {
                $path = $override;
            }
        }
        if (FixtureIntegrationHook::$commentOverride && $name === 'system_comment.tpl') {
            $path = XOOPS_ROOT_PATH . '/themes/xbootstrap5/modules/system/system_comment.tpl';
            if (! is_file($path)) {
                $source = '<div class="xoops-comment-template"><{$comment.title}></div>';
                $mtime = time();

                return;
            }
        }
        $source = file_get_contents($path);
        if (FixtureIntegrationHook::$blockFormOverride && in_array($name, ['system_block_login.tpl', 'system_block_search.tpl'], true)) {
            $source .= '<{securityToken}>';
        }
        $mtime = filemtime($path);
    }
}
final class FixtureIntegrationHook
{
    public static bool $commentOverride = false;
    public static bool $notificationOverride = false;
    public static bool $mainMenuOverride = false;
    public static bool $blockFormOverride = false;

    public static function template(array $args): void
    {
        $args[0]->registerResource('db', new FixtureStagingResource());
        $args[0]->setCompileId('fixtures-' . (int) self::$commentOverride . '-' . (int) self::$notificationOverride
            . '-' . (int) self::$mainMenuOverride . '-' . (int) self::$blockFormOverride);
    }
}
XoopsPreload::getInstance()->_events['coreclasstemplatenew'][] = ['class_name' => FixtureIntegrationHook::class, 'method' => 'template'];
require XOOPS_ROOT_PATH . '/class/template.php';
require XSTYLES_TEST_MODULE . '/language/english/main.php';
require_once XSTYLES_TEST_MODULE . '/language/english/fixtures.php';
require_once XSTYLES_TEST_MODULE . '/language/english/bootstrap5.php';
require XSTYLES_TEST_MODULE . '/class/Fixtures.php';

function check(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
$template = new XoopsTpl();
$template->assign(['block' => ['host' => 'keep'], 'xoops_isadmin' => true, 'xoops_isuser' => true, 'comment' => ['host' => 'keep'], 'xoops_upload_url' => 'https://host.invalid/uploads']);
$before = $template->getTemplateVars();
$samples = XoopsModules\Xstyles\Fixtures::build('all', $template, XOOPS_URL . '/modules/xstyles');
check(count($samples) === 40, 'All core and Bootstrap fixtures must render');
$codeSample = array_values(array_filter($samples, static fn (array $sample): bool => $sample['id'] === 'xs-code'))[0];
$codeText = html_entity_decode(strip_tags($codeSample['html']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
$codeText = str_replace("\u{00a0}", ' ', $codeText);
check(preg_match('//u', $codeSample['html']) === 1, 'Code specimen must be valid UTF-8');
check(str_contains($codeText, '$theme = "XOOPS";'), 'Code specimen must preserve the sample assignment');
check(str_contains($codeText, 'htmlspecialchars($theme, ENT_QUOTES, "UTF-8")'), 'Code specimen must preserve the sample function call');
check($before === $template->getTemplateVars(), 'Host template assignments were mutated');
$ids = array_column($samples, 'id');
check(count(array_unique($ids)) === 40, 'Fixture IDs must be unique');
check($ids === array_column(XoopsModules\Xstyles\Fixtures::build('all', $template, XOOPS_URL . '/modules/xstyles'), 'id'), 'Fixture ordering must be stable');
foreach (['typography' => 4, 'content' => 8, 'tables' => 2, 'forms' => 5, 'comments' => 2, 'blocks' => 4, 'bootstrap5' => 8, 'bootstrap6' => 7] as $section => $count) {
    $selected = XoopsModules\Xstyles\Fixtures::build($section, $template, XOOPS_URL . '/modules/xstyles');
    check(count($selected) === $count && array_unique(array_column($selected, 'section')) === [$section], 'Section filtering failed: ' . $section);
}
$document = new DOMDocument();
$previous = libxml_use_internal_errors(true);
$document->loadHTML(implode("\n", array_column($samples, 'html')));
libxml_clear_errors();
libxml_use_internal_errors($previous);
$xpath = new DOMXPath($document);
$domIds = [];
foreach ($xpath->query('//*[@id]') as $node) {
    $domIds[] = $node->getAttribute('id');
}
check(count($domIds) === count(array_unique($domIds)), 'Generated fixture HTML contains duplicate IDs');
check(! in_array('mainmenu', $domIds, true) && ! in_array('usermenu', $domIds, true), 'Menu IDs must not collide with real site blocks');
foreach ($xpath->query('//form') as $form) {
    check($form->hasAttribute('data-xs-demo-form'), 'All fixture forms must be marked as demo');
    check($form->getAttribute('action') === XOOPS_URL . '/modules/xstyles/index.php', 'Demo action must stay on xStyles');
}
check($xpath->query('//input[@name="XOOPS_TOKEN_REQUEST"]')->length === 0, 'A specimen must not mint a token');
check($xpath->query('//input[@name="not_submit"]')->length === 0, 'A specimen must not submit a real notification action');
foreach (['text', 'password', 'checkbox', 'radio', 'file', 'hidden'] as $type) {
    check($xpath->query('//input[@type="' . $type . '"]')->length > 0, 'Missing core form field: ' . $type);
}
check($xpath->query('//textarea')->length >= 2, 'Textarea and core editor must both render');
check($xpath->query('//select')->length > 0, 'Core select must render');
check($xpath->query('//input[@disabled]')->length > 0, 'Core disabled field must render');
foreach ([11, 12] as $options) {
    check($xpath->query('//select[@id="xs_advanced_select_' . $options . '"]/option')->length === $options, 'Advanced select boundary missing');
}
check($xpath->query('//select[@id="xs_advanced_multiple"][@multiple]')->length === 1, 'Core multiselect missing');
check($xpath->query('//input[@id="xs_advanced_readonly"][@readonly]')->length === 1, 'Readonly field missing');
check($xpath->query('//*[@id="xs_advanced_color"]')->length === 1, 'Color picker missing');
check($xpath->query('//input[@id="xs_tabs_required"]')->length === 1, 'Required field in second tab missing');
check($xpath->query('//input[@id="xs_advanced_readonly"]')->item(0)->getAttribute('value') === 'A "quoted" <sample> & value', 'Core form values must escape exactly once');
check($xpath->query('//input[@name="op" or @name="uname" or @name="pass" or @name="query"]')->length === 0, 'Block fixtures must not expose active action fields');
foreach ($xpath->query('//label[@for]') as $label) {
    check($xpath->query('//*[@id="' . $label->getAttribute('for') . '"]')->length === 1, 'Label has no unique target: ' . $label->getAttribute('for'));
}
foreach ($samples as $sample) {
    check(trim($sample['html']) !== '' && ! str_contains($sample['html'], '<{'), 'Unrendered fixture: ' . $sample['id']);
    if ($sample['section'] === 'blocks') {
        check(! preg_match('/href=["\'](?!#xs-)/i', $sample['html']), 'A fixture menu contains a live link');
    }
}
FixtureIntegrationHook::$commentOverride = true;
if (is_file(XOOPS_ROOT_PATH . '/themes/xbootstrap5/modules/system/blocks/system_block_mainmenu.tpl')) {
    FixtureIntegrationHook::$mainMenuOverride = true;
    $menuTemplate = new XoopsTpl();
    $menuTemplate->assign('iconOverrides', ['xstyles' => ['sub' => ['events' => 'fa-solid fa-calendar']]]);
    $menuBefore = $menuTemplate->getTemplateVars();
    $menuSamples = XoopsModules\Xstyles\Fixtures::build('blocks', $menuTemplate, XOOPS_URL . '/modules/xstyles');
    check(str_contains($menuSamples[0]['html'], 'fa-solid fa-calendar'), 'Main-menu override must resolve the sublink ID');
    check(str_contains($menuSamples[0]['html'], 'href="#xs-main-menu"'), 'Main-menu override links must remain inert');
    check(! preg_match('/href=["\'](?!#xs-)/i', $menuSamples[0]['html']), 'Main-menu override contains a live link');
    check($menuBefore === $menuTemplate->getTemplateVars(), 'Main-menu override mutated host assignments');
    FixtureIntegrationHook::$mainMenuOverride = false;
}
FixtureIntegrationHook::$notificationOverride = true;
if (is_file(XOOPS_ROOT_PATH . '/themes/xbootstrap5/modules/system/blocks/system_block_login.tpl')) {
    require_once XOOPS_ROOT_PATH . '/themes/xbootstrap5/language/english/main.php';
    FixtureIntegrationHook::$blockFormOverride = true;
    $blockSamples = XoopsModules\Xstyles\Fixtures::build('blocks', $template, XOOPS_URL . '/modules/xstyles');
    foreach (array_slice($blockSamples, 2) as $sample) {
        check(str_contains($sample['html'], 'data-xs-demo-form="true"'), 'Overridden block form lost demo marker');
        check(str_contains($sample['html'], 'action="' . XOOPS_URL . '/modules/xstyles/index.php"'), 'Overridden block has active action');
        check(! preg_match('/\bon(?:click|submit|change)=|XOOPS_TOKEN_REQUEST|\bname=["\'](?:op|uname|pass|query)["\']/i', $sample['html']), 'Overridden block has active fields or token');
    }
    FixtureIntegrationHook::$blockFormOverride = false;
}
$notificationTemplate = new XoopsTpl();
$notificationSamples = XoopsModules\Xstyles\Fixtures::build('forms', $notificationTemplate, XOOPS_URL . '/modules/xstyles');
$notificationHtml = $notificationSamples[2]['html'];
preg_match_all('/\bid=["\']([^"\']+)["\']/', $notificationHtml, $notificationIds);
check(count($notificationIds[1]) === count(array_unique($notificationIds[1])), 'Theme notification fixture repeats IDs');
$overrideTemplate = new XoopsTpl();
$override = XoopsModules\Xstyles\Fixtures::build('comments', $overrideTemplate, XOOPS_URL . '/modules/xstyles')[0]['html'];
check(str_contains($override, 'xoops-comment-template'), 'Core theme comment override did not render');
check(! str_contains($override, '<table'), 'A div-based core override must not be wrapped in a table');
check(str_contains($override, 'xs-comment-frame'), 'Grid-based comments need a container for their outer gutters');
check($before === $template->getTemplateVars(), 'A repeated render mutated host template assignments');
check(! str_contains(implode('', array_column($samples, 'html')), 'history.go'), 'Confirmation cancel must stay inert');
foreach (['XoopsFormRendererBootstrap5' => 'form-control', 'XoopsFormRendererTailwind' => 'input-bordered'] as $rendererClass => $expectedClass) {
    if (is_file(XOOPS_ROOT_PATH . '/class/xoopsform/renderer/' . $rendererClass . '.php')) {
        xoops_load($rendererClass);
        XoopsFormRenderer::getInstance()->set(new $rendererClass());
        $originalRenderer = XoopsFormRenderer::getInstance()->get();
        $forms = XoopsModules\Xstyles\Fixtures::build('forms', $template, XOOPS_URL . '/modules/xstyles');
        check(str_contains($forms[0]['html'], $expectedClass), 'Fixture ignored active renderer: ' . $rendererClass);
        foreach (['theme', 'legacy', 'bootstrap5', '../../invalid'] as $previewRenderer) {
            $previews = XoopsModules\Xstyles\Fixtures::build('forms', $template, XOOPS_URL . '/modules/xstyles', $previewRenderer);
            check(XoopsFormRenderer::getInstance()->get() === $originalRenderer, 'Renderer identity leaked after form rendering');
            check(count($previews) === 5, 'Every form must render under every preview renderer');
            $previewDocument = new DOMDocument();
            $previous = libxml_use_internal_errors(true);
            $previewDocument->loadHTML(implode('', array_column($previews, 'html')));
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
            $previewXpath = new DOMXPath($previewDocument);
            $previewIds = [];
            foreach ($previewXpath->query('//*[@id]') as $node) {
                $previewIds[] = $node->getAttribute('id');
            }
            check(count($previewIds) === count(array_unique($previewIds)), 'Preview renderer repeats IDs: ' . $previewRenderer);
            $unmatched = [];
            foreach ($previewXpath->query('//label[@for]') as $label) {
                if ($previewXpath->query('//*[@id="' . $label->getAttribute('for') . '"]')->length !== 1) {
                    $unmatched[] = $label->getAttribute('for');
                }
            }
            if ($unmatched !== []) {
                // Core renderers label group names and Bootstrap5 emits a malformed color input; keep the evidence visible.
                $knownCoreGaps = ['xs_fixture_checkbox', 'xs_fixture_radio', 'xs_fixture_actions', 'xs_advanced_color',
                    'xs_advanced_checks_0', 'xs_advanced_checks_1', 'xs_advanced_checks_2'];
                check(array_diff($unmatched, $knownCoreGaps) === [], 'New unmatched renderer label target');
                echo 'Core renderer label gaps (' . $rendererClass . '/' . $previewRenderer . '): ' . implode(', ', $unmatched) . "\n";
            }
            check($previewXpath->query('//input[@name="XOOPS_TOKEN_REQUEST"]')->length === 0, 'Preview minted a security token');
            check(str_contains($previews[4]['html'], 'xs_tabs_required'), 'Required second-tab field missing');
        }

        try {
            XoopsModules\Xstyles\LegacyFixtureRenderer::render('legacy', static function (): string {
                check(XoopsFormRenderer::getInstance()->get() instanceof XoopsFormRendererLegacy, 'Preview was not selected inside scope');

                throw new RuntimeException('fixture-render-failure');
            });

            throw new LogicException('Expected render failure did not propagate');
        } catch (RuntimeException $error) {
            check($error->getMessage() === 'fixture-render-failure', 'Unexpected fixture exception');
        }
        check(XoopsFormRenderer::getInstance()->get() === $originalRenderer, 'Renderer identity leaked after exception');
    }
}
// Compile the public frame too; source must be text, while only the fixture HTML is rendered.
FixtureIntegrationHook::$commentOverride = false;
$frame = new XoopsTpl();
$frame->assign([
    'xs_mode' => 'theme', 'xs_direction' => 'theme', 'xs_theme' => 'sample',
    'xs_locale' => 'en', 'xs_actual_direction' => 'ltr', 'xs_renderer' => 'TestRenderer',
    'xs_section' => 'content', 'xs_navigation' => [], 'xs_themes' => [],
    'xs_module_url' => XOOPS_URL . '/modules/xstyles', 'xs_page_url' => XOOPS_URL,
    'xs_zones' => 'off', 'xs_zone_names' => [], 'xs_holmes_links' => [],
    'xs_samples' => [[
        'id' => 'xs-escaping', 'section' => 'content', 'url' => '#xs-escaping',
        'title' => '<img data-title-injection src=x>', 'selector' => '<style>bad</style>',
        'html' => '<p data-safe-fixture="yes">Example &amp; source</p>',
    ]],
]);
$framed = $frame->fetch('db:xstyles_index.tpl');
check(! str_contains($framed, '<img data-title-injection'), 'Example title was interpreted as HTML');
check(! str_contains($framed, '<style>bad</style>'), 'Selector caption was interpreted as HTML');
check(str_contains($framed, '<p data-safe-fixture="yes">'), 'Trusted specimen HTML must render');
check(str_contains($framed, '&lt;p data-safe-fixture='), 'Rendered HTML source must be escaped');
check(! str_contains($framed, '<{'), 'Public gallery contains uncompiled Smarty');
// Older cores have no request-only theme override: the current-theme workspace still works.
check(! str_contains($framed, 'data-xs-compare') && ! str_contains($framed, 'id="xs-theme"'), 'Unavailable frontend theme choices must stay hidden');
check(str_contains($framed, 'data-xs-responsive') && str_contains($framed, 'id="xs-preview-settings"'), 'Current-theme responsive and layout previews must remain available');
$frame->assign('xs_module_url', XOOPS_URL . '/modules/xstyles');
check(str_contains($frame->fetch('db:xstyles_zone.tpl'), 'xs-zone-content'), 'Zone template must compile');
require XSTYLES_TEST_MODULE . '/language/english/modinfo.php';
require XSTYLES_TEST_MODULE . '/class/Helper.php';
require XSTYLES_TEST_MODULE . '/xoops_version.php';
$registered = array_column($modversion['templates'], 'file');
foreach (glob(XSTYLES_TEST_MODULE . '/templates/*.tpl') as $file) {
    check(in_array(basename($file), $registered, true), 'Template missing from manifest: ' . basename($file));
}
// Exercise lifecycle signatures with the core's real module class, without its database installer.
require_once XOOPS_ROOT_PATH . '/kernel/object.php';
require_once XOOPS_ROOT_PATH . '/kernel/module.php';
$module = new XoopsModule();
$module->setVar('dirname', 'xstyles');
foreach (['install', 'update', 'uninstall'] as $operation) {
    require_once XSTYLES_TEST_MODULE . '/include/on' . $operation . '.php';
    foreach (['pre_', ''] as $prefix) {
        $callback = 'xoops_module_' . $prefix . $operation . '_xstyles';
        check($callback($module), 'Core lifecycle callback failed: ' . $callback);
    }
}
check(xoops_module_update_xstyles($module, '1.4.0'), 'Updating an older module version must succeed');

// Theme specimens use local templates and isolated assignments; one broken template must not hide good ones.
require_once XSTYLES_TEST_MODULE . '/class/ThemeSpecimens.php';
$themeDirectory = XOOPS_VAR_PATH . '/specimen-theme';
mkdir($themeDirectory . '/modules/xstyles/specimens', 0777, true);
file_put_contents($themeDirectory . '/modules/xstyles/specimens.json', json_encode([
    'version' => 1, 'specimens' => [
        ['id' => 'card', 'title' => '<b>Card</b>', 'selector' => '.card', 'template' => 'specimens/card.tpl'],
        ['id' => 'card-heading', 'title' => 'Another card', 'selector' => '.card', 'template' => 'specimens/card.tpl'],
        ['id' => 'broken', 'title' => 'Broken', 'selector' => '', 'template' => 'specimens/broken.tpl'],
    ],
], JSON_THROW_ON_ERROR));
file_put_contents($themeDirectory . '/modules/xstyles/specimens/card.tpl', '<{assign var="host" value="changed"}><{securityToken}><article class="card"><h3 id="heading">Example</h3><p aria-labelledby="heading"><{$xs_theme_url|escape}></p><pre id="source">Specimen source text</pre><p id="title">Sample title</p><a data-id="preserved" href="/login.php">Preview</a><form action="/login.php"><input name="title"></form></article>');
file_put_contents($themeDirectory . '/modules/xstyles/specimens/broken.tpl', '<{if}>');
$definitions = XoopsModules\Xstyles\ThemeSpecimens::load($themeDirectory);
$templateBefore = $template->getTemplateVars();
$themeOutput = XoopsModules\Xstyles\Fixtures::theme($definitions['samples'], XOOPS_URL . '/modules/xstyles', XOOPS_URL . '/themes/example');
check(count($themeOutput['samples']) === 2 && count($themeOutput['errors']) === 1, 'Theme template failure must be isolated');
check($templateBefore === $template->getTemplateVars(), 'Theme template assignments leaked');
$themeHtml = $themeOutput['samples'][0]['html'];
check(str_contains($themeHtml, 'id="xs-theme-card_heading"') && str_contains($themeHtml, 'aria-labelledby="xs-theme-card_heading"'), 'Theme IDs/references must stay linked');
check(str_contains($themeHtml, 'data-id="preserved"') && ! str_contains($themeHtml, 'login.php'), 'Theme attributes/actions');
check(! str_contains($themeHtml, 'XOOPS_TOKEN_REQUEST'), 'Theme specimen minted a token');
$frame->assign('xs_samples', [$themeOutput['samples'][0] + ['url' => '#xs-theme-card']]);
check(! str_contains($frame->fetch('db:xstyles_index.tpl'), '<b>Card</b>'), 'Theme metadata was interpreted as HTML');
check(str_contains($frame->fetch('db:xstyles_index.tpl'), 'id="xs-theme-card_source"'), 'Inner ID must use a separate namespace from the source panel');
check(! str_contains($themeOutput['errors'][0], XOOPS_VAR_PATH), 'Template failure exposed a private path');

echo "Real-core fixture integration passed: 40 core/Bootstrap samples, scoped renderers, theme descriptor/rendering/error isolation, inert actions, unique IDs, label diagnostics, escaping and registration.\n";
