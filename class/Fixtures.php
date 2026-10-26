<?php

declare(strict_types=1);

namespace XoopsModules\Xstyles;

/**
 * Theme specimens for xStyles, originally by Burning & Xoops Design Team.
 * @copyright 2000-2026 XOOPS Project (https://xoops.org)
 * @license GNU GPL 2.0 or later
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/** Legacy core-output adapter; new gallery state and XMF/XTF services do not depend on its globals. */
final class Fixtures
{
    /**
     * The public gallery uses fixed data; rendering never invokes block callbacks or writes content.
     *
     * @return array<array{id:string, section:string, title:string, selector:string, html:string}>
     */
    public static function build(string $section, \XoopsTpl $template, string $moduleUrl, string $renderer = 'theme'): array
    {
        $samples = [];
        $specimens = [
            'bs5-buttons' => ['bootstrap5', _MD_XSTYLES_BS5_BUTTONS, '.btn, .badge', 'bootstrap5'],
            'bs5-alerts' => ['bootstrap5', _MD_XSTYLES_BS5_ALERTS, '.alert, .card', 'bootstrap5'],
            'bs5-navigation' => ['bootstrap5', _MD_XSTYLES_BS5_NAVIGATION, '.breadcrumb, .pagination', 'bootstrap5'],
            'bs5-inputs' => ['bootstrap5', _MD_XSTYLES_BS5_INPUTS, '.input-group, .form-control, .is-valid, .is-invalid', 'bootstrap5'],
            'bs5-tabs' => ['bootstrap5', _MD_XSTYLES_BS5_TABS, '.nav-tabs, .tab-pane, .accordion', 'bootstrap5'],
            'bs5-dropdown' => ['bootstrap5', _MD_XSTYLES_BS5_DROPDOWN, '.dropdown', 'bootstrap5'],
            'bs5-modal' => ['bootstrap5', _MD_XSTYLES_BS5_MODAL, '.modal', 'bootstrap5'],
            'bs5-toast' => ['bootstrap5', _MD_XSTYLES_BS5_TOAST, '.toast', 'bootstrap5'],
            'bs6-buttons' => ['bootstrap6', _MD_XSTYLES_BS6_BUTTONS, '.btn-solid, .btn-outline, .theme-primary, .badge', 'bootstrap6'],
            'bs6-cards' => ['bootstrap6', _MD_XSTYLES_BS6_CARDS, '.alert, .card', 'bootstrap6'],
            'bs6-forms' => ['bootstrap6', _MD_XSTYLES_BS6_FORMS, '.field, .form-control, .is-valid, .is-invalid', 'bootstrap6'],
            'bs6-tabs' => ['bootstrap6', _MD_XSTYLES_BS6_TABS, '.nav-tabs, .tab-pane', 'bootstrap6'],
            'bs6-menu' => ['bootstrap6', _MD_XSTYLES_BS6_MENU, '.menu, .menu-item', 'bootstrap6'],
            'bs6-dialog' => ['bootstrap6', _MD_XSTYLES_BS6_DIALOG, 'dialog.dialog', 'bootstrap6'],
            'bs6-drawer' => ['bootstrap6', _MD_XSTYLES_BS6_DRAWER, 'dialog.drawer', 'bootstrap6'],
            'headings' => ['typography', _MD_XSTYLES_HEADINGS, 'h1–h6', 'typography'],
            'inline' => ['typography', _MD_XSTYLES_INLINE, 'p, a, strong, em, small, mark, code, kbd', 'typography'],
            'lists' => ['typography', _MD_XSTYLES_LISTS, 'ul, ol, dl, dt, dd', 'typography'],
            'stress' => ['typography', _MD_XSTYLES_STRESS, 'long text, Unicode, bdi, overflow', 'typography'],
            'legacy-content' => ['content', _MD_XSTYLES_LEGACY_CONTENT, '.item*, .outer, .head, .odd, .even, figure, figcaption', 'content'],
            'quote' => ['content', _MD_XSTYLES_QUOTE, 'MyTextSanitizer::quoteConv() → .xoopsQuote', 'rendered'],
            'code' => ['content', _MD_XSTYLES_CODE, 'MyTextSanitizer::displayTarea() → .xoopsCode', 'rendered'],
            'messages' => ['content', _MD_XSTYLES_MESSAGES, 'xoops_result(), xoops_warning(), xoops_error()', 'rendered'],
            'confirm' => ['content', _MD_XSTYLES_CONFIRM, 'db:system_confirm.tpl → .confirmMsg', 'rendered'],
            'pagination' => ['content', _MD_XSTYLES_PAGINATION, 'XoopsPageNav → db:system_pagenav.tpl', 'rendered'],
            'pagination-select' => ['content', _MD_XSTYLES_PAGINATION_SELECT, 'XoopsPageNav::renderSelect()', 'rendered'],
            'pagination-images' => ['content', _MD_XSTYLES_PAGINATION_IMAGES, 'XoopsPageNav::renderImageNav()', 'rendered'],
            'semantic-table' => ['tables', _MD_XSTYLES_SEMANTIC_TABLE, 'table, caption, thead, tbody, tfoot, th[scope]', 'tables'],
            'legacy-table' => ['tables', _MD_XSTYLES_LEGACY_TABLE, '.outer, .head, .odd, .even, .foot', 'tables'],
            'theme-form' => ['forms', _MD_XSTYLES_THEME_FORM, 'XoopsThemeForm + active XoopsFormRenderer', 'rendered'],
            'native-form' => ['forms', _MD_XSTYLES_NATIVE_FORM, 'form, fieldset, legend, label, input, textarea, select, button', 'form'],
            'notifications' => ['forms', _MD_XSTYLES_NOTIFICATION_FORM, 'db:system_notification_select.tpl — fixed data, no token', 'rendered'],
            'advanced-form' => ['forms', _MD_XSTYLES_ADVANCED_FORM, 'XoopsFormSelect, XoopsFormColorPicker, XoopsFormCheckBox', 'rendered'],
            'tabbed-form' => ['forms', _MD_XSTYLES_TABBED_FORM, 'XoopsFormTabTray — required field in second tab', 'rendered'],
            'comment' => ['comments', _MD_XSTYLES_COMMENT, 'db:system_comment.tpl → .com*', 'comment'],
            'comment-sequence' => ['comments', _MD_XSTYLES_COMMENT_SEQUENCE, 'db:system_comment.tpl × 2', 'comment'],
            'main-menu' => ['blocks', _MD_XSTYLES_MAIN_MENU, 'db:system_block_mainmenu.tpl → .menuTop, .menuMain, .menuSub', 'rendered'],
            'user-menu' => ['blocks', _MD_XSTYLES_USER_MENU, 'db:system_block_user.tpl → .menuTop, .highlight', 'rendered'],
            'login-block' => ['blocks', _MD_XSTYLES_LOGIN_BLOCK, 'db:system_block_login.tpl — fixed data, inert action', 'rendered'],
            'search-block' => ['blocks', _MD_XSTYLES_SEARCH_BLOCK, 'db:system_block_search.tpl — fixed data, inert action', 'rendered'],
        ];
        foreach ($specimens as $id => [$group, $title, $selector, $file]) {
            if ($section !== 'all' && $section !== $group) {
                continue;
            }
            // Smarty clones share variable objects and callback adapters bound to the original instance.
            $fixture = new \XoopsTpl();
            $fixture->assign($template->getTemplateVars());
            $fixture->caching = 0;
            $fixture->assign('xs_fixture_kind', $id);
            $fixture->assign('xs_fixture_url', $moduleUrl);
            if ($id === 'quote' || $id === 'code') {
                require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
                $sanitizer = \MyTextSanitizer::getInstance();
                $html = $id === 'quote'
                    ? $sanitizer->quoteConv('[quote]' . self::escape(_MD_XSTYLES_QUOTE_TEXT) . '[/quote]')
                    : $sanitizer->displayTarea('[code]<?php' . "\n" . '$theme = "XOOPS";' . "\n"
                        . 'echo htmlspecialchars($theme, ENT_QUOTES, "UTF-8");' . "\n" . '[/code]', 0, 0, 1, 0, 0);
                $fixture->assign('xs_fixture_html', $html);
            } elseif (str_starts_with($id, 'pagination')) {
                require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
                $navigation = new \XoopsPageNav(120, 10, 40, 'xs_start', 'section=content');
                $navigation->url = self::escape($moduleUrl . '/index.php?xs_start=');
                $html = match ($id) {
                    'pagination-select' => $navigation->renderSelect(true),
                    'pagination-images' => $navigation->renderImageNav(3),
                    default => $navigation->renderNav(3),
                };
                $fixture->assign('xs_fixture_html', self::inertTemplate($html, $id, $moduleUrl));
            } elseif (in_array($id, ['theme-form', 'advanced-form', 'tabbed-form'], true)) {
                require_once __DIR__ . '/LegacyFixtureRenderer.php';
                $fixture->assign('xs_fixture_html', LegacyFixtureRenderer::render($renderer, static fn (): string => match ($id) {
                    'advanced-form' => self::advancedForm($moduleUrl),
                    'tabbed-form' => self::tabbedForm($moduleUrl),
                    default => self::form($moduleUrl),
                }));
            } elseif ($id === 'messages') {
                $fixture->assign('xs_fixture_html', self::messages());
            } elseif ($id === 'confirm') {
                $fixture->assign([
                    'msg' => self::escape(_MD_XSTYLES_CONFIRM_TEXT), 'action' => self::escape($moduleUrl . '/index.php'),
                    'hiddens' => '', 'addtoken' => false, 'submit' => self::escape(_MD_XSTYLES_PREVIEW),
                ]);
                $html = $fixture->fetch('db:system_confirm.tpl');
                $html = preg_replace('/\s+onclick=(["\'])history\.go\(-1\);?\1/i', '', $html);
                $fixture->assign('xs_fixture_html', preg_replace('/<form\b/i', '<form data-xs-demo-form="true"', $html));
            } elseif ($id === 'notifications') {
                $fixture->assign('xs_fixture_html', self::notifications($fixture, $moduleUrl));
            } elseif ($id === 'comment') {
                self::comment($fixture, $moduleUrl);
            } elseif ($id === 'comment-sequence') {
                self::comment($fixture, $moduleUrl, 'xs-sequence-one');
                $first = $fixture->getTemplateVars('xs_fixture_html');
                self::comment($fixture, $moduleUrl, 'xs-sequence-two');
                $fixture->assign('xs_fixture_html', $first . $fixture->getTemplateVars('xs_fixture_html'));
            } elseif ($id === 'main-menu' || $id === 'user-menu') {
                $fixture->assign('xs_fixture_html', self::menu($fixture, $id));
            } elseif ($id === 'login-block' || $id === 'search-block') {
                $fixture->assign('xs_fixture_html', self::blockForm($fixture, $id, $moduleUrl));
            }
            require_once __DIR__ . '/SpecimenMarkup.php';
            $html = $fixture->fetch('db:xstyles_fixture_' . $file . '.tpl');
            $samples[] = [
                'id' => 'xs-' . $id,
                'section' => $group,
                'title' => $title,
                'selector' => $selector,
                'html' => $html,
                'issues' => in_array($id, ['theme-form', 'advanced-form', 'tabbed-form'], true)
                    ? SpecimenMarkup::missingLabelTargets($html) : [],
            ];
        }

        return $samples;
    }

