<?php

GFForms::include_addon_framework();

class CggffInventory extends GFAddOn
{

    protected $_version = CgGfFeat_VERSION;
    protected $_min_gravityforms_version = '1.0.0';
    protected $_slug = 'cg-gf-feat';
    protected $_path = 'cg-gf-feat/cg-gf-feat.php';
    protected $_full_path = __FILE__;
    protected $_title = 'CG Gravity Form Features';
    protected $_short_title = 'CG GF Features';

    private static $_instance = null;

    public static function get_instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new CggffInventory();
        }

        return self::$_instance;
    }

    public function init()
    {
        parent::init();
        add_filter('gform_field_standard_settings', array($this, 'add_choice_setting'), 10, 2);
        add_action('gform_editor_js', array($this, 'field_settings_scripts'));
        add_filter('gform_tooltips', array($this, 'tooltips'));

        add_filter('gform_pre_render', array($this, 'add_dynamic_choices'));
        add_filter('gform_pre_validation', array($this, 'add_dynamic_choices'));

        add_filter('gform_field_validation', [$this, 'validate_custom_field'], 10, 4);
        add_action('gform_post_payment_completed', [$this, 'afer_payment'], 10, 2);
        add_filter('gform_confirmation', [$this, 'after_confirmation'], 10, 4);

        add_action('admin_print_scripts', [$this, 'admin_script']);
        add_filter('gform_is_value_match', [$this, 'match_conditional_rule'], 10, 6);

        add_action('wp_footer', [$this, 'front_snippets']);
        add_filter('gform_toolbar_menu', [$this, 'inventory_tab'], 10, 2);

        // add_action('admin_menu', array($this, 'create_menu'));
        // add_action('gform_field_settings_tab_content', [$this, 'inventory_tab_content'], 10, 2);
    }

    public function inventory_tab($menu_items, $form_id)
    {
        $menu_items['inventory'] = array(
            'label'        => esc_html__('Inventory', 'gravityforms'),
            'short_label'  => esc_html__('Inventory', 'gravityforms'),
            'aria-label'   => esc_html__('Inventory', 'gravityforms'),
            'icon'         => '<i class="fa fa-pencil-square-o fa-lg"></i>',
            'url'          => '?page=gf_form_inventory&id=' . $form_id,
            'menu_class'   => 'gf_form_toolbar_editor',
            'link_class'   => '',
            'capabilities' => array('gravityforms_edit_forms'),
            'priority'     => 1000,
        );

        return $menu_items;
    }

    public function create_menu()
    {
        add_submenu_page(
            'admin.php?page=gf_edit_forms',
            __('Inventory - Gravity Forms', 'gravityforms'),
            __('Inventory', 'gravityforms'),
            'manage_options',
            'gf_form_inventory',
            array($this, 'inventory_tab_content'),
            2
        );
        // Remove Sub Page To Hide From Admin Menu
        remove_menu_page('gf_edit_forms', 'gf_form_inventory');
    }

    public function inventory_tab_content()
    {

        if (GFCommon::maybe_display_wizard()) {
            return;
        };
?>

        <div class="my-custom-tab-content">
            <h3>My Custom Settings</h3>

            <ul>
                <li class="mysetting_setting field_setting">
                    <label for="mysetting_value">
                        <?php esc_html_e('Custom Setting Value', 'your-textdomain'); ?>
                    </label>

                    <input
                        id="mysetting_value"
                        type="text"
                        class="fieldwidth-3"
                        onkeyup="SetFieldProperty('mysetting_value', this.value);" />

                    <small>Enter something to save into field properties.</small>
                </li>
            </ul>
        </div>

    <?php
    }

    public function front_snippets()
    {
    ?>
        <script>
            function cggffi_compare(a, operator, b) {
                return new Function("a", "b", `return a ${operator} b;`)(a, b);
            }
            gform.addFilter('gform_is_value_match', function(isMatch, formId, rule) {
                if (rule.fieldId && (rule.fieldId).includes('inventory_') && rule.value != "") {
                    const this_value = Number(rule.value);
                    const this_compare = rule.operator;
                    const this_inventory_str = rule.fieldId;
                    const this_inventory_arr = this_inventory_str.split('inventory_');
                    const this_inventory = Number(this_inventory_arr[1]);
                    console.log([this_inventory, this_compare, this_value]);
                    isMatch = cggffi_compare(this_inventory, this_compare, this_value);
                }
                return isMatch;
            });

            jQuery('.submenu-arrow').each(function(ind, elm) {
                const this_btn = jQuery(elm);
                this_btn.on("click", () => {
                    console.log("test");
                    if (this_btn.hasClass("active")) {
                        this_btn.removeClass("active");
                        this_btn.next().removeClass("active");
                    } else {
                        this_btn.addClass("active");
                        this_btn.next().addClass("active");
                    }
                });
            });
        </script>
        <?php
    }

    public function match_conditional_rule($is_match, $field_value, $target_value, $operation, $source_field, $rule)
    {
        // do stuff
        error_log(print_r($rule, true));
        return true; //$is_match;
    }

    public function admin_script()
    {
        if (method_exists('GFForms', 'is_gravity_page') && GFForms::is_gravity_page()) { ?>
            <script type="text/javascript">
                gform.addFilter('gform_conditional_logic_fields', function(options, form, selectedFieldId) {
                    const current_field = GetSelectedField();
                    const this_fields = form.fields;
                    this_fields.forEach(function(field_itm, fld_indx) {
                        if (field_itm.id == current_field.id && field_itm.type == "product" && current_field.choices == null) {
                            options.push({
                                label: 'Available Inventory',
                                value: 'inventory_' + field_itm.inventory
                            });
                        } else if (field_itm.id == current_field.id && field_itm.type == "product" && current_field.choices != null) {
                            const this_choices = field_itm.choices;
                            let total_inv = 0;
                            this_choices.forEach((ch_itm, ch_ind) => {
                                total_inv += Number(ch_itm.inventory);
                                options.push({
                                    label: ch_itm.value + ' - Inventory',
                                    value: 'inventory_' + ch_itm.inventory
                                });
                            });
                            options.push({
                                label: 'Total Inventory',
                                value: 'inventory_' + total_inv
                            });
                        }
                    });
                    return options;
                });
            </script>
        <?php }
    }

    public function after_confirmation($confirmation, $form, $entry, $ajax)
    {
        $this->update_inventory($form, $entry);

        return $confirmation;
    }

    public function update_inventory($form, $entry)
    {
        foreach ($form['fields'] as &$field) {
            if ($field->type === 'product') {
                $qty = rgar($entry, $field->id . '.3');
                if (!$qty) $qty = 1;
                $field->inventory = max(floatval($field->inventory) - $qty, 0);
            }

            if (!empty($field->choices)) {
                $selected = rgar($entry, $field->id);
                $submitted_values = [];
                if (is_array($selected)) {
                    foreach ($selected as $val) {
                        $main_val = explode('|', $val)[0];
                        $submitted_values[] = $main_val;
                    }
                } else {
                    $main_val = explode('|', $selected)[0];
                    $submitted_values[] = $main_val;
                }
                foreach ($field->choices as &$choice) {
                    if (in_array($choice['value'], $submitted_values) && isset($choice['inventory'])) {
                        $choice['inventory'] = max($choice['inventory'] - 1, 0);
                    }
                }
            }
        }
        GFAPI::update_form($form);
    }

    public function add_choice_setting($position, $form_id)
    {
        if ($position == 20) { ?>
            <li class="inventory_setting field_setting">
                <label for="field_inventory" class="section_label">Inventory Limit</label>
                <input type="number" id="field_inventory" class="gform-input gform-input--text" onchange="SetFieldProperty('inventory', this.value);" />
            </li>
        <?php }
    }

    public function tooltips($tooltips) {}

    public function field_settings_scripts()
    {
        ?>
        <style>
            #gfield_settings_choices_container.choice_with_price>label {
                width: calc(35.23% - 3rem) !important;
                display: inline-block;
            }

            #gfield_settings_choices_container.choice_with_value_and_price>label {
                width: calc(26% - 2rem);
            }

            .field-choice-inventory {
                -webkit-appearance: none;
                background: #fff;
                border: 1px solid #c3c5db !important;
                border-radius: 3px;
                box-shadow: 0 0 0 transparent;
                box-sizing: border-box;
                color: #242748;
                font-family: inter, -apple-system, blinkmacsystemfont, "Segoe UI", roboto, oxygen-sans, ubuntu, cantarell, "Helvetica Neue", sans-serif;
                font-size: .8125rem;
                line-height: 2;
                margin: 0;
                min-block-size: 0;
                outline: 0;
                padding: .25rem .75rem;
                transition: box-shadow .15s ease, background-color .15s ease;
                border-radius: 3px !important;
                margin: auto .3125rem !important;
            }
        </style>
        <script>
            fieldSettings.product += ", .inventory_setting";
            jQuery(document).on("gform_load_field_settings", function(event, field) {
                console.log(field.inputType);
                if (field.inputType != "singleproduct" && field.inputType != "calculation") {
                    jQuery('.inventory_setting').hide();
                    return;
                }
                jQuery('.inventory_setting').show();
                jQuery("#field_inventory").val(field["inventory"]);
            });

            jQuery('.choices_setting').on('input propertychange', '.field-choice-inventory', function() {
                var $this = jQuery(this);
                var i = $this.closest('li.field-choice-row').data('index');
                field = GetSelectedField();
                field.choices[i].inventory = $this.val();
            });

            gform.addFilter('gform_append_field_choice_option', function(str, field, i) {
                if (field.type != 'product' || !field.choices || !field.choices.length) {
                    return str;
                }
                var inputType = GetInputType(field);
                var inventory = field.choices[i].inventory ? field.choices[i].inventory : '';
                if (jQuery('#field_choices').prev().hasClass('gfield_choice_header_inventory') === false) {
                    jQuery('#field_choices').before(`<label class="gfield_choice_header_inventory" data-js="choices-ui-label">Inventory</label>`);
                }
                return "<input type='number' id='" + inputType + "_choice_inventory_" + i + "' value='" + inventory + "' class='field-choice-input field-choice-inventory' placeholder='Inventory' />";
            });
        </script>
