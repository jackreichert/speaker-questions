<?php

class SpeakerQuestionsProcessQuestion {
	function __construct() {
	}

	function init() {
		// submits asked question
		add_action( 'init', [ $this, 'process_question' ] );
		add_filter( 'the_content', [ $this, 'question_form' ], 10 );
	}

	/**
	 * submits asked question
	 */
	function process_question() {
		// if form was submitted for existing sesion and session question was posted
		if ( isset( $_POST['session_slug'] ) && isset( $_POST['session_question'] ) && ! empty( $_POST['session_question'] ) ) {
			// if nonce checks out
			if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'agmisda_speakerQuestions' ) ) {
				global $wpdb;
				$sess     = get_term_by( 'slug', wp_kses_post( $_POST['session_slug'] ), 'session' );
				$sub_time = intval( $_POST['sub_time'] );
				// check to see if question was asked already (prevents page reload re-adding the question)
				$query  = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '%%%s%%%d' AND post_status <> 'trash'", $sess->slug, $sub_time );
				$result = $wpdb->query( $query );

				// if the question was just asked, go to the question page again.
				if ( $result || ! $sess ) {
					wp_redirect( '/' . SpeakerQuestionsHelpers::get_basepage_path() . '/' . $_POST['session_slug'] );
					exit();
				}

				// add question
				$new_question = [
					'post_title'   => $sess->slug . " @ " . $sub_time,
					'post_content' => wp_kses_post( substr( $_POST['session_question'], 0, 1010 ) ),
					'post_status'  => 'pending',
					'post_type'    => 'speaker_question',
					'menu_order'   => $this->getNewQuestionPosition( $_POST['session_slug'] )
				];
				$question_id  = wp_insert_post( $new_question, true );
				$asker        = ( '' != $_POST['asker_name'] ) ? wp_kses_post( $_POST['asker_name'] ) : 'Anonymous';
				add_post_meta( $question_id, 'asker_name', $asker );
				// if error, log and silently fail
				if ( is_wp_error( $question_id ) ) {
					error_log( "Error inserting questions: " . $question_id->get_error_message() );
				} else {
					// add to session taxonomy
					wp_set_post_terms( $question_id, [ $sess->term_id ], 'session' );
				}

				wp_redirect( '/' . SpeakerQuestionsHelpers::get_basepage_path() . '/' . $_POST['session_slug'] . '?thanks' );
				exit();
			}
		}
	}

	public function getNewQuestionPosition( $session_slug ) {
		$questions = get_posts( [
			'posts_per_page' => - 1,
			'post_type'      => 'speaker_question',
			'post_status'    => [ 'pending', 'publish' ],
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'tax_query'      => [
				[
					'taxonomy' => 'session',
					'field'    => 'slug',
					'terms'    => [ $session_slug ]
				]
			]
		] );

		$ids = wp_list_pluck( $questions, 'ID' );
		SpeakerQuestionsHelpers::reorderQuestions( $ids );

		return count( $questions ) - 1;
	}

	function question_form( $content ) {
		global $post;
		$sess_slug = get_query_var( 'session' );
		if ( $post->ID == SpeakerQuestionsHelpers::get_basepage_id() && ! ( 'moderate' == get_query_var( 'mod' ) || 'moderate' == $sess_slug ) ) {
			$sess = get_term_by( 'slug', $sess_slug, 'session' );
			ob_start();
			if ( isset( $_GET['thanks'] ) ) : ?>
				<h3>Thank you</h3>
				<p>Your question has been submitted to the panel.</p>
				<a href="<?php echo '/' . SpeakerQuestionsHelpers::get_basepage_path() . '/' . $sess_slug; ?>/" class="btn btn-primary">Submit another question</a>
				<a href="<?php echo '/' . SpeakerQuestionsHelpers::get_basepage_path() . '/'; ?>" class="btn btn-warning">Submit a question for a different panel</a>
			<?php elseif ( $sess ) : ?>
				<form class="form-horizontal" id="confSpeakerQuestions" method="post">
					<?php wp_nonce_field( 'agmisda_speakerQuestions', '_wpnonce' ); ?>
					<input type="hidden" name="session_slug" value="<?php echo get_query_var( 'session' ); ?>" />
					<input type="hidden" name="sub_time" value="<?php echo time(); ?>">
					<p><textarea name="session_question" class="form-control" rows="3"></textarea></p>
					<div class="form-group">
						<label for="asker_name" class="col-sm-2 control-label">Name:</label>
						<div class="col-sm-10">
							<input class="form-control" type="text" id="asker_name" name="asker_name" placeholder="Name / Company (optional)">
						</div>
					</div>
					<button type="submit" class="btn btn-primary">Submit your question</button>
				</form>
				<?php
			endif;
			$content .= ob_get_contents();
			ob_end_clean();
		}

		return $content;
	}

}
