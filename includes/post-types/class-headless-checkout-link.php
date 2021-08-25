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

			// Set it so that 
			$fastwc_headless_post_type = \get_post_type_object( $this->name );
			$fastwc_headless_post_type->template_lock = 'all';

			// Register meta fields.
			$meta_fields = array(
				'fastwc_product_id'      => 'integer',
				'fastwc_variant_id'      => 'integer',
				'fastwc_quantity'        => 'integer',
				'fastwc_product_options' => 'string',
			);
			foreach ( $meta_fields as $meta_field_key => $meta_field_type ) {
				register_post_meta(
					$this->name,
					$meta_field_key,
					array(
						'show_in_rest' => true,
						'single'       => true,
						'type'         => $meta_field_type,
					)
				);
			}
		}

		\add_action( 'template_redirect', array( $this, 'maybe_redirect' ) );
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

		// Add rewrite rules for the headless link URL's. The 'slug' is the part of the URL
		// that preceeds the link slug. The link slug is unique to each checkout link.
		// A link will be structured like this: https://www.example.com/{slug}/{link-slug}
		$rewrite = array(
			'slug'       => $this->slug,
			'with_front' => true,
			'pages'      => false,
			'feeds'      => false,
		);

		// Set the block template for the post type.
		$template = array(
			array(
				'fastwc/fast-headless', // Block type name.
				array(), // Default block attributes.
			),
		);

		// Set the args for this post type.
		$args = array(
			'label'               => __( 'Fast Headless Checkout Link', 'fast' ),
			'description'         => __( 'Headless checkout links for Fast Checkout for WooCommerce.', 'fast' ),
			'supports'            => array( 'title', 'editor', 'custom-fields' ),
			'taxonomies'          => array( 'product_cat', ' product_tag' ),
			'show_in_menu'        => false,
			'menu_icon'           => 'dashicons-admin-links',
			'show_in_nav_menus'   => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'show_in_rest'        => true,
			'template'            => $template,
		);

		// Merge the args with the default args.
		$this->args = wp_parse_args( $args, $this->args );
	}

	/**
	 * Get headless link meta fields.
	 *
	 * @return array
	 */
	function get_query_args() {
		$link_id         = \get_the_ID();
		$fast_app_id     = \fastwc_get_app_id();
		$query_args      = array(
			'app_id' => $fast_app_id,
		);
		$meta_field_keys = array(
			'product_id'      => 'fastwc_product_id',
			'variant_id'      => 'fastwc_variant_id',
			'quantity'        => 'fastwc_quantity',
			'product_options' => 'fastwc_product_options',
		);

		foreach ( $meta_field_keys as $query_arg_key => $meta_field_key ) {
			$meta_field_value = \get_post_meta( $link_id, $meta_field_key, true );

			if ( ! empty( $meta_field_value ) ) {
				$query_args[ $query_arg_key ] = $meta_field_value;
			}
		}

		return $query_args;
	}

	/**
	 * Maybe handle the redirect.
	 */
	function maybe_redirect() {
		// Do not redirect from the WP admin.
		if ( \is_admin()) {
			return;
		}

		// Do the redirect if the current URL is a checkout link URL.
		if ( \is_singular( $this->name ) ) {
			$query_args = $this->get_query_args();

			$redirect_link_base = 'https://go.fast.co';
			$redirect_link      = \add_query_arg( $query_args, $redirect_link_base );

			\wp_redirect( $redirect_link, 301 );
			exit;
		}
	}
}
