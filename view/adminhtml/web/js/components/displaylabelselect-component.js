define([
    'jquery',
    'Magento_Ui/js/form/element/select'
], function ($, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            customName: '${ $.parentName }.${ $.index }_input'
        },
        /**
         * show/hide position field
         * @param id
         */
        selectOption: function(id){
            if($("#"+id).val() == 1) {
                $('div[data-index="label_text"]').show();
                $('div[data-index="label_attribute"]').show();
                $('div[data-index="label_bg_border_radius"]').show();
                $('div[data-index="label_bg_color"]').show();
                $('div[data-index="label_font_color"]').show();
                $('div[data-index="label_font_size"]').show();
                $('div[data-index="label_padding"]').show();
            } else {
                $('div[data-index="label_text"]').hide();
                $('div[data-index="label_attribute"]').hide();
                $('div[data-index="label_bg_border_radius"]').hide();
                $('div[data-index="label_bg_color"]').hide();
                $('div[data-index="label_font_color"]').hide();
                $('div[data-index="label_font_size"]').hide();
                $('div[data-index="label_padding"]').hide();
            }
        },
    });
});
