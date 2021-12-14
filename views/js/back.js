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
            handlePreview($('#THCUSTOMREF_NUMBER_TO_USE').val());
        });

    manageInputForms();

    $('#THCUSTOMREF_NUMBER_TO_USE').change(function () {
        manageInputForms();
    })

    function manageInputForms() {
        let $THCUSTOMREF_NUMBER_TO_USE = $('#THCUSTOMREF_NUMBER_TO_USE');
        let val = $THCUSTOMREF_NUMBER_TO_USE.val();
        if (val  == 0) {
            $THCUSTOMREF_NUMBER_TO_USE.closest('form').find('#THCUSTOMREF_NEXT_INCREMENT_NUMBER').closest('.form-group').hide();
            $THCUSTOMREF_NUMBER_TO_USE.closest('form').find('#THCUSTOMREF_INCREMENT_SIZE').closest('.form-group').hide();
            handlePreview(0);
        } else if (val  == 1) {
            $THCUSTOMREF_NUMBER_TO_USE.closest('form').find('#THCUSTOMREF_NEXT_INCREMENT_NUMBER').closest('.form-group').show();
            $THCUSTOMREF_NUMBER_TO_USE.closest('form').find('#THCUSTOMREF_INCREMENT_SIZE').closest('.form-group').show();
            handlePreview(1);
        }
    }

    function handlePreview(parameter) {
        let OUTPUT = '';
        let NUMBER;

        if (parameter == 1) {
            let $select_obj = $('#THCUSTOMREF_NEXT_INCREMENT_NUMBER');
            let val = $select_obj.val();
            if (val.length) {
                NUMBER = val;
            } else {
                NUMBER = $select_obj.closest('.form-group').find('p').text().replace('Current number: ', '').trim();
            }
            let increment_size = $('#THCUSTOMREF_INCREMENT_SIZE').val();
            if (increment_size.length) {
                NUMBER = parseInt(NUMBER) + parseInt(increment_size);
            }
        } else {
            NUMBER = THCUSTOMREF_LAST_ID_ORDER;
        }

        if($('#THCUSTOMREF_PREFIX_ENABLE_on').is(':checked')) {
            OUTPUT = OUTPUT + $("input[name='THCUSTOMREF_PREFIX']").val();
        }
        OUTPUT = OUTPUT + String(NUMBER).padStart($("input[name='THCUSTOMREF_DIGITS_NUMBER']").val(), '0');
        if($('#THCUSTOMREF_SUFFIX_ENABLE_on').is(':checked')) {
            OUTPUT = OUTPUT + $("input[name='THCUSTOMREF_SUFFIX']").val();
        }
        $('#thcustomref_preview').html(OUTPUT);
    }
});
