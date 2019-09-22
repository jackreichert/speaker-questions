<?php

class SpeakerQuestionsSettings {
	function __construct() {
	}

	function init() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_init', [ $this, 'settings_init' ] );
	}

	function add_admin_menu() {
		add_submenu_page( 'edit.php?post_type=speaker_question', __( 'Speaker Questions Settings', 'textdomain' ), __( 'Settings', 'textdomain' ), 'manage_options', 'settings', [
			$this,
			'options_callback'
		] );
	}

	function settings_init() {
		register_setting( 'settingsPage', 'spq_settings' );

		add_settings_section( 'spq_settingsPage_section', __( 'Base Page', 'spq' ), [
			$this,
			'settings_section_callback'
		], 'settingsPage' );

		add_settings_field( 'spq_select_field_0', __( 'Select Plugin Base Page', 'spq' ), [
			$this,
			'render_pages_dropdown'
		], 'settingsPage', 'spq_settingsPage_section' );
	}

	function settings_section_callback() {
		echo __( 'Select which page should be used for the function.', 'spq' );
	}

	function render_pages_dropdown() {
		$options = get_option( 'spq_settings' );
		wp_dropdown_pages( [
			'name'     => 'spq_settings[base_page]',
			'selected' => $options['base_page']
		] );
	}

	function options_callback() {
		?>
		<form action='options.php' method='post'>

			<h2>Speaker Questions Settings</h2>

			<?php
			settings_fields( 'settingsPage' );
			do_settings_sections( 'settingsPage' );
			submit_button();
			?>

		</form>
		<?php
	}
}