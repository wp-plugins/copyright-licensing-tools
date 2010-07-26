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

$form .='<form name="icopyright_register_form" id="icopyright_register_form" method="post" action="" onsubmit="return validate_icopyright_tou(this)">';

$form .="<div id='tou_error' class='updated faded' style='display:none;'></div>";

$form .='<h3><u>Publication ID Registration Form</u><a href="#" onclick="hide_icopyright_form()" style="font-size:12px;margin:0px 0px 0px 10px;text-decoration:none;">(Click here to enter and save your Publication Id, if you already had one.)</a></h3>';

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

$two_digit_country_description = array(
'AF--Afghanistan',
'AL--Albania',
'DZ--Algeria',
'AS--American Samoa',
'AD--Andorra',
'AO--Angola',
'AI--Anguilla',
'AQ--Antarctica',
'AG--Antigua And Barbuda',
'AR--Argentina',
'AM--Armenia',
'AW--Aruba',
'AU--Australia',
'AT--Austria',
'AZ--Azerbaijan',
'BS--Bahamas',
'BH--Bahrain',
'BD--Bangladesh',
'BB--Barbados',
'BY--Belarus',
'BE--Belgium',
'BZ--Belize',
'BJ--Benin',
'BM--Bermuda',
'BT--Bhutan',
'BO--Bolivia',
'BA--Bosnia And Herzegovina',
'BW--Botswana',
'BV--Bouvet Island',
'BR--Brazil',
'IO--British Indian Ocean Territory',
'BN--Brunei',
'BG--Bulgaria',
'BF--Burkina Faso',
'BI--Burundi',
'KH--Cambodia',
'CM--Cameroon',
'CA--Canada',
'CV--Cape Verde',
'KY--Cayman Islands',
'CF--Central African Republic',
'TD--Chad',
'CL--Chile',
'CN--China',
'CX--Christmas Island',
'CC--Cocos (Keeling) Islands',
'CO--Columbia',
'KM--Comoros',
'CG--Congo',
'CK--Cook Islands',
'CR--Costa Rica',
'CI--Cote D\'Ivorie (Ivory Coast)',
'HR--Croatia (Hrvatska)',
'CU--Cuba',
'CY--Cyprus',
'CZ--Czech Republic',
'CD--Democratic Republic Of Congo (Zaire)',
'DK--Denmark',
'DJ--Djibouti',
'DM--Dominica',
'DO--Dominican Republic',
'TP--East Timor',
'EC--Ecuador',
'EG--Egypt',
'SV--El Salvador',
'GQ--Equatorial Guinea',
'ER--Eritrea',
'EE--Estonia',
'ET--Ethiopia',
'FK--Falkland Islands (Malvinas)',
'FO--Faroe Islands',
'FJ--Fiji',
'FI--Finland',
'FR--France',
'FX--France, Metropolitan',
'GF--French Guinea',
'PF--French Polynesia',
'TF--French Southern Territories',
'GA--Gabon',
'GM--Gambia',
'GE--Georgia',
'DE--Germany',
'GH--Ghana',
'GI--Gibraltar',
'GR--Greece',
'GL--Greenland',
'GD--Grenada',
'GP--Guadeloupe',
'GU--Guam',
'GT--Guatemala',
'GN--Guinea',
'GW--Guinea-Bissau',
'GY--Guyana',
'HT--Haiti',
'HM--Heard And McDonald Islands',
'HN--Honduras',
'HK--Hong Kong',
'HU--Hungary',
'IS--Iceland',
'IN--India',
'ID--Indonesia',
'IR--Iran',
'IQ--Iraq',
'IE--Ireland',
'IL--Israel',
'IT--Italy',
'JM--Jamaica',
'JP--Japan',
'JO--Jordan',
'KZ--Kazakhstan',
'KE--Kenya',
'KI--Kiribati',
'KW--Kuwait',
'KG--Kyrgyzstan',
'LA--Laos',
'LV--Latvia',
'LB--Lebanon',
'LS--Lesotho',
'LR--Liberia',
'LY--Libya',
'LI--Liechtenstein',
'LT--Lithuania',
'LU--Luxembourg',
'MO--Macau',
'MK--Macedonia',
'MG--Madagascar',
'MW--Malawi',
'MY--Malaysia',
'MV--Maldives',
'ML--Mali',
'MT--Malta',
'MH--Marshall Islands',
'MQ--Martinique',
'MR--Mauritania',
'MU--Mauritius',
'YT--Mayotte',
'MX--Mexico',
'FM--Micronesia',
'MD--Moldova',
'MC--Monaco',
'MN--Mongolia',
'MS--Montserrat',
'MA--Morocco',
'MZ--Mozambique',
'MM--Myanmar (Burma)',
'NA--Namibia',
'NR--Nauru',
'NP--Nepal',
'NL--Netherlands',
'AN--Netherlands Antilles',
'NC--New Caledonia',
'NZ--New Zealand',
'NI--Nicaragua',
'NE--Niger',
'NG--Nigeria',
'NU--Niue',
'NF--Norfolk Island',
'KP--North Korea',
'MP--Northern Mariana Islands',
'NO--Norway',
'OM--Oman',
'PK--Pakistan',
'PW--Palau',
'PA--Panama',
'PG--Papua New Guinea',
'PY--Paraguay',
'PE--Peru',
'PH--Philippines',
'PN--Pitcairn',
'PL--Poland',
'PT--Portugal',
'PR--Puerto Rico',
'QA--Qatar',
'RE--Reunion',
'RO--Romania',
'RU--Russia',
'RW--Rwanda',
'SH--Saint Helena',
'KN--Saint Kitts And Nevis',
'LC--Saint Lucia',
'PM--Saint Pierre And Miquelon',
'VC--Saint Vincent And The Grenadines',
'SM--San Marino',
'ST--Sao Tome And Principe',
'SA--Saudi Arabia',
'SN--Senegal',
'SC--Seychelles',
'SL--Sierra Leone',
'SG--Singapore',
'SK--Slovak Republic',
'SI--Slovenia',
'SB--Solomon Islands',
'SO--Somalia',
'ZA--South Africa',
'GS--South Georgia And South Sandwich Islands',
'KR--South Korea',
'ES--Spain',
'LK--Sri Lanka',
'SD--Sudan',
'SR--Suriname',
'SJ--Svalbard And Jan Mayen',
'SZ--Swaziland',
'SE--Sweden',
'CH--Switzerland',
'SY--Syria',
'TW--Taiwan',
'TJ--Tajikistan',
'TZ--Tanzania',
'TH--Thailand',
'TG--Togo',
'TK--Tokelau',
'TO--Tonga',
'TT--Trinidad And Tobago',
'TN--Tunisia',
'TR--Turkey',
'TM--Turkmenistan',
'TC--Turks And Caicos Islands',
'TV--Tuvalu',
'UG--Uganda',
'UA--Ukraine',
'AE--United Arab Emirates',
'UK--United Kingdom',
'US--United States',
'UM--United States Minor Outlying Islands',
'UY--Uruguay',
'UZ--Uzbekistan',
'VU--Vanuatu',
'VA--Vatican City (Holy See)',
'VE--Venezuela',
'VN--Vietnam',
'VG--Virgin Islands (British)',
'VI--Virgin Islands (US)',
'WF--Wallis And Futuna Islands',
'EH--Western Sahara',
'WS--Western Samoa',
'YE--Yemen',
'YU--Yugoslavia',
'ZM--Zambia',
'ZW--Zimbabwe'
);

