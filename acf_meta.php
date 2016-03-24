<?php
/*
Plugin Name: ACF Meta
Plugin URI: http://www.aaronrutley.com/
Description: Very experimental WordPress plugin to use ACF to set the title & description meta tags.
Version: 0.0.1
Author: Aaron Rutley
Author URI: http://www.aaronrutley.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class ACF_Meta_Plugin {

	function __construct()
	{
		// include meta fields
		function acf_meta_load_field_group() {
			require_once(__DIR__.'/acf_meta_fields.php');
		}
		add_action('acf/init', 'acf_meta_load_field_group');


		// setup custom image size
		if ( function_exists( 'add_theme_support' ) ) {
			add_image_size( 'acf_meta_1200', 1200, '', true );
		}

		// set custom title tag
		function acf_meta_custom_title() {
			global $post;
			$acf_meta_title = get_field( 'acf_meta_title', $post->ID);
			// check if this post has a title set
			if(!empty($acf_meta_title)) {
				return $acf_meta_title.' - '.get_bloginfo( 'name' );
			} else {
				// set a fallback title with a max length of 60 chars
				$fallback_title = $post->post_title.' - '.get_bloginfo( 'name' );
				$fallback_title_short = mb_strimwidth($fallback_title, 0, 60);
				return $fallback_title_short;
			}
		}
		add_filter( 'wp_title', 'acf_meta_custom_title', 10, 2 );
		function acf_meta_set_title_tag() {
			function acf_meta_override_title_parts($title) {
				$title['title'] = acf_meta_custom_title();
				return $title;
			}

			if (current_theme_supports('title-tag')) {
				add_filter('document_title_parts', 'acf_meta_override_title_parts');
			}
		}
		add_action('init', 'acf_meta_set_title_tag');

		// set custom meta tags
		function acf_meta_custom_tags() {
		    global $post;

			// og meta tags for site name, title and url
			echo '<meta property="og:site_name" content="'.get_bloginfo( 'name' ).'" />' . "\n";
			echo '<meta property="og:url" content="'.get_permalink().'" />' . "\n";
			echo '<meta property="og:title" content="'.acf_meta_custom_title().'" />' . "\n";

			// image og meta tag - if we have an image echo the tag
			$acf_meta_image_array = get_field( 'acf_meta_image', $post->ID);
			$acf_meta_image_url = $acf_meta_image_array['sizes']['acf_meta_1200'];
			if(!empty($acf_meta_image_url)) {
				echo '<meta property="og:image" content="'.$acf_meta_image_url.'" />' . "\n";
			}

			// description tag and og meta tag - if we have a description echo the tag
			$acf_meta_description = get_field( 'acf_meta_description', $post->ID);
			if(!empty($acf_meta_description)) {
				echo '<meta property="og:description" content="'. $acf_meta_description . '" />' . "\n";
		    	echo '<meta name="description" content="'. $acf_meta_description . '" />' . "\n";
			}
		}
		add_action( 'wp_head', 'acf_meta_custom_tags' , 2 );

	}
}

$acf_meta_plugin = new ACF_Meta_Plugin();
