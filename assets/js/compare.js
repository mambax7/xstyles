/* Separate documents keep each theme's CSS and request-only selection isolated. */
(() => {
    'use strict';
    function init() {
        const controls = document.querySelector('[data-xs-compare]');
        if (!controls) return;
        const choices = [...controls.querySelectorAll('[data-xs-compare-theme]')];
        const section = controls.querySelector('[data-xs-compare-section]');
        const width = controls.querySelector('[data-xs-compare-width]');
        const panes = [...controls.querySelectorAll('[data-xs-compare-pane]')];
        const timers = new Set();
        const options = [...choices[1].options].filter(option => option.value !== choices[0].value);
        const other = options.find(option => option.value === controls.dataset.xsActiveTheme) || options[0];
        if (other) choices[1].value = other.value;
        function standaloneUrl(value) {
            const url = new URL(value, location.href);
            url.searchParams.delete('preview_frame');
            return url.href;
        }
        function clear() {
            timers.forEach(clearTimeout);
            timers.clear();
            panes.forEach(pane => pane.querySelector('[data-xs-compare-frame]').replaceChildren());
        }
        function render() {
            clear();
            if (!controls.open) return;
            const base = new URL(controls.dataset.xsPreviewUrl, location.href);
            if (base.origin !== location.origin || !/\/(?:index|admin\/gallery)\.php$/.test(base.pathname)
                || base.searchParams.get('preview_frame') !== '1') return;
            const pixels = Number(width.value);
            if (![360, 768, 1280].includes(pixels)) return;
            panes.forEach((pane, index) => {
                const theme = choices[index].value;
                if (!/^[A-Za-z0-9_-]+$/.test(theme)) return;
                const url = new URL(base);
                url.searchParams.set('xs_theme', theme);
                url.searchParams.set('section', section.value);
                const caption = `${theme} — ${pixels} CSS px`;
                pane.querySelector('[data-xs-compare-caption]').textContent = caption;
                pane.querySelector('[data-xs-compare-open]').href = standaloneUrl(url);
                const status = pane.querySelector('[data-xs-compare-status]');
                status.textContent = controls.dataset.xsFrameLoading;
                const frame = document.createElement('iframe');
                frame.title = caption;
                frame.width = String(pixels);
                frame.height = '720';
                frame.style.width = `${pixels}px`;
                frame.style.minWidth = `${pixels}px`;
                const timeout = setTimeout(() => { status.textContent = controls.dataset.xsFrameError; }, 15000);
                timers.add(timeout);
                frame.addEventListener('load', () => {
                    clearTimeout(timeout);
                    timers.delete(timeout);
                    try {
                        const child = frame.contentDocument;
                        const ready = child?.getElementById('xs-gallery')
                            && !child.querySelector('[data-xs-compare], [data-xs-responsive]');
                        status.textContent = ready ? controls.dataset.xsFrameReady : controls.dataset.xsFrameError;
                        if (ready) {
                            const actual = child.getElementById('xs-gallery').dataset.xsTheme;
                            if ([...choices[index].options].some(option => option.value === actual)) {
                                choices[index].value = actual;
                                pane.querySelector('[data-xs-compare-caption]').textContent = `${actual} — ${pixels} CSS px`;
                                pane.querySelector('[data-xs-compare-open]').href = standaloneUrl(frame.contentWindow.location.href);
                            }
                            const example = child.querySelector('.xs-example');
                            if (example) {
                                const view = frame.contentWindow;
                                const margin = parseFloat(view.getComputedStyle(example).scrollMarginBlockStart) || 0;
                                // scrollIntoView also scrolls ancestor frames, hiding the outer toolbar.
                                view.scrollTo({ top: view.scrollY + example.getBoundingClientRect().top - margin, behavior: 'instant' });
                            }
                        }
                    } catch { status.textContent = controls.dataset.xsFrameError; }
                });
                frame.src = url.href;
                pane.querySelector('[data-xs-compare-frame]').append(frame);
            });
        }
        controls.addEventListener('toggle', render);
        controls.addEventListener('change', render);
        controls.querySelector('[data-xs-compare-refresh]').addEventListener('click', render);
        window.addEventListener('pagehide', clear);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
    else init();
})();