$two_digit_country_code = array(
'AF',
'AL',
'DZ',
'AS',
'AD',
'AO',
'AI',
'AQ',
'AG',
'AR',
'AM',
'AW',
'AU',
'AT',
'AZ',
'BS',
'BH',
'BD',
'BB',
'BY',
'BE',
'BZ',
'BJ',
'BM',
'BT',
'BO',
'BA',
'BW',
'BV',
'BR',
'IO',
'BN',
'BG',
'BF',
'BI',
'KH',
'CM',
'CA',
'CV',
'KY',
'CF',
'TD',
'CL',
'CN',
'CX',
'CC',
'CO',
'KM',
'CG',
'CK',
'CR',
'CI',
'HR',
'CU',
'CY',
'CZ',
'CD',
'DK',
'DJ',
'DM',
'DO',
'TP',
'EC',
'EG',
'SV',
'GQ',
'ER',
'EE',
'ET',
'FK',
'FO',
'FJ',
'FI',
'FR',
'FX',
'GF',
'PF',
'TF',
'GA',
'GM',
'GE',
'DE',
'GH',
'GI',
'GR',
'GL',
'GD',
'GP',
'GU',
'GT',
'GN',
'GW',
'GY',
'HT',
'HM',
'HN',
'HK',
'HU',
'IS',
'IN',
'ID',
'IR',
'IQ',
'IE',
'IL',
'IT',
'JM',
'JP',
'JO',
'KZ',
'KE',
'KI',
'KW',
'KG',
'LA',
'LV',
'LB',
'LS',
'LR',
'LY',
'LI',
'LT',
'LU',
'MO',
'MK',
'MG',
'MW',
'MY',
'MV',
'ML',
'MT',
'MH',
'MQ',
'MR',
'MU',
'YT',
'MX',
'FM',
'MD',
'MC',
'MN',
'MS',
'MA',
'MZ',
'MM',
'NA',
'NR',
'NP',
'NL',
'AN',
'NC',
'NZ',
'NI',
'NE',
'NG',
'NU',
'NF',
'KP',
'MP',
'NO',
'OM',
'PK',
'PW',
'PA',
'PG',
'PY',
'PE',
'PH',
'PN',
'PL',
'PT',
'PR',
'QA',
'RE',
'RO',
'RU',
'RW',
'SH',
'KN',
'LC',
'PM',
'VC',
'SM',
'ST',
'SA',
'SN',
'SC',
'SL',
'SG',
'SK',
'SI',
'SB',
'SO',
'ZA',
'GS',
'KR',
'ES',
'LK',
'SD',
'SR',
'SJ',
'SZ',
'SE',
'CH',
'SY',
'TW',
'TJ',
'TZ',
'TH',
'TG',
'TK',
'TO',
'TT',
'TN',
'TR',
'TM',
'TC',
'TV',
'UG',
'UA',
'AE',
'UK',
'US',
'UM',
'UY',
'UZ',
'VU',
'VA',
'VE',
'VN',
'VG',
'VI',
'WF',
'EH',
'WS',
'YE',
'YU',
'ZM',
'ZW'
);

