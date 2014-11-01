<?php if (!defined('ABSPATH')) exit('No direct script access allowed');

/**
 * Plugin Name: OB Relative URLs
 * Description: Uses the PHP output buffer to convert all URLs to a relative format when ?relative=true.
 * Version: 1.0
 * Author: Chris Frazier
 * Author URI: http://chrisfrazier.me
 */


/**********************************************************************
BEGIN MODIFICATIONS HERE
***********************************************************************
Add any additional domains, other than the home_url() that should
also be converted to a relative path. For example:
$ob_rel_additional = array(
    'http://google.com',
    'http://yahoo.com',
);
**********************************************************************/

$ob_rel_additional = array(
);

/**********************************************************************
STOP MODIFICATIONS - DO NOT EDIT PAST THIS LINE
**********************************************************************/


add_action('wp', 'ob_rel_urls_start');

function ob_rel_urls_start()
{
    if (is_feed() || !isset($_GET['_relative'])) return;
    ob_start('ob_rel_urls_buffer');
}

function ob_rel_urls_buffer($html)
{
    $ob_rel_additional[] = home_url();
    foreach ($ob_rel_additional as $url) {
        // Match any href="", src="", or url() attributes:
        // (href=|src=|url\()['"]?http://www\.example\.com/?['"\)]?
        $name = str_replace('.', '\.', $url);
        $pattern = '#(href=|src=|url\()(['.chr(39).'"]?)('.$name.'/?)(['.chr(39).'"\)]?)#i';
        $replace = '$1$2/$4';
        $html = preg_replace($pattern, $replace, $html);
    }
    return $html;
}