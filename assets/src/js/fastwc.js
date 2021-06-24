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
			fastSelect.initProductSelect();
			fastSelect.initRedirectPageSelect();
		},

		/**
		 * Initialize the productSelect field.
		 *
		 * @returns {void}
		 */
		initProductSelect: function() {
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

		/**
		 * Initialize the redirectPageSelect field.
		 *
		 * @returns {void}
		 */
		initRedirectPageSelect: function() {
			var redirectPageSelect = $( '.fast-select--checkout-redirect-page' );

			redirectPageSelect.select2(
				{
					ajax: {
						url: ajaxurl,
						dataType: 'json',
						data: function( params ) {
							var query = {
								term : params.term,
								action : 'fastwc_search_pages',
								security: redirectPageSelect.attr('data-security'),
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
					minimumInputLength: 3,
				}
			);
		},

	};

	$( document ).ready( function() {
		fastSelect.init();
	} );

} ) ( jQuery );