//country
$form .="<tr><td width=\"400px\"><label>Country:</label></td><td>";
$form .="<select name=\"country\"/><option value=''>Please Select One</option>";

//create country option value using $two_digit_country_description and $two_digit_country_code arrays.
for($i=0;$i<239;$i++){
$form .="<option value='$two_digit_country_code[$i]'";

if($two_digit_country_code[$i] == $country){$form.='selected="selected"';}

$form .=">$two_digit_country_description[$i]</option>";
}


$form.="</select>*</td></tr>";

//phone
$form .="<tr class=\"odd\"><td width=\"400px\"><label>Phone:</label></td><td><input style=\"width:300px\" type=\"text\" name=\"phone\" value=\"$phone\"/>*</td></tr>";

//TOU
$form .="<tr><td width=\"400px\"><label>Terms of Use:</label></td><td>I agree with the<a href='";
$form .= ICOPYRIGHT_URL."publisher/statichtml/CSA-Online-Plugin.pdf";
$form .="' target='_blank'> terms of use.</a> <input id=\"tou\" name=\"tou\" type=\"checkbox\" value=\"true\" style='border:none;'";

//get global value to determine whether form has been posted before.
//if true, we will check the checkbox.
//global variable set in icopyright-admin.php line 103
global $icopyright_tou_checked;
if($icopyright_tou_checked=='true'){
$form.="checked=yes>*</td></tr>";
}else{
$form.=">*</td></tr>";
}

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

    //get publication id and ez_excerpt setting from options table from icopyright_admin array
	$admin_option = get_option('icopyright_admin');
	$pub_id_no = $admin_option['pub_id'];
	$ez_excerpt = $admin_option['ez_excerpt'];
	
	//check publication id is not empty and all numerics
	//if not return nothing to content filter by just simply let return;
	if(empty($pub_id_no)||!is_numeric($pub_id_no)){
	return;
	}
	
	//assign ICOPYRIGHT_URL constant
	$icopyright_url = ICOPYRIGHT_URL;
	
	//get post id 
    global $post;
	$post_id = $post->ID;
	
	//construct link href
    $toolbar = "\n<!-- iCopyright Horizontal Article Toolbar -->\n";
	$toolbar .= "<script type=\"text/javascript\">\n";
	$toolbar .= "var icx_publication_id = '$pub_id_no';\n";
	$toolbar .= "var icx_content_id = '$post_id';\n";

	//check for ez_excerpt status, if yes, output into javascript
	if($ez_excerpt=='yes'){
	$toolbar .= "var icx_ez_excerpt = true;\n";	
	}
    $toolbar .= "</script>\n";
	
	$toolbar_script_url = ICOPYRIGHT_URL.'rights/js/horz-toolbar.js';//ICOPYRIGHT_URL constant defined in icopyright.php

    $toolbar .= "<script type=\"text/javascript\" src=\"$toolbar_script_url\"></script>\n";
	//extra css to control float from admin
	$toolbar .=  icopyright_toolbar_float();
	$toolbar .= "<!--End of iCopyright Horizontal Article Toolbar -->\n";
	
	//add conditional check so that toolbar will only display in full page or full post
	if(is_page()||is_single()){
	// check for icopyright custom field from post editor
	$icopyright_hide_toolbar = get_post_meta($post->ID, 'icopyright_hide_toolbar', $single = true);
	// if blogger choose to hide particular post, we will not display it, if not display as normal
	if($icopyright_hide_toolbar !== 'yes') { 
		return $toolbar;
		}
	}

}

