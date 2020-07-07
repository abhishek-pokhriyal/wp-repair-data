<?php

defined( 'ABSPATH' ) || exit;

$order_discounts = require WPRD_ABSPATH . 'data/missing-discounts.php';
?>

<table style="width: 100%">
	<thead>
		<tr>
			<th style="text-align: left; border-bottom: 2px solid;">#</th>
			<th style="text-align: left; border-bottom: 2px solid;">Subscription ID</th>
			<th style="text-align: left; border-bottom: 2px solid;">Status</th>
			<th style="text-align: left; border-bottom: 2px solid;">Next payment on</th>
			<th style="text-align: left; border-bottom: 2px solid;">Refund amount ($)</th>
			<th style="text-align: left; border-bottom: 2px solid;">Has refunded</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$orders = array();
	foreach ( $order_discounts as $order_id => $discount ) {
		$order                = wc_get_order( $order_id );
		$refunded             = get_post_meta( $order_id, WPRD_Refund_Missing_Discount::$meta_key, true );
		$orders[ $order_id ]  = array(
			'status'                 => wcs_get_subscription_status_name( $order->get_status() ),
			'next_payment_timestamp' => absint( get_date_from_gmt( $order->get_date( 'next_payment' ), 'U' ) ),
			'next_payment'           => $order->get_date_to_display( 'next_payment' ),
			'discount'               => $discount,
			'refunded'               => empty( $refunded ) ? 'No' : $refunded,
		);
	}

	uasort( $orders, function( $a, $b ) {
		return $a['next_payment_timestamp'] <=> $b['next_payment_timestamp'];
	} );

	$count = 0;
	foreach ($orders as $order_id => $order_data ) : ++$count;
		?>
		<tr>
			<td style="border-bottom: 1px solid;"><?php echo $count; ?></td>
			<td style="border-bottom: 1px solid;"><?php printf( '<a href="%s">%s</a>', get_edit_post_link( $order_id ), $order_id ); ?></td>
			<td style="border-bottom: 1px solid;"><?php echo $order_data['status']; ?></td>
			<td style="border-bottom: 1px solid;"><?php echo $order_data['next_payment']; ?></td>
			<td style="border-bottom: 1px solid;"><?php echo $order_data['discount']; ?></td>
			<td style="border-bottom: 1px solid;"><?php echo $order_data['refunded']; ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

