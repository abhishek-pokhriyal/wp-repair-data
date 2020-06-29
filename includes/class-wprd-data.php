<?php
/**
 * Data handler.
 *
 * Handles the database ($wpdb) interaction.
 *
 * @since   0.0.1
 * @version 0.0.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Data class.
 */
class WPRD_Data {

	/**
	 * Get wcsatt_scheme for each of the subscription line item in the system.
	 *
	 * @return array
	 */
	public static function get_subscription_items() {
		global $wpdb;

		$query = sprintf(
			'
			SELECT subscription_id,
				   placed_on_gmt,
				   status,
				   subscription_items.order_item_id as item_id,
				   meta_id,
				   meta_key,
				   meta_value
			FROM
			  (SELECT ID AS "subscription_id",
					  post_date_gmt AS "placed_on_gmt",
					  post_status AS "status",
					  items.order_item_id
			   FROM
				 (SELECT *
				  FROM %1$s
				  WHERE post_type = "shop_subscription") subscriptions
			   LEFT JOIN
				 (SELECT *
				  FROM %2$s
				  WHERE order_item_type = "line_item") items ON subscriptions.ID = items.order_id) subscription_items
			LEFT JOIN
			  (SELECT *
			   FROM %3$s
			   WHERE meta_key = "_wcsatt_scheme") item_scheme ON subscription_items.order_item_id = item_scheme.order_item_id
			',
			$wpdb->posts,
			$wpdb->prefix . 'woocommerce_order_items',
			$wpdb->prefix . 'woocommerce_order_itemmeta'
		);

		return $wpdb->get_results( $query, ARRAY_A );
	}

	public static function insert_order_itemmeta( $order_item_id, $meta_key, $meta_value ) {
		global $wpdb;

		$table = $wpdb->prefix . 'woocommerce_order_itemmeta';
		$data  = array(
			'order_item_id' => $order_item_id,
			'meta_key'      => $meta_key,
			'meta_value'    => $meta_value,
		);

		return $wpdb->insert( $table, $data );
	}

	public static function update_order_itemmeta( $meta_id, $meta_key, $meta_value ) {
		global $wpdb;

		$table = $wpdb->prefix . 'woocommerce_order_itemmeta';
		$data  = array(
			'meta_key'   => $meta_key,
			'meta_value' => $meta_value,
		);
		$where = array( 'meta_id' => $meta_id );

		return $wpdb->update( $table, $data, $where );
	}

	public static function get_coupon_id( string $coupon ) {
		global $wpdb;

		$post_type = 'shop_coupon';

		return $wpdb->get_var(
			$wpdb->prepare(
				'select ID from %1$s where post_type = "%2$s" and post_title = "%3$s"',
				$wpdb->posts,
				$post_type,
				$coupon
			)
		);
	}

	public static function unuse_coupon( $coupon_id, $user_id ) {
		global $wpdb;

		$wpdb->delete(
			$wpdb->postmeta,
			array(
				'post_id'    => $coupon_id,
				'meta_key'   => '_used_by',
				'meta_value' => $user_id,
			)
		);
	}
}
