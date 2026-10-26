<?php

declare(strict_types=1);

namespace XoopsModules\Xstyles;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/** Keeps the legacy singleton inside the core-fixture adapter. */
final class LegacyFixtureRenderer
{
    public static function render(string $choice, callable $render): string
    {
        \xoops_load('XoopsFormRenderer');
        $manager = \XoopsFormRenderer::getInstance();
        $original = $manager->get();

        try {
            $class = match ($choice) {
                'legacy' => 'XoopsFormRendererLegacy',
                'bootstrap5' => 'XoopsFormRendererBootstrap5',
                default => null,
            };
            if ($class !== null) {
                \xoops_load($class);
                if (! class_exists($class)) {
                    return '<p>' . htmlspecialchars(_MD_XSTYLES_FIXTURE_UNAVAILABLE, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>';
                }
                $manager->set(new $class());
            }

            return $render();
        } finally {
            $manager->set($original);
        }
    }
}
