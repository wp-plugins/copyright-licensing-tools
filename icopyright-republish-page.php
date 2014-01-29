<?php
//for logged in users
add_action('wp_ajax_repubhub_clips', 'icopyright_republish_topic_hits');
add_action('edit_form_after_title', 'icopyright_edit_form_after_title' );
function icopyright_edit_form_after_title() {
  if (!empty($_GET['icx_tag']) && get_option("repubhub_dismiss_post_new_info_box") == null) {
    ?>
      <p style="float:left; width:460px; background:lightblue; padding:5px; margin:0px 0px 10px 0px;" id="icx_post_new_info_box">
        This embed code (shown as a yellow box if you're in the Visual tab) will display the republished article.
        To Preview it, be sure to click "Save Draft" first. You may add an intro or conclusion above or below the embed code.
        <br/>
        <a style="float: right;" href="" id="icx_dismiss_post_new_info_box">Dismiss</a>
      </p>
      <div style="clear: both;"></div>
      <script type="text/javascript">
        jQuery(document).ready(function () {
          jQuery("#icx_dismiss_post_new_info_box").click(function (event) {
            jQuery("#icx_post_new_info_box").hide();
            jQuery.ajax({
              url : "/wp-admin/admin-ajax.php",
              type : "get",
              data : {action: "repubhub_dismiss_post_new_info_box", loc: '/wp-admin/edit.php?page=repubhub-republish'},
              success: function() {}
            });
            event.preventDefault();
          });
        });
      </script>
    <?php
  }
}

add_action('wp_ajax_repubhub_dismiss_post_new_info_box', 'icopyright_repubhub_dismiss_post_new_info_box');
function icopyright_repubhub_dismiss_post_new_info_box() {
  update_option("repubhub_dismiss_post_new_info_box", "true");
}

//
// Add the iCopyright republish page
//
add_action('admin_menu', 'icopyright_post_menu');
function icopyright_post_menu() {
  add_posts_page('Republish content via iCopyright\'s repubHub', 'Republish', 'edit_posts', 'repubhub-republish', 'icopyright_republish_page');
}

add_action( 'admin_bar_menu', 'icopyright_admin_bar', 999 );
function icopyright_admin_bar( $wp_admin_bar ){
  $args = array(
    'href' => '/wp-admin/edit.php?page=repubhub-republish',
    'title' => 'Republish',
    'parent' => 'new-content', // false for a root menu, pass the ID value for a submenu of that menu.
    'id' => 'republish-1', // defaults to a sanitized title value.
    'meta' => array() // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', 'target' => '', 'title' => '' );
  );
  $wp_admin_bar->add_node( $args );

  $args = array(
    'href' => '/wp-admin/edit.php?page=repubhub-republish',
    'title' => 'Republish',
    'parent' => false, // false for a root menu, pass the ID value for a submenu of that menu.
    'id' => 'republish-2', // defaults to a sanitized title value.
    'meta' => array() // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', 'target' => '', 'title' => '' );
  );
  $wp_admin_bar->add_node( $args );
}

add_filter( 'default_content', 'icopyright_republish_content', 10, 2 );

function icopyright_republish_content( $content, $post ) {
  //set content
  if (!empty( $_GET['icx_tag'] )) {
    $user_agent = ICOPYRIGHT_USERAGENT;
    $email = get_option('icopyright_conductor_email');
    $password = get_option('icopyright_conductor_password');
    $res = icopyright_get_embed(urlencode($_GET['icx_tag']), $user_agent, $email, $password);
    $xml = @simplexml_load_string($res->response);
    $content = $xml->embedCode;
    $post->title = $xml->title;
    return($content);
  }
  return $content;
}

add_filter( 'default_title', 'icopyright_republish_title', 10, 2 );

function icopyright_republish_title( $title, $post ) {
  //set content
  if (!empty( $_GET['icx_tag'] )) {
    return($post->title);
  }
  return $title;
}

function icopyright_republish_page() {
  if ($_SERVER['REQUEST_METHOD'] === 'POST')
    icopyright_republish_page_post();
  else
    icopyright_republish_page_get(array());
}

function icopyright_republish_page_post() {
  if ($_POST['action'] === "add") {
    icopyright_republish_page_post_add();
  } else if ($_POST['action'] === "delete") {
    icopyright_republish_page_post_delete();
  } else if ($_POST['action'] === "edit") {
    icopyright_republish_page_post_edit();
  }
}

function icopyright_republish_page_post_add() {
  // Get values
  $post = array(
    'andWords' => sanitize_text_field(stripslashes($_POST['andWords'])),
    'exactPhrase' => sanitize_text_field(stripslashes($_POST['exactPhrase'])),
    'orWords' => sanitize_text_field(stripslashes($_POST['orWords'])),
    'notWords' => sanitize_text_field(stripslashes($_POST['notWords']))
  );

  // Validate params.
  if (empty($post['andWords']) && empty($post['exactPhrase']) &&
    empty($post['orWords']) && empty($post['notWords'])) {
    $post['error'] = "Please provide search values.";
    icopyright_republish_page_get($post);
    return;
  }

  // Call WS
  $user_agent = ICOPYRIGHT_USERAGENT;
  $email = get_option('icopyright_conductor_email');
  $password = get_option('icopyright_conductor_password');
  $res = icopyright_add_topic(http_build_query($post), $user_agent, $email, $password);
  if(!icopyright_check_response($res)) {
    $post['error'] = "Unable to add topic at this time.  Please try again later.";
    icopyright_republish_page_get($post);
  } else {
    $topic = @simplexml_load_string($res->response);
    wp_redirect($_SERVER['PHP_SELF']."?page=".$_GET['page']."&success=".urlencode("Topic has been added!")."&topicId=".$topic->id);
  }
}

function icopyright_republish_page_post_edit() {
  // Get values
  $post = array(
    'topicId' => sanitize_text_field(stripslashes($_POST['topicId'])),
    'andWords' => sanitize_text_field(stripslashes($_POST['andWords'])),
    'exactPhrase' => sanitize_text_field(stripslashes($_POST['exactPhrase'])),
    'orWords' => sanitize_text_field(stripslashes($_POST['orWords'])),
    'notWords' => sanitize_text_field(stripslashes($_POST['notWords'])),
    'frequency' => sanitize_text_field(stripslashes($_POST['frequency']))
  );

  // Validate params.
  if (empty($post['andWords']) && empty($post['exactPhrase']) &&
    empty($post['orWords']) && empty($post['notWords'])) {
    $post['error'] = "Please provide search values.";
    icopyright_republish_page_get_edit_topic($post);
    return;
  }

  // Call WS
  $user_agent = ICOPYRIGHT_USERAGENT;
  $email = get_option('icopyright_conductor_email');
  $password = get_option('icopyright_conductor_password');
  $res = icopyright_edit_topic($_POST['topicId'], http_build_query($post), $user_agent, $email, $password);
  if(!icopyright_check_response($res)) {
    $post['error'] = "Unable to add topic at this time.  Please try again later.";
    icopyright_republish_page_get_edit_topic($post);
  } else {
    wp_redirect($_SERVER['PHP_SELF']."?page=".$_GET['page']."&success=".urlencode("Topic has been modified!")."&topicId=".$post['topicId']);
  }
}

function icopyright_republish_page_post_delete() {
  $user_agent = ICOPYRIGHT_USERAGENT;
  $email = get_option('icopyright_conductor_email');
  $password = get_option('icopyright_conductor_password');
  $res = icopyright_delete_topic($_POST['topicId'], $user_agent, $email, $password);
  if(!icopyright_check_response($res)) {
    $post['error'] = "Unable to delete topic at this time.  Please try again later.";
    icopyright_republish_page_get($post);
  } else {
    wp_redirect($_SERVER['PHP_SELF']."?page=".$_GET['page']."&success=".urlencode("Topic has been removed!"));
  }
}

function icopyright_republish_page_get($data) {
  if (!empty($_GET['success']))
    $data['success'] = $_GET['success'];
  if ($_GET['action'] === "edit") {
    $data['topicId'] = $_GET['topicId'];
    $data['andWords'] = $_GET['andWords'];
    $data['exactPhrase'] = $_GET['exactPhrase'];
    $data['orWords'] = $_GET['orWords'];
    $data['notWords'] = $_GET['notWords'];
    $data['frequency'] = $_GET['frequency'];
    if (empty($_GET['topicId']))
      icopyright_republish_page_get_edit_topic($data);
    else
      icopyright_republish_page_get_edit_topic($data, $_GET['topicId']);
  } else {
    if(isset($_GET['topicId'])) {
      icopyright_republish_page_get_topics($data, $_GET['topicId']);
    } else {
      icopyright_republish_page_get_topics($data);
    }
  }
}

function icopyright_republish_page_get_topics($data, $displayTopicId = '') {
  wp_enqueue_style('icopyright-admin-css', plugins_url('css/style.css', __FILE__), array(), '1.1.0');  // Update the version when the style changes.  Refreshes cache.
  wp_enqueue_script('icopyright-admin-js', plugins_url('js/main.js', __FILE__), array(), '1.1.0');
  $frequencies = array(
    'IMMED' => 'As Stories Break',
    'DAILY' => 'Daily',
    'WEEKLY' => 'Weekly',
    'MONTHLY' => 'Monthly',
    'NEVER' => 'Never'
  );

  ?>
  <div class="wrap">
  <?php if(!empty($data['error'])) { ?>
    <div class="icx_error fadeout"><p><?php echo $data['error']; ?></p></div>
  <?php } ?>
  <?php if(!empty($data['success'])) { ?>
    <div class="icx_success fadeout"><p><?php echo $data['success']; ?></p></div>
  <?php } ?>
  <div class="icx_republish_header">
    <h3>Find Republishable Articles</h3>
  </div>
  <div class="icx_search_wrapper">
    <form id="icx_republish_form" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?page=<?php echo $_GET['page'] ?>&noheader=true">
      <input type="hidden" name="action" value="add"/>
      <label id="icx_and_words_label" class="icx_republish_label" for="icx_and_words" style="display: none;"></label><input id="icx_and_words" type="text" name="andWords" placeholder="Enter your search terms here" value="<?php echo $data['andWords']; ?>"/>
      <input class="icx_add_btn" type="submit" value="Go"/>
      <a class="icx_republish_advanced_btn" href="">Advanced Search</a>
      <div class="icx_republish_advanced_fields" style="display: none;">
        <label class="icx_republish_label" for="icx_exact_words">With the exact phrase:</label><input id="icx_exact_words" type="text" name="exactPhrase" value="<?php echo $data['exactPhrase']; ?>"/>
        <span class="help">The exact phrase you enter here <b>must</b> exist in the content for it to match, for example <i>Mama's Apple Pies Inc.</i></span>
        <br/>
        <label class="icx_republish_label" for="icx_or_words">With at least one of the words:</label><input id="icx_or_words" type="text" name="orWords" value="<?php echo $data['orWords']; ?>"/>
        <span class="help"><b>At least one</b> of these words must exist in the content for it to match. This field is used in conjunction with one or more other fields. As an example, you might enter the words <i>retail store</i> in this field.</span>
        <br/>
        <label class="icx_republish_label" for="icx_not_words">Without the words:</label><input id="icx_not_words" type="text" name="notWords" value="<?php echo $data['notWords']; ?>"/>
        <span class="help"><b>None</b> of these words can exist in the content for it to match. This field is used in conjunction with the fields above, and is helpful for limiting a broader search. As an example, you might enter the words <i>trees orchards</i> here to avoid getting articles about apple trees and apple orchards.</span>
      </div>
    </form>
  </div>
  <div class="icx_clear"></div>
<?php
  $user_agent = ICOPYRIGHT_USERAGENT;
  $email = get_option('icopyright_conductor_email');
  $password = get_option('icopyright_conductor_password');
  $res = icopyright_get_topics($user_agent, $email, $password);

  $xml = @simplexml_load_string($res->response);
?>
  <?php if (sizeof($xml->response)>0) { ?>
  <div class="icon32" id="icon-page"><br></div>
  <h2 class="nav-tab-wrapper">
    <?php
      $index = 0;
      foreach ($xml->response as $topic) {
        ?>
          <a id="icx_nav_tab_<?php echo $topic->id; ?>" class="icx_nav_tab nav-tab<?php if((empty($displayTopicId) && $index == 0) || $displayTopicId == $topic->id){ ?> nav-tab-active<?php } ?>" href="<?php echo $topic->id; ?>"><?php echo icopyright_republish_topic_name($topic); ?></a>
        <?php
        $index ++;
      }
    ?>
  </h2>
  <?php } ?>
<?php
  $index = 0;
  foreach ($xml->response as $topic) {
?>
  <div id="icx_topic_<?php echo $topic->id; ?>" class="icx_topic" style="display: <?php if((empty($displayTopicId) && $index != 0) || (!empty($displayTopicId) && $displayTopicId != $topic->id)){ ?>none<?php } ?>;">
    <div class="icx_topic_title">
      <?php if (!empty($topic->andWords)) { ?>
        With all the words: <strong><?php echo($topic->andWords); ?></strong><br/>
      <?php } ?>
      <?php if (!empty($topic->exactPhrase)) { ?>
        With the exact phrase: <strong><?php echo($topic->exactPhrase); ?></strong><br/>
      <?php } ?>
      <?php if (!empty($topic->orWords)) { ?>
        With at least one of the words: <strong><?php echo($topic->orWords); ?></strong><br/>
      <?php } ?>
      <?php if (!empty($topic->notWords)) { ?>
        Without the words: <strong><?php echo($topic->notWords); ?></strong><br/>
      <?php } ?>
      Email me: <strong><?php echo($frequencies[$topic->frequency.""]); ?></strong><br/>
    </div>
    <div class="icx_topic_controls">
      <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?page=<?php echo $_GET['page'] ?>&noheader=true">
        <input type="hidden" name="action" value="delete"/>
        <input type="hidden" name="topicId" value="<?php echo $topic->id; ?>"/>
        <input type="submit" value="Delete Topic"/>
      </form>
    </div>
    <div class="icx_topic_controls">
      <form method="get" action="<?php echo $_SERVER['PHP_SELF'] ?>">
        <input type="hidden" name="action" value="edit"/>
        <input type="hidden" name="page" value="<?php echo $_GET['page'] ?>"/>
        <input type="hidden" name="topicId" value="<?php echo $topic->id; ?>"/>
        <input type="hidden" name="andWords" value="<?php echo $topic->andWords; ?>"/>
        <input type="hidden" name="exactPhrase" value="<?php echo $topic->exactPhrase; ?>"/>
        <input type="hidden" name="orWords" value="<?php echo $topic->orWords; ?>"/>
        <input type="hidden" name="notWords" value="<?php echo $topic->notWords; ?>"/>
        <input type="hidden" name="frequency" value="<?php echo $topic->frequency; ?>"/>
        <input type="submit" value="Edit Topic"/>
      </form>
    </div>
    <div class="icx_clear"></div>
    <div class="icx_repubhub_clips" id="icx_clips_for_topic_<?php print $topic->id ?>" data-loc="<?php print $topic->xmlLocation ?>">
      <img src="<?php print plugin_dir_url(__FILE__) ?>images/animated-spinner.gif">
    </div>
  </div>
  <div class="icx_clear"></div>
<?php
    $index ++;
  }
?>
</div>
<?php
}

/**
 * Given an XML location, returns HTML for the hits. Used in an AJAX call to fill out the page
 */
function icopyright_republish_topic_hits() {
  $xml_location = $_GET['loc'];
  $user_agent = ICOPYRIGHT_USERAGENT;
  $email = get_option('icopyright_conductor_email');
  $password = get_option('icopyright_conductor_password');
  $res = icopyright_get_topic(str_replace("http://".ICOPYRIGHT_SERVER, "", $xml_location), $user_agent, $email, $password);
  $topicxml = @simplexml_load_string($res->response);
  if (sizeof($topicxml->clips->clip) > 0 && icopyright_includes_embeddable($topicxml->clips->clip)) {
    foreach ($topicxml->clips->clip as $clip) {
      if (strcmp($clip->embeddable, "true") == 0) { ?>
        <div class="icx_clip">
          <div class="icx_clip_icon_wrapper">
            <img class="icx_clip_icon" src="<?php echo($clip->image); ?>"/>
          </div>
          <div class="icx_clip_wrapper">
            <a class="icx_clip_title" target="_blank" href="<?php echo($clip->link); ?><?php if (strcmp($clip->embeddable, "true") == 0) { ?>&wp_republish_url=<?php echo(urlencode(icopyright_server_url($_SERVER)."/wp-admin/post-new.php?icx_tag=".$clip->tag)); ?><?php } ?>"><?php echo($clip->title); ?></a>
            <?php if (strcmp($clip->embeddable, "true") == 0) { ?>
              <a class="icx_republish_btn" href="/wp-admin/post-new.php?icx_tag=<?php echo(urlencode($clip->tag)); ?>"><img src="/wp-content/plugins/copyright-licensing-tools/images/republishBtn.png"/></a>
            <?php } ?>
            <div class="icx_clear"></div>
            <div class="icx_clip_byline">
              <b><?php echo($clip->publication); ?></b>
              <?php echo($clip->pubDate);?>
            </div>
            <div class="icx_clip_body">
              <?php echo($clip->description); ?>
            </div>
          </div>
        </div>
      <?php }
    }
  } else { ?>
    <p>No articles currently match that topic.</p>
  <?php }
  exit();
}


function icopyright_includes_embeddable($clips) {
  foreach ($clips as $clip) {
    if (strcmp($clip->embeddable, "true") == 0)
      return true;
  }
  return false;
}

function icopyright_republish_page_get_edit_topic($data) {
  wp_enqueue_style('icopyright-admin-css', plugins_url('css/style.css', __FILE__), array(), '1.1.0');  // Update the version when the style changes.  Refreshes cache.
  wp_enqueue_script('icopyright-admin-js', plugins_url('js/main.js', __FILE__), array(), '1.1.0');
  $frequencies = array(
    'IMMED' => 'As Stories Break',
    'DAILY' => 'Daily',
    'WEEKLY' => 'Weekly',
    'MONTHLY' => 'Monthly',
    'NEVER' => 'Never'
  );
  ?>
  <?php if(!empty($data['error'])) { ?>
    <div class="icx_error fadeout"><p><?php echo $data['error']; ?></p></div>
  <?php } ?>
<div class="icx_republish_header">
  <h3>Edit Topic</h3>
</div>
<div class="icx_search_wrapper">
  <form id="icx_republish_form" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?page=<?php echo $_GET['page'] ?>&noheader=true">
    <input type="hidden" name="action" value="edit"/>
    <input type="hidden" name="topicId" value="<?php echo $data['topicId']; ?>"/>
    <label id="" class="icx_republish_label" for="icx_and_words">With all the words:</label><input id="icx_and_words" type="text" name="andWords" value="<?php echo $data['andWords']; ?>"/>
    <div class="icx_republish_advanced_fields" style="display: ;">
      <label class="icx_republish_label" for="icx_exact_words">With the exact phrase:</label><input id="icx_exact_words" type="text" name="exactPhrase" value="<?php echo $data['exactPhrase']; ?>"/>
      <br/>
      <label class="icx_republish_label" for="icx_or_words">With at least one of the words:</label><input id="icx_or_words" type="text" name="orWords" value="<?php echo $data['orWords']; ?>"/>
      <br/>
      <label class="icx_republish_label" for="icx_not_words">Without the words:</label><input id="icx_not_words" type="text" name="notWords" value="<?php echo $data['notWords']; ?>"/>
    </div>
    <label class="icx_republish_label" for="icx_frequency">Email me updated list:</label>
    <select name="frequency" id="icx_frequency">
      <?php foreach ($frequencies as $key => $name) { ?>
        <option value="<?php echo $key ?>"<?php if(strcmp($data['frequency'], $key) == 0){ ?> selected="selected"<?php } ?>><?php echo $name ?></option>
      <?php } ?>
    </select>
    <br/>
    <input class="icx_add_btn" type="submit" value="Save"/>
  </form>
</div>
<div class="icx_clear"></div>
<?php
}

function icopyright_republish_topic_name($topic) {
  $name = $topic->friendlyString;
  if (strlen($name) > 10) {
    $nameWords = preg_split('/\s+/', $name);
    $name = "";
    $div = "";
    foreach($nameWords as $nameWord) {
      $name .= $div . $nameWord;
      if (strlen($name)>10)
        break;
      $div = " ";
    }
  }
  return $name;
}

function icopyright_server_url($s) {
  $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
  $sp = strtolower($s['SERVER_PROTOCOL']);
  $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
  $port = $s['SERVER_PORT'];
  $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
  $host = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : $s['SERVER_NAME'];
  return $protocol . '://' . $host . $port;
}
