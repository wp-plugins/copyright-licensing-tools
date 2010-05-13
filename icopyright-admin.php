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

			 //check nonce
			 check_admin_referer('icopyright_settings-options');
			 
			 //assign posted value
			 $icopyright_pubid = stripslashes($_POST['icopyright_pubid']);
			 $icopyright_display = stripslashes($_POST['icopyright_display']);
			 $icopyright_tools = stripslashes($_POST['icopyright_tools']);
			 $icopyright_align = stripslashes($_POST['icopyright_align']);
			 $icopyright_show = stripslashes($_POST['icopyright_show']);
			 
			 //assign value to icopyright admin settings array
			 //for saving into options table as an array value.
			 $icopyright_admin = array('pub_id' => $icopyright_pubid,
			                           'display' => $icopyright_display,
									   'tools' => $icopyright_tools,
									   'align' => $icopyright_align,
									   'show' => $icopyright_show,									   
			                           );
			 //update array value icopyright admin into WordPress Database Options table
			 update_option('icopyright_admin',$icopyright_admin);                        
			 		 
		     //print success message to blogger
			 echo "<div  id=\"message\" class=\"updated fade\"><p><strong>Options Updated!</strong></p></div>";	 
			 
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
			echo "<div  id=\"message\" class=\"updated fade\">";
			echo "<strong><p>The following fields needs your attention</p></strong>";	 
			echo '<ol>';
			//error
			foreach($xml->status->messages->message as $error_message){
			echo '<li>'.$error_message.'</li>';
		    }
			echo '</ol>';
		    echo "</div>";
			
			global $show_icopyright_register_form;
			$show_icopyright_register_form = 'true';

		   }//end if ($icopyright_form_status=='400')
		   
		    //check status code for 200
			if ($icopyright_form_status=='200'){
						
			//parse publication id from response
			$icopyright_pubid_res = $xml->publication_id;
			
			
			//cast xml publication id object into array for updating into options table
			$icopyright_pubid_array = (array)$icopyright_pubid_res;
			$icopyright_pubid_new['pub_id'] = $icopyright_pubid_array[0];
									
			//update array value $icopyright_pubid_new into WordPress Database Options table
			update_option('icopyright_admin',$icopyright_pubid_new);
            
			echo "<div  id=\"message\" class=\"updated fade\">";
			echo "<strong><p>Registration was successfull! 
			Your Publication ID Number Is: $icopyright_pubid_res . It has been automatically updated into your settings.<br/> You will receive a Welcome Email, please follow the instructions in the email and log into iCopyright Conductor to complete the final steps of account activation. </p></strong>";
		    echo "</div>";
			
			global $show_icopyright_register_form;
			$show_icopyright_register_form = 'false';

		   }//end if ($icopyright_form_status=='200')

		
			 
	}//end if(isset($_POST['submitted2'])== 'yes-post-me')

?>
<div class="wrap">

<div id="icopyright-logo" class="icon32"><br /></div><h2><?php _e("iCopyright Settings"); ?></h2>

<div id="icopyright_option" <?php global $show_icopyright_register_form; if($show_icopyright_register_form=='true'){echo'style="display:none"';} ?> >
<p>
<?php _e("To activate the iCopyright Article Tools and Interactive Copyright Notice, you must get a Publication ID number and enter it into the field below, then click \"Save Settings\".<br/>Your site will be enabled with a default set of tools that enable visitors to use and share your content."); ?>
</p>

<p>
<?php _e('If you do not have a Publication ID number, please <a href="#" onclick="show_icopyright_form()">click here to register</a> and get one for your site.<br/>After you register, you will receive an email with instructions on how to edit the rules under which people can use your content freely, or buy the rights to use it for a fee.<br/> If you need assistance, please email <a href="mailto:wordpress@icopyright.com">wordpress@icopyright.com</a>.')?>
</p>

<br />

<form name="icopyrightform" id="icopyrightform" method="post" action="">

<?php settings_fields('icopyright_settings'); ?>


<?php $icopyright_option = get_option('icopyright_admin'); ?>

<p>
  <strong><?php _e('Publication ID:')?></strong> 
<input type="text" name="icopyright_pubid" style="width:200px" value="<?php $icopyright_pubid = $icopyright_option['pub_id']; echo $icopyright_pubid; ?>"/> or <a href="#" onclick="show_icopyright_form()">click here to register</a>
</p>


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
<div id="A1" style="float:left;margin:0px 50px 0px 0px;height:530px;<?php $display = $icopyright_option['display']; if($display=="manual"){echo "display:none;";}?>">
<p>
<strong><?php _e('iCopyright Article Tools: ')?></strong>
<br /><br />
<img src="<?php echo WP_PLUGIN_URL."/icopyright/";?>images/horizontal-toolbar.jpg" alt="horizontal-toolbar" align="absbottom"/>
<br /><br />

