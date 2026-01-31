<div class="purchase-page">

    <div class="heading-search">

        <button id="addNewPurchase" class="add-new">Add New Purchase</button>

        <input type="search" name="" id="search-product" placeholder="Search Product Name">

    </div>


	<div class="product-form-wrap" style="display: none;">

		<h4 class="product-table-heading">

			<span>Add Purchase</span>

			<span class="closeUserForm">&times;</span>

		</h4>

		<form id='addPurchaseForm'>

			<div class="form-inner">
				<table class="addNewPurchaseFormTable">
					<thead>
						<tr>
							<th>Purchase Info</th>
							<th>Product Info</th>
							<th>Cart Details</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="pf_col_1">
								<table class="anpPurchaseInfoTable">
									<tbody>
										<tr>
											<td>
												<div class="anpFieldWrap">
													<label for="purchase_invoice">Purchase Invoice</label>
													<input type="text" name="purchase_invoice" id="purchase_invoice" value="<?php echo getNextPurchaseInvoiceNo(); ?>" readonly />
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<div class="anpFieldWrap">
													<label>Total Cart Amount</label>
													<input type="number" oninput="this.value = Math.abs(this.value)" id="anpTotalCartAmount" name="purchase_total_payment" value="0" readonly />
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<div class="anpFieldWrap">
													<label for="payment_status">Payment Status</label>
													<select class="payment_status" name="payment_status" id="payment_status">
														<option value="Paid">Paid</option>
														<option value="Unpaid">Unpaid</option>
														<option value="Partially Paid">Partially Paid</option>
													</select>
												</div>
											</td>
										</tr>
										<tr class="anpRowPartiallyPaid">
											<td>
												<div class="anpFieldsContainer" style="display: none;">
													<div class="anpFieldWrap">
														<label for="payment_status">Paid</label>
														<input type="number" id="anpPaid" min="0" max="0" />
													</div>
													<div class="anpFieldWrap">
														<label for="payment_status">Remaining</label>
														<input type="number" id="anpRemaining" min="0" readonly />
													</div>
												</div>
											</td>
										</tr>
										<tr class="anpRowPaymentMethod">
											<td>
												<div class="anpFieldWrap">
													<label for="payment_method">Payment Method</label>
													<select class="payment_method" name="payment_method" id="payment_method">
														<option value="cash">Cash</option>
														<option value="bank_transfer">Bank Transfer</option>
														<option value="check">Check</option>
														<option value="jazzcash">JazzCash</option>
														<option value="easypaisa">EasyPaisa</option>
														<option value="other">Other</option>
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<div class="anpFieldWrap">
													<label for="description">Description</label>
													<textarea id="description" name="description" class="description" rows="3" placeholder="Save description like transaction id (TID) or Check No..."></textarea>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
							<td class="pf_col_2">
								<div id="purchase_form_fields_container">
									<div class="purchase_form_fields_group active">
										<table class="anpProductInfoTable">
											<tbody>
												<tr>
													<td>
														<div class="anpFieldWrap purchase_product_select">
															<label>Product</label>
															<select name="product_id" class="product_id needValidation">
																<option disabled selected>Select Product</option>
																<?php
																global $wpdb;
																$table = $wpdb->prefix . 'sms_products';
																$sql = "SELECT * FROM $table";
																$products = $wpdb->get_results($sql);
																if(count($products) > 0){
																	$i = 1;
																	foreach($products as $product){
																		$product_id = $product->product_id;
																		$product_name = $product->product_name; ?>
																		<option value="<?php echo $product_id; ?>"><?php echo $product_name; ?></option>
																	<?php }
																}
																?>
															</select>
														</div>
													</td>
												</tr>

												<tr>
													<td>
														<div class="anpFieldWrap purchase_product_manufacturer">
															<label>Manufacturer</label>
															<input type="text" name="manufacturer" class="manufacturer" readonly>
														</div>
													</td>
												</tr>

												<tr>
													<td>
														<div class="anpFieldWrap purchase_product_vendor">
															<label>Vendor</label>
															<input type="text" name="vendor" class="vendor" readonly>
														</div>
													</td>
												</tr>

												<tr>
													<td>
														<div class="anpFieldWrap purchase_product_rate">
															<div style="display: flex;justify-content: flex-start !important;gap: 10px;">
																<label style="width: auto; display: inline-block;">Rate</label> <a href="javascript:void(0)" class="btnUpdateRateToggler">Want to update?</a>
															</div>
															<div>
																<div class="purchase_rate_wrapper">
																	<label style="display: none;" class="needToggle">Purchase Rate</label>
																	<input type="number" oninput="this.value = Math.abs(this.value)" name="purchase_rate" class="purchase_rate" readonly>
																</div>
																<div class="sale_rate_wrapper needToggle" style="display: none;">
																	<label>Sale Rate</label>
																	<input type="number" oninput="this.value = Math.abs(this.value)" name="sale_rate" class="sale_rate" style="margin-top: 5px;">
																</div>
																
																<button class="btnUpdateRate sms_btn sms_btn_info needToggle" type="button" style="display: none;">Update Rate</button>
																<button class="btnCancelUpdateRate sms_btn sms_btn_danger needToggle" type="button" style="display: none;">Cancel</button>
															</div>
														</div>
													</td>
												</tr>

												<tr>
													<td>
														<div class="anpFieldWrap purchase_product_quantity">
															<label>Quantity</label>
															<input type="number" oninput="this.value = Math.abs(this.value)" name="quantity" class="quantity needValidation" value="1" min="1" step="1" required>
														</div>
													</td>
												</tr>

												<tr>
													<td>
														<div class="anpFieldWrap purchase_product_payment">
															<label>Price</label>
															<input type="number" oninput="this.value = Math.abs(this.value)" name="payment" class="payment" readonly>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div id="hidden_fields_group" style="display: none;">
									<div class="purchase_form_fields_group active">
										<table class="anpProductInfoTable">
											<tbody>
												<tr>
													<td>
														<div class="anpFieldWrap purchase_product_select">
															<label>Product</label>
															<select name="product_id" class="product_id needValidation">
																<option disabled selected>Select Product</option>
																<?php
																global $wpdb;
																$table = $wpdb->prefix . 'sms_products';
																$sql = "SELECT * FROM $table";
																$products = $wpdb->get_results($sql);
																if(count($products) > 0){
																	$i = 1;
																	foreach($products as $product){
																		$product_id = $product->product_id;
																		$product_name = $product->product_name; ?>
																		<option value="<?php echo $product_id; ?>"><?php echo $product_name; ?></option>
																	<?php }
																}
																?>
															</select>
														</div>
													</td>
												</tr>

												<tr>
													<td>
														<div class="anpFieldWrap purchase_product_manufacturer">
															<label>Manufacturer</label>
															<input type="text" name="manufacturer" class="manufacturer" readonly>
														</div>
													</td>
												</tr>

												<tr>
													<td>
														<div class="anpFieldWrap purchase_product_vendor">
															<label>Vendor</label>
															<input type="text" name="vendor" class="vendor" readonly>
														</div>
													</td>
												</tr>

												<tr>
													<td>
														<div class="anpFieldWrap purchase_product_rate">
															<div style="display: flex;justify-content: flex-start !important;gap: 10px;">
																<label style="width: auto; display: inline-block;">Rate</label> <a href="javascript:void(0)" class="btnUpdateRateToggler">Want to update?</a>
															</div>
															<div>
																<div class="purchase_rate_wrapper">
																	<label style="display: none;" class="needToggle">Purchase Rate</label>
																	<input type="number" oninput="this.value = Math.abs(this.value)" name="purchase_rate" class="purchase_rate" readonly>
																</div>
																<div class="sale_rate_wrapper needToggle" style="display: none;">
																	<label>Sale Rate</label>
																	<input type="number" oninput="this.value = Math.abs(this.value)" name="sale_rate" class="sale_rate" style="margin-top: 5px;">
																</div>
																
																<button class="btnUpdateRate sms_btn sms_btn_info needToggle" type="button" style="display: none;">Update Rate</button>
																<button class="btnCancelUpdateRate sms_btn sms_btn_danger needToggle" type="button" style="display: none;">Cancel</button>
															</div>
														</div>
													</td>
												</tr>

												<tr>
													<td>
														<div class="anpFieldWrap purchase_product_quantity">
															<label>Quantity</label>
															<input type="number" oninput="this.value = Math.abs(this.value)" name="quantity" class="quantity needValidation" value="1" min="1" step="1" required>
														</div>
													</td>
												</tr>

												<tr>
													<td>
														<div class="anpFieldWrap purchase_product_payment">
															<label>Price</label>
															<input type="number" oninput="this.value = Math.abs(this.value)" name="payment" class="payment" readonly>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>

							</td>
							<td class="pf_col_3">
								<table class="anpCartDetailsTable">
									<tbody>
										<tr>
											<td>
												<div class="purchase-sidebar-area">
													<h4 style="color: cyan;">Cart Items</h4>
													<ol class="purchase-sidebar-list">
														<!-- Example List item
															<li data-product_id="" class="purchase-sidebar-list-item"></li>
														-->
													</ol>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="purchase_form_footer submit-wrap">
				<div class="purchase_form_footer_btns">
					<button id="addNewPurchaseFormRow" class="sms_btn sms_btn_info" type="button" data-default-btn-text="<span>+</span> Add More"><span>+</span> Add More</button>
					<input type="submit" id="add_stock" value="Add Stock" class="save-btn sms_btn sms_btn_success">
				</div>
			</div>

		</form>

	</div>

	<div class="table-wrap scrollelement">

		<table class="purchases_listing">

			<thead>

				<tr>

					<th class="sr-Number">#</th>
					<th class="sale_man">Sale Man</th>

					<th class="purchase_product_invoice">Purchase Invoice</th>

					<th class="purchase_product_payment">Payment</th>
					<th class="purchase_product_payment_paid">Paid</th>
					<th class="purchase_product_payment_remaining">Remaining</th>

					<th class="purchase_product_payment_status">Payment Status</th>
					<th class="purchase_product_description">Description</th>

					<th class="purchase_product_date">Date</th>

					<th class="purchase_product_action">View Details</th>

				</tr>

			</thead>

			<tbody class="purchase-tbody">

				<?php 

				global $wpdb;

				$table = $wpdb->prefix . 'sms_purchases';

				$sql = "SELECT * FROM $table ORDER BY purchase_id DESC";

				$purchases = $wpdb->get_results($sql);

				if(count($purchases) > 0){

					$j = 1;

					foreach($purchases as $purchase){

						$purchase_id = $purchase->purchase_id;

						$saleman_id = $purchase->saleman_id;
						$saleman_name = get_saleman($saleman_id)->name;
						$vendor = $purchase->vendor;

						$quantity = $purchase->quantity;

						$rate = $purchase->rate;

						$total_payment = $purchase->total_payment;
						$paid = $purchase->paid;
						$due = $purchase->due;

						$payment_status = $purchase->payment_status;  

						$payment_method = $purchase->payment_method;

						$description = $purchase->description ? $purchase->description : 'N/A';
						$purchase_invoice = $purchase->purchase_invoice ? $purchase->purchase_invoice : 'N/A';

						$date = date('M j, Y', strtotime($purchase->date));

				?>		                        

						<tr data-id="<?php echo $purchase_id; ?>">

							<td><?php echo $j++; ?></td>

							<td><?php echo $saleman_name; ?></td>
							<td><?php echo $purchase_invoice; ?></td>

							<td><span>Rs. </span><span data-payment="<?php echo $total_payment; ?>"><?php echo number_format($total_payment); ?></span></td>
							<td><span>Rs. </span><span data-paid-payment="<?php echo $paid; ?>"><?php echo number_format($paid); ?></span></td>
							<td><span>Rs. </span><span data-remaining-payment="<?php echo $due; ?>"><?php echo number_format($due); ?></span></td>

							<td><?php echo $payment_status; ?></td>

							<td><?php echo $description; ?></td>

							<td><?php echo $date; ?></td>

							<td><a href="admin.php?page=purchase_invoice_details&invoice_no=<?php echo $purchase_invoice; ?>" class="purchae_view_detail_btn">View Detail ↗</a></td>

						</tr>

					<?php

					}

				}else{ ?>

					<tr>

						<td colspan="7">No purchases/stocks found.</td>

					</tr>                                

				<?php

				}

			?>

			</tbody>

		</table>

	</div>
