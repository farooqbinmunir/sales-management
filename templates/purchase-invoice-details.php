<div class="invoice-details-page">
	<button class="sms_back_btn" onclick="history.back()">&#10094; Back</button>
    <div class="invoice-details-wrap">
        
        <?php 
            $invoice_id = null;
            if(isset($_GET['invoice_no'])){
                $invoice_no = intval(sanitize_text_field($_GET['invoice_no']));
                ?>

                <div class="invoice_details_header">
                    <h1 style="text-align: center; margin-block: 30px; font-size: 30px; text-transform: uppercase;"><strong>Invoice</strong></h1>
                    <h5><strong>Invoice No: </strong><span><?php echo $invoice_no; ?></span></h5>
                </div>

                <div class="invoice_details_body" style="margin-top: 50px;">
                    <div class="invoice_details_body_wrap" style="padding: 20px; box-shadow: 0 0 10px -5px black;">
                        <h2 id="store_name" style="text-align: center;"><strong>Shahzad Store</strong></h2>
                        <?php
                        $invoice = get_purchase_invoice($invoice_no);
                        if($invoice){

                            $purchase_invoice_id = $invoice->purchase_invoice_id;
                            $invoice_data = maybe_unserialize($invoice->invoice_data);
                            $invoice_data = is_array($invoice_data) ? $invoice_data : [];
                            $date = $invoice->date;

                            $purchase = get_purchase_by_invoice_no($invoice_no);
                            $vendor = $purchase ? $purchase->vendor : 'N/A';
                            $saleman = 'N/A';
                            $saleman_id = $purchase ? intval($purchase->saleman_id) : 0;
                            if($saleman_id > 0){
                                $userdata = get_userdata($saleman_id);
                                if($userdata){
                                    $saleman = $userdata->display_name;
                                }
                            }
                            ?>

                            <table>
                                <tbody>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Sale Man</th>
                                        <td id="inv_saleman" style="padding: 0 20px 0 0;"><?php echo esc_html($saleman); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Invoice No</th>
                                        <td id="inv_invoice_no" style="padding: 0 20px 0 0;"><?php echo esc_html($invoice_no); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Invoice Type</th>
                                        <td id="inv_invoice_type" style="padding: 0 20px 0 0;">Purchase</td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Vendor</th>
                                        <td id="inv_vendor_name" style="padding: 0 20px 0 0;"><?php echo esc_html($vendor ?: 'N/A'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <table id="inv_products">
                                <thead>
                                    <tr>
                                        <th style="padding: 0 20px 5px 0;">Items</th>
                                        <th style="padding: 0 20px 5px 0;">Manufacturer</th>
                                        <th style="padding: 0 20px 5px 0;">Unit Price</th>
                                        <th style="padding: 0 20px 5px 0;">Qty</th>
                                        <th style="padding: 0 20px 5px 0;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $totalItems = count($invoice_data);
                                    foreach ($invoice_data as $product_item) {
                                        if(!is_array($product_item)){
                                            continue;
                                        }

                                        $product = get_product($product_item['product_id'] ?? 0);
                                        $product_name = $product ? $product->product_name : 'N/A';
                                        $manufacturer_name = get_manufacturer_name($product_item['manufacturer_id'] ?? 0);
                                        $manufacturer_name = $manufacturer_name ? $manufacturer_name : 'N/A';
                                        $quantity = (float) ($product_item['quantity'] ?? 0);
                                        $total_payment = (float) ($product_item['total_payment'] ?? 0);
                                        $unit_price = $quantity > 0 ? ($total_payment / $quantity) : 0;

                                        ?>

                                        <tr>
                                            <td class="inv_product_name"><?php echo esc_html($product_name); ?></td>
                                            <td class="inv_product_manufacturer"><?php echo esc_html($manufacturer_name); ?></td>
                                            <td class="inv_product_unit_price"><?php echo esc_html(number_format((float)$unit_price, 2)); ?></td>
                                            <td class="inv_product_quantity"><?php echo esc_html(number_format((float)$quantity, 2)); ?></td>
                                            <td class="inv_product_amount"><?php echo esc_html(number_format((float)$total_payment, 2)); ?></td>
                                        </tr>
                                    <?php
                                     } ?>
                                </tbody>
                            </table>
                            <hr>
                            <table>
								<?php 
								$purchase_details = get_purchase_by_invoice_no($invoice_no);
                                $purchase_id = $purchase_details ? intval($purchase_details->purchase_id) : 0;
                                $total_payment = $purchase_details ? (float)$purchase_details->total_payment : 0;
                                $payment_status = $purchase_details ? (string)$purchase_details->payment_status : 'N/A';
                                $payment_method = $purchase_details ? (string)$purchase_details->payment_method : '';
                                $description = $purchase_details ? (string)$purchase_details->description : '';
                                $date = $purchase_details ? $purchase_details->date : '';

                                $due = $purchase_id > 0 ? fbm_get_due_by_referer_id($purchase_id, 'purchase') : null;
                                $due_id = $due ? intval($due->id) : 0;
                                $due_payments = $due_id > 0 ? fbm_dues_get_payments($due_id) : [];
                                $due_amount = $due ? (float)$due->remaining_amount : 0;
                                $paid_amount = $due ? (float)$due->paid_amount : ($purchase_details ? (float)$purchase_details->paid : 0);
                                $account_status = $due_amount > 0 ? 'Open' : 'Closed';
                                $normalized_payment_status = $due_amount > 0 ? 'Partially Paid' : 'Paid';
                                if(strtolower(trim((string)$payment_status)) === 'unpaid' && $due_amount > 0){
                                    $normalized_payment_status = 'Unpaid';
                                }
								?>
                                <tbody>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Total Items</th>
                                        <td id="inv_total_items" style="padding: 0 20px 0 0;"><?php echo esc_html(number_format((float)$totalItems, 0)); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Total Payment</th>
                                        <td id="inv_total_payment" style="padding: 0 20px 0 0;"><?php echo esc_html(number_format((float)$total_payment, 2)); ?></td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Paid Amount</th>
                                        <td id="inv_paid_amount" style="padding: 0 20px 0 0;"><?php echo esc_html(number_format((float)$paid_amount, 2)); ?></td>
                                    </tr>
                                    <?php if($due):
                                        ?>
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
                                        <td id="inv_payment_status" style="padding: 0 20px 0 0;"><?php echo esc_html($normalized_payment_status); ?></td>
                                    </tr>
                                    <?php if(strtolower($normalized_payment_status) != 'unpaid' && !empty($payment_method)): ?>
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
			<button id="purchaseInvoicePrintBtn" class="print-btn">Print</button>
		</div>
        
    </div>
</div>
