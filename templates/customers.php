<?php
global $wpdb;

$table_customers = $wpdb->prefix . 'sms_customers';
$table_sales = $wpdb->prefix . 'sms_sales';
$table_invoices = $wpdb->prefix . 'sms_invoices';
$table_dues = $wpdb->prefix . 'sms_dues';

$customers_sql = "
	SELECT
		c.customer_id,
		c.name,
		c.phone,
		c.email,
		c.address,
		c.date,
		(
			SELECT COUNT(*)
			FROM {$table_sales} s
			WHERE s.customer_id = c.customer_id
		) AS total_sales,
		(
			SELECT COALESCE(SUM(s.net_total), 0)
			FROM {$table_sales} s
			WHERE s.customer_id = c.customer_id
		) AS total_sales_amount,
		(
			SELECT COALESCE(SUM(d.remaining_amount), 0)
			FROM {$table_dues} d
			WHERE d.customer_saler_id = c.customer_id
			AND LOWER(d.due_type) = 'sale'
			AND LOWER(d.status) = 'open'
		) AS open_due_amount,
		(
			SELECT i.invoice_no
			FROM {$table_sales} s
			INNER JOIN {$table_invoices} i ON i.invoice_id = s.invoice_id
			WHERE s.customer_id = c.customer_id
			ORDER BY s.sale_id DESC
			LIMIT 1
		) AS last_invoice_no
	FROM {$table_customers} c
	ORDER BY c.customer_id DESC
";

$customers = $wpdb->get_results($customers_sql);
$total_customers = is_array($customers) ? count($customers) : 0;
?>
<div class="customers-page">
	<div class="heading-search customers_heading">
		<h4 class="product-table-heading">Customers (<?php echo esc_html($total_customers); ?>)</h4>
		<?php
			include_component('search-filter', [
				'customer_name' => 'Customer Name',
				'phone' => 'Phone',
				'email' => 'Email',
				'invoice_no' => 'Last Invoice',
			]);
		?>
	</div>

	<div class="table-wrap scrollelement">
		<table class="customers_listing">
			<thead>
				<tr>
					<th class="sr-Number">#</th>
					<th class="customer_name">Name</th>
					<th class="phone">Phone</th>
					<th class="email">Email</th>
					<th class="address">Address</th>
					<th class="total_sales">Total Sales</th>
					<th class="total_sales_amount">Sales Amount</th>
					<th class="open_due_amount">Open Due</th>
					<th class="invoice_no">Last Invoice</th>
					<th class="registered_date">Registered</th>
				</tr>
			</thead>
			<tbody>
				<?php if(!$customers): ?>
					<tr>
						<td colspan="10">No customers found.</td>
					</tr>
				<?php else: ?>
					<?php $sr = 1; foreach($customers as $customer): ?>
						<?php
							$customer_id = intval($customer->customer_id);
							$customer_name = trim((string) $customer->name) !== '' ? $customer->name : '--';
							$phone = trim((string) $customer->phone) !== '' ? $customer->phone : '--';
							$email = trim((string) $customer->email) !== '' ? $customer->email : '--';
							$address = trim((string) $customer->address) !== '' ? $customer->address : '--';
							$total_sales = intval($customer->total_sales);
							$total_sales_amount = number_format((float) $customer->total_sales_amount, 2);
							$open_due_amount = number_format((float) $customer->open_due_amount, 2);
							$last_invoice_no = $customer->last_invoice_no ? intval($customer->last_invoice_no) : '--';
							$registered_date = trim((string) $customer->date) !== '' ? date('j M Y', strtotime($customer->date)) : '--';
						?>
						<tr data-id="<?php echo esc_attr($customer_id); ?>">
							<td class="sr-Number"><?php echo esc_html($sr++); ?></td>
							<td class="customer_name"><?php echo esc_html($customer_name); ?></td>
							<td class="phone"><?php echo esc_html($phone); ?></td>
							<td class="email"><?php echo esc_html($email); ?></td>
							<td class="address"><?php echo esc_html($address); ?></td>
							<td class="total_sales"><?php echo esc_html($total_sales); ?></td>
							<td class="total_sales_amount"><?php echo esc_html($total_sales_amount); ?></td>
							<td class="open_due_amount"><?php echo esc_html($open_due_amount); ?></td>
							<td class="invoice_no">
								<?php if($last_invoice_no !== '--'): ?>
									<a href="<?php echo esc_url(admin_url('admin.php?page=invoice_details&invoice_no=' . $last_invoice_no . '&customer_id=' . $customer_id)); ?>" class="view-detail-btn">
										<?php echo esc_html($last_invoice_no); ?>
									</a>
								<?php else: ?>
									<?php echo esc_html($last_invoice_no); ?>
								<?php endif; ?>
							</td>
							<td class="registered_date"><?php echo esc_html($registered_date); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

