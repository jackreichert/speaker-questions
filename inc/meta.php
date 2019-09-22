<?php

class SpeakerQuestionsMeta {
	function __construct() {
	}

	function init() {
		add_action( 'session_add_form_fields', [ $this, 'add_session_conference_meta_field' ], 10, 2 );
		add_action( 'session_edit_form_fields', [ $this, 'add_session_conference_edit_meta_field' ], 10, 2 );
		add_action( 'edited_session', [ $this, 'save_session_conference_meta' ], 10, 2 );
		add_action( 'create_session', [ $this, 'save_session_conference_meta' ], 10, 2 );
	}

	/**
	 *
	 */
	function add_session_conference_meta_field() { ?>
		<div class="form-field">
			<label for="term_meta[session_conf]"><?php _e( 'Session conference', 'spkquest' ); ?></label>
			<input type="text" name="term_meta[session_conf]" id="term_meta[session_conf]" value="">
			<p class="description"><?php _e( 'Enter the session conference', 'spkquest' ); ?></p>
		</div>
		<?php
	}

	/**
	 * @param $term
	 */
	function add_session_conference_edit_meta_field( $term ) {
		$t_id = $term->term_id;

		$term_meta = get_option( "taxonomy_$t_id" ); ?>
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="term_meta[session_conf]"><?php _e( 'Session conference', 'spkquest' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[session_conf]" id="term_meta[session_conf]" value="<?php echo esc_attr( $term_meta['session_conf'] ) ? esc_attr( $term_meta['session_conf'] ) : ''; ?>">
				<p class="description"><?php _e( 'Enter the session conference', 'spkquest' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * @param $term_id
	 */
	function save_session_conference_meta( $term_id ) {
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id      = $term_id;
			$term_meta = get_option( "taxonomy_$t_id" );
			$cat_keys  = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $_POST['term_meta'][ $key ] ) ) {
					$term_meta[ $key ] = $_POST['term_meta'][ $key ];
				}
			}
			update_option( "taxonomy_$t_id", $term_meta );
		}
	}
}