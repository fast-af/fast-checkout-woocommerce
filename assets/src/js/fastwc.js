/**
 * Fast Checkout for WooCommerce admin JS.
 */
/* global ajaxurl */

( function( $ ) {

	"use strict";

	var fastSelect = {

		/**
		 * Initialize the fastSelect object.
		 *
		 * @returns {void}
		 */
		init: function() {
			var productSelect = $( '.fast-select--hide-button-products' );

			productSelect.select2(
				{
					ajax: {
						url: ajaxurl,
						data: function( params ) {
							var query = {
								term : params.term,
								action : 'woocommerce_json_search_products',
								security: productSelect.attr('data-security'),
							};

							return query;
						},
						processResults: function( data ) {
							var terms = [];

							if ( data ) {
								$.each( data, function( id, text ) {
									terms.push( {
										id: id,
										text: text
									} );
								} );
							}

							return { results: terms };
						},
						cache: true,
					},
				}
			);
		},

	};

	$( document ).ready( function() {
		fastSelect.init();
	} );

} ) ( jQuery );
