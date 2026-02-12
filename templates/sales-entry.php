<?php 
    $sales_man_id = get_current_user_id();
    $current_user = wp_get_current_user();
    $sales_man_name = ucwords($current_user->display_name);
?>
<div class="all-wrapper-section">

    <div class="sales-management-wrap">
        <div class="customer-date-section">
            <div class="customer_info">
                <div class="customer_info_wrap">
                    <div id="customer_register_area">
                        <div class="cform_field">
                            <label for="customer-name" style="display: block;">Temp. Customer</label>
                            <input type="text" name="" id="customer-name" placeholder="Enter customer name..." />
                        </div>
                        <div id="salesCalculator" class="amount-cell" style="display: none;">
                            <div class="salesCalculatorWrapper">
                                <div><span class="btnKbdCloser"><kbd>Esc</kbd> key to close</span><span type="button" id="salesCalculatorCloser">&times;</span></div>
                                <form id="creditCustomerForm" method="post" style="/*display: none;*/">
                                    <hgroup>
                                        <h2>Register Credit Customer</h2>
                                    </hgroup>
                                    <div class="cform_field">
                                        <label for="customer-name-credit" style="display: block;">Name</label>
                                        <input type="text" name="customer-name" id="customer-name-credit" placeholder="Enter customer name..." required />
                                    </div>
                                    <div class="cform_field">
                                        <label for="customer-phone" style="display: block;">Phone</label>
                                        <input type="text" name="customer-phone" id="customer-phone" placeholder="Enter customer phone number..." maxlength="11" required />
                                    </div>
                                    <div class="cform_field">
                                        <label for="customer-email" style="display: block;">Email</label>
                                        <input type="text" name="customer-email" id="customer-email" placeholder="Enter customer email..." />
                                    </div>
                                    <div class="cform_field">
                                        <label for="customer-address" style="display: block;">Address</label>
                                        <textarea name="customer-address" id="customer-address" placeholder="Enter customer address..."></textarea>
                                    </div>
                                    <!-- <input type="submit" name="submitCreditCustomerForm" value="Register" class="sms_btn_success" /> -->
                                </form>
                            </div>
                        </div>
                        <?php
                        if(isset($_POST['submitCreditCustomerForm'])){
                            $customer_name = sanitize_text_field($_POST['customer-name']);
                            $customer_phone = sanitize_text_field($_POST['customer-phone']);
                            $customer_email = sanitize_text_field($_POST['customer-email']);
                            $customer_address = sanitize_text_field($_POST['customer-address']);
                            
                            global $wpdb;
                            $table_customers = $wpdb->prefix . 'sms_customers';
                            $customer_sql = $wpdb->prepare("INSERT INTO {$table_customers} (name, phone, email, address) VALUES(%s, %s, %s, %s)", $customer_name, $customer_phone, $customer_email, $customer_address);
                            $customer_registered = $wpdb->query($customer_sql);
                            if($customer_registered): ?>
                                <p class="notice notice-success">Customer registered successfully.</p>
                            <?php
                            else: ?>
                                <p class="notice notice-error">Failed to register customer, please reoload and try again.</p>
                            <?php
                            endif;
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="sale-invoice-wrap">
                <h5>Invoice No</h5>
                <div class="sale_invoice_input_wrap">
                    <input type="number" tabindex="-1" name="sale_invoice" id="sale_invoice" value="<?php echo getNextSaleInvoiceNo(); ?>" readonly />
                </div>
            </div>
            <div class="date-section">
                <p class="date"></p>
            </div>
        </div>
    </div>
    <hr class="fbm_line" />

    <div class="product-wrapper">
        <div class="product-table-wrap">
            <div class="heading-search">
                <h4 class="product-table-heading">Products</h4>
                <!-- Stock filter -->
                <?php include_template('sections/stock-filter'); ?>
                <!-- End Stock filter -->

                <button id="showTable">Selected Products</button>
                
                <!-- Search filter -->
                <?php include_template('sections/search-filter'); ?>
                <!-- End search filter -->
            </div>

            <div class="table-wrap scrollelement">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Items</th>
                            <th>Manufacturer</th>
                            <th class="sms_hidden">Purchase Rate</th>
                            <th>Vendor</th>
                            <th>Sale Rate</th>
                            <th>In-Stock</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body" class="inventryPageProductsTable">
                        <?php 
                            global $wpdb;
                            $table = $wpdb->prefix . 'sms_products';
                            $sql = "SELECT * FROM $table ORDER BY product_name ASC";
                            $products = $wpdb->get_results($sql);

                            if(count($products) > 0){
                                $i = 1;
                                foreach($products as $product){
                                    $product_id = $product->product_id;
                                    $product_name = $product->product_name;
                                    $product_purchase_price = $product->product_purchase_price;
                                    $product_sale_price = $product->product_sale_price;
                                    $product_vendor = $product->product_vendor;
                                    $product_location = $product->product_location;
									$product_min_quantity = $product->product_min_quantity;

                                    $product_manufacturer_id = $product->product_manufacturer;
                                    $product_manufacturer_name = get_manufacturer_name($product_manufacturer_id) ? get_manufacturer_name($product_manufacturer_id) : 'N/A';

                                    // Manage Stock Availability
                                    $stock = get_stock($product_id);
                                    $stock_quantity = $stock != false ? intval($stock->stock_quantity) : 0;
                                    $stock_alert_class = '';
                                    if($stock_quantity < 1){
                                        $stock_alert_class = 'zero_stock_alert';
                                    }elseif($stock_quantity > 0 && $stock_quantity <= $product_min_quantity){
                                        $stock_alert_class = 'low_stock_warning';
                                    }
                                    ?>
                                    <tr class="<?= $stock_alert_class; ?>" data-id="<?= $product_id;  ?>">
                                        <td class="sr_no"><?php echo $i++; ?></td>
                                        <td class="pname"><?php echo $product_name; ?></td>
                                        <td class="pmanufacturer" data-manufacturer_id="<?php echo $product_manufacturer_id; ?>"><?php echo $product_manufacturer_name; ?></td>
                                        <td class="ppurchase_rate sms_hidden"><?php echo $product_purchase_price; ?></td>
                                        <td class="pvendor"><?php echo $product_vendor; ?></td>
                                        <td class="psale_rate"><?php echo $product_sale_price; ?></td>
                                        <td class="pin_stock"><?php echo $stock_quantity; ?></td>
                                        <td class="plocation"><?php echo $product_location; ?></td>
                                    </tr>
                        <?php
                                }
                            } else { 
                        ?>
                                <tr>
                                    <td colspan="6">No products found.</td>
                                </tr>                                
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="selected-total-table">
            <div class="selected-total-table-inner">
                <div class="selected-product-table-wrap">
                    <h4 class="product-table-heading">
                        <span>Selected Products</span>
                        <span class="close-sp-tbl">&times;</span>
                    </h4>

                    <div class="table-wrap scrollelement">
                        <table id="invoiceTable">
                            <thead>
                                <tr>
                                    <th class="sr-Number">#</th>
                                    <th class="item-name">Items</th>
                                    <th class="sale-price">Sale Price</th>
                                    <th class="quantity">Quantity</th>
                                    <th class="items-price">Items Price</th>
                                    <th class="item-type">Item Type</th>
                                    <th class="edit">Edit</th>
                                </tr>
                            </thead>
                            <tbody class="selected_items_container"></tbody>
                        </table>
                    </div>
                </div>

                <div class="total-table">
                    <h4 class="product-table-heading">Total</h4>

                    <div id="grossTotalTable" class="table-wrap">
                        <table>
                            <tbody>
                                <tr>
                                    <th>Gross Total</th>
                                    <th>Discount</th>
                                    <th>Net Total</th>
                                    <th>Sales Type</th>
                                    <th>Payment Method</th>
                                </tr>
                                <tr>
                                    <td class="gross-total"></td>
                                    <td class="discount"><input type="number" name="" id="discount" value="0"></td>
                                    <td class="net-total"></td>
                                    <td class="sales-type">
                                        <select id="salesType" name="salesType">
                                            <option value="Cash Sale">Cash Sales</option>
                                            <option value="Credit Sale">Credit Sales</option>
                                            <option value="Partially Paid">Partially Paid</option>
                                        </select>
                                    </td>
                                    <td class="payment-method">
                                        <select id="paymentMethod" name="paymentMethod">
                                            <option value="Cash in Hand">Cash In Hand</option>
                                            <option value="JazzCash">JazzCash</option>
                                            <option value="Easypaisa">Easypaisa</option>
                                            <option value="Bank">Bank</option>
                                            <option value="---" id="blank_option">---</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="partial_payment_header_row" style="display: none;">
                                    <th colspan="3">Paid Amount</th>
                                    <th colspan="2">Due Amount</th>
                                </tr>
                                <tr class="partial_payment_row" style="display: none;">
                                    <td class="paid-amount" colspan="3">
                                        <input type="number" name="" id="paidAmount" value="0" min="0">
                                    </td>
                                    <td id="due-amount" colspan="2">0</td>
                                </tr>

                            </tbody>
                        </table>
                        <div class="sales-main-actions">
                            <div id="print-actions" class="btn-wrap">
                                <button id="printIvoiceBtn" class="print-btn" data-user_id="<?php echo $sales_man_id; ?>" data-user_name="<?php echo $sales_man_name; ?>">Print and Save</button>
                                <button id="printBillBtn" class="print-btn">Print Bill</button>
                                <!-- <button id="saveSaleBtn" class="save-sale-btn print-btn">Save</button> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

