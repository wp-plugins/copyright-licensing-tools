<?php

//
// Field callbacks
//
function first_name_field_callback() {
  $fname = get_option('icopyright_fname');
  icopyright_make_account_row(150, 'icopyright_fname', NULL, ($fname == 'Anonymous' ? '' : $fname));
}

function last_name_field_callback() {
  $lname = get_option('icopyright_lname');
  icopyright_make_account_row(150, 'icopyright_lname', NULL, ($lname == 'User' ? '' : $lname));
}

function site_name_field_callback() {
  icopyright_make_account_row(200, 'icopyright_site_name');
}

function site_url_field_callback() {
  icopyright_make_account_row(200, 'icopyright_site_url');
}

function address_line1_field_callback() {
  icopyright_make_account_row(200, 'icopyright_address_line1');
}

function address_line2_field_callback() {
  icopyright_make_account_row(200, 'icopyright_address_line2');
}

function address_line3_field_callback() {
  icopyright_make_account_row(200, 'icopyright_address_line3');
}

function address_city_field_callback() {
  icopyright_make_account_row(200, 'icopyright_address_city');
}

function address_state_field_callback() {
  icopyright_make_account_row(50, 'icopyright_address_state');
}

function address_country_field_callback() {
  $field = 'icopyright_address_country';
  $current_value = sanitize_text_field(stripslashes(get_option($field)));
  if(empty($current_value)) $current_value = 'US';
  $countries = array(
    "AF" => "Afghanistan",
    "AX" => "Åland Islands",
    "AL" => "Albania",
    "DZ" => "Algeria",
    "AS" => "American Samoa",
    "AD" => "Andorra",
    "AO" => "Angola",
    "AI" => "Anguilla",
    "AQ" => "Antarctica",
    "AG" => "Antigua and Barbuda",
    "AR" => "Argentina",
    "AM" => "Armenia",
    "AW" => "Aruba",
    "AU" => "Australia",
    "AT" => "Austria",
    "AZ" => "Azerbaijan",
    "BS" => "Bahamas",
    "BH" => "Bahrain",
    "BD" => "Bangladesh",
    "BB" => "Barbados",
    "BY" => "Belarus",
    "BE" => "Belgium",
    "BZ" => "Belize",
    "BJ" => "Benin",
    "BM" => "Bermuda",
    "BT" => "Bhutan",
    "BO" => "Bolivia",
    "BA" => "Bosnia and Herzegovina",
    "BW" => "Botswana",
    "BV" => "Bouvet Island",
    "BR" => "Brazil",
    "IO" => "British Indian Ocean Territory",
    "BN" => "Brunei Darussalam",
    "BG" => "Bulgaria",
    "BF" => "Burkina Faso",
    "BI" => "Burundi",
    "KH" => "Cambodia",
    "CM" => "Cameroon",
    "CA" => "Canada",
    "CV" => "Cape Verde",
    "KY" => "Cayman Islands",
    "CF" => "Central African Republic",
    "TD" => "Chad",
    "CL" => "Chile",
    "CN" => "China",
    "CX" => "Christmas Island",
    "CC" => "Cocos (Keeling) Islands",
    "CO" => "Colombia",
    "KM" => "Comoros",
    "CG" => "Congo",
    "CD" => "Congo, The Democratic Republic of The",
    "CK" => "Cook Islands",
    "CR" => "Costa Rica",
    "CI" => "Cote D'ivoire",
    "HR" => "Croatia",
    "CU" => "Cuba",
    "CY" => "Cyprus",
    "CZ" => "Czech Republic",
    "DK" => "Denmark",
    "DJ" => "Djibouti",
    "DM" => "Dominica",
    "DO" => "Dominican Republic",
    "EC" => "Ecuador",
    "EG" => "Egypt",
    "SV" => "El Salvador",
    "GQ" => "Equatorial Guinea",
    "ER" => "Eritrea",
    "EE" => "Estonia",
    "ET" => "Ethiopia",
    "FK" => "Falkland Islands (Malvinas)",
    "FO" => "Faroe Islands",
    "FJ" => "Fiji",
    "FI" => "Finland",
    "FR" => "France",
    "GF" => "French Guiana",
    "PF" => "French Polynesia",
    "TF" => "French Southern Territories",
    "GA" => "Gabon",
    "GM" => "Gambia",
    "GE" => "Georgia",
    "DE" => "Germany",
    "GH" => "Ghana",
    "GI" => "Gibraltar",
    "GR" => "Greece",
    "GL" => "Greenland",
    "GD" => "Grenada",
    "GP" => "Guadeloupe",
    "GU" => "Guam",
    "GT" => "Guatemala",
    "GG" => "Guernsey",
    "GN" => "Guinea",
    "GW" => "Guinea-bissau",
    "GY" => "Guyana",
    "HT" => "Haiti",
    "HM" => "Heard Island and Mcdonald Islands",
    "VA" => "Holy See (Vatican City State)",
    "HN" => "Honduras",
    "HK" => "Hong Kong",
    "HU" => "Hungary",
    "IS" => "Iceland",
    "IN" => "India",
    "ID" => "Indonesia",
    "IR" => "Iran, Islamic Republic of",
    "IQ" => "Iraq",
    "IE" => "Ireland",
    "IM" => "Isle of Man",
    "IL" => "Israel",
    "IT" => "Italy",
    "JM" => "Jamaica",
    "JP" => "Japan",
    "JE" => "Jersey",
    "JO" => "Jordan",
    "KZ" => "Kazakhstan",
    "KE" => "Kenya",
    "KI" => "Kiribati",
    "KP" => "Korea, Democratic People's Republic of",
    "KR" => "Korea, Republic of",
    "KW" => "Kuwait",
    "KG" => "Kyrgyzstan",
    "LA" => "Lao People's Democratic Republic",
    "LV" => "Latvia",
    "LB" => "Lebanon",
    "LS" => "Lesotho",
    "LR" => "Liberia",
    "LY" => "Libyan Arab Jamahiriya",
    "LI" => "Liechtenstein",
    "LT" => "Lithuania",
    "LU" => "Luxembourg",
    "MO" => "Macao",
    "MK" => "Macedonia, The Former Yugoslav Republic of",
    "MG" => "Madagascar",
    "MW" => "Malawi",
    "MY" => "Malaysia",
    "MV" => "Maldives",
    "ML" => "Mali",
    "MT" => "Malta",
    "MH" => "Marshall Islands",
    "MQ" => "Martinique",
    "MR" => "Mauritania",
    "MU" => "Mauritius",
    "YT" => "Mayotte",
    "MX" => "Mexico",
    "FM" => "Micronesia, Federated States of",
    "MD" => "Moldova, Republic of",
    "MC" => "Monaco",
    "MN" => "Mongolia",
    "ME" => "Montenegro",
    "MS" => "Montserrat",
    "MA" => "Morocco",
    "MZ" => "Mozambique",
    "MM" => "Myanmar",
    "NA" => "Namibia",
    "NR" => "Nauru",
    "NP" => "Nepal",
    "NL" => "Netherlands",
    "AN" => "Netherlands Antilles",
    "NC" => "New Caledonia",
    "NZ" => "New Zealand",
    "NI" => "Nicaragua",
    "NE" => "Niger",
    "NG" => "Nigeria",
    "NU" => "Niue",
    "NF" => "Norfolk Island",
    "MP" => "Northern Mariana Islands",
    "NO" => "Norway",
    "OM" => "Oman",
    "PK" => "Pakistan",
    "PW" => "Palau",
    "PS" => "Palestinian Territory, Occupied",
    "PA" => "Panama",
    "PG" => "Papua New Guinea",
    "PY" => "Paraguay",
    "PE" => "Peru",
    "PH" => "Philippines",
    "PN" => "Pitcairn",
    "PL" => "Poland",
    "PT" => "Portugal",
    "PR" => "Puerto Rico",
    "QA" => "Qatar",
    "RE" => "Reunion",
    "RO" => "Romania",
    "RU" => "Russian Federation",
    "RW" => "Rwanda",
    "SH" => "Saint Helena",
    "KN" => "Saint Kitts and Nevis",
    "LC" => "Saint Lucia",
    "PM" => "Saint Pierre and Miquelon",
    "VC" => "Saint Vincent and The Grenadines",
    "WS" => "Samoa",
    "SM" => "San Marino",
    "ST" => "Sao Tome and Principe",
    "SA" => "Saudi Arabia",
    "SN" => "Senegal",
    "RS" => "Serbia",
    "SC" => "Seychelles",
    "SL" => "Sierra Leone",
    "SG" => "Singapore",
    "SK" => "Slovakia",
    "SI" => "Slovenia",
    "SB" => "Solomon Islands",
    "SO" => "Somalia",
    "ZA" => "South Africa",
    "GS" => "South Georgia and The South Sandwich Islands",
    "ES" => "Spain",
    "LK" => "Sri Lanka",
    "SD" => "Sudan",
    "SR" => "Suriname",
    "SJ" => "Svalbard and Jan Mayen",
    "SZ" => "Swaziland",
    "SE" => "Sweden",
    "CH" => "Switzerland",
    "SY" => "Syrian Arab Republic",
    "TW" => "Taiwan, Province of China",
    "TJ" => "Tajikistan",
    "TZ" => "Tanzania, United Republic of",
    "TH" => "Thailand",
    "TL" => "Timor-leste",
    "TG" => "Togo",
    "TK" => "Tokelau",
    "TO" => "Tonga",
    "TT" => "Trinidad and Tobago",
    "TN" => "Tunisia",
    "TR" => "Turkey",
    "TM" => "Turkmenistan",
    "TC" => "Turks and Caicos Islands",
    "TV" => "Tuvalu",
    "UG" => "Uganda",
    "UA" => "Ukraine",
    "AE" => "United Arab Emirates",
    "GB" => "United Kingdom",
    "US" => "United States",
    "UM" => "United States Minor Outlying Islands",
    "UY" => "Uruguay",
    "UZ" => "Uzbekistan",
    "VU" => "Vanuatu",
    "VE" => "Venezuela",
    "VN" => "Viet Nam",
    "VG" => "Virgin Islands, British",
    "VI" => "Virgin Islands, U.S.",
    "WF" => "Wallis and Futuna",
    "EH" => "Western Sahara",
    "YE" => "Yemen",
    "ZM" => "Zambia",
    "ZW" => "Zimbabwe");
  ?>
  <select name="<?php echo($field);?>">
    <?php
      foreach ($countries as $code => $country) {
        ?>
          <option value="<?php echo($code); ?>"<?php echo(strcasecmp($code, $current_value) == 0 ? " selected=\"selected\"" : ""); ?>><?php echo($country); ?></option>
        <?php
      }
    ?>
  </select>
  <?php
}

