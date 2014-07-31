<?php

include (ICOPYRIGHT_PLUGIN_DIR . '/icopyright-admin-functions.php');
include (ICOPYRIGHT_PLUGIN_DIR . '/settings-fields-callback.php');
include (ICOPYRIGHT_PLUGIN_DIR . '/icopyright-republish-page.php');

//
// Add the iCopyright options page
//
add_action('admin_menu', 'icopyright_admin_menu');
function icopyright_admin_menu() {
  add_options_page('iCopyright', 'iCopyright', 'manage_options', 'copyright-licensing-tools', 'icopyright_options_page');
}

function icopyright_options_page() {
	
  //
  // Process the TOU Form
  //
  $touResult = icopyright_process_tou();
  if ($touResult != NULL && $touResult == 'SUCCESS') {
    icopyright_display_publication_welcome();
  }

  $registrationResult = icopyright_post_registration_form();
  if ($registrationResult != NULL && $registrationResult == 'SUCCESS') {
    icopyright_display_publication_welcome();
  }

  //
  // Check connectivity
  //
  icopyright_check_connectivity();

  //
  // Add JS and CSS
  //
  wp_enqueue_style('icopyright-admin-css', plugins_url('css/style.css', __FILE__), array(), '1.0.1');  // Update the version when the style changes.  Refreshes cache.
  wp_enqueue_style('icopyright-admin-css-2', "http://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.4.33/example1/colorbox.css", array(), '1.0.0');
  wp_enqueue_script('icopyright-admin-js', plugins_url('js/main.js', __FILE__));
  wp_enqueue_script("icopyright-admin-js-2", "http://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.4.33/jquery.colorbox-min.js");

  $tou = get_option('icopyright_tou');
  if (($touResult != NULL && $touResult == 'FAILURE') || ($registrationResult != NULL && $registrationResult == 'FAILURE') || !empty($_GET['show-registration-form'])) {
    //
    // Show register form
    //
    icopyright_create_register_form();
  } else {
    if (empty($tou)) {
      //
      // Show TOU on first view
      //
      icopyright_create_tou_form();
    } else {
    icopyright_admin_check_price_optimizer();
      //
      // Show options
      //
      ?>
    <div class="wrap">
      <h2>iCopyright Settings</h2>
      <div id="intro-video" style="position:relative;">
        <a href="http://www.youtube.com/embed/bpYG-Frhh9E?autoplay=1&vq=hd720" target="_blank" id="icopyright_wp_settings_video" title="iCopyright WordPress Settings">
          <img src="/wp-content/plugins/copyright-licensing-tools/images/bpYG-Frhh9E-mq.png" style="border: 1px solid black"/>
          <img src="/wp-content/plugins/copyright-licensing-tools/images/btn.play.png" style="position:absolute;left:157px;top:76px;opacity:.5;width:45px"/>
        </a>
      </div>
      <form action="options.php" method="POST">
        <?php
        settings_fields('icopyright-settings-group');
        do_settings_sections('copyright-licensing-tools');
        submit_button();
        ?>
      </form>
    </div>
    <?php
      $pubId = get_option('icopyright_pub_id');
      ?>
    <?php if (!empty($pubId)) { ?>
      <table class="form-table">
        <tbody>
        <tr valign="top">
          <th scope="row">
            <h3>Enter My<br/> Conductor Console</h3>
          </th>
          <td valign="top">
            <div id="enter-conductor-console">
              <?php print icopyright_graphical_link_to_conductor('acidIndex.act', 'enter-discovery.jpg', 'icx-enter-discovery'); ?>
              <?php print icopyright_graphical_link_to_conductor('pricingOptimizer.act', 'enter-price-optimizer.jpg', 'icx-enter-price-optimizer'); ?>
              <div class="clear"></div>
              <?php print icopyright_graphical_link_to_conductor('serviceGroups.act', 'modify-services-and-prices.jpg', 'icx-modify-services-prices'); ?>
              <?php print icopyright_graphical_link_to_conductor('publisherReports.act', 'view-reports.jpg', 'icx-view-reports'); ?>
            </div>
            <div style="clear:both;"></div>
          </td>
        </tr>
        </tbody>
      </table>
      <?php } ?>
    <?php

      //
      // Add various id's used by JavaScript
      //
      $siteName = get_option('icopyright_site_name');
      ?>
    <div id="pub_id" style="display:none;"><?php print get_option('icopyright_pub_id') ?></div>
    <div id="site_name" style="display:none;"><?php echo(empty($siteName) ? get_bloginfo() : $siteName); ?></div>
    <div id="icopyright_server" style="display:none;"><?php print icopyright_get_server() ?></div>
    <?php
    }
  }
}

