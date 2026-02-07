<div class="add-product-page">

    <div class="product-form-wrap" style="display: none;">

        <h4 class="product-table-heading">

            Add Product

            <span class="closeUserForm">&times;</span>

        </h4>

        <form action="" id='product-form'>

            <div class="form-inner">

                <div class="">

                    <label for="product_name">Item Name</label>

                    <input type="text" name="product_name" id="product_name" required>

                </div>



                <div class="">

                    <label for="product_purchase_price">Unit Purchase Price</label>

                    <input type="number" name="product_purchase_price" id="product_purchase_price" required>

                </div>



                <div class="">

                    <label for="product_sale_price">Unit Sale Price</label>

                    <input type="number" name="product_sale_price" id="product_sale_price" required>

                </div>



                <div class="">

                    <label for="product_sale_price">Add Min Quantity</label>

                    <input type="number" name="add_min_quantity" id="add_min_quantity" required>

                </div>



                <div class="">

                    <label for="product_vendor">Vendor</label>

                    <input type="text" name="product_vendor" id="product_vendor" required>

                </div>

                <div class="">

                    <label for="product_manufacturer">Manufacturer</label>

                    <select name="product_manufacturer" id="product_manufacturer" required style="width: 100%;display: block;max-width: 100%;">
                        <option value="">Select Manufacturer</option>
                        <?php
                            global $wpdb;
                            $table_manufacturers = $wpdb->prefix . "sms_manufacturers";
                            $manufacturers = $wpdb->get_results("SELECT * FROM {$table_manufacturers} ORDER BY manufacturer_id ASC");
                            if(!empty($manufacturers)){
                                foreach($manufacturers as $manufacturer){
                                    $manufacturer_id = $manufacturer->manufacturer_id;
                                    $manufacturer_name = $manufacturer->manufacturer_name;
                                    ?>
                                        <option value="<?php echo $manufacturer_id; ?>"><?php echo $manufacturer_name; ?></option>
                                    <?php
                                }
                            }
                        ?>
                    </select>


                </div>



                <div class="">

                    <label for="product_location">Location</label>

                    <input type="text" name="product_location" id="product_location" required>

                </div>

            </div>



            <div class="submit-wrap">

                <input type="submit" id="add_new_product" value="Add New Product" class="save-btn">

            </div>

        </form>

    </div>



    <div class="add-product-wrapper">

        <?php require_once(FBM_PLUGIN_DIR . 'templates/products-listing.php'); ?>

    </div>

</div>

