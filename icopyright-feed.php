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

  // If user has disabled the toolbar on this content, hide
  $hide_toolbar = get_post_meta($id, 'icopyright_hide_toolbar', true);
  if ($hide_toolbar == 'yes') {
    status_header(403);
    die();
  }

  // Does this pass the category filter?
  if(!icopyright_post_passes_category_filter($id)) {
    status_header(403);
    die();
  }

  // Author name and byline come from author and role
  $icx_byline = '';
  $user = new WP_User($feed_post->post_author);
  $icx_author = $user->name;
  if (!empty($user->roles) && is_array($user->roles)) {
    foreach ($user->roles as $role) {
      $icx_byline = $role;
    }
  }

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
  $xml .= "  <icx_byline>$icx_byline</icx_byline>\n";
  $xml .= "  <icx_copyright>$icx_copyright</icx_copyright>\n";
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