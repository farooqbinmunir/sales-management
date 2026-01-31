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

                        global $wpdb;
                        $table_invoices = $wpdb->prefix . 'sms_purchase_invoices';
                        $invoice_query = "SELECT * FROM $table_invoices WHERE purchase_invoice = $invoice_no";
                        $invoice = $wpdb->get_row($invoice_query);
                        if($invoice){

                            $purchase_invoice_id = $invoice->purchase_invoice_id;
                            $invoice_data = maybe_unserialize($invoice->invoice_data);
                            $date = $invoice->date;
                            $saleman = get_saleman_by_invoice_no($invoice_no)->name;
                            ?>

                            <table>
                                <tbody>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Sale Man</th>
                                        <td id="inv_saleman" style="padding: 0 20px 0 0;"><?php echo $saleman; ?><td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Invoice No</th>
                                        <td id="inv_invoice_no" style="padding: 0 20px 0 0;"><?php echo $invoice_no; ?><td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Invoice Type</th>
                                        <td id="inv_invoice_no" style="padding: 0 20px 0 0;">Purchase<td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <table id="inv_products">
                                <thead>
                                    <tr>
                                        <th style="padding: 0 20px 5px 0;">Items</th>
                                        <th style="padding: 0 20px 5px 0;">Manufacturer</th>
                                        <th style="padding: 0 20px 5px 0;">Vendor</th>
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
                                        

                                        $product_name = get_product($product_item['product_id'])->product_name;
                                        $manufacturer_name = get_manufacturer_name($product_item['manufacturer_id']);
                                        $vendor = $product_item['vendor'];
                                        $quantity = + $product_item['quantity'];
                                        $purchase_rate = + $product_item['purchase_rate'];
                                        $total_payment = + $product_item['total_payment'];
                                        $unit_price = +($total_payment / $quantity);

                                        ?>

                                        <tr>
                                            <td class="inv_product_name"><?php echo $product_name; ?></td>
                                            <td class="inv_product_manufacturer"><?php echo $manufacturer_name; ?></td>
                                            <td class="inv_product_vendor"><?php echo $vendor; ?></td>
                                            <td class="inv_product_unit_price"><?php echo $unit_price; ?></td>
                                            <td class="inv_product_quantity"><?php echo $quantity; ?></td>
                                            <td class="inv_product_amount"><?php echo $total_payment; ?></td>
                                        </tr>
                                    <?php
                                     } ?>
                                </tbody>
                            </table>
                            <hr>
                            <table>
								<?php 
								$purchase_details = get_purchase_by_invoice_no($invoice_no);
                                $purchase_id = $purchase_details->purchase_id;
                                $total_payment = $purchase_details->total_payment;
                                $payment_status = $purchase_details->payment_status;
                                $payment_method = $purchase_details->payment_method;
                                $description = $purchase_details->description;
                                $date = $purchase_details->date;


                                $due = fbm_get_due_by_referer_id($purchase_id);
                                $due_id = $due ? $due->id : null;
                                $due_payments = fbm_dues_get_payments($due_id);
								?>
                                <tbody>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Total Items</th>
                                        <td id="inv_total_items" style="padding: 0 20px 0 0;"><?php echo number_format($totalItems); ?><td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Total Payment</th>
                                        <td id="inv_total_payment" style="padding: 0 20px 0 0;"><?php echo number_format($total_payment); ?><td>
                                    </tr>
                                    <?php if($due):
                                        $due_amount = floatval($due->remaining_amount);
                                        ?>
                                        <tr>
                                            <th style="padding: 0 20px 0 0;">Paid Amounts</th>
                                            <td id="inv_payment_history" style="padding: 0 20px 0 0;">
                                                <?php if(intval($total_payment) === intval($due_amount)): ?>
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
                                                                    <td class="dph_amount"><?php echo $amount; ?></td>
                                                                    <td class="dph_date"><?php echo $date; ?></td>
                                                                    <td class="dph_note"><?php echo $note; ?></td>
                                                                </tr>
                                                                <?php    
                                                            endforeach;
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                <?php endif; ?>
                                            <td>
                                        </tr>                                        
                                        <tr>
                                            <th style="padding: 0 20px 0 0;">Remaining Amount</th>
                                            <td id="inv_due_payment" style="padding: 0 20px 0 0;"><?php echo $due_amount; ?><td>
                                        </tr>

                                        <tr>
                                            <th style="padding: 0 20px 0 0;">Account Status</th>
                                            <td id="inv_due_payment" style="padding: 0 20px 0 0;"><?php echo $due_amount == 0 ? 'Closed' : 'Open'; ?><td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Payment Status</th>
                                        <td id="inv_payment_status" style="padding: 0 20px 0 0;"><?php echo $due_amount == 0 ? 'Paid' : $payment_status; ?><td>
                                    </tr>
                                    <?php if(strtolower($payment_status) != 'unpaid'): ?>
                                        <tr>
                                            <th style="padding: 0 20px 0 0;">Payment Method</th>
                                            <td id="inv_payment_method" style="padding: 0 20px 0 0;"><?php echo ucwords(str_replace('-', ' ', $payment_method)); ?><td>
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