//Generate Vertical Toolbar from hosted script
function icopyright_vertical_toolbar(){
    
	//script hosted on license.icopyright.net

    //get publication id and ez_excerpt setting from options table from icopyright_admin array
	$admin_option = get_option('icopyright_admin');
	$pub_id_no = $admin_option['pub_id'];
	$ez_excerpt = $admin_option['ez_excerpt'];
	
	//check publication id is not empty and all numerics
	//if not return nothing to content filter by just simply let return;
	if(empty($pub_id_no)||!is_numeric($pub_id_no)){
	return;
	}
	
	//assign ICOPYRIGHT_URL constant
	$icopyright_url = ICOPYRIGHT_URL;
	
	//get post id 
    global $post;
	$post_id = $post->ID;
	
  
    $toolbar = "\n<!-- iCopyright Vertical Article Toolbar -->\n";
	$toolbar .= "<script type=\"text/javascript\">\n";
	$toolbar .= "var icx_publication_id = '$pub_id_no';\n";
	$toolbar .= "var icx_content_id = '$post_id';\n";
	
	//check for ez_excerpt status, if yes, output into javascript
	if($ez_excerpt=='yes'){
	$toolbar .= "var icx_ez_excerpt = true;\n";	
	}

	//construct toolbar link urls
    $toolbar .= "</script>\n";
	
	$toolbar_script_url = ICOPYRIGHT_URL.'rights/js/vert-toolbar.js';//ICOPYRIGHT_URL constant defined in icopyright.php

    $toolbar .= "<script type=\"text/javascript\" src=\"$toolbar_script_url\"></script>\n";
	//extra css to control float from admin
	$toolbar .=  icopyright_toolbar_float();
	$toolbar .= "<!--End of iCopyright Vertical Article Toolbar -->\n";
	
	//add conditional check so that toolbar will only display in full page or full post
	if(is_page()||is_single()){
	// check for icopyright custom field from post editor
	$icopyright_hide_toolbar = get_post_meta($post->ID, 'icopyright_hide_toolbar', $single = true);
	// if blogger choose to hide particular post, we will not display it, if not display as normal
	if($icopyright_hide_toolbar !== 'yes') { 
		return $toolbar;
		}
    }
}


