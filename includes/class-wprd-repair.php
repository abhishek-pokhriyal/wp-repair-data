<?php

defined( 'ABSPATH' ) || exit;

class WPRD_Repair {

	private static $coupon = 'subscribeandsave5';

	private static $apply_coupon_to = array(
		// 949159, 946451,
		// 948627, 948232, 946767, 948247,
		// 948065, 946918, 948428, 947729, 949800, 946615,
		// 946521, 945532, 945844, 946787, 946771, 946960, 945226,
		// , 945919, 947843, 946715, 948979, 946570, 946942, 945408,
		// 945885, 946504, 948008, 949411, 947303, 944951, 948180,
		// 947056, 946553, 945384, 948200, , 946588, 945904, 946062,
		// 947415, 946483, 948194, 947962, 948992, 945417, 947983,
		// 945944, 947424, 947639, 944887, 948367, 945761, 946019, 945659,
		// 945964, 946105, 945947, 948666, 947549, 945955, 947301, 946843,
		// 946963, 945373, 945337, 945353, 945145, 947018, 944908, 945277,
		// 945784, 947178, 946470, 946242, 945092, 944811, 945495, 947243,
		// 945749, 947750, 947704, 945441, 944927, 944849, 946003, 946383,
		// 946037, 947219, 945642, 945451, 945118, 946701, 945016,
		// 944825, 944965, 945633, 945244, 945860, 945513, 945170, 945073,
		// 944788
		944982,
		945062,
		945885,
		946330,
		946598,
		946636,
		946953,
		946970,
		947134,
		947408,
		947524,
		947539,
		947849,
		948215,
		948350,
		948781,
		948931,
		949058,
		949294,
		949591,
		949825,
		950048,
	);

	private static $order_customers = array(
		                                944982 => 6053,
		                                945062 => 22329,
		                                945885 => 22229,
		                                946330 => 22587,
		                                946598 => 22636,
		                                946636 => 22256,
		                                946953 => 22706,
		                                946970 => 22552,
		                                947134 => 21893,
		                                947408 => 22789,
		                                947524 => 22803,
		                                947539 => 22806,
		                                947849 => 22850,
		                                948215 => 19137,
		                                948350 => 22933,
		                                948781 => 23022,
		                                948931 => 23050,
		                                949058 => 23073,
		                                949294 => 22757,
		                                949591 => 23149,
		                                949825 => 8258,
		                                950048 => 22342,
		                                // 949159 => 21571,
		                                // 948627 => 20778,
		                                // 948232 => 22914,
		                                // 946767 => 22298,
		                                // 948247 => 14406,
		                                // 948065 => 21144,
		                                // 946918 => 22701,
		                                // 948428 => 22956,
		                                // 947729 => 15455,
		                                // 949800 => 20082,
		                                // 946615 => 22642,
		                                // 946521 => 21541,
		                                // 945532 => 22435,
		                                // 945844 => 22508,
		                                // 946787 => 22681,
		                                // 946771 => 22076,
		                                // 946960 => 10637,
		                                // 945226 => 22362,
		                                // 945919 => 22531,
		                                // 947843 => 13359,
		                                // 946715 => 22664,
		                                // 948979 => 15985,
		                                // 946570 => 22630,
		                                // 946942 => 22131,
		                                // 945408 => 21952,
		                                // 946504 => 21421,
		                                // 948008 => 21523,
		                                // 949411 => 23056,
		                                // 947303 => 22764,
		                                // 944951 => 21826,
		                                // 948180 => 22494,
		                                // 946451 => 22448,
		                                // 947056 => 22332,
		                                // 946553 => 22626,
		                                // 945384 => 22398,
		                                // 948200 => 22906,
		                                // 946588 => 22633,
		                                // 945904 => 22526,
		                                // 946062 => 18357,
		                                // 944788 => 14679,
		                                // 947415 => 22791,
		                                // 946483 => 21376,
		                                // 948194 => 17018,
		                                // 947962 => 22862,
		                                // 948992 => 23060,
		                                // 945417 => 22407,
		                                // 947983 => 22865,
		                                // 945944 => 7356 ,
		                                // 947424 => 22436,
		                                // 947639 => 22828,
		                                // 944887 => 22292,
		                                // 948367 => 22761,
		                                // 945761 => 22480,
		                                // 946019 => 22544,
		                                // 945659 => 22459,
		                                // 945964 => 21852,
		                                // 946105 => 19309,
		                                // 945947 => 22535,
		                                // 948666 => 21791,
		                                // 947549 => 22809,
		                                // 945955 => 22169,
		                                // 947301 => 22762,
		                                // 946843 => 1334 ,
		                                // 946963 => 5966 ,
		                                // 945373 => 22397,
		                                // 945337 => 22385,
		                                // 945353 => 22389,
		                                // 945145 => 21196,
		                                // 947018 => 19797,
		                                // 944908 => 19953,
		                                // 945277 => 21825,
		                                // 945784 => 22488,
		                                // 947178 => 22741,
		                                // 946470 => 21078,
		                                // 946242 => 22580,
		                                // 945092 => 21258,
		                                // 944811 => 22284,
		                                // 945495 => 21964,
		                                // 947243 => 22751,
		                                // 945749 => 22218,
		                                // 947750 => 21725,
		                                // 947704 => 22840,
		                                // 945441 => 22410,
		                                // 944927 => 21104,
		                                // 944849 => 18508,
		                                // 946003 => 22541,
		                                // 946383 => 22330,
		                                // 946037 => 21476,
		                                // 947219 => 22749,
		                                // 945642 => 22455,
		                                // 945451 => 22415,
		                                // 945118 => 22344,
		                                // 946701 => 22658,
		                                // 945016 => 22317,
		                                // 944825 => 21730,
		                                // 944965 => 17800,
		                                // 945633 => 22099,
		                                // 945244 => 20127,
		                                // 945860 => 22514,
		                                // 945513 => 22428,
		                                // 945170 => 19708,
		                                // 945073 => 21725,
		                            );

