<?php
/**
 * WPRD Subscriptions.
 *
 * @since   0.0.1
 */

defined( 'ABSPATH' ) || exit;

class WPRD_Helpers {
	public static function get_faulty_subscriptions() {
		$subscription_items  = WPRD_Data::get_subscription_items();
		$faulty_subscription = array();

		foreach ( $subscription_items as $item ) {
			$subscription_id = $item['subscription_id'];
			$saved_scheme    = $item['meta_value'];
			$correct_scheme  = self::get_correct_subscription_scheme( $subscription_id );

			if ( $correct_scheme !== $saved_scheme ) {
				$faulty_subscription[ $subscription_id ]['items'][] = array(
					'id'             => $item['item_id'],
					'meta_id'        => $item['meta_id'],
					'saved_scheme'   => $saved_scheme,
					'correct_scheme' => $correct_scheme,
				);

				if ( isset( $faulty_subscription[ $subscription_id ]['status'] ) && isset( $faulty_subscription[ $subscription_id ]['placed_on_gmt'] ) ) {
					continue;
				}

				$faulty_subscription[ $subscription_id ]['status']         = $item['status'];
				$faulty_subscription[ $subscription_id ]['placed_on_gmt']  = $item['placed_on_gmt'];
			}

		}

		return $faulty_subscription;
	}

	/**
	 * Get the correct subscription scheme.
	 *
	 * @param  int $subscription_id
	 * @return string
	 */
	public static function get_correct_subscription_scheme( $subscription_id ) {
		return sprintf(
			'%s_%s',
			get_post_meta( $subscription_id, '_billing_interval', true ),
			get_post_meta( $subscription_id, '_billing_period', true )
		);
	}
}
