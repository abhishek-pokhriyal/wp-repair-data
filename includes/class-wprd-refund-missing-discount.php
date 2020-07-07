<?php
/**
 * WP Renewal Discount.
 *
 * @since   1.0.0
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

		add_filter( 'wcs_renewal_order_created', __CLASS__ . '::adjust_missing_discount', 99, 2 );
	}

	public static function adjust_missing_discount( $renewal_order, $subscription ) {
		$missing_discount = self::get_missing_discount_amount( $subscription->get_id() );
		$original_total   = $renewal_order->get_total();

		/**
		 * @todo: Confirm that the $original_total contains the 5% discount.
		 */
		$adjusted_total   = $original_total - $missing_discount;

		$renewal_order->set_total( $adjusted_total );
		$renewal_order->save();
	}

	private static function get_missing_discount_amount( $order_id ) {
		$order_discounts = require_once WPRD_ABSPATH . 'data/missing-discounts.php';

		if ( array_key_exists( $order_id, $order_discounts ) ) {
			return floatval( $order_discounts[ $order_id ] );
		}

		return 0;
	}

	/**
	 * Check whether an order needs a refund amount.
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
