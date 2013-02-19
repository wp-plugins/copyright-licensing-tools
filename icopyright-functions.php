<?php
//This file contains functions of icopyright plugin

function icopyright_create_tou_form() {
  //check if curl is loaded, if not display message and hide registration form.
  $loaded_extension = get_loaded_extensions();
  if (!in_array("curl", $loaded_extension)) {
    print '<div id="curl_notice" class="updated fade"><p>A PHP extension (cURL extension), which is needed for this plugin to work, is not installed.</p></div>';
    die();
  }

  //form fields and inputs
  $form = '<div class="icopyright_tou" id="icopyright_tou">';
  $form .= '<form name="icopyright_tou_form" id="icopyright_tou_form" method="post" action="" onsubmit="return validate_icopyright_form(this)">';
  $form .= "<div id='register_error_message' class='updated faded' style='display:none;'></div>";
  $form .= '<h3>iCopyright Toolbar</h3>';
  $form .= '<p>I accept the <a href="' . ICOPYRIGHT_URL . 'publisher/statichtml/CSA-Online-Plugin.pdf" target="_blank">terms of use</a>.</p>';
  $form .= '<input id="tou" name="tou" type="hidden" value="true"/>';
  $form .= '<input type="submit" name="accept-tou" value="Agree" class="button-primary"/>';
  $form .= "</form>";
  $form .= "</div>";
  echo $form;
}

//function to dynamically create registration form!
function icopyright_create_register_form($fname, $lname, $email, $password, $pname, $url) {

  //check whether form has been submitted with errors
  //if there is errors change display form to block
  //so as to retain value for user to re-enter form for posting
  global $show_icopyright_register_form; // global value found in function icopyright_admin() in icopyright-admin.php
  if ($show_icopyright_register_form == 'true') {
    $display_form = 'style="display:block"';
  } else {
    $display_form = 'style="display:none"';
  }

  //form fields and inputs
  $form = "<div class=\"icopyright_registration\" id=\"icopyright_registration_form\" $display_form>";
  $form .= '<form name="icopyright_register_form" id="icopyright_register_form" method="post">';
  $form .= "<div id='register_error_message' class='updated faded' style='display:none;'></div>";
  $form .= '<h3>Registration Form</h3><p><a href="#" onclick="hide_icopyright_form()" style="font-size:12px;margin:0 0 0 10px;text-decoration:none;">(If you already have a publication ID, click here to enter it under Show Advanced Settings.)</a></p>';
  $form .= '<p>If you need assistance, please email <a href="mailto:wordpress@icopyright.com">wordpress@icopyright.com</a> or get <a href="http://info.icopyright.com/wordpress-setup" target="_blank">help</a>.</p>';
  $form .= '<table class="widefat">';

  //fname
  $form .= "<tr><td colspan=\"2\"><h2>About You</h2></td></tr>";
  $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>First Name:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"fname\" id=\"fname\" value=\"$fname\"/></td></tr>";
  $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Last Name:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"lname\" value=\"$lname\"/></td></tr>";

  if (!strlen($email)) { //check if email variable is not set, we use current user email
    global $current_user;
    get_currentuserinfo();
    $email = $current_user->user_email;
  }
  $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Email Address:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"email\" id=\"email\" value=\"$email\"/></td></tr>";
  $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Password:</label></td><td><input style=\"width:300px\" type=\"password\" name=\"password\" id=\"password\" value=\"$password\"/></td></tr>";
  $form .= "<tr><td colspan=\"2\"><h2>About This Site</h2></td></tr>";

  if (!strlen($pname)) {
    $pname = get_bloginfo('name');
  }
  $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Site Name:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"pname\" value=\"$pname\"/></td></tr>";

  //auto populate using WordPress site url
  //since version 1.1.4
  if (!strlen($url)) {
    $url = get_bloginfo('url') . "/";
  }
  //url
  $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Site Address (URL):</label></td><td><input style=\"width:300px\" type=\"text\" name=\"url\" value=\"$url\"/></td></tr>";

  $form .= '</td></tr></table>';

  //If this is multisite we post in blog id for feed as hidden variable.
  if (is_multisite()) {
    global $blog_id;
    $form .= "<input type='hidden' name='blog_id' value='$blog_id'/>";
  }

  $form .= '<br/><input type="hidden" name="submitted2" value="submit-initial-registration"/>
<input type="submit" name="submit" value="Submit" class="button-primary" id="registersubmit"/>';
  $form .= "</form>";
  $form .= "</div>";
  echo $form;
}