	/**
	 * Start the repair process.
	 */
	public static function start() {
		if ( isset( $_GET['wprd_start'] ) && $_GET['wprd_start'] ) {

			if ( ! current_user_can( 'administrator' ) ) {
				return;
			}

			if ( 'yes' !== get_option( 'wprd_repair_mode_enabled', 'no' ) ) {
				wp_die(
					__( 'Cannot repair subscription schemes. Please enable repair mode first.', 'repair-subscription-scheme' ),
					__( 'Repair mode disabled', 'repair-subscription-scheme' )
				);
			}

			$faulty_subscription_items = WPRD_Helpers::get_faulty_subscriptions();

			/**
			 * The number of new rows for meta_key = '_wcsatt_scheme'
			 * inserted to table woocommerce_order_itemmeta.
			 *
			 * Cases of missing subscription scheme.
			 *
			 * @var integer
			 */
			$inserted = 0;

			/**
			 * The number of rows updated for meta_key = '_wcsatt_scheme'
			 * inserted to table woocommerce_order_itemmeta.
			 *
			 * Cases of invalid subscription scheme.
			 *
			 * @var integer
			 */
			$updated = 0;

			foreach ( $faulty_subscription_items as $subscription_id => $subscription ) {
				foreach ( $subscription['items'] as $item ) {
					if ( empty( $item['id'] ) ) {
						continue;
					}

					if ( empty( $item['meta_id'] ) ) {
						// `_wcsatt_scheme` meta does not exist for the subscription line item.
						// Insert a new row for the line item meta.
						$insert_success = WPRD_Data::insert_order_itemmeta( $item['id'], '_wcsatt_scheme', $item['correct_scheme'] );

						$inserted += false === $insert_success ? 0 : $insert_success;
					} else {
						// `_wcsatt_scheme` exist for the subscription line item but it's wrong.
						// Update the meta_value for the line item meta.
						$update_success = WPRD_Data::update_order_itemmeta( $item['meta_id'], '_wcsatt_scheme', $item['correct_scheme'] );

						$updated += false === $update_success ? 0 : $update_success;
					}
				}
			}

			$message  = sprintf( 'Added missing <code>_wcsatt_scheme</code> to <strong>%d</strong> line items.<br>', $inserted );
			$message .= sprintf( 'Fixed invalid <code>_wcsatt_scheme</code> in <strong>%d</strong> line items.<br>', $updated );

			wp_die( $message, 'Subscription schemes fixed' );
		}
	}

