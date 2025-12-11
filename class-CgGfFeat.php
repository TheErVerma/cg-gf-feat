<?php

GFForms::include_addon_framework();

class CgGfFeat extends GFAddOn
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
            self::$_instance = new CgGfFeat();
        }

        return self::$_instance;
    }

    public function init()
    {
        parent::init();
        add_filter('gform_submit_button', array($this, 'form_submit_button'), 10, 2);


        add_filter('gform_field_standard_settings', array($this, 'add_choice_setting'), 10, 2);
        add_filter('gform_tooltips', array($this, 'tooltips'));
        // Register dynamic choices filters from inside class
        add_filter('gform_pre_render', array($this, 'add_dynamic_choices'));
        add_filter('gform_pre_validation', array($this, 'add_dynamic_choices'));
    }

    public function add_choice_setting($position, $form_id)
    {
        // Insert just after "choices" (position 1600)
        if ($position == 100) { ?>
            <div class="cggffeat_fields_wrapper">
                <li class="cggffeat_dynamic_source_setting field_setting">
                    <label for="cggffeat_dynamic_source_setting" class="section_label">
                        Dynamic Source
                        <?php gform_tooltip('cggffeat_dynamic_source_setting'); ?>
                    </label>

                    <select id="cggffeat_dynamic_source_setting" class="fieldwidth-3" onChange="SetFieldProperty('cggffeat_dynamic_source', this.value);">
                        <option value="">Select Source</option>
                        <option value="post">Post</option>
                        <option value="taxonomy">Taxonomy</option>
                    </select>
                </li>

                <li class="cggffeat_dynamic_type_setting field_setting">
                    <label for="cggffeat_dynamic_type_setting" class="section_label">
                        Dynamic Option Type
                        <?php gform_tooltip('cggffeat_dynamic_type_setting'); ?>
                    </label>

                    <select id="cggffeat_dynamic_type_setting" class="fieldwidth-3" onChange="SetFieldProperty('cggffeat_type_source', this.value);">
                    </select>
                </li>

                <li class="cggffeat_dc_fitlers_json field_setting">
                    <div class="cggffeat_dc_fitlers_wrapper">
                        <label for="cggffeat_dc_fitlers_json" class="section_label">Filters</label>
                        <div class="cggffeat_dc_fitlers">
    
                            <div class="cggffeat_dc_fitler_group">
    
                                <div class="cggffeat_dcf_itm">
                                    <div class="cggffeat_dcf_head">
                                        <select name="cggffeat_fsource" class="filter_source">
                                            <option value="id">ID</option>
                                            <option value="title">Title</option>
                                            <option value="desc">Description</option>
                                        </select>
                                        <select name="cggffeat_fcompare" class="filter_compare">
                                            <option value="=">Equal to</option>
                                            <option value="!=">Not equal to</option>
                                            <option value="contain">Contain</option>
                                            <option value="not_contain">Not Contain</option>
                                            <option value=">=">Greater then equal</option>
                                            <option value=">">Greater then</option>
                                            <option value="<=">Less then equal</option>
                                            <option value="<">Less then</option>
                                        </select>
                                    </div>
                                    <div class="cggffeat_dcf_body">
                                        <select name="cggffeat_fvalue_type" class="filter_value_type">
                                            <option value="">Equal to</option>
                                            <option value="custom">Custom Value</option>
                                        </select>
                                        <input type="text" name="cggffeat_fvalue" placeholder="Value">
                                    </div>
                                </div>
                                <div class="cggffeat_dcf_actions">
                                    <button type="button" class="button button-small add_filter">Add</button>
                                    <button type="button" class="button button-small remove_filter" disabled="disabled">Remove</button>
                                </div>
                            </div>
                        </div>
    
                        <button type="button" class="button button-small add_filter_group">Add -or- Group</button>
                    </div>
                    <input type="hidden" name="cggffeat_dc_fitlers_json" id="cggffeat_dc_fitlers_json" onChange="SetFieldProperty('cggffeat_dc_fitlers_json', this.value);" />
                    </select>
                </li>
            </div>
        <?php
        }
    }

    public function tooltips($tooltips)
    {
        $tooltips['dynamic_option_setting'] = 'Unique key used to load dynamic choices.';
        return $tooltips;
    }

    public function field_settings_scripts()
    {
        $source_types = ['post', 'taxonomy'];
        $opt_content_obj = [];
        if (!empty($source_types)) {
            foreach ($source_types as $source) {
                $opt_content_obj[$source] = [];
                if (!empty($source)) {
                    $options = [];
                    if ($source == 'post') {
                        $all_types = get_post_types(['public' => true], 'objects');
                        if (!empty($all_types)) {
                            foreach ($all_types as $all_type) {
                                $options[$all_type->name] = $all_type->label;
                            }
                        }
                    } else if ($source == 'taxonomy') {
                        $all_types = get_taxonomies(['public' => true], 'objects');
                        if (!empty($all_types)) {
                            foreach ($all_types as $all_type) {
                                $options[$all_type->name] = $all_type->label;
                            }
                        }
                    }
                    $opt_content_obj[$source] = $options;
                }
            }
        }

        ?>
        <style>
            .cggffeat_dc_fitlers_wrapper {
                padding: 15px;
                border: 1px solid #ccc;
                border-radius: 4px;
                position: relative;
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin: 24px 0px 16px;
            }

            .cggffeat_dc_fitlers_wrapper>label {
                background: #fff;
                padding: 0px 8px;
                position: absolute;
                top: -7px;
                left: 10px;
            }

            .cggffeat_dc_fitlers {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .cggffeat_dc_fitlers_wrapper .button-small {
                height: 26px;
            }

            .cggffeat_dcf_itm {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .cggffeat_dcf_itm .cggffeat_dcf_head {
                display: grid;
                grid-template-columns: 7fr 5fr;
                gap: 10px;
            }

            .cggffeat_dcf_itm .cggffeat_dcf_body {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            [name="cggffeat_fvalue_type"] {
                display: none;
            }

            span.cggffeat_dcf_sap {
                text-align: center;
                display: block;
                position: relative;
                color: #ccc;
                font-size: 11px;
            }

            span.cggffeat_dcf_sap:after {
                content: '';
                width: 50px;
                height: 1px;
                background: #ddd;
                position: absolute;
                top: 8px;
                left: -100px;
                right: 0;
                margin: auto;
            }

            span.cggffeat_dcf_sap:before {
                content: '';
                width: 50px;
                height: 1px;
                background: #ddd;
                position: absolute;
                top: 8px;
                left: 0;
                right: -100px;
                margin: auto;
            }

            .cggffeat_dc_fitler_group {
                background: #f5faff;
                border: 1px solid #ccc;
                border-radius: 4px;
                padding: 14px;
            }

            .cggffeat_dcf_itm:not(:first-child) {
                position: relative;
                margin-top: 40px;
            }

            .cggffeat_dcf_itm:not(:first-child):after {
                content: 'AND';
                position: absolute;
                top: -28px;
                left: 0;
                right: 0;
                margin: auto;
                text-align: center;
                background: #f5faff;
                width: 50px;
                color: #ccc;
                font-size: 11px;
            }

            .cggffeat_dcf_itm:not(:first-child):before {
                content: '';
                width: 150px;
                height: 1px;
                background: #ccc;
                position: absolute;
                top: -20px;
                left: 0;
                right: 0;
                margin: auto;
            }

            .cggffeat_dcf_actions {
                margin: 12px 0px 0px;
            }

            .cggffeat_dc_fitler_group:not(:first-child) {
                position: relative;
                margin-top: 20px;
            }

            .cggffeat_dc_fitler_group:not(:first-child):after {
                content: 'OR';
                position: absolute;
                top: -24px;
                left: 0;
                right: 0;
                margin: auto;
                text-align: center;
                color: #ccc;
                background: #fff;
                max-width: 30px;
                font-size: 11px;
            }

            .cggffeat_dc_fitler_group:not(:first-child):before {
                content: '';
                position: absolute;
                top: -15px;
                left: 0;
                right: 0;
                background: #ccc;
                width: 150px;
                height: 1px;
                margin: auto;
            }

            .cggffeat_dc_fitler_group span.remove_group {
                position: absolute;
                top: -6px;
                right: -6px;
                background: #eee;
                width: 14px;
                height: 14px;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 16px;
                border-radius: 50%;
                border: 1px solid #ccc;
                cursor: pointer;
            }
        </style>
        <script type="text/javascript">
            const cggffeat_all_sources = JSON.parse(`<?php echo json_encode($opt_content_obj); ?>`);

            const supported_fields = ['multi_choice', 'multiselect', 'checkbox', 'radio', 'option'];

            supported_fields.forEach(function(fky, finx){
                fieldSettings[fky] += ', .cggffeat_dynamic_source_setting';
                fieldSettings[fky] += ', .cggffeat_dynamic_type_setting';
                fieldSettings[fky] += ', .cggffeat_dc_fitlers_json';
            });

            // console.log(fieldSettings);

            function re_sort_cggffeat_filter_value(existing = null) {
                if (existing) {
                    let group_html = ``;
                    existing.forEach(function(group, grp_indx) {
                        let item_html = ``;
                        group.forEach(function(item, itm_indx) {
                            const _keys = {
                                "id": "ID",
                                "title": "Title",
                                "desc": "Description",
                            };
                            const _opt = {
                                "=": "Equal to",
                                "!=": "Not equal to",
                                "contain": "Contain",
                                "not_contain": "Not Contain",
                                ">=": "Greater then equal",
                                ">": "Greater then",
                                "<=": "Less then equal",
                                "<": "Less then",
                            };


                            let keys_html = ``;
                            Object.keys(_keys).forEach(function(key_val, key_ind) {
                                const key_slctd = key_val == item.key ? 'selected' : '';
                                keys_html += `<option value="${key_val}" ${key_slctd}>${_keys[key_val]}</option>`;
                            });

                            let opts_html = ``;
                            Object.keys(_opt).forEach(function(opt_val, key_ind) {
                                const opt_slctd = opt_val == item.operator ? 'selected' : '';
                                opts_html += `<option value="${opt_val}" ${opt_slctd}>${_opt[opt_val]}</option>`;
                            });

                            item_html += `
                            <div class="cggffeat_dcf_itm">
                                <div class="cggffeat_dcf_head">
                                    <select name="cggffeat_fsource" class="filter_source">${keys_html}</select>
                                    <select name="cggffeat_fcompare" class="filter_compare">${opts_html}</select>
                                </div>
                                <div class="cggffeat_dcf_body">
                                    <select name="cggffeat_fvalue_type" class="filter_value_type">
                                        <option value="">Equal to</option>
                                        <option value="custom">Custom Value</option>
                                    </select>
                                    <input type="text" name="cggffeat_fvalue" placeholder="Value" value="${item.value}">
                                </div>
                            </div>`;
                        });

                        const is_disabled = group.length == 1 ? 'disabled="disabled"' : '';
                        item_html += `
                        <div class="cggffeat_dcf_actions">
                            <button type="button" class="button button-small add_filter">Add</button>
                            <button type="button" class="button button-small remove_filter" ${is_disabled}>Remove</button>
                        </div>
                        `;

                        if (grp_indx != 0) {
                            item_html += `<span class="remove_group">&times;</span>`;
                        }
                        group_html += `<div class="cggffeat_dc_fitler_group">${item_html}</div>`;
                    });
                    jQuery('.cggffeat_dc_fitlers_wrapper .cggffeat_dc_fitlers').html(group_html);
                } else {
                    let filters = [];
                    jQuery('.cggffeat_dc_fitlers_wrapper .cggffeat_dc_fitlers .cggffeat_dc_fitler_group').each(function(grp_indx, grp) {
                        let fltr_grp = [];
                        jQuery(grp).find('.cggffeat_dcf_itm').each(function(itm_indx, itm) {
                            const this_item = jQuery(itm);
                            const this_key = this_item.find('[name="cggffeat_fsource"]').val();
                            const this_opt = this_item.find('[name="cggffeat_fcompare"]').val();
                            const this_val = this_item.find('[name="cggffeat_fvalue"]').val();

                            fltr_grp.push({
                                "key": this_key,
                                "operator": this_opt,
                                "value": this_val
                            });
                        })
                        filters.push(fltr_grp);
                    });

                    jQuery('#cggffeat_dc_fitlers_json').val(btoa(JSON.stringify(filters))).trigger('change');
                }
            }

            function cggffeat_populate_opt_values() {
                jQuery('#cggffeat_dynamic_type_setting').html('');
                const source_itms = cggffeat_all_sources[field.cggffeat_dynamic_source];
                if (source_itms && Object.keys(source_itms).length >= 1) {
                    const source_keys = Object.keys(source_itms);
                    source_keys.forEach(function(itm, indx) {
                        const selected = field.cggffeat_type_source == itm ? 'selected' : '';
                        jQuery('#cggffeat_dynamic_type_setting').append(`<option value="${itm}" ${selected}>${source_itms[itm]}</option>`);
                    });
                }

                const prev_filter_val = jQuery("#cggffeat_dc_fitlers_json").val();
                if (prev_filter_val) {
                    const prev_filter_json = atob(prev_filter_val);
                    const prev_filter_arr = JSON.parse(prev_filter_json);
                    re_sort_cggffeat_filter_value(prev_filter_arr);
                }

            }

            jQuery(document).on("gform_load_field_settings", function(event, field) {
                jQuery("#cggffeat_dynamic_source_setting").val(field.cggffeat_dynamic_source || "");
                jQuery("#cggffeat_dynamic_type_setting").val(field.cggffeat_type_source || "");
                jQuery("#cggffeat_dc_fitlers_json").val(field.cggffeat_dc_fitlers_json || "");
                cggffeat_populate_opt_values();
            });

            function cggffeat_load_dynamic_types() {
                jQuery('#cggffeat_dynamic_type_setting').html('');
                const source = jQuery('#cggffeat_dynamic_source_setting').val();

                const source_itms = cggffeat_all_sources[source];
                const source_keys = Object.keys(source_itms);
                source_keys.forEach(function(itm, indx) {
                    jQuery('#cggffeat_dynamic_type_setting').append(`<option value="${itm}" >${source_itms[itm]}</option>`);
                });
            }

            jQuery(document).on('change', '#cggffeat_dynamic_source_setting', function() {
                cggffeat_load_dynamic_types();
            });





            // Filter Start

            jQuery('.cggffeat_dc_fitlers_wrapper').on('input', 'input, select, textarea', function() {
                re_sort_cggffeat_filter_value();
            });

            jQuery('.cggffeat_dc_fitlers_wrapper').on('click', '.add_filter', function() {
                const this_btn = jQuery(this);
                const this_group = this_btn.closest('.cggffeat_dc_fitler_group');
                const this_item = jQuery(this_group.find('.cggffeat_dcf_itm').get(0));
                const new_item = this_item.clone();
                new_item.find('select').each(function(th_in, th_sl) {
                    jQuery(th_sl).val(jQuery(th_sl).find('option:first').val());
                });
                new_item.find('input').val('');
                this_group.find('.cggffeat_dcf_actions').before(new_item);
                this_group.find('.cggffeat_dcf_actions .remove_filter').removeAttr('disabled');
                re_sort_cggffeat_filter_value();
            });
            jQuery('.cggffeat_dc_fitlers_wrapper').on('click', '.remove_filter', function() {
                const this_btn = jQuery(this);
                const this_group = this_btn.closest('.cggffeat_dc_fitler_group');
                if (this_group.find('.cggffeat_dcf_itm').length >= 2) {
                    this_group.find('.cggffeat_dcf_actions').prev().remove();
                    if (this_group.find('.cggffeat_dcf_itm').length < 2) {
                        this_btn.attr('disabled', 'disabled');
                    }
                }
                re_sort_cggffeat_filter_value();
            });

            jQuery('.cggffeat_dc_fitlers_wrapper .add_filter_group').click(function() {
                const this_btn = jQuery(this);
                console.log(this_btn);
                const this_group_wrapper = this_btn.closest('.cggffeat_dc_fitlers_wrapper');
                const this_group = this_group_wrapper.find('.cggffeat_dc_fitlers .cggffeat_dc_fitler_group:first-child');
                const new_group = this_group.clone();
                new_group.append(`<span class="remove_group">&times;</span>`);
                new_group.find('.cggffeat_dcf_itm:not(:first-child)').remove();
                new_group.find('select').each(function(th_in, th_sl) {
                    jQuery(th_sl).val(jQuery(th_sl).find('option:first').val());
                });
                new_group.find('input').val('');
                this_btn.prev().append(new_group);
                re_sort_cggffeat_filter_value();
            });

            jQuery('.cggffeat_dc_fitlers_wrapper').on('click', '.remove_group', function() {
                const this_btn = jQuery(this);
                const this_group = this_btn.closest('.cggffeat_dc_fitler_group');
                console.log(this_group);
                this_group.remove();
                re_sort_cggffeat_filter_value();
            });

            // Filter End
        </script>
<?php
    }

    public function init_admin()
    {
        parent::init_admin();
        add_action('gform_field_standard_settings', array($this, 'add_choice_setting'), 10, 2);
        add_action('gform_editor_js', array($this, 'field_settings_scripts'));
    }

    public function get_posts($post_type, $filters = [])
    {
        global $wpdb;

        $query_sql = "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND (post_status = 'publish' OR post_status = 'inherit')";
        if (!empty($filters)) {
            $grp_query_sql = [];
            foreach ($filters as $filter) {
                $itm_query_sql = [];
                if (!empty($filter)) {
                    foreach ($filter as $rule) {
                        if ($rule['key'] == 'title') {
                            if ($rule['value'] != "") {
                                $num_val = floatval($rule['value']);

                                if ($rule['operator'] == "=") {
                                    $itm_query_sql[] = " post_title = '{$rule['value']}' ";
                                } else if ($rule['operator'] == "!=") {
                                    $itm_query_sql[] = " post_title != '{$rule['value']}' ";
                                } else if ($rule['operator'] == "contain") {
                                    $itm_query_sql[] = " post_title LIKE '%{$rule['value']}%' ";
                                } else if ($rule['operator'] == "not_contain") {
                                    $itm_query_sql[] = " post_title NOT LIKE '%{$rule['value']}%' ";
                                } else if ($rule['operator'] == ">=") {
                                    $itm_query_sql[] = " post_title >= {$num_val} ";
                                } else if ($rule['operator'] == ">") {
                                    $itm_query_sql[] = " post_title > {$num_val} ";
                                } else if ($rule['operator'] == "<=") {
                                    $itm_query_sql[] = " post_title <= {$num_val} ";
                                } else if ($rule['operator'] == "<") {
                                    $itm_query_sql[] = " post_title < {$num_val} ";
                                }
                            }
                        }
                    }
                }
                if (!empty($itm_query_sql)) {
                    $grp_query_sql[] = " (" . implode(' AND ', $itm_query_sql) . ") ";
                }
            }
            if (!empty($grp_query_sql)) {
                $query_sql .= " AND (" . implode(' OR ', $grp_query_sql) . ") ";
            }
        }

        $quried_ids = $wpdb->get_col($query_sql);

        $choices = [];
        if (!empty($quried_ids)) {
            $post_args = [
                'post_type' => $post_type,
                'posts_per_page' => -1,
                'post_status' => 'any',
                'post__in' => $quried_ids
            ];


            $post_qry = new WP_Query($post_args);
            if ($post_qry->have_posts()) {
                while ($post_qry->have_posts()) {
                    $post_qry->the_post();
                    $choices[] = [
                        "value" => get_the_ID(),
                        "text" => get_the_title()
                    ];
                }
                wp_reset_postdata();
            }
        }

        return $choices;
    }

    public function get_terms($taxo)
    {
        $terms = get_terms(['taxonomy' => $taxo, 'hide_empty' => false]);
        if (!empty($terms)) {
            foreach ($terms as $term) {
                $choices[] = [
                    "value" => $term->term_id,
                    "text" => $term->name
                ];
            }
        }

        return $choices;
    }


    public function add_dynamic_choices($form)
    {
        foreach ($form['fields'] as &$field) {
            if (!empty($field->cggffeat_dynamic_source) && !empty($field->cggffeat_type_source)) {
                $source = $field->cggffeat_dynamic_source;
                $type = $field->cggffeat_type_source;
                $filter = $field->cggffeat_dc_fitlers_json;
                $filter_json = base64_decode($filter);
                $filter_arr = json_decode($filter_json, true);

                if ($source == 'post') {
                    $field->choices = $this->get_posts($type, $filter_arr);
                }

                if ($source == 'taxonomy') {
                    $field->choices = $this->get_terms($type, $filter_arr);
                }
            }
        }

        return $form;
    }

    public function scripts()
    {
        $scripts = array(
            array(
                'handle'  => 'cggffeat_script',
                'src'     => $this->get_base_url() . '/assets/js/script.js',
                'version' => $this->_version,
                'deps'    => array('jquery'),
                'strings' => array(
                    'first'  => esc_html__('First Choice', 'simpleaddon'),
                    'second' => esc_html__('Second Choice', 'simpleaddon'),
                    'third'  => esc_html__('Third Choice', 'simpleaddon')
                ),
                'enqueue' => array(
                    array(
                        'admin_page' => array('form_settings'),
                        'tab'        => 'simpleaddon'
                    )
                )
            ),

        );

        return array_merge(parent::scripts(), $scripts);
    }

    public function styles()
    {
        $styles = array(
            array(
                'handle'  => 'cggffeat_styles',
                'src'     => $this->get_base_url() . '/assets/css/style.css',
                'version' => $this->_version,
                'enqueue' => array(
                    array('field_types' => array('poll'))
                )
            )
        );

        return array_merge(parent::styles(), $styles);
    }

    function form_submit_button($button, $form)
    {
        $settings = $this->get_form_settings($form);
        if (isset($settings['enabled']) && true == $settings['enabled']) {
            $text   = $this->get_plugin_setting('mytextbox');
            $button = "</pre><div>{$text}</div><pre>" . $button;
        }

        return $button;
    }

    public function form_settings_fields($form)
    {
        return array(
            array(
                'title'  => esc_html__('Simple Form Settings', 'simpleaddon'),
                'fields' => array(
                    array(
                        'label'   => esc_html__('My checkbox', 'simpleaddon'),
                        'type'    => 'checkbox',
                        'name'    => 'enabled',
                        'tooltip' => esc_html__('This is the tooltip', 'simpleaddon'),
                        'choices' => array(
                            array(
                                'label' => esc_html__('Enabled', 'simpleaddon'),
                                'name'  => 'enabled',
                            ),
                        ),
                    ),
                    array(
                        'label'   => esc_html__('My checkboxes', 'simpleaddon'),
                        'type'    => 'checkbox',
                        'name'    => 'checkboxgroup',
                        'tooltip' => esc_html__('This is the tooltip', 'simpleaddon'),
                        'choices' => array(
                            array(
                                'label' => esc_html__('First Choice', 'simpleaddon'),
                                'name'  => 'first',
                            ),
                            array(
                                'label' => esc_html__('Second Choice', 'simpleaddon'),
                                'name'  => 'second',
                            ),
                            array(
                                'label' => esc_html__('Third Choice', 'simpleaddon'),
                                'name'  => 'third',
                            ),
                        ),
                    ),
                    array(
                        'label'   => esc_html__('My Radio Buttons', 'simpleaddon'),
                        'type'    => 'radio',
                        'name'    => 'myradiogroup',
                        'tooltip' => esc_html__('This is the tooltip', 'simpleaddon'),
                        'choices' => array(
                            array(
                                'label' => esc_html__('First Choice', 'simpleaddon'),
                            ),
                            array(
                                'label' => esc_html__('Second Choice', 'simpleaddon'),
                            ),
                            array(
                                'label' => esc_html__('Third Choice', 'simpleaddon'),
                            ),
                        ),
                    ),
                    array(
                        'label'      => esc_html__('My Horizontal Radio Buttons', 'simpleaddon'),
                        'type'       => 'radio',
                        'horizontal' => true,
                        'name'       => 'myradiogrouph',
                        'tooltip'    => esc_html__('This is the tooltip', 'simpleaddon'),
                        'choices'    => array(
                            array(
                                'label' => esc_html__('First Choice', 'simpleaddon'),
                            ),
                            array(
                                'label' => esc_html__('Second Choice', 'simpleaddon'),
                            ),
                            array(
                                'label' => esc_html__('Third Choice', 'simpleaddon'),
                            ),
                        ),
                    ),
                    array(
                        'label'   => esc_html__('My Dropdown', 'simpleaddon'),
                        'type'    => 'select',
                        'name'    => 'mydropdown',
                        'tooltip' => esc_html__('This is the tooltip', 'simpleaddon'),
                        'choices' => array(
                            array(
                                'label' => esc_html__('First Choice', 'simpleaddon'),
                                'value' => 'first',
                            ),
                            array(
                                'label' => esc_html__('Second Choice', 'simpleaddon'),
                                'value' => 'second',
                            ),
                            array(
                                'label' => esc_html__('Third Choice', 'simpleaddon'),
                                'value' => 'third',
                            ),
                        ),
                    ),
                    array(
                        'label'             => esc_html__('My Text Box', 'simpleaddon'),
                        'type'              => 'text',
                        'name'              => 'mytext',
                        'tooltip'           => esc_html__('This is the tooltip', 'simpleaddon'),
                        'class'             => 'medium',
                        'feedback_callback' => array($this, 'is_valid_setting'),
                    ),
                    array(
                        'label'   => esc_html__('My Text Area', 'simpleaddon'),
                        'type'    => 'textarea',
                        'name'    => 'mytextarea',
                        'tooltip' => esc_html__('This is the tooltip', 'simpleaddon'),
                        'class'   => 'medium merge-tag-support mt-position-right',
                    ),
                    array(
                        'label' => esc_html__('My Hidden Field', 'simpleaddon'),
                        'type'  => 'hidden',
                        'name'  => 'myhidden',
                    ),
                    array(
                        'label' => esc_html__('My Custom Field', 'simpleaddon'),
                        'type'  => 'my_custom_field_type',
                        'name'  => 'my_custom_field',
                        'args'  => array(
                            'text'     => array(
                                'label'         => esc_html__('A textbox sub-field', 'simpleaddon'),
                                'name'          => 'subtext',
                                'default_value' => 'change me',
                            ),
                            'checkbox' => array(
                                'label'   => esc_html__('A checkbox sub-field', 'simpleaddon'),
                                'name'    => 'my_custom_field_check',
                                'choices' => array(
                                    array(
                                        'label'         => esc_html__('Activate', 'simpleaddon'),
                                        'name'          => 'subcheck',
                                        'default_value' => true,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }

    public function settings_my_custom_field_type($field, $echo = true)
    {
        echo '</pre>
            <div>' . esc_html__('My custom field contains a few settings:', 'simpleaddon') . '</div>
        <pre>';

        // get the text field settings from the main field and then render the text field
        $text_field = $field['args']['text'];
        $this->settings_text($text_field);

        // get the checkbox field settings from the main field and then render the checkbox field
        $checkbox_field = $field['args']['checkbox'];
        $this->settings_checkbox($checkbox_field);
    }

    public function is_valid_setting($value)
    {
        return strlen($value) > 5;
    }
}
