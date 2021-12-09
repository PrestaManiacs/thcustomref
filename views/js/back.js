/**
 * 2006-2021 THECON SRL
 *
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * YOU ARE NOT ALLOWED TO REDISTRIBUTE OR RESELL THIS FILE OR ANY OTHER FILE
 * USED BY THIS MODULE.
 *
 * @author    THECON SRL <contact@thecon.ro>
 * @copyright 2006-2021 THECON SRL
 * @license   Commercial
 */

$(document).ready(function() {
    $("#module_form input")
        .change(function() {
            let OUTPUT = '';
            if($('#THCUSTOMREF_PREFIX_ENABLE_on').is(':checked')) {
                OUTPUT = OUTPUT + $("input[name='THCUSTOMREF_PREFIX']").val();
            }
            OUTPUT = OUTPUT + String(THCUSTOMREF_LAST_ID_ORDER).padStart($("input[name='THCUSTOMREF_DIGITS_NUMBER']").val(), '0');
            if($('#THCUSTOMREF_SUFFIX_ENABLE_on').is(':checked')) {
                OUTPUT = OUTPUT + $("input[name='THCUSTOMREF_SUFFIX']").val();
            }
            $('#thcustomref_preview').html(OUTPUT);
        });
});