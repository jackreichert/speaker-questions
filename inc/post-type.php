<?php

class SpeakerQuestionsPostType {
	function __construct() {
	}

	function init() {
		// Register Custom Post Type `speaker_question`
		add_action( 'init', [ $this, 'create_questions_type' ], 0 );
	}

	/**
	 * Register Custom Post Type `speaker_question`
	 */
	function create_questions_type() {
		$labels = [
			'name'                  => _x( 'Speaker Questions', 'Post Type General Name', 'spkquest' ),
			'singular_name'         => _x( 'Speaker Question', 'Post Type Singular Name', 'spkquest' ),
			'menu_name'             => __( 'Speaker Questions', 'spkquest' ),
			'name_admin_bar'        => __( 'Speaker Questions', 'spkquest' ),
			'archives'              => __( 'Speaker Questions Archives', 'spkquest' ),
			'parent_item_colon'     => __( 'Parent Item:', 'spkquest' ),
			'all_items'             => __( 'All Questions', 'spkquest' ),
			'add_new_item'          => __( 'Add New Item', 'spkquest' ),
			'add_new'               => __( 'Add New', 'spkquest' ),
			'new_item'              => __( 'New Item', 'spkquest' ),
			'edit_item'             => __( 'Edit Item', 'spkquest' ),
			'update_item'           => __( 'Update Item', 'spkquest' ),
			'view_item'             => __( 'View Item', 'spkquest' ),
			'search_items'          => __( 'Search Item', 'spkquest' ),
			'not_found'             => __( 'Not found', 'spkquest' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'spkquest' ),
			'featured_image'        => __( 'Featured Image', 'spkquest' ),
			'set_featured_image'    => __( 'Set featured image', 'spkquest' ),
			'remove_featured_image' => __( 'Remove featured image', 'spkquest' ),
			'use_featured_image'    => __( 'Use as featured image', 'spkquest' ),
			'insert_into_item'      => __( 'Insert into item', 'spkquest' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'spkquest' ),
			'items_list'            => __( 'Items list', 'spkquest' ),
			'items_list_navigation' => __( 'Items list navigation', 'spkquest' ),
			'filter_items_list'     => __( 'Filter items list', 'spkquest' ),
		];
		$args   = [
			'label'               => __( 'Speaker Question', 'spkquest' ),
			'description'         => __( 'Questions for conference speakers', 'spkquest' ),
			'labels'              => $labels,
			'supports'            => [ 'title', 'editor', 'author', ],
			'taxonomies'          => [ 'session' ],
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'page',
		];
		register_post_type( 'speaker_question', $args );

	}
}