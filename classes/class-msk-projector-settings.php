<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class MSK_Projector_Settings {
	private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;

	public function __construct( $file ) {
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item() {
		add_options_page( 'Projector' , 'Projector' , 'manage_options' , 'msk_projector_settings' ,  array( $this, 'settings_page' ) );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=msk_projector_settings">' . __('Settings', 'msk-projector') . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings() {
		
		/*
		 * Global settings
		 */
		add_settings_section( 'global_settings' , __( 'Global settings' , 'msk-projector' ) , array( $this, 'global_settings' ) , 'msk_projector_settings' );
		
		// Images per WIP
		add_settings_field( 'msk_projector_global_settings_img_per_wip' , __( 'Number of item images' , 'msk-projector' ) , array( $this, 'global_settings_img_per_wip' )  , 'msk_projector_settings' , 'global_settings' );
		register_setting( 'msk_projector_settings' , 'msk_projector_global_settings_img_per_wip' , array( $this, 'validate_global_settings_img_per_wip' ) );
		
		// Enable sidebar
		add_settings_field( 'msk_projector_global_settings_enable_sidebar' , __( 'Display sidebar' , 'msk-projector' ) , array( $this, 'global_settings_enable_sidebar' )  , 'msk_projector_settings' , 'global_settings' );
		register_setting( 'msk_projector_settings' , 'msk_projector_global_settings_enable_sidebar' , array( $this, 'validate_checkbox' ) );

		// Enable slimbox2 zoom
		add_settings_field( 'msk_projector_global_settings_enable_zoom' , __( 'Enable Zoom' , 'msk-projector' ) , array( $this, 'global_settings_enable_zoom' )  , 'msk_projector_settings' , 'global_settings' );
		register_setting( 'msk_projector_settings' , 'msk_projector_global_settings_enable_zoom' , array( $this, 'validate_checkbox' ) );

		
		/*
		 * Messages settings
		 */
		add_settings_section( 'message_settings' , __( 'Message settings' , 'msk-projector' ) , array( $this, 'message_settings' ) , 'msk_projector_settings' );
		
		// Password pre-text
		add_settings_field( 'msk_projector_message_settings_password_pre_text' , __( 'Before password text' , 'msk-projector' ) , array( $this, 'message_settings_password_pre_text' )  , 'msk_projector_settings' , 'message_settings' );
		register_setting( 'msk_projector_settings' , 'msk_projector_message_settings_password_pre_text' );

		// Password error message
		add_settings_field( 'msk_projector_message_settings_password_error' , __( 'Password error message' , 'msk-projector' ) , array( $this, 'message_settings_password_error' )  , 'msk_projector_settings' , 'message_settings' );
		register_setting( 'msk_projector_settings' , 'msk_projector_message_settings_password_error' );

		
		/*
		 * Advanced settings
		 */
		add_settings_section( 'advanced_settings' , __( 'Advanced controls' , 'msk-projector' ) , array( $this, 'advanced_settings' ) , 'msk_projector_settings' );
		add_settings_field( 'msk_projector_advanced_settings_enable_plugin_css' , __( 'Load plugin CSS' , 'msk-projector' ) , array( $this, 'advanced_settings_enable_plugin_css' )  , 'msk_projector_settings' , 'advanced_settings' );
		register_setting( 'msk_projector_settings' , 'msk_projector_advanced_settings_enable_plugin_css' , array( $this, 'validate_checkbox' ) );

	}

	/**
	 * Subtitle for settings section
	 * @return void
	 */
	public function global_settings() {
		echo '<p><small>' . __( 'Set the global behaviour of the Projector plugin.' , 'msk-projector' ) . '</small></p>';
	}

	public function message_settings() {
		echo '<p><small>' . __( 'Define the multiple text messages displayed by the plugin.' , 'msk-projector' ) . '</small></p>';
	}

	public function advanced_settings() {
		echo '<p><small>' . __( 'You have a large level of control with the Projector plugin. Note that if you specify different CSS classes to elements, default styling will stop working.' , 'msk-projector' ) . '<br>';
		echo sprintf( __( 'If you disable the plugin CSS, you can always grab a copy of the <a href="%s" target="_blank">.css</a> or <a href="%s" target="_blank">.scss</a> file to easily customize styles.' , 'msk-projector' ), MSK_PROJECTOR_URL . 'assets/css/msk-projector.css', MSK_PROJECTOR_URL . 'assets/css/msk-projector.scss') . '</small></p>';
	}

	/**
	 * Load individual settings field
	 * @return void
	 */
	public function global_settings_img_per_wip() {
		$option = (get_option('msk_projector_global_settings_img_per_wip') > 0) ? get_option('msk_projector_global_settings_img_per_wip') : 3;

		echo '<input id="global_settings_img_per_wip" type="number" name="msk_projector_global_settings_img_per_wip" class="small-text" value="' . $option . '"/>
				<label for="global_settings_img_per_wip"><span class="description">' . __( 'How many images would you like to be able to upload <strong>at most</strong> per WIP item ?' , 'msk-projector' ) . '</span></label>';
	}

	public function global_settings_enable_sidebar() {
		$option = get_option('msk_projector_global_settings_enable_sidebar');

		echo '<input type="checkbox" id="global_settings_enable_sidebar" name="msk_projector_global_settings_enable_sidebar" value="1"' . checked( 1, $option, false ) . '/>
				<label for="global_settings_enable_sidebar">' . __('Display your theme sidebar on WIP pages.', 'msk-projector') . '</label>';
	}

	public function global_settings_enable_zoom() {
		$option = get_option('msk_projector_global_settings_enable_zoom');

		echo '<input type="checkbox" id="global_settings_enable_zoom" name="msk_projector_global_settings_enable_zoom" value="1"' . checked( 1, $option, false ) . '/>
				<label for="global_settings_enable_zoom">' . __('Enable zoom effect with slimbox2 (a lightbox-like jQuery plugin).', 'msk-projector') . '</label>';
	}

	public function message_settings_password_pre_text() {
		$option = get_option('msk_projector_message_settings_password_pre_text');

		echo '<textarea id="message_settings_password_pre_text" name="msk_projector_message_settings_password_pre_text" rows="4" class="large-text">' . $option . '</textarea>
				<br><label for="message_settings_password_pre_text"><span class="description">' . __( 'This text is displayed before the password field on protected WIP pages. <small>HTML is allowed !</small>' , 'msk-projector' ) . '</span></label>';
	}

	public function message_settings_password_error() {
		$option = get_option('msk_projector_message_settings_password_error');

		echo '<textarea id="message_settings_password_error" name="msk_projector_message_settings_password_error" rows="2" class="large-text">' . $option . '</textarea>
				<br><label for="message_settings_password_error"><span class="description">' . __( 'This is the error message displayed if the password is incorrect. <small>HTML is allowed !</small>' , 'msk-projector' ) . '</span></label>';
	}

	public function advanced_settings_enable_plugin_css() {
		$option = get_option('msk_projector_advanced_settings_enable_plugin_css');

		echo '<input type="checkbox" id="advanced_settings_enable_plugin_css" name="msk_projector_advanced_settings_enable_plugin_css" value="1"' . checked( 1, $option, false ) . '/>
				<label for="advanced_settings_enable_plugin_css">' . __('Load the Projector plugin CSS. Uncheck this if you want to overwrite styling with your own CSS.', 'msk-projector') . '</label>';
	}

	/**
	 * Validate individual settings field
	 * @param  string $data Inputted value
	 * @return string       Validated value
	 */
	public function validate_global_settings_img_per_wip($option) {
		if ($option && is_numeric($option)) {
			$option = (int) $option;
		}
		return $option;
	}

	public function validate_checkbox($option) {
		if ($option && is_numeric($option)) {
			$option = (int) $option;
		} else $option = 0;
		return $option;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page() {

		echo '<div class="wrap">
				<div class="icon32" id="msk_projector_settings-icon"><br/></div>
				<h2>' . __('Projector settings', 'msk-projector') . '</h2>
				<form method="post" action="options.php" enctype="multipart/form-data">';

				settings_fields( 'msk_projector_settings' );
				do_settings_sections( 'msk_projector_settings' );

			  echo '<p class="submit">
						<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'msk-projector' ) ) . '" />
					</p>
				</form>
			  </div>';
	}

}