<?php

/**
 * Plugin Name: CG Gravity Form Features
 * Description: Enable powerful features for your Gravity Forms, enhance the user experience, and elevate productivity.
 * Version: 1.0.0
 * Author: Codinggang
 * Author Uri: https://codinggang.com
 * 
 */

use MailPoetVendor\Doctrine\ORM\Query\Expr\Func;

if (!defined('ABSPATH')) exit;

define('CgGfFeat_VERSION', '2.0');

add_action('gform_loaded', array('CgGfFeat_BootUp', 'load'), 5);

class CgGfFeat_BootUp
{


    public static function load()
    {
        if (!method_exists('GFForms', 'include_addon_framework')) {
            return;
        }
        require_once('app/ajax-controller.php');

        require_once('class-CgGfFeat.php');
        require_once('modules/inventory.php');
        // require_once('fields/cg-phone.php');

        GFAddOn::register('CgGfFeat');
        GFAddOn::register('CggffInventory');
        // GFAddOn::register('GF_Field_CgPhone');
    }

    public function enqueue()
    {
        
    }
}

function gf_simple_addon()
{
    return CgGfFeat::get_instance();
}



add_action('admin_enqueue_scripts', function(){
    wp_enqueue_style('cggffi-main', plugin_dir_url(__FILE__) . '/assets/css/style.css');
});
