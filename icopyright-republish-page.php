<?php
//for logged in users
add_action('wp_ajax_repubhub_clips', 'icopyright_republish_topic_hits');
add_action('wp_ajax_repubhub_recent_headlines', 'icopyright_republish_recent_headlines');
add_action('wp_ajax_repubhub_search', 'icopyright_republish_page_search');
add_action('wp_ajax_repubhub_add_topic', 'icopyright_republish_page_post_add');
add_action('wp_ajax_repubhub_delete_topic', 'icopyright_republish_page_post_delete' );
add_action('wp_ajax_repubhub_update_global_settings', 'icopyright_update_global_settings' );
add_action('edit_form_after_title', 'icopyright_edit_form_after_title' );
add_action('wp_ajax_repubhub_dismiss_post_new_info_box', 'icopyright_repubhub_dismiss_post_new_info_box');
add_action('wp_ajax_repubhub_dismiss_save_search_info_box', 'icopyright_repubhub_dismiss_save_search_info_box');
add_action('wp_ajax_repubhub_clips_read', 'icopyright_republish_topic_read');
add_action('admin_menu', 'icopyright_post_menu');
add_action( 'admin_bar_menu', 'icopyright_admin_bar', 999 );
add_filter( 'default_content', 'icopyright_republish_content', 10, 2 );
add_filter( 'default_title', 'icopyright_republish_title', 10, 2 );

//
// Add the iCopyright republish page
//

function icopyright_post_menu() {
  add_posts_page('Republish content via iCopyright\'s repubHub', icopyright_get_republish_title(true), 'edit_posts', 'repubhub-republish', 'icopyright_republish_page');
}


function icopyright_admin_bar( $wp_admin_bar ){
  $title = icopyright_get_republish_title(false);
  $url = admin_url( 'edit.php?page=repubhub-republish');
  
  $args = array(
    'href' => $url,
    'title' => $title,
    'parent' => 'new-content', // false for a root menu, pass the ID value for a submenu of that menu.
    'id' => 'republish-1', // defaults to a sanitized title value.
    'meta' => array() // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', 'target' => '', 'title' => '' );
  );
  $wp_admin_bar->add_node( $args );

  $args = array(
    'href' => $url,
    'title' => $title,
    'parent' => false, // false for a root menu, pass the ID value for a submenu of that menu.
    'id' => 'republish-2', // defaults to a sanitized title value.
    'meta' => array() // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', 'target' => '', 'title' => '' );
  );
  $wp_admin_bar->add_node( $args );
}


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
    update_post_meta($post->ID, "icopyright_republish_content", true);
    return($content);
  }
  return $content;
}


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
  if ($_POST['action'] === "edit") {
    icopyright_republish_page_post_edit();
  }
}

function icopyright_republish_page_get($data, $topic_id = NULL) {
	if($topic_id == NULL) {
		if(is_numeric($data['topicId']))
			$topic_id = $data['topicId'];
		else
			$topic_id = $_GET['topicId'];
	}
	if (!empty($_GET['success']))
		$data['success'] = $_GET['success'];
	if ($_GET['action'] === "edit") {
		$data['topicId'] = $_GET['topicId'];
		$data['andWords'] = $_GET['andWords'];
		$data['exactPhrase'] = $_GET['exactPhrase'];
		$data['orWords'] = $_GET['orWords'];
		$data['notWords'] = $_GET['notWords'];
		$data['frequency'] = $_GET['frequency'];
		$data['featuredPublicationString'] = $_GET['featuredPublicationString'];
		$data['publicationName'] = $_GET['publicationName'];
		$data['author'] = $_GET['author'];
		if (empty($topic_id))
			icopyright_republish_page_get_edit_topic($data);
		else
			icopyright_republish_page_get_edit_topic($data, $topic_id);
	} else {
		initDisplay($data, $topic_id);
	}
}

function initDisplay($data, $topic_id) {
	if (!wp_script_is( 'icopyright-admin-js', $list = 'enqueued' ))
		wp_enqueue_script('icopyright-admin-js', plugins_url('js/main.js', __FILE__), array(), '1.5.0');
	
	if (!wp_script_is( 'icopyright-admin-css', $list = 'enqueued' ))
		wp_enqueue_style('icopyright-admin-css', plugins_url('css/style.css', __FILE__), array(), '1.4.0');  // Update the version when the style changes.  Refreshes cache.
	
	wp_enqueue_style('icopyright-admin-css-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css');
	wp_enqueue_script('icopyright-admin-js-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js');
	
	wp_localize_script( 'icopyright-admin-js', 'admin_ajax_url', array('url' => admin_url('admin-ajax.php')));
	wp_localize_script( 'icopyright-admin-js', 'icx_plugin_url', array('url' => ICOPYRIGHT_PLUGIN_URL));	
	
	echo '<div class="wrap">';
	icopyright_search_terms_section();
	if(isset($topic_id)) {
		icopyright_republish_page_get_topics($data, $topic_id);
	} else {
		icopyright_republish_page_get_topics($data, '');
	}	
	echo '</div>';
}

