<style type="text/css">
	.status-danger {
	    background: #eba3a3;
	    color: #761919;
	}
	.status-wc-on-hold {
	    background: #f8dda7;
	    color: #94660c;
	}
	.status-wc-completed {
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
		<th>Total</th>
		<th>Actual discount</th>
		<th>Correct discount</th>
		<th>Action</th>
	</thead>
	<tbody>
		<?php
		$count = 0;
		foreach ( $coupon_subscriptions as $subscription_data ) :
			$subscription  = wc_get_order( $subscription_data['ID'] );
			$status        = $subscription_data['post_status'];
			$text_to_match = 'Correct discount given';
			$actual        = $subscription->get_total_discount();
			$calc          = self::calculate_discount( $subscription, 5 );
			$class         = strpos( self::calculate_difference( $actual, $calc ), $text_to_match ) === false ? 'status-danger' : ''; ?>
			<tr>
				<td class="<?php echo $class; ?>"><?php echo ++$count; ?></td>
				<td class="<?php echo $class; ?>">
					<?php printf( '<a href="%s">%s</a>', get_edit_post_link( $subscription_data['ID'] ), $subscription_data['ID'] ); ?>
				</td>
				<td class="<?php echo $class; ?>"><?php echo ( new WC_DateTime( $subscription->get_date_created() ) )->format( 'M d, Y @ h:i A' ); ?></td>
				<td class="<?php echo $class; ?>"><?php echo wcs_get_subscription_statuses()[ $status ]; ?></td>
				<td class="<?php echo $class; ?>"><?php echo $subscription->get_formatted_order_total(); ?></td>
				<td class="<?php echo $class; ?>"><?php echo $actual; ?></td>
				<td class="<?php echo $class; ?>"><?php echo $calc; ?></td>
				<td class="<?php echo $class; ?>"><?php echo self::calculate_difference( $actual, $calc ); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>