<?php
/**
 * @file
 * Common functions for communicating with the iCopyright servers
 *
 */

define('ICOPYRIGHT_SERVER', 'license.icopyright.net');
define('ICOPYRIGHT_PORT', 80);
define('ICOPYRIGHT_AUTH_USER', NULL);
define('ICOPYRIGHT_AUTH_PASSWORD', NULL);

/**
 * Return the iCopyright server and port that is handling the various services
 *
 * @param $secure
 *      should we go over https?
 * @return
 *      the full server specification
 */
function icopyright_get_server($secure = FALSE) {
    $server = ($secure ? 'https' : 'http') . '://' . ICOPYRIGHT_SERVER;
    if (ICOPYRIGHT_PORT != 80) {
        $server .= ':' . ICOPYRIGHT_PORT;
    }
    return $server;
}

/**
 * Given a RESTful request to add a publisher, post it to iCopyright's servers
 *
 * @param  $postdata
 *      the package of settings to post
 * @param  $useragent
 *      the type and version of the plugin doing the posting, for example
 *      "iCopyright Drupal Plugin v6.x-1.0"
 * @param  $email
 *      the email address of the user
 * @param  $password
 *      the user's iCopyright password
 * @return the response from iCopyright's servers in XML format
 */
function icopyright_post_new_publisher($postdata, $useragent, $email, $password)
{
    // Create the new publisher
    $res = icopyright_post('/api/xml/publisher/add', $postdata, $useragent);

    // If that worked, enable syndication right away
    $xml = @simplexml_load_string($res);
    $status = $xml->status['code'];
    if ($status == '200') {
        $icopyright_pubid_array = (array)$xml->publication_id;
        $pid= $icopyright_pubid_array[0];
        icopyright_post_syndication_service($pid, TRUE, $useragent, $email, $password);
    }
    return $res;
}

/**
 * Checks the response for success code. Returns true if all is OK.
 *
 * @param  $res
 *      The response from a post
 * @return
 *      TRUE if all is OK
 */
function icopyright_check_response($res) {
    $xml = @simplexml_load_string($res);
    $status = $xml->status['code'];
    return ($status == '200');
}

/**
 * Updates a publication's feed URL. The URL must have one and only one asterisk.
 *
 * @param $pid
 *      the publication ID
 * @param $value
 *      the feed URL
 * @param $useragent
 *      a user agent string
 * @param  $email
 *      the email address of the user
 * @param  $password
 *      the user's iCopyright password
 * @return
 *      the response from iCopyright's servers in XML format
 */
function icopyright_post_update_feed_url($pid, $value, $useragent, $email, $password)
{
  $url = "/api/xml/publication/update/$pid";
  $postdata = 'feed_url=' . urlencode($value);
  $res = icopyright_post($url, $postdata, $useragent, icopyright_make_header($email, $password));
  return $res;
}


/**
 * Sets EZ-Excerpt on or off for the publication
 *
 * @param  $pid
 *      the publication ID
 * @param  $value
 *      whether EZ Excerpt should be on (true) or off (false)
 * @param  $useragent
 *      a user agent string
 * @return
 *      the response from iCopyright's servers in XML format
 */
function icopyright_post_ez_excerpt($pid, $value, $useragent, $email, $password){
    $url = "/api/xml/publication/toolbar/$pid";
    $postdata = 'ez_excerpt_enabled=' . ($value == 0 ? 'false' : 'true');
    $res = icopyright_post($url, $postdata, $useragent, icopyright_make_header($email, $password));
    return $res;
}

/**
 * Enables or disables the syndication service for the publisher
 *
 * @param  $pid
 *      the publication ID
 * @param  $value
 *      whether syndication should be on (true) or off (false)
 * @param  $useragent
 *      the type and version of the plugin doing the posting, for example
 *      "iCopyright Drupal Plugin v6.x-1.0"
 * @param  $email
 *      the email address of the user
 * @param  $password
 *      the user's iCopyright password
 * @return
 *      the response from iCopyright's servers in XML format
 */
function icopyright_post_syndication_service($pid, $value, $useragent, $email, $password) {
    $url = "/api/xml/service/offer/101";
    $queryargs = array();
    array_push($queryargs, "publication=$pid");
    array_push($queryargs, 'enable=' . ($value == 0 ? 'false' : 'true'));
    $res = icopyright_post($url, join('&', $queryargs), $useragent, icopyright_make_header($email, $password));
    return $res;
}

/**
 * Given an email address and a password, create the appropriate headers for authentication to change
 * Conductor settings
 * @param  $email
 *      the email address of the user
 * @param  $password
 *      the user's iCopyright password
 * @return
 *      headers to use
 */
function icopyright_make_header($email, $password) {
    $header_encode = base64_encode("$email:$password");
    return array("Authorization: Basic $header_encode");
}

/**
 * General helper function to post RESTfully to iCopyright
 * @param $url
 *      the URL to post to
 * @param $postdata
 *      the data that we're sending up
 * @param $useragent
 *      the user agent doing the requesting -- should be the plugin and version number
 * @param $headers
 *      headers to include for authentication, if any
 * @return
 *      the results of the post in XML format
 */
function icopyright_post($url, $postdata, $useragent = NULL, $headers = NULL) {
    $rs_ch = curl_init(icopyright_get_server(TRUE) . $url);

    // If the server is locked down (for testing, for example) use auth tokens
    if ((ICOPYRIGHT_AUTH_USER != NULL) && (ICOPYRIGHT_AUTH_PASSWORD != NULL)) {
        $token = ICOPYRIGHT_AUTH_USER . ':' . ICOPYRIGHT_AUTH_PASSWORD;
        curl_setopt($rs_ch, CURLOPT_USERPWD, $token);
        curl_setopt($rs_ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    }

    curl_setopt($rs_ch, CURLOPT_POST, 1);
    curl_setopt($rs_ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($rs_ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($rs_ch, CURLOPT_HEADER, 0);
    if ($headers != NULL) {
        curl_setopt($rs_ch, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($rs_ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($rs_ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($rs_ch, CURLOPT_USERAGENT, $useragent);
    $res = curl_exec($rs_ch);
    curl_close($rs_ch);
    return str_replace('ns0:', '', $res);
}
