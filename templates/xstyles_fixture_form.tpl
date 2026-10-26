<form action="<{$xs_fixture_url|escape}>/index.php" method="post" data-xs-demo-form="true">
    <fieldset>
        <legend><{$smarty.const._MD_XSTYLES_NATIVE_FORM|escape}></legend>
        <p><label for="xs_native_name"><{$smarty.const._MD_XSTYLES_FIELD_NAME|escape}></label><br><input type="text" id="xs_native_name" name="xs_native_name" value="<{$smarty.const._MD_XSTYLES_SAMPLE_NAME|escape}>" required aria-describedby="xs_native_help"></p>
        <p id="xs_native_help"><small><{$smarty.const._MD_XSTYLES_FIELD_DESCRIPTION|escape}></small></p>
        <p><label for="xs_native_select"><{$smarty.const._MD_XSTYLES_FIELD_SELECT|escape}></label><br><select id="xs_native_select" name="xs_native_select"><option><{$smarty.const._MD_XSTYLES_DRAFT|escape}></option><option selected><{$smarty.const._MD_XSTYLES_PUBLISHED|escape}></option></select></p>
        <p><label for="xs_native_text"><{$smarty.const._MD_XSTYLES_FIELD_TEXTAREA|escape}></label><br><textarea id="xs_native_text" name="xs_native_text" rows="3" cols="40"><{$smarty.const._MD_XSTYLES_BODY_TEXT|escape}></textarea></p>
        <p><label><input type="checkbox" name="xs_native_topic" checked> <{$smarty.const._MD_XSTYLES_NEWS|escape}></label></p>
    </fieldset>
    <fieldset disabled><legend><{$smarty.const._MD_XSTYLES_FIELD_DISABLED|escape}></legend><label for="xs_native_disabled"><{$smarty.const._MD_XSTYLES_FIELD_NAME|escape}></label> <input id="xs_native_disabled" name="xs_native_disabled" value="<{$smarty.const._MD_XSTYLES_DISABLED_VALUE|escape}>"></fieldset>
    <p><button type="submit"><{$smarty.const._MD_XSTYLES_PREVIEW|escape}></button> <button type="reset"><{$smarty.const._MD_XSTYLES_RESET|escape}></button></p>
</form>
