<?php
/*
Plugin Name: iCopyright
Plugin URI: http://info.icopyright.com/wordpress
Description: The iCopyright plugin adds article tools (print, email, post, and republish) and an interactive copyright notice to your site that facilitate the monetization and distribution of your content. Earn fees or ad revenue when your articles are re-used. Identify websites that re-use your content without permission and request takedown or convert them to customers. By iCopyright, Inc.
Author: iCopyright, Inc.  
Author URI: http://info.icopyright.com
Version: 1.7.2
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
define("ICOPYRIGHT_USERAGENT", "iCopyright WordPress Plugin v1.7.2");

//define URL to iCopyright; assuming other file structures will be intact.
//url constructed from define server from icopyright-common.php
//updated version 1.1.4
$icopyright_url = icopyright_get_server(TRUE) . "/";
define("ICOPYRIGHT_URL", $icopyright_url);

//include plugin admin page
include (ICOPYRIGHT_PLUGIN_DIR . '/icopyright-admin.php');

//include plugin functions
include (ICOPYRIGHT_PLUGIN_DIR . '/icopyright-functions.php');

//The following filters seems to work only on default plugin page.
//Therefore need to code it here.

//filter plugin_row_meta data to add a settings link to plugin information on "Manage Plugins" page.

/***Please note the following links will only appear after activating the plugin.***/
add_filter('plugin_row_meta', 'icopyright_settings_link', 10, 2);

//function to create settings link
function icopyright_settings_link($links, $file) {
  if ($file == plugin_basename(__FILE__)) {
    $settings_link = "<a href=\"options-general.php?page=icopyright.php\">Settings</a>";
    $video_link = "<a href=\"http://info.icopyright.com/icopyright-video\" target=\"_blank\">View a video introduction to iCopyright</a>"; //added version 1.1.2
    $links[] = $settings_link;
    $links[] .= $video_link; //added version 1.1.2
  }
  return $links;
}

//function to delete option during uninstallation
function icopyright_remove_settings() {
  delete_option('icopyright_admin');
  delete_option('icopyright_account');
  delete_option('icopyright_conductor_password');
  delete_option('icopyright_conductor_email');
  delete_option('icopyright_redirect_on_first_activation');
}
register_uninstall_hook(__FILE__, 'icopyright_remove_settings');

/**
 * Called on activation.
 */
function icopyright_activate() {
  update_option('icopyright_redirect_on_first_activation', 'true');
}
function icopyright_redirect_on_activation() {
  if (get_option('icopyright_redirect_on_first_activation') == 'true') {
    update_option('icopyright_redirect_on_first_activation', 'false');
    $icopyright_settings_url = admin_url() . "options-general.php?page=icopyright.php";
    wp_redirect($icopyright_settings_url);
  }
}
register_activation_hook(__FILE__, 'icopyright_activate');
add_action('init', 'icopyright_redirect_on_activation');

//admin warnings notice if empty publication id
function icopyright_admin_warning() {
  //setup admin url to icopyright settings page
  $icopyright_settings_url = admin_url() . "options-general.php?page=icopyright.php";
  $current_page_url = icopyright_current_page_url();

  //compare current url with constructed settings url to determine if we are on settings page
  $show_warning_message = ($current_page_url != $icopyright_settings_url);
  $check_admin_setting = get_option('icopyright_admin');

  //condition check to show admin warning, if publication id is empty and is not on settings page.
  if ((empty($check_admin_setting['pub_id'])) && ($show_warning_message == TRUE)) {
    add_action('admin_notices', 'icopyright_warning');
  }
}

// Warning message about the tool not being ready yet
function icopyright_warning() {
  echo "<div id='icopyright-warning' class='updated fade'><p><strong>" .
    __('The iCopyright Toolbar is almost ready.') .
    "</strong> " .
    sprintf(__('You must register or enter your Publication ID for it to work. <a href="%1$s">Visit iCopyright Settings Page.</a>'), "options-general.php?page=icopyright.php") .
    "</p></div>";
}

add_action('init', 'icopyright_admin_warning');

?>