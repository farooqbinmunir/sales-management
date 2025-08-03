<div class="sales-page">
	<?php
		global $wpdb;
		$table = $wpdb->prefix . 'sms_sales';
		$sql = "SELECT * FROM $table ORDER BY date DESC";
		$sales = $wpdb->get_results($sql);
		$total_sales = count($sales); 
		$total_sale_amount = 0;
	?>
		<div class="ouer-salesType">
			<select id="salesType" name="salesType">
				<option value="all-sales">All Sales</option>
				<option value="cash-sales">Cash Sales</option>
				<option value="credit-sales">Credit Sales</option>
			</select>
			<div class="search-section">
                <input type="text" name="" id="invoice_number" placeholder="Invoice Number">
            </div>
		</div>
		<div class="table-wrap scrollelement">
			<table class="sale_table">
				<thead>
					<tr>
						<th class="sr-Number">#</th>
						<th class="customer_name">Customer Name</th>
						<th class="invoice_no">Invoice</th>
						<th class="sales_quantity">Quantity</th>
						<th class="amount">Amount</th>
						<th class="sales_type">Sale Type</th>
						<th class="sales_date">Date</th>
						<th class="user_action">Action</th>
					</tr>
				</thead>
				<tbody>
						<?php
						if($total_sales > 0){
							$i = 1;
							foreach($sales as $sale){
								$sale_id = $sale->sale_id;
								$customer_id = $sale->customer_id;
								$table_customers = $wpdb->prefix . 'sms_customers';
								$customer_name = ucwords($wpdb->get_var("SELECT name FROM $table_customers WHERE customer_id = $customer_id"));
								$invoice_id = $sale->invoice_id;
								$table_invoices = $wpdb->prefix . 'sms_invoices';
								$invoice_no = $wpdb->get_var("SELECT invoice_no FROM $table_invoices WHERE invoice_id = $invoice_id");
								$quantity = $sale->quantity;
								$net_total = $sale->net_total;
								$sale_type = substr(ucwords(str_replace(['-', '_'], ' ', $sale->sale_type)), 0, -1);
								$sale_date = date('j M Y', strtotime($sale->date));
								
								$total_sale_amount += $net_total;
								?>
								<tr data-id="<?php echo $sale_id; ?>" data-date="<?= $sale->date; ?>">
									<td class="sr-Number"><?php echo $i++; ?></td>
									<td  class="customer_name"><?php echo $customer_name; ?></td>
									<td class="invoice_no"><?php echo $invoice_no; ?></td>
									<td class="sales_quantity"><?php echo $quantity; ?></td>
									<td class="amount"><?php echo $net_total; ?></td>
									<td  class="sales_type"><?php echo $sale_type; ?></td>
									<td  class="sales_date"><?php echo $sale_date; ?></td>
									<td class="user_action">
										<a href="admin.php?page=invoice_details&invoice_no=<?= $invoice_no; ?>&customer_id=<?= $customer_id; ?>" class='view-detail-btn' data-id="<?php echo $sale_id; ?>">View More Detail</a>
									</td>
								</tr>
								<?php
							}
						}else{ ?>
							<tr>
								<td colspan="6">No Sales found.</td>
							</tr>                               
							<?php
							}
						?>
				</tbody>
			</table>
		</div>
		<div class="amount-cell">
			<h4 class="product-table-heading">Amount Cell</h4>
			<label for="">From Date <input type="date" name="" id="from_date"></label>
			<label for="">To Date <input type="date" name="" id="to_date"></label>
			<label for="">Total Sale <input type="number" name="" id="total_amount" value="<?php echo $total_sale_amount; ?>" readonly></label>
			<label for="">Cash Sale <input type="text" name="" placeholder="0" id="sale_amount" readonly></label>
			<label for="">Credit Sale<input type="text" name="" placeholder="0" id="credit_amount" readonly></label>
			<h6 style="padding-top:10px;">Total Sales Entries (<?= $total_sales; ?>)</h6>
		</div>