    /** Local theme templates have the same trust level as the installed theme itself. */
    public static function theme(array $definitions, string $moduleUrl, string $themeUrl): array
    {
        $result = ['samples' => [], 'errors' => []];
        foreach ($definitions as $definition) {
            try {
                $fixture = new \XoopsTpl();
                $fixture->caching = 0;
                foreach (['securityToken', 'securitytoken'] as $plugin) {
                    $fixture->unregisterPlugin('function', $plugin);
                    $fixture->registerPlugin('function', $plugin, static fn (array $params): string => '');
                }
                $fixture->assign([
                    'xs_module_url' => $moduleUrl, 'xs_theme_url' => $themeUrl,
                    'xs_specimen_id' => 'xs-theme-' . $definition['id'],
                ]);
                $html = $fixture->fetch('file:' . $definition['path']);
                $result['samples'][] = [
                    'id' => 'xs-theme-' . $definition['id'], 'section' => 'theme',
                    'title' => $definition['title'], 'selector' => $definition['selector'],
                    'html' => self::inertTemplate($html, 'theme-' . $definition['id'], $moduleUrl), 'issues' => [],
                ];
            } catch (\Throwable) {
                // Template exceptions may contain private filesystem paths; expose only the registered ID.
                $result['errors'][] = $definition['id'] . ': template rendering failed';
            }
        }

        return $result;
    }