function address_postal_field_callback() {
  icopyright_make_account_row(100, 'icopyright_address_postal');
}

function address_phone_field_callback() {
  icopyright_make_account_row(100, 'icopyright_address_phone');
}

function display_field_callback() {
  ?>
<input name="icopyright_display" type="radio" value="auto"
       onclick="hide_manual_option()" <?php $icopyright_display = get_option('icopyright_display'); if (empty($icopyright_display) || $icopyright_display == "auto") {
  echo "checked";
}?> />
  <?php _e('Automatic ')?><br/>
<span class="description">
		    <?php _e('iCopyright Toolbar and Interactive Copyright Notice will be automatically added into content of post')?>
		</span>

<br/>

<input name="icopyright_display" type="radio" value="manual"
       onclick="show_manual_option()" <?php $icopyright_display2 = get_option('icopyright_display'); if ($icopyright_display2 == "manual") {
  echo "checked";
}?>/>
  <?php _e('Manual ')?><br/>
<span class="description">
		    <?php _e('Deploy iCopyright Toolbar and Interactive Copyright Notice into content of post, using WordPress shortcode')?>
		</span>

<div id="M3"
     style="float:left;margin:0 50px 0 0;display:none;<?php $display5 = get_option('icopyright_display'); if ($display5 == "manual") {
       echo "display:block;";
     }?>">
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
      <th>Purpose</th>
      <th>Attribute</th>
      <th>Variations</th>
      <th>Example Usage</th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td>Default</td>
      <td>--</td>
      <td>--</td>
      <td>[icopyright horizontal toolbar]</td>
    </tr>
    <tr>
      <td>For alignment</td>
      <td>float="right"</td>
      <td>float="left"<br/>float="right"</td>
      <td>[icopyright horizontal toolbar float="right"]</td>
    </tr>
    </tbody>
  </table>
