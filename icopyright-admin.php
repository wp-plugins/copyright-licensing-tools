<?php
//register all settings
function register_icopyright_options_parameter() {
  register_setting('icopyright_settings', 'icopyright_admin');
}
add_action('admin_init', 'register_icopyright_options_parameter');

//create admin settings page
function icopyright_admin() {
  //add values into option table
  if (isset($_POST['submitted']) == 'yes-update-me') {
    //assign error
    $error_message = '';

    //check nonce
    check_admin_referer('icopyright_settings-options');

    //assign posted value
    $icopyright_pubid = stripslashes($_POST['icopyright_pubid']);
    $icopyright_display = stripslashes($_POST['icopyright_display']);
    $icopyright_tools = stripslashes($_POST['icopyright_tools']);
    $icopyright_align = stripslashes($_POST['icopyright_align']);
    $icopyright_show = stripslashes($_POST['icopyright_show']);
    $icopyright_show_multiple = stripslashes($_POST['icopyright_show_multiple']);
    $icopyright_ez_excerpt = stripslashes($_POST['icopyright_ez_excerpt']);
    $icopyright_syndication = stripslashes($_POST['icopyright_syndication']);
    $icopyright_share = stripslashes($_POST['icopyright_share']);
    $icopyright_use_copyright_filter = stripslashes($_POST['icopyright_use_category_filter']);
    $icopyright_conductor_email = stripslashes($_POST['icopyright_conductor_email']);
    $icopyright_conductor_password = stripslashes($_POST['icopyright_conductor_password']);
    $icopyright_theme = stripslashes($_POST['icopyright_article_tools_theme']);
    $icopyright_background = stripslashes($_POST['icopyright_background']);

    //check publication id
    if (empty($icopyright_pubid)) {
      $error_message .= '<li>Empty Publication ID, Please key in Publication ID or sign up for one!</li>';
    }

    //check for numerical publication id when id is not empty
    if (!empty($icopyright_pubid) && !is_numeric($icopyright_pubid)) {
      $error_message .= '<li>Publication ID error, Please key in numerics only!</li>';
    }

    //check conductor email
    //since version 1.1.4
    if (empty($icopyright_conductor_email)) {
      $error_message .= '<li>Empty Email Address, Please key in Conductor Login Email Address!</li>';
    } else {
      //update option
      update_option('icopyright_conductor_email', $icopyright_conductor_email);
    }

    //check conductor password
    //since version 1.1.4
    if (empty($icopyright_conductor_password)) {
      $error_message .= '<li>Empty Password, Please key in Conductor Login Password!</li>';
    } else {
      //update option
      update_option('icopyright_conductor_password', $icopyright_conductor_password);
    }

    //do ez excerpt setting, after email address and password are updated for old users.
    //since version 1.1.4
    $conductor_password = get_option('icopyright_conductor_password');
    $conductor_email = get_option('icopyright_conductor_email');
    $user_agent = ICOPYRIGHT_USERAGENT;

    if ($icopyright_ez_excerpt == 'yes' && !empty($conductor_email) && !empty($conductor_password)) {
      //user enabled ez excerpt
      $ez_res = icopyright_post_ez_excerpt($icopyright_pubid, 1, $user_agent, $conductor_email, $conductor_password);

      //Constant defined in icopyright.php
      //if set to true will print_r API response, for development purpose.
      if (ICOPYRIGHT_PRINTR_RESPONSE == 'true') {
        echo "Enable ez excerpt response";
        echo "<pre>";
        $xml = @simplexml_load_string($ez_res);
        print_r($xml);
        echo "</pre>";
      }

      //checked for response from API
      $check_ez_res = icopyright_check_response($ez_res);
      if (!$check_ez_res == true) {
        $error_message .= "<li>Failed to update EZ Excerpt Setting</li>";
      }
    }

    if ($icopyright_ez_excerpt == 'no' && !empty($conductor_email) && !empty($conductor_password)) {
      //user disabled ez excerpt
      $ez_res = icopyright_post_ez_excerpt($icopyright_pubid, 0, $user_agent, $conductor_email, $conductor_password);

     //Constant defined in icopyright.php
     //if set to true will print_r API response, for development purpose.
      if (ICOPYRIGHT_PRINTR_RESPONSE == 'true') {
        echo "Disabled ez excerpt response";
        echo "<pre>";
        $xml = @simplexml_load_string($ez_res);
        print_r($xml);
        echo "</pre>";
      }

      //checked for response from API
      $check_ez_res = icopyright_check_response($ez_res);
      if (!$check_ez_res == true) {
        $error_message .= "<li>Failed to update EZ Excerpt Setting</li>";
      }
    }


    //do syndication setting, variables same as ez excerpt setting, except api call.
    //since version 1.1.4
    if ($icopyright_syndication == 'yes' && !empty($conductor_email) && !empty($conductor_password)) {
      //user enabled syndication
      $syndicate_res = icopyright_post_syndication_service($icopyright_pubid, 1, $user_agent, $conductor_email, $conductor_password);

      //Constant defined in icopyright.php
      //if set to true will print_r API response, for development purpose.
      if (ICOPYRIGHT_PRINTR_RESPONSE == 'true') {
        echo "Enabled Syndication Response";
        echo "<pre>";
        $xml = @simplexml_load_string($syndicate_res);
        print_r($xml);
        echo "</pre>";
      }

      //checked for response from API
      $check_syndicate_res = icopyright_check_response($syndicate_res);
      if (!$check_syndicate_res == true) {
        $error_message .= "<li>Failed to update Syndication Setting</li>";
      }
    }

    if ($icopyright_syndication == 'no' && !empty($conductor_email) && !empty($conductor_password)) {
      //user disabled syndication
      $syndicate_res = icopyright_post_syndication_service($icopyright_pubid, 0, $user_agent, $conductor_email, $conductor_password);

      //Constant defined in icopyright.php
      //if set to true will print_r API response, for development purpose.
      if (ICOPYRIGHT_PRINTR_RESPONSE == 'true') {
        echo "Disable Syndication Response";
        echo "<pre>";
        $xml = @simplexml_load_string($syndicate_res);
        print_r($xml);
        echo "</pre>";
      }

      //checked for response from API
      $check_syndicate_res = icopyright_check_response($syndicate_res);
      if (!$check_syndicate_res == true) {
        $error_message .= "<li>Failed to update Syndication Setting</li>";
      }
    }

    // Turn on and off sharing
    if (!empty($conductor_email) && !empty($conductor_password)) {
      $val = ($icopyright_share == 'yes' ? 1 : 0);
      $share_res = icopyright_post_share_service($icopyright_pubid, $val, $user_agent, $conductor_email, $conductor_password);
      $check_share_res = icopyright_check_response($share_res);
      if (!$check_share_res == true) {
        $error_message .= "<li>Failed to update Share Setting</li>";
      }
    }

    // Set the toolbar theme and background and so on
    if (!empty($conductor_email) && !empty($conductor_password)) {
      $t_res = icopyright_post_toolbar_theme($icopyright_pubid, $icopyright_theme, $icopyright_background, $user_agent, $conductor_email, $conductor_password);
      if (icopyright_check_response($t_res) != true) {
        $error_message .= "<li>Failed to update Toolbar Settings</li>";
      }
    }

    // Check selected categories input for sensibility
    $selectedCategories = array();
    $selectedCat = isset($_POST['selectedCat']) ? $_POST['selectedCat'] : array();
    foreach($selectedCat as $catid) {
      if(is_numeric($catid)) $selectedCategories[] = $catid;
    }

    //assign value to icopyright admin settings array
    //for saving into options table as an array value.
    $icopyright_admin = array('pub_id' => $icopyright_pubid,
                              'display' => $icopyright_display,
                              'tools' => $icopyright_tools,
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
    );
    //check if no error, then update admin setting
    if (empty($error_message)) {
      //update array value icopyright admin into WordPress Database Options table
      update_option('icopyright_admin', $icopyright_admin);
    }

    //check error message, if there is any, show it to blogger
    if (!empty($error_message)) {
      echo "<div  id=\"message\" class=\"updated fade\"><p style='font-size:14px;margin:5px;'><strong>The following error(s) needs your attention!</strong></p>";
      echo "<ol>" . $error_message . "</ol>";
      echo "</div>";
    } else {
      //if no error, print success message to blogger
      echo "<div  id=\"message\" class=\"updated fade\"><p><strong>Options Updated!</strong></p></div>";
      echo "<script type='text/javascript'>document.getElementById('icopyright-warning').style.display='none';</script>";
    }
  }
  //end if $_POST['submitted']

  //process form post values!
  if (isset($_POST['submitted2']) == 'yes-post-me') {
    //assign posted values
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $pname = $_POST['pname'];
    $url = $_POST['url'];

    //create post data string
    $postdata = "fname=$fname&lname=$lname&email=$email&password=$password&pname=$pname&url=$url";

    //post data to API using CURL and assigning response.
    $useragent = ICOPYRIGHT_USERAGENT;
    $response = icopyright_post_new_publisher($postdata, $useragent, $email, $password);
    $response = str_replace('ns0:', '', $response);
    $response = str_replace('ns0:', '', $response);

    $xml = @simplexml_load_string($response);
    
     //Constant defined in icopyright.php
     //if set to true will print_r API response, for development purpose.
    if(ICOPYRIGHT_PRINTR_RESPONSE == 'true'){
    echo "Registration Response";
    echo "<pre>";
    print_r($xml);
    echo "</pre>";
    }

    //check if response is empty or not xml, echo out service not available notice to blogger!
    if (empty($xml)) {
      //print error message to blogger
      echo "<div  id=\"message\" class=\"updated fade\"><p><strong>Sorry! Publication ID Registration Service is not available.
			This may be due to API server maintenance. Please try again later!</strong></p></div>";
    }

    //echo $xml->status['code'];
    $icopyright_form_status = $xml->status['code'];
    
    
        //check status code for 401
    if ($icopyright_form_status == '401') {
      echo "<div id=\"message\" class=\"updated fade\">";
      echo "<strong><p>The following fields needs your attention</p></strong>";
      echo '<ol>';
      //error
      foreach ($xml->status->messages->message as $error_message) {
        echo '<li>' . $error_message . '</li>';
      }
      echo '</ol>';
      echo "</div>";

      //check terms of agreement box, since the blogger had already checked and posted the form.
      global $icopyright_tou_checked;
      $icopyright_tou_checked = 'true';

      global $show_icopyright_register_form;
      $show_icopyright_register_form = 'true';
    }
    //end if ($icopyright_form_status=='401')
    
    
    
    

    //check status code for 400
    if ($icopyright_form_status == '400') {
      echo "<div id=\"message\" class=\"updated fade\">";
      echo "<strong><p>The following fields needs your attention</p></strong>";
      echo '<ol>';
      //error
      foreach ($xml->status->messages->message as $error_message) {
        echo '<li>' . $error_message . '</li>';
      }
      echo '</ol>';
      echo "</div>";

      //check terms of agreement box, since the blogger had already checked and posted the form.
      global $icopyright_tou_checked;
      $icopyright_tou_checked = 'true';

      global $show_icopyright_register_form;
      $show_icopyright_register_form = 'true';
    }
    //end if ($icopyright_form_status=='400')

    //check status code for 200
    if ($icopyright_form_status == '200') {

      //parse publication id from response
      $icopyright_pubid_res = $xml->publication_id;

      //cast xml publication id object into array for updating into options table
      $icopyright_pubid_array = (array)$icopyright_pubid_res;

      //auto update admin setting with response publication id,
      //and other default values.
      $icopyright_admin_default = array('pub_id' => $icopyright_pubid_array[0],
                                        'display' => 'auto',
                                        'tools' => 'horizontal',
                                        'align' => 'left',
                                        'theme' => 'CLASSIC',
                                        'background' => 'OPAQUE',
                                        'show' => 'both',
                                        'show_multiple' => 'both',
                                        'ez_excerpt' => 'yes',
                                        'syndication' => 'yes',
                                        'share' => 'yes',
                                        'categories' => '',
                                        'use_category_filter' => 'no',
      );
      //update array value $icopyright_pubid_new into WordPress Database Options table
      update_option('icopyright_admin', $icopyright_admin_default);

      //update conductor password and email into option for ez excerpt use.
      //since version 1.1.4
      update_option('icopyright_conductor_password', $password);
      update_option('icopyright_conductor_email', $email);

      $blog_id = null; //declare blank variables
      $plugin_feed_url = null;

      //assign blog id if form posted in value, in case of multi site form will
      //generate a hidden blog id value to post in for creating feed.
      //if there is no blog id value posted in, this will be normal single site.
      $blog_id = $_POST['blog_id'];

      if (!empty($blog_id)) {
        //this is multisite, we use main blog url and sub blog id for feed.
        $plugin_feed_url .= get_site_url(1) . "/wp-content/plugins/copyright-licensing-tools/icopyright_xml.php?blog_id=$blog_id&id=*";
      } else {
        //this is single site install, no need for blog id.
        //post in old feed url structure.
        $plugin_feed_url .= WP_PLUGIN_URL . "/copyright-licensing-tools/icopyright_xml.php?id=*";

      }

      //create post data string
      $id2 = $icopyright_pubid_array[0];
      $useragent = ICOPYRIGHT_USERAGENT;

      //post data to API using CURL and assigning response.
      $response2 = icopyright_post_update_feed_url($id2, $plugin_feed_url, $useragent, $email, $password);
      $response2 = str_replace('ns0:', '', $response2);
      $response2 = str_replace('ns0:', '', $response2);
      $xml2 = @simplexml_load_string($response2);
 
      
     //Constant defined in icopyright.php
     //if set to true will print_r API response, for development purpose.
	  if(ICOPYRIGHT_PRINTR_RESPONSE == 'true'){
	  echo "Update Feed Url response";
      echo "<pre>";
      print_r($xml2);
      echo "</pre>";
      }      
      
      
      $icopyright_feed_status = $xml2->status['code'];

      if ($icopyright_feed_status == '200') {
        //successful feed url update, we show no additional message

        $update_feed_error = "";
      } elseif ($icopyright_feed_status == '400') {
        //update unsuccessful, we show additional instruction
        $update_feed_error = "There is an error in updating your feed url. You may need to login to iCopyright Conductor to update your feed url, if there is a problem using your plugin.";
      } else {
        //if no status code or wrong status code, could be server down
        //we show server down message
        $update_feed_error = "However, our API server is experiencing some problems. You may need to login to iCopyright Conductor to update your feed url, if there is a problem using your plugin.";
      }

      $icopyright_conductor_url = ICOPYRIGHT_URL . "publisher/";

      echo "<div id=\"message\" class=\"updated fade\">";
      echo "<strong><h3>Congratulations, your website is now live with iCopyright! Please review the default settings below and make any changes you wish. You may find it helpful to view the video <a href='http://info.icopyright.com/icopyright-video' target='_blank'>\"Introduction to iCopyright\"</a>. Feel free to visit your new <a href='$icopyright_conductor_url' target='_blank'>Conductor</a> account to explore your new capabilities. A welcome email has been sent to you with some helpful hints. $update_feed_error</h3></strong>";
      echo "</div>";

      echo "<script type='text/javascript'>document.getElementById('icopyright-warning').style.display='none';</script>";

      global $show_icopyright_register_form;
      $show_icopyright_register_form = 'false';
    }
    //end if ($icopyright_form_status=='200')
  }//end if(isset($_POST['submitted2'])== 'yes-post-me')

  ?>
<div class="wrap">

<h2><?php _e("iCopyright Settings"); ?></h2>

<div id="icopyright_option" <?php global $show_icopyright_register_form; if($show_icopyright_register_form=='true'){echo'style="display:none"';} ?> >

<p>
The following settings will determine how the iCopyright Article Tools and Interactive Copyright notice appear on your content pages. If you need assistance, please email <a href="mailto:wordpress@icopyright.com">wordpress@icopyright.com</a> or get <a href="http://info.icopyright.com/wordpress" target="_blank">help</a>.
</p>

<form name="icopyrightform" id="icopyrightform" method="post" action="">

<?php settings_fields('icopyright_settings'); ?>


<?php $icopyright_option = get_option('icopyright_admin'); ?>

<!--interactive tools deployment -->
<p>
<strong><?php _e('Deployment of iCopyright Article Tools and Interactive Copyright Notice: ')?></strong>
<br />
<br />


<input name="icopyright_display" type="radio" value="auto"  onclick="hide_manual_option()" <?php $icopyright_display = $icopyright_option['display']; if(empty($icopyright_display)||$icopyright_display=="auto"){echo "checked";}?> />
<?php _e('Automatic ')?>
<span style="font-size:10px">
(<?php _e('iCopyright Article Toolbar and Interactive Copyright Notice will be automatically added into Content of Blog Post')?>)
</span>

<br />


<input name="icopyright_display" type="radio" value="manual" onclick="show_manual_option()" <?php $icopyright_display2 = $icopyright_option['display']; if($icopyright_display2=="manual"){echo "checked";}?>/>
<?php _e('Manual ')?>
<span style="font-size:10px">
(<?php _e('Deploy iCopyright Article Toolbar and Interactive Copyright Notice into Content of Blog Post, using WordPress Shortcode')?>)
</span>
</p>

<!--WordPress shortcodes -->
<div id="M3" style="float:left;margin:0 50px 0 0;display:none;<?php $display5 = $icopyright_option['display']; if($display5=="manual"){echo "display:block;";}?>">
<p>
<strong><?php _e('Available WordPress Shortcodes: ')?></strong>
</p>
  <ul>
    <li>[icopyright horizontal toolbar]</li>
    <li>[icopyright vertical toolbar]</li>
    <li>[interactive copyright notice]</li>
  </ul>
<p>
<strong><?php _e('Available WordPress Shortcode Attributes: ')?></strong>
</p>
  <table class="widefat">
    <thead>
      <tr>
        <th>Purpose</th><th>Attribute</th><th>Variations</th><th>Example Usage</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Default</td><td>--</td><td>--</td>
        <td>[icopyright horizontal toolbar]</td>
      </tr>
      <tr>
        <td>For alignment</td><td>float="right"</td><td>float="left"<br />float="right"</td>
        <td>[icopyright horizontal toolbar float="right"]</td>
      </tr>
    </tbody>
  </table>
</div>

<!--Interactive Tools Selection -->
<?php $icopyright_share = $icopyright_option['share']; ?>

<p>
<strong><?php _e('iCopyright Toolbar Appearance:')?></strong>
<br/>
<div style="float: left">
  <table width="350" style="padding-top: 25px;">
    <tr id="alignment">
      <td class="type" width="30%">Alignment</td>
      <td class="ui-one" width="35%">
        <input name="icopyright_align" type="radio" value="left" <?php $icopyright_align = $icopyright_option['align']; if(empty($icopyright_align)||$icopyright_align=="left"){echo "checked";}?> /> <?php _e('Left')?>
      </td>
      <td class="ui-two" width="35%">
        <input name="icopyright_align" type="radio" value="right" <?php $icopyright_align = $icopyright_option['align'];if($icopyright_align=="right"){echo "checked";}?> /> <?php _e('Right')?>
      </td>
    </tr>
    <tr id="orientation">
      <td class="type">Orientation</td>
      <td class="ui-one">
        <input name="icopyright_tools" type="radio" value="horizontal" <?php $icopyright_tools = $icopyright_option['tools']; if(empty($icopyright_tools)||$icopyright_tools=="horizontal"){echo "checked";}?> /> <?php _e('Horizontal')?>
      </td>
      <td class="ui-two">
        <input name="icopyright_tools" type="radio" value="vertical" <?php if($icopyright_tools=="vertical"){echo "checked";}?> /> <?php _e('Vertical')?>
      </td>
    </tr>
    <tr id="theme">
      <td class="type">Theme</td>
      <td class="ui-one">
        <select name="icopyright_article_tools_theme" class="form-select" id="icopyright_article_tools_theme" >
          <?php
            $themes = icopyright_theme_options();
            $icopyright_theme = $icopyright_option['theme']; if(empty($icopyright_theme)) $icopyright_theme = 'CLASSIC';
            foreach($themes as $option => $name) {
              print "<option value=\"$option\"";
              if($option == $icopyright_theme) print ' selected="selected"';
              print ">$name</option>";
            }
          ?>
        </select>
      </td>
      <td class="ui-two"></td>
    </tr>
    <tr id="background">
      <td class="type">Background</td>
      <td class="ui-one">
        <input name="icopyright_background" type="radio" value="OPAQUE" <?php $icopyright_background = $icopyright_option['background']; if(empty($icopyright_background)||$icopyright_background=="OPAQUE"){echo "checked";}?> /> <?php _e('Opaque')?>
      </td>
      <td class="ui-two">
        <input name="icopyright_background" type="radio" value="TRANSPARENT" <?php if($icopyright_background=="TRANSPARENT"){echo "checked";}?> /> <?php _e('Transparent')?>
      </td>
    </tr>
  </table>
</div>
<div style="float: left;">
  <p>Preview of Toolbar</p>
  <iframe id="article-tools-preview" style="border: 0;" scrolling="no" ></iframe>
</div>
<div style="float: left; padding-left: 20px;">
  <p>Interactive Copyright Notice</p>
  <iframe id="copyright-notice-preview" style="border: 0;" height="50" scrolling="no" ></iframe>
</div>
<div style="clear: both;"></div>
</p>

<br />

<!-- Post display options -->
<p>
<strong><?php _e('Tools Displayed on Pages With:')?></strong>
<br/>
<div style="float: left">
  <table>
    <thead>
      <tr>
        <th width="15%">Single&nbsp;Post</th>
        <th width="20%">Multiple&nbsp; Posts</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr class="show-both">
        <td style="text-align: center;">
          <input name="icopyright_show" type="radio" value="both" <?php $icopyright_show = $icopyright_option['show']; if(empty($icopyright_show)||$icopyright_show=="both"){echo "checked";}?> />
        </td>
        <td style="text-align: center;">
          <input name="icopyright_show_multiple" type="radio" value="both" <?php $icopyright_show_multiple = $icopyright_option['show_multiple']; if(empty($icopyright_show_multiple)||$icopyright_show_multiple=="both"){echo "checked";}?> />
        </td>
        <td>
          Show both iCopyright Article Toolbar and Interactive Copyright Notice
        </td>
      </tr>
      <tr class="show-toolbar">
        <td style="text-align: center;">
          <input name="icopyright_show" type="radio" value="tools" <?php $icopyright_show = $icopyright_option['show'];if($icopyright_show=="tools"){echo "checked";}?> />
        </td>
        <td style="text-align: center;">
          <input name="icopyright_show_multiple" type="radio" value="tools" <?php $icopyright_show_multiple = $icopyright_option['show_multiple'];if($icopyright_show_multiple=="tools"){echo "checked";}?> />
        </td>
        <td>
          Show only iCopyright Article Toolbar
        </td>
      </tr>
      <tr class="show-icn">
        <td style="text-align: center;">
          <input name="icopyright_show" type="radio" value="notice" <?php $icopyright_show = $icopyright_option['show'];if($icopyright_show=="notice"){echo "checked";}?> />
        </td>
        <td style="text-align: center;">
          <input name="icopyright_show_multiple" type="radio" value="notice" <?php $icopyright_show_multiple = $icopyright_option['show_multiple'];if($icopyright_show_multiple=="notice"){echo "checked";}?> />
        </td>
        <td>
          Show only Interactive Copyright Notice
        </td>
      </tr>
      <tr class="show-nothing">
        <td>
          &nbsp;
        </td>
        <td style="text-align: center;">
          <input name="icopyright_show_multiple" type="radio" value="nothing" <?php $icopyright_show_multiple = $icopyright_option['show_multiple'];if($icopyright_show_multiple=="nothing"){echo "checked";}?> />
        </td>
        <td>
          Show nothing
        </td>
      </tr>
    </tbody>
  </table>
</div>
<br clear="all"/><br/>

<!--Share tools -->
<?php
//used to check whether to disable radio buttons of ez excerpt and syndication
//if there is no email address or password in database, we disable these buttons
$check_email = get_option('icopyright_conductor_email');
$check_password = get_option('icopyright_conductor_password');
?>

<p>
<strong><?php _e('Share services: ')?></strong>
<br /><br />
<input name="icopyright_share" type="radio" value="yes" <?php if($icopyright_share=="yes"){echo "checked";}?> <?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('On ')?>
<input name="icopyright_share" type="radio" value="no" <?php if(empty($icopyright_share)||$icopyright_share=="no"){echo "checked";}?><?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('Off ')?>
<span style="font-size:10px">
<br/>
<br />
(Share services make it easy for readers to share links to your articles using Facebook, LinkedIn, Twitter, and Google+.)
</span>
</p>
<br/>
<!--Toggle EZ Excerpt Feature -->

<p>
<strong><?php _e('Enable EZ Excerpt feature: ')?></strong>

<br />
<br />

<input name="icopyright_ez_excerpt" type="radio" value="yes" <?php $icopyright_ez_excerpt = $icopyright_option['ez_excerpt']; if(empty($icopyright_ez_excerpt)||$icopyright_ez_excerpt=="yes"){echo "checked";}?> <?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('On ')?>


<input name="icopyright_ez_excerpt" type="radio" value="no" <?php $icopyright_ez_excerpt2 = $icopyright_option['ez_excerpt']; if($icopyright_ez_excerpt2=="no"){echo "checked";}?> <?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('Off ')?>
<span style="font-size:10px">
<br/>
<br />
(For EZ Excerpt to be enabled, the display option selected above must include iCopyright Article Tools. When EZ Excerpt is activated, any reader who tries to copy/paste a portion of your article will be presented with a box asking "Obtain a License?". If reader selects "yes" he or she will be offered the opportunity to license the excerpt for purposes of posting on the reader's own website.)
</span>
</p>

<br/>

<!--Syndication -->

<p>
<strong><?php _e('Syndication: ')?></strong>

<br />
<br />

<input name="icopyright_syndication" type="radio" value="yes" <?php $icopyright_syndication = $icopyright_option['syndication']; if(empty($icopyright_syndication)||$icopyright_syndication=="yes"){echo "checked";}?> <?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('On ')?>


<input name="icopyright_syndication" type="radio" value="no" <?php $icopyright_syndication2 = $icopyright_option['syndication']; if($icopyright_syndication2=="no"){echo "checked";}?><?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('Off ')?>
<span style="font-size:10px">
<br/>
<br />
(The Syndication Feed service enables other websites to subscribe to a feed of your content and pay you based on the number of times your articles are viewed on their site at a CPM rate you specify. When you receive your Welcome email, click to go into Conductor and set the business terms you would like. Until you do that, default pricing and business terms will apply.)
</span>
</p>

<br/>

<?php
  $systemCategories = get_categories();
  $use_filter = $icopyright_option['use_category_filter'];
?>
<strong><?php _e('Categories: ')?></strong><br/>
<input class="category-radio" name="icopyright_use_category_filter" type="radio" value="no" <?php if($use_filter!="yes"){echo "checked";}?> /> <?php _e('Apply toolbar to all posts')?>
<br />
<input class="category-radio" name="icopyright_use_category_filter" type="radio" value="yes" <?php if($use_filter=="yes"){echo "checked";}?> /> <?php _e('Apply toolbar only to selected categories')?>
<br />
<?php
    echo '<div id="icopyright-category-list" style="';
    if($use_filter != "yes") { echo 'display: none; '; }
    echo 'font-size:10px;"><p>Select categories on which to display the Article Tools.</p>';
    $selectedCategories = icopyright_selected_categories();
    echo '<ul>';
    foreach($systemCategories as $cat) {
      $checked = (in_array($cat->term_id, $selectedCategories) ? 'checked' : '');
      echo '<li><input id="'.$cat->term_id.'" type="checkbox" name="selectedCat[]" value="'.$cat->term_id.'" '.$checked.' /><label style="margin-left: 5px;" for="'.$cat->term_id.'">'.$cat->name.'</label></li>';
    }
    echo '</ul></div>';
?>

<br/>

<script type="text/javascript">
// Function to update the previews with what the toolbars will look like with these settings
function toolbarTouch() {
    var orient = (jQuery('input:radio[name=icopyright_tools]:checked').val() == 'horizontal' ? 'horz' : 'vert');
    var theme = jQuery('#icopyright_article_tools_theme').val();
    var background = jQuery('input:radio[name=icopyright_background]:checked').val();
    var publication = <?php print $icopyright_option['pub_id']; ?>;
    var url = '<?php print icopyright_get_server() ?>/publisher/TouchToolbar.act?' +
        jQuery.param({
                theme: theme,
                background: background,
                orientation: orient,
                publication: publication});
    jQuery('#article-tools-preview').attr('src', url);
    jQuery('#article-tools-preview').attr('height', (orient == 'horz' ? 53 : 130));
    jQuery('#article-tools-preview').attr('width', (orient == 'horz' ? 300 : 100));
    var noticeUrl = '<?php print icopyright_get_server() ?>/publisher/copyright-preview.jsp?' +
        jQuery.param({
                themeName: theme,
                background: background,
                publicationId: publication,
                publicationName: '<?php print get_bloginfo() ?>'});
    jQuery('#copyright-notice-preview').attr('src', noticeUrl);
}

jQuery(document).ready(function() {
	jQuery("#toggle_advance_setting").toggle(function(){
		jQuery("#advance_setting").slideDown();
		jQuery("#toggle_advance_setting").val("Hide Advanced Settings");
  },
  function() {
  jQuery("#advance_setting").slideUp();
    jQuery("#toggle_advance_setting").val("Show Advanced Settings")
  }
  );
  jQuery("input.category-radio").change(function() {
    if(jQuery("input.category-radio:checked").val() == "yes") {
      jQuery("#icopyright-category-list").slideDown();
    } else {
      jQuery("#icopyright-category-list").slideUp();
    }
  });

  toolbarTouch();
  jQuery('#icopyright_article_tools_theme').change(function () {
      toolbarTouch();
  });
  jQuery('input:radio[name=icopyright_background]').change(function () {
      toolbarTouch();
  });
  jQuery('input:radio[name=icopyright_tools]').change(function () {
      toolbarTouch();
  });
});

</script>

<input type="button" id="toggle_advance_setting" value="Show Advanced Settings" style="cursor:pointer"><?php
$icopyright_conductor_email = get_option('icopyright_conductor_email');
$icopyright_conductor_password = get_option('icopyright_conductor_password');
$icopyright_conductor_id = $icopyright_option['pub_id'];

if(!empty($icopyright_conductor_id)){
//this is existing installation, we show email and password required message.
//this will not show for new installation.
	if(empty($icopyright_conductor_password) || empty($icopyright_conductor_email)){
	echo '<span style="font-style:italic;font-weight:bold;padding:5px;background-color: #FFFFE0;border: 1px #E6DB55;">To manage your Conductor account from this plugin, enter your email address and password here.</span><br/><br/>';
	}
}
?>

<div id='advance_setting' style="display:none">

<!--Publication ID-->
<p>
  <strong><?php _e('Publication ID:')?></strong>
<input type="text" name="icopyright_pubid" style="width:200px" value="<?php $icopyright_pubid = $icopyright_option['pub_id']; echo $icopyright_pubid; ?>"/>
<?php
if(empty($icopyright_pubid)){
echo 'or <a href="#" onclick="show_icopyright_form()">click here to register</a>';
}else{
echo '<br/><span style="font-style:italic;margin:0 0 0 105px;">Advanced User Only.</span>';
}
?>
</p>

<br />

<!--Conductor email-->
<p>
<strong><?php _e('Conductor Email Address:')?></strong>
<input type="text" name="icopyright_conductor_email" style="width:200px;" value="<?php echo $icopyright_conductor_email; ?>"/>
</p>

<br />

<!--Conductor password-->
<p>
<strong><?php _e('Conductor Password:')?></strong>
<input type="password" name="icopyright_conductor_password" style="width:200px;margin-left:30px;" value="<?php echo $icopyright_conductor_password; ?>"/>
</p>

</div><!--close div id="advance_settings"-->

<br />
<br />


<p>
<input type="hidden" name="submitted" value="yes-update-me"/>
<input type="submit" name="submit" value="Save Settings" class="button-primary" />
</p>


<br />


<!--visit conductor link-->
<p>
<strong><a href="<?php echo ICOPYRIGHT_URL.'publisher/';?>" target="_blank"><?php _e('Log in to Conductor')?></a> to enable additional services, adjust further settings, and view usage reports.</strong>
</p>

<br/>

</form>

<br />

</div><!--end icopyright_option -->

<?php
if($icopyright_form_status!='200'){
create_icopyright_register_form($fname, $lname, $email,$password,$pname,$url);
}
?>

<?php
}//end of icopyright_admin()


//function to add sub menu link under WordPress Admin Settings.
function icopyright_admin_menu() {
	$icopyrightpage = add_submenu_page('options-general.php', 'iCopyright', 'iCopyright', 'activate_plugins', 'icopyright.php', 'icopyright_admin');
	//add admin css and javascripts only to icopyright settings page if there is any.
    add_action( "admin_print_scripts-$icopyrightpage", 'icopyright_admin_scripts');
	add_action( 'admin_footer-'. $icopyrightpage, 'icopyright_admin_footer_script' );

}

//hook admin_menu to display admin page
add_action('admin_menu', 'icopyright_admin_menu');

//function to generate icopyright admin scripts
function icopyright_admin_scripts() {

$css = "<!-- icopyright admin css -->\n";
$css .="<style type=\"text/css\">\n";
$css .=".widefat{background:none;}";
$css .=".widefat tr td {border:none;height:30px;}";
$css .=".widefat input {background:none;border:1px solid #666666}";
$css .=".widefat tr {background-color: #eee;}";
$css .=".widefat tr.odd {background-color: #fff;}";
$image_url = ICOPYRIGHT_PLUGIN_URL."/images/icopyright-logo.png";
$css .= "#icopyright-logo{width:30px;height:30px;background-image:url('$image_url');background-repeat:no-repeat;}";
$css .="</style>\n";
echo $css;

$icopyright_pdf_url = ICOPYRIGHT_URL."publisher/statichtml/CSA-Online-Plugin.pdf";

$js = "<!-- icopyright admin javascript -->\n";
$js .="<script type=\"text/javascript\">\n";

//version 1.1.2
//added form validation for email and password in addition to tou validation
$js .="function validate_icopyright_form(){

var error_message = '';

//validate tou

if(!document.getElementById('tou').checked){
error_message+='<li>Terms of Use: You need to agree to the Terms of Use, before submitting for registration. You may view the terms <a href=\"$icopyright_pdf_url\" target=\"_blank\">here.</a></li>';
}

if(error_message!=''){
document.getElementById('register_error_message').innerHTML = '<strong><p>The following fields needs your attention</p></strong><ol>'+error_message+'</ol>';
document.getElementById('register_error_message').style.display='block';
return false;
}else{
document.getElementById('register_error_message').style.display='none';
return true;
}
}\n";

$js .="function show_icopyright_form(){document.getElementById('icopyright_registration_form').style.display='block';
document.getElementById('icopyright_option').style.display='none';document.getElementById('fname').focus();}\n";