/**
 * Simple function to create and return the account form.
 */
function icopyright_create_account_form() {
  $rv = '<p>Indicate below where we should mail your revenue checks.</p>';
  $rv .= '<table class="form-table"><tbody><tr align="top">';
  $rv .= icopyright_make_account_row('First Name',150,'fname');
  $rv .= icopyright_make_account_row('Last Name',150,'lname');
  $rv .= icopyright_make_account_row('Site Name',200,'site_name');
  $rv .= icopyright_make_account_row('Site URL',200,'site_url');
  $rv .= icopyright_make_account_row('Address',200,'address_line1');
  $rv .= icopyright_make_account_row('',200,'address_line2');
  $rv .= icopyright_make_account_row('',200,'address_line3');
  $rv .= icopyright_make_account_row('City', 200, 'address_city');
  $rv .= icopyright_make_account_row('State', 50, 'address_state');
  $rv .= icopyright_make_account_row('Country', 50, 'address_country', 2);
  $rv .= icopyright_make_account_row('Postal Code', 100, 'address_postal');
  $rv .= icopyright_make_account_row('Phone', 100, 'address_phone');
  $rv .= '</tbody></table>';
  return $rv;
}
function icopyright_make_account_row($heading, $width, $field, $max = NULL) {
  $current_value = icopyright_account_value_for_post($field);
  $row = '<tr align="top">';
  $row .= '<th scope="row">' . $heading . '</th>';
  $row .= "<td><input type=\"text\" name=\"icopyright_${field}\" style=\"width: ${width}px;\" ";
  if(is_numeric($max)) $row .= "maxlength=\"$max\" size=\"$max\" ";
  $row .= "value=\"${current_value}\"/></td>";
  $row .= '</tr>' . "\n";
  return $row;
}

/**
 * Given an argument that corresponds to an icopyright account variable, either returns the value from the post array
 * (if possible), or from the iCopyright accounts system variable. This is useful in populating the account form.
 * @param $parg
 */
function icopyright_account_value_for_post($parg) {
  if (isset($_POST["icopyright_$parg"]))
    return stripslashes($_POST["icopyright_$parg"]);
  else {
    $icopyright_account = get_option('icopyright_account');
    return $icopyright_account[$parg];
  }
}


//WordPress Shortcodes to generate tool bars for content
//functions to generate tool bars, reuseable for auto inclusion or manual inclusion.
//Admin option to select toolbars and change auto to manual display