</div>
<?php
}

function tools_field_callback() {
  ?>
<fieldset id="toolbar-format">
  <input name="icopyright_tools" type="radio"
         value="horizontal" <?php $icopyright_tools = get_option('icopyright_tools'); if (empty($icopyright_tools) || $icopyright_tools == "horizontal") {
    echo "checked";
  }?> />
  <iframe id="horizontal-article-tools-preview" style="border: 0;" scrolling="no" height="53" width="300"></iframe>
  <input name="icopyright_tools" type="radio" value="vertical" <?php if ($icopyright_tools == "vertical") {
    echo "checked";
  }?> />
  <iframe id="vertical-article-tools-preview" style="border: 0;" scrolling="no" height="130" width="100"></iframe>
  <input name="icopyright_tools" type="radio" value="onebutton" <?php if ($icopyright_tools == "onebutton") {
    echo "checked";
  }?> />
  <iframe id="onebutton-article-tools-preview" style="border: 0;" scrolling="no" height="250" width="200"></iframe>
</fieldset>
<?php
}

function theme_field_callback() {
  ?>
<fieldset>
  <select name="icopyright_theme" class="form-select" id="icopyright_article_tools_theme">
    <?php
    $themes = icopyright_theme_options();
    $icopyright_theme = get_option('icopyright_theme'); if (empty($icopyright_theme)) {
    $icopyright_theme = 'CLASSIC';
  }
    foreach ($themes as $option => $name) {
      print "<option value=\"$option\"";
      if ($option == $icopyright_theme) {
        print ' selected="selected"';
      }
      print ">$name</option>";
    }
    ?>
  </select>
</fieldset>
<?php
}

