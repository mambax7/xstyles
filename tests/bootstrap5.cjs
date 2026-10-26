'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const crypto = require('node:crypto');
const { execFileSync } = require('node:child_process');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');

const moduleRoot = path.resolve(__dirname, '..');
const themeRoot = process.env.XSTYLES_BS5_THEME || path.resolve(__dirname, '../../../themes/xbootstrap5');
const bundle = path.join(themeRoot, 'js/bootstrap.bundle.min.js');
const css = path.join(themeRoot, 'css/bootstrap.min.css');
// Pin the reviewed local Bootstrap 5.3.8 assets; never fetch a substitute.
for (const [file, hash] of [
    [bundle, 'e4fd49181388c48ec5040bd3fe66f57c29c8e67fcd8502b3354b96ec7ab47cc7'],
    [css, 'd85327d99c7a3ee1f9b5d0500d1370acea3ad2db39c163c2f51f232baedbdede'],
]) assert.equal(crypto.createHash('sha256').update(fs.readFileSync(file)).digest('hex'), hash, file);

const constants = JSON.parse(execFileSync(process.env.PHP_BINARY || 'php', ['-r',
    "require $argv[1] . '/language/english/main.php'; require_once $argv[1] . '/language/english/bootstrap5.php'; echo json_encode(get_defined_constants(true)['user'], JSON_THROW_ON_ERROR);", moduleRoot], { encoding: 'utf8' }));
