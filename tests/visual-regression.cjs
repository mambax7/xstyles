'use strict';
// Explicit capture and comparison; never updates a baseline implicitly.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const playwright = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const engine = process.env.XSTYLES_BROWSER || 'chromium';
assert.ok(['chromium', 'firefox'].includes(engine), 'Use chromium or firefox');
const [command, first, second] = process.argv.slice(2);
const options = { headless: true, ...(engine === 'chromium' ? { executablePath: process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe' } : {}) };

async function comparePixels(page, before, after) {
    return page.evaluate(async ([a, b]) => {
        const read = async data => {
            const img = new Image(); img.src = 'data:image/png;base64,' + data; await img.decode();
            const canvas = document.createElement('canvas'); canvas.width = img.width; canvas.height = img.height;
            const ctx = canvas.getContext('2d'); ctx.drawImage(img, 0, 0);
            return { canvas, ctx, pixels: ctx.getImageData(0, 0, img.width, img.height) };
        };
        const old = await read(a), current = await read(b);
        if (old.canvas.width !== current.canvas.width || old.canvas.height !== current.canvas.height) {
            return { changed: true, reason: 'Image dimensions changed', ratio: 1 };
        }
        let count = 0;
        const data = current.pixels.data, previous = old.pixels.data;
        for (let i = 0; i < data.length; i += 4) {
            const changed = [0, 1, 2, 3].some(channel => Math.abs(data[i + channel] - previous[i + channel]) > 16);
            if (changed) { count++; data[i] = 255; data[i + 1] = 0; data[i + 2] = 128; data[i + 3] = 255; }
            else { data[i] = data[i + 1] = data[i + 2] = 240; data[i + 3] = 255; }
        }
        current.ctx.putImageData(current.pixels, 0, 0);
        return { ratio: count / (data.length / 4), pixels: count, diff: current.canvas.toDataURL('image/png').split(',')[1] };
    }, [before.toString('base64'), after.toString('base64')]);
}

(async () => {
    const browser = await playwright[engine].launch(options);
    try {
        if (command === 'self-test') {
            const page = await browser.newPage();
            const make = async color => Buffer.from(await page.evaluate(color => {
                const canvas = document.createElement('canvas'); canvas.width = canvas.height = 10;
                const ctx = canvas.getContext('2d'); ctx.fillStyle = color; ctx.fillRect(0, 0, 10, 10);
                return canvas.toDataURL('image/png').split(',')[1];
            }, color), 'base64');
            const white = await make('white');
            assert.equal((await comparePixels(page, white, white)).ratio, 0);
            assert.equal((await comparePixels(page, white, await make('black'))).ratio, 1);
            const small = await page.screenshot({ clip: { x: 0, y: 0, width: 2, height: 2 } });
            assert.equal((await comparePixels(page, white, small)).changed, true);
            console.log('Pixel comparison: equal, changed pixels and changed dimensions passed.');
            return;
        }
        assert.ok(first, 'Usage: visual-regression.cjs capture NEW_DIRECTORY | compare BASELINE CURRENT | self-test');
        if (command === 'compare') {
            assert.ok(second, 'Provide baseline and current capture directories');
            const baseline = JSON.parse(fs.readFileSync(path.join(first, 'manifest.json')));
            const current = JSON.parse(fs.readFileSync(path.join(second, 'manifest.json')));
            assert.equal(baseline.format, 1, 'Unsupported baseline format');
            assert.equal(current.format, 1, 'Unsupported capture format');
            assert.ok(Array.isArray(current.images) && current.images.length > 0 && new Set(current.images).size === current.images.length, 'Empty or duplicate screenshot inventory');
            assert.deepEqual(current.context, baseline.context, 'Capture environments differ; use matching browser, OS, viewport and locale matrices');
            assert.deepEqual(current.images, baseline.images, 'Specimen inventory changed; review before creating a new baseline');
            const tolerance = Number(process.env.XSTYLES_PIXEL_TOLERANCE || '0.001');
            assert.ok(Number.isFinite(tolerance) && tolerance >= 0 && tolerance <= 1, 'Invalid pixel tolerance');
            const page = await browser.newPage(), results = [];
            for (const file of current.images) {
                assert.ok(/^[a-zA-Z0-9_./-]+\.png$/.test(file) && !file.split('/').includes('..') && !path.isAbsolute(file), 'Invalid manifest image path');
                const result = await comparePixels(page, fs.readFileSync(path.join(first, file)), fs.readFileSync(path.join(second, file)));
                const failed = !!result.changed || result.ratio > tolerance;
                results.push({ file, ratio: result.ratio, failed, reason: result.reason });
                if (failed && result.diff) {
                    const target = path.join(second, 'diff', file);
                    fs.mkdirSync(path.dirname(target), { recursive: true }); fs.writeFileSync(target, Buffer.from(result.diff, 'base64'));
                }
            }
            fs.writeFileSync(path.join(second, 'comparison.json'), JSON.stringify({ tolerance, results }, null, 2));
            const failed = results.filter(result => result.failed);
            console.log(`${results.length} screenshots compared; ${failed.length} differences exceed ${tolerance * 100}%.`);
            if (failed.length) { console.error(failed); process.exitCode = 1; }
            return;
        }
        assert.equal(command, 'capture', 'Unknown command');
        assert.ok(process.env.XSTYLES_TEST_URL, 'Set XSTYLES_TEST_URL');
        assert.ok(!fs.existsSync(first), 'Capture directory already exists; use a fresh directory to preserve evidence');
        fs.mkdirSync(first, { recursive: true });
        const themes = (process.env.XSTYLES_TEST_THEMES || 'xbootstrap5,mytheme6').split(',');
        const locales = (process.env.XSTYLES_TEST_LOCALES || 'en,ar').split(',');
        const widths = [360, 768, 1280], modes = ['light', 'dark'];
        const manifest = { format: 1, context: { engine, version: browser.version(), platform: process.platform, themes, locales, widths, modes, scale: 1, hiddenOverlays: ['#scrollUp'] }, images: [], evidence: [] };
        for (const theme of themes) for (const locale of locales) for (const mode of modes) for (const width of widths) {
            assert.ok(/^[a-zA-Z0-9_-]+$/.test(theme) && /^(en|ar)$/.test(locale), 'Unsupported theme or locale name');
            const context = await browser.newContext({ viewport: { width, height: 960 }, deviceScaleFactor: 1, locale, colorScheme: mode, reducedMotion: 'reduce' });
            const page = await context.newPage(), errors = [], writes = [];
            page.on('pageerror', error => errors.push(error.message));
            page.on('request', request => { if (request.method() !== 'GET') writes.push(request.url()); });
            const url = new URL(process.env.XSTYLES_TEST_URL);
            url.search = new URLSearchParams({ section: 'theme', xoops_theme_select: theme, lang: locale, mode, direction: 'theme', zones: 'off' });
            assert.equal((await page.goto(url.href, { waitUntil: 'load' })).status(), 200);
            await page.locator('#xs-gallery').waitFor();
            await page.evaluate(() => document.fonts.ready);
            const actual = await page.evaluate(() => ({ lang: document.documentElement.lang, dir: document.documentElement.dir, mode: document.documentElement.getAttribute('data-bs-theme'), context: document.querySelector('.xs-context').textContent.replace(/\s+/g, ' ').trim(), rtlCss: [...document.styleSheets].map(sheet => sheet.href).filter(href => href?.includes('rtl')) }));
            assert.match(actual.lang, new RegExp(`^${locale}(?:-|$)`, 'i'), 'Actual language negotiation failed');
            assert.equal(actual.dir, locale === 'ar' ? 'rtl' : 'ltr', 'Actual locale direction differs');
            assert.equal(actual.mode, mode, 'Requested colour preview did not apply');
            assert.ok(actual.context.includes(theme), 'Visitor theme did not switch');
            if (locale === 'ar' && theme === 'xbootstrap5') assert.ok(actual.rtlCss.some(url => url.includes('bootstrap.rtl')), 'Real RTL stylesheet not loaded');
            assert.doesNotMatch(await page.locator('body').innerText(), /Warning: Undefined|Fatal error:|Unable to load template/);
            const samples = page.locator('.xs-example[data-xs-section="theme"]');
            assert.equal(await samples.count(), 3);
            const ids = await samples.evaluateAll(nodes => nodes.map(node => node.id));
            for (const id of ids) {
                assert.ok(/^xs-theme-[a-z0-9-]+$/.test(id));
                const relative = `${theme}/${locale}-${mode}-${width}/${id}.png`;
                fs.mkdirSync(path.dirname(path.join(first, relative)), { recursive: true });
                // The host's jQuery scroll-to-top fade is outside the specimen and is not a CSS animation.
                await page.locator('#' + id).screenshot({ path: path.join(first, relative), animations: 'disabled', caret: 'hide', style: '#scrollUp { visibility: hidden !important; }' });
                manifest.images.push(relative);
            }
            assert.deepEqual(errors, []); assert.deepEqual(writes, []);
            manifest.evidence.push({ theme, locale, mode, width, ...actual });
            await context.close();
            console.log(`${engine} ${theme} ${locale}/${actual.dir} ${mode} ${width}: 3 specimens captured.`);
        }
        fs.writeFileSync(path.join(first, 'manifest.json'), JSON.stringify(manifest, null, 2));
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
