<?php
/**
 * WP Renewal Discount.
 *
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

class WPRD_Refund_Missing_Discount {
	/**
	 * The type of the date we will be using anywhere to record the
	 * datetime while recording the event.
	 *
	 * @var string
	 */
	private static $date_type = 'mysql';

	/**
	 * Post meta key that is added to the renewal order right after it is created.
	 *
	 * Used to determine whether the missing discount has been added previously or not.
	 *
	 * @var string
	 */
	public static $meta_key = '_refunded_outstanding_discount';

	public static function init() {
		// add_action( 'woocommerce_scheduled_subscription_payment', __CLASS__ . '::conditional_hooks', -1 );
		add_action( 'wp_loaded', __CLASS__ . '::show_refunding_plan' );
	}

	public static function show_refunding_plan() {
		if ( isset( $_GET['show_refunding_plan'] ) && $_GET['show_refunding_plan'] ) {

			if ( ! current_user_can( 'administrator' ) ) {
				return;
			}

			include_once WPRD_ABSPATH . 'templates/refund-plan.php';
			die;
		}
	}

	public static function conditional_hooks( $subscription_id ) {
		if ( ! self::needs_refund( $subscription_id ) ) {
			return;
		}

		add_filter( 'wcs_renewal_order_created', __CLASS__ . '::adjust_missing_discount', 99, 2 );
	}

	public static function adjust_missing_discount( $renewal_order, $subscription ) {
		$subscription_id  = $subscription->get_id();
		$missing_discount = self::get_missing_discount_amount( $subscription_id );
		wprd_order_add_discount( $renewal_order, 'subscribeandsave5-previous', $missing_discount );
		update_post_meta( $subscription_id, self::$meta_key, current_time( self::$date_type, true ) );

		return $renewal_order;
	}

	private static function get_missing_discount_amount( $order_id ) {
		$order_discounts = require WPRD_ABSPATH . 'data/missing-discounts.php';

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
		$needs_refund     = array_keys( require WPRD_ABSPATH . 'data/missing-discounts.php' );
		$already_refunded = get_post_meta( $order_id, self::$meta_key, true );

		return in_array( $order_id, $needs_refund ) && empty( $already_refunded );
	}
}

WPRD_Refund_Missing_Discount::init();
