<?php
//This file contains functions of icopyright plugin

//function to dynamically create registration form!
function create_icopyright_register_form($fname, $lname, $email, $password, $pname, $url) {

  //check whether form has been submitted with errors
  //if there is errors change display form to block
  //so as to retain value for user to re-enter form for posting

  global $show_icopyright_register_form; // global value found in function icopyright_admin() in icopyright-admin.php
  if ($show_icopyright_register_form == 'true') {
    $display_form = 'style="display:block"';
  } else {
    $display_form = 'style="display:none"';
  }

  //check if curl is loaded, if not display message and hide registration form.
  $loaded_extension = get_loaded_extensions();
  if (!in_array("curl", $loaded_extension)) {
    echo "<div id='curl_notice' class='updated fade'><p>A PHP extension ( cURL extension ), which is needed for our Registration Form to work, is not installed by your Hosting Provider. You will need to request for a Publication Id <a href='http://info.icopyright.com/publishers-sign-in-sign-up' target='_blank'>here.</a><a href='#' onclick='document.getElementById(\"curl_notice\").style.display=\"none\";document.getElementById(\"icopyright_option\").style.display=\"block\";' style='font-size:12px;text-decoration:none;'>(If you already have a publication ID, click here to enter and save it under Show Advanced Settings.)</a></p></div>";
    $initial_js = "<script type=\"text/javascript\">\n";
    $initial_js .= "document.getElementById('icopyright_option').style.display='none';";
    $initial_js .= "</script>\n";
    echo $initial_js;
    die();
  }

  //form fields and inputs
  $form = "<div class=\"icopyright_registration\" id=\"icopyright_registration_form\" $display_form>";

  $form .= '<form name="icopyright_register_form" id="icopyright_register_form" method="post" action="" onsubmit="return validate_icopyright_form(this)">';

  $form .= "<div id='register_error_message' class='updated faded' style='display:none;'></div>";

  $form .= '<h3>Registration Form</h3><p><a href="#" onclick="hide_icopyright_form()" style="font-size:12px;margin:0 0 0 10px;text-decoration:none;">(If you already have a publication ID, click here to enter it under Show Advanced Settings.)</a></p>';

  $form .= '<p>If you need assistance, please email <a href="mailto:wordpress@icopyright.com">wordpress@icopyright.com</a> or get <a href="http://info.icopyright.com/wordpress-setup" target="_blank">help</a>.</p>';

  $form .= '<table class="widefat">';

  //fname
  $form .= "<tr><td colspan=\"2\"><h2>About You</h2></td></tr>";
  $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>First Name:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"fname\" id=\"fname\" value=\"$fname\"/></td></tr>";

  //lname
  $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Last Name:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"lname\" value=\"$lname\"/></td></tr>";

  //auto populate current user email
  //since version 1.1.4

  if (!isset($email)) { //check if email variable is not set, we use current user email
    global $current_user;
    get_currentuserinfo();
    $email = $current_user->user_email;
  }

  //email
  $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Email Address:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"email\" id=\"email\" value=\"$email\"/></td></tr>";

  //password
  $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Password:</label></td><td><input style=\"width:300px\" type=\"password\" name=\"password\" id=\"password\" value=\"$password\"/></td></tr>";

  //pname
  $form .= "<tr><td colspan=\"2\"><h2>About This Site</h2></td></tr>";

  if (!isset($pname)) {
    $pname = get_bloginfo('name');
  }
  $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Site Name:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"pname\" value=\"$pname\"/></td></tr>";

  //auto populate using WordPress site url
  //since version 1.1.4
  if (!isset($url)) {
    $url = get_bloginfo('url') . "/";
  }
  //url
  $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Site Address (URL):</label></td><td><input style=\"width:300px\" type=\"text\" name=\"url\" value=\"$url\"/></td></tr>";

  $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Terms of Use:</label></td><td>I agree with the<a href='";
  $form .= ICOPYRIGHT_URL . "publisher/statichtml/CSA-Online-Plugin.pdf";
  $form .= "' target='_blank'> terms of use.</a> <input id=\"tou\" name=\"tou\" type=\"checkbox\" value=\"true\" style='border:none;'";

  //get global value to determine whether form has been posted before.
  //if true, we will check the checkbox.
  //global variable set in icopyright-admin.php line 103
  global $icopyright_tou_checked;
  if ($icopyright_tou_checked == 'true') {
    $form .= "checked=yes></td></tr>";
  } else {
    $form .= "></td></tr>";
  }

  $form .= '</table>';

  //If this is multisite we post in blog id for feed as hidden variable.
  if (is_multisite()) {
    global $blog_id;
    $form .= "<input type='hidden' name='blog_id' value='$blog_id'/>";
  }


  $form .= '<br/><input type="hidden" name="submitted2" value="yes-post-me"/>
<input type="submit" name="submit" value="Submit" class="button-primary" id="registersubmit"/>';

  $form .= "</form>";

  $form .= "</div>";

  echo $form;

}


//WordPress Shortcodes to generate tool bars for content
//functions to generate tool bars, reuseable for auto inclusion or manual inclusion.
//Admin option to select toolbars and change auto to manual display

