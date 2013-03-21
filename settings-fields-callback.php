<?php

//
// Field callbacks
//
function first_name_field_callback() {
    icopyright_make_account_row(150, 'fname');
}
function last_name_field_callback() {
    icopyright_make_account_row(150, 'lname');
}
function site_name_field_callback() {
    icopyright_make_account_row(200, 'site_name');
}
function site_url_field_callback() {
    icopyright_make_account_row(200, 'site_url');
}
function address_line1_field_callback() {
    icopyright_make_account_row(200, 'address_line1');
}
function address_line2_field_callback() {
    icopyright_make_account_row(200, 'address_line2');
}
function address_line3_field_callback() {
    icopyright_make_account_row(200, 'address_line3');
}
function address_city_field_callback() {
    icopyright_make_account_row(200, 'address_city');
}
function address_state_field_callback() {
    icopyright_make_account_row(50, 'address_state');
}
function address_country_field_callback() {
    icopyright_make_account_row(50, 'address_country');
}
function address_postal_field_callback() {
    icopyright_make_account_row(100, 'address_postal');
}
function address_phone_field_callback() {
    icopyright_make_account_row(100, 'address_phone');
}
function display_field_callback() {
    ?>
    <input name="display" type="radio" value="auto"  onclick="hide_manual_option()" <?php $icopyright_display = get_option('display'); if(empty($icopyright_display)||$icopyright_display=="auto"){echo "checked";}?> />
    <?php _e('Automatic ')?><br/>
    <span class="description">
		    <?php _e('iCopyright Toolbar and Interactive Copyright Notice will be automatically added into content of post')?>
		</span>

    <br />

    <input name="display" type="radio" value="manual" onclick="show_manual_option()" <?php $icopyright_display2 = get_option('display'); if($icopyright_display2=="manual"){echo "checked";}?>/>
    <?php _e('Manual ')?><br/>
    <span class="description">
		    <?php _e('Deploy iCopyright Toolbar and Interactive Copyright Notice into content of post, using WordPress shortcode')?>
		</span>

    <div id="M3" style="float:left;margin:0 50px 0 0;display:none;<?php $display5 = get_option('display'); if($display5=="manual"){echo "display:block;";}?>">
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
<?php
}
function tools_field_callback() {
    ?>
    <fieldset id="toolbar-format">
        <input name="tools" type="radio" value="horizontal" <?php $icopyright_tools = get_option('tools'); if(empty($icopyright_tools)||$icopyright_tools=="horizontal"){echo "checked";}?> />
        <iframe id="horizontal-article-tools-preview" style="border: 0;" scrolling="no" height="53" width="300"></iframe>
        <input name="tools" type="radio" value="vertical" <?php if($icopyright_tools=="vertical"){echo "checked";}?> />
        <iframe id="vertical-article-tools-preview" style="border: 0;" scrolling="no" height="130" width="100"></iframe>
        <input name="tools" type="radio" value="onebutton" <?php if($icopyright_tools=="onebutton"){echo "checked";}?> />
        <iframe id="onebutton-article-tools-preview" style="border: 0;" scrolling="no" height="250" width="200"></iframe>
    </fieldset>
<?php
}
function theme_field_callback() {
    ?>
    <fieldset>
        <select name="theme" class="form-select" id="icopyright_article_tools_theme" >
            <?php
            $themes = icopyright_theme_options();
            $icopyright_theme = get_option('theme'); if(empty($icopyright_theme)) $icopyright_theme = 'CLASSIC';
            foreach($themes as $option => $name) {
                print "<option value=\"$option\"";
                if($option == $icopyright_theme) print ' selected="selected"';
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
        <input name="background" type="radio" value="OPAQUE" <?php $icopyright_background = get_option('background'); if(empty($icopyright_background)||$icopyright_background=="OPAQUE"){echo "checked";}?> /> <?php _e('Opaque')?>
        <br/>
        <input name="background" type="radio" value="TRANSPARENT" <?php if($icopyright_background=="TRANSPARENT"){echo "checked";}?> /> <?php _e('Transparent')?>
    </fieldset>
<?php
}
function align_field_callback() {
    ?>
    <fieldset>
        <input name="align" type="radio" value="left" <?php $icopyright_align = get_option('align'); if(empty($icopyright_align)||$icopyright_align=="left"){echo "checked";}?> /> <?php _e('Left')?>
        <br/>
        <input name="align" type="radio" value="right" <?php $icopyright_align = get_option('align');if($icopyright_align=="right"){echo "checked";}?> /> <?php _e('Right')?>
    </fieldset>

<?php
}
function copyright_notice_preview_callback() {
    ?>
    <fieldset>
        <iframe id="copyright-notice-preview" style="border: 0;" height="50" scrolling="no" ></iframe>
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
                    <input name="show" type="radio" value="both" <?php $icopyright_show = get_option('show'); if(empty($icopyright_show)||$icopyright_show=="both"){echo "checked";}?> />
                </td>
                <td style="text-align: center;">
                    <input name="show_multiple" type="radio" value="both" <?php $icopyright_show_multiple = get_option('show_multiple'); if(empty($icopyright_show_multiple)||$icopyright_show_multiple=="both"){echo "checked";}?> />
                </td>
                <td>
                    Show both iCopyright Toolbar and Interactive Copyright Notice
                </td>
            </tr>
            <tr class="show-toolbar">
                <td style="text-align: center;">
                    <input name="show" type="radio" value="tools" <?php $icopyright_show = get_option('show');if($icopyright_show=="tools"){echo "checked";}?> />
                </td>
                <td style="text-align: center;">
                    <input name="show_multiple" type="radio" value="tools" <?php $icopyright_show_multiple = get_option('show_multiple');if($icopyright_show_multiple=="tools"){echo "checked";}?> />
                </td>
                <td>
                    Show only iCopyright Toolbar
                </td>
            </tr>
            <tr class="show-icn">
                <td style="text-align: center;">
                    <input name="show" type="radio" value="notice" <?php $icopyright_show = get_option('show');if($icopyright_show=="notice"){echo "checked";}?> />
                </td>
                <td style="text-align: center;">
                    <input name="show_multiple" type="radio" value="notice" <?php $icopyright_show_multiple = get_option('show_multiple');if($icopyright_show_multiple=="notice"){echo "checked";}?> />
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
                    <input name="show_multiple" type="radio" value="nothing" <?php $icopyright_show_multiple = get_option('show_multiple');if($icopyright_show_multiple=="nothing"){echo "checked";}?> />
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

function display_on_pages_field_callback() {
    ?>
        <fieldset>
            <input id="display_on_pages" name="display_on_pages" type="checkbox" <?php if(get_option('display_on_pages') == 'yes') print 'checked="checked"'; ?> value="yes">
            <label for="display_on_pages">Display tools on pages as well as posts</label>
        </fieldset>
    <?php
}

function use_category_filter_field_callback() {
    $use_filter = get_option('use_category_filter');
    ?>
    <fieldset>
        <input class="category-radio" name="use_category_filter" type="radio" value="no" <?php if($use_filter!="yes"){echo "checked";}?> /> <?php _e('Apply tools to all posts')?>
        <br />
        <input class="category-radio" name="use_category_filter" type="radio" value="yes" <?php if($use_filter=="yes"){echo "checked";}?> /> <?php _e('Apply tools only to selected categories')?>
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
        $selectedCategories = icopyright_selected_categories();
        echo '<ul>';

        foreach( $systemCategories as $cat ) {
            $checked = (in_array($cat->term_id, $selectedCategories) ? 'checked' : '');
            echo '<li><input id="'.$cat->term_id.'" type="checkbox" name="categories[]" value="'.$cat->term_id.'" '.$checked.' /><label style="margin-left: 5px;" for="'.$cat->term_id.'">'.$cat->name.'</label></li>';
        }
        echo '</ul></div>';
        ?>
    </fieldset>
    <?php
}

function share_field_callback() {
    $icopyright_share = get_option('share');
    $check_email = get_option('icopyright_conductor_email');
    $check_password = get_option('icopyright_conductor_password');
    ?>
        <fieldset>
            <input name="share" type="radio" value="yes" <?php if($icopyright_share=="yes"){echo "checked";}?> <?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('On ')?>
            <br/>
            <input name="share" type="radio" value="no" <?php if(empty($icopyright_share)||$icopyright_share=="no"){echo "checked";}?><?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('Off ')?>
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
            <input name="ez_excerpt" type="radio" value="yes" <?php $icopyright_ez_excerpt = get_option('ez_excerpt'); if(empty($icopyright_ez_excerpt)||$icopyright_ez_excerpt=="yes"){echo "checked";}?> <?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('On ')?>
            <br/>
            <input name="ez_excerpt" type="radio" value="no" <?php $icopyright_ez_excerpt2 = get_option('ez_excerpt'); if($icopyright_ez_excerpt2=="no"){echo "checked";}?> <?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('Off ')?>
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
            <input name="syndication" type="radio" value="yes" <?php $icopyright_syndication = get_option('syndication'); if(empty($icopyright_syndication)||$icopyright_syndication=="yes"){echo "checked";}?> <?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('On ')?>
            <br/>
            <input name="syndication" type="radio" value="no" <?php $icopyright_syndication2 = get_option('syndication'); if($icopyright_syndication2=="no"){echo "checked";}?><?php if(empty($check_email) || empty($check_password)){echo 'disabled';}?>/> <?php _e('Off ')?>
        </fieldset>
        <span class="description">The Syndication Feed service enables other websites to subscribe to a feed
                  of your content and pay you based on the number of times your articles are viewed on their site at
                  a CPM rate you specify. When you receive your welcome email, click to go into Conductor and set the
                  business terms you would like. Until you do that, default pricing and business terms will apply.</span>
    <?php
}

function pub_id_field_callback() {
    ?>
        <input type="text" name="pub_id" style="width:200px" value="<?php $icopyright_pubid = sanitize_text_field(stripslashes(get_option('pub_id'))); echo $icopyright_pubid; ?>"/>
    <?php
}

function conductor_email_field_callback() {
    ?>
        <input type="text" name="icopyright_conductor_email" style="width:200px;" value="<?php echo sanitize_text_field(stripslashes(get_option('icopyright_conductor_email'))); ?>"/>
    <?php
}

function conductor_password_field_callback() {
    ?>
        <input type="password" name="icopyright_conductor_password" style="width:200px;" value="<?php echo sanitize_text_field(stripslashes(get_option('icopyright_conductor_password'))); ?>"/>
    <?php
}

function feed_url_field_callback() {
    $feedUrl = sanitize_text_field(stripslashes(get_option('feed_url')));
    ?>
        <input type="text" name="feed_url" style="width:500px;"
           value="<?php echo (!empty($feedUrl) ? $feedUrl : icopyright_get_default_feed_url()); ?>"/>
    <?php
}

?>