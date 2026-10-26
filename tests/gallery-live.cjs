'use strict';
// Read-only public smoke: an isolated browser profile, no site credentials.
const assert = require('node:assert/strict');
const path = require('node:path');
const playwright = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const engine = process.env.XSTYLES_BROWSER || 'chromium';
if (!process.env.XSTYLES_TEST_URL) throw new Error('Set XSTYLES_TEST_URL to the reviewed gallery URL.');
(async () => {
    const browser = await playwright[engine].launch({ headless: true, ...(engine === 'chromium' ? { executablePath: process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe' } : {}) });
    try {
        for (const theme of (process.env.XSTYLES_TEST_THEMES || 'xbootstrap5,mytheme6,argon6').split(',')) {
            const context = await browser.newContext({ viewport: { width: 1280, height: 960 } });
            const page = await context.newPage(), errors = [], writes = [];
            page.on('pageerror', error => errors.push(error.message));
            page.on('request', request => { if (request.method() !== 'GET') writes.push(request.url()); });
            const url = new URL(process.env.XSTYLES_TEST_URL);
            url.search = new URLSearchParams({ section: 'all', zones: 'off', mode: 'theme', direction: 'theme', xoops_theme_select: theme }).toString();
            assert.equal((await page.goto(url.href, { waitUntil: 'load' })).status(), 200);
            await page.locator('#xs-gallery').waitFor();
            assert.equal(await page.locator('#xs-gallery .xs-example:not([data-xs-section="theme"])').count(), 40);
            const themeCount = await page.locator('#xs-gallery .xs-example[data-xs-section="theme"]').count();
            assert.match(await page.locator('.xs-context').textContent(), new RegExp(theme));
            assert.doesNotMatch(await page.locator('body').innerText(), /Warning: Undefined|Fatal error:|Unable to load template/);
            assert.equal(await page.locator('.xs-example:visible').count(), 1);
            assert.equal(await page.evaluate(() => {
                const controls = [...document.querySelectorAll('[data-xs-specimen] button, [data-xs-specimen] input')];
                const properties = ['fontFamily', 'fontSize', 'color', 'backgroundColor', 'borderRadius', 'padding'];
                const snapshot = () => JSON.stringify(controls.map(node => properties.map(key => getComputedStyle(node)[key])));
                const before = snapshot();
                const sheet = [...document.styleSheets].find(sheet => sheet.href?.includes('/xstyles/assets/css/gallery.css'));
                sheet.disabled = true;
                const unchanged = before === snapshot();
                sheet.disabled = false;
                return unchanged;
            }), true, 'Workspace CSS must not restyle the sample controls');
            assert.equal(await page.evaluate(() => {
                const overlay = document.createElement('div');
                overlay.style.cssText = 'position:fixed;top:0;left:0;width:1px;height:1px;pointer-events:none';
                document.querySelector('.xs-example:not([hidden]) [data-xs-specimen]').append(overlay);
                const top = overlay.getBoundingClientRect().top;
                overlay.remove();
                return top;
            }), 0, 'Workspace must not become the containing block for fixed theme overlays');
            await page.locator('[data-xs-responsive] > summary').click();
            await page.selectOption('[data-xs-width]', 'all');
            await page.waitForFunction(() => document.querySelectorAll('[data-xs-frames] iframe').length === 3 && [...document.querySelectorAll('[data-xs-frames] iframe')].every(f => f.contentDocument?.querySelector('#xs-gallery')));
            assert.deepEqual(await page.locator('[data-xs-frames] iframe').evaluateAll(frames => frames.map(f => f.contentWindow.innerWidth)), [360, 768, 1280]);
            for (const frame of page.frames().filter(f => f.url().includes('preview_frame=1'))) {
                assert.match(await frame.locator('.xs-context').textContent(), new RegExp(theme));
                assert.equal(await frame.locator('[data-xs-responsive], #cssholmes-designer').count(), 0);
                assert.equal(await frame.locator('#xs-gallery').evaluate(node => node.parentElement !== document.body), true, 'Full theme wraps gallery');
                assert.doesNotMatch(await frame.locator('body').innerText(), /Warning: Undefined|Fatal error:/);
            }
            if (process.env.XSTYLES_SCREENSHOT_DIR) await page.locator('[data-xs-responsive]').screenshot({ path: path.join(process.env.XSTYLES_SCREENSHOT_DIR, `responsive-${theme}.png`) });
            await page.locator('[data-xs-responsive] > summary').click();
            await page.waitForFunction(() => !document.querySelector('[data-xs-frames] iframe'));
            await page.setViewportSize({ width: 320, height: 900 });
            assert.equal(await page.locator('#xs-gallery').evaluate(node => node.scrollWidth <= node.clientWidth + 2), true, 'Gallery controls overflow');
            assert.deepEqual(writes, []); assert.deepEqual(errors, []);
            console.log(`Live ${theme}: 40 built-ins + ${themeCount} theme samples, full theme frames, inherited theme, widths, no PHP/JS errors or writes passed.`);
            await context.close();
        }
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
