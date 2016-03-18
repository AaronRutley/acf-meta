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
		// Setup a title and description field group
		function acf_meta_load_field_group() {

			if( function_exists('acf_add_local_field_group') ) {

				acf_add_local_field_group(array (
					'key' => 'group_56eb45e948d7a',
					'title' => 'ACF Meta',
					'fields' => array (
						array (
							'key' => 'field_56eb45ecdd246',
							'label' => 'Title',
							'name' => 'acf_meta_title',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array (
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
							'readonly' => 0,
							'disabled' => 0,
						),
						array (
							'key' => 'field_56eb45f2dd247',
							'label' => 'Description',
							'name' => 'acf_meta_description',
							'type' => 'textarea',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array (
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'maxlength' => 160,
							'rows' => 2,
							'new_lines' => '',
							'readonly' => 0,
							'disabled' => 0,
						),
					),
					'location' => array (
						array (
							array (
								'param' => 'post_type',
								'operator' => '==',
								'value' => 'post',
							),
						),
						array (
							array (
								'param' => 'post_type',
								'operator' => '==',
								'value' => 'page',
							),
						),
					),
					'menu_order' => 0,
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'left',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => 1,
					'description' => '',
				));

			}
		}
		add_action('acf/init', 'acf_add_local_field_group');


		// set custom title tag
		function acf_meta_custom_title( $title, $sep ) {
			global $post;
			$acf_meta_title = get_field( 'acf_meta_title', $post->ID);
			// check if this post has a title set
			if(!empty($acf_meta_title)) {
				return $acf_meta_title;
			} else {
				// set a fallback title with a max length of 60 chars
				$fallback_title = $post->post_title.' - '.get_bloginfo( 'name' );
				$fallback_title_short = mb_strimwidth($fallback_title, 0, 60);
				return $fallback_title_short;
			}
		}
		add_filter( 'wp_title', 'acf_meta_custom_title', 10, 2 );


		// set custom meta tags
		function acf_meta_custom_tags() {
		    global $post;
			$acf_meta_description = get_field( 'acf_meta_description', $post->ID);

			// if we have a description show it on the page
			if(!empty($acf_meta_description)) {
		    	echo '<meta name="description" content="' . $acf_meta_description . '" />' . "\n";
			}
		}
		add_action( 'wp_head', 'acf_meta_custom_tags' , 2 );

	}

}

$acf_meta_plugin = new ACF_Meta_Plugin();
