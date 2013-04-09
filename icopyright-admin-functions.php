<?php

/**
 * Migrate old options to new options that use settings api.
 */
function icopyright_migrate_options() {
    $icopyright_account = get_option('icopyright_account');
    $icopyright_admin = get_option('icopyright_admin');
    if (!empty($icopyright_account)) {

        icopyright_migrate_option($icopyright_account, "fname");
        icopyright_migrate_option($icopyright_account, "lname");
        icopyright_migrate_option($icopyright_account, "site_name");
        icopyright_migrate_option($icopyright_account, "site_url");
        icopyright_migrate_option($icopyright_account, 'address_line1');
        icopyright_migrate_option($icopyright_account, 'address_line2');
        icopyright_migrate_option($icopyright_account, 'address_line3');
        icopyright_migrate_option($icopyright_account, 'address_city');
        icopyright_migrate_option($icopyright_account, 'address_state');
        icopyright_migrate_option($icopyright_account, 'address_country');
        icopyright_migrate_option($icopyright_account, 'address_postal');
        icopyright_migrate_option($icopyright_account, 'address_phone');

        icopyright_migrate_option($icopyright_admin, "pub_id");
        icopyright_migrate_option($icopyright_admin, "display");
        icopyright_migrate_option($icopyright_admin, "tools");
        icopyright_migrate_option($icopyright_admin, "theme");
        icopyright_migrate_option($icopyright_admin, "background");
        icopyright_migrate_option($icopyright_admin, "align");
        icopyright_migrate_option($icopyright_admin, "show");
        icopyright_migrate_option($icopyright_admin, "show_multiple");
        icopyright_migrate_option($icopyright_admin, "display_on_pages");
        icopyright_migrate_option($icopyright_admin, "use_category_filter");
        icopyright_migrate_option($icopyright_admin, "categories");
        icopyright_migrate_option($icopyright_admin, "share");
        icopyright_migrate_option($icopyright_admin, "ez_excerpt");
        icopyright_migrate_option($icopyright_admin, "syndication");
        icopyright_migrate_option($icopyright_admin, "feed_url");
        add_option("icopyright_tou", "on");

        delete_option('icopyright_account');
        delete_option('icopyright_admin');
    }
}

/**
 * Migrate the given option with the given array.
 *
 * @param $array
 * @param $name
 */
function icopyright_migrate_option($array, $name) {
    if (!empty($array) && is_array($array) && array_key_exists($name, $array))
        update_option('icopyright_'.$name, $array[$name]);
}

/**
 * Output an html input field.
 *
 * @param $width
 * @param $field
 * @param null $max
 */
function icopyright_make_account_row($width, $field, $max = NULL) {
    $current_value = get_option($field);
    ?>
    <input type="text" name="<?php echo($field);?>" style="width: <?php echo($width); ?>px;"
        <?php
        if(is_numeric($max)) {
            ?>
            maxlength="<?php echo($max); ?>" size="<?php echo($max); ?>"
        <?php
        }
        ?>
           value="<?php echo(sanitize_text_field(stripslashes($current_value))); ?>"/>
<?php
}

/**
 * Posts the changes made to the settings page
 */
