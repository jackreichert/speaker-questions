<?php

class SpeakerQuestionsTaxonomy {
	function __construct() {
	}

	function init() {
		// Register Custom Taxonomy `session`
		add_action( 'init', [ $this, 'create_session_tax' ], 0 );
	}

	/**
	 * Register Custom Taxonomy `session`
	 */
	function create_session_tax() {

		$labels = array(
			'name'                       => _x( 'Sessions', 'Taxonomy General Name', 'spkquest' ),
			'singular_name'              => _x( 'Session', 'Taxonomy Singular Name', 'spkquest' ),
			'menu_name'                  => __( 'Sessions', 'spkquest' ),
			'all_items'                  => __( 'All Sessions', 'spkquest' ),
			'parent_item'                => __( 'Parent Session', 'spkquest' ),
			'parent_item_colon'          => __( 'Parent Session:', 'spkquest' ),
			'new_item_name'              => __( 'New Session Name', 'spkquest' ),
			'add_new_item'               => __( 'Add New Session', 'spkquest' ),
			'edit_item'                  => __( 'Edit Session', 'spkquest' ),
			'update_item'                => __( 'Update Session', 'spkquest' ),
			'view_item'                  => __( 'View Session', 'spkquest' ),
			'separate_items_with_commas' => __( 'Separate sessions with commas', 'spkquest' ),
			'add_or_remove_items'        => __( 'Add or remove sessions', 'spkquest' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'spkquest' ),
			'popular_items'              => __( 'Popular Sessions', 'spkquest' ),
			'search_items'               => __( 'Search Sessions', 'spkquest' ),
			'not_found'                  => __( 'Not Found', 'spkquest' ),
			'no_terms'                   => __( 'No Sessions', 'spkquest' ),
			'items_list'                 => __( 'Sessions list', 'spkquest' ),
			'items_list_navigation'      => __( 'Sessions list navigation', 'spkquest' ),
		);
		$args   = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
		);
		register_taxonomy( 'session', array( 'speaker_question' ), $args );
	}
}