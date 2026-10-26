<?php

declare(strict_types=1);

namespace XoopsModules\Xstyles;

/** Request-only navigation and block fixtures; no XOOPS bootstrap or persistence. */
final class Gallery
{
    public static function sections(): array
    {
        return [
            'typography' => '_MD_XSTYLES_SECTION_TYPOGRAPHY',
            'content' => '_MD_XSTYLES_SECTION_CONTENT',
            'tables' => '_MD_XSTYLES_SECTION_TABLES',
            'forms' => '_MD_XSTYLES_SECTION_FORMS',
            'comments' => '_MD_XSTYLES_SECTION_COMMENTS',
            'blocks' => '_MD_XSTYLES_SECTION_BLOCKS',
            'bootstrap5' => '_MD_XSTYLES_SECTION_BOOTSTRAP5',
            'bootstrap6' => '_MD_XSTYLES_SECTION_BOOTSTRAP6',
            'theme' => '_MD_XSTYLES_SECTION_THEME',
        ];
    }

    public static function section(mixed $value): string
    {
        return is_string($value) && isset(self::sections()[$value]) ? $value : 'all';
    }

    public static function choice(mixed $value, array $allowed, string $default): string
    {
        return is_string($value) && in_array($value, $allowed, true) ? $value : $default;
    }

    public static function zoneMode(mixed $value): string
    {
        if ($value === '1') {
            return 'all';
        }

        return self::choice($value, ['off', 'all', 'untitled', 'empty'], 'off');
    }

    /** Pure request data shared by legacy controllers and future XMF routes. */
    public static function state(array $input): array
    {
        return [
            'section' => self::section($input['section'] ?? null),
            'zones' => self::zoneMode($input['zones'] ?? null),
            'mode' => self::choice($input['mode'] ?? null, ['theme', 'light', 'dark', 'auto'], 'theme'),
            'direction' => self::choice($input['direction'] ?? null, ['theme', 'ltr', 'rtl'], 'theme'),
            'renderer' => self::choice($input['renderer'] ?? null, ['theme', 'legacy', 'bootstrap5'], 'theme'),
        ];
    }

    /** Installed public themes from the site's allowed list, never arbitrary paths. */
    public static function themes(array $allowed, string $root): array
    {
        return array_values(array_unique(array_filter(
            $allowed,
            static fn ($name): bool =>
            is_string($name) && preg_match('/^[A-Za-z0-9_-]+$/D', $name)
            && (is_file($root . '/' . $name . '/theme.tpl') || is_file($root . '/' . $name . '/theme.html'))
        )));
    }

    public static function previewState(array $input, array $themes = []): array
    {
        $state = self::state($input) + ['preview_frame' => '1'];
        $theme = self::choice($input['xs_theme'] ?? null, $themes, '');
        if ($theme !== '') {
            $state['xs_theme'] = $theme;
        }

        return $state;
    }

    public static function zoneNames(): array
    {
        return [
            'canvas_left', 'canvas_right', 'page_topleft', 'page_topcenter',
            'page_topright', 'page_bottomleft', 'page_bottomcenter', 'page_bottomright',
            'footer_left', 'footer_center', 'footer_right', 'footer_all',
        ];
    }

    /** Called only for a zone preview; never create real blocks or edit targets. */
    public static function zoneBlocks(string $mode, string $title, string $content): array
    {
        if ($mode === 'off') {
            return [];
        }
        $blocks = [];
        foreach (self::zoneNames() as $zone) {
            $blocks[$zone] = $mode === 'empty' ? [] : [[
                'id' => 'xs-zone-' . $zone,
                'title' => $mode === 'untitled' ? '' : $title . ': ' . $zone,
                'content' => $content . '<p><code>' . $zone . '</code></p>',
                'weight' => 0,
            ]];
        }

        return $blocks;
    }
}
