<?php

defined( 'ABSPATH' ) || exit;

class WPRD_Repair {

	private static $coupon = 'subscribeandsave5';

	private static $apply_coupon_to = array(
		952009,
		944788,
		950284,
	);

	private static $order_customers = array(
		                                952009 => 10227,
		                                944788 => 14679,
		                                950284 => 17821,
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

	public static function get_query_to_reset_coupon_used_by() {
		if ( isset( $_GET['wprd_repair_coupon_usage_query'] ) && $_GET['wprd_repair_coupon_usage_query'] ) {
			if ( current_user_can( 'administrator' ) ) {
				global $wpdb;

				$coupon_id = $wpdb->get_var( sprintf( 'select * from %1$s where post_title = "%2$s"', $wpdb->posts, self::$coupon ) );

				if ( ! $coupon_id ) {
					return;
				}

				$meta_key           = '_used_by';
				$used_by_query      = sprintf(
					'SELECT * FROM %1$s WHERE post_id = %2$s and meta_key = "%3$s"',
					$wpdb->postmeta,
					absint( $coupon_id ),
					$meta_key
				);
				$coupon_used_by     = $wpdb->get_results( $used_by_query, ARRAY_A );
				$to_be_delete       = array();
				$valid_coupon_usage = array();

				foreach ( $coupon_used_by as $key => $used_by ) {
					$user_id = $used_by['meta_value'];
					$meta_id = $used_by['meta_id'];

					if ( in_array( $user_id, $valid_coupon_usage ) ) {
						$to_be_delete[] = $meta_id;
					} else {
						$valid_coupon_usage[ $meta_id ] = $user_id;
					}
				}

				$query_to_delete = sprintf(
					'SELECT * FROM %1$s WHERE post_id = %2$s AND meta_key = "%3$s" AND meta_id IN (%4$s)',
					$wpdb->postmeta,
					absint( $coupon_id ),
					$meta_key,
					implode( ',', $to_be_delete )
				);

				printf( '<code>%s</code><br>', $query_to_delete );

				$meta_to_be_deleted = $wpdb->get_results( $query_to_delete, ARRAY_A );
				$user_ids           = array_map( 'absint', array_column( $meta_to_be_deleted, 'meta_value' ) );
				$users_active_subs  = array();

				foreach ( $user_ids as $user_id ) {
					$user_subscriptions  = wcs_get_subscriptions( array(
						'subscriptions_per_page' => -1,
						'customer_id'            => $user_id,
						'subscription_status'    => array( 'active' ),
					) );

					$users_active_subs[ $user_id ] = array_keys( $user_subscriptions );
				}

				var_dump( $users_active_subs );
				die;
			}
		}
	}
}
