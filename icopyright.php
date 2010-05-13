<?php
/*
Plugin Name: iCopyright
Plugin URI: http://info.icopyright.com/wordpress-plugin
Description: The iCopyright plugin will add article tools that enable visitors to your site to print, email, post, and republish your posts. This includes ad-supported free uses as well as licensing options for a fee. This plugin also adds an interactive copyright notice at the bottom of your pages. To fully activate the plugin, you must click on "Settings" below to register your site/blog and get a Publication ID. For detailed instructions, click the "Visit Plugin Site" link below.
Author: iCopyright, Inc.  
Author URI: http://info.icopyright.com
Version: 1.0.2
*/


//define constant that need to be changed from test environment to live environment

//define URL to iCopyright API
define("ICOPYRIGHT_API_URL","http://license.icopyright.net/api/xml/publisher/add");

//define URL to iCopyright
//assuming other file structures will be intact.
define("ICOPYRIGHT_URL","http://license.icopyright.net/");



//leave this page for including functions organised into files.
//for easy reference.

//include plugin functions
include (WP_PLUGIN_DIR . '/icopyright/icopyright-functions.php');

//include plugin admin page
include (WP_PLUGIN_DIR . '/icopyright/icopyright-admin.php');


//hook in icopyright-interactive-tools.css into theme template <head>
function load_icopyright_script(){
//register script
wp_register_script('icopyright-notice-js', WP_PLUGIN_URL . '/icopyright/icopyright-interactive-tools.js','', '1.0');
wp_register_style('icopyright_notice', WP_PLUGIN_URL.'/icopyright/icopyright-interactive-tools.css', $deps, '1.0', 'screen');
//load script
wp_enqueue_script('icopyright-notice-js', WP_PLUGIN_URL . '/icopyright/icopyright-interactive-tools.js','', '1.0');
wp_enqueue_style( 'icopyright_notice', WP_PLUGIN_URL.'/icopyright/icopyright-interactive-tools.css', $deps, '1.0', 'screen' );
} 

//add script to theme <head>
add_action('init', 'load_icopyright_script');


//hook for icopyright toolbar float
function icopyright_toolbar_float(){
do_action('icopyright_toolbar_float');
}

//hook in css if blogger selected icopyright article tools alignment in WordPress Admin
//use this css to add on to class="icopyright-horizontal-interactive-toolbar" and class="icopyright-vertical-interactive-toolbar"
function load_icopyright_alignment_css(){

$css = get_option('icopyright_admin');
$toolbar_alignment = $css['align'];

if($toolbar_alignment=="right"){

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

}//end if

}
add_action('icopyright_toolbar_float','load_icopyright_alignment_css');



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
?>