function background_field_callback() {
  ?>
<fieldset>
  <input name="icopyright_background" type="radio"
         value="OPAQUE" <?php $icopyright_background = get_option('icopyright_background'); if (empty($icopyright_background) || $icopyright_background == "OPAQUE") {
    echo "checked";
  }?> /> <?php _e('Opaque')?>
  <br/>
  <input name="icopyright_background" type="radio"
         value="TRANSPARENT" <?php if ($icopyright_background == "TRANSPARENT") {
    echo "checked";
  }?> /> <?php _e('Transparent')?>
</fieldset>
<?php
}

function align_field_callback() {
  ?>
<fieldset>
  <input name="icopyright_align" type="radio"
         value="left" <?php $icopyright_align = get_option('icopyright_align'); if (empty($icopyright_align) || $icopyright_align == "left") {
    echo "checked";
  }?> /> <?php _e('Left')?>
  <br/>
  <input name="icopyright_align" type="radio"
         value="right" <?php $icopyright_align = get_option('icopyright_align');if ($icopyright_align == "right") {
    echo "checked";
  }?> /> <?php _e('Right')?>
</fieldset>

<?php
}

function copyright_notice_preview_callback() {
  ?>
<fieldset>
  <iframe id="copyright-notice-preview" style="border: 0;" height="50" scrolling="no"></iframe>
</fieldset>
<?php
}

