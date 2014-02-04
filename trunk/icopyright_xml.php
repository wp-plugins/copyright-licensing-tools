<?php
/**
 * Legacy feed template; simply do a permanent redirect to the new best location. New installs (v1.8 and on) will not need this.
 */
$root = dirname(dirname(dirname(dirname(__FILE__))));
if (file_exists($root . '/wp-load.php')) {
  require_once($root . '/wp-load.php');
} else {
  require_once($root . '/wp-config.php');
}

// Just redirect to the new loc
$icopyright_post_id = $_GET['id']; //requested post id
$new_location = site_url() . "/?feed=icopyright_feed&id=$icopyright_post_id";
wp_redirect( $new_location, 301 );
die();