// Returns the common prefix for all the toolbars
function icopyright_toolbar_common($comment, $script) {
  global $post;
  $post_id = $post->ID;
  $admin_option = get_option('icopyright_admin');
  $pub_id_no = $admin_option['pub_id'];

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
  $admin_option = get_option('icopyright_admin');
  if($admin_option['align'] == 'right') {
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
  $admin_option = get_option('icopyright_admin');
  $css = $admin_option['align'] == 'right' ? '.icx-toolbar{padding: 0 0 0 5px;}' : '.icx-toolbar{padding: 0 5px 0 0;}';
  if($admin_option['align'] == 'right') {
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
  $css = '.icx-toolbar-closure{clear:both;} .icx-toolbar{padding: 0 0 5px 0;}';
  $admin_option = get_option('icopyright_admin');
  if($admin_option['align'] == 'right') {
    $toolbar = '<div class="icx-toolbar-align-right">' . $toolbar . '</div>';
    $css .= '.icx-toolbar-align-right{float: right;}';
  }
  if($admin_option['display'] == 'auto') {
    // In auto display, add a clear block afterwards to make sure that
    $toolbar .= '<div class="icx-toolbar-closure"></div>';
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
  $admin_option = get_option('icopyright_admin');
  $pub_id_no = $admin_option['pub_id'];

  //construct copyright notice
  $publish_date = $post->post_date;
  $date = explode('-', $publish_date);
  $account_option = get_option('icopyright_account');
  $pname = addslashes(empty($account_option['site_name']) ? get_bloginfo() : $account_option['site_name']);
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
  $setting = get_option('icopyright_admin');

  // Do nothing if it isn't appropriate for us to add the content anyway
  $display_status = $setting['display']; //deployment
  if(($display_status != 'auto') || is_feed() || is_attachment()) {
    return $content;
  }
  $selected_toolbar = $setting['tools']; //toolbar selected

  //Single Post Display Option
  //valves includes, both, tools, notice.
  //both - means display both article tools and interactive copyright notice
  //tools - means display only article tools
  //notice - means displays only interactive copyright notice
  $single_display_option = $setting['show'];

  //Multiple Post Display Option
  //valves includes, both, tools, notice.
  //both - means display both article tools and interactive copyright notice
  //tools - means display only article tools
  //notice - means displays only interactive copyright notice
  //nothing - means hide all article tools and interactive notice.
  $multiple_display_option = $setting['show_multiple'];

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
 * Returns an array of categories that the tools should be placed on. Can be empty but will never be null.
 * @return
 */
function icopyright_selected_categories() {
  $setting = get_option('icopyright_admin');
  $categories = $setting['categories'];
  if(strlen($categories) > 0) {
    return explode(',', $categories);
  } else {
    return array();
  }
}

/**
 * Returns true if the post passes all the various filters and the article tools are eligible to be placed here.
 * The filters include such things as (a) the user not explicitly turning them off for a post; (b) the category
 *
 * @return bool true if the post passes
 */
function icopyright_post_passes_filters() {
  global $post;
  $post_id = $post->ID;

  // Is there even a configured publication ID? If not, no point in continuing
  $admin_option = get_option('icopyright_admin');
  $pub_id_no = $admin_option['pub_id'];
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
    if($admin_option['display_on_pages'] != 'yes')
      return FALSE;
  } else {
    // Does the post pass all the category filters? If not, then return false
    if(!icopyright_post_passes_category_filter($post_id)) {
      return FALSE;
    }
  }
  // Got this far? Then it passed all the filters
  return TRUE;
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
  $setting = get_option('icopyright_admin');
  $use_filter = $setting['use_category_filter'];
  if($use_filter != 'yes') return TRUE;

  // Which categories are we allowing through?
  $icopyright_categories = icopyright_selected_categories();
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

//auto update admin setting with response publication id,
//and other default values.
function icopyright_admin_defaults() {
  return array(
    'display' => 'auto',
    'tools' => 'horizontal',
    'align' => 'right',
    'display_on_pages' => 'yes',
    'theme' => 'CLASSIC',
    'background' => 'OPAQUE',
    'show' => 'both',
    'show_multiple' => 'notice',
    'ez_excerpt' => 'yes',
    'syndication' => 'yes',
    'share' => 'yes',
    'categories' => '',
    'use_category_filter' => 'no',
  );
}

/**
 * After a new publication has been created, set up all the various settings
 * @param $pid
 * @param $email
 * @param $password
 */
function icopyright_set_up_new_publication($pid, $email, $password) {
  $icopyright_admin_default = icopyright_admin_defaults();
  $icopyright_admin_default['pub_id'] = $pid;

  $plugin_feed_url = icopyright_get_default_feed_url();
  $icopyright_admin_default['feed_url'] = $plugin_feed_url;
  icopyright_post_update_feed_url($pid, $plugin_feed_url, ICOPYRIGHT_USERAGENT, $email, $password);

  update_option('icopyright_admin', $icopyright_admin_default);
  update_option('icopyright_conductor_password', $password);
  update_option('icopyright_conductor_email', $email);
}

/**
 * Initialize the account with what information we have
 * @param $fname
 * @param $lname
 * @param $pname
 * @param $url
 */
function icopyright_set_up_new_account($fname, $lname, $pname, $url) {
  $icopyright_account = array(
    'fname' => $fname,
    'lname' => $lname,
    'site_name' => $pname,
    'site_url' => $url,
  );
  update_option('icopyright_account', $icopyright_account);
}


/**
 * Returns the default feed URL for this publication, based on whether this is a singlesite or multisite installation
 * @return string the default feed URL for this publication
 */
function icopyright_get_default_feed_url() {
  $plugin_feed_url = NULL;
  $blog_id = $_POST['blog_id'];
  if (!empty($blog_id)) {
    //this is multisite, we use main blog url and sub blog id for feed.
    $plugin_feed_url .= get_site_url(1) . "/wp-content/plugins/copyright-licensing-tools/icopyright_xml.php?blog_id=$blog_id&id=*";
  } else {
    //this is single site install, no need for blog id.
    //post in old feed url structure.
    $plugin_feed_url .= WP_PLUGIN_URL . "/copyright-licensing-tools/icopyright_xml.php?id=*";
  }
  return $plugin_feed_url;
}


/**
 * Posts the changes made to the settings page
 */
function icopyright_post_settings() {
  //assign error
  $error_message = '';

  //check nonce
  check_admin_referer('icopyright_settings-options');

  //assign posted value
  $icopyright_pubid = sanitize_text_field(stripslashes($_POST['icopyright_pubid']));
  $icopyright_display = sanitize_text_field(stripslashes($_POST['icopyright_display']));
  $icopyright_tools = sanitize_text_field(stripslashes($_POST['icopyright_tools']));
  $icopyright_display_on_pages = sanitize_text_field(stripslashes($_POST['icopyright_display_on_pages']));
  $icopyright_align = sanitize_text_field(stripslashes($_POST['icopyright_align']));
  $icopyright_show = sanitize_text_field(stripslashes($_POST['icopyright_show']));
  $icopyright_show_multiple = sanitize_text_field(stripslashes($_POST['icopyright_show_multiple']));
  $icopyright_ez_excerpt = sanitize_text_field(stripslashes($_POST['icopyright_ez_excerpt']));
  $icopyright_syndication = sanitize_text_field(stripslashes($_POST['icopyright_syndication']));
  $icopyright_share = sanitize_text_field(stripslashes($_POST['icopyright_share']));
  $icopyright_use_copyright_filter = sanitize_text_field(stripslashes($_POST['icopyright_use_category_filter']));
  $icopyright_conductor_email = sanitize_email(stripslashes($_POST['icopyright_conductor_email']));
  $icopyright_conductor_password = sanitize_text_field(stripslashes($_POST['icopyright_conductor_password']));
  $icopyright_theme = sanitize_text_field(stripslashes($_POST['icopyright_article_tools_theme']));
  $icopyright_background = sanitize_text_field(stripslashes($_POST['icopyright_background']));
  $icopyright_feed_url = sanitize_text_field(stripslashes($_POST['icopyright_feed_url']));

  $icopyright_fname = sanitize_text_field(stripslashes($_POST['icopyright_fname']));
  $icopyright_lname = sanitize_text_field(stripslashes($_POST['icopyright_lname']));
  $icopyright_site_name = sanitize_text_field(stripslashes($_POST['icopyright_site_name']));
  $icopyright_site_url = sanitize_text_field(stripslashes($_POST['icopyright_site_url']));
  $icopyright_address_line1 = sanitize_text_field(stripslashes($_POST['icopyright_address_line1']));
  $icopyright_address_line2 = sanitize_text_field(stripslashes($_POST['icopyright_address_line2']));
  $icopyright_address_line3 = sanitize_text_field(stripslashes($_POST['icopyright_address_line3']));
  $icopyright_address_city = sanitize_text_field(stripslashes($_POST['icopyright_address_city']));
  $icopyright_address_state = sanitize_text_field(stripslashes($_POST['icopyright_address_state']));
  $icopyright_address_country = sanitize_text_field(stripslashes($_POST['icopyright_address_country']));
  $icopyright_address_postal = sanitize_text_field(stripslashes($_POST['icopyright_address_postal']));
  $icopyright_address_phone = sanitize_text_field(stripslashes($_POST['icopyright_address_phone']));

  //check publication id
  if (empty($icopyright_pubid)) {
    $error_message .= '<li>Empty Publication ID, Please key in Publication ID or sign up for one!</li>';
  }

  //check for numerical publication id when id is not empty
  if (!empty($icopyright_pubid) && !is_numeric($icopyright_pubid)) {
    $error_message .= '<li>Publication ID error, Please key in numerics only!</li>';
  }

  //check conductor email
  if (empty($icopyright_conductor_email)) {
    $error_message .= '<li>Empty Email Address, Please key in Conductor Login Email Address!</li>';
  } else {
    //update option
    update_option('icopyright_conductor_email', $icopyright_conductor_email);
  }

  //check conductor password
  if (empty($icopyright_conductor_password)) {
    $error_message .= '<li>Empty Password, Please key in Conductor Login Password!</li>';
  } else {
    //update option
    update_option('icopyright_conductor_password', $icopyright_conductor_password);
  }
  if(!empty($error_message)) return;

  //do ez excerpt setting, after email address and password are updated for old users.
  $conductor_password = get_option('icopyright_conductor_password');
  $conductor_email = get_option('icopyright_conductor_email');
  $user_agent = ICOPYRIGHT_USERAGENT;

  if(empty($icopyright_display)) {
    icopyright_set_up_new_publication($icopyright_pubid, $icopyright_conductor_email, $icopyright_conductor_password);
    icopyright_display_publication_welcome();
  } else {
    $results = array();
    $error_message = '';

    // Submit as appropriate
    $ez_res = icopyright_post_ez_excerpt($icopyright_pubid, ($icopyright_ez_excerpt == 'yes'), $user_agent, $conductor_email, $conductor_password);
    $results['EZ Excerpt Setting'] = $ez_res;
    $syndicate_res = icopyright_post_syndication_service($icopyright_pubid, ($icopyright_syndication == 'yes'), $user_agent, $conductor_email, $conductor_password);
    $results['Syndication Setting'] = $ez_res;
    $share_res = icopyright_post_share_service($icopyright_pubid, ($icopyright_share == 'yes'), $user_agent, $conductor_email, $conductor_password);
    $results['Sharing Setting'] = $ez_res;
    $t_res = icopyright_post_toolbar_theme($icopyright_pubid, $icopyright_theme, $icopyright_background, $user_agent, $conductor_email, $conductor_password);
    $results['Toolbar Setting'] = $ez_res;
    $error_message = icopyright_error_messages_from_response($results);

    // Save publication info details
    $i_res = icopyright_post_publication_info($icopyright_pubid, $icopyright_fname, $icopyright_lname,
      $icopyright_site_name, $icopyright_site_url, $icopyright_feed_url,
      $icopyright_address_line1, $icopyright_address_line2, $icopyright_address_line3, $icopyright_address_city,
      $icopyright_address_state, $icopyright_address_postal, $icopyright_address_country, $icopyright_address_phone,
      $user_agent, $conductor_email, $conductor_password
    );
    if (icopyright_check_response($i_res) != TRUE) {
      // The update failed; let's pull out the errors and report them
      $error_message .= '<li>Failed to update account information</li><ol>';
      $responses = @simplexml_load_string($i_res->response);
      foreach ($responses->status->messages->message as $m) {
        $error_message .=  "<li>$m</li>";
      }
      $error_message .= '</ol>';
    }

    // Check selected categories input for sensibility
    $selectedCategories = array();
    $selectedCat = isset($_POST['selectedCat']) ? $_POST['selectedCat'] : array();
    foreach($selectedCat as $catid) {
      if(is_numeric($catid)) $selectedCategories[] = $catid;
    }

    // Save the icopyright admin settings.
    $icopyright_admin = array('pub_id' => $icopyright_pubid,
      'display' => $icopyright_display,
      'tools' => $icopyright_tools,
      'display_on_pages' => $icopyright_display_on_pages,
      'align' => $icopyright_align,
      'background' => $icopyright_background,
      'theme' => $icopyright_theme,
      'show' => $icopyright_show,
      'show_multiple' => $icopyright_show_multiple,
      'ez_excerpt' => $icopyright_ez_excerpt,
      'syndication' => $icopyright_syndication,
      'share' => $icopyright_share,
      'categories' => implode(',', $selectedCategories),
      'use_category_filter' => $icopyright_use_copyright_filter,
      'feed_url' => $icopyright_feed_url,
    );
    // Save the account info also
    $icopyright_account = array(
      'fname' => $icopyright_fname,
      'lname' => $icopyright_lname,
      'site_name' => $icopyright_site_name,
      'site_url' => $icopyright_site_url,
      'address_line1' => $icopyright_address_line1,
      'address_line2' => $icopyright_address_line2,
      'address_line3' => $icopyright_address_line3,
      'address_city' => $icopyright_address_city,
      'address_state' => $icopyright_address_state,
      'address_country' => $icopyright_address_country,
      'address_postal' => $icopyright_address_postal,
      'address_phone' => $icopyright_address_phone,
    );

    //check if no error, then update admin setting
    if (empty($error_message)) {
      //update array value icopyright admin into WordPress Database Options table
      update_option('icopyright_admin', $icopyright_admin);
      update_option('icopyright_account', $icopyright_account);
    }
  }
  icopyright_display_status_update($error_message);
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
 * Simply spits out a welcome message
 */
function icopyright_display_publication_welcome() {
  $icopyright_conductor_url = ICOPYRIGHT_URL . "publisher/publisherMessages.act?type=welcome";
  $form = icopyright_graphical_link_to_conductor($icopyright_conductor_url, NULL, 'conductor-login');
  print '<div id="message" class="updated fade">';
  print $form;
  print '<h2>Congratulations, your website is now live with iCopyright!</h2>';
  print '<p>';
  print 'Please review the default settings below and make any changes you wish. You may find it helpful to view the ';
  print 'video <a href="http://info.icopyright.com/icopyright-video" target="_blank">"Introduction to iCopyright"</a>. ';
  print 'Feel free to visit your new <a href="' . $icopyright_conductor_url . '" target="_blank" id="welcome-anchor">Conductor</a> ';
  print 'account to explore your new capabilities. A welcome email has been sent to you with some helpful hints.';
  print '</p>';
  print '</div>';
  print '<script type="text/javascript">jQuery("#icopyright-warning").hide();jQuery("#welcome-anchor").click(function() { jQuery("#conductor-login").submit(); return false;});</script>';
}

/**
 * Displays warning message if there is no connectivity
 */
function icopyright_check_connectivity() {
  $icopyright_option = get_option('icopyright_admin');
  $icopyright_pubid = $icopyright_option['pub_id'];
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
 * Posts the new publisher (registration) form
 */
function icopyright_post_registration_form() {
  $post = array(
    'fname' => sanitize_text_field(stripslashes($_POST['fname'])),
    'lname' => sanitize_text_field(stripslashes($_POST['lname'])),
    'email' => sanitize_text_field(stripslashes($_POST['email'])),
    'password' => sanitize_text_field(stripslashes($_POST['password'])),
    'pname' => sanitize_text_field(stripslashes($_POST['pname'])),
    'url' => sanitize_text_field(stripslashes($_POST['url'])),
  );
  $postdata = http_build_query($post);
  $rv = icopyright_post_new_publisher($postdata, ICOPYRIGHT_USERAGENT, $post['email'], $post['password']);
  $xml = @simplexml_load_string($rv->response);
  if (icopyright_check_response($rv)) {
    // Success: store the publication ID that got sent as a variable and set up the publication
    $pid = (string)$xml->publication_id;
    if(is_numeric($pid)) {
      icopyright_set_up_new_publication($pid, $post['email'], $post['password']);
      icopyright_set_up_new_account($post['fname'], $post['lname'], $post['pname'], $post['url']);
      icopyright_display_publication_welcome();
      return;
    }
  }

  // Was there an error, or did the response not even go through?
  if(empty($xml)) {
    print '<div id="message" class="updated fade">';
    print '<p><strong>Sorry! Publication ID Registration Service is not available. This may be due to API server maintenance. Please try again later.</strong></p>';
    print '</div>';
  } else {
    // There was a failure for the post, as in problems with the form field elements
    print '<div id="message" class="updated fade">';
    print '<p><strong>The following fields needs your attention</strong></p>';
    print '<ol>';
    foreach ($xml->status->messages->message as $error_message) {
      print '<li>' . $error_message . '</li>';
    }
    print '</ol></div>';

    //check terms of agreement box, since the blogger had already checked and posted the form.
    global $icopyright_tou_checked;
    $icopyright_tou_checked = 'true';
    global $show_icopyright_register_form;
    $show_icopyright_register_form = 'true';
  }
}

/**
 * Try to register for a new publication record, guessing some reasonable values for registration. We then send the
 * user to the general options page, which takes it from there.
 */
function icopyright_preregister() {
  // First time being activated, so set up with appropriate defaults
  // Make some reasonable guesses about what to use; the user can change them later
  global $current_user;
  get_currentuserinfo();
  $email = $current_user->user_email;
  $fname = $current_user->user_firstname;
  if(empty($fname)) $fname = 'Anonymous';
  $lname = $current_user->user_lastname;
  if(empty($lname)) $lname = 'User';
  $pname = get_bloginfo('name');
  $url = get_bloginfo('url') . "/";
  $password = wp_generate_password(12, FALSE, FALSE);

  $postdata = array(
    'fname' => $fname,
    'lname' => $lname,
    'email' => $email,
    'pname' => $pname,
    'url' => $url,
    'password' => $password,
  );
  $rv = icopyright_post_new_publisher(http_build_query($postdata), ICOPYRIGHT_USERAGENT, $email, $password);
  if (icopyright_check_response($rv)) {
    // Success: store the publication ID that got sent as a variable
    $xml = @simplexml_load_string($rv->response);
    $pid = (string)$xml->publication_id;
    if(is_numeric($pid)) {
      icopyright_set_up_new_publication($pid, $email, $password);
      icopyright_set_up_new_account($fname, $lname, $pname, $url);
      icopyright_display_publication_welcome();
    }
  }
  // Failure? That's OK, user will be sent to the registration page shortly
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

/**
 * Builds a login link which, when pushed, will automatically log the user into WP. Password is
 * sensitive so we include it here.
 * @param $page string the page to direct to
 * @param $img string image containing the button
 * @param $id string CSS id for the form
 * @return string the HTML for the form
 */
function icopyright_graphical_link_to_conductor($page, $img = NULL, $id = NULL) {
  $options = get_option('icopyright_admin');
  $rv = '<form action="' . icopyright_get_server(TRUE) . '/publisher/signin.act" method="POST" name="signin" target="_blank"';
  if($id != NULL) {
    $rv .= " id=\"$id\"";
  }
  $rv .= ">\n";
  $rv .= '  <input type="hidden" name="_publication" value="' . $options['pub_id'] . '">' . "\n";
  $rv .= '  <input type="hidden" name="email" value="' . get_option('icopyright_conductor_email') . '">' . "\n";
  $rv .= '  <input type="hidden" name="password" value="' . get_option('icopyright_conductor_password') . '">' . "\n";
  $rv .= '  <input type="hidden" name="ru" value="' . $page . '">' . "\n";
  $rv .= '  <input type="hidden" name="signin" value="signin">' . "\n";
  if($img != NULL) {
    $rv .= '  <input type="image" name="signin" src="' . plugin_dir_url(__FILE__) . 'images/' . $img . '">';
  }
  $rv .= "\n</form>\n";
  return $rv;
}

?>