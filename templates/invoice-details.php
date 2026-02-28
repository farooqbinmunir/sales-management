<div class="invoice-details-page">
	<button class="sms_back_btn" onclick="history.back()">&#10094; Back</button>
    <div class="invoice-details-wrap">
        
        <?php 
            $invoice_id = null;
            if(isset($_GET['invoice_no']) && isset($_GET['customer_id'])){
                $invoice_no = intval(sanitize_text_field($_GET['invoice_no']));
                $customer_id = intval(sanitize_text_field($_GET['customer_id']));
                ?>

                <div class="invoice_details_header">
                    <h1 style="text-align: center; margin-block: 30px; font-size: 30px; text-transform: uppercase;"><strong>Invoice</strong></h1>
                    <h5><strong>Invoice No: </strong><span><?php echo $invoice_no; ?></span></h5>
                </div>

                <div class="invoice_details_body" style="margin-top: 50px;">
                    <div class="invoice_details_body_wrap" style="padding: 20px; box-shadow: 0 0 10px -5px black;">
                        <h2 id="store_name" style="text-align: center;"><strong>Shahzad Store</strong></h2>
                        <?php 

                        global $wpdb;
                        $table_invoices = $wpdb->prefix . 'sms_invoices';
                        $invoice_query = $wpdb->prepare("SELECT * FROM $table_invoices WHERE invoice_no = %d", $invoice_no);
                        $invoice = $wpdb->get_row($invoice_query);
                        if($invoice != null){

                            $invoice_id = $invoice->invoice_id;
                            $invoice_data = maybe_unserialize($invoice->invoice_data);
                            $invoice_data = is_array($invoice_data) ? $invoice_data : [];
                            $date = $invoice->date;

                            $customer = get_customer($customer_id);
                            $customer_name = $customer ? ucwords($customer->name) : '--WALKING-CUSTOMER--';

                            ?>

                            <table>
                                <tbody>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Customer Name</th>
                                        <td id="inv_customer_name" style="padding: 0 20px 0 0;"><?php echo esc_html($customer_name); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Invoice No</th>
                                        <td id="inv_invoice_no" style="padding: 0 20px 0 0;"><?php echo esc_html($invoice_no); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <table id="inv_products">
                                <thead>
                                    <tr>
                                        <th style="padding: 0 20px 5px 0;">Items</th>
                                        <th style="padding: 0 20px 5px 0;">Unit Price</th>
                                        <th style="padding: 0 20px 5px 0;">Qty</th>
                                        <th style="padding: 0 20px 5px 0;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($invoice_data as $product_item) {
                                        if(!is_array($product_item)){
                                            continue;
                                        }

                                        $product_name = ucwords((string) ($product_item['prod_name'] ?? ''));
                                        $quantity = (float) ($product_item['prod_quantity'] ?? 0);
                                        $amount = (float) ($product_item['prod_total_amount'] ?? 0);
                                        $unit_price = $quantity > 0 ? ($amount / $quantity) : 0;

                                        ?>

                                        <tr>
                                            <td class="inv_product_name"><?php echo esc_html($product_name); ?></td>
                                            <td class="inv_product_unit_price"><?php echo esc_html(number_format((float)$unit_price, 2)); ?></td>
                                            <td class="inv_product_quantity"><?php echo esc_html(number_format((float)$quantity, 2)); ?></td>
                                            <td class="inv_product_amount"><?php echo esc_html(number_format((float)$amount, 2)); ?></td>
                                        </tr>
                                    <?php
                                     } ?>
                                </tbody>
                            </table>
                            <hr>
                            <table>
								<?php 
								$sale_details = get_sale_by_invoice_id($invoice_id);
								$total_items = count($invoice_data);
								$gross_total = $sale_details ? (float)$sale_details->gross_total : 0;
								$discount = $sale_details ? (float)$sale_details->discount : 0;
								$net_total = $sale_details ? (float)$sale_details->net_total : 0;
								$sale_type = $sale_details ? (string)$sale_details->sale_type : '';
								$payment_method = $sale_details ? (string)$sale_details->payment_method : '';

								$sales_person = 'N/A';
								$sales_person_id = $sale_details ? intval($sale_details->sales_man) : 0;
								if($sales_person_id > 0){
									$userdata = get_userdata($sales_person_id);
									if($userdata){
										$sales_person = $userdata->display_name;
									}
								}

								$due = null;
								$due_id = 0;
								$due_payments = [];
								$due_amount = 0;
								$total_received = $net_total;
								if($sale_details){
									$sale_id = intval($sale_details->sale_id);
									$due = fbm_get_due_by_referer_id($sale_id, 'sale');
									$due_id = $due ? intval($due->id) : 0;
									$due_payments = $due_id > 0 ? fbm_dues_get_payments($due_id) : [];
									$due_amount = $due ? (float)$due->remaining_amount : 0;
									$total_received = $due ? (float)$due->paid_amount : $net_total;
								}

								$account_status = $due_amount > 0 ? 'Open' : 'Closed';
								$sale_type_normalized = strtolower(trim((string)$sale_type));
								if($sale_type_normalized === 'credit sale' && $due_amount > 0){
									$payment_status = 'Unpaid';
								}elseif($due_amount > 0){
									$payment_status = 'Partially Paid';
								}else{
									$payment_status = 'Paid';
								}
                                ?>
                                <tbody>
                                    <tr>
                                        <th style="padding: 0 20px 0 0; width: 50%;">Total Items</th>
                                        <td id="inv_total_items" style="padding: 0 20px 0 0;"><?php echo esc_html($total_items); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Gross Total:</th>
                                        <td id="inv_gross_total" style="padding: 0 20px 0 0;"><?php echo esc_html(number_format((float)$gross_total, 2)); ?></td>
                                    </tr>
                                    <?php if(boolval($discount)): ?>
                                        <tr>
                                            <th style="padding: 0 20px 0 0;">Discount %</th>
                                            <td id="inv_discount" style="padding: 0 20px 0 0;"><?php echo esc_html(number_format((float)$discount, 2)); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Net Price:</th>
                                        <td id="inv_net_total" style="padding: 0 20px 0 0;"><?php echo esc_html(number_format((float)$net_total, 2)); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Sales Person</th>
                                        <td id="inv_sales_person" style="padding: 0 20px 0 0;"><?php echo esc_html($sales_person); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Paid Amount</th>
                                        <td id="inv_paid_amount" style="padding: 0 20px 0 0;"><?php echo esc_html(number_format((float)$total_received, 2)); ?></td>
                                    </tr>
                                    <?php if($due): ?>
                                        <tr>
                                            <th style="padding: 0 20px 0 0;">Paid Amounts</th>
                                            <td id="inv_payment_history" style="padding: 0 20px 0 0;">
                                                <?php if(empty($due_payments)): ?>
                                                    <?php echo 0; ?>
                                                <?php else: ?>
                                                    <table class="due_payment_history_table striped">
                                                        <tbody>
                                                            <?php
                                                            foreach($due_payments as $due_payment):
                                                                $amount = intval($due_payment->payment_amount);
                                                                $date_db = $due_payment->payment_date;
                                                                $note = $due_payment->note;
                                                                $date_obj = date_create($date_db);
                                                                $formatted_date = date_format($date_obj, 'd M, Y');
                                                                $time = date_format($date_obj, 'h:i A');
                                                                $date = $formatted_date . ' | ' . $time;

                                                                ?>
                                                                
                                                                <tr>
                                                                    <td class="dph_amount"><?php echo esc_html(number_format((float)$amount, 2)); ?></td>
                                                                    <td class="dph_date"><?php echo esc_html($date); ?></td>
                                                                    <td class="dph_note"><?php echo esc_html($note); ?></td>
                                                                </tr>
                                                                <?php    
                                                            endforeach;
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Remaining Amount</th>
                                        <td id="inv_due_payment" style="padding: 0 20px 0 0;"><?php echo esc_html(number_format((float)$due_amount, 2)); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Account Status</th>
                                        <td id="inv_account_status" style="padding: 0 20px 0 0;"><?php echo esc_html($account_status); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Payment Status</th>
                                        <td id="inv_payment_status" style="padding: 0 20px 0 0;"><?php echo esc_html($payment_status); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Sales Type</th>
                                        <td id="inv_sale_type" style="padding: 0 20px 0 0;"><?php echo esc_html(ucwords(str_replace('-', ' ', $sale_type))); ?></td>
                                    </tr>
                                    <?php if($sale_type_normalized !== 'credit sale'): ?>
                                        <tr>
                                            <th style="padding: 0 20px 0 0;">Payment Method</th>
                                            <td id="inv_payment_method" style="padding: 0 20px 0 0;"><?php echo esc_html(ucwords(str_replace('-', ' ', $payment_method))); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    
                                </tbody>
                            </table>
                        <?php
                        } ?>
                    </div>
                </div>

            <?php
            }else{
                echo 'directly accessed';
            }
        ?>
		
		
		<div class="invoice_print_btn_wrapper" style="text-align: right;margin-top: 20px;">
			<button id="invoicePrintBtn" class="print-btn">Print</button>
		</div>
        
    </div>
</div>