function icopyright_post_settings($input) {

    //assign posted value
    $icopyright_pubid = sanitize_text_field(stripslashes($_POST['icopyright_pub_id']));
    $icopyright_ez_excerpt = sanitize_text_field(stripslashes($_POST['icopyright_ez_excerpt']));
    $icopyright_syndication = sanitize_text_field(stripslashes($_POST['icopyright_syndication']));
    $icopyright_share = sanitize_text_field(stripslashes($_POST['icopyright_share']));
    $icopyright_conductor_email = sanitize_email(stripslashes($_POST['icopyright_conductor_email']));
    $icopyright_conductor_password = sanitize_text_field(stripslashes($_POST['icopyright_conductor_password']));
    $icopyright_theme = sanitize_text_field(stripslashes($_POST['icopyright_theme']));
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
        add_settings_error( 'icopyright', '', 'Empty Publication ID, Please key in Publication ID or sign up for one!', 'icopyright-hide' );
        add_settings_error( 'icopyright', '', '', 'icopyright-hide' );
    }

    //check for numerical publication id when id is not empty
    if (!empty($icopyright_pubid) && !is_numeric($icopyright_pubid)) {
        add_settings_error( 'icopyright', '', 'Publication ID error, Please key in numerics only!', 'icopyright-hide' );
    }

    //check conductor email
    if (empty($icopyright_conductor_email)) {
        add_settings_error( 'icopyright', '', 'Empty Email Address, Please key in Conductor Login Email Address!', 'icopyright-hide' );
    }

    //check conductor password
    if (empty($icopyright_conductor_password)) {
        add_settings_error( 'icopyright', '', 'Empty Password, Please key in Conductor Login Password!', 'icopyright-hide' );
    }
    if(!empty($error_message)) return $input;

    //do ez excerpt setting, after email address and password are updated for old users.
    $conductor_password = $icopyright_conductor_password;
    $conductor_email = $icopyright_conductor_email;
    $user_agent = ICOPYRIGHT_USERAGENT;


    $results = array();

    // Submit as appropriate
    $ez_res = icopyright_post_ez_excerpt($icopyright_pubid, ($icopyright_ez_excerpt == 'yes'), $user_agent, $conductor_email, $conductor_password);
    $results['EZ Excerpt Setting'] = $ez_res;
    $syndicate_res = icopyright_post_syndication_service($icopyright_pubid, ($icopyright_syndication == 'yes'), $user_agent, $conductor_email, $conductor_password);
    $results['Syndication Setting'] = $ez_res;
    $share_res = icopyright_post_share_service($icopyright_pubid, ($icopyright_share == 'yes'), $user_agent, $conductor_email, $conductor_password);
    $results['Sharing Setting'] = $ez_res;
    $t_res = icopyright_post_toolbar_theme($icopyright_pubid, $icopyright_theme, $icopyright_background, $user_agent, $conductor_email, $conductor_password);
    $results['Toolbar Setting'] = $ez_res;
    icopyright_admin_error_messages_from_response($results);

    // Save publication info details
    $i_res = icopyright_post_publication_info($icopyright_pubid, $icopyright_fname, $icopyright_lname,
        $icopyright_site_name, $icopyright_site_url, $icopyright_feed_url,
        $icopyright_address_line1, $icopyright_address_line2, $icopyright_address_line3, $icopyright_address_city,
        $icopyright_address_state, $icopyright_address_postal, $icopyright_address_country, $icopyright_address_phone,
        $user_agent, $conductor_email, $conductor_password
    );
    if (icopyright_check_response($i_res) != TRUE) {
        // The update failed; let's pull out the errors and report them
        add_settings_error( 'icopyright', '', 'Failed to update account information', 'icopyright-hide' );
        $xml = @simplexml_load_string($i_res->response);
        //add_settings_error( 'icopyright', '', implode("|",$xml), 'icopyright-hide' );
        foreach ($xml->status->messages->message as $m) {
            add_settings_error( 'icopyright', '', (string) $m, 'icopyright-hide' );
        }
    }

    return $input;
}

/**
 * Given an associative array of settings and results, returns an error message.
 * Returns NULL if there were no errors.
 * @param $results array map of setting => icopyright results
 * @return string message
 */
function icopyright_admin_error_messages_from_response($results) {
    $unauthorized = FALSE;
    foreach($results as $setting => $res) {
        if(!icopyright_check_response($res)) {
            add_settings_error( 'icopyright', '', 'Failed to update ' . $setting . ' (' . $res->http_expl . ')', 'icopyright-hide' );
            if($res->http_code == 401) $unauthorized = TRUE;
        }
    }
    // Special case: unauthorized so just say that, no need to list everything
    if($unauthorized) {
        add_settings_error( 'icopyright', '', 'Your email address and password were not accepted, so no changes were made. ' .
            'Use <em>Advanced Settings</em> below to make changes.', 'icopyright-hide' );
    }
}

/**
 * Create the Terms of Use form and return it as a string.
 */
