'use strict';
const assert = require('node:assert/strict');
const path = require('node:path');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
if (!process.env.XSTYLES_TEST_URL) throw new Error('Set XSTYLES_TEST_URL to the reviewed gallery URL.');
(async () => {
    const browser = await chromium.launch({ headless: true, executablePath: process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe' });
    try {
        for (const theme of ['xbootstrap5', 'mytheme']) {
            const context = await browser.newContext({ viewport: { width: 1280, height: 900 }, colorScheme: 'light' });
            const page = await context.newPage(), errors = [], writes = [];
            page.on('pageerror', error => errors.push(error.message));
            page.on('request', request => { if (request.method() !== 'GET') writes.push(request.url()); });
            const colors = {};
            for (const mode of ['light', 'dark', 'auto', 'theme']) {
                const url = new URL(process.env.XSTYLES_TEST_URL);
                url.search = new URLSearchParams({ section: 'typography', mode, direction: 'rtl', xoops_theme_select: theme }).toString();
                assert.equal((await page.goto(url.href, { waitUntil: 'load' })).status(), 200);
                assert.match(await page.locator('.xs-context').innerText(), new RegExp(theme));
                assert.equal(await page.locator('html').getAttribute('dir'), 'rtl');
                assert.doesNotMatch(await page.locator('body').innerText(), /Warning:|Fatal error:|Unable to load template/);
                assert.equal(await page.locator('.xs-nav a').evaluateAll(links => links.every(link => !new URL(link.href).searchParams.has('xoops_theme_select'))), true);
                if (mode === 'light' || mode === 'dark') {
                    for (const attribute of ['data-bs-theme', 'data-theme', 'data-mt-mode']) assert.equal(await page.locator('html').getAttribute(attribute), mode);
                    // Wait for the theme's actual transition to settle; compare computed presentation, not attributes alone.
                    await page.waitForFunction(() => !document.getAnimations().some(a => a.playState === 'running' && Number.isFinite(a.effect?.getTiming().iterations)));
                    colors[mode] = await page.locator('body').evaluate(node => getComputedStyle(node).backgroundColor);
                    if (process.env.XSTYLES_SCREENSHOT_DIR) await page.locator('#xs-gallery').screenshot({ path: path.join(process.env.XSTYLES_SCREENSHOT_DIR, `mode-${theme}-${mode}.png`) });
                }
                if (mode === 'auto') {
                    await page.emulateMedia({ colorScheme: 'dark' });
                    await page.waitForFunction(() => document.documentElement.getAttribute('data-mt-mode') === 'dark');
                    await page.emulateMedia({ colorScheme: 'light' });
                    await page.waitForFunction(() => document.documentElement.getAttribute('data-mt-mode') === 'light');
                }
                if (mode === 'theme') {
                    const native = theme === 'mytheme' ? 'data-mt-mode' : 'data-theme';
                    assert.notEqual(await page.locator('html').getAttribute(native), 'dark', 'Preview did not persist');
                }
            }
            assert.notEqual(colors.light, colors.dark, 'Actual theme colours must change');
            await page.locator('.xs-nav a').filter({ hasText: 'Theme examples' }).click();
            if (theme === 'xbootstrap5') assert.equal(await page.locator('.xs-example[data-xs-section="theme"]').count(), 3);
            else assert.match(await page.locator('#xs-gallery').innerText(), /no registered examples/i);
            assert.deepEqual(writes, []); assert.deepEqual(errors, []);
            console.log(`Live ${theme}: light/dark ${JSON.stringify(colors)}, system changes, direction notice, navigation and default restoration passed.`);
            await context.close();
        }
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
