<?php
/*
Plugin Name: Stem Content
Plugin URI: https://github.com/jawngee/stem-content
Description: Plugin for Stem that extends with a basic framework for content blocks in WordPress.
Author: Jon Gilkison
Version: 0.3.1
Author URI: http://interfacelab.io
*/

define('STEM_CONTENT_DIR',dirname(__FILE__));
define('STEM_CONTENT_URI', plugin_dir_url(__FILE__));

if (file_exists(STEM_CONTENT_DIR.'/vendor/autoload.php')) {
	require_once STEM_CONTENT_DIR.'/vendor/autoload.php';
}

add_action('heavymetal/app/packages/install', function() {
	new \Stem\Packages\Package(STEM_CONTENT_DIR, 'Stem Content', 'Package for providing easy to use page builder content blocks.');
});