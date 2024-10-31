<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class MSK_Projector_Post_Type {
	private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;
	private $token;

	public function __construct( $file ) {
		$this->dir = dirname( $file );
		$this->file = $file;
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
		$this->token = 'msk_wip';

		// Regsiter post type
		add_action( 'init' , array( $this, 'register_post_type' ) );

		// Register taxonomy
		//add_action('init', array( $this, 'register_taxonomy' ) );

		if ( is_admin() ) {
			// Modify text in main title text box
			add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) );

			// Handle post columns
			add_filter( 'manage_edit-' . $this->token . '_columns', array( $this, 'register_custom_column_headings' ), 10, 1 );
			add_action( 'manage_pages_custom_column', array( $this, 'register_custom_columns' ), 10, 2 );

		}

	}

	/**
	 * Register new post type
	 * @return void
	 */
	public function register_post_type() {

		$labels = array(
			'name' => _x( 'WIP', 'post type general name' , 'msk-projector' ),
			'singular_name' => _x( 'WIP', 'post type singular name' , 'msk-projector' ),
			'add_new' => __( 'Add New', 'msk-projector' ),
			'add_new_item' => sprintf( __( 'Add new %s' , 'msk-projector' ), __( 'Work in Progress' , 'msk-projector' ) ),
			'edit_item' => sprintf( __( 'Edit %s' , 'msk-projector' ), __( 'Work in Progress' , 'msk-projector' ) ),
			'new_item' => sprintf( __( 'New %s' , 'msk-projector' ), __( 'WIP' , 'msk-projector' ) ),
			'all_items' => sprintf( __( 'All %s' , 'msk-projector' ), __( 'WIPs' , 'msk-projector' ) ),
			'view_item' => sprintf( __( 'View %s' , 'msk-projector' ), __( 'WIP' , 'msk-projector' ) ),
			'search_items' => sprintf( __( 'Search %a' , 'msk-projector' ), __( 'Works in Progress' , 'msk-projector' ) ),
			'not_found' =>  sprintf( __( 'No %s found' , 'msk-projector' ), __( 'WIP' , 'msk-projector' ) ),
			'not_found_in_trash' => sprintf( __( 'No %s Found In Trash' , 'msk-projector' ), __( 'WIP' , 'msk-projector' ) ),
			'parent_item_colon' => '',
			'menu_name' => __( 'WIP\'s' , 'msk-projector' )
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'wip'
			),
			'capability_type' => 'page',
			'has_archive' => true,
			'hierarchical' => true,
			'supports' => array( 'title' , 'editor' ),
			'menu_position' => 5,
			'menu_icon' => $this->assets_url . '/images/menu_icon.png'
		);

		register_post_type( $this->token, $args );

		$wip_metabox_content_fields = array();
		$wip_metabox_content_fields[] = array(
			'name'          => 'title',
			'label'         => __('Title', 'msk-projector'),
			'description'   => __('Define a title here (optional).', 'msk-projector'),
			'type'          => 'text'
		);

		for ($i = 1; $i <= get_option('msk_projector_global_settings_img_per_wip'); $i++) {
			$name = 'image' . $i;

			$wip_metabox_content_fields[] = array(
				'name'          => $name,
				'label'         => __('Image', 'msk-projector') . ' ' . $i,
				'description'   => __('Upload your image here.', 'msk-projector'),
				'type'          => 'image'
			);
		}

		$wip_metabox_content_fields[] = array(
			'name'          => 'desc',
			'label'         => __('Description', 'msk-projector'),
			'description'   => __('Describe your image here (optional).', 'msk-projector'),
			'type'          => 'wysiwyg'
		);

		$wip_metabox_content = new Cuztom_Meta_Box(
			'wip_content',
			__('Images and descriptions', 'msk-projector'),
			$this->token,
			array(
				'bundle',
				$wip_metabox_content_fields
			),
			'normal',
			'default'
		);

		$wip_metabox_data = new Cuztom_Meta_Box(
			'data',
			__('Data', 'msk-projector'),
			$this->token,
			array( $this, 'data_metabox' ),
			'side',
			'high'
		);

		$wip_metabox_settings = new Cuztom_Meta_Box(
			'wip_settings',
			__('Project settings', 'msk-projector'),
			$this->token,
			array(
				array(
					'name'          => 'password',
					'label'         => __('Password', 'msk-projector'),
					'type'          => 'text'
				)
			),
			'side',
			'high'
		);
	}

	/**
	 * Data metabox
	 * @return void
	 */
	public function data_metabox($data) {
		echo '<strong>' . __('Private <em>Work in Progress</em> link', 'msk-projector') . ' : </strong><br>';
		if ($data->post_status == 'publish') {
			echo '<a target="_blank" href="' . get_permalink($data->ID) . '" title="' . __('See WIP page', 'msk-projector') . '">' . str_replace(get_home_url(), '', get_permalink($data->ID)) . '</a>';
		} else {
			_e('Please publish this WIP project first.', 'msk-projector');
		}
	}

	/**
	 * Register new taxonomy
	 * @return void
	 */
	/*public function register_taxonomy() {

        $labels = array(
            'name' => __( 'Terms' , 'msk-projector' ),
            'singular_name' => __( 'Term', 'msk-projector' ),
            'search_items' =>  __( 'Search Terms' , 'msk-projector' ),
            'all_items' => __( 'All Terms' , 'msk-projector' ),
            'parent_item' => __( 'Parent Term' , 'msk-projector' ),
            'parent_item_colon' => __( 'Parent Term:' , 'msk-projector' ),
            'edit_item' => __( 'Edit Term' , 'msk-projector' ),
            'update_item' => __( 'Update Term' , 'msk-projector' ),
            'add_new_item' => __( 'Add New Term' , 'msk-projector' ),
            'new_item_name' => __( 'New Term Name' , 'msk-projector' ),
            'menu_name' => __( 'Terms' , 'msk-projector' ),
        );

        $args = array(
            'public' => true,
            'hierarchical' => true,
            'rewrite' => true,
            'labels' => $labels
        );

        register_taxonomy( 'post_type_terms' , $this->token , $args );
    }*/

    /**
     * Regsiter column headings for post type
     * @param  array $defaults Default columns
     * @return array           Modified columns
     */
    public function register_custom_column_headings($columns) {
	    $columns = array_slice($columns, 0, 1, true) + array('wip_image' => __('Image', 'msk-projector')) + array_slice($columns, 1, count($columns), true);
	    return $columns;
	}

	/**
	 * Load data for post type columns
	 * @param  string  $column_name Name of column
	 * @param  integer $id          Post ID
	 * @return void
	 */
	public function register_custom_columns( $column_name, $id ) {

		switch ( $column_name ) {

			case 'wip_image':
				$meta = get_post_meta( $id, '_wip_content', true );
				$first_image_id = $meta[0]['_image1'];

				if (isset($first_image_id) && $first_image_id != '') {
					$first_image = wp_get_attachment_image_src($first_image_id, 'thumbnail');
					echo '<a href="' . get_edit_post_link($id) . '" title="' . get_the_title($id) . '"><img style="width:150px;" src="' . $first_image[0] . '" /></a>';
				}
			break;

			default:
			break;
		}

	}

	/**
	 * Load custom title placeholder text
	 * @param  string $title Default title placeholder
	 * @return string        Modified title placeholder
	 */
	public function enter_title_here( $title ) {
		if ( get_post_type() == $this->token ) {
			$title = __( 'Enter WIP title here...' , 'msk-projector' );
		}
		return $title;
	}


}