    private static function messages(): string
    {
        ob_start();

        try {
            \xoops_result(self::escape(_MD_XSTYLES_SUCCESS_TEXT));
            \xoops_warning(self::escape(_MD_XSTYLES_NOTICE_TEXT));
            \xoops_error(self::escape(_MD_XSTYLES_ERROR_TEXT));

            return (string) ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    private static function notifications(\XoopsTpl $template, string $moduleUrl): string
    {
        // A specimen must never mint a real session token, including inside theme overrides.
        foreach (['securityToken', 'securitytoken'] as $plugin) {
            $template->unregisterPlugin('function', $plugin);
            $template->registerPlugin('function', $plugin, static fn (array $params): string => '');
        }
        $template->assign([
            'xoops_notification' => [
                'show' => true, 'target_page' => self::escape($moduleUrl . '/index.php'), 'redirect_script' => '',
                'categories' => [[
                    'name' => 'xs_fixture', 'title' => self::escape(_MD_XSTYLES_NEWS), 'itemid' => 0,
                    'events' => [
                        ['name' => 'published', 'caption' => self::escape(_MD_XSTYLES_NOTIFICATION_EVENT), 'subscribed' => false],
                        ['name' => 'updated', 'caption' => self::escape(_MD_XSTYLES_NOTIFICATION_UPDATE), 'subscribed' => true],
                    ],
                ]],
            ],
            'lang_activenotifications' => self::escape(_MD_XSTYLES_NOTIFICATION_FORM),
            'lang_notificationoptions' => self::escape(_MD_XSTYLES_NOTIFICATION_OPTIONS),
            'lang_category' => self::escape(_MD_XSTYLES_FIELD_CHECKBOX), 'lang_checkall' => self::escape(_MD_XSTYLES_CHECK_ALL),
            'lang_events' => self::escape(_MD_XSTYLES_EVENTS), 'lang_updatenow' => self::escape(_MD_XSTYLES_PREVIEW),
            'lang_notificationmethodis' => self::escape(_MD_XSTYLES_NOTIFICATION_METHOD),
            'user_method' => self::escape(_MD_XSTYLES_NOTIFICATION_DEMO),
            'editprofile_url' => '#xs-notifications', 'lang_change' => self::escape(_MD_XSTYLES_PREVIEW),
        ]);
        $html = $template->fetch('db:system_notification_select.tpl');
        require_once __DIR__ . '/SpecimenMarkup.php';
        // Some theme overrides repeat this fixed ID for every event checkbox.
        $event = 0;

        return SpecimenMarkup::attributes($html, static function (string $name, string $value) use (&$event): string|false|null {
            return match (true) {
                str_starts_with($name, 'on') || in_array($name, ['action', 'formaction'], true) => null,
                in_array($name, ['id', 'name', 'for'], true) && $value === 'notification_select' => 'xs_fixture_notifications',
                in_array($name, ['id', 'for'], true) && $value === 'allbox' => 'xs_fixture_allbox',
                $name === 'id' && $value === 'not_list[]' => 'xs-fixture-notification-' . ++$event,
                $name === 'name' && str_starts_with($value, 'not_') => 'xs_fixture_' . $value,
                default => false,
            };
        }, $moduleUrl . '/index.php');
    }

    private static function form(string $moduleUrl): string
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new \XoopsThemeForm(_MD_XSTYLES_FORM_TITLE, 'xs_fixture_form', $moduleUrl . '/index.php', 'post', false);
        $form->setExtra('data-xs-demo-form="true"');
        $name = new \XoopsFormText(_MD_XSTYLES_FIELD_NAME, 'xs_fixture_name', 40, 100, _MD_XSTYLES_SAMPLE_NAME);
        $name->setDescription(_MD_XSTYLES_FIELD_DESCRIPTION);
        $form->addElement($name, true);
        $disabled = new \XoopsFormText(_MD_XSTYLES_FIELD_DISABLED, 'xs_fixture_disabled', 40, 100, _MD_XSTYLES_DISABLED_VALUE);
        $disabled->setExtra('disabled="disabled"');
        $form->addElement($disabled);
        $form->addElement(new \XoopsFormPassword(_MD_XSTYLES_FIELD_PASSWORD, 'xs_fixture_password', 30, 100));
        $form->addElement(new \XoopsFormTextArea(_MD_XSTYLES_FIELD_TEXTAREA, 'xs_fixture_textarea', _MD_XSTYLES_BODY_TEXT, 4, 40));
        $select = new \XoopsFormSelect(_MD_XSTYLES_FIELD_SELECT, 'xs_fixture_select', 'published');
        $select->addOptionArray(['draft' => _MD_XSTYLES_DRAFT, 'published' => _MD_XSTYLES_PUBLISHED]);
        $form->addElement($select);
        $checkbox = new \XoopsFormCheckBox(_MD_XSTYLES_FIELD_CHECKBOX, 'xs_fixture_checkbox', ['news']);
        $checkbox->addOptionArray(['news' => _MD_XSTYLES_NEWS, 'events' => _MD_XSTYLES_EVENTS]);
        $form->addElement($checkbox);
        $radio = new \XoopsFormRadio(_MD_XSTYLES_FIELD_RADIO, 'xs_fixture_radio', 'public');
        $radio->addOptionArray(['public' => _MD_XSTYLES_PUBLIC, 'members' => _MD_XSTYLES_MEMBERS]);
        $form->addElement($radio);
        $file = new \XoopsFormFile(_MD_XSTYLES_FIELD_FILE, 'xs_fixture_file', 1048576);
        $file->setDescription(_MD_XSTYLES_FILE_DESCRIPTION);
        $form->addElement($file);
        $form->addElement(new \XoopsFormTextDateSelect(_MD_XSTYLES_FIELD_DATE, 'xs_fixture_date', 15, 1792454400));
        // The plain editor has no upload/picker endpoints and still uses the core editor factory.
        $form->addElement(new \XoopsFormEditor(_MD_XSTYLES_FIELD_EDITOR, 'xs_fixture_editor', [
            'editor' => 'textarea', 'value' => _MD_XSTYLES_BODY_TEXT, 'rows' => 4, 'cols' => 40,
        ], true, 'textarea'));
        $tray = new \XoopsFormElementTray(_MD_XSTYLES_FIELD_ACTIONS, ' ', 'xs_fixture_actions');
        $tray->addElement(new \XoopsFormButton('', 'xs_fixture_preview', _MD_XSTYLES_PREVIEW, 'submit'));
        $tray->addElement(new \XoopsFormButton('', 'xs_fixture_reset', _MD_XSTYLES_RESET, 'reset'));
        $form->addElement($tray);
        $form->addElement(new \XoopsFormHidden('xs_fixture_hidden', 'demo'));

        return $form->render();
    }

    private static function advancedForm(string $moduleUrl): string
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        // Core colour renderers enqueue Spectrum, whose jQuery dependency may be absent from the theme.
        $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/jquery.js');
        $form = new \XoopsThemeForm(_MD_XSTYLES_ADVANCED_FORM, 'xs_advanced_form', $moduleUrl . '/index.php', 'post', false);
        $form->setExtra('data-xs-demo-form="true"');
        foreach ([11 => _MD_XSTYLES_SELECT_ELEVEN, 12 => _MD_XSTYLES_SELECT_TWELVE] as $count => $caption) {
            $select = new \XoopsFormSelect($caption, 'xs_advanced_select_' . $count, '3');
            for ($option = 1; $option <= $count; ++$option) {
                $select->addOption((string) $option, _MD_XSTYLES_NEWS . ' ' . $option);
            }
            $form->addElement($select);
        }
        $multiple = new \XoopsFormSelect(_MD_XSTYLES_MULTISELECT, 'xs_advanced_multiple', ['news', 'events'], 4, true);
        $multiple->addOptionArray(['news' => _MD_XSTYLES_NEWS, 'events' => _MD_XSTYLES_EVENTS, 'members' => _MD_XSTYLES_MEMBERS]);
        $form->addElement($multiple);
        \xoops_load('XoopsFormColorPicker');
        $form->addElement(class_exists('XoopsFormColorPicker')
            ? new \XoopsFormColorPicker(_MD_XSTYLES_COLOR_PICKER, 'xs_advanced_color', '#446688')
            : new \XoopsFormLabel(_MD_XSTYLES_COLOR_PICKER, self::escape(_MD_XSTYLES_FIXTURE_UNAVAILABLE)));
        foreach ([0, 1, 2] as $columns) {
            $checkbox = new \XoopsFormCheckBox(sprintf(_MD_XSTYLES_CHECKBOX_LAYOUT, $columns), 'xs_advanced_checks_' . $columns, ['news']);
            if (property_exists($checkbox, 'columns')) {
                $checkbox->columns = $columns;
                $checkbox->addOptionArray(['news' => _MD_XSTYLES_NEWS, 'events' => _MD_XSTYLES_EVENTS, 'members' => _MD_XSTYLES_MEMBERS]);
                $form->addElement($checkbox);
            } else {
                $form->addElement(new \XoopsFormLabel($checkbox->getCaption(), self::escape(_MD_XSTYLES_FIXTURE_UNAVAILABLE)));
            }
        }
        $readonly = new \XoopsFormText(_MD_XSTYLES_READONLY, 'xs_advanced_readonly', 40, 100, 'A "quoted" <sample> & value');
        $readonly->setExtra('readonly="readonly"');
        $readonly->setDescription(_MD_XSTYLES_FIELD_DESCRIPTION);
        $form->addElement($readonly);
        $disabled = new \XoopsFormText(_MD_XSTYLES_FIELD_DISABLED, 'xs_advanced_disabled', 40, 100, _MD_XSTYLES_DISABLED_VALUE);
        $disabled->setExtra('disabled="disabled"');
        $form->addElement($disabled);

        return $form->render();
    }

