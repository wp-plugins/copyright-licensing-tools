<?php
//This file contains functions of icopyright plugin

//WordPress Shortcodes to generate tool bars for content
//functions to generate tool bars, reuseable for auto inclusion or manual inclusion.
//Admin option to select toolbars and change auto to manual display

// Returns the common prefix for all the toolbars
function icopyright_toolbar_common($comment, $script) {
  global $post;
  $post_id = $post->ID;
  $pub_id_no = get_option('icopyright_pub_id');

  // Build up the toolbar piece by piece
  $toolbar = "\n<!-- iCopyright $comment Article Toolbar -->\n";
  $toolbar .= "<script type=\"text/javascript\">\n";
  $toolbar .= "var icx_publication_id = $pub_id_no;\n";
  $toolbar .= "var icx_content_id = $post_id;\n";
  $toolbar .= "</script>\n";
  $toolbar_script_url = ICOPYRIGHT_URL . "rights/js/$script"; //ICOPYRIGHT_URL constant defined in icopyright.php
  $toolbar .= "<script type=\"text/javascript\" src=\"$toolbar_script_url\"></script>\n";
  $toolbar .= "<!-- End of iCopyright $comment Article Toolbar -->\n";
  return $toolbar;
}

//Generate Horizontal Toolbar from hosted script
function icopyright_horizontal_toolbar() {
  if(!icopyright_post_passes_filters())
    return;
  $toolbar = icopyright_toolbar_common('Horizontal', 'horz-toolbar.js');
  // Wrap the toolbar with some styles
  $css = '.icx-toolbar-closure{clear: both;}';
  if(get_option('icopyright_align') == 'right') {
    $toolbar = '<div class="icx-toolbar-align-right">' . $toolbar . '</div>';
    $css .= '.icx-toolbar-align-right{float: right;}';
  }
  $toolbar .= '<div class="icx-toolbar-closure"></div>';
  $toolbar .= '<style type="text/css">' . $css . '</style>';
  return $toolbar;
}

//Generate Vertical Toolbar from hosted script
function icopyright_vertical_toolbar() {
  if(!icopyright_post_passes_filters())
    return;
  $toolbar = icopyright_toolbar_common('Vertical', 'vert-toolbar.js');

  // Wrap the toolbar with some styles
  $css = get_option('icopyright_align') == 'right' ? '.icx-toolbar{padding: 0 0 0 5px;}' : '.icx-toolbar{padding: 0 5px 0 0;}';
  if(get_option('icopyright_align') == 'right') {
    $toolbar = '<div class="icx-toolbar-align-right">' . $toolbar . '</div>';
    $css .= '.icx-toolbar-align-right{float: right;}';
  }
  $toolbar .= '<style type="text/css">' . $css . '</style>';
  return $toolbar;
}

//Generate One button from hosted script or directy
function icopyright_onebutton_toolbar() {
  if(!icopyright_post_passes_filters())
    return;
  $toolbar = icopyright_toolbar_common('OneButton', 'one-button-toolbar.js');
  // Wrap the toolbar with some styles
  $css = '.icx-toolbar{padding: 0 0 5px 0;}';
  if(get_option('icopyright_align') == 'right') {
    $toolbar = '<div class="icx-toolbar-align-right">' . $toolbar . '</div>';
    $css .= ' .icx-toolbar-align-right{float: right;}';
  }
  $toolbar .= '<style type="text/css">' . $css . '</style>';
  return $toolbar;
}

//Generate iCopyright interactive notice
function icopyright_interactive_notice() {
  if(!icopyright_post_passes_filters())
    return;

  global $post;
  $post_id = $post->ID;
  $pub_id_no = get_option('icopyright_pub_id');

  //construct copyright notice
  $publish_date = $post->post_date;
  $date = explode('-', $publish_date);
  $site_name = get_option('icopyright_site_name');
  $pname = addslashes(empty($site_name) ? get_bloginfo() : $site_name);
  $icx_copyright = "Copyright " . $date['0'] . " $pname";

  $server = icopyright_get_server();

  //construct icopyright interactive copyright notice

  $icn = <<<NOTICE
<!-- iCopyright Interactive Copyright Notice -->
<script type="text/javascript">
    var icx_publication_id = $pub_id_no;
    var icx_copyright_notice = '$icx_copyright';
    var icx_content_id = '$post_id';
</script>
<script type="text/javascript"
        src="$server/rights/js/copyright-notice.js"></script>
<noscript>
    <a style="color: #336699; font-family: Arial, Helvetica, sans-serif; font-size: 12px;"
       href="$server/3.$pub_id_no?icx_id=$post_id"
       target="_blank" title="Main menu of all reuse options">
      <img height="25" width="27" border="0" align="bottom"
           alt="[Reuse options]"
           src="$server/images/icopy-w.png"/>Click here for reuse options!</a>
</noscript>
<!-- iCopyright Interactive Copyright Notice -->
NOTICE;

  return $icn;
}


