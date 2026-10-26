/* Fixed same-origin viewports; published styles only, no editor or cross-frame messages. */
(() => {
    'use strict';
    function init() {
        const controls = document.querySelector('[data-xs-responsive]');
        if (!controls) return;
        const tray = controls.querySelector('[data-xs-frames]');
        const choice = controls.querySelector('[data-xs-width]');
        const timers = new Set();
        const clear = () => { timers.forEach(clearTimeout); timers.clear(); tray.replaceChildren(); };
        function render() {
            clear();
            if (!controls.open) return;
            const url = new URL(controls.dataset.xsPreviewUrl, location.href);
            if (url.origin !== location.origin || !url.pathname.endsWith('/index.php') || url.searchParams.get('preview_frame') !== '1') return;
            const widths = choice.value === 'all' ? [360, 768, 1280] : [Number(choice.value)];
            widths.filter(width => [360, 768, 1280].includes(width)).forEach(width => {
                const figure = document.createElement('figure');
                const caption = document.createElement('figcaption');
                const status = document.createElement('span');
                status.setAttribute('role', 'status');
                status.textContent = controls.dataset.xsFrameLoading;
                caption.append(`${width} CSS px — `, status);
                const frame = document.createElement('iframe');
                frame.title = `${controls.dataset.xsFrameTitle}: ${width} CSS px`;
                frame.width = String(width);
                frame.height = '720';
                frame.style.width = `${width}px`;
                frame.style.minWidth = `${width}px`;
                frame.src = url.href;
                const timeout = setTimeout(() => { status.textContent = controls.dataset.xsFrameError; }, 15000);
                timers.add(timeout);
                frame.addEventListener('load', () => {
                    clearTimeout(timeout); timers.delete(timeout);
                    try {
                        const child = frame.contentDocument;
                        status.textContent = child?.getElementById('xs-gallery') && !child.querySelector('[data-xs-responsive]')
                            ? controls.dataset.xsFrameReady : controls.dataset.xsFrameError;
                    } catch { status.textContent = controls.dataset.xsFrameError; }
                });
                figure.append(caption, frame);
                tray.append(figure);
            });
        }
        controls.addEventListener('toggle', render);
        choice.addEventListener('change', render);
        controls.querySelector('[data-xs-refresh]').addEventListener('click', render);
        window.addEventListener('pagehide', clear);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
    else init();
})();
