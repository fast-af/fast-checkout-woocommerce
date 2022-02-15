/**
 * Handle Payment Gateway Selector.
 *
 * event: payment_method_selected
 */

( function( $ ) {

	"use strict";

	var fastGateway = {
		/**
		 * Hide the checkout button if the Fast gateway is selected.
		 *
		 * @returns {void}
		 */
		maybeHideCheckout: function() {
			var selectedPaymentMethod = $( '.woocommerce-checkout input[name="payment_method"]:checked' ).attr( 'id' ),
				placeOrder = $( 'div.place-order' );

			if ( selectedPaymentMethod == 'payment_method_Fast' && placeOrder ) {
				placeOrder.hide();
			} else {
				placeOrder.show();
			}
		},
	};

	$( document ).ready( function() {
		fastGateway.maybeHideCheckout();
	} );

	$( document.body ).on( 'payment_method_selected', function() {
		fastGateway.maybeHideCheckout();
	} );

} ) ( jQuery );