//WordPress Shortcode [icopyright horizontal toolbar]
function icopyright_horizontal_toolbar_shortcode($atts) {
  $h_toolbar = icopyright_horizontal_toolbar();
  return "<!--horizontal toolbar wrapper -->" . $h_toolbar . "<!--end of wrapper -->";
}
add_shortcode('icopyright horizontal toolbar', 'icopyright_horizontal_toolbar_shortcode');

//WordPress Shortcode [icopyright vertical toolbar]
function icopyright_vertical_toolbar_shortcode($atts) {
  $v_toolbar = icopyright_vertical_toolbar();
  return "<!--vertical toolbar wrapper -->" . $v_toolbar . "<!--end of wrapper -->";
}
add_shortcode('icopyright vertical toolbar', 'icopyright_vertical_toolbar_shortcode');

// WordPress shortcode [icopyright_onebutton_toolbar]
function icopyright_onebutton_toolbar_shortcode($atts) {
  $ob_toolbar = icopyright_onebutton_toolbar();
  return "<!--onebutton toolbar wrapper -->" . $ob_toolbar . "<!--end of wrapper -->";
}
add_shortcode('icopyright one button toolbar', 'icopyright_onebutton_toolbar_shortcode');

//WordPress Shortcode [interactive copyright notice]
function icopyright_interactive_copyright_notice_shortcode($atts) {
  $icn = icopyright_interactive_notice();
  return "<!--icopyright interactive notice wrapper -->" . $icn . "<!--end of wrapper -->";
}

add_shortcode('interactive copyright notice', 'icopyright_interactive_copyright_notice_shortcode');


//Since Version 1.0
//Added Multiple Post Display Option -- Version 2.8
//Added intensive condition checks -- Version 2.8
//function to filter content or excerpt and automatically add icopyright toolbars and interactive copyright notice
function auto_add_icopyright_toolbars($content) {

  //get settings from icopyright_admin option array

  // Do nothing if it isn't appropriate for us to add the content anyway
  $display_status = get_option('icopyright_display'); //deployment
  if(($display_status != 'auto') || is_feed() || is_attachment()) {
    return $content;
  }
  $selected_toolbar = get_option('icopyright_tools'); //toolbar selected

  //Single Post Display Option
  //valves includes, both, tools, notice.
  //both - means display both article tools and interactive copyright notice
  //tools - means display only article tools
  //notice - means displays only interactive copyright notice
  $single_display_option = get_option('icopyright_show');

  //Multiple Post Display Option
  //valves includes, both, tools, notice.
  //both - means display both article tools and interactive copyright notice
  //tools - means display only article tools
  //notice - means displays only interactive copyright notice
  //nothing - means hide all article tools and interactive notice.
  $multiple_display_option = get_option('icopyright_show_multiple');

  // What modes are we paying attention to?
  if(is_single() || is_page()) {
    $show_toolbar = ($single_display_option == 'both') || ($single_display_option == 'tools');
    $show_icn = ($single_display_option == 'both') || ($single_display_option == 'notice');
  } else {
    $show_toolbar = ($multiple_display_option == 'both') || ($multiple_display_option == 'tools');
    $show_icn = ($multiple_display_option == 'both') || ($multiple_display_option == 'notice');
  }

  // Build the toolbar and ICN if we need to display them
  if($show_toolbar) {
    if($selected_toolbar == 'horizontal')
      $pre = icopyright_horizontal_toolbar();
    else if($selected_toolbar == 'vertical')
      $pre = icopyright_vertical_toolbar();
    else
      $pre = icopyright_onebutton_toolbar();
  }
  if($show_icn) {
    $post = icopyright_interactive_notice();
  }

  // Regardless, return what we have
  return $pre . $content . $post;
}

//end function auto_add_icopyright_toolbars($content)

//since version 1.0
add_filter('the_content', 'auto_add_icopyright_toolbars');
//Version 1.0.8
//add toolbars in excerpt
add_filter('the_excerpt', 'auto_add_icopyright_toolbars');

