<?php
/**
 * Base post type class for Fast Checkout for WooCommerce.
 *
 * @package fast
 */

namespace FastWC\Post_Types;

/**
 * Base post type class.
 */
class Post_Type {

	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Post type args.
	 *
	 * @var array
	 */
	protected $args = array();

	/**
	 * Post type labels.
	 *
	 * @var array
	 */
	protected $labels = array();

	/**
	 * Post type constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the post type.
	 */
	protected function init() {
		$this->set_default_labels();
		$this->set_labels();
		$this->set_default_args();
		$this->set_args();

		\register_post_type( $this->name, $this->args );
	}

	/**
	 * Set the default label values.
	 */
	protected function set_default_labels() {
		$this->labels = array(
			'name'                  => _x( 'Post Types', 'Post Type General Name', 'text_domain' ),
			'singular_name'         => _x( 'Post Type', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'             => __( 'Post Types', 'text_domain' ),
			'name_admin_bar'        => __( 'Post Type', 'text_domain' ),
			'archives'              => __( 'Item Archives', 'text_domain' ),
			'attributes'            => __( 'Item Attributes', 'text_domain' ),
			'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
			'all_items'             => __( 'All Items', 'text_domain' ),
			'add_new_item'          => __( 'Add New Item', 'text_domain' ),
			'add_new'               => __( 'Add New', 'text_domain' ),
			'new_item'              => __( 'New Item', 'text_domain' ),
			'edit_item'             => __( 'Edit Item', 'text_domain' ),
			'update_item'           => __( 'Update Item', 'text_domain' ),
			'view_item'             => __( 'View Item', 'text_domain' ),
			'view_items'            => __( 'View Items', 'text_domain' ),
			'search_items'          => __( 'Search Item', 'text_domain' ),
			'not_found'             => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
			'featured_image'        => __( 'Featured Image', 'text_domain' ),
			'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
			'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
			'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
			'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
			'items_list'            => __( 'Items list', 'text_domain' ),
			'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
			'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
		);
	}

	/**
	 * Set the label values.
	 */
	protected function set_labels() {
		// This is optional, and enables the child class to override the default labels.
	}

	/**
	 * Set the default args.
	 */
	protected function set_default_args() {
		$this->args = array(
			'label'               => __( 'Post Type', 'text_domain' ),
			'description'         => __( 'Post Type Description', 'text_domain' ),
			'labels'              => $this->labels,
			'supports'            => false,
			'taxonomies'          => array( 'category', 'post_tag' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);
	}

	/**
	 * Set the args.
	 */
	protected function set_args() {
		// This is optional, and enables the child class to override the default args.
	}

}
