<div id="help-template" class="outer">
    <{include file=$smarty.const._MI_XSTYLES_HELP_HEADER}>
    <h4 class="odd">DESCRIPTION</h4><br>
    <div class="even">
    <p>A living style guide for XOOPS themes. Preview how the current theme styles every common page element. Repeatable examples help you check consistency, compare themes, and find layout problems without creating sample content.</p>
    </div>

    <h4 class="odd">INSTALL/UNINSTALL</h4>
    <div class="even">
    <p><strong>Requirements</strong></p>
    <p>XOOPS 2.7 or later and PHP 8.2 or later. The gallery is also tested with XOOPS 2.8 and Smarty 5.</p>
    <p><strong>Install, update, and uninstall</strong></p>
    <p>Copy the xstyles folder into the XOOPS modules directory, then use System Administration &gt; Modules. xStyles has no database tables, custom permissions, upload folders, or preferences. Updates and uninstall callbacks leave historical files and uploads untouched.</p>
    </div>

    <h4 class="odd">OPERATING INSTRUCTIONS</h4><br>
    <div class="even">
    <p><strong>Beginner: explore your theme</strong></p>
    <ol>
        <li>Use <strong>Go to module</strong> to open the frontend gallery, or <strong>Admin styles</strong> to inspect administration themes.</li>
        <li>In <strong>Single preview</strong>, choose a category such as Typography, Tables, or Forms, then select an element in the navigator.</li>
        <li>Use <strong>Find an example</strong> to search the current category. Press <strong>Escape</strong> to clear it; choose <strong>All elements</strong> to search the whole catalog.</li>
        <li>Open <strong>Preview details</strong> to check the theme, language/direction, and form renderer.</li>
    </ol>
    <p>Use sample values only. Example forms do not save records or upload files, and example navigation links stay in the gallery.</p>
    <br>
    <p><strong>Advanced: test and compare themes</strong></p>
    <ul>
        <li><strong>Preview a theme:</strong> choose a frontend theme and click <strong>Apply preview</strong>, or choose an admin-theme link. <strong>Return to current theme</strong> leaves the temporary preview without activating another site-wide theme.</li>
        <li><strong>Compare themes:</strong> click the button at the top of either gallery, choose two themes, then select a shared category and viewport width. Use <strong>Open preview</strong> for a full gallery in its own tab.</li>
        <li><strong>Responsive preview:</strong> in the frontend, test 360, 768, and 1280-pixel widths, or compare all widths. Wider previews can require horizontal scrolling.</li>
        <li><strong>Theme and layout previews:</strong> check form renderers, supported colour modes, direction, and populated or empty block zones. Direction simulation alone does not load an RTL language's stylesheets.</li>
    </ul>
    <p>Frontend theme switching and side-by-side theme comparison require the request-only module-theme override available in XOOPS 2.8. On XOOPS 2.7.4, use the current frontend theme with layout, renderer and responsive previews; admin theme selection and comparison remain available.</p>
    <p>Bootstrap 5 and Bootstrap 6 Alpha examples use the selected theme's assets; xStyles does not install a framework. Bootstrap 6 examples target <code>6.0.0-alpha.1</code>.</p>
    <br>
    <p><strong>Theme designer: inspect and improve</strong></p>
    <ul>
        <li>Expand <strong>Rendered preview HTML</strong> and use <strong>Copy HTML</strong> to inspect an example. If automatic copying is unavailable, copy the selected text with your keyboard. The markup includes gallery adjustments; it is not the original Smarty template.</li>
        <li>With cssHolmes and the appropriate permissions, use <strong>Open theme designer</strong> to preview style changes. <strong>Save to theme</strong> publishes them and can affect visitors.</li>
        <li>Comparison and responsive frames show published styles, not unsaved designer drafts. Use <strong>Refresh previews</strong> after publishing.</li>
        <li>Add custom components to <strong>Theme examples</strong> using the descriptor and template walkthrough in the full tutorial.</li>
    </ul>
    </div>

    <h4 class="odd">TUTORIAL</h4><br>
    <div class="even">
    <p><a href="<{$xoops_url|escape}>/modules/<{$smarty.const._MI_XSTYLES_DIRNAME|escape}>/docs/TUTORIAL.md">Read the illustrated xStyles tutorial (Markdown)</a> for step-by-step beginner, advanced, and theme-designer walkthroughs, screenshots, and troubleshooting. The guide is included in <code>docs/TUTORIAL.md</code>.</p>
    </div>

    <h4 class="odd">TRANSLATIONS</h4><br>
    <p class="even">Translations are on <a href="https://www.transifex.com/xoops/">Transifex</a> and in the <a href="https://github.com/XoopsLanguages/">XOOPS Languages Repository on GitHub</a>.</p>

    <h4 class="odd">SUPPORT</h4><br>
    <p class="even">Visit the <a href="https://xoops.org/modules/newbb/viewforum.php?forum=28/">XOOPS support forum</a> for help.</p>
    <h4 class="odd">DEVELOPMENT</h4><br>
    <div class="even">
        <p>xStyles is open source. Contributions to its examples, documentation, translations, and tests are welcome.</p>
        <p>Explore the XOOPS projects on GitHub:</p>
        <ul>
            <li><a href="https://github.com/XOOPS">XOOPS Core</a></li>
            <li><a href="https://github.com/XoopsModules27x">XOOPS Modules</a></li>
            <li><a href="https://github.com/XoopsThemes">XOOPS Themes</a></li>
        </ul>
        <p>Go check it out, and <strong>GET INVOLVED</strong>.</p>
    </div>
</div>
