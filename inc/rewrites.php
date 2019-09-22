<?php

class SpeakerQuestionsRewrites {
	function __construct() {
	}

	function init() {
		// add query_var and rewrite rule for adding $_GET['session'] variable to /questions/ page
		add_action( 'init', [ $this, 'question_query' ] );
	}


	/**
	 * add query_var and rewrite rule for adding $_GET['session'] variable to /questions/ page
	 */
	function question_query() {
		global $wp, $wp_rewrite;
		$basepath = SpeakerQuestionsHelpers::get_basepage_path();

		// add query_var
		$wp->add_query_var( 'session' );
		$wp->add_query_var( 'mod' );

		// add rewrite_rule
		$wp_rewrite->add_rule( "$basepath/([^/]+)/([^/]+)?", 'index.php?pagename=' . $basepath . '&session=$matches[1]&mod=$matches[2]', 'top' );
		$wp_rewrite->add_rule( "$basepath/([^/]+)/?", 'index.php?pagename=' . $basepath . '&session=$matches[1]', 'top' );

		// Once you get working, remove this next line
		$wp_rewrite->flush_rules( false );

	}


}