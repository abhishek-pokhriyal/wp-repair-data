<style type="text/css">
	.status-processing {
	    background: #c6e1c6;
	    color: #5b841b;
	}
	.status-cancelled {
		background: #e5e5e5;
		color: #777;
	}
	.status-failed {
	    background: #eba3a3;
	    color: #761919;
	}
	.status-on-hold {
	    background: #f8dda7;
	    color: #94660c;
	}
	.status-completed {
	    background: #c8d7e1;
	    color: #2e4453;
	}
	.table {
	width: 100%;
	margin-bottom: 1rem;
	color: #212529;
	}

	.table th,
	.table td {
	padding: 0.75rem;
	vertical-align: top;
	border-top: 1px solid #dee2e6;
	}

	.table thead th {
	vertical-align: bottom;
	border-bottom: 2px solid #dee2e6;
	}

	.table tbody + tbody {
	border-top: 2px solid #dee2e6;
	}
	.table-bordered {
	border: 1px solid #dee2e6;
	}

	.table-bordered th,
	.table-bordered td {
	border: 1px solid #dee2e6;
	}

	.table-bordered thead th,
	.table-bordered thead td {
	border-bottom-width: 2px;
	}
</style>

<table class="table table-bordered" cellspacing="0">
	<thead>
		<th>#</th>
		<th>Subscription</th>
		<th>Status</th>
		<th>Total missing ($)</th>
		<th>Renewal</th>
		<th>Renewed on</th>
		<th>Discount missing ($)</th>
	</thead>
	<tbody>
		<?php
		$count = 0;
		foreach ( $coupon_subscriptions as $subscription_data ) :
			$subscription   = wc_get_order( $subscription_data['ID'] );
			$related_orders = $subscription->get_related_orders();
			$parent_order   = array_pop( $related_orders );
			$total_missing  = 0;
			$renewals       = array();

			foreach ( array_reverse( $related_orders ) as $order_id ) {
				$order   = wc_get_order( $order_id );
				$status  = $order->get_status();
				$actual  = $order->get_total_discount(); 
				$calc    = self::calculate_discount( $order, 5 );
				$diff    = self::calculate_difference( $actual, $calc );

				if ( $diff >= 0 || in_array( $status, array( 'failed', 'cancelled' ) ) ) {
					continue;
				}

				$renewals[ $order_id ] = array(
					'actual'       => $actual,
					'date_created' => $order->get_date_created(),
					'calc'         => $calc,
					'status'       => $status,
				);
				$total_missing += ( $diff * -1 );
			}

			$renewal_count = count( $renewals );
			$renewal       = 0;
			foreach ( $renewals as $order_id => $order ) : ++$renewal; ?>
				<tr>
					<?php if ( 1 === $renewal ) : ++$count; ?>
						<td rowspan="<?php echo $renewal_count; ?>"><?php echo $count; ?></td>
						<td rowspan="<?php echo $renewal_count; ?>"><?php printf( '<a href="%s">%s</a>', get_edit_post_link( $subscription_data['ID'] ), $subscription_data['ID'] ); ?></td>
						<td rowspan="<?php echo $renewal_count; ?>"><?php echo wcs_get_subscription_statuses()[ $subscription_data['post_status'] ]; ?></td>
						<td rowspan="<?php echo $renewal_count; ?>"><?php echo $total_missing; ?></td>
					<?php endif; ?>
					<td><?php printf( '<a href="%1$s">%2$s</a>', get_edit_post_link( $order_id ), $order_id ); ?></td>
					<td class="status-<?php echo $order['status']; ?>"><?php echo ( new WC_DateTime( $order['date_created'] ) )->format( 'M d, Y @ h:i A' ); ?></td>
					<td class="status-<?php echo $order['status']; ?>"><?php echo $order['calc']; ?></td>
				</tr>
			<?php endforeach ?>
		<?php endforeach; ?>
	</tbody>
</table>