function icopyright_display_global_settings() {
	$frequencies = array(
			'IMMED' => 'As Stories Break',
			'DAILY' => 'Daily',
			'WEEKLY' => 'Weekly',
			'MONTHLY' => 'Monthly',
			'NEVER' => 'Never'
	);

	$user_agent = ICOPYRIGHT_USERAGENT;
	$email = get_option('icopyright_conductor_email');
	$password = get_option('icopyright_conductor_password');

	$global_settings_res = icopyright_get_global_settings($user_agent, $email, $password);
	$global_settings_xml = @simplexml_load_string($global_settings_res->response);

	$excludeAllChecked = ($global_settings_xml->excludeAll == 'true') ? 'checked' : '';
	?>
	   <div id="icx_global_settings_wrap" class="nav-tab">
	   Global Settings
	   </div>
		<div id="icx_global_settings_wrap_inner" style="display: none;">
				<div id="icx_global_settings_overlay">
					
				</div>
				<div id="icx_global_settings_message">
				</div>
	   		<form id="icx_global_settings_form" method="post" action="#">
	   		  <div class="icx_settings_title">Exclude Publications:</div>
	   		  <div class="icx_settings_desc">
	   		  If you would like to exclude certain publications from your searches and feeds when &quot;All Publications&quot;
	   		  is selected, please do so below.
	   		  
	   		  <br style="clear: both;"/><br/>
	   		  <div>
	   		  <input type="checkbox" name="icx_global_settings_checkboxes" value="excludeAll" <?php echo $excludeAllChecked; ?> />
	   		  Exclude all publications that are not a Featured Publication or included in a category<br/><br/>
	   		  </div>
	   		  <h4 class="icx_global_setting_section_title">Exclude all publications in the following categories:</h4>
	   		  <div class="icx_global_setting_section_data">
	   		  <?php 
	   		  foreach ($global_settings_xml->groupExcludes as $group_exclude) {
	   		  	$checked = ($group_exclude->checked == "true") ? 'checked' : '';
	   		  	echo '<input type="checkbox" name="icx_global_settings_checkboxes" value="' . $group_exclude->code . '" ' . $checked . '/>' . $group_exclude->display . '<br/>';
	   		  }
	   		  echo '</div>';
					echo '<br/>';
					echo '<h4 class="icx_global_setting_section_title">Exclude the following Featured Publication(s):</h4>';
					
					echo '<div class="icx_global_setting_section_data">';
					foreach ($global_settings_xml->featuredPublicationExcludes as $pub_exclude) {
						$checked = ($pub_exclude->checked == "true") ? 'checked' : '';
						echo '<input type="checkbox" name="icx_global_settings_checkboxes" value="' . $pub_exclude->code . '" ' . $checked . '/>' . $pub_exclude->display . '<br/>';
					}
					echo '</div>';
					echo '<br/>';
					echo '<h4 class="icx_global_setting_section_title">Exclude my own publication(s):</h4>';
					echo '<div class="icx_global_setting_section_data">';
					foreach ($global_settings_xml->myPublicationExcludes as $my_pub_exclude) {
						$checked = ($my_pub_exclude->checked == "true") ? 'checked' : '';
						echo '<input type="checkbox" name="icx_global_settings_checkboxes" value="' . $my_pub_exclude->code . '" ' . $checked . '/>' . $my_pub_exclude->display . '<br/>';
					}
					echo '</div>';
					echo '</div>';
					echo '<p style="margin-top: 0px; clear: both;"></p>';
					echo '<div class="icx_settings_title">Topic advisories:</div><div class="icx_settings_desc">';
					echo 'Email me clips matching my topics:&nbsp;';
					$frequencies = array(
							'IMMED' => 'As Stories Break',
							'DAILY' => 'Daily',
							'WEEKLY' => 'Weekly',
							'MONTHLY' => 'Monthly',
							'NEVER' => 'Never'
					);
					echo '<select name="frequency">';
					foreach ($frequencies as $key => $name) {
						$selectedText = $global_settings_xml->frequency == $key ? 'selected' : '';
						echo '<option value="' . $key .'" ' . $selectedText . ' >' . $name . '</option>';
					}				
					echo '</select><br/>';
					$noMatchChecked = ($global_settings_xml->noMatch == "true") ? 'checked' : '';
					echo '<input type="checkbox" name="noMatch" value="true" ' . $noMatchChecked . ' />';
	        echo 'Send the email even when there are no matching clips (so I consider refining the topic)<br/>';
	        
	        echo '<input style="margin: 20px 0 0 200px; width: 50px;" type="submit" value="Save"/>';
					echo '</div>';
	   		  ?>
	   		</form>
	   	</div> 
	   	<?php     
}

function icopyright_search_terms_section() {
	$custom_css = "
    #repubhub-logo a {
    	display: inline-block;
		  width: 114px;
		  height: 50px;
    	background-image: url('" . ICOPYRIGHT_PLUGIN_URL . "/images/ReverseLogo-small.png');
		  background-repeat: no-repeat;
			vertical-align: bottom;
		  margin-left: 50px;
    }
  ";
	wp_add_inline_style( 'icopyright-admin-css', $custom_css );
	
	$user_agent = ICOPYRIGHT_USERAGENT;
	$email = get_option('icopyright_conductor_email');
	$password = get_option('icopyright_conductor_password');
	
	$search_res = icopyright_get_search_filters($user_agent, $email, $password);
	$search_xml = @simplexml_load_string($search_res->response);
	
	?>
	  <div id="repubhub-logo">
	 		<a id="rh" href="http://repubhub.icopyright.net" target="_blank"></a>
	 		<h3>Find Republishable Articles</h3>
	 		<?php icopyright_display_global_settings(); ?>
	  </div>
		 
	  <div class="icx_search_wrapper">
	    <form id="icx_search_form" method="post" action="#">
			<input type="hidden" name="page" value="1"/>
	      
	      <select id="featuredPublicationSelect" name="featuredPublicationString" multiple="multiple">
	      <?php 
	        $selected_pub = $data['featuredPublicationString'];
	        $optGroupOpen = false;
		      foreach ($search_xml->featuredPublications as $featured_pub) {
		      	if ($featured_pub->id == 'parent') {
		      	  if ($optGroupOpen == true) {
		      	  	echo '</optgroup>';
		      	  	$optGroupOpen = false;
		      	  }
		      	  echo '<optgroup label="' . $featured_pub->display . '">';
		      	  $optGroupOpen = true;
		      	} else {
		      		$selected = ($selected_pub == $featured_pub->id) ? 'selected' : '';
			      	echo '<option value="' . $featured_pub->id . '" ' .  $selected .'>' . $featured_pub->display . '</option>';
		      	}
		      }      
	      ?>
	      </select>
	      
	      <div id="search_box">
	      	<a class="icx_republish_advanced_btn" href="">More Options</a>
	      	<input id="icx_and_words" type="text" name="andWords" placeholder="Enter your search terms here" value="<?php echo $data['andWords']; ?>">
	  		</div>
	      <select id="icx_date_filter" name="dateFilter">
	      <?php 
	        $selected_date = $data['dateFilter'];
	        if(empty($selected_date)) {$selected_date = 'TWO_DAYS';}
		      foreach ($search_xml->dateFilters as $date_filter) {
	      		$selected = ($selected_date == $date_filter->name) ? 'selected' : '';
		      	echo '<option value="' . $date_filter->name . '" ' . $selected .'>' . $date_filter->displayName . '</option>';
		      }      
	      ?>
	      </select>   
	   
	      <input class="icx_add_btn icx_submit_btn" type="submit" value="Go"/>
	      
	      <div class="icx_republish_advanced_fields_front_page" style="display: none;">
	        <label class="icx_republish_label" for="icx_exact_words">With the exact phrase:</label><input id="icx_exact_words" type="text" name="exactPhrase" value="<?php echo $data['exactPhrase']; ?>"/>
	        <!-- <span class="help">The exact phrase you enter here <b>must</b> exist in the content for it to match, for example <i>Mama's Apple Pies Inc.</i></span> -->
	        <br/>
	        <label class="icx_republish_label" for="icx_or_words">With at least one of the words:</label><input id="icx_or_words" type="text" name="orWords" value="<?php echo $data['orWords']; ?>"/>
	        <!-- <span class="help"><b>At least one</b> of these words must exist in the content for it to match. This field is used in conjunction with one or more other fields. As an example, you might enter the words <i>retail store</i> in this field.</span> -->
	        <br/>
	        <label class="icx_republish_label" for="icx_not_words">Without the words:</label><input id="icx_not_words" type="text" name="notWords" value="<?php echo $data['notWords']; ?>"/>
	        <!-- <span class="help"><b>None</b> of these words can exist in the content for it to match. This field is used in conjunction with the fields above, and is helpful for limiting a broader search. As an example, you might enter the words <i>trees orchards</i> here to avoid getting articles about apple trees and apple orchards.</span> -->
	        <br/>
	        <label class="icx_republish_label" for="author">Author:</label><input id="author" type="text" name="author" value="<?php echo $data['author']; ?>"/>
					<br/>
	        <label class="icx_republish_label" for="publicationName">Publication Name:</label><input id="publicationName" type="text" name="publicationName" value="<?php echo $data['publicationName']; ?>"/>        
	      </div>
	      
	      
	    </form>
	  </div>
	  <div class="icx_clear"></div>
	  <?php 
}

