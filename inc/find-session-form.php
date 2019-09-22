<?php

class SpeakerQuestionsFindSessionForm {
	function __construct() {
	}

	function init() {
		// if person goes to the wrong url they can submit a form that lets them go to the correct one. This processes that form.
		add_action( 'wp', [ $this, 'redirect_to_session_code' ] );
		add_filter( 'the_content', [ $this, 'find_session_form' ], 10 );
	}

	/**
	 * if person goes to the wrong url they can submit a form that lets them go to the correct one. This processes that form.
	 */
	function redirect_to_session_code() {
		// if sessionCode is set
		if ( isset( $_POST['sessionCode'] ) ) {
			// if nonce checks out
			if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'isdaconf_sessionCode' ) ) {
				// redirect to questions page with requested code
				wp_redirect( '/' . SpeakerQuestionsHelpers::get_basepage_path() . '/' . $_POST['sessionCode'] );
			}

		}
	}

	function find_session_form( $content ) {
		global $post;
		if ( $post->ID == SpeakerQuestionsHelpers::get_basepage_id() ) {
			$sess_slug     = get_query_var( 'session' );
			$sess          = get_term_by( 'slug', $sess_slug, 'session' );
			$session_options = $this->get_session_options();
			if (( '' == $sess_slug || ! $sess ) && 'moderate' != $sess_slug ): ?>
				<form class="form-horizontal" id="confSpeakerQuestions" method="post">
					<?php wp_nonce_field( 'isdaconf_sessionCode', '_wpnonce' ); ?>
					<?php if ( "" != $sess_slug ) : ?>
						<p class="bg-warning">No session found for code: <?php echo $sess_slug; ?></p>
					<?php endif; ?>
					<p>
						<label for="sessionCode">Select session from dropdown menu to submit a question</label>
						<select type="text" class="form-control" name="sessionCode" id="sessionCode">
							<?php echo $session_options; ?>
						</select>
					</p>
					<button type="submit" class="btn btn-primary">Continue</button>
				</form>
			<?php endif;
		}

		return $content;
	}

	/**
	 * @return array|int|WP_Error
	 */
	protected function get_session_options() {
		$session_terms = get_terms( 'session', array(
			'hide_empty' => false,
		) );

		$sessions_as_options = '<option selected="true" disabled="disabled">-- Select a session --</option>';
		foreach ( $session_terms as $session ) {
			$sessions_as_options .= '<option value="' . $session->slug . '">' . $session->name . '</option>';
		}

		return $sessions_as_options;
	}
}