function icopyright_create_tou_form() {

    //form fields and inputs
    $form = '<div class="icopyright_tou" id="icopyright_tou">';
    $form .= '<form name="icopyright_tou_form" id="icopyright_tou_form" method="post" action="/wp-admin/options-general.php?page=copyright-licensing-tools" onsubmit="return validate_icopyright_form(this)">';
    $form .= "<div id='register_error_message' class='updated faded' style='display:none;'></div>";
    $form .= '<h3>iCopyright Toolbar</h3>';
    $form .= '<p>I accept the <a href="' . ICOPYRIGHT_URL . 'publisher/statichtml/CSA-Online-Plugin.pdf" target="_blank">terms of use</a>.</p>';
    $form .= '<input id="_wpnonce" name="_wpnonce" type="hidden" value="'.wp_create_nonce  ('icopyright-tou').'"/>';
    $form .= '<input id="tou" name="tou" type="hidden" value="true"/>';
    $form .= '<input type="submit" name="accept-tou" value="Agree" class="button-primary"/>';
    $form .= "</form>";
    $form .= "</div>";
    echo $form;
}

/**
 * Handle the Terms of Use POST.
 * @return SUCCESS|FAILURE|NULL
 */
function icopyright_process_tou() {

    if (isset($_POST['tou']) && isset($_POST['accept-tou'])) {
        $nonce=$_POST['_wpnonce'];
        if (! wp_verify_nonce($nonce, 'icopyright-tou') ) die('Security check');

        // User accepted the TOU so mark it as such, and then preregister
        add_option("icopyright_tou", "on");
        return icopyright_preregister();
    }
    return NULL;
}

/**
 * Try to register for a new publication record, guessing some reasonable values for registration. We then send the
 * user to the general options page, which takes it from there.
 * @return SUCCESS|FAILURE|NULL
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
            return 'SUCCESS';
        }
    }
    // Failure? That's OK, user will be sent to the registration page shortly
    return 'FAILURE';
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
 * Builds a login link which, when pushed, will automatically log the user into WP. Password is
 * sensitive so we include it here.
 * @param $page string the page to direct to
 * @param $img string image containing the button
 * @param $id string CSS id for the form
 * @return string the HTML for the form
 */
