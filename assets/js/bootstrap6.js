/* Bootstrap 6 is ESM-only; the host theme exposes the components it supports. */
(() => {
    'use strict';
    function init() {
        document.querySelectorAll('#xs-gallery [data-xs-bootstrap6]').forEach(example => {
            const supported = example.dataset.xsBootstrap6.split(' ').every(name => {
                const component = window.bootstrap?.[name];
                return component && /^6\./.test(component.VERSION)
                    && typeof component.getOrCreateInstance === 'function';
            });
            example.querySelector('[data-xs-bs6-unavailable]').hidden = supported;
        });
    }
    if (document.readyState === 'complete') init();
    else window.addEventListener('load', init, { once: true });
})();
