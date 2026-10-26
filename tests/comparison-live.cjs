'use strict';
// Anonymous, isolated browser profile: never changes site configuration or publishes styles.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const playwright = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const engine = process.env.XSTYLES_BROWSER || 'chromium';
const base = process.env.XSTYLES_TEST_URL;
if (!base) throw new Error('Set XSTYLES_TEST_URL to the public gallery.');
(async () => {
    const browser = await playwright[engine].launch({ headless: true, ...(engine === 'chromium' ? { executablePath: process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe' } : {}) });
    try {
        const context = await browser.newContext({ viewport: { width: 1500, height: 1050 } });
        const page = await context.newPage(), errors = [], writes = [];
        context.on('page', p => p.on('pageerror', error => errors.push(error.message)));
        page.on('pageerror', error => errors.push(error.stack));
        context.on('request', request => { if (request.method() !== 'GET') writes.push(request.url()); });
        const url = params => { const value = new URL(base); value.search = new URLSearchParams(params).toString(); return value.href; };
        await page.goto(url({ section: 'typography', xoops_theme_select: 'xbootstrap5' }));
        assert.equal(await page.locator('.xs-context code').first().textContent(), 'xbootstrap5');
        await page.selectOption('#xs-theme', 'mytheme6');
        await page.locator('.xs-theme-quick button').click();
        await page.waitForFunction(() => document.querySelector('#xs-gallery')?.dataset.xsTheme === 'mytheme6');
        assert.equal(new URL(page.url()).searchParams.get('xs_theme'), 'mytheme6', 'Visible theme selector submits its associated preview form');
        assert.equal(await page.locator('.xs-context code').first().textContent(), 'mytheme6');
        await page.locator('.xs-view-switch [data-xs-view="compare"]').click();
        const ready = async () => page.waitForFunction(() => {
            const controls = document.querySelector('[data-xs-compare]');
            return [...controls.querySelectorAll('[data-xs-compare-status]')].every(node => node.textContent === controls.dataset.xsFrameReady);
        });
        await ready();
        const popupPromise = page.waitForEvent('popup');
        await page.locator('[data-xs-compare-open]').first().click();
        const standalone = await popupPromise;
        await standalone.waitForLoadState();
        assert.equal(new URL(standalone.url()).searchParams.has('preview_frame'), false, 'Open preview must leave embedded mode');
        assert.equal(await standalone.locator('.xs-controls, [data-xs-responsive], [data-xs-compare]').count(), 3);
        await standalone.locator('.xs-preview-notice a').click();
        await standalone.waitForLoadState();
        assert.equal(await standalone.locator('.xs-context code').first().textContent(), 'xbootstrap5');
        assert.equal(await standalone.locator('.xs-controls, [data-xs-responsive], [data-xs-compare]').count(), 3, 'Return to current theme keeps standalone controls');
        await standalone.close();
        assert.equal(new URL(await page.locator('[data-xs-responsive] a[target="_blank"]').getAttribute('href')).searchParams.has('preview_frame'), false, 'Responsive standalone link must leave embedded mode too');
        assert.deepEqual(await page.locator('[data-xs-compare] iframe').evaluateAll(frames => frames.map(f => f.contentWindow.innerWidth)), [768, 768]);
        assert.equal(await page.locator('[data-xs-compare] iframe').evaluateAll(frames => new Set(frames.map(f => f.contentDocument.querySelector('.xs-context code').textContent)).size), 2);
        await page.selectOption('[data-xs-compare-section]', 'forms');
        await page.locator('[data-xs-compare]').evaluate(node => node.scrollIntoView({ block: 'start', behavior: 'instant' }));
        const comparisonScroll = await page.evaluate(() => window.scrollY);
        await ready();
        assert.ok(Math.abs(await page.evaluate(() => window.scrollY) - comparisonScroll) <= 2, 'Loading examples must not scroll the outer page away from comparison controls');
        for (const frame of page.frames().filter(f => f.url().includes('preview_frame=1'))) {
            assert.equal(await frame.locator('.xs-example').count(), 5);
            assert.equal(await frame.locator('[data-xs-compare], [data-xs-responsive], #cssholmes-designer').count(), 0);
        }
        await page.selectOption('[data-xs-compare-width]', '360');
        await ready();
        assert.deepEqual(await page.locator('[data-xs-compare] iframe').evaluateAll(frames => frames.map(f => f.contentWindow.innerWidth)), [360, 360]);
        if (process.env.XSTYLES_SCREENSHOT_DIR) {
            fs.mkdirSync(process.env.XSTYLES_SCREENSHOT_DIR, { recursive: true });
            await page.locator('[data-xs-compare]').screenshot({ path: path.join(process.env.XSTYLES_SCREENSHOT_DIR, `compare-${engine}.png`) });
        }
        await page.setViewportSize({ width: 390, height: 900 });
        assert.equal(await page.locator('[data-xs-compare]').evaluate(node => node.scrollWidth <= node.clientWidth + 2), true);
        await page.locator('[data-xs-compare] > summary').click();
        await page.waitForFunction(() => !document.querySelector('[data-xs-compare] iframe'));
        // A separate normal request must retain the visitor's original theme after both frames ran.
        const normal = await context.newPage();
        await normal.goto(url({ section: 'typography' }));
        assert.equal(await normal.locator('.xs-context code').first().textContent(), 'xbootstrap5');
        await normal.close();
        for (const xs_theme of ['../../private', 'missing', 'default-not-allowed']) {
            await page.goto(url({ section: 'typography', xs_theme }));
            assert.equal(await page.locator('.xs-context code').first().textContent(), 'xbootstrap5');
        }
        await page.goto(url({ section: 'typography', 'xs_theme[]': 'mytheme6' }));
        assert.equal(await page.locator('.xs-context code').first().textContent(), 'xbootstrap5');
        await page.setViewportSize({ width: 1280, height: 960 });
        await page.goto(url({ section: 'bootstrap6', xs_theme: 'mytheme6' }));
        assert.equal(await page.locator('.xs-example').count(), 7);
        assert.equal(await page.locator('[data-xs-bs6-unavailable]:visible').count(), 0);
        await page.locator('#xs-bs6-buttons [data-bs-toggle="button"]').click();
        assert.equal(await page.locator('#xs-bs6-buttons [data-bs-toggle="button"]').getAttribute('aria-pressed'), 'false');
        await page.locator('[data-xs-example-link="xs-bs6-tabs"]').click();
        await page.locator('#xs-bs6-details-tab').focus();
        await page.keyboard.press('ArrowRight');
        await page.waitForFunction(() => document.getElementById('xs-bs6-discussion-tab').getAttribute('aria-selected') === 'true');
        await page.locator('[data-xs-example-link="xs-bs6-menu"]').click();
        await page.locator('#xs-bs6-menu-trigger').click();
        await page.waitForFunction(() => document.getElementById('xs-bs6-menu-trigger').getAttribute('aria-expanded') === 'true');
        await page.keyboard.press('Escape');
        for (const component of ['dialog', 'drawer']) {
            await page.locator(`[data-xs-example-link="xs-bs6-${component}"]`).click();
            await page.evaluate(component => {
                document.getElementById(`xs-bs6-${component}-window`).addEventListener(`shown.bs.${component}`, event => { event.target.dataset.testShown = 'true'; }, { once: true });
            }, component);
            await page.locator(`#xs-bs6-${component}-trigger`).click();
            await page.waitForFunction(id => document.getElementById(id).open && document.getElementById(id).dataset.testShown === 'true', `xs-bs6-${component}-window`);
            await page.keyboard.press('Escape');
            await page.waitForFunction(id => !document.getElementById(id).open, `xs-bs6-${component}-window`);
            assert.equal(await page.evaluate(() => document.activeElement.id), `xs-bs6-${component}-trigger`);
        }
        await page.locator('[data-xs-example-link="xs-bs6-buttons"]').click();
        if (process.env.XSTYLES_SCREENSHOT_DIR) await page.locator('#xs-bs6-buttons').screenshot({ path: path.join(process.env.XSTYLES_SCREENSHOT_DIR, `bootstrap6-${engine}.png`) });
        await page.goto(url({ section: 'bootstrap6', xs_theme: 'xbootstrap5' }));
        assert.equal(await page.locator('[data-xs-bs6-unavailable]:not([hidden])').count(), 5);
        assert.deepEqual(writes, []);
        assert.deepEqual(errors, []);
        console.log(`${engine}: two independent themes, shared section/width, no recursion, narrow layout, invalid choices, unchanged visitor session and Bootstrap 6 interactions/fallback passed.`);
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
