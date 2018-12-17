<?php
/*
Plugin Name: Content plugin for ILAB Stem App Framework
Plugin URI: https://github.com/jawngee/stem-content
Description: Plugin for Stem that extends with a basic framework for content blocks in WordPress.
Author: Jon Gilkison
Version: 0.2.3
Author URI: http://interfacelab.io
*/

require_once('vendor/autoload.php');

define('ILAB_STEM_CONTENT_DIR',dirname(__FILE__));
define('ILAB_STEM_CONTENT_URI', plugin_dir_url(__FILE__));

register_activation_hook( __FILE__, function(){
	if (!class_exists('\Stem\Core\Context')) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( __( 'Please install ILAB Stem App Framework for WordPress before activating this plugin.', 'stem-content' ) );
	}
});

function stem_content_register() {
	if (!defined('WP_CLI')) {
		add_action('acf/include_field_types', function( $version ) {
			new \ILab\StemContent\ACF\FontAwesomeField();
			new \ILab\StemContent\ACF\CSSClassesField();
			new \ILab\StemContent\ACF\ContentTemplateField();
		});
	}

	add_filter('stem/additional_view_paths', function($paths) {
		$paths[] = ILAB_STEM_CONTENT_DIR.'/views';

		return $paths;
	});


	add_filter('acf/settings/load_json', function($paths) {
		$context = \Stem\Core\Context::current();

		if (!empty($context)) {
			$import_fields = arrayPath($context->ui->config, 'content/import_fields', true);
			if ($import_fields) {
				$paths[] = ILAB_STEM_CONTENT_DIR.'/data/fields';
			}
		}

		return $paths;
	});
}