$js .="function hide_icopyright_form(){document.getElementById('icopyright_registration_form').style.display='none';
document.getElementById('icopyright_option').style.display='block';}\n";

$js.="function show_manual_option(){jQuery('#M3').show();}";
$js.="function hide_manual_option(){jQuery('#M3').hide();}";
$js .="</script>\n";

echo $js;
}

//function to generate icopyright admin footer scripts
//to control display of register form or settings form
function icopyright_admin_footer_script() {

//check if empty publication id, show register form,
//if not hide form, show settings
$icopyright_option = get_option('icopyright_admin');
$icopyright_pubid = $icopyright_option['pub_id'];

	if(empty($icopyright_pubid)){

	$initial_js ="<script type=\"text/javascript\">\n";
	$initial_js .="
	document.getElementById('icopyright_registration_form').style.display='block';
	document.getElementById('icopyright_option').style.display='none';
	document.getElementById('fname').focus();\n";
	$initial_js .="</script>\n";
	echo $initial_js;

	}else{

	$initial_js ="<script type=\"text/javascript\">\n";
	$initial_js .="
	document.getElementById('icopyright_registration_form').style.display='none';
	document.getElementById('icopyright_option').style.display='block';
	document.getElementById('fname').focus();\n";
	$initial_js .="</script>\n";
	echo $initial_js;

	}
}
