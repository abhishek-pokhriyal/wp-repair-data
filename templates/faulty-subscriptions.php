<style>
	.table {
		width: 100%;
		border: 1px solid black;
	}
	.table-cell {
		border: 1px solid black;
	}
</style>
<table class="table" cellspacing="0">
	<thead>
		<tr>
			<th class="table-cell">#</th>
			<th class="table-cell">Subscription</th>
			<th class="table-cell">Started on (UTC)</th>
			<th class="table-cell">Status</th>
			<th class="table-cell">Line items</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 0;
		foreach ( $faulty_subscription_items as $subscription_id => $subscription ) : ?>
			<tr>
				<td valign="top" class="table-cell"><?php echo ++$i; ?></td>
				<td valign="top" class="table-cell">
					<?php printf( '<a href="%s" target="_blank">%s</a>', esc_url( get_edit_post_link( $subscription_id ) ), $subscription_id ); ?>
				</td>
				<td valign="top" class="table-cell"><?php echo $subscription['placed_on_gmt']; ?></td>
				<td valign="top" class="table-cell"><?php echo wcs_get_subscription_status_name( $subscription['status'] ); ?></td>
				<td valign="top" class="table-cell">
					<ol>
						<?php foreach ( $subscription['items'] as $item ) : ?>
							<li><?php printf( 'Saved scheme: <code>%s</code>; Correct scheme: <code>%s</code>', $item['saved_scheme'], $item['correct_scheme'] ); ?></li>
						<?php endforeach; ?>
					</ol>
				</td valign="top" class="table-cell">
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