//Generate Horizontal Toolbar from hosted script or directy
function icopyright_horizontal_toolbar() {
  //get publication id from options table from icopyright_admin array
  $admin_option = get_option('icopyright_admin');
  $pub_id_no = $admin_option['pub_id'];
  if (empty($pub_id_no) || !is_numeric($pub_id_no)) {
    return;
  }

  //get post id
  global $post;
  $post_id = $post->ID;
  if(!icopyright_post_passes_category_filter($post_id)) {
    return;
  }
  // if blogger choose to hide particular post, we will not display it
  $icopyright_hide_toolbar = get_post_meta($post->ID, 'icopyright_hide_toolbar', $single = true);
  if ($icopyright_hide_toolbar == 'yes') {
    return;
  }

  // Build up the toolbar piece by piece
  $toolbar = "\n<!-- iCopyright Horizontal Article Toolbar -->\n";
  $toolbar .= "<script type=\"text/javascript\">\n";
  $toolbar .= "var icx_publication_id = $pub_id_no;\n";
  $toolbar .= "var icx_content_id = '$post_id';\n";
  $toolbar .= "</script>\n";
  $toolbar_script_url = ICOPYRIGHT_URL . 'rights/js/horz-toolbar.js'; //ICOPYRIGHT_URL constant defined in icopyright.php
  $toolbar .= "<script type=\"text/javascript\" src=\"$toolbar_script_url\"></script>\n";
  $toolbar .= "<!--End of iCopyright Horizontal Article Toolbar -->\n";

  // Wrap the toolbar with some styles
  $css = '.icx-toolbar-closure{clear: both;}';
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
    //get publication id from options table from icopyright_admin array
  $admin_option = get_option('icopyright_admin');
  $pub_id_no = $admin_option['pub_id'];
  if (empty($pub_id_no) || !is_numeric($pub_id_no)) {
    return;
  }

  //get post id
  global $post;
  $post_id = $post->ID;
  if(!icopyright_post_passes_category_filter($post_id)) {
    return;
  }
  // if blogger choose to hide particular post, we will not display it
  $icopyright_hide_toolbar = get_post_meta($post->ID, 'icopyright_hide_toolbar', $single = true);
  if ($icopyright_hide_toolbar == 'yes') {
    return;
  }

  // Build up the toolbar piece by piece
  $toolbar = "\n<!-- iCopyright Vertical Article Toolbar -->\n";
  $toolbar .= "<script type=\"text/javascript\">\n";
  $toolbar .= "var icx_publication_id = $pub_id_no;\n";
  $toolbar .= "var icx_content_id = '$post_id';\n";
  $toolbar .= "</script>\n";
  $toolbar_script_url = ICOPYRIGHT_URL . 'rights/js/vert-toolbar.js'; //ICOPYRIGHT_URL constant defined in icopyright.php
  $toolbar .= "<script type=\"text/javascript\" src=\"$toolbar_script_url\"></script>\n";
  $toolbar .= "<!--End of iCopyright Vertical Article Toolbar -->\n";

  // Wrap the toolbar with some styles
  $css = $admin_option['align'] == 'right' ? '.icx-toolbar{padding: 0 0 0 5px;}' : '.icx-toolbar{padding: 0 5px 0 0;}';
  if($admin_option['align'] == 'right') {
    $toolbar = '<div class="icx-toolbar-align-right">' . $toolbar . '</div>';
    $css .= '.icx-toolbar-align-right{float: right;}';
  }
  $toolbar .= '<style type="text/css">' . $css . '</style>';
  return $toolbar;
}


//Generate iCopyright interactive notice
function icopyright_interactive_notice() {

  //get publication id from options table from icopyright_admin array
  $pub_id = get_option('icopyright_admin');
  $pub_id_no = $pub_id['pub_id'];

  //check publication id is not empty and all numerics
  //if not return nothing to content filter by just simply let return;
  if (empty($pub_id_no) || !is_numeric($pub_id_no)) {
    return;
  }

  //assign ICOPYRIGHT_URL constant
  $icopyright_url = ICOPYRIGHT_URL;

  //get post id
  global $post;
  $post_id = $post->ID;
  if(!icopyright_post_passes_category_filter($post_id)) {
    return;
  }

  //construct copyright notice
  $publish_date = $post->post_date;
  $date = explode('-', $publish_date);
  $icx_copyright = "Copyright " . $date['0'] . " " . get_bloginfo();

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

  // check for icopyright custom field from post editor
  //get post id
  $icopyright_hide_toolbar = get_post_meta($post_id, 'icopyright_hide_toolbar', $single = true);
  // if blogger choose to hide particular post, we will not display it, if not display as normal
  if ($icopyright_hide_toolbar !== 'yes') {
    return $icn;
  }
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
  if(is_single()) {
    $show_toolbar = ($single_display_option == 'both') || ($single_display_option == 'tools');
    $show_icn = ($single_display_option == 'both') || ($single_display_option == 'notice');
  } else {
    $show_toolbar = ($multiple_display_option == 'both') || ($multiple_display_option == 'tools');
    $show_icn = ($multiple_display_option == 'both') || ($multiple_display_option == 'notice');
  }

  // Build the toolbar and ICN if we need to display them
  if($show_toolbar) {
    $pre = ($selected_toolbar == 'horizontal' ? icopyright_horizontal_toolbar() : icopyright_vertical_toolbar());
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
  $data = get_post_meta($content, 'icopyright_hide_toolbar', true);

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
 * Returns true if either (a) no categories are selected; or (b) categories are selected, but the post
 * is in one or more of those categories; or (c) the admin has specifically said no categories. Returns false otherwise.
 *
 * @param $post_id the post ID
 * @return true if the post passes
 */
function icopyright_post_passes_category_filter($post_id) {
  // If the filter itself is not being used, then we always pass
  $setting = get_option('icopyright_admin');

  $use_filter = $setting['use_category_filter'];
  if($use_filter != 'yes') return TRUE;

  $icopyright_categories = icopyright_selected_categories();
  if(count($icopyright_categories) == 0)
    return FALSE;

  // There are categories, so check these
  $post_categories = wp_get_post_categories($post_id);
  foreach($post_categories as $cat ) {
    if(in_array($cat, $icopyright_categories))
      return TRUE;
  }

  // Got this far? Then we fail the filter
  return FALSE;
}

?>