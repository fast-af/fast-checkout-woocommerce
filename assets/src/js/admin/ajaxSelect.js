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
			fastSelect.initTestModeUserSelect();
		},

		/**
		 * Get the ajax object.
		 *
		 * @param {Object} element The Select2 element.
		 * @param {String} action  The query action.
		 *
		 * @return {Object}
		 */
		ajaxObject: function( element, action ) {
			return {
				url: ajaxurl,
				data: function( params ) {
					var query = {
						term : params.term,
						action : action,
						security: element.attr('data-security'),
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
			};
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
					ajax: fastSelect.ajaxObject( productSelect, 'woocommerce_json_search_products' ),
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
					ajax: fastSelect.ajaxObject( redirectPageSelect, 'fastwc_search_pages' ),
					minimumInputLength: 3,
				}
			);
		},

		/**
		 * Initialize the Test Mode User select field.
		 *
		 * @returns {void}
		 */
		 initTestModeUserSelect: function() {
		 	var testModeUserSelect = $( '.fast-select--test-mode-users' );

			testModeUserSelect.select2(
				{
					ajax: fastSelect.ajaxObject( testModeUserSelect, 'fastwc_search_users' ),
					minimumInputLength: 3,
				}
			);
		 },

	};

	$( document ).ready( function() {
		fastSelect.init();
	} );

} ) ( jQuery );
