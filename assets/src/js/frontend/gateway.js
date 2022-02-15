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
			var fastPaymentMethod = $( 'li.payment_method_Fast > div.payment_method_Fast' ),
				placeOrder = $( 'div.place-order' );

			if ( fastPaymentMethod && fastPaymentMethod.is(':visible') && placeOrder ) {
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