//added in Version 1.0.8
//replace wp_trim_excerpt() found in wp-includes/formatting.php in version WordPress 3.0
//wp_trim_excerpt() is filtered in get_the_excerpt(),
//which is used by the_excerpt() to display excerpt in WordPress Loop (List of Multiple Post)
//We need to remove tool bar from content if empty excerpt
//so as to prevent toolbars duplication.
function icopyright_trim_excerpt($text) {
  $raw_excerpt = $text;

  //if empty text
  if ('' == $text) {
    //if there is no excerpt crafted from add post admin
    //WordPress will use the_content instead.
    //therefore we need to remove tools filter in content,
    //so as not to cause duplicate,
    //anyway the strip_tags below will cause the tools bars to malfunction
    remove_filter('the_content', 'auto_add_icopyright_toolbars');

    //The following are default wp_trim_excerpt() behaviour, left for theme compatibility.
    //codes copy and paste from wp_trim_excerpt with added explanation.

    //if empty use content.
    $text = get_the_content('');
    //remove shortcodes
    $text = strip_shortcodes($text);
    //apply content filters
    $text = apply_filters('the_content', $text);
    //replace > with html character entity to prevent script executing.
    $text = str_replace(']]>', ']]&gt;', $text);
    //strip out html tags.
    $text = strip_tags($text);
    //excerpt_length filter, default 55 words
    $excerpt_length = apply_filters('excerpt_length', 55);
    //excerpt_more filter, default [...]
    $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
    // split the phrase by any number of commas or space characters,
    // which include " ", \r, \t, \n and \f
    $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
    if (count($words) > $excerpt_length) {
      array_pop($words);
      $text = implode(' ', $words);
      $text = $text . $excerpt_more;
    } else {
      $text = implode(' ', $words);
    }
  }
  return apply_filters('icopyright_trim_excerpt', $text, $raw_excerpt);

}

//added in Version 1.0.8
//set priority to 0 so that wp_trim_excerpt() gets removed 
//and our function icopyright_trim_excerpt() gets added before any theme function filters into it.
remove_filter('get_the_excerpt', 'wp_trim_excerpt', 0);
add_filter('get_the_excerpt', 'icopyright_trim_excerpt', 0);


//added in Version 1.0.8
//add custom meta data box to admin page!

//adds a custom meta box to the add or edit Post and Page editor
function icopyright_add_custom_box() {

  if (function_exists('add_meta_box')) {

    add_meta_box('icopyright_sectionid', __('iCopyright Custom Field', 'icopyright_textdomain'),
      'icopyright_inner_custom_box', 'post', 'normal', 'high');

    add_meta_box('icopyright_sectionid', __('iCopyright Custom Field', 'icopyright_textdomain'),
      'icopyright_inner_custom_box', 'page', 'normal', 'high');

  }

}

//creates the inner fields for the custom meta box
function icopyright_inner_custom_box() {

  //Create icopyright_admin_nonce for verification
  echo '<input type="hidden" name="icopyright_noncename" id="icopyright_noncename" value="' .
    wp_create_nonce('icopyright_admin_nonce') . '" />';

  global $post;
  $content = $post->ID;

  //retrieve custom field data
  $data = get_post_meta($content, 'icopyright_hide_toolbar', TRUE);

  echo "<p><label>Do not offer iCopyright Article Tools on this story</label> <input name=\"icopyright_hide_toolbar\" type=\"checkbox\" value=\"yes\"";
  if ($data == 'yes') {
    echo 'checked';
  } else {
    echo '';
  }
  ;
  echo " /></p>";


}

//saves our custom field data, when the post is saved
function icopyright_save_postdata($post_id) {

  //check admin nonce
  if (!wp_verify_nonce($_POST['icopyright_noncename'], 'icopyright_admin_nonce')) {
    return $post_id;
  }

  //check user permission
  if ('page' == $_POST['post_type']) {
    if (!current_user_can('edit_page', $post_id))
      return $post_id;
  } else {
    if (!current_user_can('edit_post', $post_id))
      return $post_id;
  }

  //assign posted data
  $mydata = $_POST['icopyright_hide_toolbar'];

  //update custom field
  update_post_meta($post_id, 'icopyright_hide_toolbar', $mydata);

}

//hook in admin_menu action to create the custom meta box
add_action('admin_menu', 'icopyright_add_custom_box');