/**
 * Main entry point and renders most visual items on screen
 * @param unknown $data
 * @param string $topic_id
 * @param string $currentSearch
 */
function icopyright_republish_page_get_topics($data, $displayTopicId = '') {
	$user_agent = ICOPYRIGHT_USERAGENT;
	$email = get_option('icopyright_conductor_email');
	$password = get_option('icopyright_conductor_password');

  icopyright_calculate_unread_republish_clips();
  $unreadCounts = icopyright_get_unread_counts();

  $res = icopyright_get_topics($user_agent, $email, $password);
  $xml = @simplexml_load_string($res->response);
  
?>
	<div class="icx_wrap">
	  <?php if(!empty($data['error'])) { ?>
	    <div class="icx_error fadeout"><p><?php echo $data['error']; ?></p></div>
	  <?php } ?>
	  <?php if(!empty($data['success'])) { ?>
	    <div class="icx_success fadeout"><p><?php echo $data['success']; ?></p></div>
	  <?php } ?>	
  <div class="icon32" id="icon-page"><br></div>
  <h2 class="nav-tab-wrapper">
    <a id="icx_nav_tab_recent_headlines" class="nav-tab-recent-headlines icx_nav_tab nav-tab<?php if(empty($displayTopicId)){ ?> nav-tab-active icx-nav-tab-active<?php } ?>" href="recent_headlines">Recent Headlines</a>
    <a id="icx_nav_tab_search_results" class="nav-tab-search icx_nav_tab nav-tab" href="search_results"></a>
    
    <?php
      $index = 0;
      foreach ($xml->response as $topic) {
        ?>
          <a id="icx_nav_tab_<?php echo $topic->id; ?>" class="icx_nav_tab nav-tab<?php if(!empty($displayTopicId) &&  $displayTopicId == $topic->id){ ?> nav-tab-active icx-nav-tab-active<?php } ?>" href="<?php echo $topic->id; ?>"><?php echo icopyright_republish_topic_name($topic, $unreadCounts); ?></a>
        <?php
        $index ++;
      }
    ?>
  </h2>
  <div id="icx_topic_recent_headlines" class="icx_topic" style="display: <?php if(!empty($displayTopicId)){ ?>none<?php } ?>;">
    <img id="rh-spinner" src="<?php print plugin_dir_url(__FILE__) ?>images/animated-spinner.gif">
    <div class="icx_repubhub_clips" id="icx_clips_for_topic_0" data-loc="recent_headlines" data-topicid="recent_headlines">
    </div>
  </div>
  <div class="icx_clear"></div>
  
  <div id="icx_topic_search_results" class="icx_topic" style="display: none;">
		<img id="icx_search_results_spinner" src="<?php print plugin_dir_url(__FILE__) ?>images/animated-spinner.gif">
		<div id="icx_topic_search_results_inner"></div>
  </div>
  <div class="icx_clear"></div>
    
<?php
  $index = 0;
  foreach ($xml->response as $topic) {
  	$active = !empty($displayTopicId) && $displayTopicId == $topic->id;
		icopyright_search_topic_display(NULL, $topic, $active, FALSE);
?>
  <div class="icx_clear"></div>
<?php
    $index ++;
  }
  if(isset($displayTopicId)) { ?>
    <script type="application/javascript">
      jQuery("#icx_topic_<?php print $displayTopicId; ?>").trigger("click");
    </script>
  <?php
  }
?>
	</div>
<?php
}


function icopyright_republish_page_post_add() {
	$formData = $_POST['formValues'];
	$formData = explode("&",$_POST['formValues']);
	$post = array();
	foreach($formData as $val) {
		$keyVal = explode("=", $val);
		$val = urldecode(urldecode($keyVal[1])); // decode twice because Dallas Cowboys turns into Dallas+Cowboys, which turns into Dallas%2BCowboys
		
		$post[$keyVal[0]] = sanitize_text_field(stripslashes($val));
	}
			
	
	// Validate params.
	if (empty($post['andWords']) && empty($post['exactPhrase']) &&
			empty($post['orWords']) && empty($post['notWords']) && empty($post['author'])
			&& empty($post['publicationName']) && empty($post['featuredPublicationString'])) {
				echo "<p>Please provide search values.</p>";
				exit();
	}
	
	// Call WS
	$user_agent = ICOPYRIGHT_USERAGENT;
	$email = get_option('icopyright_conductor_email');
	$password = get_option('icopyright_conductor_password');
	$res = icopyright_add_topic(http_build_query($post), $user_agent, $email, $password);

	$topic = @simplexml_load_string($res->response);
	 if(!icopyright_check_response($res)) {
	 if (is_object($topic) && ($topic->status->messages->count() > 0)) {
	 $post['error'] = (string)$topic->status->messages[0]->message;
	 } else {
	 $post['error'] = 'Sorry, we were unable to add that topic.';
	 }
	 } else {
	 $post = array();
	 $post['success'] = "Topic has been added.";
	 $post['topicId'] = (string)$topic->id;
	}
	
	//echo$post;
	$_GET['page'] = 'repubhub-republish';
	icopyright_republish_page_get_topics($post, $post['topicId']);
	exit();
}

