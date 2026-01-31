<?php 
    require_once(FBM_PLUGIN_DIR . 'inc/functions.php');
?>
<div class="add-product-table-wrap">

    <div class="heading-search">

        <button id="addNewProduct" class="add-new">Add New Product</button>

        <input type="search" name="" id="search-product" placeholder="Search Product Name">

    </div>
    <div id="product_extras">
        <div class="product_manufacturers">
            <!-- ADD NEW MANUFACTURER - PROCESSING -->
            <?php 
                if(isset($_POST['add_new_manufacturer'])){
                    $product_manufacturer_name = sanitize_text_field( $_POST['product_new_manufacturer']);
                    global $wpdb;
                    $table_manufacturers = $wpdb->prefix . "sms_manufacturers";
                    $manufacturer_exists = $wpdb->get_results("SELECT * FROM {$table_manufacturers} WHERE manufacturer_name = '{$product_manufacturer_name}'");
                    if(empty($manufacturer_exists)){
                        $sql = $wpdb->prepare("INSERT INTO {$table_manufacturers} (manufacturer_name) VALUES (%s)", ["{$product_manufacturer_name}"]);
                        $wpdb->query($sql);
                        $insert_id = $wpdb->insert_id;
                        if($insert_id){
                            echo '<p class="notice notice-success" style="padding: 10px;">Success! Manufacturer added.</p>';
                        }else{
                            echo '<p class="notice notice-error" style="padding: 10px;">Failure! Manufacturer not added.</p>';
                        }
                    }else{
                        echo '<p class="notice notice-error" style="padding: 10px;">Failed! this <strong>MANUFACTURER</strong> already exists.</p>';
                    }
                    
                }
            ?>

            
            <button id="toggleManufacturers" type="button" class="sms_btn_light toggleNextElement">Manufacturers</button>
            <div class="manufacturers_listing" style="display: none;">
                <button id="addNewManufacturer" type="button" class="sms_btn_light toggleNextElement">Add New Manufacturer</button>
                <form class="formAddNewManufacturer" method="POST" style="display: none;">
                    <div class="formWrapper">
                        <input type="text" name="product_new_manufacturer" placeholder="Enter Manufacturer Name" required />
                        <input type="submit" name="add_new_manufacturer" value="Add" class="sms_btn_light" />
                    </div>
                </form>
                <table class="table_manufacturers table">
                    <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Manufacturer Name</th>
                            <th colspan="2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            global $wpdb;
                            $table_manufacturers = $wpdb->prefix . "sms_manufacturers";
                            $manufacturers = $wpdb->get_results("SELECT * FROM {$table_manufacturers}");
                            if(!empty(($manufacturers))){
                                $i = 1;
                                foreach($manufacturers as $manufacturer){
                                    $manufacturer_id = $manufacturer->manufacturer_id;
                                    $manufacturer_name = $manufacturer->manufacturer_name;
                                    $formSelector_delete = "btnDeleteManufacturer_{$manufacturer_id}";
                                    $formSelector_update = "btnUpdateManufacturer_{$manufacturer_id}";
                                    ?>
                                    <tr data-manufacturer_id="<?php echo $manufacturer_id; ?>" data-manufacturer_name="<?php echo $manufacturer_name; ?>" title="<?php echo $manufacturer_name; ?>">
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $manufacturer_name; ?></td>
                                        <td>
                                            <button class="btnUpdateManufacturer sms_btn_light" type="button">Update</button>
                                            <form method="POST" style="display: inline-block;">
                                                <input type="hidden" name="manufacturer_id" value="<?php echo $manufacturer_id; ?>" />
                                                <button type="submit" name="<?php echo $formSelector_delete; ?>" value="<?php echo $manufacturer_id; ?>" class="sms_btn_light sms_btn_danger">Delete</button>
                                            </form>

                                            <!-- DELETE MANUFACTURER - PROCESSING -->
                                            <?php 
                                                if (isset($_POST[$formSelector_delete])) {
                                                    $manufacturer_id = intval($_POST[$formSelector_delete]);
                                                    global $wpdb;
                                                    $table_manufacturers = $wpdb->prefix . "sms_manufacturers";

                                                    $deleted = $wpdb->query(
                                                        $wpdb->prepare("DELETE FROM {$table_manufacturers} WHERE manufacturer_id = %d", $manufacturer_id)
                                                    );

                                                    if ($deleted) {
                                                        wp_safe_redirect($_SERVER['REQUEST_URI']);
                                                        exit;
                                                    } else {
                                                        echo '<p class="notice notice-error" style="padding: 10px;">Failure! Manufacturer not deleted.</p>';
                                                    }
                                                }
                                            ?>

                                        </td>
                                    </tr>
                                    <tr style="display: none;">
                                        <td colspan="4">
                                            <form class="formUpdateManufacturer" method="POST">
                                                <div class="formWrapper">
                                                    <input type="hidden" name="manufacturer_id" value="<?php echo $manufacturer_id; ?>" />
                                                    <input type="text" name="product_manufacturer_<?php echo $manufacturer_id; ?>" value="<?php echo $manufacturer_name; ?>" />
                                                    <button type="submit" name="<?php echo $formSelector_update; ?>" value="<?php echo $manufacturer_id; ?>" class="sms_btn_light">Update</button>
                                                </div>
                                            </form>

                                            <!-- UPDATE MANUFACTURER - PROCESSING -->
                                            <?php 
                                                if (isset($_POST[$formSelector_update])) {
                                                    $manufacturer_id = intval($_POST[$formSelector_update]);
                                                    $manufacturer_name = sanitize_text_field($_POST["product_manufacturer_{$manufacturer_id}"]);
                                                    global $wpdb;
                                                    $table_manufacturers = $wpdb->prefix . "sms_manufacturers";

                                                    $updated = $wpdb->query(
                                                        $wpdb->prepare("UPDATE {$table_manufacturers} SET manufacturer_name = %s WHERE manufacturer_id = %d", $manufacturer_name, $manufacturer_id)
                                                    );

                                                    if ($updated) {
                                                        wp_safe_redirect($_SERVER['REQUEST_URI']);
                                                        exit;
                                                    } else {
                                                        echo '<p class="notice notice-error" style="padding: 10px;">Failure! Manufacturer not deleted.</p>';
                                                    }
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        ?>
                        
                    </tbody>
                </table>
            </div>
        </div>
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
                    <!-- <th class="products_sold">Sold</th> -->

                    <!-- <th class="vendor">Vendor</th> -->
                    <th class="manufacturer">Manufacturer</th>

                    <th class="stock">In-Stock</th>

                    <th class="location">Location</th>

                    <th class="add-product-action">Action</th>

                </tr>

            </thead>



            <tbody id="products_table_listing_rows">

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

                            $product_manufacturer_id = $product->product_manufacturer;
                            $product_manufacturer_name = get_manufacturer_name($product_manufacturer_id) ?? 'N/A';

                            $product_location = $product->product_location; 
                            $product_min_quantity = $product->product_min_quantity;
                            $sold = $product->sold;



                            // Manage Stock Availability

                            $stock = get_stock($product_id);

                            $stock_quantity = $stock != false ? intval($stock->stock_quantity) : 0; 

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
                                <!-- <td><?php echo $sold; ?></td> -->

                                <!-- <td><?php echo $product_vendor; ?></td> -->
                                <td><?php echo $product_manufacturer_name; ?></td>

                                <td>

                                    <?php

                                        $stock = get_stock($product_id);

                                        if($stock){

                                            echo $stock->stock_quantity;

                                        }else{
                                            echo 0;
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

                                                        <input type="number" oninput="this.value = Math.abs(this.value)" name="product_purchase_price" id="product_purchase_price" value="<?= $product_purchase_price; ?>" required>

                                                    </div>



                                                    <div class="">

                                                        <label for="product_sale_price">Unit Sale Price</label>

                                                        <input type="number" oninput="this.value = Math.abs(this.value)" name="product_sale_price" id="product_sale_price" value="<?= $product_sale_price; ?>" required>

                                                    </div>



                                                    <div class="">

                                                        <label for="product_sale_price">Add Min Quantity</label>

                                                        <input type="number" oninput="this.value = Math.abs(this.value)" name="add_min_quantity" id="add_min_quantity" value="<?php echo $product_min_quantity; ?>" required>

                                                    </div>



                                                    <div class="">

                                                        <label for="product_vendor">Vendor</label>

                                                        <input type="text" name="product_vendor" id="product_vendor" value="<?= $product_vendor; ?>" required>

                                                    </div>

                                                    <div class="">

                                                        <label for="product_manufacturer">Manufacturer</label>

                                                        <select name="product_manufacturer" id="product_manufacturer" style="width: 100%;display: block;max-width: 100%;" required>
                                                            <option value="">Select Manufacturer</option>
                                                            <?php
                                                                global $wpdb;
                                                                $table_manufacturers = $wpdb->prefix . "sms_manufacturers";
                                                                $manufacturers = $wpdb->get_results("SELECT * FROM {$table_manufacturers} ORDER BY manufacturer_id ASC");
                                                                
                                                                if(!empty($manufacturers)){
                                                                    foreach($manufacturers as $manufacturer){
                                                                        $manufacturer_id = $manufacturer->manufacturer_id;
                                                                        $manufacturer_name = $manufacturer->manufacturer_name;
                                                                        $selected = '';
                                                                        if($product_manufacturer_id == $manufacturer_id){
                                                                            $selected = 'selected';
                                                                        }
                                                                        ?>
                                                                            <option value="<?php echo $manufacturer_id; ?>" <?php echo $selected; ?>><?php echo $manufacturer_name; ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                            ?>
                                                        </select>

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





