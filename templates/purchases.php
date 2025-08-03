<div class="purchase-page">

    <div class="heading-search">

        <button id="addNewPurchase" class="add-new">Add New Purchase</button>

        <input type="search" name="" id="search-product" placeholder="Search Product Name">

    </div>


	<div class="product-form-wrap">

		<h4 class="product-table-heading">

			Add Purchase

			<span class="closeUserForm">&times;</span>

		</h4>

		<form action="" id='add_stock_form'>

			<div class="form-inner">

				<div class="purchase_product_select">

					<label for="product_id">Product</label>

                	<select id="product_id" name="product_id">

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

				<div class="">

					<label for="purchase_product_vendor">Vendor</label>

					<input type="text" name="purchase_product_vendor" id="purchase_product_vendor" readonly>

				</div>

				<div class="purchase_product_rate_wrapper">

					<div style="display: flex;justify-content: flex-start !important;gap: 10px;">
						<label for="purchase_rate" style="width: auto; display: inline-block;">Rate</label> <a href="javascript:void(0)" id="btnUpdateRateToggler">Want to update?</a>
					</div>
					<div>
						<input type="number" name="purchase_rate" id="purchase_rate" readonly>
						<button id="btnUpdateRate" class="sms_btn" type="button" style="display: none;">Update Rate</button>	
					</div>
				</div>

				<div class="">

					<label for="purchase_quantity">Quantity</label>

					<input type="number" name="purchase_quantity" id="purchase_quantity" class="purchase_quantity" required>

				</div>

				<div class="">

					<label for="purchase_payment">Payment</label>

					<input type="number" name="purchase_payment" id="purchase_payment" readonly>

				</div>

				<div class="">

					<label for="purchase_payment_status">Payment Status</label>

					<select id="purchase_payment_status" name="purchase_payment_status">

						<option value="paid">Paid</option>

						<option value="unpaid">Unpaid</option>

						<option value="partially_paid">Partially Paid</option>

					</select>

				</div>

				<div class="">

					<label for="purchase_payment_method">Payment Method</label>

					<select id="purchase_payment_method" name="purchase_payment_method">

						<option value="cash">Cash</option>

						<option value="bank_transfer">Bank Transfer</option>

						<option value="check">Check</option>

						<option value="jazzcash">JazzCash</option>

						<option value="easypaisa">EasyPaisa</option>

						<option value="other">Other</option>

					</select>

				</div>

				<div class="purchase_product_description">

					<label for="purchase_description">Description</label>

					<textarea name="purchase_description" id="purchase_description" rows="3" placeholder="Save description like transaction id (TID) or Check No..."></textarea>

				</div>

			</div>

			<div class="submit-wrap">

				<input type="submit" id="add_stock" value="Add Stock" class="save-btn">

			</div>

		</form>

	</div>

	<div class="table-wrap scrollelement">

		<table>

			<thead>

				<tr>

					<th class="sr-Number">#</th>

					<th class="purchase_product_name">Product</th>

					<th class="purchase_product_vendor">Vendor</th>

					<th class="purchase_product_quantity">Rate</th>

					<th class="purchase_product_unit_price">Quantity</th>

					<th class="purchase_product_payment">Payment</th>

					<th class="purchase_product_payment_status">Payment Status</th>

					<th class="purchase_product_date">Date</th>

					<th class="purchase_product_action">Action</th>

				</tr>

			</thead>

			<tbody class="purchase-tbody">

				<?php 

				global $wpdb;

				$table = $wpdb->prefix . 'sms_purchases';

				$sql = "SELECT * FROM $table";

				$purchases = $wpdb->get_results($sql);

				if(count($purchases) > 0){

					$j = 1;

					foreach($purchases as $purchase){

						$purchase_id = $purchase->purchase_id;

						$product_id = $purchase->product_id;

						$vendor = $purchase->vendor;

						$quantity = $purchase->quantity;

						$rate = $purchase->rate;

						$total_payment = $purchase->total_payment;

						$payment_status = $purchase->payment_status;  

						$payment_method = $purchase->payment_method;

						$description = $purchase->description;

						$date = date('M j, Y', strtotime($purchase->date));

				?>		                        

						<tr data-id="<?php echo $purchase_id; ?>">

							<td><?php echo $j++; ?></td>

							<td>

								<?php

									$single_product = get_product($product_id);

									if($single_product){

										echo $single_product->product_name;

									}

								?>

							</td>

							<td><?php echo $vendor; ?></td>

							<td><span>Rs. </span><span><?php echo $rate; ?></span></td>

							<td><?php echo $quantity; ?></td>

							<td><span>Rs. </span><span><?php echo $total_payment; ?></span></td>

							<td><?php echo $payment_status; ?></td>

							<td><?php echo $date; ?></td>

							<td><button type="button" class='edit_purchase_btn quick_edit_btn edit_btn sms_btn' data-id="<?= $purchase_id;  ?>">Edit</button></td>

						</tr>

						<tr class="edit_form" data-id="<?= $purchase_id;  ?>">



							<td colspan="9">

								<div class="quick_edit_form edit_purchase_form" style="display: none;">

									<div class="edit_product_form_fields_container">

										<h4 class="product-table-heading">

											Update Purchase

											<span class="close_quick_edit_popup">&times;</span>

										</h4>

										<form class='quick_edit_form_wrap'>

											<div class="form-inner">

												<div class="purchase_product_select">

													<label for="product_id">Product</label>

													<select id="product_id_<?php echo $purchase_id; ?>" name="product_id" inert>

														<option>None</option>

														<?php 

														global $wpdb;

														$table = $wpdb->prefix . 'sms_products';

														$sql = "SELECT * FROM $table";

														$products = $wpdb->get_results($sql);

														if(count($products) > 0){

															$i = 1;

															foreach($products as $product){

																$productId = $product->product_id;

																$product_name = $product->product_name;

																if(intval($product_id) == intval($productId)){ ?>

																	<option value="<?php echo $productId; ?>" selected><?php echo $product_name; ?></option>

																<?php

																}else{ ?>

																	<option value="<?php echo $productId; ?>"><?php echo $product_name; ?></option>

																<?php

																} 

															}

														}

														?>

													</select>

												</div>

												<div class="">

													<label for="purchase_product_vendor">Vendor</label>

													<input type="text" name="purchase_product_vendor" id="purchase_product_vendor" value="<?= $vendor; ?>">

												</div>

												<div class="">

													<label for="purchase_rate">Rate</label>

													<input type="number" name="purchase_rate" id="purchase_rate" value="<?= $rate; ?>">

												</div>

												<div class="">

													<label for="purchase_quantity">Quantity</label>

													<input type="number" name="purchase_quantity" id="purchase_quantity" class="purchase_quantity" value="<?= $quantity; ?>" required>

												</div>

												<div class="">

													<label for="purchase_payment">Payment</label>

													<input type="number" name="purchase_payment" id="purchase_payment" value="<?= $total_payment; ?>" readonly>

												</div>

												<div class="">

													<label for="purchase_payment_status">Payment Status</label>

													<select id="purchase_payment_status" name="purchase_payment_status" value="<?= $payment_status; ?>">

														<option value="paid">Paid</option>

														<option value="unpaid">Unpaid</option>

														<option value="partially_paid">Partially Paid</option>

													</select>

												</div>

												<div class="">

													<label for="purchase_payment_method">Payment Method</label>

													<select id="purchase_payment_method" name="purchase_payment_method" value="<?= $payment_method; ?>">

														<option value="cash">Cash</option>

														<option value="bank_transfer">Bank Transfer</option>

														<option value="check">Check</option>

														<option value="jazzcash">JazzCash</option>

														<option value="easypaisa">EasyPaisa</option>

														<option value="other">Other</option>

													</select>

												</div>

												<div class="purchase_product_description">

													<label for="purchase_description">Description</label>

													<textarea name="purchase_description" id="purchase_description" rows="3" placeholder="Save description like transaction id (TID) or Check No..."><?= $description; ?></textarea>

												</div>



												<div class="submit-wrap">

													<button type="button" class="update_purchase sms_btn_filled quick_edit_update_btn" data-id="<?= $purchase_id; ?>">Update</button>

												</div>

											</div>

										</form>

									</div>

								</div>

							</td>

						</tr>

					<?php

					}

				}else{ ?>

					<tr>

						<td colspan="6">No purchases/stocks found.</td>

					</tr>                                

				<?php

				}

			?>

			</tbody>

		</table>

	</div>