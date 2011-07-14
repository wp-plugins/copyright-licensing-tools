<?php
/*
Plugin Name: iCopyright
Plugin URI: http://info.icopyright.com/wordpress
Description: The iCopyright plugin adds article tools (print, email, post, and republish) and an interactive copyright notice to your site that facilitate the monetization and distribution of your content. Earn fees or ad revenue when your articles are re-used. Identify websites that re-use your content without permission and request takedown or convert them to customers. By iCopyright, Inc.
Author: iCopyright, Inc.  
Author URI: http://info.icopyright.com
Version: 1.1.4
*/


//define constant that need to be changed from test environment to live environment

//define the plugin's name, directory, and url
define("ICOPYRIGHT_PLUGIN_NAME", "copyright-licensing-tools");
define("ICOPYRIGHT_PLUGIN_DIR", WP_PLUGIN_DIR . "/" . ICOPYRIGHT_PLUGIN_NAME);
define("ICOPYRIGHT_PLUGIN_URL", WP_PLUGIN_URL . "/" . ICOPYRIGHT_PLUGIN_NAME);

//include icopyright common functions file, there is defined server settings.
//since version 1.1.4
include (ICOPYRIGHT_PLUGIN_DIR . '/icopyright-common.php');

//define user agent
define("ICOPYRIGHT_USERAGENT", "iCopyright WordPress Plugin v1.1.4");

//define URL to iCopyright; assuming other file structures will be intact.
//url constructed from define server from icopyright-common.php
//updated version 1.1.4
$icopyright_url = icopyright_get_server(TRUE)."/";
define("ICOPYRIGHT_URL",$icopyright_url);

//include plugin admin page
include (ICOPYRIGHT_PLUGIN_DIR . '/icopyright-admin.php');

//include plugin functions
include (ICOPYRIGHT_PLUGIN_DIR . '/icopyright-functions.php');

//hook in icopyright-interactive-tools.css into theme template <head>
function load_icopyright_script(){
//register script
wp_register_script('icopyright-notice-js', ICOPYRIGHT_PLUGIN_URL . '/icopyright-interactive-tools.js','', '1.0');
wp_register_style('icopyright_notice', ICOPYRIGHT_PLUGIN_URL . '/icopyright-interactive-tools.css', $deps, '1.0', 'screen');
//load script
wp_enqueue_script('icopyright-notice-js', ICOPYRIGHT_PLUGIN_URL . '/icopyright-interactive-tools.js','', '1.0');
wp_enqueue_style( 'icopyright_notice', ICOPYRIGHT_PLUGIN_URL . '/icopyright-interactive-tools.css', $deps, '1.0', 'screen' );
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
float:right !important;
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
        $settings_link = "<a href=\"options-general.php?page=icopyright.php\">Settings</a>";
		$video_link = "<a href=\"http://info.icopyright.com/icopyright-video\" target=\"_blank\">View a video introduction to iCopyright</a>";//added version 1.1.2
        $links[] = $settings_link;
		$links[] .= $video_link;//added version 1.1.2
    }
    return $links;
}
//function to delete option during uninstallation 
function icopyright_remove_settings(){
delete_option('icopyright_admin');
}
register_uninstall_hook( __FILE__, 'icopyright_remove_settings' );

function icopyright_default_settings(){

$check_admin_setting = get_option('icopyright_admin');

if(empty($check_admin_setting)){
             $icopyright_admin = array('pub_id' => '',
			                           'display' => 'auto',
									   'tools' => 'horizontal',
									   'align' => 'left',
									   'show' => 'both',
									   'show_multiple' => 'both',
									   'ez_excerpt'=> 'yes',
									   'syndication'=>'yes'
							   	     );

			 update_option('icopyright_admin',$icopyright_admin);
			 //prepare blank option to save conductor password into option to use for ez excerpt setting.
			 //since version 1.1.4
			 update_option('icopyright_conductor_password','');
			 update_option('icopyright_conductor_email','');       
}

}
register_activation_hook( __FILE__, 'icopyright_default_settings' );

//admin warnings notice if empty publication id
function icopyright_admin_warning(){

/***since version 1.1.2***/

//setup admin url to icopyright settings page
$icopyright_settings_url = admin_url() . "options-general.php?page=icopyright.php";

//setup current url
$current_page_url = icopyright_current_page_url();

//compare current url with constructed settings url
// to determine if we are on settings page			
if($current_page_url==$icopyright_settings_url){
$show_warning_message = false;// Yes we are, do not show message!
}else{
$show_warning_message = true;// else not on settings page, show message!
}
/**end of version 1.1.2 addition*****/

$check_admin_setting = get_option('icopyright_admin');

  //condition check to show admin warning,
  //if publication id is empty and is not on settings page.
  if((empty($check_admin_setting['pub_id'])) && ($show_warning_message == true)){

		function icopyright_warning() {
echo "
			<div id='icopyright-warning' class='updated fade'><p><strong>".__('Copyright and Licensing Tools is almost ready.')."</strong> ".sprintf(__('You must register or enter your Publication Id for it to work. <a href="%1$s">Please click to visit iCopyright Settings Page.</a>'), "options-general.php?page=icopyright.php")."</p></div>
			";
		}
		add_action('admin_notices','icopyright_warning');
  }

}
add_action('init','icopyright_admin_warning');
?>