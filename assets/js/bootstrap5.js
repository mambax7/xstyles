/* The theme owns Bootstrap. Only the toast needs explicit initialization. */
(() => {
    'use strict';
    function init() {
        const gallery = document.getElementById('xs-gallery');
        if (!gallery) return;
        gallery.querySelectorAll('[data-xs-bootstrap5]').forEach(specimen => {
            if (specimen.dataset.xsBs5Ready) return;
            specimen.dataset.xsBs5Ready = 'true';
            const supported = specimen.dataset.xsBootstrap5.split(' ').every(name => {
                const component = window.bootstrap && window.bootstrap[name];
                return component && /^5\./.test(component.VERSION)
                    && typeof component.getOrCreateInstance === 'function';
            });
            const notice = specimen.querySelector('[data-xs-bs5-unavailable]');
            if (notice) notice.hidden = supported;
            if (!supported) return;
            const trigger = specimen.querySelector('[data-xs-bs5-toast]');
            if (trigger) {
                trigger.addEventListener('click', () => {
                    const toast = specimen.querySelector('.toast');
                    window.bootstrap.Toast.getOrCreateInstance(toast).show();
                });
            }
        });
    }
    // Themes may place their framework bundle after module scripts in the footer.
    if (document.readyState === 'complete') init();
    else window.addEventListener('load', init, { once: true });
})();