//
// Section callbacks
//
function account_settings_section_callback() {
  $address = get_option('icopyright_address_line1');
  if (!empty($address)) {
?>
    <input type="button" id="toggle_account_setting" value="Show Address" style="cursor:pointer; margin-top: 1em;">
<?php
  } else {
?>
    <h3>Send Revenue Checks To:</h3>
    <div style="float:left;max-width: 700px;">
<?php
  }
}

function deployment_mechanism_section_callback() {
  $address = get_option('icopyright_address_line1');
  if (!empty($address)) {
  ?>
<div style="float:left;max-width: 700px;">
  <?php
  }
  ?>
<p>
    For assistance, please email <a
    href="mailto:wordpress@icopyright.com">wordpress@icopyright.com</a> or get <a
    href="http://info.icopyright.com/wordpress" target="_blank">help</a>.
</p>
  <?php
  if (empty($address)) {
  ?>
</div>
  <?php
  }
}

function toolbar_appearance_section_callback() {
  $address = get_option('icopyright_address_line1');
  if (!empty($address)) {
    ?>
    </div>
    <div style="clear: both;"></div>
  <?php
  }
}

function display_section_callback() {
}

function service_section_callback() {
}

function advanced_section_callback() {
  ?>
<input type="button" id="toggle_advance_setting" value="Show Advanced Settings" style="cursor:pointer; display: block; margin-top: 1em;">
<?php
}

//
// Add the settings.
//
function icopyright_admin_check_price_optimizer() {
$icopyright_pricing_optimizer_opt_in = get_option('icopyright_pricing_optimizer_opt_in');
  if ($icopyright_pricing_optimizer_opt_in != FALSE) {
    add_settings_field('icopyright_pricing_optimizer_opt_in', 'Price Optimizer', 'pricing_optimizer_opt_in_field_callback', 'copyright-licensing-tools', 'service-settings');
    register_setting('icopyright-settings-group', 'icopyright_pricing_optimizer_opt_in');

    add_settings_field('icopyright_pricing_optimizer_apply_automatically', '', 'pricing_optimizer_apply_automatically_field_callback', 'copyright-licensing-tools', 'service-settings');
    register_setting('icopyright-settings-group', 'icopyright_pricing_optimizer_apply_automatically');
  }
}

