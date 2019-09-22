<?php

class SpeakerQuestionsColumns {
	function __construct() {
	}

	function init() {
		add_filter( 'manage_edit-session_columns', [ $this, 'add_session_conference_column_head' ] );
		add_filter( 'manage_session_custom_column', [ $this, 'add_session_conference_column_content' ], 10, 3 );
	}

	/**
	 * @param $defaults
	 *
	 * @return mixed
	 */
	function add_session_conference_column_head( $defaults ) {
		$defaults['conference'] = 'Conference';

		return $defaults;
	}

	/**
	 * @param $content
	 * @param $column_name
	 * @param $term_id
	 *
	 * @return mixed
	 */
	function add_session_conference_column_content( $content, $column_name, $term_id ) {
		switch ( $column_name ) {
			case 'conference':
				$t_id      = $term_id;
				$term_meta = get_option( "taxonomy_$t_id" );
				$content   = $term_meta['session_conf'];
				break;
			default:
				break;
		}

		return $content;
	}

}