/* No saved preferences or backend actions: every preview belongs to this page. */
(() => {
    'use strict';
    function init() {
        const gallery = document.getElementById('xs-gallery');
        if (!gallery) return;
        const root = document.documentElement;
        const mode = gallery.dataset.xsMode;
        if (['light', 'dark', 'auto'].includes(mode)) {
            const attributes = (gallery.dataset.xsModeAttributes || 'data-bs-theme data-theme data-mt-mode')
                .split(/\s+/).filter(name => /^data-[a-z][a-z0-9-]{0,47}$/.test(name)).slice(0, 4);
            const preference = window.matchMedia('(prefers-color-scheme: dark)');
            const applyMode = () => {
                const selected = mode === 'auto' ? (preference.matches ? 'dark' : 'light') : mode;
                attributes.forEach(name => {
                    if (root.getAttribute(name) !== selected) root.setAttribute(name, selected);
                });
                if (root.style.colorScheme !== selected) root.style.colorScheme = selected;
            };
            // A requested preview owns these attributes even when a theme initializes later.
            const observer = new MutationObserver(applyMode);
            const observe = () => {
                applyMode();
                if (attributes.length) observer.observe(root, { attributes: true, attributeFilter: attributes });
            };
            observe();
            if (mode === 'auto') preference.addEventListener('change', applyMode);
            window.addEventListener('pagehide', () => observer.disconnect());
            window.addEventListener('pageshow', observe);
        }
        const direction = gallery.dataset.xsDirection;
        if (direction === 'ltr' || direction === 'rtl') root.dir = direction;
        const status = gallery.querySelector('.xs-status');
        const workspace = gallery.hasAttribute('data-xs-workspace');
        const comparison = gallery.querySelector('[data-xs-compare]');
        if (workspace) {
            gallery.classList.add('xs-enhanced');
            const switcher = gallery.querySelector('.xs-view-switch');
            switcher.hidden = false;
            const updateView = () => {
                const view = comparison?.open ? 'compare' : 'preview';
                gallery.dataset.xsView = view;
                switcher.querySelectorAll('button').forEach(button => {
                    button.setAttribute('aria-pressed', String(button.dataset.xsView === view));
                });
                if (view === 'compare') {
                    const responsive = gallery.querySelector('[data-xs-responsive]');
                    if (responsive) responsive.open = false;
                }
            };
            switcher.addEventListener('click', event => {
                const button = event.target.closest('button');
                if (button && comparison) {
                    comparison.open = button.dataset.xsView === 'compare';
                    updateView();
                }
            });
            comparison?.addEventListener('toggle', updateView);
            updateView();
        }
        const filter = gallery.querySelector('#xs-filter');
        if (filter) {
            const examples = [...gallery.querySelectorAll('.xs-example')].map(node => ({
                node, text: node.textContent.toLocaleLowerCase(),
            }));
            const count = gallery.querySelector('#xs-filter-count');
            const empty = gallery.querySelector('.xs-filter-empty');
            const links = [...gallery.querySelectorAll('[data-xs-example-link]')];
            let selected = location.hash.slice(1) || examples[0]?.node.id;
            const applyFilter = () => {
                const query = filter.value.trim().toLocaleLowerCase();
                const matches = examples.filter(example => example.text.includes(query));
                if (!matches.some(example => example.node.id === selected)) selected = matches[0]?.node.id;
                examples.forEach(example => {
                    example.node.hidden = workspace ? example.node.id !== selected : !example.text.includes(query);
                });
                links.forEach(link => {
                    link.hidden = !matches.some(example => example.node.id === link.dataset.xsExampleLink);
                    if (link.dataset.xsExampleLink === selected) link.setAttribute('aria-current', 'true');
                    else link.removeAttribute('aria-current');
                });
                count.textContent = `${matches.length} / ${examples.length}`;
                empty.hidden = matches.length > 0;
            };
            links.forEach(link => link.addEventListener('click', event => {
                event.preventDefault();
                selected = link.dataset.xsExampleLink;
                history.replaceState(null, '', '#' + selected);
                applyFilter();
            }));
            filter.closest('.xs-filter-controls').hidden = false;
            filter.addEventListener('input', applyFilter);
            filter.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    event.preventDefault();
                    filter.value = '';
                    applyFilter();
                }
            });
            window.addEventListener('hashchange', () => {
                const target = document.getElementById(location.hash.slice(1));
                const example = target && target.closest('.xs-example');
                if (example && example.hidden) {
                    selected = example.id;
                    filter.value = '';
                    applyFilter();
                    target.scrollIntoView();
                }
            });
            applyFilter();
        }
        gallery.addEventListener('submit', event => {
            if (event.target.closest('[data-xs-specimen]')) {
                event.preventDefault();
                status.textContent = gallery.dataset.xsDemoNotice;
            }
        }, true);
        // Themes may add block-edit controls; synthetic zones have no database ID.
        document.querySelectorAll('a[href*="bid=xs-zone-"]').forEach(link => {
            link.removeAttribute('href');
            link.setAttribute('aria-disabled', 'true');
        });
        gallery.querySelectorAll('[data-xs-copy]').forEach(button => {
            button.hidden = false;
            button.addEventListener('click', async () => {
                const source = document.getElementById(button.dataset.xsCopy);
                try {
                    if (!navigator.clipboard || !window.isSecureContext) throw new Error('clipboard unavailable');
                    await navigator.clipboard.writeText(source.textContent);
                    button.nextElementSibling.textContent = gallery.dataset.xsCopied;
                } catch {
                    const range = document.createRange();
                    range.selectNodeContents(source);
                    const selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);
                    source.closest('pre').focus();
                    button.nextElementSibling.textContent = gallery.dataset.xsCopyFailed;
                }
            });
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
    else init();
})();
