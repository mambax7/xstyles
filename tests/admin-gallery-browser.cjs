'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');
const playwright = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const core = path.resolve(process.env.XSTYLES_CORE || path.join(__dirname, '../../..'));
const moduleRoot = path.resolve(__dirname, '..');
const rendered = JSON.parse(execFileSync(process.env.PHP_BINARY || 'php', [path.join(__dirname, 'admin-gallery.php'), core, '--json'], { encoding: 'utf8', maxBuffer: 8 * 1024 * 1024 }));
const home = execFileSync(process.env.PHP_BINARY || 'php', [path.join(__dirname, 'admin-home.php'), core, '--html'], { encoding: 'utf8' });
const engine = process.env.XSTYLES_BROWSER || 'chromium';
assert.ok(['chromium', 'firefox'].includes(engine));
(async () => {
    const browser = await playwright[engine].launch({ headless: true, ...(engine === 'chromium' ? { executablePath: process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe' } : {}) });
    try {
        const page = await browser.newPage({ viewport: { width: 1280, height: 960 } }), errors = [], writes = [];
        page.on('pageerror', error => errors.push(error.message));
        page.on('request', request => { if (request.method() !== 'GET') writes.push(request.url()); });
        await page.addInitScript(() => Object.defineProperty(navigator, 'clipboard', { value: { writeText: async text => { window.copiedSource = text; } } }));
        await page.context().route('**/*', async route => {
            const url = new URL(route.request().url());
            if (url.pathname === '/modules/xstyles/admin/index.php') {
                return route.fulfill({ contentType: 'text/html', body: `<!doctype html><html lang="en"><head><meta charset="utf-8"><link rel="stylesheet" href="/modules/system/themes/modern/css/modern.css"><link rel="stylesheet" href="/modules/system/themes/modern/css/xoops.css"><link rel="stylesheet" href="/Frameworks/moduleclasses/moduleadmin/css/admin.css"><link rel="stylesheet" href="/modules/xstyles/assets/css/admin-home.css"></head><body><main class="content-wrapper">${home}</main></body></html>` });
            }
            if (url.pathname === '/modules/xstyles/admin/gallery.php') {
                const theme = url.searchParams.get('xs_theme') || 'modern';
                const section = url.searchParams.get('section') || 'all';
                const html = rendered[`${theme}:${section}:${url.searchParams.has('preview_frame') ? 'child' : 'parent'}`] || rendered.all;
                const css = theme === 'modern' ? ['modern.css', 'xoops.css', 'fixes.css'] : [];
                return route.fulfill({ contentType: 'text/html', body: `<!doctype html><html lang="en" dir="ltr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">${css.map(file => `<link rel="stylesheet" href="/modules/system/themes/${theme}/css/${file}">`).join('')}<link rel="stylesheet" href="/modules/xstyles/assets/css/gallery.css"></head><body><main class="content-wrapper">${html}</main><script src="/modules/xstyles/assets/js/gallery.js"></script><script src="/modules/xstyles/assets/js/compare.js"></script></body></html>` });
            }
            const pathname = decodeURIComponent(url.pathname);
            const moduleAsset = pathname.startsWith('/modules/xstyles/');
            const assetRoot = moduleAsset ? moduleRoot : core;
            const file = path.resolve(assetRoot, moduleAsset ? pathname.slice('/modules/xstyles/'.length) : '.' + pathname);
            if (!file.startsWith(assetRoot + path.sep) || !fs.existsSync(file) || !fs.statSync(file).isFile()) return route.fulfill({ status: 404 });
            const type = { '.css': 'text/css', '.js': 'text/javascript', '.png': 'image/png' }[path.extname(file)] || 'application/octet-stream';
            return route.fulfill({ contentType: type, body: fs.readFileSync(file) });
        });
        await page.goto('https://example.invalid/modules/xstyles/admin/gallery.php');
        assert.equal(await page.locator('.xs-example').count(), 21);
        assert.equal(await page.locator('[data-xs-workspace]').count(), 1, 'Parent gallery uses the shared workspace');
        assert.equal(await page.locator('.xs-example:visible').count(), 1, 'Workspace focuses one example');
        assert.equal(await page.locator('[data-xs-responsive], iframe, .xs-controls').count(), 0);
        await page.locator('#xs-filter').fill('semantic table');
        assert.ok(await page.locator('.xs-example:visible').count() < 21);
        await page.locator('#xs-filter').press('Escape');
        assert.equal(await page.locator('.xs-example:visible').count(), 1);
        await page.locator('.xs-nav a[href*="section=forms"]').click();
        await page.waitForFunction(() => document.querySelectorAll('.xs-example').length === 5);
        assert.equal(await page.locator('.xs-example').count(), 5);
        await page.locator('.xs-source > summary').first().click();
        for (const selector of ['.xs-source pre', '[data-xs-copy]']) {
            assert.ok(await page.locator(selector).first().evaluate(node => parseFloat(getComputedStyle(node).borderTopLeftRadius) >= 6), 'Source panels and buttons share rounded corners');
        }
        await page.locator('[data-xs-copy]').first().click();
        assert.ok((await page.evaluate(() => window.copiedSource)).includes('<form'));
        if (process.env.XSTYLES_SCREENSHOT_DIR) {
            fs.mkdirSync(process.env.XSTYLES_SCREENSHOT_DIR, { recursive: true });
            await page.locator('#xs-gallery').screenshot({ path: path.join(process.env.XSTYLES_SCREENSHOT_DIR, `admin-${engine}-workspace.png`) });
        }
        await page.locator('[data-xs-specimen] form').first().evaluate(form => form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true })));
        assert.match(page.url(), /admin\/gallery\.php\?section=forms/);
        await page.locator('.xs-view-switch [data-xs-view="compare"]').click();
        await page.waitForFunction(() => [...document.querySelectorAll('[data-xs-compare-status]')].every(node => node.textContent === 'Published preview loaded'));
        assert.deepEqual(await page.locator('[data-xs-compare] iframe').evaluateAll(frames => frames.map(frame => frame.contentWindow.innerWidth)), [768, 768]);
        assert.deepEqual(await page.locator('[data-xs-compare] iframe').evaluateAll(frames => frames.map(frame => frame.contentDocument.querySelector('.xs-context code').textContent)), ['modern', 'default']);
        await page.selectOption('[data-xs-compare-theme="1"]', 'dark');
        await page.waitForFunction(() => document.querySelectorAll('[data-xs-compare] iframe')[1]?.contentDocument?.querySelector('.xs-context code')?.textContent === 'dark');
        for (const frame of page.frames().filter(frame => frame.url().includes('preview_frame=1'))) {
            assert.equal(await frame.locator('[data-xs-compare]').count(), 0);
            assert.equal(await frame.locator('.xs-example').count(), 5);
        }
        const second = await page.locator('[data-xs-compare] iframe').nth(1).elementHandle();
        const child = await second.contentFrame();
        await child.locator('.xs-preview-notice a').click();
        await page.waitForFunction(() => document.querySelectorAll('[data-xs-compare-caption]')[1].textContent.startsWith('modern'));
        assert.ok(child.url().includes('preview_frame=1'));
        assert.equal(await child.locator('[data-xs-compare]').count(), 0, 'Reset cannot enable nested comparisons');
        const popupPromise = page.waitForEvent('popup');
        await page.locator('[data-xs-compare-open]').nth(1).click();
        const standalone = await popupPromise;
        await standalone.waitForLoadState();
        assert.equal(new URL(standalone.url()).searchParams.has('preview_frame'), false, 'Admin standalone preview must leave embedded mode');
        assert.equal(await standalone.locator('[data-xs-compare]').count(), 1);
        await standalone.close();
        if (process.env.XSTYLES_SCREENSHOT_DIR) {
            fs.mkdirSync(process.env.XSTYLES_SCREENSHOT_DIR, { recursive: true });
            await page.locator('[data-xs-compare]').screenshot({ path: path.join(process.env.XSTYLES_SCREENSHOT_DIR, `admin-${engine}-compare.png`) });
        }
        await page.setViewportSize({ width: 360, height: 960 });
        assert.equal(await page.locator('#xs-gallery').evaluate(node => node.scrollWidth <= node.clientWidth + 2), true);
        await page.locator('[data-xs-compare] > summary').click();
        await page.waitForFunction(() => !document.querySelector('[data-xs-compare] iframe'));
        if (process.env.XSTYLES_SCREENSHOT_DIR) {
            fs.mkdirSync(process.env.XSTYLES_SCREENSHOT_DIR, { recursive: true });
            await page.locator('#xs-theme-form').screenshot({ path: path.join(process.env.XSTYLES_SCREENSHOT_DIR, `admin-${engine}-forms.png`) });
        }
        await page.setViewportSize({ width: 1000, height: 1000 });
        await page.goto('https://example.invalid/modules/xstyles/admin/index.php');
        assert.equal(await page.locator('#xo-modadmin-index').count(), 1);
        assert.match(await page.locator('body').innerText(), /Home[\s\S]*Configuration Check[\s\S]*Server status/);
        assert.ok(await page.locator('.rmmenuicon a').count() >= 3, 'Standard About, admin examples and Help tiles');
        assert.equal(await page.locator('.rmmenuicon img').evaluateAll(images => images.every(image => image.complete && image.naturalWidth > 0)), true, 'Dashboard icons must load');
        assert.equal(await page.locator('.rmmenuicon a').evaluateAll(tiles => tiles.every(tile => tile.scrollHeight <= tile.clientHeight + 1)), true, 'Dashboard tile labels must fit');
        if (process.env.XSTYLES_SCREENSHOT_DIR) await page.screenshot({ path: path.join(process.env.XSTYLES_SCREENSHOT_DIR, `admin-${engine}-home.png`), fullPage: true });
        assert.deepEqual(errors, []); assert.deepEqual(writes, []);
        if (process.env.XSTYLES_TEST_URL) {
            const anonymous = await browser.newPage();
            const response = await anonymous.goto(new URL('admin/gallery.php', process.env.XSTYLES_TEST_URL).href);
            assert.equal(response.status(), 200);
            assert.equal(await anonymous.locator('#xs-gallery').count(), 0, 'Anonymous visitors must not receive the live admin gallery');
            await anonymous.close();
        }
        console.log(`${engine} admin: Home dashboard/configuration/server status, real controller/Smarty, Modern CSS, section links, filter, copy, inert forms and narrow viewport passed.`);
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