const escape = value => value.replace(/[&<>"']/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[character]));
const source = fs.readFileSync(path.join(moduleRoot, 'templates/xstyles_fixture_bootstrap5.tpl'), 'utf8');
// These branches contain only static markup and escaped constants. Real Smarty rendering is covered by fixtures.php.
const specimens = [...source.matchAll(/^<{(?:if|elseif) \$xs_fixture_kind == '([^']+)'}>\r?\n([\s\S]*?)(?=^<{(?:elseif|\/if))/gm)];
assert.equal(specimens.length, 8);
const html = specimens.map(([, id, markup]) => `<section id="xs-${id}" data-xs-specimen>${markup.replace(/<{\$smarty\.const\.([A-Z0-9_]+)\|escape}>/g, (_, key) => {
    assert.equal(typeof constants[key], 'string', key);
    return escape(constants[key]);
})}</section>`).join('\n');
assert.ok(!html.includes('<{'), 'all Smarty expressions accounted for');
assert.ok(!/<(?:script|link|form)\b/i.test(html), 'no framework loads or submittable forms');

async function check(mode) {
    const context = await browser.newContext();
    const page = await context.newPage();
    page.setDefaultTimeout(5000);
    const errors = [];
    const requests = [];
    page.on('pageerror', error => errors.push(error.message));
    await page.route('**/*', route => { requests.push(route.request().url()); return route.abort(); });
    const themeAssets = mode === 'present'
        ? `<style>${fs.readFileSync(css, 'utf8')}</style><script>${fs.readFileSync(bundle, 'utf8')}</script>` : '';
    await page.setContent(`<!doctype html><html><head>${themeAssets}</head><body><main id="xs-gallery">${html}</main></body></html>`);
    if (mode === 'incompatible') {
        // Negative detection only; success cases always use the actual pinned theme bundle.
        await page.evaluate(() => {
            window.bootstrap = Object.fromEntries(['Button', 'Alert', 'Tab', 'Collapse', 'Dropdown', 'Modal', 'Toast'].map(name => [name, {
                VERSION: '4.6.2', getOrCreateInstance() { throw new Error('incompatible component called'); },
            }]));
        });
    }
    await page.addScriptTag({ path: path.join(moduleRoot, 'assets/js/bootstrap5.js') });
    await page.addScriptTag({ path: path.join(moduleRoot, 'assets/js/bootstrap5.js') });
    assert.deepEqual(await page.evaluate(() => {
        const ids = [...document.querySelectorAll('[id]')].map(node => node.id);
        const broken = [];
        document.querySelectorAll('[for], [aria-controls], [aria-labelledby], [aria-describedby]').forEach(node => {
            ['for', 'aria-controls', 'aria-labelledby', 'aria-describedby'].forEach(attribute => {
                (node.getAttribute(attribute) || '').split(/\s+/).filter(Boolean).forEach(id => {
                    if (!document.getElementById(id)) broken.push(id);
                });
            });
        });
        return { duplicateIds: ids.filter((id, index) => ids.indexOf(id) !== index), broken };
    }), { duplicateIds: [], broken: [] });
    assert.equal(await page.locator('[data-xs-bs5-unavailable]:visible').count(), mode === 'present' ? 0 : 6);
    if (mode === 'present') {
        await page.locator('[data-bs-toggle="button"]').click();
        assert.equal(await page.locator('[data-bs-toggle="button"]').getAttribute('aria-pressed'), 'false');
        await page.locator('#xs-bs5-details-tab').focus();
        await page.keyboard.press('ArrowRight');
        await page.waitForFunction(() => document.getElementById('xs-bs5-discussion-tab').getAttribute('aria-selected') === 'true');
        assert.equal(await page.evaluate(() => document.activeElement.id), 'xs-bs5-discussion-tab');
        await page.locator('.accordion-button').click();
        await page.waitForSelector('#xs-bs5-accordion-body.show');
        await page.locator('#xs-bs5-dropdown-trigger').focus();
        await page.keyboard.press('ArrowDown');
        await page.waitForSelector('.dropdown-menu.show');
        assert.ok(await page.evaluate(() => document.activeElement.classList.contains('dropdown-item')));
        await page.keyboard.press('Escape');
        assert.equal(await page.evaluate(() => document.activeElement.id), 'xs-bs5-dropdown-trigger');
        await page.locator('#xs-bs5-modal-trigger').click();
        await page.waitForFunction(() => document.getElementById('xs-bs5-dialog').contains(document.activeElement));
        await page.keyboard.press('Escape');
        await page.waitForFunction(() => document.activeElement.id === 'xs-bs5-modal-trigger');
        assert.equal(await page.locator('.modal-backdrop').count(), 0);
        await page.evaluate(() => {
            window.toastShows = 0;
            document.getElementById('xs-bs5-notification').addEventListener('show.bs.toast', () => window.toastShows++);
        });
        await page.locator('[data-xs-bs5-toast]').click();
        await page.waitForSelector('#xs-bs5-notification.show:not(.showing)');
        await page.evaluate(() => { window.firstToast = bootstrap.Toast.getInstance(document.getElementById('xs-bs5-notification')); });
        await page.locator('#xs-bs5-notification [data-bs-dismiss]').click();
        await page.waitForFunction(() => !document.getElementById('xs-bs5-notification').classList.contains('show'));
        await page.locator('[data-xs-bs5-toast]').click();
        await page.waitForSelector('#xs-bs5-notification.show:not(.showing)');
        assert.deepEqual(await page.evaluate(() => ({ shows: window.toastShows, reused: window.firstToast === bootstrap.Toast.getInstance(document.getElementById('xs-bs5-notification')) })), { shows: 2, reused: true });
        await page.locator('[data-bs-dismiss="alert"]').click();
        await page.waitForFunction(() => !document.querySelector('.alert-danger'));
    } else {
        await page.locator('[data-xs-bs5-toast]').click();
        assert.equal(await page.locator('.toast.show').count(), 0);
    }
    assert.equal(await page.locator('form, button:not([type="button"]), input[name]').count(), 0, 'no submission endpoints or named values');
    assert.deepEqual(requests, [], 'specimens never request framework assets or perform actions');
    assert.deepEqual(errors, [], mode);
    await context.close();
    console.log(`Bootstrap pack ${mode}: passed`);
}

let browser;
(async () => {
    browser = await chromium.launch({ executablePath: process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: true });
    try {
        for (const mode of ['present', 'absent', 'incompatible']) await check(mode);
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
