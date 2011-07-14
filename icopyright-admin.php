<?php
//register all settings
function register_icopyright_options_parameter(){
register_setting('icopyright_settings', 'icopyright_admin');
}
add_action('admin_init','register_icopyright_options_parameter');


//create admin settings page
function icopyright_admin(){


     //add values into option table
     if(isset($_POST['submitted'])== 'yes-update-me'){

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
			 $icopyright_conductor_email = stripslashes($_POST['icopyright_conductor_email']);
			 $icopyright_conductor_password = stripslashes($_POST['icopyright_conductor_password']);			 
			 
			 			 			 
			 //check publication id
			 if(empty($icopyright_pubid)){
			 $error_message .= '<li>Empty Publication ID, Please key in Publication ID or sign up for one!</li>';
			 }
			 
			 //check for numerical publication id when id is not empty
			 if(!empty($icopyright_pubid)&&!is_numeric($icopyright_pubid)){
			 $error_message .= '<li>Publication ID error, Please key in numerics only!</li>';
			 }
			
			//check conductor email
			//since version 1.1.4
			 if(empty($icopyright_conductor_email)){
			 $error_message .= '<li>Empty Email Address, Please key in Conductor Login Email Address!</li>';
			 }else{
			 //update option
			 update_option('icopyright_conductor_email',$icopyright_conductor_email);
			 }
			 
			 
			 //check conductor password
			 //since version 1.1.4
			 if(empty($icopyright_conductor_password)){
			 $error_message .= '<li>Empty Password, Please key in Conductor Login Password!</li>';
			 }else{
			 //update option
			 update_option('icopyright_conductor_password',$icopyright_conductor_password);
			 }						 
			 
			 						 		 			 
			 //do ez excerpt setting, after email address and password are updated for old users.
			 //since version 1.1.4
			 $conductor_password = get_option('icopyright_conductor_password');
			 $conductor_email = get_option('icopyright_conductor_email');
			 $user_agent = ICOPYRIGHT_USERAGENT;
			 
			 if($icopyright_ez_excerpt=='yes' && !empty($conductor_email) && !empty($conductor_password)){
			 //user enabled ez excerpt
			 $ez_res = icopyright_post_ez_excerpt($icopyright_pubid, 1, $user_agent, $conductor_email, $conductor_password);
			 //checked for response from API
			 $check_ez_res = icopyright_check_response($ez_res);
			      if(!$check_ez_res == true){
					$error_message .= "<li>Failed to update EZ Excerpt Setting!</li>";
			      }		 
			 }
			 
			 if($icopyright_ez_excerpt=='no' && !empty($conductor_email) && !empty($conductor_password)){
			 //user disabled ez excerpt
			 $ez_res = icopyright_post_ez_excerpt($icopyright_pubid, 0, $user_agent, $conductor_email, $conductor_password);
			 //checked for response from API
			 $check_ez_res = icopyright_check_response($ez_res);
			      if(!$check_ez_res == true){
					$error_message .= "<li>Failed to update EZ Excerpt Setting!</li>";
			      }		 
			 }			 
			 
			 
			
			//do syndication setting, variables same as ez excerpt setting, except api call.
			//since version 1.1.4
			if($icopyright_syndication=='yes' && !empty($conductor_email) && !empty($conductor_password)){
			 //user enabled syndication
			 $syndicate_res = icopyright_post_syndication_service($icopyright_pubid, 1, $user_agent, $conductor_email, $conductor_password);
			//checked for response from API
			 $check_syndicate_res = icopyright_check_response($syndicate_res);
			      if(!$check_syndicate_res == true){
					$error_message .= "<li>Failed to update Syndication Setting!</li>";
			      }	
			 }
			 
			 
			if($icopyright_syndication=='no' && !empty($conductor_email) && !empty($conductor_password)){
			 //user disabled syndication
			 $syndicate_res = icopyright_post_syndication_service($icopyright_pubid, 0, $user_agent, $conductor_email, $conductor_password);
			//checked for response from API
			 $check_syndicate_res = icopyright_check_response($syndicate_res);
			      if(!$check_syndicate_res == true){
					$error_message .= "<li>Failed to update Syndication Setting!</li>";
			      }	
			 }			 
			 
			  
		 			 
			 //assign value to icopyright admin settings array
			 //for saving into options table as an array value.
			 $icopyright_admin = array('pub_id' => $icopyright_pubid,
			                           'display' => $icopyright_display,
									   'tools' => $icopyright_tools,
									   'align' => $icopyright_align,
									   'show' => $icopyright_show,
									   'show_multiple' => $icopyright_show_multiple,
									   'ez_excerpt'=> $icopyright_ez_excerpt,
									   'syndication'=> $icopyright_syndication,									   
			                           );
		     //check if no error, then update admin setting
			 if(empty($error_message)){
			 //update array value icopyright admin into WordPress Database Options table
			 update_option('icopyright_admin',$icopyright_admin);
			 }                        
			 
			 //check error message, if there is any, show it to blogger		 
		     if(!empty($error_message)){
		     echo "<div  id=\"message\" class=\"updated fade\"><p style='font-size:14px;margin:5px;'><strong>The following error(s) needs your attention!</strong></p>";
			 echo "<ol>".$error_message."</ol>";
			 echo "</div>";
			 }else{
			 //if no error, print success message to blogger
			 echo "<div  id=\"message\" class=\"updated fade\"><p><strong>Options Updated!</strong></p></div>";
			 echo "<script type='text/javascript'>document.getElementById('icopyright-warning').style.display='none';</script>";
			 }	 
			 
	}//end if $_POST['submitted']
	
	
	 //process form post values!   
     if(isset($_POST['submitted2'])== 'yes-post-me'){
	

		//assign posted values
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$email = $_POST['email'];
		$email2 = $_POST['email2'];// not posted to API but required to re-populate form
		$password = $_POST['password'];
		$password2 = $_POST['password2'];// not posted to API but required to re-populate form
		$pname = $_POST['pname'];
		$url = $_POST['url'];
		$line1 = $_POST['line1'];
		$line2 = $_POST['line2'];
		$line3 = $_POST['line3'];
		$city = $_POST['city'];
		$state = $_POST['state'];
		$postal = $_POST['postal'];
		$country = $_POST['country'];
		$phone = $_POST['phone'];
		$description = $_POST['description'];

		
		//create post data string
		$postdata = "fname=$fname&lname=$lname&email=$email&password=$password&pname=$pname&url=$url";
		$postdata .= "&line1=$line1&line2=$line2&line3=$line3&city=$city&state=$state&postal=$postal&country=$country";
		$postdata .= "&phone=$phone&description=$description";
		
		//post data to API using CURL and assigning response.
		$useragent = ICOPYRIGHT_USERAGENT;
		$response = icopyright_post_new_publisher($postdata, $useragent, $email, $password);
		
		$response = str_replace( 'ns0:' , '' , $response);
		$response = str_replace( 'ns0:' , '' , $response);
		
	
		$xml = @simplexml_load_string($response);
		
			//check if response is empty or not xml, echo out service not available notice to blogger!
			if(empty($xml)){
			//print error message to blogger
			echo "<div  id=\"message\" class=\"updated fade\"><p><strong>Sorry! Publication ID Registration Service is not available. 
			This may be due to API server maintenance. Please try again later!</strong></p></div>";	 
			}
		
		//echo $xml->status['code'];
		$icopyright_form_status = $xml->status['code'];
		
		    //check status code for 400
			if ($icopyright_form_status=='400'){
			echo "<div id=\"message\" class=\"updated fade\">";
			echo "<strong><p>The following fields needs your attention</p></strong>";	 
			echo '<ol>';
			//error
			foreach($xml->status->messages->message as $error_message){
			echo '<li>'.$error_message.'</li>';
		    }
			echo '</ol>';
		    echo "</div>";
			
			//check terms of agreement box, since the blogger had already checked and posted the form.
			global $icopyright_tou_checked;
			$icopyright_tou_checked = 'true';
									
			global $show_icopyright_register_form;
			$show_icopyright_register_form = 'true';
					

		   }//end if ($icopyright_form_status=='400')
		   
		    //check status code for 200
			if ($icopyright_form_status=='200'){
						
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
									 'show' => 'both',
									 'show_multiple' => 'both',
									 'ez_excerpt'=> 'yes',
									 'syndication'=>'yes'		
									 );
            //update array value $icopyright_pubid_new into WordPress Database Options table
			update_option('icopyright_admin',$icopyright_admin_default);
			
			//update conductor password and email into option for ez excerpt use.
			//since version 1.1.4
			update_option('icopyright_conductor_password',$password);
			update_option('icopyright_conductor_email',$email);
			
			$blog_id = null; //declare blank variables
			$plugin_feed_url = null;
			
			//assign blog id if form posted in value, in case of multi site form will
			//generate a hidden blog id value to post in for creating feed.
			//if there is no blog id value posted in, this will be normal single site.
			$blog_id = $_POST['blog_id'];
			
			if(!empty($blog_id)){
			//this is multisite, we use main blog url and sub blog id for feed.
		
			$plugin_feed_url .= get_site_url(1)."/wp-content/plugins/copyright-licensing-tools/icopyright_xml.php?blog_id=$blog_id&id=*";
			}else{
			//this is single site install, no need for blog id.
			//post in old feed url structure.
						 			
			$plugin_feed_url .= WP_PLUGIN_URL."/copyright-licensing-tools/icopyright_xml.php?id=*";
			
			}
			
			
			//create post data string
			$id2 = $icopyright_pubid_array[0];
			$useragent = ICOPYRIGHT_USERAGENT;

		    //post data to API using CURL and assigning response.
		    $response2 = icopyright_post_update_feed_url($id2, $plugin_feed_url, $useragent, $email, $password);
		    
		    $response2 = str_replace( 'ns0:' , '' , $response2);
		    $response2 = str_replace( 'ns0:' , '' , $response2);

		     $xml2 = @simplexml_load_string($response2);
		     
		     $icopyright_feed_status = $xml2->status['code'];
		     
		     if ($icopyright_feed_status=='200'){
		     //successful feed url update, we show no additional message
		     
		     $update_feed_error = "";
		     }elseif ($icopyright_feed_status=='400'){
		     //update unsuccessful, we show additional instruction
		     
		     $update_feed_error = "There is an error in updating your feed url. You may need to login to iCopyright Conductor to update your feed url, if there is a problem using your plugin.";
		     
		     }else{
		     //if no status code or wrong status code, could be server down
		     //we show server down message
		     
		     $update_feed_error = "However, our API server is experiencing some problems. You may need to login to iCopyright Conductor to update your feed url, if there is a problem using your plugin.";		     
		     }		                 
			
			
			$icopyright_conductor_url = ICOPYRIGHT_URL."publisher/";  
            
			echo "<div id=\"message\" class=\"updated fade\">";
			echo "<strong><h3>Congratulations, your website is now live with iCopyright! Please review the default settings below and make any changes you wish. You may find it helpful to view the video <a href='http://info.icopyright.com/icopyright-video' target='_blank'>\"Introduction to iCopyright\"</a>. Feel free to visit your new <a href='$icopyright_conductor_url' target='_blank'>Conductor</a> account to explore your new capabilities. A welcome email has been sent to you with some helpful hints. $update_feed_error</h3></strong>";
		    echo "</div>";
			
			echo "<script type='text/javascript'>document.getElementById('icopyright-warning').style.display='none';</script>";
			
			global $show_icopyright_register_form;
			$show_icopyright_register_form = 'false';

		   }//end if ($icopyright_form_status=='200')

		
			 
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

<!--Interactive Tools Selection -->
<div id="A1" style="float:left;margin:0 50px 0 0;height:700px;<?php $display = $icopyright_option['display']; if($display=="manual"){echo "display:none;";}?>">
<p>
<strong><?php _e('iCopyright Article Tools: ')?></strong>
<br /><br />
<img src="<?php echo ICOPYRIGHT_PLUGIN_URL?>/images/horizontal-toolbar.jpg" alt="horizontal-toolbar" align="absbottom"/>
<br /><br />


<input name="icopyright_tools" type="radio" value="horizontal" <?php $icopyright_tools = $icopyright_option['tools']; if(empty($icopyright_tools)||$icopyright_tools=="horizontal"){echo "checked";}?> /> <?php _e('Horizontal Toolbar ')?><br /><br />

<img src="<?php echo ICOPYRIGHT_PLUGIN_URL?>/images/vertical-toolbar.jpg" alt="vertical-toolbar" align="absbottom"/>
<br /><br />


<input name="icopyright_tools" type="radio" value="vertical" <?php $icopyright_tools2 = $icopyright_option['tools']; if($icopyright_tools2=="vertical"){echo "checked";}?> /> <?php _e('Vertical Toolbar ')?>
</p>

<br />

<p>
<strong><?php _e('iCopyright Article Tools Alignment:')?></strong>
<br />
<br />
<input name="icopyright_align" type="radio" value="left" <?php $icopyright_align = $icopyright_option['align']; if(empty($icopyright_align)||$icopyright_align=="left"){echo "checked";}?> /> <?php _e('Left ')?>

<input name="icopyright_align" type="radio" value="right" <?php $icopyright_align = $icopyright_option['align'];if($icopyright_align=="right"){echo "checked";}?> /> <?php _e('Right ')?>
</p>

<br />

<!--single post display option-->
<p>
<strong><?php _e('Single Post Display Option:')?></strong>
<br />
<br />
<input name="icopyright_show" type="radio" value="both" <?php $icopyright_show = $icopyright_option['show']; if(empty($icopyright_show)||$icopyright_show=="both"){echo "checked";}?> /> <?php _e('Show both iCopyright Article Tools and Interactive Copyright Notice ')?>
<br />



<input name="icopyright_show" type="radio" value="tools" <?php $icopyright_show = $icopyright_option['show'];if($icopyright_show=="tools"){echo "checked";}?> /> <?php _e('Show only iCopyright Article Tools ')?>
<br />



<input name="icopyright_show" type="radio" value="notice" <?php $icopyright_show = $icopyright_option['show'];if($icopyright_show=="notice"){echo "checked";}?> /> <?php _e('Show only Interactive Copyright Notice ')?>
</p>


<br />


<!--Multiple Post display option added in Version 1.0.8-->
<p>
<strong><?php _e('Multiple Post Display Option:')?></strong>
<br />
<br />

<input name="icopyright_show_multiple" type="radio" value="both" <?php $icopyright_show_multiple = $icopyright_option['show_multiple']; if(empty($icopyright_show_multiple)||$icopyright_show_multiple=="both"){echo "checked";}?> /> <?php _e('Show both iCopyright Article Tools and Interactive Copyright Notice ')?>
<br />


 
<input name="icopyright_show_multiple" type="radio" value="tools" <?php $icopyright_show_multiple = $icopyright_option['show_multiple'];if($icopyright_show_multiple=="tools"){echo "checked";}?> /> <?php _e('Show only iCopyright Article Tools ')?>
<br />



<input name="icopyright_show_multiple" type="radio" value="notice" <?php $icopyright_show_multiple = $icopyright_option['show_multiple'];if($icopyright_show_multiple=="notice"){echo "checked";}?> /> <?php _e('Show only Interactive Copyright Notice ')?>
<br /> 



<input name="icopyright_show_multiple" type="radio" value="nothing" <?php $icopyright_show_multiple = $icopyright_option['show_multiple'];if($icopyright_show_multiple=="nothing"){echo "checked";}?> /> <?php _e('Show nothing ')?>
</p>

</div>

<div id="A2" style="float:left;margin:0 50px 0 0;height:360px;<?php $display2 = $icopyright_option['display']; if($display2=="manual"){echo "display:none;";}?>">
<p>
<strong><?php _e('Interactive Copyright Notice: ')?></strong>
<br /><br />
<img src="<?php echo ICOPYRIGHT_PLUGIN_URL?>/images/interactive_copyright_notice.jpg" alt="Copyright Notice" align="absbottom"/>
<br />
<br />
No option available.
</p>

</div>

<!--WordPress shortcodes -->
<div id="M1" style="float:left;margin:0 50px 0 0;display:none;height:300px;<?php $display3 = $icopyright_option['display']; if($display3=="manual"){echo "display:block;";}?>">
<p>
<strong><?php _e('iCopyright Article Tools WordPress Shortcode: ')?></strong>
<br /><br />
<img src="<?php echo ICOPYRIGHT_PLUGIN_URL?>/images/horizontal-toolbar.jpg" alt="horizontal-toolbar" align="absbottom"/>
<br />
<br />

[icopyright horizontal toolbar]
<br />
<br />


<img src="<?php echo ICOPYRIGHT_PLUGIN_URL?>/images/vertical-toolbar.jpg" alt="vertical-toolbar" align="absbottom"/>
<br />
<br />

[icopyright vertical toolbar]
</p>

</div>

<div id="M2" style="float:left;margin:0 50px 0 0;display:none;height:300px;<?php $display4 = $icopyright_option['display']; if($display4=="manual"){echo "display:block;";}?>">
<p>
<strong><?php _e('Interactive Copyright Notice WordPress Shortcode: ')?></strong>
<br /><br />
<img src="<?php echo ICOPYRIGHT_PLUGIN_URL?>/images/interactive_copyright_notice.jpg" alt="Copyright Notice" align="absbottom"/>
<br />
<br />
[interactive copyright notice]
</p>

</div>

<div id="M3" style="float:left;margin:0 50px 0 0;display:none;height:160px;<?php $display5 = $icopyright_option['display']; if($display5=="manual"){echo "display:block;";}?>">
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

<br clear="all"/>

<!--Toggle EZ Excerpt Feature -->

<p>
<strong><?php _e('Enable EZ Excerpt feature: ')?></strong>

<br />
<br />

<?php
//used to check whether to disable radio buttons of ez excerpt and syndication
//if there is no email address or password in database, we disable these buttons
$check_email = get_option('icopyright_conductor_email');
$check_password = get_option('icopyright_conductor_password');
?>


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

<script type="text/javascript">
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
echo '<br/><span style="font-style:italic;margin:0px 0px 0px 105px;">Advanced User Only.</span>';
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
create_icopyright_register_form($fname,$lname,$email,$email2,$password,$password2,$pname,$url,$line1,$line2,$line3,$city,$state,$postal,$country,$phone,$description);
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

var email1 = document.getElementById('email').value;
var email2 = document.getElementById('email2').value;
if(email1!==email2){
error_message+='<li>Email Address of Site Admin: Retype email address is different. Both email address needs to be the same.</li>';
}

var password1 = document.getElementById('password').value;
var password2 = document.getElementById('password2').value;
if(password1!==password2){
error_message+='<li>Create Password for iCopyright Console: Retype password is different. Both password needs to be the same.</li>';
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

$js.="function show_manual_option(){document.getElementById('M1').style.display='block';document.getElementById('M2').style.display='block';
document.getElementById('M3').style.display='block';document.getElementById('A1').style.display='none';document.getElementById('A2').style.display='none';}\n";

$js.="function hide_manual_option(){document.getElementById('A1').style.display='block';document.getElementById('A2').style.display='block';
document.getElementById('M1').style.display='none';document.getElementById('M2').style.display='none';document.getElementById('M3').style.display='none';}\n";
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
?>