function show_preview_callback() {
  ?>
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
        <input name="icopyright_show" type="radio"
               value="both" <?php $icopyright_show = get_option('icopyright_show'); if (empty($icopyright_show) || $icopyright_show == "both") {
          echo "checked";
        }?> />
      </td>
      <td style="text-align: center;">
        <input name="icopyright_show_multiple" type="radio"
               value="both" <?php $icopyright_show_multiple = get_option('icopyright_show_multiple'); if (empty($icopyright_show_multiple) || $icopyright_show_multiple == "both") {
          echo "checked";
        }?> />
      </td>
      <td>
        Show both iCopyright Toolbar and Interactive Copyright Notice
      </td>
    </tr>
    <tr class="show-toolbar">
      <td style="text-align: center;">
        <input name="icopyright_show" type="radio"
               value="tools" <?php $icopyright_show = get_option('icopyright_show');if ($icopyright_show == "tools") {
          echo "checked";
        }?> />
      </td>
      <td style="text-align: center;">
        <input name="icopyright_show_multiple" type="radio"
               value="tools" <?php $icopyright_show_multiple = get_option('icopyright_show_multiple');if ($icopyright_show_multiple == "tools") {
          echo "checked";
        }?> />
      </td>
      <td>
        Show only iCopyright Toolbar
      </td>
    </tr>
    <tr class="show-icn">
      <td style="text-align: center;">
        <input name="icopyright_show" type="radio"
               value="notice" <?php $icopyright_show = get_option('icopyright_show');if ($icopyright_show == "notice") {
          echo "checked";
        }?> />
      </td>
      <td style="text-align: center;">
        <input name="icopyright_show_multiple" type="radio"
               value="notice" <?php $icopyright_show_multiple = get_option('icopyright_show_multiple');if ($icopyright_show_multiple == "notice") {
          echo "checked";
        }?> />
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
        <input name="icopyright_show_multiple" type="radio"
               value="nothing" <?php $icopyright_show_multiple = get_option('icopyright_show_multiple');if ($icopyright_show_multiple == "nothing") {
          echo "checked";
        }?> />
      </td>
      <td>
        Show nothing
      </td>
    </tr>
    </tbody>
  </table>
</fieldset>
<?php
}

function show_multiple_callback() {}

function display_on_pages_field_callback() {
  ?>
<fieldset>
  <input id="display_on_pages" name="icopyright_display_on_pages"
         type="checkbox" <?php if (get_option('icopyright_display_on_pages') == 'yes') {
    print 'checked="checked"';
  } ?>
         value="yes">
  <label for="display_on_pages">Display tools on pages as well as posts</label>
</fieldset>
<?php
}

function use_category_filter_field_callback() {
  $use_filter = get_option('icopyright_use_category_filter');
  ?>
<fieldset>
  <input class="category-radio" name="icopyright_use_category_filter" type="radio"
         value="no" <?php if ($use_filter != "yes") {
    echo "checked";
  }?> /> <?php _e('Apply tools to all posts')?>
  <br/>
  <input class="category-radio" name="icopyright_use_category_filter" type="radio"
         value="yes" <?php if ($use_filter == "yes") {
    echo "checked";
  }?> /> <?php _e('Apply tools only to selected categories')?>
  <br/>
</fieldset>
<?php
}

function categories_field_callback() {
  $systemCategories = get_categories();
  ?>
<fieldset>

  <?php
  echo '<div id="icopyright-category-list" style="font-size:10px;"><span class="description">Select categories on which to display the Article Tools.</span>';
  $selectedCategories = get_option('icopyright_categories', array());
  echo '<ul>';

  foreach ($systemCategories as $cat) {
    $checked = (!empty($selectedCategories) && in_array($cat->term_id, $selectedCategories) ? 'checked' : '');
    echo '<li><input id="cat_' . $cat->term_id . '" type="checkbox" name="icopyright_categories[]" value="' . $cat->term_id . '" ' . $checked . ' /><label style="margin-left: 5px;" for="cat_' . $cat->term_id . '">' . $cat->name . '</label></li>';
  }
  echo '</ul></div>';
  ?>
</fieldset>
<?php
}

