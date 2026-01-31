<?php

	$date = date('Y-m-d');
	global $wpdb;
	$table = $wpdb->prefix . 'sms_sales';
	$sql = "SELECT * FROM $table ORDER BY sale_id DESC";
	$sales = $wpdb->get_results($sql);
	$total_sales = count($sales) ? count($sales) : 0; 
	$total_sale_amount = 0;
	$profit = $wpdb->get_var("SELECT SUM(profit) FROM {$table} WHERE date = '{$date}'") ?? 0;
	echo "Date: " . $date;
?>
<div class="sales-page">
	<div class="ouer-salesType">
		<select id="salesType" name="salesType">
			<option value="">All Sales</option>
			<option value="Cash Sale">Cash Sales</option>
			<option value="Credit Sale">Credit Sales</option>
			<option value="Partially Paid">Partially Paid</option>
			<option value="Due Payment">Due Payment Sales</option>
		</select>
		<label>Shifts
			<select id="shiftsDropdown" name="shiftsDropdown">
				<option value="">All Shifts</option>
				<option value="Morning">Morning</option>
				<option value="Evening">Evening</option>
				<option value="Night">Night</option>
			</select>
		</label>
		<button id="salesCalculatorToggler" type="button">Show All Calculations</button>
		<div class="search-section">
	        <input type="number" name="" id="invoice_number" placeholder="Invoice Number">
	    </div>
		<div class="sales_total_profit_display">
			<h2 class="sales_total_profit_text"><strong>Today's Profit:</strong> <span><?php echo $profit; ?></span></h2>
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
					<th class="due_amount">Due Amount</th>
					<th class="sales_profit">Profit</th>
					<th class="sales_man">Sales Man</th>
					<th class="sales_type">Sale Type</th>
					<th class="shift">Shift</th>
					<th class="sales_time">Time</th>
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
							$sale_type = substr(ucwords(str_replace(['-', '_'], ' ', $sale->sale_type)), 0);
							$sale_date = date('j M Y', strtotime($sale->date));
							$sale_profit = intval($sale->profit);
							$sale_time = $sale->sale_time;
							$total_sale_amount += $net_total;

							$sales_person_id = $sale->sales_man;
							$sales_person = '';
							if($sales_person_id){
								$first_name = ucwords(get_user_meta($sales_person_id, 'first_name', true));
								$last_name  = ucwords(get_user_meta($sales_person_id, 'last_name', true));
								if($first_name){
									$sales_person = $first_name . ' ' . $last_name;
								}else{
									$userdata = get_userdata($sales_person_id);
									$sales_person = $userdata->display_name;
								}
							}else{
								$sales_person = 'N/A';
							}

							// Get the due record by sale id
							$due = fbm_get_due_by_referer_id($sale_id);

							// Determine shift based on sale time
							$shift_time = explode(':', (explode(' ', $sale_time)[1]))[0];
							$shift = '';
							if($shift_time >= '6' && $shift_time <= '14'){
								$shift = 'Morning';
							}elseif($shift_time >= '14' && $shift_time <= '22'){
								$shift = 'Evening';
							}elseif($shift_time > '22'){
								$shift = 'Night';
							}
							// $shift_poped = array_pop($shift);

								
							?>
							<tr data-id="<?php echo $sale_id; ?>" data-date="<?= $sale->date; ?>">
								<td class="sr-Number"><?php echo $i++; ?></td>
								<td  class="customer_name"><?php echo $customer_name ? $customer_name : '--WALKING-CUSTOMER--'; ?></td>
								<td class="invoice_no"><?php echo $invoice_no; ?></td>
								<td class="sales_quantity"><?php echo $quantity; ?></td>
								<td class="amount"><?php echo $net_total; ?></td>
								<td class="due_amount"><?php echo $due ? intval($due->remaining_amount) : 0; ?></td>
								<td class="profit"><?php echo $sale_profit; ?></td>
								<td class="sales_person" data-user_id="<?php echo $sales_person_id; ?>" data-sales_person="<?php echo $sales_person; ?>"><?php echo $sales_person; ?></td>
								<td  class="sales_type"><?php echo $sale_type; ?></td>
								<td  class="shift"><?php echo $shift; ?></td>
								<td  class="sale_time"><?php echo $sale_time; ?></td>
								<td class="user_action">
									<a href="admin.php?page=invoice_details&invoice_no=<?= $invoice_no; ?>&customer_id=<?= $customer_id; ?>" class='view-detail-btn' data-id="<?php echo $sale_id; ?>">View More Detail</a>
								</td>
							</tr>
							<?php
						}
					}else{ ?>
						<tr>
							<td colspan="11">No Sales found.</td>
						</tr>                               
						<?php
						}
					?>
			</tbody>
		</table>
	</div>
	<div id="salesCalculator" class="amount-cell" style="display: none;">
		<div class="salesCalculatorWrapper">
			<div><span class="btnKbdCloser"><kbd>Esc</kbd> key to close</span><span type="button" id="salesCalculatorCloser">&times;</span></div>
			<h4 class="product-table-heading">Sales Calculator</h4>
			<div class="salesCalculatorBanner">Today's Sale</div>
			<div class="sc_btns">
				<button class="viewTodaysSaleBtn sms_btn sms_btn_info" type="button" onclick="window.location.reload()">View Todays Sale</button>
			</div>
			<h5 style="margin-top: 10px; font-weight: 600;">Filter Sales</h5>
			<label for="">From Date 
				<input type="date" name="" id="from_date">
			</label>
			<label for="">To Date 
				<input type="date" name="" id="to_date">
			</label>
			<label for="">Total Sale 
				<input type="number" name="" id="total_amount" value="<?php echo $total_sale_amount; ?>" readonly>
				<small style="color: green;"><i>Profile Rs. <span id="sc_profit">0</span></i></small>
			</label>
			<label for="">Cash Sale 
				<input type="number" name="" placeholder="0" id="sale_amount" readonly>
			</label>
			<label for="">Credit Sale
				<input type="number" name="" placeholder="0" id="credit_amount" readonly>
			</label>
			<div class="partial_paid_sales">
				<h5 style="margin-top: 10px; font-weight: 600;">Partially Paid Sale</h5>
				<label for="">Received <input type="number" name="" placeholder="0" id="partially_recieved_amount" readonly></label>
				<label for="">Remaining <input type="number" name="" placeholder="0" id="partially_remaining_amount" readonly></label>
			</div>
			<h6 class="totalEntries" style="padding-top:10px;">Total Sales Entries (<span class="totalEntriesCount"><?php echo $total_sales; ?></span>)
			</h6>
		</div>
	</div>
</div>