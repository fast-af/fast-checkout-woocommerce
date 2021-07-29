<?php
/**
 * Headless checkout link post type for Fast Checkout for WooCommerce.
 *
 * @package fast
 */

namespace FastWC\Post_Types;

/**
 * Headless checkout link post type class.
 */
class Headless_Checkout_Link extends Post_Type {

	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected $name = 'fastwc_headless_link';

	/**
	 * Headless link slug.
	 *
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Initialize the post type.
	 */
	protected function init() {
		// Only register this post type if Gutenberg is active.
		if ( \fastwc_gutenberg_is_active() ) {
			parent::init();

			// Check if the slug matches the saved slug. If not, flush the rewrite rules.
			$saved_slug = get_option( FASTWC_SETTING_SAVED_HEADLESS_LINK_SLUG, '' );
			if ( $this->slug !== $saved_slug ) {
				update_option( FASTWC_SETTING_SAVED_HEADLESS_LINK_SLUG, $this->slug );

				// Flush the URL rewrite rules to account for the updated slug.
				flush_rewrite_rules( false );
			}
		}
	}

	/**
	 * Set the labels.
	 */
	protected function set_labels() {
		$labels = array(
			'name'                  => _x( 'Fast Headless Checkout Links', 'Post Type General Name', 'fast' ),
			'singular_name'         => _x( 'Fast Headless Checkout Link', 'Post Type Singular Name', 'fast' ),
			'menu_name'             => __( 'Headless Checkout Links', 'fast' ),
			'name_admin_bar'        => __( 'Headless Checkout Link', 'fast' ),
			'all_items'             => __( 'All Links', 'fast' ),
			'add_new_item'          => __( 'Add New Link', 'fast' ),
			'add_new'               => __( 'Add New', 'fast' ),
			'new_item'              => __( 'New Link', 'fast' ),
			'edit_item'             => __( 'Edit Link', 'fast' ),
			'update_item'           => __( 'Update Link', 'fast' ),
			'view_item'             => __( 'View Link', 'fast' ),
			'view_items'            => __( 'View Links', 'fast' ),
			'search_items'          => __( 'Search Link', 'fast' ),
			'items_list'            => __( 'Links list', 'fast' ),
			'items_list_navigation' => __( 'Links list navigation', 'fast' ),
			'filter_items_list'     => __( 'Filter links list', 'fast' ),
		);

		// Merge with the default labels.
		$this->labels = \wp_parse_args( $labels, $this->labels );
	}

	/**
	 * Set the args.
	 */
	protected function set_args() {
		$this->slug = get_option( FASTWC_SETTING_HEADLESS_LINK_SLUG, FASTWC_DEFAULT_HEADLESS_LINK_SLUG );

		$rewrite = array(
			'slug'       => $headless_link_slug,
			'with_front' => true,
			'pages'      => false,
			'feeds'      => false,
		);
		$capabilities = array(
			'edit_post'          => 'manage_woocommerce',
			'read_post'          => 'read_post',
			'delete_post'        => 'manage_woocommerce',
			'edit_posts'         => 'manage_woocommerce',
			'edit_others_posts'  => 'manage_woocommerce',
			'publish_posts'      => 'manage_woocommerce',
			'read_private_posts' => 'read_private_posts',
		);
		$args = array(
			'label'               => __( 'Fast Headless Checkout Link', 'fast' ),
			'description'         => __( 'Headless checkout links for Fast Checkout for WooCommerce.', 'fast' ),
			'supports'            => array( 'title', 'editor' ),
			'taxonomies'          => array( 'product_cat', ' product_tag' ),
			'show_in_menu'        => false,
			'menu_icon'           => 'dashicons-admin-links',
			'show_in_nav_menus'   => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => '',
			'capabilities'        => $capabilities,
			'show_in_rest'        => true,
		);

		$this->args = wp_parse_args( $args, $this->args );
	}

	/**
	 * Maybe redirect to the Fast headless checkout link.
	 */
	public function maybe_redirect() {
		// TODO: Handle the redirect.
	}
}