function icopyright_republish_page_post_edit() {
  // Get values
  $post = array();

  foreach($_POST as $key => $value) {
  	if ($key == 'featuredPublicationString') {
  		$post['featuredPublicationString'] = implode(",", $value);
  	} else {
  		$post[$key] = sanitize_text_field(stripslashes($value));
  	}  	
  }  

  // Validate params.
  if (empty($post['andWords']) && empty($post['exactPhrase']) &&
    empty($post['orWords']) && empty($post['notWords']) &&
  	empty($post['featuredPublicationString']) && empty($post['author']) &&
  		empty($post['publicationName'])) {
    $post['error'] = "Please provide search values.";
    icopyright_republish_page_get_edit_topic($post);
    return;
  }

  // Call WS
  $user_agent = ICOPYRIGHT_USERAGENT;
  $email = get_option('icopyright_conductor_email');
  $password = get_option('icopyright_conductor_password');
  $res = icopyright_edit_topic($post['topicId'], http_build_query($post), $user_agent, $email, $password);
  if(!icopyright_check_response($res)) {
    $post['error'] = "Unable to edit topic at this time.  Please try again later.";
    icopyright_republish_page_get_edit_topic($post, $post['topicId']);
  } else {
    $results = array();
    $results['success'] = "Topic has been modified.";
    $xml = @simplexml_load_string($res->response);
    $tid = (string)$xml->id;
    icopyright_republish_page_get($results, $tid);
  }
}

function icopyright_republish_page_post_delete() {
  $user_agent = ICOPYRIGHT_USERAGENT;
  $email = get_option('icopyright_conductor_email');
  $password = get_option('icopyright_conductor_password');
  $res = icopyright_delete_topic($_POST['topicId'], $user_agent, $email, $password);
  if(!icopyright_check_response($res)) {
  	echo 'error';
    //$post['error'] = "Unable to delete topic at this time.  Please try again later.";
  } else {
  	echo 'success';
    //$post['success'] = "Topic has been deleted.";
  }
  exit();
}



function icopyright_search_topic_display($response, $obj, $active, $isSearch = FALSE) {
	$id = $isSearch ? 'search_results' : $obj->id;
	$action = $isSearch ? 'addTopic' : 'edit';
	$submitText = $isSearch ? 'Save Search' : 'Edit Topic';
  $method = $isSearch ? 'post' : 'get';
  $formLocation = '';
  $formId = $isSearch ? 'icx_form_save_search' : 'icx_form_edit_topic';
  $hideBtn = ($isSearch && $obj->hideFeed == "true");
  $frequencies = array(
  		'IMMED' => 'As Stories Break',
  		'DAILY' => 'Daily',
  		'WEEKLY' => 'Weekly',
  		'MONTHLY' => 'Monthly',
  		'NEVER' => 'Never'
  );
	if(!$isSearch) {
?>	
		<div id="icx_topic_<?php echo $id; ?>" class="icx_topic" style="display: <?php if($active == FALSE){ ?>none<?php } ?>;">
		
<?php }?>
	<div class="icx_topic_title">
	      <?php 
				if($isSearch && get_option("repubhub_dismiss_save_search_info_box") == null) {
				    ?>
				      <p style="float:left; background:lightblue; padding:10px; margin: 0 0 20px 0;" id="icx_save_hint_info_box">
								Tip: Click <em>Save Search</em> to automatically see new articles on this topic next time you visit. Saving a search also allows you to set up email alerts when new articles on this topic come in. 
				        <br/>
				        <a style="float: right;" href="#" id="icx_dismiss_save_hint_info_box">Dismiss</a>
				      </p>
				      <div style="clear: both;"></div>
				    <?php
				    }
	      
				if (!$isSearch) {
		      if (!empty($obj->andWords)) { 
		        echo "With all the words: <strong>" . $obj->andWords . "</strong><br/>";
		       } ?>
		      <?php if (!empty($obj->exactPhrase)) { ?>
		        With the exact phrase: <strong><?php echo($obj->exactPhrase); ?></strong><br/>
		      <?php } ?>
		      <?php if (!empty($obj->orWords)) { ?>
		        With at least one of the words: <strong><?php echo($obj->orWords); ?></strong><br/>
		      <?php } ?>
		      <?php if (!empty($obj->notWords)) { ?>
		        Without the words: <strong><?php echo($obj->notWords); ?></strong><br/>
		      <?php }
	
		      if (!empty($obj->publicationOrGroupName)) {
		      	echo "Publication(s): <strong>" . $obj->publicationOrGroupName . "</strong><br/>";
		      }
		      if (!empty($obj->author)) {
		      	echo "Author: <strong>" . str_replace("+", " ", $obj->author) . "</strong><br/>";
		      }
		      if (!empty($obj->dateFilter)) {
		      	echo "Date range: <strong>" . str_replace("_", " ", $obj->dateFilter) . "</strong><br/>";
		      }	      
		      
		      ?>
		      Email me: <strong><?php echo($frequencies[$obj->frequency.""]); ?></strong><br/>
	<?php }?>	      
	      
	    </div>
		  
		  <?php 
		  if(!$hideBtn) {
		  ?>
		    <div class="icx_topic_controls">
		      <form id="<?php echo $formId; ?>" method="<?php echo $method; ?>" action="<?php echo $formLocation; ?>">
		        <input type="hidden" name="action" value="<?php echo $action; ?>"/>
		        <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>"/>
		        <input type="hidden" name="topicId" value="<?php echo $obj->id; ?>"/>
		        <input type="hidden" name="andWords" value="<?php echo $obj->andWords; ?>"/>
		        <input type="hidden" name="exactPhrase" value="<?php echo $obj->exactPhrase; ?>"/>
		        <input type="hidden" name="orWords" value="<?php echo $obj->orWords; ?>"/>
		        <input type="hidden" name="notWords" value="<?php echo $obj->notWords; ?>"/>
		        <input type="hidden" name="frequency" value="<?php echo $obj->frequency; ?>"/>
		        <input type="hidden" name="featuredPublicationString" value="<?php if(!empty($obj->featuredPublicationString)) echo $obj->featuredPublicationString; ?>"/>
		        <input type="hidden" name="author" value="<?php if(!empty($obj->author)) echo $obj->author; ?>"/>
		        <input type="hidden" name="publicationName" value="<?php if(!empty($obj->publicationName)) echo $obj->publicationName; ?>"/>
		        <input type="hidden" name="dateFilter" value=""/>
		        <input class="icx_submit_btn" type="submit" value="<?php echo $submitText ?>"/>
		      </form>
		    </div>
		  <?php 
		  }
		  ?>
	    <div class="icx_clear"></div>
	    <?php
	    if (!$isSearch) {
	    ?>
		    <div class="icx_repubhub_clips" id="icx_clips_for_topic_<?php print $obj->id ?>" data-loc="<?php print $obj->xmlLocation ?>" data-topicid="<?php print $obj->id ?>">
		      <img src="<?php print plugin_dir_url(__FILE__) ?>images/animated-spinner.gif">
		    </div>
		  <?php 
	    } else {
	    	icopyright_display_solr_content($response, $obj->content, FALSE);
	    	
	    	$pageCount = (int)$obj->pageCount;
	    	$page = (int)$obj->page;

	    	echo '<p></p>';
	    	if ($page > 1) {
	    		echo "<a style=\"float: left; text-decoration: none;\"class=\"icx_pager\" href =\"prev\">";
	    		echo "<img src=\"" . icopyright_static_server() . "/portal/images/repubhub_prev.png\" alt=\"Previous\"/>";
	    		echo "</a>";
	    	}	    	
	    	
	    	if ($page < $pageCount) {
	    		echo "<a style=\"float: right; text-decoration: none;\"class=\"icx_pager\" href =\"next\">";
	    		echo "<img src=\"" . icopyright_static_server() . "/portal/images/repubhub_next.png\" alt=\"Next\"/>";
	    		echo "</a>";
	    	}
	    	
	    	if ($pageCount > 0) {
	    		echo "<p style=\"width: 100%; text-align: center; padding-top: 4px;\">Page " . $page . " of " . $pageCount . "</p>";
	    	}	    	
	    	
	    	exit();
	    }  
	    
	    if(!$isSearch) {
	    ?>
	  	</div>
	  	<?php }?>	
<?php 
 	  
}

