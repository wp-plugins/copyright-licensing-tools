<?php
//This file contains functions of icopyright plugin

//function to dynamically create registration form!
function create_icopyright_register_form($fname,$lname,$email,$password,$pname,$url,$line1,$line2,$line3,$city,$state,$postal,$country,$phone){

//check whether form has been submitted with errors
//if there is errors change display form to block
//so as to retain value for user to re-enter form for posting

global $show_icopyright_register_form;// global value found in function icopyright_admin() in icopyright-admin.php 
if($show_icopyright_register_form=='true'){
$display_form = 'style="display:block"';
}else{
$display_form = 'style="display:none"';
}

//form fields and inputs
$form = "<div class=\"icopyright_registration\" id=\"icopyright_registration_form\" $display_form>";

$form .='<form name="icopyright_register_form" id="icopyright_register_form" method="post" action="">';

$form .='<h3><u>Publication ID Registeration Form</u><a href="#" onclick="hide_icopyright_form()" style="font-size:12px;margin:0px 0px 0px 10px;text-decoration:none;">(Back to Option Form)</a></h3>';

$form .='<strong><p>Complete the fields below to get a Publication ID number. Required fields indicated by *</p></strong>';

$form .='<table class="widefat">';

//fname
$form .="<tr><td width=\"400px\"><label>First Name of Site Admin:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"fname\" id=\"fname\" value=\"$fname\"/>*</td></tr>";

//lname
$form .="<tr class=\"odd\"><td width=\"400px\"><label>Last Name of Site Admin:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"lname\" value=\"$lname\"/>*</td></tr>";

//email
$form .="<tr><td width=\"400px\"><label>Email Address of Site Admin:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"email\" value=\"$email\"/>*</td></tr>";

//password
$form .="<tr class=\"odd\"><td width=\"400px\"><label>Password for iCopyright Console (must be at least 6 characters):</label></td><td><input style=\"width:300px\" type=\"text\" name=\"password\" value=\"$password\"/>*</td></tr>";

//pname
$form .="<tr><td width=\"400px\"><label>Site Title (the name of your blog or publication):</label></td><td><input style=\"width:300px\" type=\"text\" name=\"pname\" value=\"$pname\"/>*</td></tr>";

//url
$form .="<tr class=\"odd\"><td width=\"400px\"><label>WordPress Site Address (URL):</label></td><td><input style=\"width:300px\" type=\"text\" name=\"url\" value=\"$url\"/>*</td></tr>";

//line1
$form .="<tr height=\"40px\"><td width=\"400px\"><label>Street Address 1<br />
 (this is needed to send payments for licensing sales):
</label></td><td><input style=\"width:500px;margin:10px 0px\" type=\"text\" name=\"line1\" value=\"$line1\"/>*</td></tr>";

//line2
$form .="<tr class=\"odd\"><td width=\"400px\"><label>Street Address 2:</label></td><td><input style=\"width:500px\" type=\"text\" name=\"line2\" value=\"$line2\"/></td></tr>";

//line3
$form .="<tr><td width=\"400px\"><label>Street Address 3:</label></td><td><input style=\"width:500px\" type=\"text\" name=\"line3\" value=\"$line3\"/></td></tr>";

//city
$form .="<tr class=\"odd\"><td width=\"400px\"><label>City:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"city\" value=\"$city\"/>*</td></tr>";

//state
$form .="<tr><td width=\"400px\"><label>State/Province:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"state\" value=\"$state\"/>*</td></tr>";

//postal
$form .="<tr class=\"odd\"><td width=\"400px\"><label>Postal Code:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"postal\" value=\"$postal\"/>*</td></tr>";

//country
$form .="<tr><td width=\"400px\"><label>Country:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"country\" value=\"$country\"/>*</td></tr>";

//phone
$form .="<tr class=\"odd\"><td width=\"400px\"><label>Phone:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"phone\" value=\"$phone\"/>*</td></tr>";

$form .='</table>';

$form .='<br/><input type="hidden" name="submitted2" value="yes-post-me"/>
<input type="submit" name="submit" value="Submit" class="button-primary" />';

$form .= "</form>";

$form .= "</div>";

echo $form;

}


//function to post data to API!
function icopyright_post_data($postdata){
		   
		   
		   $api_url = ICOPYRIGHT_API_URL;//ICOPYRIGHT_API_URL constant defined in icopyright.php
		   
		   $rs_ch = curl_init("$api_url");
		   curl_setopt($rs_ch, CURLOPT_POST, 1);
		   curl_setopt($rs_ch, CURLOPT_POSTFIELDS ,$postdata);
		   curl_setopt($rs_ch, CURLOPT_FOLLOWLOCATION ,1);
		   curl_setopt($rs_ch, CURLOPT_HEADER ,0);  // DO NOT RETURN HTTP HEADERS
		   curl_setopt($rs_ch, CURLOPT_RETURNTRANSFER ,1);  // RETURN THE CONTENTS OF THE CALL
		   //curl_setopt($rs_ch, CURLOPT_TIMEOUT, 20);//set time out 
		   $res = curl_exec($rs_ch);
		   curl_close($rs_ch);
		   return $res;
		   }



