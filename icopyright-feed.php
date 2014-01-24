<?php
add_action('init', 'icopyright_wp_feed_add_feed');

function icopyright_wp_feed_add_feed() {
  add_feed('icopyright_feed', 'icopyright_wp_feed_emit_feed');
}

/**
 * Feed function to display data from a post in the format expected by iCopyright's servers
 */
function icopyright_wp_feed_emit_feed() {
  // Load up the post
  $id = $_GET['id'];

  // Is this a heartbeat check?
  if (!empty($id) && strcasecmp($id, "heartbeat") == 0) {
    echo("icopyright-useragent:".ICOPYRIGHT_USERAGENT);
    return;
  }

  // Validate that the id is a number.
  if (!is_numeric($id)) {
    status_header(404);
    die();
  }
  $feed_post = get_post($id);

  // If not published, emit nothing
  if ($feed_post->post_status != 'publish') {
    status_header(403);
    die();
  }

  // If article doesn't pass basic filters do not display it
  if(!icopyright_post_passes_filters($id)) {
    status_header(403);
    die();
  }

  // Author name comes from the public item
  $user = new WP_User($feed_post->post_author);
  $icx_author = $user->data->display_name;

  // get copyright date and year based on the date the content was published
  $publish_date = $feed_post->post_date;
  $year = mysql2date('Y', $publish_date);
  $icx_copyright = $year . " " . get_bloginfo();
  $icx_pubyear = $year;
  // get publication date
  $icx_pubdate = mysql2date('F j, Y', $publish_date);

  // Headline is obviously the title
  $icx_headline = $feed_post->post_title;

  //get story from database
  //add in <br> to format content, so that no break tags are inserting during processing of shortcodes!
  $icx_story_raw = nl2br($feed_post->post_content);
  $icx_story = apply_filters('the_content', $icx_story_raw);
  $icx_excerpt = $feed_post->post_excerpt;

  //get url
  $icx_url = get_permalink($id);

  // get category
  $category = get_the_category($id);
  $icx_section_raw = $category[0]->cat_name;
  $icx_section = (strcasecmp($icx_section_raw, 'uncategorized') == 0) ? '' : $icx_section_raw;

  // Construct and emit the XML feed output. Sanitation happens iCopyright-serverside
  $xml = '<?xml version="1.0" encoding="UTF-8"?>';
  $xml .= "<icx>\n";
  $xml .= "  <icx_authors>$icx_author</icx_authors>\n";
  $xml .= "  <icx_copyright>$icx_copyright</icx_copyright>\n";
  $xml .= "  <icx_deckheader>$icx_excerpt</icx_deckheader>\n";
  $xml .= "  <icx_headline>$icx_headline</icx_headline>\n";
  $xml .= "  <icx_pubdate>$icx_pubdate</icx_pubdate>\n";
  $xml .= "  <icx_pubyear>$icx_pubyear</icx_pubyear>\n";
  $xml .= "  <icx_section>$icx_section</icx_section>\n";
  $xml .= "  <icx_story>\n$icx_story\n  </icx_story>\n";
  $xml .= "<icx_url>$icx_url</icx_url>\n";
  $xml .= "</icx>";
  echo $xml;
}

?>