/**
 * When a topic is read, decrease the "unread" numbers for the total and set this one to zero
 */

function icopyright_republish_topic_read() {
  $topicId = (int) $_GET['topicid'];
  $total = icopyright_update_unread_count($topicId);
  $unreadMarkers = icopyright_get_unread_markers();
  $unreadMarkers[$topicId] = (int) $_GET['contentid'];
  update_option('icopyright_unread_republish_markers_' . get_option('icopyright_pub_id'), json_encode($unreadMarkers));
  echo $total;
  exit();
}

/**
 * Given an XML location, returns HTML for the hits. Used in an AJAX call to fill out the page
 */
function icopyright_republish_recent_headlines() {
  $user_agent = ICOPYRIGHT_USERAGENT;
  $email = get_option('icopyright_conductor_email');
  $password = get_option('icopyright_conductor_password');
  
  $page = (empty($_GET['rhPage'])) ? '1' : $_GET['rhPage'];
  $res = icopyright_get_recent_headlines($user_agent, $email, $password, $page);
  $topicxml = @simplexml_load_string($res->response);
  
  icopyright_display_solr_content($res, $topicxml->content, TRUE, $topicxml->page);
}

/**
 * Updates global settings after a "save"
 */
function icopyright_update_global_settings() {
	// Get values
	$formData = $_POST['formValues'];
	$formData = explode("&",$_POST['formValues']);
	
	//icx_global_settings_checkboxes=g-6+&icx_global_settings_checkboxes=p-13278+&icx_global_settings_checkboxes=p-13280+
	$post = array();
	$excludes = '';
	foreach($formData as $val) {
		$keyVal = explode("=", $val);
		if($keyVal[0] == "icx_global_settings_checkboxes") {
			$excludes = $excludes . str_replace("+", "", sanitize_text_field(stripslashes($keyVal[1]))) . ',';
		}
		else {
			$post[$keyVal[0]] = sanitize_text_field(stripslashes($keyVal[1]));
		}
	}
	
	$post['excludes'] = $excludes;
	
	$user_agent = ICOPYRIGHT_USERAGENT;
	$email = get_option('icopyright_conductor_email');
	$password = get_option('icopyright_conductor_password');
	$res = icopyright_post_global_settings($user_agent, $post, $email, $password);

	if((strlen($res->http_code) > 0) && ($res->http_code != '200')) {
		echo "<p>" . $errorMessage . " (" . $res->http_code . ': ' . $res->http_expl . ")</p>";
		if ($res->http_code == 401) {
			echo '<p>Your email address and password don\'t match a valid account in Conductor. Please visit the ' .
					'<a href="' . $adminUrl . 'options-general.php?page=copyright-licensing-tools#advanced">iCopyright settings page</a> and ' .
					'push <em>Show Advanced Settings</em> to check your Conductor email address and password.</p>';
		}
		exit();
	}
	
	$res = @simplexml_load_string($res->response);
	$attributes = $res->status->attributes();
	$code = $attributes['code'];
	$message = $res->status->messages->message;
	
	if ($code == 200 || $code == '200') {
		echo '<div class="icx_success fadeout"><p>' . $message . '</p></div>';
	} else {
		echo '<div class="icx_error fadeout"><p>' . $message . '</p></div>';
	}
		
	exit();
}

/**
 * Does an ajax search after inputting terms and hitting "go" 
 */
function icopyright_republish_page_search() {
	// Get values
	$formData = $_POST['formValues'];
	$formData = explode("&",$_POST['formValues']);
	
	$post = array();
	$featuredPublicationString = '';
	foreach($formData as $val) {
		$keyVal = explode("=", $val);
		
		if ($keyVal[0] == 'featuredPublicationString') {
			$featuredPublicationString .= $keyVal[1] . ',';
		} else { 
			$post[$keyVal[0]] = sanitize_text_field(stripslashes($keyVal[1]));
		}
	}
	$post['featuredPublicationString'] = $featuredPublicationString;

	// Validate params.
	if (empty($post['andWords']) && empty($post['exactPhrase']) &&
			empty($post['orWords']) && empty($post['notWords']) && empty($post['author'])
			&& empty($post['publicationName']) && empty($post['featuredPublicationString'])) {
			echo "<p>Please provide search values.</p>";
			exit();
	}
	
	// Call WS
	$user_agent = ICOPYRIGHT_USERAGENT;
	$email = get_option('icopyright_conductor_email');
	$password = get_option('icopyright_conductor_password');
	$res = icopyright_search(http_build_query($post), $user_agent, $email, $password);
	$search = @simplexml_load_string($res->response);
	
	if (is_object($search) && ($search->status->messages->count() > 0)) {
		echo '<p style="color: red;">' . (string)$search->status->messages[0]->message . '</p>'; 
	} else {
		icopyright_search_topic_display($res, $search, TRUE, TRUE);
	}
	
	exit();
}