//hook in save_post action to save custom field data
add_action('save_post', 'icopyright_save_postdata');


//Since Version 1.1.2 
//function to get current page url to be used for 
//condition check in icopyright_admin_warning() found in icopyright.php
function icopyright_current_page_url() {
  $pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
  }
  else
  {
    $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
  }
  return $pageURL;
}

/**
 * Returns true if the post passes all the various filters and the article tools are eligible to be placed here.
 * The filters include such things as (a) the user not explicitly turning them off for a post; (b) the category
 *
 * @param $post_id integer the post ID to look at, or null for current post
 * @return bool true if the post passes
 */
function icopyright_post_passes_filters($post_id = NULL) {
  if($post_id == NULL) {
    global $post;
    $post_id = $post->ID;
  }
  // Is there even a configured publication ID? If not, no point in continuing
  $pub_id_no = get_option('icopyright_pub_id');
  if (empty($pub_id_no) || !is_numeric($pub_id_no)) {
    return FALSE;
  }
  // Has the site admin chosen to hide this particular post? If so then return false
  $icopyright_hide_toolbar = get_post_meta($post_id, 'icopyright_hide_toolbar', $single = TRUE);
  if ($icopyright_hide_toolbar == 'yes') {
    return FALSE;
  }
  // If this is a page, check to see if we're supposed to be on pages
  if(is_page()) {
    if(get_option('icopyright_display_on_pages') != 'yes')
      return FALSE;
  } else {
    // Does the post pass all the category filters? If not, then return false
    if(!icopyright_post_passes_category_filter($post_id)) {
      return FALSE;
    }
  }
  // Is there content within the post that we *know* can't be reused?
  if(icopyright_post_contains_known_unlicensable_content(get_post($post_id))) {
    return FALSE;
  }
  // Got this far? Then it passed all the filters
  return TRUE;
}

/**
 * Returns true if the body of the post contains content that is known unlicensable content
 * @param $post object the post in question
 * @return TRUE if this post looks like it has unlicenseable stuff in it
 */
function icopyright_post_contains_known_unlicensable_content($post) {
  // Be aggressive with the fingerprints: better to refuse than accidentally allow license
  $fingerprints = array(
    'src=\"(http:)?\/\/\w+\.icopyright\.net\/user\/viewFreeUse\.act\?fuid',
    'src=\"(http:)?\/\/\w+\.icopyright\.net\/user\/webEprint\.act\?id',
    'src=\"https:\/\/\d+.rp-api.com\/rjs\/repost-article.js'
  );
  foreach($fingerprints as $fingerprint) {
    if(preg_match("/$fingerprint/", $post->post_content)) {
      return TRUE;
    }
  }
  return FALSE;
}

/**
 * Returns true if either (a) no categories are selected; or (b) categories are selected, but the post
 * is in one or more of those categories; or (c) the admin has specifically said no categories. Returns false otherwise.
 *
 * @param $post_id integer the post ID
 * @return bool true if the post passes
 */
function icopyright_post_passes_category_filter($post_id) {
  // If the filter itself is not being used, then we always pass
  $use_filter = get_option('icopyright_use_category_filter');
  if($use_filter != 'yes') return TRUE;

  // Which categories are we allowing through?
  $icopyright_categories = get_option('icopyright_categories');
  if(count($icopyright_categories) == 0)
    return FALSE;

  // There are categories that we allow through, so check these
  $post_categories = wp_get_post_categories($post_id);
  foreach($post_categories as $cat ) {
    if(in_array($cat, $icopyright_categories))
      return TRUE;
  }

  // Got this far? Then we fail the filter
  return FALSE;
}


/**
 * Returns the default feed URL for this publication
 * @return string the default feed URL for this publication
 */
function icopyright_get_default_feed_url() {
  return site_url() . "/?feed=icopyright_feed&id=*";
}

/**
 * Given an error message, displays it on the page. If the error message is empty, then an OK message is shown.
 * @param $error_message
 */
function icopyright_display_status_update($error_message) {
  print '<div id="message" class="updated fade">';
  if(empty($error_message)) {
    print '<p><strong>Options Updated.</strong></p>';
  } else {
    print '<p style="font-size: 14px; margin: 5px;"><strong>The options were not successfully updated.</strong></p>';
    print '<ol>' . $error_message . '</ol>';
  }
  print '</div>';
  print '<script type="text/javascript">jQuery("#icopyright-warning").hide();</script>';
}