    private static function tabbedForm(string $moduleUrl): string
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        \xoops_load('XoopsFormTabTray');
        if (! class_exists('XoopsFormTabTray')) {
            return '<p>' . self::escape(_MD_XSTYLES_FIXTURE_UNAVAILABLE) . '</p>';
        }
        $form = new \XoopsThemeForm(_MD_XSTYLES_TABBED_FORM, 'xs_tabs_form', $moduleUrl . '/index.php', 'post', false);
        $form->setExtra('data-xs-demo-form="true"');
        $tabs = new \XoopsFormTabTray(_MD_XSTYLES_TABBED_FORM, 'xs_tabs');
        $tabs->addTab(_MD_XSTYLES_TAB_GENERAL);
        $name = new \XoopsFormText(_MD_XSTYLES_FIELD_NAME, 'xs_tabs_name', 40, 100, _MD_XSTYLES_SAMPLE_NAME);
        $name->setDescription(_MD_XSTYLES_TAB_HELP);
        $tabs->addElement($name);
        $tabs->addTab(_MD_XSTYLES_TAB_DETAILS);
        $tabs->addElement(new \XoopsFormText(_MD_XSTYLES_TAB_REQUIRED, 'xs_tabs_required', 40, 100, ''), true);
        $form->addElement($tabs);
        $form->addElement(new \XoopsFormButton('', 'xs_tabs_preview', _MD_XSTYLES_PREVIEW, 'submit'));

