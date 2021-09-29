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
	 * Redirect post meta key.
	 *
	 * @var string
	 */
	protected $redirects_post_meta_key = 'fastwc_redirect_counter';

	/**
	 * Redirect column key.
	 *
	 * @var string
	 */
	protected $redirects_column_key = 'redirects_counter';

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

			// Display a description at the top of the headless checkout page.
			\add_filter( 'views_edit-' . $this->name, array( $this, 'views_filter' ) );

			// Display the counter column.
			\add_filter( 'manage_' . $this->name . '_posts_columns', array( $this, 'manage_columns' ) );
			\add_action( 'manage_' . $this->name . '_posts_custom_column', array( $this, 'manage_counter_column' ), 10, 2 );
		}

		\add_action( 'template_redirect', array( $this, 'maybe_redirect' ) );
	}

	/**
	 * Set the labels.
	 */
	protected function set_labels() {
		$labels = array(
			'name'                  => _x( 'Fast Headless Checkout', 'Post Type General Name', 'fast' ),
			'singular_name'         => _x( 'Fast Headless Checkout', 'Post Type Singular Name', 'fast' ),
			'menu_name'             => __( 'Headless Checkout', 'fast' ),
			'name_admin_bar'        => __( 'Headless Checkout', 'fast' ),
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
			'description'         => __( 'Headless checkout for Fast Checkout for WooCommerce.', 'fast' ),
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
	protected function get_query_args() {
		$link_id         = \get_the_ID();
		$fast_app_id     = \fastwc_get_app_id();
		$query_args      = array(
			'app_id' => $fast_app_id,
		);
		$meta_field_keys = array(
			'product_id'    => 'fastwc_product_id',
			'variant_id'    => 'fastwc_variant_id',
			'quantity'      => 'fastwc_quantity',
			'option_values' => 'fastwc_product_options',
		);

		foreach ( $meta_field_keys as $query_arg_key => $meta_field_key ) {
			$meta_field_value = \get_post_meta( $link_id, $meta_field_key, true );

			if ( ! empty( $meta_field_value ) ) {
				if ( 'option_values' === $query_arg_key ) {
					$query_args[ $query_arg_key ] = $this->get_converted_json_string( $meta_field_value );
				} else {
					$query_args[ $query_arg_key ] = $meta_field_value;
				}
			}
		}

		/**
		 * Filter the list of query args used for the Fast Headless Checkout URL.
		 *
		 * @param array $query_args      The query args used to build the Fast headless Checkout URL.
		 * @param array $meta_field_keys The list of meta field keys for fetching the query args.
		 *
		 * @return array
		 */
		return apply_filters( 'fastwc_headless_checkout_link_query_args', $query_args, $meta_field_keys );
	}

	/**
	 * Convert JSON string to a url encoded string with the following format:
	 * KEY_1,VALUE_1,KEY_2,VALUE_2,...,KEY_N,VALUE_N
	 *
	 * @param string $json_string The json string.
	 *
	 * @return string
	 */
	protected function get_converted_json_string( $json_string ) {
		$json_object = json_decode( $json_string );

		$new_string       = '';
		$new_string_parts = array();

		foreach ( $json_object as $key => $value ) {
			$new_string_parts[] = $key;
			$new_string_parts[] = $value;
		}

		if ( ! empty( $new_string_parts ) ) {
			$new_string = implode( ',', $new_string_parts );
		}

		return urlencode( $new_string );
	}

	/**
	 * Maybe handle the redirect.
	 */
	public function maybe_redirect() {
		// Do not redirect from the WP admin.
		if ( \is_admin()) {
			return;
		}

		// Do the redirect if the current URL is a checkout link URL.
		if ( \is_singular( $this->name ) ) {
			$query_args = $this->get_query_args();

			$redirect_link_base = \get_option( FASTWC_SETTING_HEADLESS_LINK_BASE, FASTWC_HEADLESS_LINK_BASE );
			$redirect_link      = \add_query_arg( $query_args, $redirect_link_base );

			/**
			 * Fires before the customer is redirected to the Fast Headless Checkout URL.
			 *
			 * @param string $redirect_link The Fast Headless Checkout URL to which the customer is redirected.
			 * @param array  $query_args    The query args that get added to the checkout link.
			 */
			\do_action( 'fastwc_before_headless_checkout_link_redirect', $redirect_link, $query_args );

			$this->increment_redirect_counter();

			\wp_redirect( $redirect_link, 301 );
			exit;
		}
	}

	/**
	 * Increment the redirect counter.
	 */
	protected function increment_redirect_counter() {
		$link_post_id = \get_the_ID();

		$current_count = \absint( \get_post_meta( $link_post_id, $this->redirects_post_meta_key, true ) );
		$current_count++;

		\update_post_meta( $link_post_id, $this->redirects_post_meta_key, $current_count );
	}

	/**
	 * Filter for the views output on the Fast Headless Checkout page in the admin.
	 *
	 * @param array $views An array of available list table views.
	 */
	public function views_filter( $views ) {
		printf(
			'<p>%s</p>',
			esc_html__( 'Generate a Fast headless checkout URL and button by selecting a product to sell along with the product variation, quantity, and product options.', 'fast' )
		);

		return $views;
	}

	/**
	 * Manage the columns in the editor.
	 *
	 * @param array $columns The list of columns.
	 *
	 * @return array
	 */
	public function manage_columns( $columns ) {
		$new_columns = array();

		$index = 0;
		foreach ( $columns as $column_key => $column ) {
			$new_columns[ $column_key ] = $column;

			if ( 1 === $index ) {
				$new_columns[ $this->redirects_column_key ] = __( 'Hits', 'fast' );
			}
			$index++;
		}

		return $new_columns;
	}

	/**
	 * Manage the counter column.
	 *
	 * @param string $column  The name of the column to manage.
	 * @param int    $post_id The ID of the current post.
	 */
	public function manage_counter_column( $column, $post_id ) {
		if ( $this->redirects_column_key !== $column ) {
			return;
		}

		$redirects = \absint( \get_post_meta( $post_id, $this->redirects_post_meta_key, true ) );

		echo \esc_html( \absint( $redirects ) );
	}
}