<?php
    }

    public function init_admin()
    {
        parent::init_admin();
        add_action('gform_field_standard_settings', array($this, 'add_choice_setting'), 10, 2);
        add_action('gform_editor_js', array($this, 'field_settings_scripts'));
    }

    public function add_dynamic_choices($form)
    {
        return $form;
    }

    public function validate_custom_field($result, $value, $form, $field)
    {
        if ($field->type === 'product' && isset($field->inventory) && in_array($field->inputType, ['singleproduct', 'calculation'])) {
            $qty_field = $field->id . '_3';
            $submitted_qty = isset($_POST["input_{$qty_field}"]) ? intval($_POST["input_{$qty_field}"]) : 1;
            $stock = intval($field->inventory);
            if ($submitted_qty > $stock) {
                $result['is_valid'] = false;
                if ($stock <= 0) {
                    $result['message'] = "Item is out of stock.";
                } else {
                    $itms_label = $stock >= 2 ? 'items are' : 'item is';
                    $result['message'] = "Only {$stock} {$itms_label} available.";
                }
            }
        }


        if ($field->type == "quantity") {
            $this_fields = $form['fields'];
            if (!empty($this_fields)) {
                foreach ($this_fields as $this_field) {
                    if ($this_field->type == 'quantity' && $this_field->id == $field->id) {

                        foreach ($this_fields as $sub_field) {
                            if ($sub_field->type == 'product' && $sub_field->id == $this_field->productField) {
                                $inventory_choice = isset($_POST['input_' . $sub_field->id]) ? $_POST['input_' . $sub_field->id] : 1;
                                $inventory_choice_val = explode('|', $inventory_choice)[0];
                                if (!empty($sub_field->choices)) {
                                    foreach ($sub_field->choices as $choice) {
                                        if (!in_array($choice['value'], [$inventory_choice_val])) continue;
                                        if (!isset($choice['inventory'])) continue;

                                        if (intval($choice['inventory']) <= 0) {
                                            $result['is_valid'] = false;
                                            $result['message']  = "This option is out of stock.";
                                            break;
                                        } else if (intval($choice['inventory']) <= $value) {
                                            $result['is_valid'] = false;
                                            $itms_label = $choice['inventory'] >= 2 ? "items are" : 'item is ';
                                            $result['message']  = "Only " . $choice['inventory'] . " $itms_label available.";
                                            break;
                                        }
                                    }
                                } else {
                                    if (intval($sub_field->inventory) <= 0) {
                                        $result['is_valid'] = false;
                                        $result['message']  = "This option is out of stock.";
                                        break;
                                    } else if (intval($sub_field->inventory) <= $value) {
                                        $result['is_valid'] = false;
                                        $itms_label = $sub_field->inventory >= 2 ? "items are" : 'item is ';
                                        $result['message']  = "Only " . $sub_field->inventory . " $itms_label available.";
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }


    function afer_payment($entry, $action)
    {
        if ($action['payment_status'] !== 'Paid') {
            return;
        }

        $form = GFAPI::get_form($entry['form_id']);
        foreach ($form['fields'] as &$field) {
            if ($field->type !== 'product') continue;

            if (isset($field->inventory) && in_array($field->inputType, ['singleproduct', 'calculation'])) {

                $qty = rgar($entry, $field->id . '.3');
                if (!$qty) $qty = 1;

                $field->inventory = max($field->inventory - $qty, 0);
            }

            if (!empty($field->choices)) {
                $selected = rgar($entry, $field->id);
                $submitted_values = [];
                if (is_array($selected)) {
                    foreach ($selected as $val) {
                        $main_val = explode('|', $val)[0];
                        $submitted_values[] = $main_val;
                    }
                } else {
                    $main_val = explode('|', $selected)[0];
                    $submitted_values[] = $main_val;
                }
                foreach ($field->choices as &$choice) {
                    if (in_array($choice['value'], $submitted_values) && isset($choice['inventory'])) {
                        $choice['inventory'] = max($choice['inventory'] - 1, 0);
                    }
                }
            }
        }
        GFAPI::update_form($form);
    }
}
