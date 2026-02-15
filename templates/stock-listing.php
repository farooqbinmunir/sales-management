<div class="add-product-table-wrap">

    <div class="heading-search">

        <h2>All Stocks</h2>
        <?php include_component('search-field', ['search_column' => 'product_name']); ?>

    </div>



    <div class="table-wrap scrollelement">

        <table style="width: 100%;">

            <thead>

                <tr>

                    <th class="sr-Number">#</th>

                    <th class="stock_product_name item-name">Product</th>

                    <th class="stock_quantity">Quantity</th>

                    <th class="stock_product_purchase_rate">Purchase Rate</th>

                    <th class="stock_product_sale_rate">Sale Rate</th>

                    <th class="stock_purchase_amount">Purchase Amount</th>

                    <th class="stock_sale_amount">Sale Amount</th>

                    <th class="stock_profit">Profit</th>

                </tr>

            </thead>



            <tbody id="add-product-table-body">

                <?php 

                global $wpdb;

                $table = $wpdb->prefix . 'sms_stock';

                $sql = "SELECT * FROM $table";

                $stocks = $wpdb->get_results($sql);



                // Calculate Total

                $investment = 0;

                $sold_amount = 0;

                $profit = 0;

                $profit_percent = 0;

                if(count($stocks) > 0){

                    $i = 1;

                    foreach($stocks as $stock){

                        $stock_id = $stock->stock_id;

                        $product_id = $stock->product_id;

                        $stock_quantity = $stock->stock_quantity; 



                        $stock_alert_class = '';

                        if($stock_quantity < 1){

                            $stock_alert_class = 'zero_stock_alert';

                        }elseif($stock_quantity > 0 && $stock_quantity <= 10){

                            $stock_alert_class = 'low_stock_warning';

                        } ?>

                        <?php 
                        if($stock_quantity > 0){
                            if($product_id){
                            

                                $product = get_product($product_id);
                                $product_name = ucwords(str_replace(['-', '_'], ' ', $product->product_name));

                                $product_purchase_rate = intval($product->product_purchase_price);

                                $product_sale_rate = intval($product->product_sale_price);

                                $product_purchase_amount = floor($product_purchase_rate * $stock_quantity);

                                $product_sale_amount = floor($product_sale_rate * $stock_quantity);

                                $product_profit = floor($product_sale_amount - $product_purchase_amount);

                                $product_profit_percent = floor(($product_profit * 100) / $product_purchase_amount);



                                // Calculate Total

                                $investment += $product_purchase_amount;

                                $sold_amount += $product_sale_amount;

                                $profit += $product_profit;

                                $profit_percent += $product_profit_percent; 
                                ?>
                                <tr class="<?= $stock_alert_class; ?>" data-id="<?= $stock_id;  ?>">
                                    <td><?= $i++; ?></td>

                                    <td class="product_name"><?= $product_name; ?></td>

                                    <td><?= $stock_quantity; ?></td>

                                    <td><?= $product_purchase_rate; ?></td>

                                    <td><?= $product_sale_rate; ?></td>

                                    <td><?= $product_purchase_amount; ?></td>

                                    <td><?= $product_sale_amount; ?></td>

                                    <td>

                                        <span class="profit_value"><?= $product_profit; ?></span>

                                        <span class="percent_wrap">

                                            (<span class="percent_value"><?= $product_profit_percent; ?></span><span class="profit_percent_postfix">%</span>)

                                        </span>                                    

                                    </td>
                                </tr>
                            <?php
                            }
                        }
                    }



                } else { ?>

                    <tr>

                        <td colspan="8">Stock is empty.</td>

                    </tr>

                <?php

                } ?>

                <tfoot>

                    <tr>

                        <th colspan="5" style="text-align: center;">Total</th>

                        <th><?= $investment; ?></th>

                        <th><?= $sold_amount; ?></th>

                        <th><?= $profit; ?> <small style="font-size: 12px;">(Approx. <?= $profit_percent; ?>%)</small></th>

                    </tr>

                </tfoot>



            </tbody>

        </table>

    </div>

</div>