add_action('admin_init', 'icopyright_admin_init');
function icopyright_admin_init() {

  //
  // Update settings
  //
  icopyright_update_settings();

  $address = get_option('icopyright_address_line1');
  if (empty($address)) {
    add_account_settings_section();
  }

  add_settings_section('deployment-mechanism', 'The Basics:', 'deployment_mechanism_section_callback', 'copyright-licensing-tools');

  add_settings_field('icopyright_display', 'Toolbar Placement', 'display_field_callback', 'copyright-licensing-tools', 'deployment-mechanism');
  register_setting('icopyright-settings-group', 'icopyright_display');
  
  add_settings_field('icopyright_searchable', 'Searchable', 'searchable_field_callback', 'copyright-licensing-tools', 'deployment-mechanism');
  register_setting('icopyright-settings-group', 'icopyright_searchable');
  
  add_settings_field('icopyright_use_category_filter', 'Excludes', 'use_category_filter_field_callback', 'copyright-licensing-tools', 'deployment-mechanism');
  register_setting('icopyright-settings-group', 'icopyright_use_category_filter');

  /*add_settings_field('icopyright_categories', '', '', 'copyright-licensing-tools', '');
  register_setting('icopyright-settings-group', 'icopyright_categories');*/

  add_settings_field('icopyright_exclude_categories', '', '', 'copyright-licensing-tools', '');
  register_setting('icopyright-settings-group', 'icopyright_exclude_categories');  
  
  add_settings_field('icopyright_exclude_author_filter', '', '', 'copyright-licensing-tools', '');
  register_setting('icopyright-settings-group', 'icopyright_exclude_author_filter');

  add_settings_field('icopyright_authors', '', '', 'copyright-licensing-tools', '');
  register_setting('icopyright-settings-group', 'icopyright_authors');    

  add_settings_section('toolbar-appearance', 'Toolbar Appearance:', 'toolbar_appearance_section_callback', 'copyright-licensing-tools');

  add_settings_field('icopyright_tools', 'Format', 'tools_field_callback', 'copyright-licensing-tools', 'toolbar-appearance');
  register_setting('icopyright-settings-group', 'icopyright_tools');

  add_settings_field('icopyright_theme', 'Theme', 'theme_field_callback', 'copyright-licensing-tools', 'toolbar-appearance');
  register_setting('icopyright-settings-group', 'icopyright_theme');

  add_settings_field('icopyright_background', 'Background', 'background_field_callback', 'copyright-licensing-tools', 'toolbar-appearance');
  register_setting('icopyright-settings-group', 'icopyright_background');

  add_settings_field('icopyright_align', 'Align', 'align_field_callback', 'copyright-licensing-tools', 'toolbar-appearance');
  register_setting('icopyright-settings-group', 'icopyright_align');

  add_settings_field('copyright_notice_preview', 'Preview of Interactive Copyright Notice (displayed below articles)', 'copyright_notice_preview_callback', 'copyright-licensing-tools', 'toolbar-appearance');
  register_setting('icopyright-settings-group', 'copyright_notice_preview');

  //add_settings_section('toolbar-display', 'Tools Displayed on Pages With:', 'display_section_callback', 'copyright-licensing-tools');

  add_settings_field('icopyright_show', 'Display style', 'show_preview_callback', 'copyright-licensing-tools', 'toolbar-appearance');
  register_setting('icopyright-settings-group', 'icopyright_show');

  add_settings_field('icopyright_display_on_pages', 'Pages', 'display_on_pages_field_callback', 'copyright-licensing-tools', 'toolbar-appearance');
  register_setting('icopyright-settings-group', 'icopyright_display_on_pages');


  add_settings_section('service-settings', 'Service Settings:', 'service_section_callback', 'copyright-licensing-tools');

  add_settings_field('icopyright_ez_excerpt', 'EZ Excerpt', 'ez_excerpt_field_callback', 'copyright-licensing-tools', 'service-settings');
  register_setting('icopyright-settings-group', 'icopyright_ez_excerpt');

  $icopyright_pricing_optimizer_opt_in = get_option('icopyright_pricing_optimizer_opt_in');
  if ($icopyright_pricing_optimizer_opt_in != FALSE) {
    add_settings_field('icopyright_pricing_optimizer_opt_in', 'Price Optimizer', 'pricing_optimizer_opt_in_field_callback', 'copyright-licensing-tools', 'service-settings');
    register_setting('icopyright-settings-group', 'icopyright_pricing_optimizer_opt_in');

    add_settings_field('icopyright_pricing_optimizer_apply_automatically', '', 'pricing_optimizer_apply_automatically_field_callback', 'copyright-licensing-tools', 'service-settings');
    register_setting('icopyright-settings-group', 'icopyright_pricing_optimizer_apply_automatically');
  }

  add_settings_field('icopyright_share', 'Share services', 'share_field_callback', 'copyright-licensing-tools', 'service-settings');
  register_setting('icopyright-settings-group', 'icopyright_share');

  if (!empty($address)) {
    add_account_settings_section();
  }

  add_settings_section('advanced-settings', '', 'advanced_section_callback', 'copyright-licensing-tools');

  add_settings_field('icopyright_pub_id', 'Publication ID', 'pub_id_field_callback', 'copyright-licensing-tools', 'advanced-settings');
  register_setting('icopyright-settings-group', 'icopyright_pub_id');

  add_settings_field('icopyright_conductor_email', 'Conductor Email Address', 'conductor_email_field_callback', 'copyright-licensing-tools', 'advanced-settings');
  register_setting('icopyright-settings-group', 'icopyright_conductor_email');

  add_settings_field('icopyright_conductor_password', 'Conductor Password', 'conductor_password_field_callback', 'copyright-licensing-tools', 'advanced-settings');
  register_setting('icopyright-settings-group', 'icopyright_conductor_password');

  add_settings_field('icopyright_feed_url', 'Conductor Feed URL', 'feed_url_field_callback', 'copyright-licensing-tools', 'advanced-settings');
  register_setting('icopyright-settings-group', 'icopyright_feed_url', 'icopyright_post_settings');

  add_settings_field('icopyright_show_multiple', '', 'show_multiple_callback', 'copyright-licensing-tools', '');
  register_setting('icopyright-settings-group', 'icopyright_show_multiple');
}

