<?php

class SpeakerQuestionsHelpers {
	private static $options;

	function __construct() {
		self::$options = get_option( 'spq_settings' );
	}

	static public function get_basepage_path() {
		$basepath = parse_url( get_permalink( self::$options['base_page'] ) );

		return trim( $basepath['path'], '/' );
	}

	static public function get_basepage_id() {
		return intval( self::$options['base_page'] );
	}

	static public function reorderQuestions( $orderIds ) {
		$response = "";
		if ( is_array( $orderIds ) ) {
			foreach ( $orderIds as $i => $id ) {
				$q   = [
					'ID'         => preg_replace( "/[^0-9]/", "", $id ),
					'menu_order' => $i
				];
				$res = wp_update_post( $q );
			}
			$response = 'reordered';
		}

		return $response;
	}
}