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
			 
			 //check publication id
			 if(empty($icopyright_pubid)){
			 $error_message .= '<div id="message" class="updated fade"><p><strong>Empty Publication ID, Please key in Publication ID or sign up for one!</strong></p></div>';
			 }
			 
			 //check for numerical publication id when id is not empty
			 if(!empty($icopyright_pubid)&&!is_numeric($icopyright_pubid)){
			 $error_message .= '<div id="message" class="updated fade"><p><strong>Publication ID error, Please key in numerics only!</strong></p></div>';
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
			                           );
		     //check if no error, then update admin setting
			 if(empty($error_message)){
			 //update array value icopyright admin into WordPress Database Options table
			 update_option('icopyright_admin',$icopyright_admin);
			 }                        
			 
			 //check error message, if there is any, show it to blogger		 
		     if(!empty($error_message)){
			 echo $error_message;
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
		$password = $_POST['password'];
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

		
		//create post data string
		$postdata = "fname=$fname&lname=$lname&email=$email&password=$password&pname=$pname&url=$url";
		$postdata .= "&line1=$line1&line2=$line2&line3=$line3&city=$city&state=$state&postal=$postal&country=$country";
		$postdata .= "&phone=$phone";
		
		//post data to API using CURL and assigning response.
		$response = icopyright_post_data($postdata);
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
									 'ez_excerpt'=> 'yes',									   			                           );
            //update array value $icopyright_pubid_new into WordPress Database Options table
			update_option('icopyright_admin',$icopyright_admin_default);

            
			echo "<div id=\"message\" class=\"updated fade\">";
			echo "<strong><p>Registration was successfull! 
			Your Publication ID Number Is: $icopyright_pubid_res . It has been automatically updated into your settings.<br/> You will receive a Welcome Email, please follow the instructions in the email and log into iCopyright Conductor to complete the final steps of account activation. </p></strong>";
		    echo "</div>";
			
			echo "<script type='text/javascript'>document.getElementById('icopyright-warning').style.display='none';</script>";
			
			global $show_icopyright_register_form;
			$show_icopyright_register_form = 'false';

		   }//end if ($icopyright_form_status=='200')

		
			 
	}//end if(isset($_POST['submitted2'])== 'yes-post-me')

?>
<div class="wrap">

<h2><?php _e("iCopyright Settings"); ?></h2>

<p>
These settings affect how the iCopyright Article Tools and Interactive Copyright Notice work. If you need assistance, please email <a href="mailto:wordpress@icopyright.com">wordpress@icopyright.com</a>.
</p>


<div id="icopyright_option" <?php global $show_icopyright_register_form; if($show_icopyright_register_form=='true'){echo'style="display:none"';} ?> >

<form name="icopyrightform" id="icopyrightform" method="post" action="">

<?php settings_fields('icopyright_settings'); ?>


<?php $icopyright_option = get_option('icopyright_admin'); ?>

<!--interactive tools deployment -->
<p>
<strong><?php _e('Deployment of iCopyright Article Tools and Interactive Copyright Notice: ')?></strong>
<br />
<br />

<?php _e('Automatic ')?>
<input name="icopyright_display" type="radio" value="auto"  onclick="hide_manual_option()" <?php $icopyright_display = $icopyright_option['display']; if(empty($icopyright_display)||$icopyright_display=="auto"){echo "checked";}?> />
<span style="font-size:10px">
(<?php _e('iCopyright Article Toolbar and Interactive Copyright Notice will be automatically added into Content of Blog Post')?>)
</span>

<br />


<?php _e('Manual ')?>
<input name="icopyright_display" type="radio" value="manual" onclick="show_manual_option()" <?php $icopyright_display2 = $icopyright_option['display']; if($icopyright_display2=="manual"){echo "checked";}?>/>
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

<?php _e('Horizontal Toolbar ')?>
<input name="icopyright_tools" type="radio" value="horizontal" <?php $icopyright_tools = $icopyright_option['tools']; if(empty($icopyright_tools)||$icopyright_tools=="horizontal"){echo "checked";}?> /><br /><br />

<img src="<?php echo ICOPYRIGHT_PLUGIN_URL?>/images/vertical-toolbar.jpg" alt="vertical-toolbar" align="absbottom"/>
<br /><br />

<?php _e('Vertical Toolbar ')?>
<input name="icopyright_tools" type="radio" value="vertical" <?php $icopyright_tools2 = $icopyright_option['tools']; if($icopyright_tools2=="vertical"){echo "checked";}?> />
</p>

<br />

<p>
<strong><?php _e('iCopyright Article Tools Alignment:')?></strong>
<br />
<br />
left 
<input name="icopyright_align" type="radio" value="left" <?php $icopyright_align = $icopyright_option['align']; if(empty($icopyright_align)||$icopyright_align=="left"){echo "checked";}?> />
Right
<input name="icopyright_align" type="radio" value="right" <?php $icopyright_align = $icopyright_option['align'];if($icopyright_align=="right"){echo "checked";}?> />
</p>

<br />