	public static function apply_subscribeandsave5_coupon() {
		if ( isset( $_GET['wprd_apply_coupon'] ) && $_GET['wprd_apply_coupon'] ) {

			if ( ! current_user_can( 'administrator' ) ) {
				return;
			}

			if ( 'yes' !== get_option( 'wprd_repair_mode_enabled', 'no' ) ) {
				wp_die(
					__( 'Cannot apply coupon. Please enable repair mode first.', 'repair-subscription-scheme' ),
					__( 'Repair mode disabled', 'repair-subscription-scheme' )
				);
			}

			add_filter( 'woocommerce_coupon_is_valid_for_product', '__return_true', 99 );

			$message = '<ol>';

			foreach ( self::$apply_coupon_to as $order_id ) {
				try {
					$order = wc_get_order( $order_id );

					if ( ! $order ) {
						throw new Exception( __( 'Invalid order', 'woocommerce' ) );
					}

					$calculate_tax_args = array(
						'country'  => $order->get_shipping_country() ?? '',
						'state'    => $order->get_shipping_state() ?? '',
						'postcode' => $order->get_shipping_postcode() ?? '',
						'city'     => $order->get_shipping_city() ?? '',
					);

					$user_id_arg = self::$order_customers[ $order_id ];
					$coupon_id   = absint( WPRD_Data::get_coupon_id( self::$coupon ) );

					WPRD_Data::unuse_coupon( $coupon_id, $user_id_arg );

					// Remove coupon.
					$order->remove_coupon( wc_format_coupon_code( wp_unslash( self::$coupon ) ) );
					$order->calculate_taxes( $calculate_tax_args );
					$order->calculate_totals( false );

					// Then add it again.
					$result = $order->apply_coupon( wc_format_coupon_code( wp_unslash( self::$coupon ) ) );

					if ( is_wp_error( $result ) ) {
						throw new Exception( html_entity_decode( wp_strip_all_tags( $result->get_error_message() ) ) );
					}

					$order->calculate_taxes( $calculate_tax_args );
					$order->calculate_totals( false );

					$message .= sprintf(
						'<li>Subscription <strong><a href="%1$s" target="_blank">%2$s</a></strong>. Coupon <code>%3$s</code> applied. Order total: %4$s</li>',
						get_edit_post_link( $order_id ),
						$order_id,
						self::$coupon,
						get_post_meta( $order_id, '_order_total', true )
					);

				} catch ( Exception $e ) {
					$message .= printf(
						'<li>Subscription <strong><a href="%1$s" target="_blank">%2$s</a></strong>. %3$s</li>',
						get_edit_post_link( $order_id ),
						$order_id,
						$e->getMessage()
					);
				}
			}

			$message .= '</ol>';

			wp_die( $message, sprintf( 'Reapply %s coupon', self::$coupon ) );
		}
	}


	public static function show_subscriptions_needs_coupon() {
		if ( isset( $_GET['wprd_show_subscriptions'] ) && $_GET['wprd_show_subscriptions'] ) {

			if ( ! current_user_can( 'administrator' ) ) {
				return;
			} ?>

			<table style="width: 100%; border: 1px solid gray;" cellspacing="0">
				<thead>
					<tr>
						<th style="border: 1px solid gray;">#</th>
						<th style="border: 1px solid gray;">Subscription</th>
						<th style="border: 1px solid gray;">Coupon(s)</th>
						<th style="border: 1px solid gray;">Subtotal</th>
						<th style="border: 1px solid gray;">Total</th>
						<th style="border: 1px solid gray;">Discount</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( self::$apply_coupon_to as $index => $order_id ) : $order = wc_get_order( $order_id ); ?>
						<tr>
							<td style="text-align: center; border: 1px solid gray;"><?php echo ++$index; ?></td>
							<td style="text-align: center; border: 1px solid gray;"><?php printf( '<a href="%s" target="_blank">%s</a>', get_edit_post_link( $order_id ), $order_id ); ?></td>
							<td style="text-align: center; border: 1px solid gray;">
								<?php
								$coupons = array_map( function( $coupon_code ) { return '<code>' . $coupon_code . '</code>'; }, $order->get_coupon_codes() );
								echo implode( '<br>', $coupons );
								?>
							</td>
							<td style="text-align: center; border: 1px solid gray;"><strong><?php echo $order->get_subtotal_to_display(); ?></strong></td>
							<td style="text-align: center; border: 1px solid gray;"><strong><?php echo $order->get_formatted_order_total(); ?></strong></td>
							<td style="text-align: center; border: 1px solid gray;"><strong><?php echo $order->get_discount_to_display(); ?></strong></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php
			wp_die( '', 'Subscriptions and coupons' );
		}
	}
}
