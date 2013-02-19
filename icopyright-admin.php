<?php
//register all settings
function register_icopyright_options_parameter() {
  register_setting('icopyright_settings', 'icopyright_admin');
}
add_action('admin_init', 'register_icopyright_options_parameter');

//create admin settings page
function icopyright_admin() {

  $icopyright_admin = get_option('icopyright_admin');
  if (!isset($_POST['tou']) && empty($icopyright_admin)) {
    // User is just starting out; show him the terms of use form
    print icopyright_create_tou_form(); return;
  }

  // This page can be slow, so give a status message
  ob_implicit_flush(TRUE);
  ob_end_flush();
  ob_flush();
  print '<h2 id="wait">Please wait...</h2>';
  ob_start();

  $icopyright_pubid = $icopyright_admin['pub_id'];
  if (isset($_POST['tou']) && isset($_POST['accept-tou']) && empty($icopyright_pubid)) {
    // User accepted the TOU so mark it as such, and then preregister
    $icopyright_admin['tou_accepted'] = TRUE;
    update_option('icopyright_admin', $icopyright_admin);
    icopyright_preregister();
  }
  if (isset($_POST['submitted']) == 'yes-update-me') {
    icopyright_post_settings();
  }
  if (isset($_POST['submitted2']) == 'submit-initial-registration') {
    icopyright_post_registration_form();
  }

  // Do a full load up of the settings for below
  $icopyright_account = get_option('icopyright_account');
  $icopyright_conductor_email = get_option('icopyright_conductor_email');
  $icopyright_conductor_password = get_option('icopyright_conductor_password');
  $icopyright_admin = get_option('icopyright_admin');
  $icopyright_pubid = $icopyright_admin['pub_id'];
  ?>

	<div class="wrap" id="noneedtohide" style="display:none;" >
		<h2><?php _e("iCopyright Settings"); ?></h2>
<div id="icopyright_option" <?php if(empty($icopyright_pubid)){echo'style="display:none"';} ?> >

  <?php icopyright_check_connectivity() ?>

<form name="icopyrightform" id="icopyrightform" method="post" action="">

  <?php settings_fields('icopyright_settings'); ?>
  <?php if(!empty($icopyright_pubid)) {?>

  <?php
  // If there's no address on file yet, put the account form at the very front
  if(!strlen($icopyright_account['address_line1'])) print '<h3>Account Settings:</h3>' . icopyright_create_account_form();
  ?>

<!--Deployment of iCopyright Toolbar Section Begin -->
<br/>
<p>
  The following settings will determine how the iCopyright Toolbar and Interactive Copyright Notice appear on your content pages. If you need assistance, please email <a href="mailto:wordpress@icopyright.com">wordpress@icopyright.com</a> or get <a href="http://info.icopyright.com/wordpress" target="_blank">help</a>.
</p>

<h3><?php _e('Deployment Mechanism: ')?></h3>

<table class="form-table">
  <tbody>
  <tr align="top">
    <th scope="row">&nbsp;</th>
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
            <li>[icopyright one button toolbar]</li>
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
    <th scope="row">Toolbar Format</th>
    <td valign="top">
      <fieldset id="toolbar-format">
        <input name="icopyright_tools" type="radio" value="horizontal" <?php $icopyright_tools = $icopyright_admin['tools']; if(empty($icopyright_tools)||$icopyright_tools=="horizontal"){echo "checked";}?> />
        <iframe id="horizontal-article-tools-preview" style="border: 0;" scrolling="no" height="53" width="300"></iframe>
        <input name="icopyright_tools" type="radio" value="vertical" <?php if($icopyright_tools=="vertical"){echo "checked";}?> />
        <iframe id="vertical-article-tools-preview" style="border: 0;" scrolling="no" height="130" width="100"></iframe>
        <input name="icopyright_tools" type="radio" value="onebutton" <?php if($icopyright_tools=="onebutton"){echo "checked";}?> />
        <iframe id="onebutton-article-tools-preview" style="border: 0;" scrolling="no" height="250" width="200"></iframe>
      </fieldset>
    </td>
  </tr>
  </tbody>
</table>
<table class="form-table" id="remaining-section">
  <tbody>
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
  <tr valign="top">
    <th scope="row">Alignment</th>
    <td>
      <fieldset>
        <input name="icopyright_align" type="radio" value="left" <?php $icopyright_align = $icopyright_admin['align']; if(empty($icopyright_align)||$icopyright_align=="left"){echo "checked";}?> /> <?php _e('Left')?>
        <br/>
        <input name="icopyright_align" type="radio" value="right" <?php $icopyright_align = $icopyright_admin['align'];if($icopyright_align=="right"){echo "checked";}?> /> <?php _e('Right')?>
      </fieldset>
    </td>
  </tr>
  <tr valign="top">
    <th scope="row">Preview of Interactive Copyright Notice (displayed below articles)</th>
    <td>
      <fieldset>
        <iframe id="copyright-notice-preview" style="border: 0;" height="50" scrolling="no" ></iframe>
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
              Facebook, LinkedIn, Twitter, and Google+. Displayable in the four-button versions of the Toolbar only.</span>
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
              on the reader's own website.</span>
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
    var theme = jQuery('#icopyright_article_tools_theme').val();
    var background = jQuery('input:radio[name=icopyright_background]:checked').val();
    var publication = '<?php print $icopyright_admin['pub_id']; ?>';
    var url_h = '<?php print icopyright_get_server() ?>/publisher/TouchToolbar.act?' +
      jQuery.param({
        theme: theme,
        background: background,
        orientation: 'horz',
        publication: publication});
    jQuery('#horizontal-article-tools-preview').attr('src', url_h);
    var url_v = '<?php print icopyright_get_server() ?>/publisher/TouchToolbar.act?' +
        jQuery.param({
          theme: theme,
          background: background,
          orientation: 'vert',
          publication: publication});
    jQuery('#vertical-article-tools-preview').attr('src', url_v);
    var url_o = '<?php print icopyright_get_server() ?>/publisher/TouchToolbar.act?' +
        jQuery.param({
          theme: theme,
          background: background,
          orientation: 'one-button',
          publication: publication});
    jQuery('#onebutton-article-tools-preview').attr('src', url_o);
    var noticeUrl = '<?php print icopyright_get_server() ?>/publisher/copyright-preview.jsp?' +
      jQuery.param({
        themeName: theme,
        background: background,
        publicationId: publication,
        publicationName: '<?php
          $account_option = get_option('icopyright_account');
          print addslashes(empty($account_option['site_name']) ? get_bloginfo() : $account_option['site_name']);
          ?>'});
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
    jQuery("#toggle_account_setting").toggle(function(){
        jQuery("#account_setting").slideDown();
        jQuery("#toggle_account_setting").val("Hide Account Settings");
      },
      function() {
        jQuery("#account_setting").slideUp();
        jQuery("#toggle_account_setting").val("Show Account Settings")
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
  });

</script>

  <?php
  // If there's no address on file yet, put the account form at the very front
  if(strlen($icopyright_account['address_line1']) > 0):
    ?>
  <!-- Account Settings Begin -->
  <h3><?php _e('Account Settings: ')?></h3>
  <input type="button" id="toggle_account_setting" value="Show Account Settings" style="cursor:pointer">
  <div id='account_setting' style="display:none">
    <?php print icopyright_create_account_form() ?>
  </div>
    <?php endif; ?>

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
      <td><input type="text" name="icopyright_feed_url" style="width:500px;"
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
</form>
<br />

<?php if(!empty($icopyright_pubid)) { ?>
<table class="form-table">
  <tbody>
  <tr valign="top">
    <th scope="row">
      <h3>Enter My<br/> Conductor Console</h3>
    </th>
    <td valign="top">
      <div id="enter-conductor-console">
        <?php print icopyright_graphical_link_to_conductor('acidIndex.act', 'search-infringers.png'); ?>
        <?php print icopyright_graphical_link_to_conductor('serviceGroups.act', 'modify-prices.png'); ?>
        <?php print icopyright_graphical_link_to_conductor('publisherReports.act', 'view-reports.png'); ?>
        <?php print icopyright_graphical_link_to_conductor('contentSyndicationFeedWizard.act', 'syndication-feeds.png'); ?>
      </div>
      <div style="clear:both;"></div>
    </td>
  </tr>
  </tbody>
</table>
<?php } ?>

</div><!--end icopyright_option -->

  <?php
  if (empty($icopyright_pubid)) {
    //assign posted values
    $fname = sanitize_text_field(stripslashes($_POST['fname']));
    $lname = sanitize_text_field(stripslashes($_POST['lname']));
    $email = sanitize_email(stripslashes($_POST['email']));
    $password = sanitize_text_field(stripslashes($_POST['password']));
    $pname = sanitize_text_field(stripslashes($_POST['pname']));
    $url = sanitize_text_field(stripslashes($_POST['url']));
    icopyright_create_register_form($fname, $lname, $email,$password,$pname,$url);
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
  #toolbar-format input { vertical-align: top; }
  #toolbar-format iframe { vertical-align: top; margin-top: -10px;}
  #enter-conductor-console form { float: left; }
  #remaining-section { margin-top: -200px; }
</style>

  <?php
  $icopyright_pdf_url = ICOPYRIGHT_URL."publisher/statichtml/CSA-Online-Plugin.pdf";
  ?>
<!-- icopyright admin javascript -->
<script type="text/javascript">

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
