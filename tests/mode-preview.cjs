'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const script = fs.readFileSync(path.join(__dirname, '../assets/js/gallery.js'), 'utf8');
(async () => {
    const browser = await chromium.launch({ headless: true, executablePath: process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe' });
    try {
        const page = await browser.newPage({ colorScheme: 'light' });
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        await page.route('https://mode.test/**', route => route.fulfill({ contentType: 'text/html', body: `<!doctype html><html data-theme="light" data-bs-theme="light"><body><div id="xs-gallery" data-xs-mode="${new URL(route.request().url()).searchParams.get('mode') || 'dark'}" data-xs-mode-attributes="data-theme data-bs-theme data-mt-mode"><p class="xs-status"></p></div><script>${script}</script><script>document.addEventListener('DOMContentLoaded', () => {document.documentElement.setAttribute('data-theme', 'light');document.documentElement.setAttribute('data-bs-theme', 'light');});</script></body></html>` }));
        await page.goto('https://mode.test/');
        for (const attr of ['data-theme', 'data-bs-theme', 'data-mt-mode']) assert.equal(await page.locator('html').getAttribute(attr), 'dark');
        await page.evaluate(() => document.documentElement.setAttribute('data-theme', 'light'));
        await page.waitForFunction(() => document.documentElement.dataset.theme === 'dark');
        assert.equal(await page.evaluate(() => localStorage.length), 0);
        await page.goto('https://mode.test/?mode=auto');
        assert.equal(await page.locator('html').getAttribute('data-mt-mode'), 'light');
        await page.emulateMedia({ colorScheme: 'dark' });
        await page.waitForFunction(() => document.documentElement.dataset.mtMode === 'dark');
        await page.goto('https://mode.test/?mode=theme');
        assert.equal(await page.locator('html').getAttribute('data-mt-mode'), null);
        assert.equal(await page.locator('html').getAttribute('data-theme'), 'light');
        assert.deepEqual(errors, []);
        console.log('Mode previews: late theme initialization, all conventions, system changes, default and no persistence passed.');
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
