<?php

class SpeakerQuestionsModerate {
	function __construct() {
	}

	function init() {
		add_filter( 'the_content', [ $this, 'moderate_list' ], 10 );
	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	function moderate_list( $content ) {
		global $post;
		if ( $post->ID == SpeakerQuestionsHelpers::get_basepage_id() ) {
			$sess_slug = get_query_var( 'session' );

			if ( 'moderate' == get_query_var( 'mod' ) && ! is_user_logged_in() ) {
				$content .= wp_login_form();
			} elseif ( ( 'moderate' == get_query_var( 'mod' ) || 'moderate' == $sess_slug ) && current_user_can( 'edit_posts' ) ) {
				$qs = $this->get_questions_for_moderation( $sess_slug );
				$content .= $this->get_moderate_list_markup( $sess_slug, $qs );
			}
		}

		return $content;
	}

	/**
	 * @param $sess_slug
	 *
	 * @return array
	 */
	protected function get_questions_for_moderation( $sess_slug ) {
		$questions = get_posts( [
			'posts_per_page' => - 1,
			'post_type'      => 'speaker_question',
			'post_status'    => [ 'pending', 'publish' ],
			'tax_query'      => [
				[
					'taxonomy'         => 'session',
					'field'            => 'slug',
					'terms'            => $sess_slug,
					'include_children' => false
				]
			],
			'orderby'        => 'menu_order',
			'order'          => 'ASC'
		] );

		$qs = [];
		foreach ( $questions as $i => $q ) {
			$qs["questions"][] = [
				'ID'         => $q->ID,
				'content'    => wpautop( $q->post_content ),
				'asker_name' => get_post_meta( $q->ID, 'asker_name', true ),
				'star'       => ( 'publish' == $q->post_status ) ? 'filled' : 'empty'
			];
		}

		return $qs;
	}

	/**
	 * @param $sess_slug
	 * @param $qs
	 *
	 * @return string
	 */
	private function get_moderate_list_markup( $sess_slug, $qs ) {
		ob_start(); ?>
		<button id="unhide" class="btn btn-primary">Unhide all</button>
		<button id="starsToTop" class="btn">Starred on top</button>
		<ul id="sq_list">
			<li id="q_0">No questions yet</li>
		</ul>
		<input type="hidden" name="session_slug" value="<?php echo $sess_slug; ?>">
		<script>var questions = <?php echo json_encode( $qs ); ?></script>
		<script id="sq_list-template" type="text/x-handlebars-template">
			{{#each questions}}
			<li id="q_{{ID}}" class="row star-{{star}}">
				<div class="question-content col-sm-8 col-xs-12">
					<p><strong>{{{content}}}</strong></p>
					<p>Asked by: {{asker_name}}</p>
					<input type="hidden" class="array_index" value="{{@key}}">
				</div>
				<div class="col-sm-4 col-xs-12">
					<div class="dashicons dashicons-star-{{star}}"></div>
					<div class="dashicons dashicons-image-flip-vertical"></div>
					<div class="dashicons dashicons-dismiss"></div>
				</div>
			</li>
			{{/each}}
		</script>
		<?php $moderate_list_markup = ob_get_contents();
		ob_end_clean();

		return $moderate_list_markup;
	}
}