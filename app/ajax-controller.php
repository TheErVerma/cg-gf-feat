<?php

if(!defined('ABSPATH')) exit();

if(!class_exists('CgGfAjaxController')){
    class CgGfAjaxController{
        
        public function __construct()
        {
            add_action('wp_ajax_cggffeat_load_dynamic_choices', [$this, 'load_dynamic_choices']);
        }

        public function load_dynamic_choices()
        {
            $source = isset($_POST['source']) ? $_POST['source'] : '';
            $options = [];
            if(!empty($source)){
                if($source == 'post'){
                    $all_types = get_post_types(['public' => true], 'objects');
                    if(!empty($all_types)){
                        foreach($all_types as $all_type){
                            $options[$all_type->name] = $all_type->label;
                        }
                    }
                }else if($source == 'taxonomy'){
                    $all_types = get_taxonomies(['public' => true], 'objects');
                    if(!empty($all_types)){
                        foreach($all_types as $all_type){
                            $options[$all_type->name] = $all_type->label;
                        }
                    }
                }
            }
            wp_send_json(['status' => 200, 'message' => 'Success', 'options' => $options]);
            exit();
        }
    }
}

if(class_exists('CgGfAjaxController')){
    $CgGfAjaxController = new CgGfAjaxController();
}