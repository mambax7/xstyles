'use strict';
const assert = require('node:assert/strict');
const playwright = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const engine = process.env.XSTYLES_BROWSER || 'chromium';
if (!process.env.XSTYLES_TEST_URL) throw new Error('Set XSTYLES_TEST_URL to the gallery.');
(async () => {
    const browser = await playwright[engine].launch({ headless: true, ...(engine === 'chromium' ? { executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe' } : {}) });
    try {
        const page = await browser.newPage({ viewport: { width: 2048, height: 800 } });
        const directory = new URL('./', process.env.XSTYLES_TEST_URL);
        await page.goto(new URL('?xoops_theme_select=xbootstrap5', directory).href);
        await page.goto(directory.href);
        assert.equal(await page.locator('.xs-compare-theme').count(), 2, 'The current pane-aligned template must be served at the directory URL');
        const scriptUrl = await page.locator('script[src*="/assets/js/compare.js"]').getAttribute('src');
        const script = await (await page.request.get(new URL(scriptUrl, directory).href)).text();
        assert.match(script, /view\.scrollTo/, 'The served script must scroll only the child window');
        assert.doesNotMatch(script, /\.scrollIntoView\(/);
        const switcher = page.locator('.xs-view-switch [data-xs-view="compare"]');
        const summary = page.locator('[data-xs-compare] > summary');
        await switcher.evaluate(node => window.scrollTo({ top: window.scrollY + node.getBoundingClientRect().top - 150, behavior: 'instant' }));
        const before = await page.evaluate(() => window.scrollY);
        await switcher.click();
        await page.waitForFunction(() => [...document.querySelectorAll('[data-xs-compare-status]')].every(node => node.textContent === 'Published preview loaded'));
        const after = await page.evaluate(() => window.scrollY);
        assert.ok(Math.abs(before - after) <= 2, `Opening comparison moved the parent: ${before} -> ${after}`);
        assert.ok((await summary.boundingBox()).y >= 100, 'Comparison heading remains below the fixed navbar');
        for (const selector of ['[data-xs-compare-section]', '[data-xs-compare-width]', '[data-xs-compare-refresh]']) {
            const box = await page.locator(selector).boundingBox();
            assert.ok(box.y >= 0 && box.y + box.height < 800, 'Shared controls remain on screen');
        }
        console.log(`${engine}: directory URL serves current template/script; opening frontend comparison preserves scroll (${before} -> ${after}) and keeps controls visible.`);
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
