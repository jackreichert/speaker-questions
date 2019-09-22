<?php
class SpeakerQuestionsEnqueueScripts {
	function __construct() {
	}

	function init(){
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 *
	 */
	function enqueue_scripts() {
		wp_enqueue_style( 'speaker_questions_css', plugin_dir_url( __FILE__ ) . '../css/speaker-questions.css', [], 20150820 );

		wp_enqueue_script( 'handlebars_js', 'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.min.js', array(), null, true );
		wp_enqueue_script( 'touch_ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js', [
			'jquery-ui-sortable',
			'jquery'
		], null, true );
		wp_enqueue_script( 'speaker_questions_js', plugin_dir_url( __FILE__ ) . '../js/speaker-questions.js', [
			'jquery',
			'handlebars_js',
			'touch_ui'
		], 20150820 );
		wp_localize_script( 'speaker_questions_js', 'spkquest_ajax', array(
			'ajaxurl'     => admin_url( 'admin-ajax.php' ),
			'wpAJAXNonce' => wp_create_nonce( 'wpAJAX-nonce' )
		) );
	}
}