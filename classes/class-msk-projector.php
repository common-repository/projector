<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class MSK_Projector {
	private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;

	public function __construct( $file ) {
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		// Load CSS on front-end
		add_action( 'wp_enqueue_scripts', array( $this, 'front_custom_css_js' ) );

		// Load CSS on back-end
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_custom_css' ) );

		// Load footer JS code
		add_action( 'wp_footer', array( $this, 'footer_code' ) );

		// Load plugin or theme template files for WIP post type
		add_filter( 'template_include', array( $this, 'load_wip_template' ) );
	}

	/**
	 * Load plugin localisation
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'msk-projector' , false , dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Load plugin textdomain
	 * @return void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'msk-projector';

	    $locale = apply_filters( 'plugin_locale' , get_locale() , $domain );

	    load_textdomain( $domain , WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain , FALSE , dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}


	/**
	 * Load custom CSS on admin pages
	 */
	public function admin_custom_css() {
		echo '<style type="text/css">
			.mp6 #adminmenu #menu-posts-msk_wip div.wp-menu-image:before { content: "\f308"; }
			.mp6 #adminmenu #menu-posts-msk_wip div.wp-menu-image img { display:none; }
			#wip_image { width:150px; }
			.settings_page_msk_projector_settings #wpbody-content h3 { margin-top: 2em; }
        </style>';
	}


	/**
	 * Load custom CSS & JS on front-end
	 */
	public function front_custom_css_js() {
		if (get_option('msk_projector_advanced_settings_enable_plugin_css') == 1 && is_singular('msk_wip')) {
			wp_enqueue_style('msk-projector', MSK_PROJECTOR_URL . 'assets/css/msk-projector.css', false, null);
		}

		if (get_option('msk_projector_global_settings_enable_zoom') == 1 && is_singular('msk_wip')) {
			// Slimbox CSS & JS
			wp_enqueue_style('msk_projector_slimbox', MSK_PROJECTOR_URL . 'assets/css/slimbox2.css', false, null);
			wp_enqueue_script('msk_projector_slimbox', MSK_PROJECTOR_URL . 'assets/js/slimbox2.min.js', array('jquery'), '2.05', true);
		}
	}


	/**
	 * Load Slimbox JS+CSS & enable it on single-msk_wip pages
	 */
	public function footer_code() {
		if (get_option('msk_projector_global_settings_enable_zoom') == 1 && is_singular('msk_wip')) {
			echo '<script>if (!/android|iphone|ipod|series60|symbian|windows ce|blackberry/i.test(navigator.userAgent)) { jQuery(function($) { $("a[rel^=\'msk-lightbox\']").slimbox({ counterText: false }, null, function(el) { return (this == el) || ((this.rel.length > 8) && (this.rel == el.rel)); }); }); }</script>';
		}
	}


	/**
	 * Load single-msk_wip.php plugin or theme template file
	 */
	public function load_wip_template( $tpl ) {
		$id = get_the_ID();

		if ( get_post_type( $id ) != 'msk_wip' ) {
			return $tpl;
		}

		else {
			// Get the template slug
			if ( $theme_file = locate_template( array( 'single-msk_wip.php' ) ) ) {
				$file = $theme_file;
			}
			else {
				$file = MSK_PROJECTOR_PATH . 'templates/single-msk_wip.php';
			}

			return apply_filters( 'msk_projector_tpl_' . $file, $file );
		}
	}
}