function add_account_settings_section() {
  add_settings_section('account-settings', '', 'account_settings_section_callback', 'copyright-licensing-tools');

  add_settings_field('icopyright_fname', 'First Name', 'first_name_field_callback', 'copyright-licensing-tools', 'account-settings');
  register_setting('icopyright-settings-group', 'icopyright_fname');

  add_settings_field('icopyright_lname', 'Last Name', 'last_name_field_callback', 'copyright-licensing-tools', 'account-settings');
  register_setting('icopyright-settings-group', 'icopyright_lname');

  add_settings_field('icopyright_site_name', 'Site Name', 'site_name_field_callback', 'copyright-licensing-tools', 'account-settings');
  register_setting('icopyright-settings-group', 'icopyright_site_name');

  add_settings_field('icopyright_site_url', 'Site URL', 'site_url_field_callback', 'copyright-licensing-tools', 'account-settings');
  register_setting('icopyright-settings-group', 'icopyright_site_url');

  add_settings_field('icopyright_address_line1', 'Address', 'address_line1_field_callback', 'copyright-licensing-tools', 'account-settings');
  register_setting('icopyright-settings-group', 'icopyright_address_line1');

  add_settings_field('icopyright_address_line2', '', 'address_line2_field_callback', 'copyright-licensing-tools', 'account-settings');
  register_setting('icopyright-settings-group', 'icopyright_address_line2');

  add_settings_field('icopyright_address_line3', '', 'address_line3_field_callback', 'copyright-licensing-tools', 'account-settings');
  register_setting('icopyright-settings-group', 'icopyright_address_line3');

  add_settings_field('icopyright_address_city', 'City', 'address_city_field_callback', 'copyright-licensing-tools', 'account-settings');
  register_setting('icopyright-settings-group', 'icopyright_address_city');

  add_settings_field('icopyright_address_state', 'State', 'address_state_field_callback', 'copyright-licensing-tools', 'account-settings');
  register_setting('icopyright-settings-group', 'icopyright_address_state');

  add_settings_field('icopyright_address_country', 'Country', 'address_country_field_callback', 'copyright-licensing-tools', 'account-settings');
  register_setting('icopyright-settings-group', 'icopyright_address_country');

  add_settings_field('icopyright_address_postal', 'Postal Code', 'address_postal_field_callback', 'copyright-licensing-tools', 'account-settings');
  register_setting('icopyright-settings-group', 'icopyright_address_postal');

  add_settings_field('icopyright_address_phone', 'Phone', 'address_phone_field_callback', 'copyright-licensing-tools', 'account-settings');
  register_setting('icopyright-settings-group', 'icopyright_address_phone');
}

//
// Display validation errors
//
function icopyright_admin_notices() {

  $wp_settings_errors = get_settings_errors('icopyright');
  if (sizeof($wp_settings_errors) > 0) {
    echo("<div class=\"updated settings-error\">");
    foreach ($wp_settings_errors as $error) {
      echo("<p>" . $error['message'] . "</p>");
    }
    echo("</div>");
  }
}

add_action('admin_notices', 'icopyright_admin_notices');

?>