/**
 * Fetch the publication settings from the server and update the WP options.
 */
function icopyright_update_settings() {
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //
    // Only perform this update every 5 minutes.
    //
    $prevUpdate = get_option('icopyright_update_settings_time');
    $now = time();
    if (($now - $prevUpdate) < (60*5))
      return;

    //
    // Perform update
    //
    $icopyright_pubid = get_option('icopyright_pub_id');
    if(is_numeric($icopyright_pubid)) {
      $email = get_option('icopyright_conductor_email');
      $password = get_option('icopyright_conductor_password');

      //
      // Get the settings.
      //
      $settings = icopyright_get_publication_settings(ICOPYRIGHT_USERAGENT, $icopyright_pubid, $email, $password);
      if ($settings['success']) {
        update_option("icopyright_fname", $settings['fname']);
        update_option("icopyright_lname", $settings['lname']);
        update_option("icopyright_site_name", $settings['pname']);
        update_option("icopyright_site_url", $settings['purl']);
        update_option('icopyright_address_line1', $settings['line1']);
        update_option('icopyright_address_line2', $settings['line2']);
        update_option('icopyright_address_line3', $settings['line3']);
        update_option('icopyright_address_city', $settings['city']);
        update_option('icopyright_address_state', $settings['state']);
        update_option('icopyright_address_country', $settings['country']);
        update_option('icopyright_address_postal', $settings['postal']);
        update_option('icopyright_address_phone', $settings['phone']);
        update_option('icopyright_feed_url', $settings['furl']);
        if (!isset($settings['pricingOptimizerOptIn']) || strcmp($settings['pricingOptimizerOptIn'], '') == 0) {
          delete_option('icopyright_pricing_optimizer_opt_in');
          delete_option('icopyright_pricing_optimizer_apply_automatically');
        } else {
          update_option('icopyright_pricing_optimizer_opt_in', $settings['pricingOptimizerOptIn']);
          update_option('icopyright_pricing_optimizer_apply_automatically', $settings['pricingOptimizerApplyAutomatically']);
        }
        update_option('icopyright_ez_excerpt', $settings['ezExcerpt'] == 'true' ? 'yes' : 'no');
        update_option('icopyright_share', $settings['shareService'] == 'true' ? 'yes' : 'no');
        update_option('icopyright_syndication', $settings['syndication'] == 'true' ? 'yes' : 'no');
        update_option('icopyright_background', $settings['background']);
        update_option('icopyright_theme', $settings['theme']);
        update_option('icopyright_created_date', $settings['createdDate']);
        update_option('icopyright_update_settings_time', time());
      } else {
        print '<div id="message" class="updated">';
        print '<p><strong>WARNING</strong>: Unable to sync settings with iCopyright servers.</p>';
        print '<p><em>'.$settings['error'].'</em>.</p>';
        print '</div>';
      }
    }
  }
}

/**
 * Displays warning message if there is no connectivity
 */
function icopyright_check_connectivity() {
  $icopyright_pubid = get_option('icopyright_pub_id');
  if(is_numeric($icopyright_pubid)) {
    $email = get_option('icopyright_conductor_email');
    $password = get_option('icopyright_conductor_password');
    if(!icopyright_ping(ICOPYRIGHT_USERAGENT, $icopyright_pubid, $email, $password)) {
      print '<div id="message" class="updated">';
      print '<p><strong>WARNING</strong>: The iCopyright servers cannot communicate with this site. Services that require the link will be degraded.</p>';
      print '<p>Check your Conductor Feed URL, in <em>Advanced Settings</em>.</p>';
      print '</div>';

    }
  }
}

/**
 * Given an associative array of settings and results, returns an error message.
 * Returns NULL if there were no errors.
 * @param $results array map of setting => icopyright results
 * @return string message
 */
function icopyright_error_messages_from_response($results) {
  $msg = NULL;
  $unauthorized = FALSE;
  foreach($results as $setting => $res) {
    if(!icopyright_check_response($res)) {
      $msg .= '<li>Failed to update ' . $setting . ' (' . $res->http_expl . ')</li>';
      if($res->http_code == 401) $unauthorized = TRUE;
    }
  }
  // Special case: unauthorized so just say that, no need to list everything
  if($unauthorized) {
    $msg = '<li>Your email address and password were not accepted, so no changes were made. ' .
      'Use <em>Advanced Settings</em> below to make changes.</li>';
  }
  return $msg;
}

?>