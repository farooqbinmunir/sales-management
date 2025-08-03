<?php



	/* Wiselogix - Custom Plugin functions */



	// Including utility functions to be available in this file
	require_once(FBM_PLUGIN_DIR . 'inc/utilities.php');

	// Enqueue scripts & styles for backend use
	function fbm_backend_enqueues(){



		// Enqueue Styles

		wp_enqueue_style('fbm_backend_styles', plugins_url(FBM_PLUGIN_DIR_NAME) . '/assets/css/backend/backend.css', '', time(), 'all');
		wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', '', '1.0', 'all');



		// Enqueue Scripts

		wp_enqueue_script('fbm_backend_scripts', plugins_url(FBM_PLUGIN_DIR_NAME) . '/assets/js/backend/backend.js', ['jquery'], time(), true);
		wp_enqueue_script('fbm_backend_fbm_js', plugins_url(FBM_PLUGIN_DIR_NAME) . '/assets/js/backend/fbm.js', ['jquery'], time(), true);

		wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', 'jquery', '1.0', false);

		// Enqueue Chart.js
	    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.7.1', true);
	    wp_enqueue_script('fbm-analytics-charts-js', plugins_url(FBM_PLUGIN_DIR_NAME) . '/assets/js/backend/analytics-charts.js', array('chart-js'), filemtime(FBM_PLUGIN_DIR . 'assets/js/backend/analytics-charts.js'), true);

		$localized_data = array(
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce(FBM_PLUGIN_NONCE),
			'path' => site_url(),
		);
		wp_localize_script('fbm_backend_scripts', 'fbm_ajax', $localized_data);
		wp_localize_script('fbm_backend_fbm_js', 'fbm_ajax', $localized_data);

		// Pass data to JS
	    wp_localize_script('fbm-analytics-charts-js', 'fbmChartData', [
	        'monthlySales' => fbm_get_monthly_sales_data(),
	        'productPerformance' => fbm_get_product_performance_data()
	    ]);

	    // Enqueueing LIB SELECT2
	    wp_enqueue_style('fbm-select2-css', plugins_url(FBM_PLUGIN_DIR_NAME) . '/assets/css/backend/select2.css', '', '4.1.0');
	    wp_enqueue_script('fbm-select2-js', plugins_url(FBM_PLUGIN_DIR_NAME) . '/assets/js/backend/select2.js', ['chart-js'], '4.1.0');
	}



	// Enqueue scripts & styles which are used by the plugin

	function fbm_frontend_enqueues(){



		// Enqueue Styles

		wp_enqueue_style('fbm_frontend_styles', plugins_url(FBM_PLUGIN_DIR_NAME) . '/assets/css/frontend/frontend.css', '', time(), 'all');

		wp_enqueue_style('slick_css', plugins_url(FBM_PLUGIN_DIR_NAME) . '/assets/css/frontend/slick.css', '', '1.0', 'all');



		// Enqueue Scripts

		wp_enqueue_script('slick_js', plugins_url(FBM_PLUGIN_DIR_NAME) . '/assets/js/frontend/slick.min.js', ['jquery'], '1.0');

		wp_enqueue_script('frontend_js', plugins_url(FBM_PLUGIN_DIR_NAME) . '/assets/js/frontend/frontend.js', ['jquery'], time(), true);
	}

	// Perform actions on wordpress admin init - hook

	function fbm_admin_menu_callback(){



		add_menu_page( 

			FBM_PLUGIN_TITLE, 

			FBM_PLUGIN_MENU_NAME, 

			'read', 

			sanitize_title_with_dashes(FBM_PLUGIN_MENU_NAME), 

			'sales_management_callback',

			null,

			6

		);



		if (current_user_can('administrator')) {
			add_submenu_page( 

				sanitize_title_with_dashes(FBM_PLUGIN_MENU_NAME), 

				'Products',

				'Products',

				'manage_options', 

				'products',

				'products_callback',

			);





			add_submenu_page( 

				sanitize_title_with_dashes(FBM_PLUGIN_MENU_NAME),

				'Purchase',

				'Purchase',

				'manage_options', 

				'purchase',

				'purchases_callback',

			);

			add_submenu_page( 

				sanitize_title_with_dashes(FBM_PLUGIN_MENU_NAME),

				'Sales',

				'Sales',

				'manage_options', 

				'sales',

				'sales_callback',

			);

			add_submenu_page( 

				sanitize_title_with_dashes(FBM_PLUGIN_MENU_NAME),

				'Returns',

				'Returns',

				'manage_options', 

				'returns',

				'returns_callback',

			);

			add_submenu_page( 

				sanitize_title_with_dashes(FBM_PLUGIN_MENU_NAME), 

				'Stock',

				'Stock',

				'manage_options', 

				'stock',

				'stock_callback',

			);

			add_submenu_page( 

				null, 

				'Invoice Details',

				'Invoice Details',

				'manage_options', 

				'invoice_details',

				'invoice_details_callback',

			);

			add_submenu_page(
		        sanitize_title_with_dashes(FBM_PLUGIN_MENU_NAME),
		        'Product Analytics',
		        'Analytics',
		        'manage_options',
		        'fbm-analytics',
		        'fbm_render_analytics_page'
		    );
		}


	}



	// Render the html on plugin menu page
	function sales_management_callback(){
		include (FBM_PLUGIN_DIR . '/templates/sales-panel.php');
	}

	// Submenu page for CPTs
	function products_callback(){
		include(FBM_PLUGIN_DIR . '/templates/products-panel.php');
	}

	// Submenu page for Sales listing
	function sales_callback(){
		require_once(FBM_PLUGIN_DIR . 'templates/sales-listing-panel.php');
	}

	// Submenu page for Purchases
	function purchases_callback(){
		require_once(FBM_PLUGIN_DIR . 'templates/purchases-panel.php');
	}

	// Submenu page for Returns
	function returns_callback(){
		require_once(FBM_PLUGIN_DIR . 'templates/returns-panel.php');
	}

	// Submenu page for Stock
	function stock_callback(){
		require_once(FBM_PLUGIN_DIR . 'templates/stock-panel.php');
	}

	// Submenu page for Invoice Details
	function invoice_details_callback(){
		require_once(FBM_PLUGIN_DIR . 'templates/invoice-panel.php');
	}

	// Submenu page for Invoice Details
	function fbm_render_analytics_page(){
		require_once(FBM_PLUGIN_DIR . 'templates/analytics-panel.php');
	}

	// Perform actions on wordpress init - hook
	function fbm_init_callback() {
		flush_rewrite_rules();
	}

	





	function handle_product_action() {



		$payload = json_decode(stripcslashes($_POST['payload']));

		$action = strtolower(sanitize_text_field($_POST['required_action']));

		$product_name = ucwords(str_replace(['-', '_'], ' ', sanitize_text_field($payload->product_name)));

		$product_purchase_price = sanitize_text_field($payload->product_purchase_price);

		$product_sale_price = sanitize_text_field($payload->product_sale_price);

		$product_vendor = sanitize_text_field($payload->product_vendor);

		$product_location = sanitize_text_field($payload->product_location);
		$product_min_quantity = +sanitize_text_field($payload->product_min_quantity);



		global $wpdb;

		$table = $wpdb->prefix . 'sms_products';

		if($action == 'update'){
			$product_id = intval(sanitize_text_field($_POST['id']));
			$update_product_query = "UPDATE $table SET product_name = '$product_name', product_purchase_price = $product_purchase_price, product_sale_price = $product_sale_price, product_vendor = '$product_vendor', product_location = '$product_location', product_min_quantity = $product_min_quantity WHERE product_id = $product_id";	
			$result = $wpdb->query($update_product_query);
			if($result){

				wp_send_json_success();
	
			}else{
	
				wp_send_json_error();
	
			}

		}elseif( $action == 'add'){
			$query = $wpdb->prepare("INSERT INTO $table (product_name, product_purchase_price, product_sale_price, product_vendor, product_location, product_min_quantity) VALUES (%s, %d, %d, %s, %s, %d)", ["$product_name", $product_purchase_price, $product_sale_price, "$product_vendor", "$product_location", $product_min_quantity]);

		

			$result = $wpdb->query($query);
	
	
	
			$inserted_product_id = $wpdb->insert_id;
	
	
	
			// Insert initial stock quantity into the stock table
	
			$table_stock = $wpdb->prefix . 'sms_stock';
	
			$result = $wpdb->insert(
	
				$table_stock,
	
				[
	
					'product_id' => $inserted_product_id,
	
					'stock_quantity' => 0,
	
					'stock_location' => $product_location,
	
				]
	
			);

			if($result){

				wp_send_json_success();
	
			}else{
	
				wp_send_json_error();
	
			}
		}

	}

	

	add_action('wp_ajax_handle_product', 'handle_product_action'); // For logged-in users

	add_action('wp_ajax_nopriv_handle_product', 'handle_product_action'); // For non-logged-in users





	function handle_purchase(){

		$payload = json_decode(stripcslashes($_POST['payload']));
		$action = strtolower(sanitize_text_field($_POST['required_action']));

		$product_id = intval(sanitize_text_field($payload->product_id));
		$vendor = sanitize_text_field($payload->vendor);
		$quantity = intval(sanitize_text_field($payload->quantity));
		$rate = intval(sanitize_text_field($payload->rate));
		$total_payment = intval(sanitize_text_field($payload->payment));
		$payment_status = ucwords(str_replace(['-', '_'], ' ', sanitize_text_field($payload->payment_status)));
		$payment_method = ucwords(str_replace(['-', '_'], ' ', sanitize_text_field($payload->payment_method)));
		$description = ucfirst(sanitize_text_field($payload->description));

		global $wpdb;
		$date = date('Y-m-d');
		$table_purchase = $wpdb->prefix . 'sms_purchases';
		$table_stock = $wpdb->prefix . 'sms_stock';

		$old_purchase_quantity = intval($wpdb->get_var("SELECT quantity FROM $table_purchase WHERE product_id = $product_id"));
		$available_stock = intval($wpdb->get_var("SELECT stock_quantity FROM $table_stock WHERE product_id = $product_id"));


		if($action == 'add'){
			$sql = "INSERT INTO $table_purchase (product_id, vendor, quantity, rate, total_payment, payment_status, payment_method, description, date) 
			VALUES ($product_id, '$vendor', $quantity, $rate, $total_payment, '$payment_status', '$payment_method', '$description', '$date')";
			$result = $wpdb->query($sql);
			if($result){
				$stock_new_quantity = $quantity > 0 ? $available_stock + $quantity : $available_stock;
				$sql2 = "UPDATE $table_stock SET stock_quantity = $stock_new_quantity WHERE product_id = $product_id";
				$result2 = $wpdb->query($sql2);
				if($result2){
					wp_send_json_success();
				}else{
					wp_send_json_error();
				}
			}
		}elseif($action == 'update'){
			$purchase_id = intval(sanitize_text_field($_POST['id']));

			$quantity_diff = $quantity - $old_purchase_quantity;
			$stock_new_quantity = $available_stock + $quantity_diff;

			if(!($stock_new_quantity < 0)){
				// Update Purchase SQL
				$update_purchase_query = "UPDATE $table_purchase SET 
				vendor = '$vendor', 
				quantity = $quantity, 
				rate = $rate, 
				payment_status = '$payment_status', 
				payment_method = '$payment_method', 
				total_payment = $total_payment, 
				description = '$description' 
				WHERE purchase_id = $purchase_id";

				// Update Purchase SQL run
				$wpdb->query($update_purchase_query);

				// Update Stock SQL
				$update_stock_query = "UPDATE $table_stock SET stock_quantity = $stock_new_quantity WHERE product_id = $product_id";

				// Update Stock SQL run
				$wpdb->query($update_stock_query);

				wp_send_json_success();

			}else{
				$stock_mismatch_warning_message = "Stock error! you reduced quantity by $quantity_diff, but available stock is $available_stock";
				wp_send_json_error($stock_mismatch_warning_message);
			}
			

			
		}
		

	}

	add_action('wp_ajax_handle_purchase', 'handle_purchase');

	add_action('wp_ajax_nopriv_handle_purchase', 'handle_purchase');





	function get_product($product_id){

		global $wpdb;

		$table = $wpdb->prefix . 'sms_products';

		$sql = "SELECT * FROM $table WHERE product_id = $product_id";

		$products = $wpdb->get_results($sql);

		if(count($products) > 0){

		    return $products[0];

		}else{

			return false;

		}

	}


	

	function get_stock($product_id){

		global $wpdb;

		$table = $wpdb->prefix . 'sms_stock';

		$sql = "SELECT * FROM $table WHERE product_id = $product_id";

		$stock = $wpdb->get_results($sql);

		if(count($stock) > 0){

		    return $stock[0];

		}else{

			return false;

		}

	}

	function get_customer($customer_id){

		global $wpdb;

		$table = $wpdb->prefix . 'sms_customers';

		$sql = "SELECT * FROM $table WHERE customer_id = $customer_id";

		$customers = $wpdb->get_results($sql);

		if(count($customers) > 0){

		    return $customers[0];

		}else{

			return false;

		}

	}

	
	function save_sale(){
		global $wpdb;
		$date = date('Y-m-d');

		$payload = $_POST['payload'];

		// Get invoice data
		$invoice_data = $payload['invoice_data'];
		$invoice_no = $invoice_data['invoice_no'];
		$invoice_data_arr = json_decode(stripcslashes($invoice_data['data']), true);
		$invoice_data = maybe_serialize($invoice_data_arr);

		// Update Stock
		foreach ($invoice_data_arr as $index => $product ) {
			$product_id = intval($product['prod_id']);
			$product_quantity = intval($product['prod_quantity']);
			// Store invoice data
			$table_stock = $wpdb->prefix . 'sms_stock';
			$stock_old_quantity = intval($wpdb->get_var("SELECT stock_quantity FROM $table_stock WHERE product_id = $product_id"));
			$new_stock_quantity = $stock_old_quantity - $product_quantity;
			$sql_stock = "UPDATE $table_stock SET stock_quantity = $new_stock_quantity, restock_date = '$date' WHERE product_id = $product_id";
			$wpdb->query($sql_stock);
		}

		// Store invoice data
		$table_invoices = $wpdb->prefix . 'sms_invoices';
		$sql_invoices = "INSERT INTO $table_invoices (invoice_no, invoice_data, date) 
		VALUES ($invoice_no, '$invoice_data', '$date')";
		$wpdb->query($sql_invoices);
		// Get the invoice id, just added
		$invoice_id = $wpdb->insert_id;


		// Get customer data
		$customer_data = $payload['customer_data'];
		$customer_name = sanitize_text_field($customer_data['customer_name']);
		$customer_email = sanitize_text_field($customer_data['customer_email']);
		// Store customer data
		$table_customers = $wpdb->prefix . 'sms_customers';
		$sql_customers = "INSERT INTO $table_customers (name, email, date) 
		VALUES ('$customer_name', '$customer_email', '$date')";
		$wpdb->query($sql_customers);
		// Get the customer id, just added
		$customer_id = $wpdb->insert_id;
		
		
		// Get sale data
		$sale_data = $payload['sale_data'];
		$quantity = $sale_data['quantity'];
		$gross_total = $sale_data['gross_total'];
		$discount = $sale_data['discount'];
		$net_total = $sale_data['net_total'];
		$sale_type = $sale_data['sale_type'];
		$payment_method = $sale_data['payment_method'];
		$payment_status = $sale_data['payment_status'];

		$table_sales = $wpdb->prefix . 'sms_sales';
		$sql_sales = "INSERT INTO $table_sales (invoice_id, customer_id, quantity, gross_total, discount, net_total, sale_type, payment_method, payment_status, date) 
		VALUES ($invoice_id, $customer_id, $quantity, $gross_total, $discount, $net_total, '$sale_type', '$payment_method', '$payment_status', '$date')";
		$result = $wpdb->query($sql_sales);
		if($result){
			wp_send_json_success($result);
		}else{
			wp_send_json_error($result);
		}
	}
	add_action('wp_ajax_save_sale', 'save_sale');
	add_action('wp_ajax_nopriv_save_sale', 'save_sale');


	// Ajax Request to get product data
	function get_product_rate(){
		$product_id = intval(sanitize_text_field($_POST['product_id']));
		global $wpdb;
		$table = $wpdb->prefix . 'sms_products';
		$sql = "SELECT * FROM $table WHERE product_id = $product_id";
		$product = $wpdb->get_results($sql);
		wp_send_json_success(json_encode($product[0]));
	}
	add_action('wp_ajax_get_product_rate', 'get_product_rate');
	add_action('wp_ajax_nopriv_get_product_rate', 'get_product_rate');

	function get_purchase(){
		$purchase_id = intval(sanitize_text_field($_POST['purchase_id']));
		global $wpdb;
		$table = $wpdb->prefix . 'sms_purchases';
		$sql = "SELECT * FROM $table WHERE product_id = $purchase_id";
		$purchase = $wpdb->get_results($sql);
		wp_send_json_success(json_encode($purchase[0]));
	}
	add_action('wp_ajax_get_purchase', 'get_purchase');
	add_action('wp_ajax_nopriv_get_purchase', 'get_purchase');


	function sms_delete(){
		$id = intval(sanitize_text_field(stripcslashes($_POST['id'])));
		$table_name = sanitize_text_field( $_POST['table_name'] );
		$id_col_name = sanitize_text_field( $_POST['id_col_name'] );
		global $wpdb;
		$table = $wpdb->prefix . $table_name;
		$sql = "DELETE FROM $table WHERE $id_col_name = $id";
		$is_product_deleted = $wpdb->query($sql);
		if($is_product_deleted){
			wp_send_json_success();
		}else{
			wp_send_json_error();
		}
	}
	add_action('wp_ajax_sms_delete', 'sms_delete');
	add_action('wp_ajax_nopriv_sms_delete', 'sms_delete');

	// Processing Returns
	function sm_handle_product_return() {
	    global $wpdb;
	    $product_id = intval($_POST['product_id']);
	    $quantity = intval($_POST['quantity']);
	    $return_reason = sanitize_text_field($_POST['return_reason']);
	    $invoice_no = sanitize_text_field($_POST['invoice_no']);

	    if ($product_id && $quantity > 0) {
	        // Insert return record into the sales_returns table
	        $wpdb->insert(
	            $wpdb->prefix . 'sms_sales_returns',
	            array(
	                'product_id' 	=> $product_id,
	                'quantity' 		=> $quantity,
	                'return_reason' => $return_reason,
	                'invoice_no' 	=>	$invoice_no,
	            ),
	            array('%d', '%d', '%s', '%s'),
	        );

	        // Update product stock in your product stock management logic
	        // Assuming a function `sm_update_product_stock` exists to handle stock adjustment
	        sm_update_product_stock($product_id, $quantity, 'add'); // 'add' to increment stock
	        
	        // Respond with success
	        wp_send_json_success(array('message' => 'Product return processed successfully.'));
	    } else {
	        wp_send_json_error(array('message' => 'Invalid return data.'));
	    }

	    wp_die(); // Terminate AJAX request
	}
	add_action('wp_ajax_handle_product_return', 'sm_handle_product_return');
	add_action('wp_ajax_nopriv_handle_product_return', 'sm_handle_product_return');


	// Function for getting sale(single) by using invoice_id
	function get_sale_by_invoice_id($invoice_id){

		global $wpdb;

		$table = $wpdb->prefix . 'sms_sales';

		$sql = "SELECT * FROM $table WHERE invoice_id = $invoice_id";

		$sales = $wpdb->get_results($sql);

		if(count($sales) > 0){

		    return $sales[0];

		}else{

			return false;

		}

	}

	function get_invoiced_products() {
	    
	    $invoice_no = intval($_POST['invoice_no']);
	    if($invoice_no){
	    	$invoice_data = get_invoice_data_by_no($invoice_no);
	    	$invoice_data = maybe_unserialize( $invoice_data->invoice_data );
	    	wp_send_json($invoice_data);
	    }

		// global $wpdb;
	

	    // wp_die(); // Terminate AJAX request
	}
	add_action('wp_ajax_get_invoiced_products', 'get_invoiced_products');
	add_action('wp_ajax_nopriv_get_invoiced_products', 'get_invoiced_products');

	// Function for getting sale(single) by using invoice_id
	function get_invoice_data_by_no($invoice_no){

		global $wpdb;

		$table = $wpdb->prefix . 'sms_invoices';

		$sql = "SELECT invoice_data FROM $table WHERE invoice_no = $invoice_no";

		$invoice_data = $wpdb->get_results($sql);

		if(count($invoice_data) > 0){

		    return $invoice_data[0];

		}else{

			return false;

		}

	}


