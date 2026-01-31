<form id="returnForm">

    <div class="return_form_field_group">

        <label for="add_invoice_no">Add Invoice No: </label>

        <div>
            <input type="number" name="add_invoice_no" id="add_invoice_no" placeholder="Invoice Number" required />
            <p id="invoice_no_error" style="color: red; display: none;">Invoice not exists.</p>
        </div>

    </div>
    <section id="returnForm_body" style="display: none;">
        <div class="return_form_field_group">

            <label for="product_id">Product returned: </label>
            <div class="rf_field_wrap">
                <select id="product_id" name="product_id" required>
                    <option disabled selected>Select Product</option>
                </select>
                <!-- <small style="display: none;"><strong>Rate</strong> <span id="returnRateContainer"></span></small> -->
            </div>

        </div>

        <div class="return_form_field_group">

            <label for="return_quantity">Quantity returned: </label>

            <input type="number" name="quantity" id="return_quantity" placeholder="Enter quantity to return" min="1" required />

        </div>

        <div class="return_form_field_group">
            <label for="return_quantity">Amount</label>
            <input type="number" name="amount" id="return_amount" placeholder="Amount to return" readonly />
        </div>

        <div class="return_form_field_group">

            <label for="return_reason">Reason: </label>

            <textarea name="return_reason" id="return_reason" rows="5" placeholder="Return Reason"></textarea>

        </div>

        <button type="submit" class="sms_btn_info">Process Return</button>
    </section>

</form>

<hr>

<section id="returns_listing">
    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <caption style="caption-side: top; font-weight: bold;">All Returns Listing</caption>
        <thead>
            <tr>
                <th>Sr. No</th>
                <th>Invoice No.</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Reason</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
                global $wpdb;
                $table_returns = "{$wpdb->prefix}sms_sales_returns";
                $query_returns = "SELECT * FROM {$table_returns}";
                $all_returns = $wpdb->get_results($query_returns);
                $i = 1;
                foreach ($all_returns as $returned_item) {
                    $return_id = $returned_item->return_id;
                    $product_id = $returned_item->product_id;
                    $quantity = $returned_item->quantity;
                    $amount = $returned_item->amount ? $returned_item->amount : 0;
                    $product = get_product($returned_item->product_id);
                    $product_name = $product->product_name;
                    $reason = $returned_item->return_reason ? $returned_item->return_reason : 'N/A';
                    $invoice_no = $returned_item->invoice_no ? $returned_item->invoice_no : 'N/A';
                    $date = new DateTime($returned_item->return_date);
                    $date = $date->format('d M, Y');
                ?>
                    <tr data-return-id="<?php echo $return_id; ?>">
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $invoice_no; ?></td>
                        <td><?php echo $product_name; ?></td>
                        <td><?php echo $quantity; ?></td>
                        <td><?php echo $amount; ?></td>
                        <td><?php echo $reason; ?></td>
                        <td><?php echo $date; ?></td>
                    </tr>
                <?php    
                }
            ?>
            
        </tbody>
    </table>
</section>

