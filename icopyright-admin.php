<?php
//register all settings
function register_icopyright_options_parameter() {
  register_setting('icopyright_settings', 'icopyright_admin');
}
add_action('admin_init', 'register_icopyright_options_parameter');

//create admin settings page
function icopyright_admin() {

  // This page can be slow, so give a status message
  ob_implicit_flush(TRUE);
  ob_end_flush();
  ob_flush();
  print '<h2 id="wait">Please wait...</h2>';
  ob_start();

  $icopyright_admin = get_option('icopyright_admin');
  if (empty($icopyright_admin)) {
    icopyright_preregister();
  }
  if (isset($_POST['submitted']) == 'yes-update-me') {
    post_settings();
  }
  if (isset($_POST['submitted2']) == 'yes-post-me') {
    post_new_publisher();
  }
  $icopyright_admin = get_option('icopyright_admin');
  $icopyright_account = get_option('icopyright_account');
  $icopyright_conductor_email = get_option('icopyright_conductor_email');
  $icopyright_conductor_password = get_option('icopyright_conductor_password');
  $icopyright_pubid = $icopyright_admin['pub_id'];
  ?>

	<div class="wrap" id="noneedtohide" style="display:none;" >
		<h2><?php _e("iCopyright Settings"); ?></h2>
<div id="icopyright_option" <?php if(empty($icopyright_pubid)){echo'style="display:none"';} ?> >

  <?php check_connectivity() ?>

<form name="icopyrightform" id="icopyrightform" method="post" action="">

  <?php settings_fields('icopyright_settings'); ?>
  <?php if(!empty($icopyright_pubid)) {?>

<h3>Account:</h3>
<p>
  Indicate below where we should mail your revenue checks.
</p>
<table class="form-table">
  <tbody>
  <tr align="top">
    <th scope="row">First Name</th>
    <td><input type="text" name="icopyright_fname" style="width:150px;" value="<?php echo pvalue('fname') ; ?>"/></td>
  </tr>
  <tr align="top">
    <th scope="row">Last Name</th>
    <td><input type="text" name="icopyright_lname" style="width:150px;" value="<?php echo pvalue('lname'); ?>"/></td>
  </tr>
  <tr align="top">
    <th scope="row">Site Name</th>
    <td><input type="text" name="icopyright_site_name" style="width:200px;" value="<?php echo pvalue('site_name'); ?>"/></td>
  </tr>
  <tr align="top">
    <th scope="row">Site URL</th>
    <td><input type="text" name="icopyright_site_url" style="width:200px;" value="<?php echo pvalue('site_url'); ?>"/></td>
  </tr>
  <tr align="top">
    <th scope="row">Address</th>
    <td><input type="text" name="icopyright_address_line1" style="width:200px;" value="<?php echo pvalue('address_line1'); ?>"/></td>
  </tr>
  <tr align="top">
    <th scope="row"></th>
    <td><input type="text" name="icopyright_address_line2" style="width:200px;" value="<?php echo pvalue('address_line2'); ?>"/></td>
  </tr>
  <tr align="top">
    <th scope="row"></th>
    <td><input type="text" name="icopyright_address_line3" style="width:200px;" value="<?php echo pvalue('address_line3'); ?>"/></td>
  </tr>
  <tr align="top">
    <th scope="row">City</th>
    <td><input type="text" name="icopyright_address_city" style="width:200px;" value="<?php echo pvalue('address_city'); ?>"/></td>
  </tr>
  <tr align="top">
    <th scope="row">State</th>
    <td><input type="text" name="icopyright_address_state" style="width:50px;" value="<?php echo pvalue('address_state'); ?>"/></td>
  </tr>
  <tr align="top">
    <th scope="row">Country</th>
    <td><input type="text" name="icopyright_address_country" style="width:50px;" value="<?php echo pvalue('address_country'); ?>"/></td>
  </tr>
  <tr align="top">
    <th scope="row">Postal Code</th>
    <td><input type="text" name="icopyright_address_postal" style="width:100px;" value="<?php echo pvalue('address_postal'); ?>"/></td>
  </tr>
  <tr align="top">
    <th scope="row">Phone</th>
    <td><input type="text" name="icopyright_address_phone" style="width:100px;" value="<?php echo pvalue('address_phone'); ?>"/></td>
  </tr>
  </tbody>
</table>

<!--Deployment of iCopyright Toolbar Section Begin -->
<br/>
<h3><?php _e('Deployment of iCopyright Toolbar and Interactive Copyright Notice: ')?></h3>
<p>
  The following settings will determine how the iCopyright Toolbar and Interactive Copyright Notice appear on your content pages. If you need assistance, please email <a href="mailto:wordpress@icopyright.com">wordpress@icopyright.com</a> or get <a href="http://info.icopyright.com/wordpress" target="_blank">help</a>.
</p>

<table class="form-table">
  <tbody>
  <tr align="top">
    <th scope="row">Deployment Mechanism</th>
    <td>
      <fieldset>
        <input name="icopyright_display" type="radio" value="auto"  onclick="hide_manual_option()" <?php $icopyright_display = $icopyright_admin['display']; if(empty($icopyright_display)||$icopyright_display=="auto"){echo "checked";}?> />
        <?php _e('Automatic ')?><br/>
								<span class="description">
									<?php _e('iCopyright Toolbar and Interactive Copyright Notice will be automatically added into content of post')?>
								</span>

        <br />

        <input name="icopyright_display" type="radio" value="manual" onclick="show_manual_option()" <?php $icopyright_display2 = $icopyright_admin['display']; if($icopyright_display2=="manual"){echo "checked";}?>/>
        <?php _e('Manual ')?><br/>
								<span class="description">
									<?php _e('Deploy iCopyright Toolbar and Interactive Copyright Notice into content of post, using WordPress shortcode')?>
								</span>

      </fieldset>

      <fieldset>
        <div id="M3" style="float:left;margin:0 50px 0 0;display:none;<?php $display5 = $icopyright_admin['display']; if($display5=="manual"){echo "display:block;";}?>">
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
      </fieldset>
    </td>
  </tr>

  </tbody>
</table>
<!--Deployment of iCopyright Article Tools Section End -->
<br/>
<!--iCopyright Toolbar Appearance Section Begin -->
  <?php $icopyright_share = $icopyright_admin['share']; ?>

<h3><?php _e('iCopyright Toolbar Appearance:')?></h3>
<table class="form-table">
  <tbody>
  <tr valign="top">
    <th scope="row">Alignment</th>
    <td>
      <fieldset>
        <input name="icopyright_align" type="radio" value="left" <?php $icopyright_align = $icopyright_admin['align']; if(empty($icopyright_align)||$icopyright_align=="left"){echo "checked";}?> /> <?php _e('Left')?>
        <br/>
        <input name="icopyright_align" type="radio" value="right" <?php $icopyright_align = $icopyright_admin['align'];if($icopyright_align=="right"){echo "checked";}?> /> <?php _e('Right')?>
      </fieldset>
    </td>
    <td rowspan="4">
      <span class="description">Preview of Toolbar and Interactive Copyright Notice</span>
      <fieldset style="height:140px;">
        <iframe id="article-tools-preview" style="border: 0;" scrolling="no" ></iframe>
      </fieldset>
      <fieldset>
        <iframe id="copyright-notice-preview" style="border: 0;" height="50" scrolling="no" ></iframe>
      </fieldset>
    </td>
  </tr>
  <tr valign="top">
    <th scope="row">Orientation</th>
    <td>
      <fieldset>
        <input name="icopyright_tools" type="radio" value="horizontal" <?php $icopyright_tools = $icopyright_admin['tools']; if(empty($icopyright_tools)||$icopyright_tools=="horizontal"){echo "checked";}?> /> <?php _e('Horizontal')?>
        <br/>
        <input name="icopyright_tools" type="radio" value="vertical" <?php if($icopyright_tools=="vertical"){echo "checked";}?> /> <?php _e('Vertical')?>
      </fieldset>
    </td>
  </tr>
  <tr valign="top">
    <th scope="row">Theme</th>
    <td>
      <fieldset>
        <select name="icopyright_article_tools_theme" class="form-select" id="icopyright_article_tools_theme" >
          <?php
          $themes = icopyright_theme_options();
          $icopyright_theme = $icopyright_admin['theme']; if(empty($icopyright_theme)) $icopyright_theme = 'CLASSIC';
          foreach($themes as $option => $name) {
            print "<option value=\"$option\"";
            if($option == $icopyright_theme) print ' selected="selected"';
            print ">$name</option>";
          }
          ?>
        </select>
      </fieldset>
    </td>
  </tr>
  <tr valign="top">
    <th scope="row">Background</th>
    <td>
      <fieldset>
        <input name="icopyright_background" type="radio" value="OPAQUE" <?php $icopyright_background = $icopyright_admin['background']; if(empty($icopyright_background)||$icopyright_background=="OPAQUE"){echo "checked";}?> /> <?php _e('Opaque')?>
        <br/>
        <input name="icopyright_background" type="radio" value="TRANSPARENT" <?php if($icopyright_background=="TRANSPARENT"){echo "checked";}?> /> <?php _e('Transparent')?>
      </fieldset>
    </td>
  </tr>
  </tbody>
</table>
<!--iCopyright Toolbar Appearance Section End -->

<br/>
<!-- Tools Displayed on Pages Section Begin -->
<h3><?php _e('Tools Displayed on Pages With:')?></h3>
<table class="form-table">
  <tbody>
  <tr align="top">
    <th scope="row">
      Display style
    </th>
    <td>
      <fieldset>
        <table id="icopyright-show-when">
          <thead>
          <tr>
            <td width="15%" align="center">Single&nbsp;Post</td>
            <td width="20%" align="center">Multiple&nbsp;Posts</td>
            <td></td>
          </tr>
          </thead>
          <tbody>
          <tr class="show-both">
            <td style="text-align: center;">
              <input name="icopyright_show" type="radio" value="both" <?php $icopyright_show = $icopyright_admin['show']; if(empty($icopyright_show)||$icopyright_show=="both"){echo "checked";}?> />
            </td>
            <td style="text-align: center;">
              <input name="icopyright_show_multiple" type="radio" value="both" <?php $icopyright_show_multiple = $icopyright_admin['show_multiple']; if(empty($icopyright_show_multiple)||$icopyright_show_multiple=="both"){echo "checked";}?> />
            </td>
            <td>
              Show both iCopyright Toolbar and Interactive Copyright Notice
            </td>
          </tr>
          <tr class="show-toolbar">
            <td style="text-align: center;">
              <input name="icopyright_show" type="radio" value="tools" <?php $icopyright_show = $icopyright_admin['show'];if($icopyright_show=="tools"){echo "checked";}?> />
            </td>
            <td style="text-align: center;">
              <input name="icopyright_show_multiple" type="radio" value="tools" <?php $icopyright_show_multiple = $icopyright_admin['show_multiple'];if($icopyright_show_multiple=="tools"){echo "checked";}?> />
            </td>
            <td>
              Show only iCopyright Toolbar
            </td>
          </tr>
          <tr class="show-icn">
            <td style="text-align: center;">
              <input name="icopyright_show" type="radio" value="notice" <?php $icopyright_show = $icopyright_admin['show'];if($icopyright_show=="notice"){echo "checked";}?> />
            </td>
            <td style="text-align: center;">
              <input name="icopyright_show_multiple" type="radio" value="notice" <?php $icopyright_show_multiple = $icopyright_admin['show_multiple'];if($icopyright_show_multiple=="notice"){echo "checked";}?> />
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
              <input name="icopyright_show_multiple" type="radio" value="nothing" <?php $icopyright_show_multiple = $icopyright_admin['show_multiple'];if($icopyright_show_multiple=="nothing"){echo "checked";}?> />
            </td>
            <td>
              Show nothing
            </td>
          </tr>
          </tbody>
        </table>
      </fieldset>
    </td>
  </tr>
  <tr align="top">
    <th scope="row">Pages</th>
    <td>
      <fieldset>
        <input id="icopyright_display_on_pages" name="icopyright_display_on_pages" type="checkbox" <?php if($icopyright_admin['display_on_pages'] == 'yes') print 'checked="checked"'; ?> value="yes">
        <label for="icopyright_display_on_pages">Display tools on pages as well as posts</label>
      </fieldset>
    </td>
  </tr>
  <!-- Categories Begin -->
    <?php
    $systemCategories = get_categories();
    $use_filter = $icopyright_admin['use_category_filter'];
    ?>
  <tr align="top">
    <th scope="row">Categories</th>
    <td>
      <fieldset>
        <input class="category-radio" name="icopyright_use_category_filter" type="radio" value="no" <?php if($use_filter!="yes"){echo "checked";}?> /> <?php _e('Apply tools to all posts')?>
        <br />
        <input class="category-radio" name="icopyright_use_category_filter" type="radio" value="yes" <?php if($use_filter=="yes"){echo "checked";}?> /> <?php _e('Apply tools only to selected categories')?>
        <br/>

        <?php
        echo '<div id="icopyright-category-list" style="';
        if( $use_filter != "yes" ) {
          echo 'display: none; ';
        }

        echo 'font-size:10px;"><span class="description">Select categories on which to display the Article Tools.</span>';
        $selectedCategories = icopyright_selected_categories();
        echo '<ul>';

        foreach( $systemCategories as $cat ) {
          $checked = (in_array($cat->term_id, $selectedCategories) ? 'checked' : '');
          echo '<li><input id="'.$cat->term_id.'" type="checkbox" name="selectedCat[]" value="'.$cat->term_id.'" '.$checked.' /><label style="margin-left: 5px;" for="'.$cat->term_id.'">'.$cat->name.'</label></li>';
        }
        echo '</ul></div>';
        ?>
      </fieldset>
    </td>
  </tr>
  </tbody>
</table>
<!-- Tools Displayed on Pages Section End -->
<br/>

<h3><?php _e('Service Settings:')?></h3>
<table class="form-table">
  <tbody>

  <!--Share tools Begin -->
    <?php
    //used to check whether to disable radio buttons of ez excerpt and syndication
    //if there is no email address or password in database, we disable these buttons
    $check_email = get_option('icopyright_conductor_email');
    $check_password = get_option('icopyright_conductor_password');
    ?>

  <tr align="top">
    <th scope="row">Share services</th>
    <td>
      <fieldset>
        <input name="icopyright_share" type="radio" value="yes" <?php if($icopyright_share=="yes"){echo "checked";}?> <?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('On ')?>
        <br/>
        <input name="icopyright_share" type="radio" value="no" <?php if(empty($icopyright_share)||$icopyright_share=="no"){echo "checked";}?><?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('Off ')?>
      </fieldset>
            <span class="description">Share services make it easy for readers to share links to your articles using
              Facebook, LinkedIn, Twitter, and Google+.</span>
    </td>
  </tr>
  <tr align="top">
    <th scope="row">EZ Excerpt</th>
    <td>
      <fieldset>
        <input name="icopyright_ez_excerpt" type="radio" value="yes" <?php $icopyright_ez_excerpt = $icopyright_admin['ez_excerpt']; if(empty($icopyright_ez_excerpt)||$icopyright_ez_excerpt=="yes"){echo "checked";}?> <?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('On ')?>
        <br/>
        <input name="icopyright_ez_excerpt" type="radio" value="no" <?php $icopyright_ez_excerpt2 = $icopyright_admin['ez_excerpt']; if($icopyright_ez_excerpt2=="no"){echo "checked";}?> <?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('Off ')?>
      </fieldset>
            <span class="description">When EZ Excerpt is activated, any reader who tries to copy/paste
              a portion of your article will be presented with a box asking "Obtain a License?". If reader
              selects "yes" he or she will be offered the opportunity to license the excerpt for purposes of posting
              on the reader's own website. For EZ Excerpt to be enabled, the display option selected above must
              include the iCopyright Toolbar.</span>
    </td>
  </tr>
  <tr align="top">
    <th scope="row">Syndication</th>
    <td>
      <fieldset>
        <input name="icopyright_syndication" type="radio" value="yes" <?php $icopyright_syndication = $icopyright_admin['syndication']; if(empty($icopyright_syndication)||$icopyright_syndication=="yes"){echo "checked";}?> <?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('On ')?>
        <br/>
        <input name="icopyright_syndication" type="radio" value="no" <?php $icopyright_syndication2 = $icopyright_admin['syndication']; if($icopyright_syndication2=="no"){echo "checked";}?><?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('Off ')?>
      </fieldset>
            <span class="description">The Syndication Feed service enables other websites to subscribe to a feed
              of your content and pay you based on the number of times your articles are viewed on their site at
              a CPM rate you specify. When you receive your welcome email, click to go into Conductor and set the
              business terms you would like. Until you do that, default pricing and business terms will apply.</span>
    </td>
  </tr>
  </tbody>
</table>
  <?php } ?>

<script type="text/javascript">
  // Function to update the previews with what the toolbars will look like with these settings
  function toolbarTouch() {
    if('<?php print $icopyright_admin['pub_id']; ?>' == '') return;
    var orient = (jQuery('input:radio[name=icopyright_tools]:checked').val() == 'horizontal' ? 'horz' : 'vert');
    var theme = jQuery('#icopyright_article_tools_theme').val();
    var background = jQuery('input:radio[name=icopyright_background]:checked').val();
    var publication = '<?php print $icopyright_admin['pub_id']; ?>';
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
    jQuery("h2#wait").hide();
    jQuery("div#noneedtohide").show();
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

<!-- Advanced Settings Begin -->
<h3><?php _e('Advanced Settings: ')?></h3>

  <?php
  $icopyright_conductor_id = $icopyright_admin['pub_id'];
  if(!empty($icopyright_conductor_id)){
    //this is existing installation, we show email and password required message.
    //this will not show for new installation.
    if(empty($icopyright_conductor_password) || empty($icopyright_conductor_email)){
      echo '<span style="font-style:italic;font-weight:bold;padding:5px;background-color: #FFFFE0;border: 1px #E6DB55;">To manage your Conductor account from this plugin, enter your email address and password here.</span><br/><br/>';
    }
  }
  ?>

<input type="button" id="toggle_advance_setting" value="Show Advanced Settings" style="cursor:pointer">

<div id='advance_setting' style="display:none">
  <table class="form-table">
    <tbody>
    <tr valign="top">
      <th scope="row">Publication ID</th>
      <td><input type="text" name="icopyright_pubid" style="width:200px" value="<?php $icopyright_pubid = $icopyright_admin['pub_id']; echo $icopyright_pubid; ?>"/></td>
    </tr>
    </tbody>
    <tr valign="top">
      <th scope="row">Conductor Email Address</th>
      <td><input type="text" name="icopyright_conductor_email" style="width:200px;" value="<?php echo $icopyright_conductor_email; ?>"/></td>
    </tr>
    <tr valign="top">
      <th scope="row">Conductor Password</th>
      <td><input type="password" name="icopyright_conductor_password" style="width:200px;" value="<?php echo $icopyright_conductor_password; ?>"/></td>
    </tr>
    <tr valign="top">
      <th scope="row">Conductor Feed URL</th>
      <td><input type="text" name="icopyright_feed_url" style="width:300px;"
                 value="<?php echo (isset($icopyright_admin['feed_url']) ? $icopyright_admin['feed_url'] : icopyright_get_default_feed_url()); ?>"/></td>
    </tr>
  </table>
</div>

<!-- Advanced Settings End -->
<br /><br />
<p>
  <input type="hidden" name="submitted" value="yes-update-me"/>
  <input type="submit" name="submit" value="Save Settings" class="button-primary"/>
</p>
<br />

<!--visit conductor link-->
  <?php if(!empty($icopyright_pubid )) { ?>
<p>
  <strong><a href="<?php echo ICOPYRIGHT_URL.'publisher/';?>" target="_blank"><?php _e('Log in to Conductor')?></a> to enable additional services, adjust further settings, and view usage reports.</strong>
</p>
<br/>
  <?php } ?>

</form>
<br />
</div><!--end icopyright_option -->

  <?php
  if (empty($icopyright_pubid)) {
    //assign posted values
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $pname = $_POST['pname'];
    $url = $_POST['url'];
    create_icopyright_register_form($fname, $lname, $email,$password,$pname,$url);
  }

}	//end of function icopyright_admin()


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
  ?>
<!-- icopyright admin css -->
<style type="text/css">
  .widefat	{ background: none; }
  .widefat tr td	{ border: none; height: 20px; }
  .widefat input { background: none; border: 1px solid #666666 }
  .widefat tr { background-color: #eee; }
  .widefat tr.odd { background-color: #fff; }
  #icopyright-logo	{ width:30px; height:30px; background-image:url('<?php echo ICOPYRIGHT_PLUGIN_URL; ?>/images/icopyright-logo.png'); background-repeat:no-repeat; }
  #icopyright-show-when td { padding: 0; }
</style>

  <?php
  $icopyright_pdf_url = ICOPYRIGHT_URL."publisher/statichtml/CSA-Online-Plugin.pdf";
  ?>
<!-- icopyright admin javascript -->
<script type="text/javascript">

  //version 1.1.2
  //added form validation for email and password in addition to tou validation
  function validate_icopyright_form() {

    var error_message = '';

    //validate tou

    if( !document.getElementById('tou').checked ) {
      error_message += '<li>Terms of Use: You need to agree to the Terms of Use, before submitting for registration. You may view the terms <a href="<?php echo $icopyright_pdf_url; ?>" target="_blank">here.</a></li>';
    }

    if( error_message != '' ) {
      document.getElementById('register_error_message').innerHTML = '<strong><p>The following fields needs your attention</p></strong><ol>'+error_message+'</ol>';
      document.getElementById('register_error_message').style.display='block';
      return false;
    } else {
      document.getElementById('register_error_message').style.display='none';

      /* 2012.3.8 Begin */
      document.getElementById('registersubmit').disabled = true;
      /* 2012.3.8 End */

      return true;
    }
  }

  function show_icopyright_form() {
    document.getElementById('icopyright_registration_form').style.display='block';
    document.getElementById('icopyright_option').style.display='none';
    document.getElementById('fname').focus();
  }

  function hide_icopyright_form() {
    document.getElementById('icopyright_registration_form').style.display='none';
    document.getElementById('icopyright_option').style.display='block';
  }

  function show_manual_option() {
    jQuery('#M3').show();
  }

  function hide_manual_option() {
    jQuery('#M3').hide();
  }

</script>
  <?php
}

//function to generate icopyright admin footer scripts
//to control display of register form or settings form
function icopyright_admin_footer_script() {

  //check if empty publication id, show register form,
  //if not hide form, show settings
  $icopyright_option = get_option('icopyright_admin');
  $icopyright_pubid = $icopyright_option['pub_id'];

  if(empty($icopyright_pubid)){
    ?>
  <!-- Show Registration form -->
  <script type="text/javascript">
    document.getElementById('icopyright_registration_form').style.display='block';
    document.getElementById('icopyright_option').style.display='none';
    document.getElementById('fname').focus();
  </script>

    <?php
  } else {
    ?>
  <!-- Hide Registration form -->
  <script type="text/javascript">
    document.getElementById('icopyright_registration_form').style.display='none';
    document.getElementById('icopyright_option').style.display='block';
    document.getElementById('fname').focus();
  </script>

  <?php
  }
}

/**
 * Posts the changes made to the settings page
 */
function post_settings() {
  //assign error
  $error_message = '';

  //check nonce
  check_admin_referer('icopyright_settings-options');

  //assign posted value
  $icopyright_pubid = stripslashes($_POST['icopyright_pubid']);
  $icopyright_display = stripslashes($_POST['icopyright_display']);
  $icopyright_tools = stripslashes($_POST['icopyright_tools']);
  $icopyright_display_on_pages = stripslashes($_POST['icopyright_display_on_pages']);
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
  $icopyright_feed_url = stripslashes($_POST['icopyright_feed_url']);

  $icopyright_fname = stripslashes($_POST['icopyright_fname']);
  $icopyright_lname = stripslashes($_POST['icopyright_lname']);
  $icopyright_site_name = stripslashes($_POST['icopyright_site_name']);
  $icopyright_site_url = stripslashes($_POST['icopyright_site_url']);
  $icopyright_address_line1 = stripslashes($_POST['icopyright_address_line1']);
  $icopyright_address_line2 = stripslashes($_POST['icopyright_address_line2']);
  $icopyright_address_line3 = stripslashes($_POST['icopyright_address_line3']);
  $icopyright_address_city = stripslashes($_POST['icopyright_address_city']);
  $icopyright_address_state = stripslashes($_POST['icopyright_address_state']);
  $icopyright_address_country = stripslashes($_POST['icopyright_address_country']);
  $icopyright_address_postal = stripslashes($_POST['icopyright_address_postal']);
  $icopyright_address_phone = stripslashes($_POST['icopyright_address_phone']);

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
    display_publication_welcome($icopyright_pubid);
  } else {
    $ez_res = icopyright_post_ez_excerpt($icopyright_pubid, ($icopyright_ez_excerpt == 'yes'), $user_agent, $conductor_email, $conductor_password);
    $check_ez_res = icopyright_check_response($ez_res);
    if (!$check_ez_res == TRUE) {
      $error_message .= "<li>Failed to update EZ Excerpt Setting</li>";
    }

    // Syndication setting
    $syndicate_res = icopyright_post_syndication_service($icopyright_pubid, ($icopyright_syndication == 'yes'), $user_agent, $conductor_email, $conductor_password);
    $check_syndicate_res = icopyright_check_response($syndicate_res);
    if (!$check_syndicate_res == TRUE) {
      $error_message .= "<li>Failed to update Syndication Setting</li>";
    }

    // Turn on and off sharing
    $share_res = icopyright_post_share_service($icopyright_pubid, ($icopyright_share == 'yes'), $user_agent, $conductor_email, $conductor_password);
    $check_share_res = icopyright_check_response($share_res);
    if (!$check_share_res == TRUE) {
      $error_message .= "<li>Failed to update Share Setting</li>";
    }

    // Set the toolbar theme and background and so on
    $t_res = icopyright_post_toolbar_theme($icopyright_pubid, $icopyright_theme, $icopyright_background, $user_agent, $conductor_email, $conductor_password);
    if (icopyright_check_response($t_res) != TRUE) {
      $error_message .= "<li>Failed to update Toolbar Settings</li>";
    }

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

  display_status_update($error_message);
}

/**
 * Given an error message, displays it on the page. If the error message is empty, then an OK message is shown.
 * @param $error_message
 */
function display_status_update($error_message) {
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
function display_publication_welcome($pid) {
  $icopyright_conductor_url = ICOPYRIGHT_URL . "publisher/";
  print '<div id="message" class="updated fade">';
  print '<iframe src="http://info.icopyright.com/welcome-wp.php?pid=' . $pid . '" style="border: 0; height: 50px; width: 700px;" scrolling="no"></iframe>';
  print '<p>';
  print 'Please review the default settings below and make any changes you wish. You may find it helpful to view the ';
  print 'video <a href="http://info.icopyright.com/icopyright-video" target="_blank">"Introduction to iCopyright"</a>. ';
  print 'Feel free to visit your new <a href="' . $icopyright_conductor_url . '" target="_blank">Conductor</a> ';
  print 'account to explore your new capabilities. A welcome email has been sent to you with some helpful hints.';
  print '</p>';
  print '</div>';
  print '<script type="text/javascript">jQuery("#icopyright-warning").hide();</script>';
}

/**
 * Displays warning message if there is no connectivity
 */
function check_connectivity() {
  $icopyright_option = get_option('icopyright_admin');
  $icopyright_pubid = $icopyright_option['pub_id'];
  if(isset($icopyright_pubid)) {
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
function post_new_publisher() {
  //assign posted values
  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $pname = $_POST['pname'];
  $url = $_POST['url'];

  //create post data string
  $postdata = "fname=$fname&lname=$lname&email=$email&password=$password&pname=$pname&url=$url";
  $useragent = ICOPYRIGHT_USERAGENT;
  $rv = icopyright_post_new_publisher($postdata, $useragent, $email, $password);
  $xml = @simplexml_load_string($rv->response);
  if (icopyright_check_response($rv)) {
    // Success: store the publication ID that got sent as a variable and set up the publication
    $pid = (string)$xml->publication_id;
    icopyright_set_up_new_publication($pid, $email, $password);
    icopyright_set_up_new_account($fname, $lname, $pname, $url);
    display_publication_welcome($pid);
  } else {
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
    icopyright_set_up_new_publication($pid, $email, $password);
    icopyright_set_up_new_account($fname, $lname, $pname, $url);
    display_publication_welcome($pid);
  }
  // Failure? That's OK, user will be sent to the registration page shortly
}

function pvalue($parg) {
  if (isset($_POST["icopyright_$parg"]))
    print $_POST["icopyright_$parg"];
  else {
    $icopyright_account = get_option('icopyright_account');
    print $icopyright_account[$parg];
  }
}
	