// PRODUCT ANALYTICS WIDGETS
function fbm_display_top_selling_products($limit = 5) {
    global $wpdb;
    
    // Get all invoices
    $invoices = $wpdb->get_results("
        SELECT invoice_data 
        FROM {$wpdb->prefix}sms_invoices
    ");
    
    $product_sales = [];
    
    foreach ($invoices as $invoice) {
        $invoice_data = maybe_unserialize($invoice->invoice_data);
        
        if (is_array($invoice_data)) {
            foreach ($invoice_data as $item) {
                if (isset($item['prod_id'])) {
                    $product_id = $item['prod_id'];
                    $quantity = isset($item['prod_quantity']) ? (int)$item['prod_quantity'] : 0;
                    
                    if (!isset($product_sales[$product_id])) {
                        $product_sales[$product_id] = [
                            'quantity' => 0,
                            'name' => isset($item['prod_name']) ? $item['prod_name'] : 'Unknown Product'
                        ];
                    }
                    $product_sales[$product_id]['quantity'] += $quantity;
                }
            }
        }
    }
    
    // Sort by sales quantity
    uasort($product_sales, function($a, $b) {
        return $b['quantity'] - $a['quantity'];
    });
    
    // Get top products
    $top_products = array_slice($product_sales, 0, $limit, true);
    
    if (!empty($top_products)) {
        echo '<ol>';
        foreach ($top_products as $product_id => $data) {
            // Try to get current product name from products table
            $product_name = $wpdb->get_var($wpdb->prepare(
                "SELECT product_name FROM {$wpdb->prefix}sms_products WHERE product_id = %d", 
                $product_id
            )) ?: $data['name'];
            
            echo "<li>{$product_name} <strong>({$data['quantity']} sold)</strong></li>";
        }
        echo '</ol>';
    } else {
        echo '<p>No sales data found.</p>';
    }
}

function fbm_display_most_profitable_products($limit = 5) {
    global $wpdb;
    
    // Get all invoices
    $invoices = $wpdb->get_results("
        SELECT invoice_data 
        FROM {$wpdb->prefix}sms_invoices
    ");
    
    $product_profits = [];
    
    foreach ($invoices as $invoice) {
        $invoice_data = maybe_unserialize($invoice->invoice_data);
        
        if (is_array($invoice_data)) {
            foreach ($invoice_data as $item) {
                if (isset($item['prod_id'])) {
                    $product_id = $item['prod_id'];
                    $quantity = isset($item['prod_quantity']) ? (int)$item['prod_quantity'] : 0;
                    $total_amount = isset($item['prod_total_amount']) ? (float)$item['prod_total_amount'] : 0;
                    
                    // Calculate unit price
                    $unit_price = $quantity > 0 ? ($total_amount / $quantity) : 0;
                    
                    // Get product cost from products table
                    $product_cost = $wpdb->get_var($wpdb->prepare(
                        "SELECT product_purchase_price FROM {$wpdb->prefix}sms_products WHERE product_id = %d", 
                        $product_id
                    ));
                    
                    if ($product_cost !== null) {
                        $profit = ($unit_price - $product_cost) * $quantity;
                        
                        if (!isset($product_profits[$product_id])) {
                            $product_profits[$product_id] = [
                                'profit' => 0,
                                'name' => isset($item['prod_name']) ? $item['prod_name'] : 'Unknown Product'
                            ];
                        }
                        $product_profits[$product_id]['profit'] += $profit;
                    }
                }
            }
        }
    }
    
    // Sort by profit
    uasort($product_profits, function($a, $b) {
        return $b['profit'] - $a['profit'];
    });
    
    // Get top products
    $top_products = array_slice($product_profits, 0, $limit, true);
    
    if (!empty($top_products)) {
        echo '<ol>';
        foreach ($top_products as $product_id => $data) {
            // Try to get current product name from products table
            $product_name = $wpdb->get_var($wpdb->prepare(
                "SELECT product_name FROM {$wpdb->prefix}sms_products WHERE product_id = %d", 
                $product_id
            )) ?: $data['name'];
            
            $formatted_profit = number_format($data['profit'], 2);
            echo "<li>{$product_name} <strong>(PKR {$formatted_profit} profit)</strong></li>";
        }
        echo '</ol>';
    } else {
        echo '<p>No profit data available.</p>';
    }
}

function fbm_display_low_stock_products($limit = 5) {
    global $wpdb;
    
    $results = $wpdb->get_results("
        SELECT p.product_id, p.product_name, st.stock_quantity, p.product_min_quantity
        FROM {$wpdb->prefix}sms_products p
        JOIN {$wpdb->prefix}sms_stock st ON p.product_id = st.product_id
        WHERE st.stock_quantity <= p.product_min_quantity
        ORDER BY st.stock_quantity ASC
        LIMIT $limit
    ");
    
    if ($results) {
        echo '<ul class="fbm-alert-list">';
        foreach ($results as $product) {
            $alert_class = ($product->stock_quantity == 0) ? 'fbm-critical' : 'fbm-warning';
            echo "<li class='{$alert_class}'>
                {$product->product_name} 
                <span>(Stock: {$product->stock_quantity} / Min: {$product->product_min_quantity})</span>
            </li>";
        }
        echo '</ul>';
    } else {
        echo '<p>All products are well-stocked. ✅</p>';
    }
}

function fbm_display_recent_sales($limit = 5) {
    global $wpdb;
    
    $results = $wpdb->get_results("
        SELECT s.sale_id, s.invoice_id, s.date, c.name as customer_name, s.net_total
        FROM {$wpdb->prefix}sms_sales s
        LEFT JOIN {$wpdb->prefix}sms_customers c ON s.customer_id = c.customer_id
        ORDER BY s.date DESC
        LIMIT $limit
    ");
    
    if ($results) {
    	$i = 1;
        echo '<div class="fbm-recent-sales">';
        foreach ($results as $sale) {
            echo '<div class="fbm-sale-item">';
            echo '<div class="fbm-sale-header">';
            echo '<span class="fbm-sale-id">#' . $i++ . '</span>';
            echo '<span class="fbm-sale-date">' . date('M j, Y', strtotime($sale->date)) . '</span>';
            echo '</div>';
            echo '<div class="fbm-sale-customer">' . ($sale->customer_name ?: 'Guest') . '</div>';
            echo '<div class="fbm-sale-amount">PKR ' . number_format($sale->net_total, 2) . '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No recent sales found.</p>';
    }
}


// Data preparation functions
function fbm_get_monthly_sales_data() {
    global $wpdb;
    
    $results = $wpdb->get_results("
        SELECT 
            DATE_FORMAT(date, '%Y-%m') as month,
            SUM(net_total) as total_sales
        FROM {$wpdb->prefix}sms_sales
        WHERE date >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
        GROUP BY month
        ORDER BY month ASC
    ");
    
    return $results;
}

function fbm_get_product_performance_data() {
    global $wpdb;
    
    // Get top 5 products by sales
    $products = $wpdb->get_results("
        SELECT p.product_id, p.product_name
        FROM {$wpdb->prefix}sms_products p
        ORDER BY p.product_id DESC
        LIMIT 5
    ");
    
    $performance_data = [];
    
    foreach ($products as $product) {
        $sales = $wpdb->get_var($wpdb->prepare("
            SELECT SUM(s.net_total)
            FROM {$wpdb->prefix}sms_sales s
            JOIN {$wpdb->prefix}sms_invoices i ON s.invoice_id = i.invoice_id
            WHERE i.invoice_data LIKE %s", '%"prod_id";i:' . $product->product_id . ';%'));
        
        $performance_data[] = [
            'product' => $product->product_name,
            'sales' => $sales ? $sales : 1
        ];

    }
    
    return $performance_data;
}

function update_product_rate(){
	global $wpdb;
	if(!isset($_POST['product_id']) || !isset($_POST['new_rate'])) {
	    wp_send_json_error('Missing required fields.');
	}

	$product_id = intval(sanitize_text_field($_POST['product_id']));
	$new_rate = intval(sanitize_text_field($_POST['new_rate']));

	$table_products = $wpdb->prefix . 'sms_products';
	$sql = $wpdb->prepare("UPDATE {$table_products}
	                       SET product_purchase_price = %f
	                       WHERE product_id = %d", $new_rate, $product_id);
	$updated = $wpdb->query($sql);

	if($updated !== false){
	    wp_send_json_success("Rate updated successfully.");
	} else {
	    wp_send_json_error("Failed to update rate.");
	}

}
add_action('wp_ajax_update_product_rate', 'update_product_rate');
add_action('wp_ajax_nopriv_update_product_rate', 'update_product_rate');