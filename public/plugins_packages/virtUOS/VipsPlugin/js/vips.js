// generic Vips JS functions

function vips_url(url, param_object) {
    return STUDIP.URLHelper.getURL(VIPS_BASE_URL + '/' + url, param_object);
}

jQuery(function() {
    jQuery('.character_input').focus(function(event) {
        vipsCharacterPickerTarget = jQuery(this);
    });

    jQuery('.open_character_picker').click(function(event) {
        openCharacterPicker(jQuery(this).data('language'));
        event.preventDefault();
    });

    jQuery('.vips_display_toggle').click(function(event) {
        jQuery(this).closest('form').find('.display_toggle').toggle();
        event.preventDefault();
    });

    jQuery('.textarea_toggle').click(function(event) {
        var toggle = jQuery(this).closest('.size_toggle');
        var items  = toggle.find('.character_input');

        for (i = 0; i < items.length; i += 2) {
            var name = items[i].name;
            items[i].name = items[i + 1].name;
            items[i + 1].name = name;

            var value = items[i].value;
            items[i].value = items[i + 1].value;
            items[i + 1].value = value;
        }

        toggle.toggleClass('size_large').toggleClass('size_small');
        event.preventDefault();
    });

    jQuery('.add_dynamic_row').click(function(event) {
        var container = jQuery(this).closest('.dynamic_list');
        var template = container.children('.template').last();
        var count = container.children('.dynamic_row').not(template).length;
        var clone = template.clone(true).removeClass('template');

        clone.insertBefore(template);
        clone.find('input[data-name], select[data-name], textarea[data-name]').each(function(i) {
            if (jQuery(this).data('name').indexOf(':') === 0) {
                jQuery(this).data('name', jQuery(this).data('name').substr(1) + '[' + count + ']');
            } else {
                jQuery(this).attr('name', jQuery(this).data('name') + '[' + count + ']');
                jQuery(this).removeAttr('data-name');
            }
        });
        clone.find('input[data-value], select[data-value], textarea[data-value]').each(function(i) {
            if (jQuery(this).data('value').indexOf(':') === 0) {
                jQuery(this).data('value', jQuery(this).data('value').substr(1));
            } else {
                jQuery(this).attr('value', count);
                jQuery(this).removeAttr('data-value');
            }
        });
        clone.children('.add_dynamic_row').click();
        event.preventDefault();
    });

    jQuery('.delete_dynamic_row').click(function(event) {
        jQuery(this).closest('.dynamic_row').remove();
        event.preventDefault();
    });

    jQuery('.assignment_type').change(function(event) {
        if (jQuery(this).val() === 'exam') {
            jQuery('#exam_length').show().find('input').attr('disabled', null);
        } else {
            jQuery('#exam_length').hide().find('input').attr('disabled', 'disabled');
        }

        if (jQuery(this).val() === 'selftest') {
            jQuery('#end_date input').attr('required', null);
            jQuery('#end_date span').removeClass('required');
        } else {
            jQuery('#end_date input').attr('required', 'required');
            jQuery('#end_date span').addClass('required');
        }
    });

    jQuery('.rh_list').sortable({
        item: '> .rh_item',
        tolerance: 'pointer',
        connectWith: '.rh_list',
        update: function(event, ui) {
            if (ui.sender) {
                ui.item.find('input').val(jQuery(this).data('group'));
            }
        },
        over: function(event, ui) {
            jQuery(this).addClass('hover');
        },
        out: function(event, ui) {
            jQuery(this).removeClass('hover');
        },
        receive: function(event, ui) {
            var sortable = jQuery(this);
            var container = sortable.closest('tbody').find('.answer_container');

            // default answer container can have more items
            if (sortable.children().length > 1 && !sortable.is(container)) {
                sortable.find('.rh_item').each(function(i) {
                    if (!ui.item.is(this)) {
                        jQuery(this).find('input').val(-1);
                        jQuery(this).detach().appendTo(container)
                                    .css('opacity', 0).animate({opacity: 1});
                    }
                });
            }
        },
    });
});

// JS character picker
var vipsCharacterPicker = null;
var vipsCharacterPickerTarget = null;

function openCharacterPicker(language) {
    if (vipsCharacterPicker == null) {
        vipsCharacterPicker = jQuery('<div id="character_picker"></div>').appendTo('body');

        // fill character picker via ajax and make it a jQuery dialog
        vipsCharacterPicker.load(vips_url('sheets/get_character_picker_ajax'), function() {
            if (language) {
                vipsCharacterPicker.find('select').val(language).trigger('change');
            }
        }).dialog({
            autoOpen: false,
            position: {at: 'right center'},
            title: 'Zeichenw\u00e4hler',
            width: 600
        });
    }

    if (!vipsCharacterPickerTarget || !vipsCharacterPickerTarget.is(':visible')) {
        vipsCharacterPickerTarget = jQuery('.character_input').filter(':visible').first();
    }

    vipsCharacterPickerTarget.focus();
    vipsCharacterPicker.dialog('open');
}

// insert the chosen character into the target
function insertAtCaret(button) {
    var target    = vipsCharacterPickerTarget[0];
    var character = jQuery.trim(jQuery(button).text()).replace(/\u25cc/g, '');

    if (target) {
        if (window.getSelection || document.getSelection) {  // most browsers
            var caret_start    = Math.min(target.selectionStart, target.selectionEnd);
            var caret_end      = Math.max(target.selectionStart, target.selectionEnd);
            var content_before = target.value.substring(0, caret_start);
            var content_after  = target.value.substring(caret_end);
            var new_content    = content_before + character + content_after;
            var new_caret_pos  = caret_start + character.length;
            target.value = new_content;
            target.focus();
            target.setSelectionRange(new_caret_pos, new_caret_pos);

        } else if (document.selection) {  // IE 7+8
            target.focus();
            var range = document.selection.createRange();
            range.text = character;
            range.select();
        }
    }
}
