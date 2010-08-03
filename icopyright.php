<?php
/*
Plugin Name: iCopyright(R) Article Tools
Plugin URI: http://info.icopyright.com/wordpress-plugin
Description: The iCopyright plugin adds article tools (print, email, post, and republish) and an interactive copyright notice to your site that facilitate the monetization and distribution of your content. Earn fees or ad revenue when your articles are re-used. Identify websites that re-use your content without permission and request takedown or convert them to customers. Use the same solution many of the world's leading publishers use to protect and monetize their content. By iCopyright, Inc.
Author: iCopyright, Inc.  
Author URI: http://info.icopyright.com
Version: 1.1.0
*/


//define constant that need to be changed from test environment to live environment

//define URL to iCopyright API
define("ICOPYRIGHT_API_URL", "http://license.icopyright.net/api/xml/publisher/add");

//define URL to iCopyright; assuming other file structures will be intact.
define("ICOPYRIGHT_URL", "http://license.icopyright.net/");

//define the plugin's name
define("ICOPYRIGHT_PLUGIN_NAME", "copyright-licensing-tools");
define("ICOPYRIGHT_PLUGIN_DIR", WP_PLUGIN_DIR . "/" . ICOPYRIGHT_PLUGIN_NAME);
define("ICOPYRIGHT_PLUGIN_URL", WP_PLUGIN_URL . "/" . ICOPYRIGHT_PLUGIN_NAME);

//leave this page for including functions organised into files for easy reference.

//include plugin admin page
include (ICOPYRIGHT_PLUGIN_DIR . '/icopyright-admin.php');

//include plugin functions
include (ICOPYRIGHT_PLUGIN_DIR . '/icopyright-functions.php');

//hook in icopyright-interactive-tools.css into theme template <head>
function load_icopyright_script() {
    //register script
    wp_register_script('icopyright-notice-js', ICOPYRIGHT_PLUGIN_URL . '/icopyright-interactive-tools.js', '', '1.0');
    wp_register_style('icopyright_notice', ICOPYRIGHT_PLUGIN_URL . '/icopyright-interactive-tools.css', $deps, '1.0', 'screen');
//load script
    wp_enqueue_script('icopyright-notice-js', ICOPYRIGHT_PLUGIN_URL . '/icopyright-interactive-tools.js', '', '1.0');
    wp_enqueue_style('icopyright_notice', ICOPYRIGHT_PLUGIN_URL . '/icopyright-interactive-tools.css', $deps, '1.0', 'screen');
}

//add script to theme <head>
add_action('init', 'load_icopyright_script');


//hook for icopyright toolbar float
function icopyright_toolbar_float() {
    do_action('icopyright_toolbar_float');
}

//hook in css if blogger selected icopyright article tools alignment in WordPress Admin
//use this css to add on to class="icopyright-horizontal-interactive-toolbar" and class="icopyright-vertical-interactive-toolbar"
function load_icopyright_alignment_css() {

    $css = get_option('icopyright_admin');
    $toolbar_alignment = $css['align'];

    if ($toolbar_alignment == "right") {

        //use heredox syntax
        $str = <<<CSS
\n
<!--icopyright embedded css -->
<style type="text/css">
.icx-toolbar{
float:right;
margin:0px 0px 10px 10px;
}
</style>\n
CSS;
        echo $str;

    }
    //end if

}

add_action('icopyright_toolbar_float', 'load_icopyright_alignment_css');


//The following filters seems to work only on default plugin page.
//Therefore need to code it here.

//filter plugin_row_meta data to add a settings link to plugin information on "Manage Plugins" page.

/***Please note the following links will only appear after activating the plugin.***/
add_filter('plugin_row_meta', 'icopyright_settings_link', 10, 2);

//function to create settings link
function icopyright_settings_link($links, $file) {
    if ($file == plugin_basename(__FILE__)) {
        $settings_link = "<a href=\"options-general.php?page=icopyright\">Settings</a>";
        $links[] = $settings_link;
    }
    return $links;
}

//function to delete option during uninstallation
function icopyright_remove_settings() {
    delete_option('icopyright_admin');
}

register_uninstall_hook(__FILE__, 'icopyright_remove_settings');

function icopyright_default_settings() {

    $check_admin_setting = get_option('icopyright_admin');

    if (empty($check_admin_setting)) {
        $icopyright_admin = array('pub_id' => '',
            'display' => 'auto',
            'tools' => 'horizontal',
            'align' => 'left',
            'show' => 'both',
            'show_multiple' => 'both',
            'ez_excerpt' => 'yes',);

        update_option('icopyright_admin', $icopyright_admin);

    }

}

register_activation_hook(__FILE__, 'icopyright_default_settings');

//admin warnings notice if empty publication id
function icopyright_admin_warning() {

    $check_admin_setting = get_option('icopyright_admin');

    if (empty($check_admin_setting['pub_id'])) {

        function icopyright_warning() {
            echo "
			<div id='icopyright-warning' class='updated fade'><p><strong>" . __('Copyright and Licensing Tools is almost ready.') . "</strong> " . sprintf(__('You must register or enter your Publication Id for it to work. <a href="%1$s">Please click to visit iCopyright Settings Page.</a>'), "options-general.php?page=icopyright") . "</p></div>
			";
        }

        add_action('admin_notices', 'icopyright_warning');
    }

}

add_action('init', 'icopyright_admin_warning');
?>