function icopyright_graphical_link_to_conductor($page, $img = NULL, $id = NULL) {
    $rv = '<form action="' . icopyright_get_server(TRUE) . '/publisher/signin.act" method="POST" name="signin" target="_blank"';
    if($id != NULL) {
        $rv .= " id=\"$id\"";
    }
    $rv .= ">\n";
    $rv .= '  <input type="hidden" name="_publication" value="' . get_option('icopyright_pub_id') . '">' . "\n";
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

/**
 * After a new publication has been created, set up all the various settings
 * @param $pid
 * @param $email
 * @param $password
 */
function icopyright_set_up_new_publication($pid, $email, $password) {
    icopyright_admin_defaults();
    update_option('icopyright_pub_id', $pid);

    $plugin_feed_url = icopyright_get_default_feed_url();
    update_option('icopyright_feed_url', $plugin_feed_url);
    icopyright_post_update_feed_url($pid, $plugin_feed_url, ICOPYRIGHT_USERAGENT, $email, $password);

    update_option('icopyright_conductor_password', $password);
    update_option('icopyright_conductor_email', $email);
}

//auto update admin setting with response publication id,
//and other default values.
function icopyright_admin_defaults() {
    update_option('icopyright_display', 'auto');
    update_option('icopyright_tools', 'horizontal');
    update_option('icopyright_align', 'right');
    update_option('icopyright_display_on_pages', 'yes');
    update_option('icopyright_theme', 'CLASSIC');
    update_option('icopyright_background', 'OPAQUE');
    update_option('icopyright_show', 'both');
    update_option('icopyright_show_multiple', 'notice');
    update_option('icopyright_ez_excerpt', 'yes');
    update_option('icopyright_syndication', 'yes');
    update_option('icopyright_share', 'yes');
    update_option('icopyright_categories', '');
    update_option('icopyright_use_category_filter', 'no');
}

/**
 * Initialize the account with what information we have
 * @param $fname
 * @param $lname
 * @param $pname
 * @param $url
 */
function icopyright_set_up_new_account($fname, $lname, $pname, $url) {
    update_option('icopyright_fname', $fname);
    update_option('icopyright_lname', $lname);
    update_option('icopyright_site_name', $pname);
    update_option('icopyright_site_url', $url);
}

//function to dynamically create registration form!
function icopyright_create_register_form() {
    $fname = sanitize_text_field(stripslashes($_POST['fname']));
    $lname = sanitize_text_field(stripslashes($_POST['lname']));
    $email = sanitize_text_field(stripslashes($_POST['email']));
    $pname = sanitize_text_field(stripslashes($_POST['pname']));
    $url = sanitize_text_field(stripslashes($_POST['url']));

    //form fields and inputs
    $form = "<div class=\"icopyright_registration\" id=\"icopyright_registration_form\">";
    $form .= '<form name="icopyright_register_form" id="icopyright_register_form" method="post" action="/wp-admin/options-general.php?page=copyright-licensing-tools">';
    $form .= "<div id='register_error_message' class='updated faded' style='display:none;'></div>";
    $form .= '<h3>Registration Form</h3><p><a href="/wp-admin/options-general.php?page=copyright-licensing-tools&advanced-settings=1#toggle_advance_setting" style="font-size:12px;margin:0 0 0 10px;text-decoration:none;">(If you already have a publication ID, click here to enter it under Show Advanced Settings.)</a></p>';
    $form .= '<p>If you need assistance, please email <a href="mailto:wordpress@icopyright.com">wordpress@icopyright.com</a> or get <a href="http://info.icopyright.com/wordpress-setup" target="_blank">help</a>.</p>';
    $form .= '<table class="widefat">';

    //fname
    $form .= "<tr><td colspan=\"2\"><h2>About You</h2></td></tr>";
    $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>First Name:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"fname\" id=\"fname\" value=\"$fname\"/></td></tr>";
    $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Last Name:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"lname\" value=\"$lname\"/></td></tr>";

    global $current_user;
    get_currentuserinfo();
    if (empty($email))
        $email = $current_user->user_email;

    $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Email Address:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"email\" id=\"email\" value=\"$email\"/></td></tr>";
    $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Password:</label></td><td><input style=\"width:300px\" type=\"password\" name=\"password\" id=\"password\" value=\"$password\"/></td></tr>";
    $form .= "<tr><td colspan=\"2\"><h2>About This Site</h2></td></tr>";

    if (empty($pname))
        $pname = get_bloginfo('name');

    $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Site Name:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"pname\" value=\"$pname\"/></td></tr>";

    //auto populate using WordPress site url
    //since version 1.1.4
    if (empty($url))
        $url = get_bloginfo('url') . "/";
    //url
    $form .= "<tr class=\"odd\"><td align=\"right\" width=\"400px\"><label>Site Address (URL):</label></td><td><input style=\"width:300px\" type=\"text\" name=\"url\" value=\"$url\"/></td></tr>";

    $form .= '</td></tr></table>';

    //If this is multisite we post in blog id for feed as hidden variable.
    if (is_multisite()) {
        global $blog_id;
        $form .= "<input type='hidden' name='blog_id' value='$blog_id'/>";
    }

    $form .= '<br/><input type="hidden" name="submitted2" value="submit-initial-registration"/>';
    $form .= '<input id="_wpnonce" name="_wpnonce" type="hidden" value="'.wp_create_nonce  ('icopyright-register').'"/>';
    $form .= '<input type="submit" name="submit" value="Submit" class="button-primary" id="registersubmit"/>';
    $form .= "</form>";
    $form .= "</div>";
    echo $form;
}

/**
 * Posts the new publisher (registration) form
 */
function icopyright_post_registration_form() {
    if (isset($_POST['submitted2']) == 'submit-initial-registration') {

        $nonce=$_REQUEST['_wpnonce'];
        if (! wp_verify_nonce($nonce, 'icopyright-register') ) die('Security check');

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
                return 'SUCCESS';
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
            foreach ($xml->status->messages->message as $m) {
                print '<li>' . (string) $m . '</li>';
            }
            print '</ol></div>';
        }
        return 'FAILURE';
    }
    return NULL;
}
?>