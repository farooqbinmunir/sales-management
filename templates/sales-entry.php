<div class="all-wrapper-section">

    <div class="sales-management-wrap">
        <div class="customer-date-section">
            <div class="search-section">
                <input type="text" name="" id="customer-name" placeholder="Customer Name">
            </div>
            <div class="sale-invoice-wrap">
                <h5>Invoice No</h5>
                <div class="sale_invoice_input_wrap">
                    <?php 
                        $today_date = date('Y-m-d');
                        global $wpdb;
                        $table_invoices = $wpdb->prefix . 'sms_invoices';
                        $sql = "SELECT count(*) FROM $table_invoices WHERE date = '$today_date'";
                        $result = $wpdb->get_var($sql) + 1;
                        $invoiceId = date('Ymd');
                        $invoiceId = $invoiceId . str_pad($result,2,0, STR_PAD_LEFT);
                    ?>
                    <input tabindex="-1" type="number" name="sale_invoice" id="sale_invoice" value="<?php echo $invoiceId; ?>" readonly />
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
                <select id="stockFilter" name="stockFilter">
                    <option value="all-stock">All Stock</option>
                    <option value="near-to-end">Near to End</option>
                    <option value="out-of-stock">Out of Stock</option>
                </select>
                <button id="showTable">Selected Products</button>
                <input type="search" name="" id="search-product" placeholder="Search Product Name">
            </div>

            <div class="table-wrap scrollelement">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Items</th>
                            <th>Purchase Rate</th>
                            <th>Sale Rate</th>
                            <th>Vendor</th>
                            <th>In-Stock</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
                        <?php 
                            global $wpdb;
                            $table = $wpdb->prefix . 'sms_products';
                            $sql = "SELECT * FROM $table";
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

                                    // Manage Stock Availability
                                    $stock = get_stock($product_id);
                                    $stock_quantity = intval($stock->stock_quantity); 
                                    $stock_alert_class = '';
                                    if($stock_quantity < 1){
                                        $stock_alert_class = 'zero_stock_alert';
                                    }elseif($stock_quantity > 0 && $stock_quantity <= $product_min_quantity){
                                        $stock_alert_class = 'low_stock_warning';
                                    }
                                    ?>
                                    <tr class="<?= $stock_alert_class; ?>" data-id="<?= $product_id;  ?>">
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $product_name; ?></td>
                                        <td><?php echo $product_purchase_price; ?></td>
                                        <td><?php echo $product_sale_price; ?></td>
                                        <td><?php echo $product_vendor; ?></td>
                                        <td><?= $stock_quantity; ?></td>
                                        <td><?php echo $product_location; ?></td>
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
                                    <th class="quantity">Sale Price</th>
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
                                    <!-- <th>Flat Discoun</th> -->
                                    <th>Discount</th>
                                    <th>Net Total</th>
                                    <th>Sales Type</th>
                                    <th>Payment Method</th>
                                </tr>
                                <tr>
                                    <td class="gross-total"></td>
                                    <!-- <td class="flat-discount"><input type="number" name="" id="flat_discount" value="0"></td> -->
                                    <td class="discount"><input type="number" name="" id="discount" value="0"></td>
                                    <td class="net-total"></td>
                                    <td class="sales-type">
                                        <select id="salesType" name="salesType">
                                            <option value="cash-sales">Cash Sales</option>
                                            <option value="credit-sales">Credit Sales</option>
                                        </select>
                                    </td>
                                    <td class="payment-method">
                                        <select id="paymentMethod" name="paymentMethod">
                                            <option value="cash-in-hand">Cash In Hand</option>
                                            <option value="jazzCash">JazzCash</option>
                                            <option value="easypaisa">Easypaisa</option>
                                            <option value="bank">Bank</option>
                                            <option value="---" id="blank_option">---</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="btn-wrap">
                            <button id="printIvoiceBtn" class="print-btn">Print</button>
                            <!-- <button id="saveSaleBtn" class="save-sale-btn print-btn">Save</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