//WordPress Shortcodes to generate tool bars for content
//functions to generate tool bars, reuseable for auto inclusion or manual inclusion.
//Admin option to select toolbars and change auto to manual display

//Generate Horizontal Toolbar from hosted script or directy
function icopyright_horizontal_toolbar(){

    //script hosted on license.icopyright.net

    //get publication id from options table from icopyright_admin array
	$pub_id = get_option('icopyright_admin');
	$pub_id_no = $pub_id['pub_id'];
	
	//assign ICOPYRIGHT_URL constant
	$icopyright_url = ICOPYRIGHT_URL;
	
	//get post id 
    global $post;
	$post_id = $post->ID;
	
	//construct link href
    $toolbar = "\n<!-- iCopyright Horizontal Article Toolbar -->\n";
	$toolbar .= "<script type=\"text/javascript\">\n";
	$toolbar .= "icx_publication_id = '$pub_id_no';\n";
	$toolbar .= "icx_content_id = '$post_id';\n";
	//construct toolbar link urls
    $toolbar .= "</script>\n";
	
	$css_url = ICOPYRIGHT_URL.'rights/style/horz-toolbar.css';
	$toolbar_script_url = ICOPYRIGHT_URL.'rights/js/icx-toolbar.js';//ICOPYRIGHT_URL constant defined in icopyright.php
	$functions_script_url = ICOPYRIGHT_URL.'rights/js/icx-functions.js';//ICOPYRIGHT_URL constant defined in icopyright.php

   
    $toolbar .= "<script type=\"text/javascript\" src=\"$toolbar_script_url\"></script>\n";
    $toolbar .= "<script type=\"text/javascript\" src=\"$functions_script_url\"></script>\n";
    $toolbar .="<link rel='stylesheet' href='$css_url' type='text/css' media='screen' />";
	//extra css to control float from admin
	$toolbar .=  icopyright_toolbar_float();
	$toolbar .= "<!--End of iCopyright Horizontal Article Toolbar -->\n";
	
	return $toolbar;
}

//Generate Vertical Toolbar from hosted script
function icopyright_vertical_toolbar(){
    
	//script hosted on license.icopyright.net

    //get publication id from options table from icopyright_admin array
	$pub_id = get_option('icopyright_admin');
	$pub_id_no = $pub_id['pub_id'];
	
	//assign ICOPYRIGHT_URL constant
	$icopyright_url = ICOPYRIGHT_URL;
	
	//get post id 
    global $post;
	$post_id = $post->ID;
	
  
    $toolbar = "\n<!-- iCopyright Vertical Article Toolbar -->\n";
	$toolbar .= "<script type=\"text/javascript\">\n";
	$toolbar .= "icx_publication_id = '$pub_id_no';\n";
	$toolbar .= "icx_content_id = '$post_id';\n";

	//construct toolbar link urls
    $toolbar .= "</script>\n";
	
	$css_url = ICOPYRIGHT_URL.'rights/style/vert-toolbar.css';
	$toolbar_script_url = ICOPYRIGHT_URL.'rights/js/icx-toolbar.js';//ICOPYRIGHT_URL constant defined in icopyright.php
	$functions_script_url = ICOPYRIGHT_URL.'rights/js/icx-functions.js';//ICOPYRIGHT_URL constant defined in icopyright.php


    $toolbar .= "<script type=\"text/javascript\" src=\"$toolbar_script_url\"></script>\n";
    $toolbar .= "<script type=\"text/javascript\" src=\"$functions_script_url\"></script>\n";
	$toolbar .="<link rel='stylesheet' href='$css_url' type='text/css' media='screen' />";
	//extra css to control float from admin
	$toolbar .=  icopyright_toolbar_float();
	$toolbar .= "<!--End of iCopyright Vertical Article Toolbar -->\n";
	
	return $toolbar;

}