        return $form->render();
    }

    private static function blockForm(\XoopsTpl $template, string $kind, string $moduleUrl): string
    {
        foreach (['securityToken', 'securitytoken'] as $plugin) {
            $template->unregisterPlugin('function', $plugin);
            $template->registerPlugin('function', $plugin, static fn (array $params): string => '');
        }
        $template->assign('xoops_requesturi', '#xs-' . $kind);
        $template->assign('block', array_map(self::escape(...), [
            'lang_username' => _MD_XSTYLES_FIELD_NAME, 'unamevalue' => _MD_XSTYLES_SAMPLE_NAME,
            'lang_password' => _MD_XSTYLES_FIELD_PASSWORD, 'lang_login' => _MD_XSTYLES_PREVIEW,
            'lang_rememberme' => _MD_XSTYLES_REMEMBER_ME, 'lang_lostpass' => _MD_XSTYLES_LOST_PASSWORD,
            'lang_registernow' => _MD_XSTYLES_REGISTER, 'lang_search' => _MD_XSTYLES_SEARCH,
            'lang_advsearch' => _MD_XSTYLES_ADVANCED_SEARCH, 'sslloginlink' => '',
        ]));
        $html = $template->fetch($kind === 'login-block' ? 'db:system_block_login.tpl' : 'db:system_block_search.tpl');

        return self::inertTemplate($html, $kind, $moduleUrl);
    }

    private static function inertTemplate(string $html, string $kind, string $moduleUrl): string
    {
        require_once __DIR__ . '/SpecimenMarkup.php';
        // New theme specimens must not collide with wrapper IDs such as <sample>-source or <sample>-title.
        $prefix = 'xs-' . $kind . (str_starts_with($kind, 'theme-') ? '_' : '-');

        return SpecimenMarkup::attributes($html, static function (string $name, string $value) use ($prefix, $kind): string|false|null {
            if (str_starts_with($name, 'on') || in_array($name, ['action', 'formaction', 'data-xs-demo-form'], true)) {
                return null;
            }

            return match ($name) {
                'href' => '#xs-' . $kind,
                'id', 'name', 'list', 'form', 'aria-activedescendant' => $prefix . $value,
                'for', 'aria-labelledby', 'aria-describedby', 'aria-controls', 'aria-details', 'aria-errormessage', 'aria-owns', 'aria-flowto', 'headers' => preg_replace('/\S+/', $prefix . '$0', $value),
                'data-bs-target', 'data-bs-parent' => preg_match('/^#[a-zA-Z][\w:-]*$/D', $value) ? '#' . $prefix . substr($value, 1) : false,
                default => false,
            };
        }, $moduleUrl . '/index.php');
    }

    private static function comment(\XoopsTpl $template, string $moduleUrl, string $id = 'xs-fixture'): void
    {
        $template->assign([
            'xoops_iscommentadmin' => false, 'xoops_isuser' => false, 'xoops_userid' => 0, 'anon_canpost' => false,
            'lang_posted' => self::escape(_MD_XSTYLES_POSTED), 'lang_updated' => self::escape(_MD_XSTYLES_UPDATED),
            'lang_joined' => self::escape(_MD_XSTYLES_JOINED), 'lang_from' => self::escape(_MD_XSTYLES_FROM),
            'lang_posts' => self::escape(_MD_XSTYLES_POSTS), 'xoops_upload_url' => self::escape($moduleUrl . '/assets/images'),
            'editcomment_link' => '#xs-comment', 'deletecomment_link' => '#xs-comment', 'replycomment_link' => '#xs-comment',
            'lang_edit' => self::escape(_MD_XSTYLES_PREVIEW), 'lang_delete' => self::escape(_MD_XSTYLES_PREVIEW),
            'lang_reply' => self::escape(_MD_XSTYLES_PREVIEW),
            'comment' => [
                'id' => $id, 'title' => self::escape(_MD_XSTYLES_COMMENT_TITLE),
                'text' => self::escape(_MD_XSTYLES_BODY_TEXT), 'image' => '',
                'date_posted' => '2026-10-20 10:00', 'date_modified' => '2026-10-20 11:00',
                'poster' => [
                    'id' => 1, 'uname' => self::escape(_MD_XSTYLES_SAMPLE_NAME),
                    'rank_title' => self::escape(_MD_XSTYLES_MEMBERS), 'rank_image' => 'rankimage109x24.png',
                    'avatar' => 'comUserImg86.jpg', 'regdate' => '2025-03-12',
                    'from' => self::escape(_MD_XSTYLES_SAMPLE_LOCATION), 'postnum' => 128,
                    'status' => self::escape(_MD_XSTYLES_PUBLIC),
                ],
            ],
        ]);
        $html = $template->fetch('db:system_comment.tpl');
        $template->assign('xs_fixture_comment_rows', preg_match('/^\s*(?:<!--.*?-->\s*)*<tr\b/is', $html) === 1);
        $template->assign('xs_fixture_html', $html);
    }

    private static function menu(\XoopsTpl $template, string $kind): string
    {
        $template->assign('xoops_isadmin', false);
        $template->assign('block', $kind === 'main-menu' ? [
            'lang_home' => self::escape(_MD_XSTYLES_HOME), 'nothome' => true,
            'modules' => [[
                'name' => self::escape(_MD_XSTYLES_NEWS), 'directory' => 'xstyles', 'highlight' => true,
                'sublinks' => [[
                    'id' => 'events', 'name' => self::escape(_MD_XSTYLES_EVENTS),
                    'url' => '#xs-main-menu', 'full_url' => '#xs-main-menu',
                ]],
            ]],
        ] : [
            'lang_adminmenu' => self::escape(_MD_XSTYLES_ADMIN), 'lang_youraccount' => self::escape(_MD_XSTYLES_ACCOUNT),
            'lang_editaccount' => self::escape(_MD_XSTYLES_EDIT_ACCOUNT), 'lang_notifications' => self::escape(_MD_XSTYLES_NOTIFICATIONS),
            'lang_inbox' => self::escape(_MD_XSTYLES_INBOX), 'lang_logout' => self::escape(_MD_XSTYLES_LOGOUT), 'new_messages' => 3,
        ]);
        $html = $template->fetch($kind === 'main-menu' ? 'db:system_block_mainmenu.tpl' : 'db:system_block_user.tpl');
        // Core menus have fixed IDs; prefix them to coexist with the site's real blocks.
        require_once __DIR__ . '/SpecimenMarkup.php';

        return SpecimenMarkup::attributes($html, static fn (string $name, string $value): string|false|null => match (true) {
            $name === 'id' && in_array($value, ['mainmenu', 'usermenu'], true) => 'xs-fixture-' . $value,
            str_starts_with($name, 'on') => null,
            $name === 'href' => '#xs-' . $kind,
            default => false,
        });
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
