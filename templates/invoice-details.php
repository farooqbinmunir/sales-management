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
                        $invoice_query = "SELECT * FROM $table_invoices WHERE invoice_no = $invoice_no";
                        $invoice = $wpdb->get_results($invoice_query);
                        $invoice = count($invoice) > 0 ? $invoice[0] : null;
                        if($invoice != null){

                            $invoice_id = $invoice->invoice_id;
                            $invoice_data = maybe_unserialize($invoice->invoice_data);
                            $date = $invoice->date;

                            $customer = get_customer($customer_id);
                            $customer_name = ucwords($customer->name);

                            ?>

                            <table>
                                <tbody>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Customer Name</th>
                                        <td id="inv_customer_name" style="padding: 0 20px 0 0;"><?php echo $customer_name; ?><td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Invoice No</th>
                                        <td id="inv_invoice_no" style="padding: 0 20px 0 0;"><?php echo $invoice_no; ?><td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <table id="inv_products">
                                <thead>
                                    <tr>
                                        <th style="padding: 0 20px 5px 0;">Sr. No</th>
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
                                        // echo '<pre>';
                                        // print_r($product_item);
                                        // echo '</pre>';

                                        $product_name = ucwords($product_item['prod_name']);
                                        $quantity = $product_item['prod_quantity'];
                                        $amount = $product_item['prod_total_amount'];
                                        $prod_type = $product_item['prod_type'];
                                        $unit_price = +($amount / $quantity);

                                        ?>

                                        <tr>
                                            <td class="inv_sr_no"><?php echo $i++; ?></td>
                                            <td class="inv_product_name"><?php echo $product_name; ?></td>
                                            <td class="inv_product_unit_price"><?php echo $unit_price; ?></td>
                                            <td class="inv_product_quantity"><?php echo $quantity; ?></td>
                                            <td class="inv_product_amount"><?php echo $amount; ?></td>
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
								$gross_total = $sale_details->gross_total;
								$discount = $sale_details->discount;
								$net_total = $sale_details->net_total;
								$sale_type = substr($sale_details->sale_type, 0, -1);
								$payment_method = $sale_details->payment_method;
								?>
                                <tbody>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Total Items</th>
                                        <td id="inv_total_items" style="padding: 0 20px 0 0;"><?php echo $total_items; ?><td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Gross Total:</th>
                                        <td id="inv_gross_total" style="padding: 0 20px 0 0;"><?php echo $gross_total; ?><td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Discount %</th>
                                        <td id="inv_discount" style="padding: 0 20px 0 0;"><?php echo $discount; ?><td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Net Price:</th>
                                        <td id="inv_net_total" style="padding: 0 20px 0 0;"><?php echo $net_total; ?><td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Sales Type</th>
                                        <td id="inv_sale_type" style="padding: 0 20px 0 0;"><?php echo ucwords(str_replace('-', ' ', $sale_type)); ?><td>
                                    </tr>
                                    <tr>
                                        <th style="padding: 0 20px 0 0;">Payment Method</th>
                                        <td id="inv_payment_method" style="padding: 0 20px 0 0;"><?php echo ucwords(str_replace('-', ' ', $payment_method)); ?><td>
                                    </tr>
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
