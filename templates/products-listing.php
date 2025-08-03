<div class="add-product-table-wrap">

    <div class="heading-search">

        <button id="addNewProduct" class="add-new">Add New Product</button>

        <input type="search" name="" id="search-product" placeholder="Search Product Name">

    </div>



    <div class="products_table_listing table-wrap scrollelement">

        <table style="width: 100%;">

            <thead>

                <tr>

                    <th class="sr-Number">#</th>

                    <th class="item-name">Product</th>

                    <th class="purches">Purchase Price</th>

                    <th class="sale">Sale Price</th>

                    <th class="min_quantity">Min Quantity</th>

                    <th class="vendor">Vendor</th>

                    <th class="stock">In-Stock</th>

                    <th class="location">Location</th>

                    <th class="add-product-action">Action</th>

                </tr>

            </thead>



            <tbody id="products_table_listing_rows">

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

                            }elseif($stock_quantity > 0 && $stock_quantity <= intval($product_min_quantity)){

                                $stock_alert_class = 'low_stock_warning';

                            } ?>

                            <tr class="<?= $stock_alert_class; ?>" data-id="<?= $product_id;  ?>">

                                <td><?php echo $i++; ?></td>

                                <td><?php echo $product_name; ?></td>

                                <td><span>Rs. </span><span><?php echo $product_purchase_price; ?></span></td>

                                <td><span>Rs. </span><span><?php echo $product_sale_price; ?></span></td>

                                <td><?php echo $product_min_quantity; ?></td>

                                <td><?php echo $product_vendor; ?></td>

                                <td>

                                    <?php

                                        $stock = get_stock($product_id);

                                        if($stock){

                                            echo $stock->stock_quantity;

                                        }

                                    ?>        

                                </td>

                                <td><?php echo $product_location; ?></td>

                                <td>

                                    <button type="button" class='edit_product_btn quick_edit_btn edit_btn sms_btn' data-id="<?= $product_id;  ?>">Edit</button>

                                </td>

                            </tr>

                            <tr class="edit_form" data-id="<?= $product_id;  ?>">

                                <td colspan="8">

                                    <div class="quick_edit_form edit_product_form_wrap" style="display: none;">

                                        <div class="edit_product_form_fields_container">

                                            <h4 class="product-table-heading">

                                                Update Product

                                                <span class="close_quick_edit_popup">&times;</span>

                                            </h4>

                                            <form class='quick_edit_form_wrap'>

                                                <div class="form-inner">

                                                    <div class="">

                                                        <label for="product_name">Item Name</label>

                                                        <input type="text" name="product_name" id="product_name" value="<?= $product_name; ?>" required>

                                                    </div>



                                                    <div class="">

                                                        <label for="product_purchase_price">Unit Purchase Price</label>

                                                        <input type="number" name="product_purchase_price" id="product_purchase_price" value="<?= $product_purchase_price; ?>" required>

                                                    </div>



                                                    <div class="">

                                                        <label for="product_sale_price">Unit Sale Price</label>

                                                        <input type="number" name="product_sale_price" id="product_sale_price" value="<?= $product_sale_price; ?>" required>

                                                    </div>



                                                    <div class="">

                                                        <label for="product_sale_price">Add Min Quantity</label>

                                                        <input type="number" name="add_min_quantity" id="add_min_quantity" value="<?php echo $product_min_quantity; ?>" required>

                                                    </div>



                                                    <div class="">

                                                        <label for="product_vendor">Vendor</label>

                                                        <input type="text" name="product_vendor" id="product_vendor" value="<?= $product_vendor; ?>" required>

                                                    </div>



                                                    <div class="">

                                                        <label for="product_location">Location</label>

                                                        <input type="text" name="product_location" id="product_location" value="<?= $product_location; ?>" required>

                                                    </div>

                                                </div>



                                                <div class="submit-wrap">

                                                    <button type="button" class="update_product sms_btn_filled quick_edit_update_btn" data-id="<?= $product_id; ?>">Update</button>

                                                </div>

                                            </form>

                                        </div>

                                    </div>

                                </td>

                            </tr>

                        <?php

                        }

                    } else { ?>

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