function share_field_callback() {
  $icopyright_share = get_option('icopyright_share');
  $check_email = get_option('icopyright_conductor_email');
  $check_password = get_option('icopyright_conductor_password');
  ?>
<fieldset>
  <input name="icopyright_share" type="radio" value="yes" <?php if ($icopyright_share == "yes") {
    echo "checked";
  }?> <?php if (empty($check_email) || empty($check_password)) {
    echo 'disabled';
  }?>/> <?php _e('On ')?>
  <br/>
  <input name="icopyright_share" type="radio"
         value="no" <?php if (empty($icopyright_share) || $icopyright_share == "no") {
    echo "checked";
  }?><?php if (empty($check_email) || empty($check_password)) {
    echo ' disabled';
  }?>/> <?php _e('Off ')?>
</fieldset>
<span class="description">Share services make it easy for readers to share links to your articles using
                  Facebook, LinkedIn, Twitter, and Google+. Displayable in the four-button versions of the Toolbar only.</span>
<?php
}

function ez_excerpt_field_callback() {
  $check_email = get_option('icopyright_conductor_email');
  $check_password = get_option('icopyright_conductor_password');
  ?>
<fieldset>
  <input name="icopyright_ez_excerpt" type="radio"
         value="yes" <?php $icopyright_ez_excerpt = get_option('icopyright_ez_excerpt'); if (empty($icopyright_ez_excerpt) || $icopyright_ez_excerpt == "yes") {
    echo "checked";
  }?> <?php if (empty($check_email) || empty($check_password)) {
    echo 'disabled';
  }?>/> <?php _e('On ')?>
  <br/>
  <input name="icopyright_ez_excerpt" type="radio"
         value="no" <?php $icopyright_ez_excerpt2 = get_option('icopyright_ez_excerpt'); if ($icopyright_ez_excerpt2 == "no") {
    echo "checked";
  }?> <?php if (empty($check_email) || empty($check_password)) {
    echo 'disabled';
  }?>/> <?php _e('Off ')?>
</fieldset>
<span class="description">When EZ Excerpt is activated, any reader who tries to copy/paste
                  a portion of your article will be presented with a box asking "Obtain a License?". If reader
                  selects "yes" he or she will be offered the opportunity to license the excerpt for purposes of posting
                  on the reader's own website.</span>
<?php
}

function syndication_field_callback() {
  $check_email = get_option('icopyright_conductor_email');
  $check_password = get_option('icopyright_conductor_password');
  ?>
<fieldset>
  <input name="icopyright_syndication" type="radio"
         value="yes" <?php $icopyright_syndication = get_option('icopyright_syndication'); if (empty($icopyright_syndication) || $icopyright_syndication == "yes") {
    echo "checked";
  }?> <?php if (empty($check_email) || empty($check_password)) {
    echo 'disabled';
  }?>/> <?php _e('On ')?>
  <br/>
  <input name="icopyright_syndication" type="radio"
         value="no" <?php $icopyright_syndication2 = get_option('icopyright_syndication'); if ($icopyright_syndication2 == "no") {
    echo "checked";
  }?><?php if (empty($check_email) || empty($check_password)) {
    echo 'disabled';
  }?>/> <?php _e('Off ')?>
</fieldset>
<span class="description">The Syndication Feed service enables other websites to subscribe to a feed
                  of your content and pay you based on the number of times your articles are viewed on their site at
                  a CPM rate you specify. When you receive your welcome email, click to go into Conductor and set the
                  business terms you would like. Until you do that, default pricing and business terms will apply.</span>
<?php
}

function pricing_optimizer_apply_automatically_field_callback() {

}

