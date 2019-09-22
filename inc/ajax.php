<?php

class SpeakerQuestionsAjax {
	function __construct() {
	}

	function init() {
		add_action( 'wp_ajax_questionAjax', [ $this, 'ajaxSSL_func' ] );
	}


	/**
	 *
	 */
	function ajaxSSL_func() {

		$nonce = $_GET['ajaxSSLNonce'];
		if ( ! wp_verify_nonce( $nonce, 'wpAJAX-nonce' ) ) {
			die ( 'Busted!' );
		}

		$session_param = urldecode( strtolower( trim( $_GET['params']['session'] ) ) );
		$isSession     = term_exists( $session_param, 'session' );

		switch ( $_GET['method'] ) {
			case 'reorderQuestions':
				$response = SpeakerQuestionsHelpers::reorderQuestions( $_GET['params']['order'] );
				break;
			case 'starQuestion':
				$response = $this->starQuestion();
				break;
			case 'dismissQuestion':
				$response = $this->dismissQuestion();
				break;
			case 'unhideAll':
				$response = $this->unhideAll( $session_param, $isSession );
				break;
			case 'pollStarsOrder':
				$response = $this->pollStarsOrder( $session_param, $isSession );
				break;
		}

		// generate the response
		$response = json_encode( $response );

		// response output
		header( "content-type: text/javascript; charset=utf-8" );
		header( "access-control-allow-origin: *" );
		echo htmlspecialchars( $_GET['callback'] ) . '(' . $response . ')';

		// IMPORTANT: don't forget to "exit"
		exit;
	}

	/**
	 * @return array
	 */
	protected function starQuestion() {
		$id       = preg_replace( "/[^0-9]/", "", $_GET['params']['id'] );
		$question = get_post( $id );
		if ( 'pending' == $question->post_status ) {
			$q = [
				'ID'          => $id,
				'post_status' => 'publish'
			];
			wp_update_post( $q );
			$response = [ 'status' => 'starred', 'id' => $id ];
		} elseif ( 'publish' == $question->post_status ) {
			$q = [
				'ID'          => $id,
				'post_status' => 'pending'
			];
			wp_update_post( $q );
			$response = [ 'status' => 'emptied', 'id' => $id ];
		}

		return $response;
	}

	/**
	 * @return array
	 */
	protected function dismissQuestion() {
		$id = preg_replace( "/[^0-9]/", "", $_GET['params']['id'] );
		$q  = [
			'ID'          => $id,
			'post_status' => 'draft'
		];
		wp_update_post( $q );

		return [ 'status' => 'draft', 'id' => $id ];
	}

	/**
	 * @param $session_param
	 * @param $isSession
	 *
	 * @return false|int
	 */
	protected function unhideAll( $session_param, $isSession ) {
		global $wpdb;
		$where_term = $isSession ? sprintf( " AND $wpdb->terms.slug = '%s'", $session_param ) : '';
		$query      = $wpdb->prepare( "
					UPDATE $wpdb->posts
					LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
					LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
					LEFT JOIN $wpdb->terms ON($wpdb->terms.term_id= $wpdb->term_relationships.term_taxonomy_id)
					SET $wpdb->posts.post_status = 'pending'
					WHERE $wpdb->posts.post_status = 'draft' 
					$where_term
				", $session_param );
		$response   = $wpdb->query( $query );

		return $response;
	}

	/**
	 * @param $session_param
	 * @param $isSession
	 *
	 * @return array
	 */
	function pollStarsOrder( $session_param, $isSession ) {
		$time            = time();
		$notInConference = [];
		while ( ( time() - $time ) < 15 ) {
			// check if settings are the same as before
			// get current settings
			$isModerateAll       = 'moderate' == $session_param;
			$isSessionConference = ! $isSession;

			$questions = $this->get_questions( $session_param, $isSession );

			$is_same = true;
			$this->remove_empty_get_elements();

			foreach ( $questions as $i => $q ) {
				$conference = $this->get_conference( $isSessionConference, $q );

				if ( $isSession || $isModerateAll || $session_param == $conference ) {
					$qs["questions"][ $i ] = $this->build_question_response( $q );
				} elseif ( $session_param != $conference ) {
					$notInConference[ $i ] = $q->ID;
				}

				if ( ! isset( $_GET['params']['current'][ $i ] ) && $this->not_in_conference( $q->ID, $notInConference ) ) {
					// error_log( "$i not set" );
					$is_same = false;
				} elseif ( $this->not_in_conference( $q->ID, $notInConference ) && intval( $_GET['params']['current'][ $i ]['ID'] ) !== intval( $qs["questions"][ $i ]['ID'] ) ) {
					// error_log( $_GET['params']['current'][ $i ]['ID'] . " != " . $qs["questions"][ $i ]['ID'] );
					$is_same = false;
				} elseif ( $this->not_in_conference( $q->ID, $notInConference ) && $_GET['params']['current'][ $i ]['star'] !== $qs["questions"][ $i ]['star'] ) {
					// error_log( 'stars changed' );
					$is_same = false;
				}
			}

			if ( count( $_GET['params']['current'] ) != count( $qs["questions"] ) ) {
				$is_same = false;
			}
			// if change send change
			$response = [ 'same' => $is_same, 'questions' => $qs ];
			if ( ! $is_same ) {
				return $response;
			}
			// else send no change
			usleep( 250000 );
		}

		return $response;
	}

	/**
	 * @param $session_param
	 * @param $isSession
	 *
	 * @return array
	 */
	protected function get_questions( $session_param, $isSession ) {
		$tax_query = ( $isSession ) ? [
			'taxonomy' => 'session',
			'field'    => 'slug',
			'terms'    => [ $session_param ]
		] : '';

		$questions = get_posts( [
			'posts_per_page' => - 1,
			'post_type'      => 'speaker_question',
			'post_status'    => [ 'pending', 'publish' ],
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'tax_query'      => [ $tax_query ]
		] );

		return $questions;
	}

	/**
	 *
	 */
	protected function remove_empty_get_elements() {
		if ( isset( $_GET['params']['current'] ) ) {
			foreach ( $_GET['params']['current'] as $i => $val ) {
				if ( empty( $val ) ) {
					unset( $_GET['params']['current'][ $i ] );
				}
			}
		}
	}

	/**
	 * @param $isSessionConference
	 * @param $q
	 *
	 * @return string
	 */
	protected function get_conference( $isSessionConference, $q ) {
		$conference = '';
		if ( $isSessionConference ) {
			$session_list = wp_get_post_terms( $q->ID, 'session' );
			if ( count( $session_list ) && isset( $session_list[0]->term_id ) ) {
				$t_id       = $session_list[0]->term_id;
				$term_meta  = get_option( "taxonomy_$t_id" );
				$conference = strtolower( $term_meta['session_conf'] );

				return $conference;
			}

			return $conference;
		}

		return $conference;
	}

	/**
	 * @param $q
	 *
	 * @return array
	 */
	protected function build_question_response( $q ) {
		$question_response = [
			'ID'         => $q->ID,
			'content'    => wpautop( $q->post_content ),
			'asker_name' => get_post_meta( $q->ID, 'asker_name', true ),
			'star'       => ( 'publish' == $q->post_status ) ? 'filled' : 'empty'
		];

		return $question_response;
	}

	/**
	 * @param $qID
	 * @param $notInConference
	 *
	 * @return bool
	 */
	protected function not_in_conference( $qID, $notInConference ) {
		return ! in_array( $qID, $notInConference );
	}

}