//Generate iCopyright interactive notice
function icopyright_interactive_notice(){

	//get publication id from options table from icopyright_admin array
	$pub_id = get_option('icopyright_admin');
	$pub_id_no = $pub_id['pub_id'];
	
	//check publication id is not empty and all numerics
	//if not return nothing to content filter by just simply let return;
	if(empty($pub_id_no)||!is_numeric($pub_id_no)){
	return;
	}
	
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

	//add conditional check so that notice will only display in full page or full post
	if(is_page()||is_single()){
	// check for icopyright custom field from post editor
	//get post id 
    global $post;
	$post_id = $post->ID;
	$icopyright_hide_toolbar = get_post_meta($post_id, 'icopyright_hide_toolbar', $single = true);
	// if blogger choose to hide particular post, we will not display it, if not display as normal
	if($icopyright_hide_toolbar !== 'yes') { 
		return $icn;
		}
	}
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

//added in Version 1.0.8
//add custom meta data box to admin page!

//adds a custom meta box to the add or edit Post and Page editor
function icopyright_add_custom_box() {

  if( function_exists( 'add_meta_box' )) {

    add_meta_box( 'icopyright_sectionid', __( 'iCopyright Custom Field', 'icopyright_textdomain' ), 
                'icopyright_inner_custom_box', 'post', 'normal' ,'high');

    add_meta_box( 'icopyright_sectionid', __( 'iCopyright Custom Field', 'icopyright_textdomain' ), 
                'icopyright_inner_custom_box', 'page', 'normal' ,'high');

   } 

}

//creates the inner fields for the custom meta box
function icopyright_inner_custom_box() {

  //Create icopyright_admin_nonce for verification
  echo '<input type="hidden" name="icopyright_noncename" id="icopyright_noncename" value="' . 
        wp_create_nonce('icopyright_admin_nonce') . '" />';

    //use WordPress global post object
    //determine post type, so as to get correct post id object.
	//for future compatibility if there is a change in page or post object.
    global $post;
    if($post->post_type == 'page'){
        $content .= $post->ID;
    } elseif($post->post_type == 'post') {
        $content .= $post->ID;
    }
  
  //retrieve custom field data
  $data = get_post_meta($content, 'icopyright_hide_toolbar', true);
 
  echo "<p><label>Do not offer iCopyright Article Tools on this story</label> <input name=\"icopyright_hide_toolbar\" type=\"checkbox\" value=\"yes\"";
  if($data == 'yes'){echo 'checked';}else{echo '';};
  echo " /></p>";
  
 
}

//saves our custom field data, when the post is saved
function icopyright_save_postdata( $post_id ) {

  //check admin nonce
  if ( !wp_verify_nonce( $_POST['icopyright_noncename'],'icopyright_admin_nonce')) {
    return $post_id;
  }

  //check user permission
  if ( 'page' == $_POST['post_type'] ) {
    if ( !current_user_can( 'edit_page', $post_id ))
      return $post_id;
  } else {
    if ( !current_user_can( 'edit_post', $post_id ))
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
?>