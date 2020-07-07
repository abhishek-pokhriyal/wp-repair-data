<?php
/**
 * WP Renewal Discount.
 *
 * @since   0.0.12
 */

defined( 'ABSPATH' ) || exit;

class WPRD_Refund_Missing_Discount {
	public static function init() {
		add_action( 'woocommerce_scheduled_subscription_payment', __CLASS__ . '::conditional_hooks', 1, -1 );
	}

	public static function conditional_hooks( $subscription_id ) {
		if ( ! self::needs_refund( $subscription_id ) ) {
			return;
		}

		// Add actions/filters to subtract the discount amount from the order.
	}


	/**
	 * Check whether an order needs to refunded.
	 *
	 * @param  int $order_id
	 * @return bool
	 */
	private static function needs_refund( $order_id ) {
		$needs_refund = array_keys( require_once WPRD_ABSPATH . 'data/missing-discounts.php' );

		return in_array( $order_id, $needs_refund );
	}
}

WPRD_Refund_Missing_Discount::init();