//Generate iCopyright interactive notice
function icopyright_interactive_notice(){

	//get publication id from options table from icopyright_admin array
	$pub_id = get_option('icopyright_admin');
	$pub_id_no = $pub_id['pub_id'];
	
	//assign ICOPYRIGHT_URL constant
	$icopyright_url = ICOPYRIGHT_URL;

    //get post id 
    global $post;
	$post_id = $post->ID;
	
	//post permalink
	//$permalink = get_permalink($post_id);
	
	//construct copyright notice
    $publish_date = $post->post_date;
    $date = explode('-',$publish_date);
    $icx_copyright = "Copyright ".$date['0']." ".get_bloginfo();
	
	//construct link href
	$link_href = $icopyright_url."3.".$pub_id_no."?icx_id=".$post_id;
	
//construct icopyright interactive copyright notice
//All CSS style codes in icopyright-interactive-tools.css
//use php heredox syntax to return string $icn
	
$icn = <<<NOTICE

<!-- iCopyright Interactive Notice -->
<div class="icopyright-interactive-notice">
<a href="$link_href" target="_blank" title="Main menu of all reuse options" onclick="openLicenseWindow(this.getAttribute('href'));return false;">Click here for reuse options!</a>
<p><span class="icopyright-note">$icx_copyright</span></p>
</div>
<!-- end iCopyright Interactive Notice -->

NOTICE;

return $icn;
}



//WordPress Shortcode [icopyright horizontal toolbar]
function icopyright_horizontal_toolbar_shortcode($atts) {
	extract( shortcode_atts( array(
		     'float' => '',
		    ), $atts ) );
	
	if(!empty($float)){
	$style = "style='float:".$float."'";
	}else{
	$style="";
	}
			
    $h_toolbar = icopyright_horizontal_toolbar();
	return "<div ".$style."><!--horizontal toolbar wrapper -->".$h_toolbar."</div><!--end of wrapper -->";
}
add_shortcode('icopyright horizontal toolbar', 'icopyright_horizontal_toolbar_shortcode');

//WordPress Shortcode [icopyright vertical toolbar]
function icopyright_vertical_toolbar_shortcode($atts) {
	extract( shortcode_atts( array(
		     'float' => '',
		    ), $atts ) );
	
	if(!empty($float)){
	$style = "style='float:".$float."'";
	}else{
	$style="";
	}
	
    $v_toolbar = icopyright_vertical_toolbar();
	return "<div ".$style."><!--vertical toolbar wrapper -->".$v_toolbar."</div><!--end of wrapper -->";
}
add_shortcode('icopyright vertical toolbar', 'icopyright_vertical_toolbar_shortcode');

//WordPress Shortcode [interactive copyright notice]
function icopyright_interactive_copyright_notice_shortcode($atts) {
	extract( shortcode_atts( array(
		     'float' => '',
		    ), $atts ) );
	
	if(!empty($float)){
	$style = "style='float:".$float."'";
	}else{
	$style="";
	}
    $icn = icopyright_interactive_notice();
	return "<div ".$style."><!--icopyright interactive notice wrapper -->".$icn."</div><!--end of wrapper -->";
}
add_shortcode('interactive copyright notice', 'icopyright_interactive_copyright_notice_shortcode');



//filter content and automatically add icopyright toolbars
function auto_add_icopyright_toolbars($content){

   //get settings from icopyright_admin option array   
   $setting = get_option('icopyright_admin');
   $display_status = $setting['display'];//deployment
   $selected_toolbar = $setting['tools'];//toolbar selected
   $show_option = $setting['show'];//show only top bar or bottom notice or both
   
   //if automatic deployment of toolbars are selected in the admin
   //or empty option, which is new installation
   //we will auto add toolbars and copyright notice into post content
   if($display_status=="auto"&&!is_feed()){
   
   if($show_option=="both"){//show both top and bottom tools
	   //check toolbar selection for horizontal or vertical toolbar
	   if($selected_toolbar=="horizontal"){
	   $top_bar = icopyright_horizontal_toolbar();
	   $bottom_bar = icopyright_interactive_notice();
	   return $top_bar.$content.$bottom_bar;
	   }//end if
	   
	   if($selected_toolbar=="vertical"){
	   $top_bar = icopyright_vertical_toolbar();
	   $bottom_bar = icopyright_interactive_notice();
	   return $top_bar.$content.$bottom_bar;
	   }//end if
   }//end if show_option equal to both
   
   
   if($show_option=="tools"){//show only top article toolbars
	   //check toolbar selection for horizontal or vertical toolbar
	   if($selected_toolbar=="horizontal"){
	   $top_bar = icopyright_horizontal_toolbar();
	   return $top_bar.$content;
	   }//end if
	   
	   if($selected_toolbar=="vertical"){
	   $top_bar = icopyright_vertical_toolbar();
	   return $top_bar.$content;
	   }//end if
   }// end if show_option equals to tools
   
   
   if($show_option=="notice"){//show only bottom interactive notice
       $bottom_bar = icopyright_interactive_notice();
	   return $content.$bottom_bar;
   }// end if show_option equals to notice
   
   
   
  }else{
  //if display setting is manual, we will return content only
  return $content;
  
  }
    
}
add_filter('the_content','auto_add_icopyright_toolbars');
?>