<!--single post display option-->
<p>
<strong><?php _e('Single Post Display Option:')?></strong>
<br />
<br />
Show both iCopyright Article Tools and Interactive Copyright Notice
<input name="icopyright_show" type="radio" value="both" <?php $icopyright_show = $icopyright_option['show']; if(empty($icopyright_show)||$icopyright_show=="both"){echo "checked";}?> />
<br />


Show only iCopyright Article Tools 
<input name="icopyright_show" type="radio" value="tools" <?php $icopyright_show = $icopyright_option['show'];if($icopyright_show=="tools"){echo "checked";}?> />
<br />


Show only Interactive Copyright Notice
<input name="icopyright_show" type="radio" value="notice" <?php $icopyright_show = $icopyright_option['show'];if($icopyright_show=="notice"){echo "checked";}?> />
</p>


<br />


<!--Multiple Post display option added in Version 1.0.8-->
<p>
<strong><?php _e('Multiple Post Display Option:')?></strong>
<br />
<br />
Show both iCopyright Article Tools and Interactive Copyright Notice
<input name="icopyright_show_multiple" type="radio" value="both" <?php $icopyright_show_multiple = $icopyright_option['show_multiple']; if(empty($icopyright_show_multiple)||$icopyright_show_multiple=="both"){echo "checked";}?> />
<br />


Show only iCopyright Article Tools 
<input name="icopyright_show_multiple" type="radio" value="tools" <?php $icopyright_show_multiple = $icopyright_option['show_multiple'];if($icopyright_show_multiple=="tools"){echo "checked";}?> />
<br />


Show only Interactive Copyright Notice
<input name="icopyright_show_multiple" type="radio" value="notice" <?php $icopyright_show_multiple = $icopyright_option['show_multiple'];if($icopyright_show_multiple=="notice"){echo "checked";}?> />
<br />


Show nothing
<input name="icopyright_show_multiple" type="radio" value="nothing" <?php $icopyright_show_multiple = $icopyright_option['show_multiple'];if($icopyright_show_multiple=="nothing"){echo "checked";}?> />
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

<?php _e('Yes ')?>
<input name="icopyright_ez_excerpt" type="radio" value="yes" <?php $icopyright_ez_excerpt = $icopyright_option['ez_excerpt']; if(empty($icopyright_ez_excerpt)||$icopyright_ez_excerpt=="yes"){echo "checked";}?> />

<?php _e('No ')?>
<input name="icopyright_ez_excerpt" type="radio" value="no" <?php $icopyright_ez_excerpt2 = $icopyright_option['ez_excerpt']; if($icopyright_ez_excerpt2=="no"){echo "checked";}?>/>
<span style="font-size:10px">
<br/>(When EZ Excerpt is activated, any reader who tries to copy/paste a portion of your article will be presented with a box asking "Obtain a License?".<br/>If reader selects "yes" he or she will be offered the opportunity to license the excerpt for purposes of posting on the reader's own website.)
</span>
</p>

<br/>

<!--Publication ID-->
<p>
  <strong><?php _e('Publication ID:')?></strong> 
<input type="text" name="icopyright_pubid" style="width:200px" value="<?php $icopyright_pubid = $icopyright_option['pub_id']; echo $icopyright_pubid; ?>"/> 
<?php
if(empty($icopyright_pubid)){
echo 'or <a href="#" onclick="show_icopyright_form()">click here to register</a>';
}else{
echo '<br/><span style="font-style:italic;margin:0px 0px 0px 105px;">Advance User Only.</span>';
}
?>
</p>

<br />

<!--visit conductor link-->
<p>
<strong><a href="<?php echo ICOPYRIGHT_URL.'publisher/';?>" target="_blank"><?php _e('Visit Conductor ')?></a></strong>
</p>

<br/>

<p>
<input type="hidden" name="submitted" value="yes-update-me"/>
<input type="submit" name="submit" value="Save Settings" class="button-primary" />
</p>

</form>

<br />

</div><!--end icopyright_option -->

<?php 
if($icopyright_form_status!='200'){
create_icopyright_register_form($fname,$lname,$email,$password,$pname,$url,$line1,$line2,$line3,$city,$state,$postal,$country,$phone);
}
?>

<?php
}//end of icopyright_admin()


//function to add sub menu link under WordPress Admin Settings.
function icopyright_admin_menu() {
	$icopyrightpage = add_submenu_page('options-general.php', 'iCopyright', 'iCopyright', 'activate_plugins', 'icopyright', 'icopyright_admin');
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

$js .="function validate_icopyright_tou(){if(!document.getElementById('tou').checked){document.getElementById('tou_error').innerHTML='<strong><p>The following fields needs your attention</p></strong><ol><li>Terms of Use: You need to agree to the Terms of Use, before submitting for registration. You may view the terms <a href=\"$icopyright_pdf_url\" target=\"_blank\">here.</a></li></ol>';document.getElementById('tou_error').style.display='block';return false;}else{document.getElementById('tou_error').style.display='none';return true;}}\n";

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