/**
 * Used to display recent headlines and search results
 * @param unknown $res
 */
function icopyright_display_solr_content($res, $contentList, $isRecentHeadlines, $rhPage = 1) {
	$adminUrl = admin_url();
	$pluginsUrl = plugins_url();
	$errorMessage = $isRecentHeadlines ? "Failed to get recent headlines" : "Failed to do search";
	if((strlen($res->http_code) > 0) && ($res->http_code != '200')) {
		echo "<p>" . $errorMessage . " (" . $res->http_code . ': ' . $res->http_expl . ")</p>";
		if ($res->http_code == 401) {
			echo '<p>Your email address and password don\'t match a valid account in Conductor. Please visit the ' .
					'<a href="' . $adminUrl . 'options-general.php?page=copyright-licensing-tools#advanced">iCopyright settings page</a> and ' .
					'push <em>Show Advanced Settings</em> to check your Conductor email address and password.</p>';
		}
		exit();
	}	
	
	if (sizeof($contentList) > 0 && icopyright_includes_embeddable($contentList)) {
		$firstClipId = -1;
		foreach ($contentList as $clip) {
			if (strcmp($clip->embeddable, "true") == 0) {
				$clipId = (int) $clip->clipId;
				if ($clipId > $firstClipId) {
					$firstClipId = $clipId;
	        }?>
	        <div class="icx_clip">
	          <div class="icx_clip_icon_wrapper">
	            <img class="icx_clip_icon" src="<?php echo($clip->image); ?>"/>
	          </div>
	          <div class="icx_clip_wrapper">
	            <a class="icx_clip_title" target="_blank" href="<?php echo($clip->link); ?><?php if (strcmp($clip->embeddable, "true") == 0) { ?>&wp_republish_url=<?php echo(urlencode($adminUrl . "post-new.php?icx_tag=".$clip->tag)); ?><?php } ?>"><?php echo($clip->title); ?></a>
	            <?php if (strcmp($clip->embeddable, "true") == 0) { ?>
	              <a class="icx_republish_btn" target="_blank" href="<?php echo($adminUrl); ?>post-new.php?icx_tag=<?php echo(urlencode($clip->tag)); ?>"><img src="<?php echo($pluginsUrl); ?>/copyright-licensing-tools/images/republishBtn.png"/></a>
	            <?php } ?>
	            <div class="icx_clear"></div>
	            <div class="icx_clip_byline">
	              <?php if(!empty($clip->author)) echo "<a class=\"icx_clip_author\" href=\"" . $clip->author . "\">By " . $clip->author . "</a> &mdash;"; ?>
	              <?php echo($clip->pubDate);?>
	            </div>
	            <div class="icx_clip_body">
	            	<b>
	            	<?php 

	            		echo "<a class=\"icx_clip_publicationName\" href=\"" . $clip->publication . "\">" . $clip->publication . "</a>";
	            		?>
	             </b> 
	              <?php echo($clip->description); ?>
	            </div>
	          </div>
	        </div>
	      <?php }
	    }
	    ?>
	  <?php
	  } else { ?>
	    <p>No articles currently match that topic.</p>
	  <?php }
	  	
	if ($isRecentHeadlines) {
		
		echo '<p></p>';
		if ($rhPage > 1) {
			echo "<a style=\"display: inline-block; float: left; text-decoration: none;\"class=\"icx_pager_rh\" href =\"" . ($rhPage - 1) . "\">";
			echo "<img src=\"" . icopyright_static_server() . "/portal/images/repubhub_prev.png\" alt=\"Previous\"/>";
			echo "</a>";
		}
		
		echo "<a style=\"display: inline-block; float: right; text-decoration: none;\"class=\"icx_pager_rh\" href =\"" . ($rhPage + 1) . "\">";
		echo "<img src=\"" . icopyright_static_server() . "/portal/images/repubhub_next.png\" alt=\"Next\"/>";
		echo "</a>";
		
		exit();
	}
}

/**
 * Given an XML location, returns HTML for the hits. Used in an AJAX call to fill out the page
 */
