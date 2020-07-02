<style type="text/css">
	.status-processing {
	    background: #c6e1c6;
	    color: #5b841b;
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

<table class="table table-bordered">
	<thead>
		<th>#</th>
		<th>Subscription #</th>
		<th>Date created UTC</th>
		<th>Status</th>
		<th>Renewals</th>
	</thead>
	<tbody>
		<?php
		$count = 0;
		foreach ( $coupon_subscriptions as $subscription_data ) :
			$subscription = wc_get_order( $subscription_data['ID'] ); ?>
			<tr>
				<td><?php echo ++$count; ?></td>
				<td>
					<?php printf( '<a href="%s">%s</a>', get_edit_post_link( $subscription_data['ID'] ), $subscription_data['ID'] ); ?>
				</td>
				<td><?php echo $subscription_data['post_date_gmt']; ?></td>
				<td><?php echo wcs_get_subscription_statuses()[ $subscription_data['post_status'] ]; ?></td>
				<td>
					<?php
					$related_orders = $subscription->get_related_orders();
					$parent_order   = array_pop( $related_orders );

					if ( ! empty( $related_orders ) ) :
					?>
						<table>
							<tr>
								<th>Renewal #</th>
								<th>Created on</th>
								<th>Status</th>
								<th>Total</th>
								<th>Actual discount</th>
								<th>Correct discount</th>
								<th>Status</th>
							</tr>
							<?php
							foreach ( array_reverse( $related_orders ) as $order_id ) :
								$order  = wc_get_order( $order_id );
								$status = $order->get_status() ?>
								<tr>
									<td class="status-<?php echo $status; ?>"><?php printf( '<a href="%1$s">%2$s</a>', get_edit_post_link( $order_id ), $order_id ); ?></td>
									<td class="status-<?php echo $status; ?>"><?php echo ( new WC_DateTime( $order->get_date_created() ) )->format( 'M d, Y @ h:i A' ); ?></td>
									<td class="status-<?php echo $status; ?>"><?php echo wc_get_order_status_name( $status  ); ?></td>
									<td class="status-<?php echo $status; ?>"><?php echo $order->get_formatted_order_total(); ?></td>
									<td class="status-<?php echo $status; ?>"><?php echo $actual = $order->get_total_discount(); ?></td>
									<td class="status-<?php echo $status; ?>"><?php echo $calc = self::calculate_discount( $order, 5 ); ?></td>
									<td class="status-<?php echo $status; ?>"><?php echo self::calculate_difference( $actual, $calc ); ?></td>

								</tr>
							<?php endforeach ?>
						</table>
					<?php else: ?>
						<strong>No renewals were created!</strong>
					<?php endif;?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>