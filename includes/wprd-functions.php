<?php

function wprd_has_coupon( $subscription ) {
	$parent_order = wc_get_order( $subscription['post_parent'] );
	$coupons      = $parent_order->get_coupon_codes();

	return in_array( 'subscribeandsave5', $coupons);
}

/**
 * Add a discount to an Orders programmatically
 * (Using the FEE API - A negative fee)
 *
 * @since  1.0.0
 * @param  int     $order_id  The order ID. Required.
 * @param  string  $title  The label name for the discount. Required.
 * @param  float   $amount  Fixed amount (float). Required.
 * @param  string  $tax_class  The tax Class. '' by default. Optional.
 */
function wprd_order_add_discount( $order_id, $title, $amount, $tax_class = '' ) {
	$order    = wc_get_order( $order_id );
	$subtotal = $order->get_subtotal();
	$item     = new WC_Order_Item_Fee();
	$discount = $amount;
	$discount = $discount > $subtotal ? -$subtotal : -$discount;

	$item->set_tax_class( $tax_class );
	$item->set_name( $title );
	$item->set_amount( $discount );
	$item->set_total( $discount );
	$item->save();

	$order->add_item( $item );
	$order->calculate_totals( false );
	$order->save();
}
