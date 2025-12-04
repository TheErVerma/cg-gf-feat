<?php

class GF_Field_CgPhone extends GF_Field
{
    public $type = 'cg_phone';

    public function get_form_editor_field_title()
    {
        return esc_attr__('Phone', 'gravityforms');
    }

    public function get_form_editor_button()
    {
        return array(
            'group' => 'advanced_fields',
            'text'  => $this->get_form_editor_field_title()
        );
    }

    function get_form_editor_field_settings()
    {
        return array(
            'conditional_logic_field_setting',
            'prepopulate_field_setting',
            'error_message_setting',
            'label_setting',
            'label_placement_setting',
            'admin_label_setting',
            'size_setting',
            'rules_setting',
            'visibility_setting',
            'duplicate_setting',
            'default_value_setting',
            'placeholder_setting',
            'description_setting',
            // 'phone_format_setting',
            'css_class_setting',
        );
    }

    public function is_conditional_logic_supported()
    {
        return true;
    }

    public function get_field_input($form, $value = '', $entry = null)
    {
        $form_id         = $form['id'];
        $is_entry_detail = $this->is_entry_detail();
        $id              = (int) $this->id;

        if ($is_entry_detail) {
            $input = "<input type='hidden' id='input_{$id}' name='input_{$id}' value='{$value}' />";

            return $input . '<br/>' . esc_html__('Coupon fields are not editable', 'gravityformscoupons');
        }

        $is_entry_detail = $this->is_entry_detail();
        $is_form_editor  = $this->is_form_editor();
        $is_admin        = $is_entry_detail || $is_form_editor;
        $field_label     = $this->get_field_label($this->label, $value);
        $required_div    = $this->isRequired ? '<span class="gfield_required">' . $this->get_required_indicator() . '</span>' : '';

        $label = !$is_admin ? "<label class='gfield_label gform-field-label' for='input_{$id}_{$form_id}'>{$field_label}{$required_div}</label>" : '';
        $disabled_text         = $this->is_form_editor() ? 'disabled="disabled"' : '';
        $placeholder_attribute = $this->get_field_placeholder_attribute();


        $input = "<div class='ginput_container' id='gf_coupons_container_{$id}_{$form_id}'>" .
            $label .
            "<input id='gf_coupon_code_{$id}_{$form_id}' name='input_{$id}' class='gf_coupon_code' type='text'  {$disabled_text} {$placeholder_attribute} " . $this->get_tabindex() . '/>' .
            "</div>";

        return $input;
    }

    public function get_field_content($value, $force_frontend_label, $form)
    {
        $form_id         = $form['id'];
        $admin_buttons   = $this->get_admin_buttons();
        $is_entry_detail = $this->is_entry_detail();
        $is_form_editor  = $this->is_form_editor();
        $is_admin        = $is_entry_detail || $is_form_editor;
        $field_label     = $this->get_field_label($force_frontend_label, $value);
        $field_id        = $is_admin || $form_id == 0 ? "input_{$this->id}" : 'input_' . $form_id . "_{$this->id}";
        $required_div    = $this->isRequired ? '<span class="gfield_required">' . $this->get_required_indicator() . '</span>' : '';

        $field_content   = ! $is_admin ? '{FIELD}' : $field_content = sprintf("%s<label class='gfield_label gform-field-label' for='%s'>%s{$required_div}</label>{FIELD}", $admin_buttons, $field_id, esc_html($field_label));

        return $field_content;
    }


    public function validate($value, $form)
    {
        $regex = '/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/';
        if ($this->phoneFormat == 'standard' && $value !== '' && $value !== 0 && ! preg_match($regex, $value)) {
            $this->failed_validation = true;
            if (! empty($this->errorMessage)) {
                $this->validation_message = $this->errorMessage;
            }
        }
    }

    public function get_form_inline_script_on_page_render($form)
    {
        $script = '';
        if ($this->phoneFormat == 'standard') {
            $script = "if(!/(android)/i.test(navigator.userAgent)){jQuery('#input_{$form['id']}_{$this->id}').mask('(999) 999-9999').on('keypress', function(e){if(e.which == 13){jQuery(this).blur();} } );}";
        }
        return $script;
    }

    public function get_form_editor_inline_script_on_page_render()
    {
        return "
        gform.addFilter('gform_form_editor_can_field_be_added', function (canFieldBeAdded, type) {
            if (type == 'coupon') {
                if (GetFieldsByType(['product']).length <= 0) {
                    alert(" . json_encode(esc_html__('You must add a Product field to the form', 'gravityformscoupons')) . ");
                    return false;
                } else if (GetFieldsByType(['total']).length  <= 0) {
                    alert(" . json_encode(esc_html__('You must add a Total field to the form', 'gravityformscoupons')) . ");
                    return false;
                } else if (GetFieldsByType(['coupon']).length) {
                    alert(" . json_encode(esc_html__('Only one Coupon field can be added to the form', 'gravityformscoupons')) . ");
                    return false;
                }
            }
            return canFieldBeAdded;
        });";
    }

    public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead)
    {
        
        if ($this->phoneFormat == 'standard' && preg_match('/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/', $value, $matches)) {
            $value = sprintf('(%s) %s-%s', $matches[1], $matches[2], $matches[3]);
        }
        
        return $value;
    }

    public function get_value_merge_tag($value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br)
    {
        $format_modifier = empty($modifier) ? $this->dateFormat : $modifier;

        return GFCommon::date_display($value, $format_modifier);
    }

    public function get_value_entry_detail($value, $currency = '', $use_text = false, $format = 'html', $media = 'screen')
    {
        return $value;
    }
}
GF_Fields::register(new GF_Field_CgPhone());
