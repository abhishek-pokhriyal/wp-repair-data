<?php

function wprd_has_coupon( $subscription ) {
	$parent_order = wc_get_order( $subscription['post_parent'] );
	$coupons      = $parent_order->get_coupon_codes();

	return in_array( 'subscribeandsave5', $coupons);
}