function icopyright_republish_topic_hits() {
  $xml_location = $_GET['loc'];
  $topicId = $_GET['topicid'];
  $unreadMarkers = icopyright_get_unread_markers();
  $lastReadclipId = 0;
	$adminUrl = admin_url();
  $pluginsUrl = plugins_url();
  if (array_key_exists((int)$topicId, $unreadMarkers)) {
    $lastReadclipId = (int) $unreadMarkers[(int)$topicId];
  }
  $user_agent = ICOPYRIGHT_USERAGENT;
  $email = get_option('icopyright_conductor_email');
  $password = get_option('icopyright_conductor_password');
  $res = icopyright_get_topic(str_replace("http://".ICOPYRIGHT_SERVER, "", $xml_location), $user_agent, $email, $password);
  if((strlen($res->http_code) > 0) && ($res->http_code != '200')) {
    echo "<p>Failed to get topic clips (" . $res->http_code . ': ' . $res->http_expl . ")</p>";
    if ($res->http_code == 401) {
      echo '<p>Your email address and password don\'t match a valid account in Conductor. Please visit the ' .
        '<a href="' . $adminUrl . 'options-general.php?page=copyright-licensing-tools#advanced">iCopyright settings page</a> and ' .
        'push <em>Show Advanced Settings</em> to check your Conductor email address and password.</p>';
    }
    exit;
  }
  $topicxml = @simplexml_load_string($res->response);
  if (sizeof($topicxml->clips->clip) > 0 && icopyright_includes_embeddable($topicxml->clips->clip)) {
    $firstClipId = -1;
    foreach ($topicxml->clips->clip as $clip) {
      if (strcmp($clip->embeddable, "true") == 0) {
        $clipId = (int) $clip->clipId;
        if ($clipId > $firstClipId) {
          $firstClipId = $clipId;
        }?>
        <div class="icx_clip">
          <div class="icx_clip_icon_wrapper">
            <img class="icx_clip_icon" src="<?php echo($clip->image); ?>"/>
          </div>
          <div class="icx_clip_wrapper">
            <a class="icx_clip_title <?php if ($clipId>$lastReadclipId) { ?>icx_unread_title<?php } ?>" target="_blank" href="<?php echo($clip->link); ?><?php if (strcmp($clip->embeddable, "true") == 0) { ?>&wp_republish_url=<?php echo(urlencode($adminUrl . "post-new.php?icx_tag=".$clip->tag)); ?><?php } ?>"><?php echo($clip->title); ?></a>
            <?php if (strcmp($clip->embeddable, "true") == 0) { ?>
              <a class="icx_republish_btn" href="<?php echo($adminUrl); ?>/post-new.php?icx_tag=<?php echo(urlencode($clip->tag)); ?>"><img src="<?php echo($pluginsUrl); ?>/copyright-licensing-tools/images/republishBtn.png"/></a>
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
    ?>
    <div id="icx_topic_<?php echo $topicId; ?>_first_clip_id" style="display:none;"><?php echo $firstClipId; ?></div>
    <?php
  } else { ?>
    <p>No articles currently match that topic.</p>
  <?php }
  exit();
}

/**
 * When a topic is "read" by the user clicking on the tab, set the number of unread stories to be zero and update the
 * totals for all topics accordingly
 * @param $topicId
 * @return int
 */
function icopyright_update_unread_count($topicId) {
  $unreadCounts = icopyright_get_unread_counts();
  $topicCount = 0;
  if (array_key_exists($topicId, $unreadCounts)) {
    $topicCount = $unreadCounts[$topicId];
  }
  $total = 0;
  if (array_key_exists('total', $unreadCounts))
    $total = $unreadCounts['total'];
  $total = $total - $topicCount;
  if ($total<0) $total = 0;
  $unreadCounts['total'] = $total;
  $unreadCounts[$topicId] = 0;
  update_option('icopyright_unread_republish_clips_' . get_option('icopyright_pub_id'), json_encode($unreadCounts));
  return $total;
}


function icopyright_includes_embeddable($clips) {
  foreach ($clips as $clip) {
    if (strcmp($clip->embeddable, "true") == 0)
      return true;
  }
  return false;
}

function icopyright_republish_page_get_edit_topic($data, $displayTopicId = '') {
	$user_agent = ICOPYRIGHT_USERAGENT;
	$email = get_option('icopyright_conductor_email');
	$password = get_option('icopyright_conductor_password');
	
	
	$search_res = icopyright_get_search_filters($user_agent, $email, $password);
	$search_xml = @simplexml_load_string($search_res->response);
		
  wp_enqueue_style('icopyright-admin-css', plugins_url('css/style.css', __FILE__), array(), '1.4.0');  // Update the version when the style changes.  Refreshes cache.
  wp_enqueue_script('icopyright-admin-js', plugins_url('js/main.js', __FILE__), array(), '1.5.0');
  wp_localize_script( 'icopyright-admin-js', 'admin_ajax_url', array('url' => admin_url('admin-ajax.php')));
  
  wp_enqueue_style('icopyright-admin-css-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css');
  wp_enqueue_script('icopyright-admin-js-select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js');
    
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
  <form id="icx_republish_form" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?page=<?php echo $_GET['page'] ?>">
    <input type="hidden" name="action" value="edit"/>
    <input type="hidden" name="topicId" value="<?php echo $data['topicId']; ?>"/>
    <label id="" class="icx_republish_label" for="icx_and_words">With all the words:</label><input id="icx_and_words" type="text" name="andWords" value="<?php echo $data['andWords']; ?>"/>
   
    <div class="icx_republish_advanced_fields" style="display: ;">
    
      <label class="icx_republish_label" for="icx_exact_words">With the exact phrase:</label><input id="icx_exact_words" type="text" name="exactPhrase" value="<?php echo $data['exactPhrase']; ?>"/>
      <br/>
      <label class="icx_republish_label" for="icx_or_words">With at least one of the words:</label><input id="icx_or_words" type="text" name="orWords" value="<?php echo $data['orWords']; ?>"/>
      <br/>
      <label class="icx_republish_label" for="icx_not_words">Without the words:</label><input id="icx_not_words" type="text" name="notWords" value="<?php echo $data['notWords']; ?>"/>
			<br/>
      <label class="icx_republish_label" for="icx_author">Author:</label><input id="icx_author" type="text" name="author" value="<?php echo $data['author']; ?>"/>      
      <br/>
      <label class="icx_republish_label" for="icx_publication_name">Publication Name:</label><input id="icx_publication_name" type="text" name="publicationName" value="<?php echo $data['publicationName']; ?>"/>
    	 <br/>
    	<label class="icx_republish_label" for="featuredPublicationString">Publication/Group:</label> 
      <select id="featuredPublicationSelectEdit" name="featuredPublicationString[]" multiple="multiple">
      <?php 
        $selected_pubs = $data['featuredPublicationString'];
        if (!empty ($selected_pubs)) {
        	$selected_pubs = explode(",", $selected_pubs);
        }
        $optGroupOpen = false;
	      foreach ($search_xml->featuredPublications as $featured_pub) {
	      	if ($featured_pub->id == 'parent') {
	      	  if ($optGroupOpen == true) {
	      	  	echo '</optgroup>';
	      	  	$optGroupOpen = false;
	      	  }
	      	  echo '<optgroup label="' . $featured_pub->display . '">';
	      	  $optGroupOpen = true;
	      	} else {
	      		$selected = (!empty($selected_pubs) && in_array($featured_pub->id, $selected_pubs)) ? 'selected' : '';
		      	echo '<option value="' . $featured_pub->id . '" ' .  $selected .'>' . $featured_pub->display . '</option>';
	      	}
	      }      
      ?>
      </select>       
    </div>
    <label class="icx_republish_label" for="icx_frequency">Email me updated list:</label>
    <select name="frequency" id="icx_frequency">
      <?php foreach ($frequencies as $key => $name) { ?>
        <option value="<?php echo $key ?>"<?php if(strcmp($data['frequency'], $key) == 0){ ?> selected="selected"<?php } ?>><?php echo $name ?></option>
      <?php } ?>
    </select>
    <br/>
    <input class="icx_save_edit_btn" type="submit" value="Save"/>
  </form>
</div>
<div class="icx_clear"></div>
<?php
}

function icopyright_republish_has_unread($topic, $unreadCounts) {
  $topicId = (int)$topic->id;
  return array_key_exists($topicId, $unreadCounts) && $unreadCounts[$topicId]>0;
}

function icopyright_republish_topic_name($topic, $unreadCounts) {
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
  
  if (isset($topic->id)) {
	  $topicId = (int)$topic->id;
	  if (array_key_exists($topicId, $unreadCounts) && $unreadCounts[$topicId]>0)
	    $name .= '<span class="icx_unread_tab_count"><span class="icx_unread update-plugins count-1"><span class="plugin-count">'.$unreadCounts[$topicId].'</span></span></span>';
	    //$name .= ' <span>'.$unreadCounts[$topicId].'</span>';
	    
	  $name .= '<div class="icx_topic_delete_btn" data-topicid="' . $topic->id . '">X</div>';
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

function icopyright_calculate_unread_republish_clips() {
  //
  // Only do this every 30 mins
  //
  $prevUpdate = get_option('icopyright_update_unread_republish_time');
  if (empty($prevUpdate)) $prevUpdate = 0;
  $now = time();
  if (($now - $prevUpdate) < (60*30))
    return;

  //
  // Fetch all topics and clips.  Calculate the unread clips per topic.
  //
  update_option('icopyright_update_unread_republish_time', time());

  $totalUnreadCount = 0;
  $unreadCounts = array();
  $unreadMarkers = icopyright_get_unread_markers();

  $user_agent = ICOPYRIGHT_USERAGENT;
  $email = get_option('icopyright_conductor_email');
  $password = get_option('icopyright_conductor_password');
  $res = icopyright_get_topics($user_agent, $email, $password);

  $xml = @simplexml_load_string($res->response);
  if (sizeof($xml->response)>0) {
    foreach ($xml->response as $topic) {
      $res = icopyright_get_topic(str_replace("http://".ICOPYRIGHT_SERVER, "", $topic->xmlLocation), $user_agent, $email, $password);
      $topicxml = @simplexml_load_string($res->response);
      $unreadCount = 0;
      if (sizeof($topicxml->clips->clip) > 0 && icopyright_includes_embeddable($topicxml->clips->clip)) {
        $lastReadclipId = 0;
        if (array_key_exists((int)$topic->id, $unreadMarkers)) {
          $lastReadclipId = (int) $unreadMarkers[(int)$topic->id];
        }
        foreach ($topicxml->clips->clip as $clip) {
          if (strcmp($clip->embeddable, "true") == 0 && (int) $clip->clipId > $lastReadclipId) {
            $unreadCount ++;
          }
        }
      }
      $unreadCounts[(int)$topic->id] = $unreadCount;
      $totalUnreadCount += $unreadCount;
    }
  }
  $unreadCounts["total"] = $totalUnreadCount;
  update_option('icopyright_unread_republish_clips_' . get_option('icopyright_pub_id'), json_encode($unreadCounts));
}

function icopyright_get_unread_counts() {
  $unreadCounts = array();
  try {
    $unreadJson = get_option('icopyright_unread_republish_clips_' . get_option('icopyright_pub_id'));
    if (!empty($unreadJson))
      $unreadCounts = json_decode($unreadJson, true);
  } catch (Exception $e) {}
  return $unreadCounts;
}

function icopyright_get_unread_markers() {
  $unreadMarkers = array();
  try {
    $markersJson = get_option('icopyright_unread_republish_markers_' . get_option('icopyright_pub_id'));
    if (!empty($markersJson))
      $unreadMarkers = json_decode($markersJson, true);
  } catch (Exception $e) {}
  return $unreadMarkers;
}

function icopyright_get_republish_title($circleStyle) {
  icopyright_calculate_unread_republish_clips();
  $unreadCounts = icopyright_get_unread_counts();
  $title = "Republish";
  if (array_key_exists('total', $unreadCounts) && $unreadCounts['total']>0) {
    if ($circleStyle)
      $title .= '<span class="icx_unread update-plugins count-1"><span class="plugin-count">'.$unreadCounts['total'].'</span></span>';
    else
      $title .= '&nbsp;'.$unreadCounts['total'];
  }
  return $title;
}

function icopyright_edit_form_after_title() {
	if (!empty($_GET['icx_tag']) || (!empty($_GET['post']) && get_post_meta($_GET['post'], "icopyright_republish_content"))) {
		if(get_option("repubhub_dismiss_post_new_info_box") == null) {
			$adminAjaxUrl = admin_url('admin-ajax.php');
			$dataLoc = admin_url('edit.php?page=repubhub-republish');
			?>
      <p style="float:left; background:lightblue; padding:10px; margin: 0 0 20px 0;" id="icx_post_new_info_box">
			The embed code in the text editor will display the republished article. 
			To preview the article, be sure to click &quot;Save Draft&quot; first, 
			and then &quot;View Post&quot; at top (since clicking Preview will not work in some browsers). 
			You may add an intro or conclusion above or below the embed code in the text editor.
        <br/>
        <a style="float: right;" href="" id="icx_dismiss_post_new_info_box">Dismiss</a>
      </p>
      <div style="clear: both;"></div>
      <script type="text/javascript">
        jQuery(document).ready(function () {
          jQuery("#icx_dismiss_post_new_info_box").click(function (event) {
            jQuery("#icx_post_new_info_box").hide();
            jQuery.ajax({
              url : '<?php echo $adminAjaxUrl;?>',
              type : "get",
              data : {action: "repubhub_dismiss_post_new_info_box", loc: '<?php echo $dataLoc;?>'},
              success: function() {}
            });
            event.preventDefault();
          });
        });
      </script>
    <?php
    }
  ?>
  <p style="float:left; background:lightblue; padding:10px; margin: 0 0 20px 0;" id="icx_terms_of_use_box">
    By clicking "Publish" you agree to the
  <a target="_blank" href="<?php print icopyright_get_server() ?>/rights/termsOfUse.act?sid=15&tag=<?php print urlencode($_GET['icx_tag']) ?>">terms of use</a>.
      </p>
      <div style="clear: both;"></div>
  <?php
  }
}


function icopyright_repubhub_dismiss_post_new_info_box() {
  update_option("repubhub_dismiss_post_new_info_box", "true");
}


function icopyright_repubhub_dismiss_save_search_info_box() {
	update_option("repubhub_dismiss_save_search_info_box", "true");
}
