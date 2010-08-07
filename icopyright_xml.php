<?php
/**
 * Feed Template for displaying iCopyright feed, modified from WordPress core file wp-includes/feed-rss2.php
 */
//include wp-config
$root = dirname(dirname(dirname(dirname(__FILE__))));
if (file_exists($root.'/wp-load.php')) {
// WP 2.6
require_once($root.'/wp-load.php');
} else {
// Before 2.6
require_once($root.'/wp-config.php');
}
//include $wpdb class
require_once(ABSPATH . WPINC . '/wp-db.php');

//function to format date
function format_date($date)
{
    $full_date = explode(' ',$date);
    $date =  explode('-',$full_date[0]);
    $year = $date['0'];
    $month = $date['1'];
    $day = $date['2'];
    return $month.'/'.$day.'/'.$year;
}

//get id http queried from icopyright conductor
$icopyright_post_id = $_GET['id'];

global $wpdb;

$posttable = $wpdb->prefix."posts";

$usertable = $wpdb->prefix."users";

$response = $wpdb->get_results("SELECT * FROM $posttable JOIN $usertable on $posttable.post_author=$usertable.ID WHERE $posttable.ID = '$icopyright_post_id'");
foreach ($response as $res){

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="UTF-8"?>';


    // prepare some variable for the XML feed
    $icx_author = $res->display_name;
    
    
	// get the User Role
    $user = new WP_User($res->post_author);

    if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
	foreach ( $user->roles as $role )
		$icx_byline = $role;
    }


    // get copyrights
    $publish_date = $res->post_date;
    $date = explode('-',$publish_date);
    $icx_copyright = $date['0']." ".get_bloginfo();
    $icx_pubyear =  $date['0'];

    // get publication date
    $icx_pubdate = format_date($publish_date);

    // get heading
    $icx_headline = $res->post_title;
    
    //get story from database
	//add in <br> to format content
	$icx_story_raw = nl2br($res->post_content);

	//do_shortcode on video embed in content.
	//show it only as a link to avoid XML Structure Error in Feed!
	$run_embed = new WP_Embed;
    $icx_story_pro_1 = $run_embed->run_shortcode($icx_story_raw);
	
	//assign id passed to feed to global post id
	//so that shortcodes like gallery that needs the post id will work!
	global $post;
	$post->ID = $icopyright_post_id;
	
	//do all other shortcodes
	$icx_story_pro_2 = do_shortcode($icx_story_pro_1);
    
	//assign final processed content to produce in feed.
	$icx_story = $icx_story_pro_2;

    //get url
    $icx_url = get_permalink($icopyright_post_id);

    // get category
    $category = get_the_category($icopyright_post_id);
    $icx_section = $category[0]->cat_name;

	// Construct the XML feed output
	$xml = "<icx>\n";
	$xml.="<icx_authors>$icx_author</icx_authors>\n";
	$xml.="<icx_byline>$icx_byline</icx_byline>\n";
	$xml.="<icx_copyright>$icx_copyright</icx_copyright>\n";
	$xml.="<icx_deckheader>$icx_headline</icx_deckheader>\n";
	$xml.="<icx_headline>$icx_headline</icx_headline>\n";
	$xml.="<icx_pubdate>$icx_pubdate</icx_pubdate>\n";
	$xml.="<icx_pubyear>$icx_pubyear</icx_pubyear>\n";
	$xml.="<icx_section>$icx_section</icx_section>\n";
	$xml.="<icx_story>$icx_story</icx_story>\n";
	$xml.="<icx_url>$icx_url</icx_url>\n";
	$xml.="</icx>";
	
	echo $xml;
}
?>