<?php _e('Horizontal Toolbar ')?>
<input name="icopyright_tools" type="radio" value="horizontal" <?php $icopyright_tools = $icopyright_option['tools']; if(empty($icopyright_tools)||$icopyright_tools=="horizontal"){echo "checked";}?> /><br /><br />

<img src="<?php echo WP_PLUGIN_URL."/icopyright/";?>images/vertical-toolbar.jpg" alt="vertical-toolbar" align="absbottom"/>
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

<p>
<strong><?php _e('Display Option:')?></strong>
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



</div>

<div id="A2" style="float:left;margin:0px 50px 0px 0px;height:360px;<?php $display2 = $icopyright_option['display']; if($display2=="manual"){echo "display:none;";}?>">
<p>
<strong><?php _e('Interactive Copyright Notice: ')?></strong>
<br /><br />
<img src="<?php echo WP_PLUGIN_URL."/icopyright/";?>images/interactive_copyright_notice.jpg" alt="Copyright Notice" align="absbottom"/>
<br />
<br />
No option available.
</p>

</div>


<!--WordPress shortcodes -->
<div id="M1" style="float:left;margin:0px 50px 0px 0px;display:none;height:300px;<?php $display3 = $icopyright_option['display']; if($display3=="manual"){echo "display:block;";}?>">
<p>
<strong><?php _e('iCopyright Article Tools WordPress Shortcode: ')?></strong>
<br /><br />
<img src="<?php echo WP_PLUGIN_URL."/icopyright/";?>images/horizontal-toolbar.jpg" alt="horizontal-toolbar" align="absbottom"/>
<br />
<br />

[icopyright horizontal toolbar]
<br />
<br />


<img src="<?php echo WP_PLUGIN_URL."/icopyright/";?>images/vertical-toolbar.jpg" alt="vertical-toolbar" align="absbottom"/>
<br />
<br />

[icopyright vertical toolbar]
</p>

</div>

<div id="M2" style="float:left;margin:0px 50px 0px 0px;display:none;height:300px;<?php $display4 = $icopyright_option['display']; if($display4=="manual"){echo "display:block;";}?>">
<p>
<strong><?php _e('Interactive Copyright Notice WordPress Shortcode: ')?></strong>
<br /><br />
<img src="<?php echo WP_PLUGIN_URL."/icopyright/";?>images/interactive_copyright_notice.jpg" alt="Copyright Notice" align="absbottom"/>
<br />
<br />
[interactive copyright notice]
</p>

</div>

<div id="M3" style="float:left;margin:0px 50px 0px 0px;display:none;height:160px;<?php $display5 = $icopyright_option['display']; if($display5=="manual"){echo "display:block;";}?>">
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

<p>
<input type="hidden" name="submitted" value="yes-update-me"/>
<input type="submit" name="submit" value="Save Settings" class="button-primary" />
</p>

</form>

<br />

<p>
<strong><?php _e('iCopyright Article Feed: ')?></strong>
</p>
<p>Please copy the following url and paste into iCopyright Conductor under Tag & Feed Settings. This is a plugin generated xml information of your articles for iCopyright Conductor. This information does not contain any of your private or security information that will compromise your blog.
</p>
<?php
$icopyright_feed_url = WP_PLUGIN_URL."/icopyright/icopyright_xml.php?id=*";
?>
<input name="" type="text" value="<?php echo $icopyright_feed_url; ?>" style="width:80%"/>



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
	$icopyrightpage = add_submenu_page('options-general.php', 'iCopyright', 'iCopyright', 8, 'icopyright', 'icopyright_admin');
	//add admin css and javascripts only to icopyright settings page if there is any.
    add_action( "admin_print_scripts-$icopyrightpage", 'icopyright_admin_scripts');
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
$image_url = WP_PLUGIN_URL."/icopyright/images/icopyright-logo.png";
$css .= "#icopyright-logo{width:30px;height:30px;background-image:url('$image_url');background-repeat:no-repeat;}";
$css .="</style>\n";
echo $css;

$js = "<!-- icopyright admin javascript -->\n";
$js .="<script type=\"text/javascript\">\n";

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
?>