<?php
/*
Plugin Name: iCopyright
Plugin URI: http://info.icopyright.com/wordpress
Description: Find current articles from leading publishers and websites. Republish them with one click. Plus, syndicate and monetize your own content. By iCopyright, Inc.
Author: iCopyright, Inc.  
Author URI: http://info.icopyright.com
Version: 2.5.7
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
define("ICOPYRIGHT_USERAGENT", "iCopyright WordPress Plugin v2.5.7");

//define URL to iCopyright; assuming other file structures will be intact.
//url constructed from define server from icopyright-common.php
//updated version 1.1.4
$icopyright_url = icopyright_get_server(TRUE) . "/";
define("ICOPYRIGHT_URL", $icopyright_url);

//include plugin admin, functions, feed
include (ICOPYRIGHT_PLUGIN_DIR . '/icopyright-admin.php');
include (ICOPYRIGHT_PLUGIN_DIR . '/icopyright-functions.php');
include (ICOPYRIGHT_PLUGIN_DIR . '/icopyright-feed.php');

//The following filters seems to work only on default plugin page.
//Therefore need to code it here.

//filter plugin_row_meta data to add a settings link to plugin information on "Manage Plugins" page.

/***Please note the following links will only appear after activating the plugin.***/
add_filter('plugin_row_meta', 'icopyright_settings_link', 10, 2);

//function to create settings link
function icopyright_settings_link($links, $file) {
  if ($file == plugin_basename(__FILE__)) {
    wp_enqueue_script('icopyright-admin-js', plugins_url('js/main.js', __FILE__));
    wp_enqueue_style('icopyright-admin-css', "http://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.4.33/example1/colorbox.css", array(), '1.0.0');
    wp_enqueue_script("icopyright-admin-js-2", "http://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.4.33/jquery.colorbox-min.js");
    $settings_link = "<a href=\"options-general.php?page=copyright-licensing-tools\">Settings</a>";
    $video_link = "<a id=\"icopyright_wp_settings_video\" href=\"https://www.youtube.com/watch?v=0MtjRF51i_k\" target=\"_blank\">View a video introduction to iCopyright</a>"; //added version 1.1.2
    $links[] = $settings_link;
    $links[] .= $video_link; //added version 1.1.2
  }
  return $links;
}



function icopyright_suspend_conductor() {
	$pid = get_option('icopyright_pub_id');
	$playerStatus = 'SUSPENDED';
	icopyright_deactivate($pid, $playerStatus);
}

function icopyright_deactivate($pid, $playerStatus) {
	deactivate_account($pid, $playerStatus);
}

//register_uninstall_hook(__FILE__, 'icopyright_remove_settings');
register_deactivation_hook(__FILE__, 'icopyright_suspend_conductor');

/**
 * Called on activation.
 */
function icopyright_activate() {
  $pid = get_option('icopyright_pub_id');
	$icopyright_searchable = get_option('icopyright_searchable');

	reactivate_account($pid, $icopyright_searchable);

  update_option('icopyright_redirect_on_first_activation', 'true');
  
  icopyright_update_excludes();
}

function icopyright_update_excludes() {
	if (!get_option('icopyright_did_exclude_migrate')) {
		$selectedCategories = get_option('icopyright_categories', array());
		$systemCategories = get_categories();  

		if ($selectedCategories && $systemCategories) {
			$excludeCategories = array();
			foreach ($systemCategories as $sys) {
				if (!in_array($sys->term_id, $selectedCategories)) {
					$excludeCategories[] = $sys->term_id;
				}
			}

			if ($excludeCategories) {
				update_option('icopyright_exclude_categories', $excludeCategories);
			}
		}
		update_option('icopyright_exclude_author_filter', get_option('icopyright_use_category_filter'));
		update_option('icopyright_did_exclude_migrate', 'yes');
	}
}


/**
 * On first activation, redirect the user to the general options page
 */
function icopyright_redirect_on_activation() {
  if (current_user_can('activate_plugins')) {
    if (get_option('icopyright_redirect_on_first_activation') == 'true') {
      delete_option('icopyright_redirect_on_first_activation');
      $icopyright_settings_url = admin_url() . "options-general.php?page=copyright-licensing-tools";
      wp_safe_redirect($icopyright_settings_url);
    }
  }
}

register_activation_hook(__FILE__, 'icopyright_activate');
add_action('admin_init', 'icopyright_redirect_on_activation');

//admin warnings notice if empty publication id
function icopyright_admin_warning() {
  //setup admin url to icopyright settings page
  $icopyright_settings_url = admin_url() . "options-general.php?page=copyright-licensing-tools";
  $current_page_url = icopyright_current_page_url();

  //compare current url with constructed settings url to determine if we are on settings page
  $show_warning_message = ($current_page_url != $icopyright_settings_url);
  $pub_id = get_option('icopyright_pub_id');

  //condition check to show admin warning, if publication id is empty and is not on settings page.
  if ((empty($pub_id)) && ($show_warning_message == TRUE)) {
    add_action('admin_notices', 'icopyright_warning');
  }
}

// Warning message about the tool not being ready yet
function icopyright_warning() {
  echo "<div id='icopyright-warning' class='updated fade'><p><strong>" .
    __('The iCopyright Toolbar is almost ready.') .
    "</strong> " .
    sprintf(__('You must register or enter your Publication ID for it to work. <a href="%1$s">Visit iCopyright Settings Page.</a>'), "options-general.php?page=copyright-licensing-tools") .
    "</p></div>";
}

add_action('init', 'icopyright_admin_warning');
add_action('init', 'icopyright_migrate_options');
?>