function pricing_optimizer_opt_in_field_callback() {
  $check_email = get_option('icopyright_conductor_email');
  $check_password = get_option('icopyright_conductor_password');

  $icopyright_pricing_optimizer_opt_in = get_option('icopyright_pricing_optimizer_opt_in');
  $icopyright_pricing_optimizer_apply_automatically = get_option('icopyright_pricing_optimizer_apply_automatically');
  $icopyright_created_date = get_option('icopyright_created_date')+(3*24*60*60);
  $autoPriceOptimizerDate = date("m/d/Y", $icopyright_created_date)
  ?>
  <input type="hidden" name="icopyright_pricing_optimizer_showing" value="true"/>
  <fieldset>
    <input name="icopyright_pricing_optimizer_opt_in" type="checkbox"
          value="true" <?php if ($icopyright_pricing_optimizer_opt_in == "true") echo('checked="checked"'); ?> <?php if (empty($check_email) || empty($check_password)) echo(' disabled="disabled"');?>/> <?php _e('Start Price Optimizer on '.$autoPriceOptimizerDate.' and')?>
    <br/>
    <input class="price_optimizer_radio" name="icopyright_pricing_optimizer_apply_automatically" value="false" type="radio" <?php if ($icopyright_pricing_optimizer_apply_automatically != "true") echo('checked="checked"'); ?> <?php if (empty($check_email) || empty($check_password) || $icopyright_pricing_optimizer_opt_in == "false") echo(' disabled="disabled"');?>/> <?php _e('Show me the results so I can decide what prices to implement'); ?>
    <br/>
    <input class="price_optimizer_radio" name="icopyright_pricing_optimizer_apply_automatically" value="true" type="radio" <?php if ($icopyright_pricing_optimizer_apply_automatically == "true") echo('checked="checked"'); ?> <?php if (empty($check_email) || empty($check_password) || $icopyright_pricing_optimizer_opt_in == "false") echo(' disabled="disabled"');?>/> <?php _e('Automatically implement the pricing found to be the most profitable by Pricing Optimizer'); ?>
  </fieldset>
  <input type="hidden" name="icopyright_pricing_optimizer_apply_automatically2" value="<?php echo(($icopyright_pricing_optimizer_apply_automatically == "true") ? 'true' : 'false'); ?>"/>
  <span class="description">
    Price Optimizer runs a 10 week live test of different Instant License prices to determine which prices generate the most revenue.
  </span>
  <script type="text/javascript">
    jQuery(document).ready(function() {
      jQuery("input[name='icopyright_pricing_optimizer_opt_in']").change(function() {
        if (jQuery("input[name='icopyright_pricing_optimizer_opt_in']").is(":checked")) {
          jQuery(".price_optimizer_radio").removeAttr("disabled");
        } else {
          jQuery(".price_optimizer_radio").attr("disabled", "disabled");
        }
      });
      jQuery("input[name='icopyright_pricing_optimizer_apply_automatically']").change(function() {
        jQuery("input[name='icopyright_pricing_optimizer_apply_automatically2']").val(jQuery("input[name='icopyright_pricing_optimizer_apply_automatically']:checked").val());
      });
    });
  </script>
<?php
}

function pub_id_field_callback() {
  ?>
<input type="text" name="icopyright_pub_id" style="width:200px"
       value="<?php $icopyright_pubid = sanitize_text_field(stripslashes(get_option('icopyright_pub_id'))); echo $icopyright_pubid; ?>"/>
<span class="description" id="no_pub_id_message">Click <a
    href="<?php echo admin_url('options-general.php?page=copyright-licensing-tools&show-registration-form=1') ?>">here</a>
  to register your publication</span>
<?php
}

function conductor_email_field_callback() {
  ?>
<input type="text" name="icopyright_conductor_email" style="width:200px;"
       value="<?php echo sanitize_text_field(stripslashes(get_option('icopyright_conductor_email'))); ?>"/>
<?php
}

function conductor_password_field_callback() {
  ?>
<input type="password" name="icopyright_conductor_password" style="width:200px;"
       value="<?php echo sanitize_text_field(stripslashes(get_option('icopyright_conductor_password'))); ?>"/>
<?php
}

function feed_url_field_callback() {
  $feedUrl = sanitize_text_field(stripslashes(get_option('icopyright_feed_url')));
  ?>
<input type="text" name="icopyright_feed_url" style="width:500px;"
       value="<?php echo (!empty($feedUrl) ? $feedUrl : icopyright_get_default_feed_url()); ?>"/>
<?php
}

?>