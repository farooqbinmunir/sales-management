<?php



	/* Sales Management - Custom Plugin functions */



	// Including utility functions to be available in this file
	require_once(FBM_PLUGIN_DIR . 'inc/utilities.php');

	// Enqueue scripts & styles for backend use
	function fbm_backend_enqueues(){
		// Enqueue Styles
		wp_enqueue_style('fbm_backend_styles', 
			FBM_PLUGIN_URL . '/assets/css/backend/backend.css', 
			'', time());

		wp_enqueue_style('fbm_css', 
			FBM_PLUGIN_URL . '/assets/css/backend/fbm.css', 
			'', time());
		wp_enqueue_style('bootstrap-css', 
			'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', 
			'', '1.0', 'all');

		// Enqueue Scripts
		wp_enqueue_script('fbm_backend_scripts', 
			FBM_PLUGIN_URL . '/assets/js/backend/backend.js', 
			['jquery', 'fbm_functions_js', 'fbm_print_sale_invoice_js'], 
			time(), true);

		wp_enqueue_script('fbm_functions_js', 
			FBM_PLUGIN_URL . '/assets/js/backend/functions.js', 
			['jquery'], 
			time(), true);

		wp_enqueue_script('fbm_backend_fbm_js', 
			FBM_PLUGIN_URL . '/assets/js/backend/fbm.js', 
			['jquery', 'fbm_functions_js', 'fbm_print_sale_invoice_js'], 
			time(), true);

		wp_enqueue_script('fbm_print_purchase_invoice_js', 
			FBM_PLUGIN_URL . '/assets/js/backend/print-purchase-invoice.js',
			['jquery'], 
			time(), true);

		wp_enqueue_script('fbm_print_sale_invoice_js', 
			FBM_PLUGIN_URL . '/assets/js/backend/print-sale-invoice.js', 
			['jquery'], 
			time(), true);

		wp_enqueue_script('fbm_returns_js', 
			FBM_PLUGIN_URL . '/assets/js/backend/returns.js', 
			['jquery'], 
			time(), true);

		wp_enqueue_script('fbm_purchase_js', 
			FBM_PLUGIN_URL . '/assets/js/backend/purchase.js', 
			['jquery'], 
			time(), true);

		wp_enqueue_script('bootstrap-js', 
			'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', 
			'jquery', 
			'1.0', false);

		// Enqueue Chart.js
	    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.7.1', true);

	    wp_enqueue_script('fbm-analytics-charts-js', 
	    	FBM_PLUGIN_URL . '/assets/js/backend/analytics-charts.js', 
	    	['chart-js'], 
	    	'1.0', true);

		$current_user = wp_get_current_user();
		$user_display_name = $current_user->display_name;
		$localized_data = array(
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce(FBM_PLUGIN_NONCE),
			'path' => site_url(),
			'current_user' => $user_display_name,
		);
		wp_localize_script('fbm_backend_scripts', 'fbm_ajax', $localized_data);
		wp_localize_script('fbm_backend_fbm_js', 'fbm_ajax', $localized_data);
		wp_localize_script('jquery', 'fbm_ajax', $localized_data);

		// Pass data to JS
	    wp_localize_script('fbm-analytics-charts-js', 'fbmChartData', [
	        'monthlySales' => fbm_get_monthly_sales_data(),
	        'productPerformance' => fbm_get_product_performance_data()
	    ]);

	    // Enqueueing LIB SELECT2
	    wp_enqueue_style('fbm-select2-css', FBM_PLUGIN_URL . '/assets/css/backend/select2.css', '', '4.1.0');
	    wp_enqueue_script('fbm-select2-js', FBM_PLUGIN_URL . '/assets/js/backend/select2.js', ['chart-js'], '4.1.0');
	}



	// Enqueue scripts & styles which are used by the plugin

	function fbm_frontend_enqueues(){



		// Enqueue Styles

		wp_enqueue_style('fbm_frontend_styles', FBM_PLUGIN_URL . '/assets/css/frontend/frontend.css', '', time(), 'all');

		wp_enqueue_style('slick_css', FBM_PLUGIN_URL . '/assets/css/frontend/slick.css', '', '1.0', 'all');



		// Enqueue Scripts

		wp_enqueue_script('slick_js', FBM_PLUGIN_URL . '/assets/js/frontend/slick.min.js', ['jquery'], '1.0');

		wp_enqueue_script('frontend_js', FBM_PLUGIN_URL . '/assets/js/frontend/frontend.js', ['jquery'], time(), true);
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

			// New page for Pending Payments
			add_submenu_page( 

				sanitize_title_with_dashes(FBM_PLUGIN_MENU_NAME), 

				'Pending Payments',

				'Pending Payments',

				'manage_options', 

				'pending-payments',

				'pending_payments_callback',

			);

			// Detail page for Purchase Invoice
			add_submenu_page( 

				null, 

				'Purchase Invoice Details',
				'Purchase Invoice Details',
				'manage_options', 
				'purchase_invoice_details',
				'purchase_invoice_details_callback',

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

	
	// Submenu page for Invoice Details
	function pending_payments_callback(){
		require_once(FBM_PLUGIN_DIR . 'templates/pending-payments-panel.php');
	}

	// Submenu page for Purchase Invoice Details
	function purchase_invoice_details_callback(){
		require_once(FBM_PLUGIN_DIR . 'templates/purchase-invoice-panel.php');
	}

	// Perform actions on wordpress init - hook
	function fbm_init_callback() {
		flush_rewrite_rules();
	}

	





	function handle_product() {



		$payload = json_decode(stripcslashes($_POST['payload']));

		$action = strtolower(sanitize_text_field($_POST['required_action']));

		$product_name = ucwords(str_replace(['-', '_'], ' ', sanitize_text_field($payload->product_name)));

		$product_purchase_price = sanitize_text_field($payload->product_purchase_price);

		$product_sale_price = sanitize_text_field($payload->product_sale_price);

		$product_vendor = sanitize_text_field($payload->product_vendor);
		$product_manufacturer = intval(sanitize_text_field($payload->product_manufacturer));

		$product_location = sanitize_text_field($payload->product_location);
		$product_min_quantity = +sanitize_text_field($payload->product_min_quantity);



		global $wpdb;

		$table = $wpdb->prefix . 'sms_products';

		if($action == 'update'){
			$product_id = intval(sanitize_text_field($_POST['id']));
			$update_product_query = "UPDATE {$table} SET product_name = '{$product_name}', product_purchase_price = {$product_purchase_price}, product_sale_price = {$product_sale_price}, product_vendor = '{$product_vendor}', product_manufacturer = $product_manufacturer, product_location = '{$product_location}', product_min_quantity = {$product_min_quantity} WHERE product_id = {$product_id}";	
			$result = $wpdb->query($update_product_query);
			if($result){

				wp_send_json_success();
	
			}else{
	
				wp_send_json_error();
	
			}

		}elseif( $action == 'add'){
			$query = $wpdb->prepare("INSERT INTO {$table} (product_name, product_purchase_price, product_sale_price, product_vendor, product_manufacturer, product_location, product_min_quantity) VALUES (%s, %d, %d, %s, %d, %s, %d)", $product_name, $product_purchase_price, $product_sale_price, $product_vendor, $product_manufacturer, $product_location, $product_min_quantity);
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

	

	add_action('wp_ajax_handle_product', 'handle_product');
	add_action('wp_ajax_nopriv_handle_product', 'handle_product');





	function handle_purchase(){
		global $wpdb;

		$payload = json_decode(stripcslashes($_POST['payload']));
		$purchase_info = json_decode(stripcslashes($_POST['purchase_info']));
		$purchase_invoice = intval($purchase_info->invoice);
		
		$total_payment = $purchase_info->totalPayment;
		$payment_status = ucwords(str_replace(['-', '_'], ' ', sanitize_text_field($purchase_info->paymentStatus)));
		$payment_method = $purchase_info->paymentStatus === 'unpaid' ? '' : ucwords(str_replace(['-', '_'], ' ', sanitize_text_field($purchase_info->paymentMethod)));
		$description = ucfirst(sanitize_text_field($purchase_info->description));

		$purchase_paid_amount = (int) $purchase_info->paymentPaid;
		$purchase_remaining_amount = (int) $purchase_info->paymentRemaining;

		// Prepare DB tables and date for saving/updating database
		$date = date('Y-m-d');
		$table_purchase = $wpdb->prefix . 'sms_purchases';
		$table_purchase_invoices = $wpdb->prefix . 'sms_purchase_invoices';
		$table_stock = $wpdb->prefix . 'sms_stock';
		$table_products = $wpdb->prefix . 'sms_products';

		// To store query results success/failure
		$response = [];
		$errors = [];

		// Add entry in Purchase Table
		$purchase_sql = "INSERT INTO $table_purchase (total_payment, paid, due, payment_status, payment_method, description, purchase_invoice, date) VALUES ($total_payment, $purchase_paid_amount, $purchase_remaining_amount, '$payment_status', '$payment_method', '$description', '$purchase_invoice', '$date');";
		// wp_send_json_success($purchase_sql);
		$rows_inserted = $wpdb->query($purchase_sql);

		$purchase_id = $wpdb->insert_id;

		if($rows_inserted){
			$response[] = "✅ Purchase added successfully!";
			if ($purchase_remaining_amount > 0) {
				$due_type = 'purchase';
				$saleman_id = 1; // Default saleman ID (--SALE-MAN--)

				// Create due record
				$due_added = fbm_dues_create_from_sale($purchase_id, $saleman_id, $total_payment, $purchase_paid_amount, $due_type);
				if($due_added){
					$response[] = "✅ Due payment initiated successfully!";
				}else{
					$errors[] = "❌ Failed to initiate due payment!";
				}
			}
		}else{
			$errors[] = "❌ Failed to save purchase data!";
		}

		
		// Group the prodcuts purchased to make single entry
		$purchase_invoice_data = [];
		$stockUpdated = [];
		$producstUpdated = [];
		foreach($payload as $product){

			// Get the product details
			$product_id = intval(sanitize_text_field($product->product_id));
			$manufacturer_id = intval(sanitize_text_field($product->manufacturer_id));
			$vendor = sanitize_text_field($product->vendor);
			$quantity = intval(sanitize_text_field($product->quantity));
			$purchase_rate = intval(sanitize_text_field($product->rate));
			$total_payment = intval(sanitize_text_field($product->payment));
			
			// Prepare single product data
			$purchased_single_product = [
				'product_id'	=>	$product_id,
				'manufacturer_id'	=>	$manufacturer_id,
				'vendor'	=>	$vendor,
				'quantity'	=>	$quantity,
				'purchase_rate'	=>	$purchase_rate,
				'total_payment'	=>	$total_payment,
			];
			array_push($purchase_invoice_data, $purchased_single_product);

			// Update the stocks table
			$available_stock = intval($wpdb->get_var("SELECT stock_quantity FROM $table_stock WHERE product_id = $product_id"));
			$stock_new_quantity = $quantity > 0 ? $available_stock + $quantity : $available_stock;
			$stockSQL = "UPDATE $table_stock SET stock_quantity = $stock_new_quantity WHERE product_id = $product_id";
			$stockQueryResult = $wpdb->query($stockSQL);
			
			if(!$stockQueryResult){ // If update failed, means record not exists, so need to insert new record
				$product = get_product($product_id);
				$stock_location = $product->product_location;
				$low_stock_alert = $product->product_min_quantity;
				
				$stockSQL = $wpdb->prepare("INSERT INTO $table_stock (product_id, stock_quantity, stock_location, restock_date, low_stock_alert) VALUES(%d, %d, %s, %s, %d)", $product_id, $stock_new_quantity, $stock_location, $date, $low_stock_alert);

				$stockQueryResult = $wpdb->query($stockSQL);
			}
			if($stockQueryResult){
				$stockUpdated[] = true;
			}else{
				$stockUpdated[] = false;
			}

			
		}

		if(in_array(false, $stockUpdated)){
			$errors[] = "❌ Failed to update stock!";
		}else{
			$response[] = "✅ Stock updated successfully!";
		}

		// Serialize the purchased products to make it string as it'll be stored in a TEXT column in DB
		$purchase_invoice_data_serialized = maybe_serialize($purchase_invoice_data);

		// Add entry in Purchase Invoces table referencing with purchase_invoice
		$purchase_invoice_sql = "INSERT INTO $table_purchase_invoices (purchase_invoice, invoice_data, date) VALUES ($purchase_invoice, '$purchase_invoice_data_serialized', '$date');";
		$pinvoice_rows_inserted = $wpdb->query($purchase_invoice_sql);
		
		if($pinvoice_rows_inserted){
			$response[] = "✅ Purchase Invoice saved successfully!";
		}else{
			$errors[] = "❌ Failed to save Purchase Invoice!";
		}

		if(empty($errors)){
			wp_send_json_success($response);
		}else{
			wp_send_json_error($errors);
		}
		

	}

	add_action('wp_ajax_handle_purchase', 'handle_purchase');

	add_action('wp_ajax_nopriv_handle_purchase', 'handle_purchase');





	function get_product($product_id){

		global $wpdb;

		$table = $wpdb->prefix . 'sms_products';
		$sanitized_product_id = intval(sanitize_text_field( $product_id ));
		if($sanitized_product_id){
			$sql = "SELECT * FROM $table WHERE product_id = $sanitized_product_id";
			$products = $wpdb->get_results($sql);
			if(count($products) > 0){

			    return $products[0];

			}else{

				return false;

			}
		}
	}

	function get_products(){
		global $wpdb;
		$table = $wpdb->prefix . 'sms_products';
		$sql = "SELECT * FROM $table";
		$products = $wpdb->get_results($sql);
		return $products;
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

	function get_saleman($saleman_id){

		global $wpdb;

		$table = $wpdb->prefix . 'sms_salemans';

		$sql = "SELECT * FROM $table WHERE id = $saleman_id";

		$saleman = $wpdb->get_row($sql);

		return $saleman;

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

		// Get Customer data
		$customer_id = 1; // Defualt customer  --WALKING-CUSTOMER-- database id
		$customer_data = $payload['customer_data'];
		$cname = sanitize_text_field($customer_data['cname']);
		$cphone = sanitize_text_field($customer_data['cphone']);
		$cemail = sanitize_text_field($customer_data['cemail']);
		$caddress = sanitize_text_field($customer_data['caddress']);

		// Save customer data
		$table_customers = $wpdb->prefix . 'sms_customers';
		$table_products = $wpdb->prefix . 'sms_products';
		if($cname && $cphone):
			$sql_customers = $wpdb->prepare("INSERT INTO $table_customers (name, phone, email, address, date) 
			VALUES (%s, %s, %s, %s, %s)", $cname, $cphone, $cemail, $caddress, $date);
			$wpdb->query($sql_customers);

			// Get the customer id, just added
			$customer_id = $wpdb->insert_id;
		endif;

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

			// Update sold quantity in products table
			$sql_products = "UPDATE $table_products SET sold = sold + $product_quantity WHERE product_id = $product_id";
			$wpdb->query($sql_products);


		}

		// Store invoice data
		$table_invoices = $wpdb->prefix . 'sms_invoices';
		$sql_invoices = "INSERT INTO $table_invoices (invoice_no, invoice_data, date) 
		VALUES ($invoice_no, '$invoice_data', '$date')";
		$wpdb->query($sql_invoices);

		// Get the invoice id, just added
		$invoice_id = $wpdb->insert_id;
		
		
		// Get sale data
		$sale_data = $payload['sale_data'];
		$quantity = $sale_data['quantity'];
		$gross_total = $sale_data['gross_total'];
		$discount = $sale_data['discount'];
		$net_total = $sale_data['net_total'];
		$sale_type = $sale_data['sale_type'];
		$payment_method = $sale_data['payment_method'];
		$payment_status = $sale_data['payment_status'];

		$profit = $sale_data['profit'];
		$sales_person = intval($sale_data['sales_person']);

		$paid_amount  = intval($sale_data['paid_amount']);
		$due_amount  = intval($sale_data['due_amount']);
		$due_type = 'sale';

		$table_sales = $wpdb->prefix . 'sms_sales';
		$sql_sales = "INSERT INTO $table_sales (invoice_id, customer_id, quantity, gross_total, discount, net_total, profit, sales_man, sale_type, payment_method, payment_status, date) 
		VALUES ($invoice_id, $customer_id, $quantity, $gross_total, $discount, $net_total, $profit, $sales_person, '$sale_type', '$payment_method', '$payment_status', '$date')";

		$insertedSalesRows = $wpdb->query($sql_sales);
		$sale_id = $wpdb->insert_id;

		if($insertedSalesRows){

			if ($due_amount > 0) {
				// Create due record
				$due_added = fbm_dues_create_from_sale($sale_id, $customer_id, $net_total, $paid_amount, $due_type);
				if($due_added){
					wp_send_json_success($due_added);
				}
				wp_send_json_success();
			}
			wp_send_json_success();
		}else{
			wp_send_json_error('Failed to add sale entry.');
		}
	}
	add_action('wp_ajax_save_sale', 'save_sale');
	add_action('wp_ajax_nopriv_save_sale', 'save_sale');


	// Ajax Request to get product data
	function get_product_details(){
		$product_id = intval(sanitize_text_field($_POST['product_id']));
		$product = get_product($product_id);
		$manufacturer_id = $product->product_manufacturer;
		$manufacturer_name = get_manufacturer_name($manufacturer_id);
		$response = [
			'product' => json_encode($product),
			'manufacturer' => [
				'id' => $manufacturer_id,
				'name' => $manufacturer_name,
			]
		];
		wp_send_json_success($response);
	}
	add_action('wp_ajax_get_product_details', 'get_product_details');
	add_action('wp_ajax_nopriv_get_product_details', 'get_product_details');

	function get_purchase(){
		$purchase_id = intval(sanitize_text_field($_POST['purchase_id']));
		global $wpdb;
		$table = $wpdb->prefix . 'sms_purchases';
		$sql = "SELECT * FROM $table WHERE purchase_id = $purchase_id";
		$purchase = $wpdb->get_results($sql);
		wp_send_json_success(json_encode($purchase[0]));
	}
	add_action('wp_ajax_get_purchase', 'get_purchase');
	add_action('wp_ajax_nopriv_get_purchase', 'get_purchase');

	function get_purchase_by_id($purchase_id){
		$purchase_id = intval(sanitize_text_field($purchase_id));
		global $wpdb;
		$table = $wpdb->prefix . 'sms_purchases';
		$sql = $wpdb->prepare("SELECT * FROM $table WHERE purchase_id = %d", $purchase_id);
		return $wpdb->get_row($sql);

	}

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
	    $amount = intval($_POST['amount']);
	    $return_reason = sanitize_text_field($_POST['return_reason']);
	    $invoice_no = sanitize_text_field($_POST['invoice_no']);

	    if ($product_id && $quantity > 0) {
	        // Insert return record into the sales_returns table
	        $wpdb->insert(
	            $wpdb->prefix . 'sms_sales_returns',
	            array(
	                'product_id' 	=> $product_id,
	                'quantity' 		=> $quantity,
	                'amount' 		=> $amount,
	                'return_reason' => $return_reason,
	                'invoice_no' 	=>	$invoice_no,
	            ),
	            array('%d', '%d', '%d', '%s', '%s'),
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
function fbm_display_top_selling_products($limit = -1) {
    global $wpdb;
    $product_sales = [];
    $products = get_products();

    usort($products, function($a, $b) {
	    return $b->sold <=> $a->sold; // descending order
	});
    
    // Get top products
    $top_products = ($limit == -1) ? $products : array_slice($products, 0, $limit, true);
    
    if (!empty($top_products)) {
        echo '<ol>';
        foreach ($top_products as $product) {            
            echo "<li>{$product->product_name} <strong>({$product->sold} sold)</strong></li>";
        }
        echo '</ol>';
    } else {
        echo '<p>No sales data found.</p>';
    }
}

function fbm_display_most_profitable_products($limit = 5) {
    global $wpdb;
    $product_sales = [];
    $products = get_products();

	// Sort by profit
	usort($products, function($a, $b) {
	    $profitA = $a->product_sale_price - $a->product_purchase_price;
	    $profitB = $b->product_sale_price - $b->product_purchase_price;

	    return $profitB <=> $profitA; // highest profit first
	});

	// Apply limit
	$top_products = ($limit == -1) ? $products : array_slice($products, 0, $limit);

	// Output
	if (!empty($top_products)) {
	    echo '<ol>';

	    foreach ($top_products as $data) {
	        $profit = $data->product_sale_price - $data->product_purchase_price;
	        $formatted_profit = number_format($profit, 2);
	        $product_name = $data->product_name;

	        echo "<li>{$product_name} <strong>(PKR {$formatted_profit} profit)</strong></li>";
	    }

	    echo '</ol>';
	} else {
	    echo '<p>No profit data available.</p>';
	}

}

function fbm_display_low_stock_products($limit = 5) {
    global $wpdb;
	$limit_sql = ($limit == -1) ? "" : "LIMIT $limit";
    
    $results = $wpdb->get_results("
        SELECT p.product_id, p.product_name, st.stock_quantity, p.product_min_quantity
        FROM {$wpdb->prefix}sms_products p
        JOIN {$wpdb->prefix}sms_stock st ON p.product_id = st.product_id
        WHERE st.stock_quantity <= p.product_min_quantity
        ORDER BY st.stock_quantity ASC
        $limit_sql
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
    $limit_sql = ($limit == -1) ? "" : "LIMIT $limit";
    $results = $wpdb->get_results("
        SELECT s.sale_id, s.invoice_id, s.date, c.name as customer_name, s.net_total
        FROM {$wpdb->prefix}sms_sales s
        LEFT JOIN {$wpdb->prefix}sms_customers c ON s.customer_id = c.customer_id
        ORDER BY s.date DESC
        $limit_sql
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
	if(!isset($_POST['product_id']) || !isset($_POST['purchase_new_rate']) || !isset($_POST['sale_new_rate'])) {
	    wp_send_json_error('Missing required fields.');
	}

	$product_id = intval(sanitize_text_field($_POST['product_id']));
	$purchase_new_rate = intval(sanitize_text_field($_POST['purchase_new_rate']));
	$sale_new_rate = intval(sanitize_text_field($_POST['sale_new_rate']));

	$table_products = $wpdb->prefix . 'sms_products';
	$sql = $wpdb->prepare("UPDATE {$table_products}
	                       SET 	product_purchase_price = %d, 
						   		product_sale_price = %d
	                       WHERE product_id = %d", $purchase_new_rate, $sale_new_rate, $product_id);
	$updated = $wpdb->query($sql);

	if($updated !== false){
	    wp_send_json_success("Rate updated successfully.");
	} else {
	    wp_send_json_error("Failed to update rate.");
	}

}
add_action('wp_ajax_update_product_rate', 'update_product_rate');
add_action('wp_ajax_nopriv_update_product_rate', 'update_product_rate');

// Get the Manufacturer by it's ID
function get_manufacturer_name($manufacturer_id){
	global $wpdb;
	$table_manufacturers = $wpdb->prefix . "sms_manufacturers";
	$manufacturer_name = $wpdb->get_var("SELECT manufacturer_name FROM {$table_manufacturers} WHERE manufacturer_id = {$manufacturer_id}");
	if($manufacturer_name){
		return $manufacturer_name;
	}else{
		return false;
	}
}


function delete_entries_older_than_two_years_once_per_day(){
    if (get_transient('entries_cleanup_ran_today')) {
        return; // Already ran today
    }

    global $wpdb;
    $table_sales   = $wpdb->prefix . "sms_sales";
    $table_returns = $wpdb->prefix . "sms_sales_returns";
    $two_years_old_date = date('Y-m-d', strtotime('-2 years'));

    // Delete old sales
    $sql_delete_sales = $wpdb->prepare("DELETE FROM $table_sales WHERE date < %s", $two_years_old_date);
    $wpdb->query($sql_delete_sales);

    // Delete old returns
    $sql_delete_returns = $wpdb->prepare("DELETE FROM $table_returns WHERE date < %s", $two_years_old_date);
    $wpdb->query($sql_delete_returns);

    // Show admin notice
    add_action('admin_notices', function() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>✅ Old sales and returns older than 2 years were cleaned up today.</p>
        </div>
        <?php
    });

    // Set transient for 24 hours
    set_transient('entries_cleanup_ran_today', true, DAY_IN_SECONDS);
}



// Ajax for payment history for credit customer on pending payments page
function fbm_get_due_payments() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied');
    }

    global $wpdb;
    $due_id = isset($_GET['due_id']) ? intval($_GET['due_id']) : 0;
    if (!$due_id) {
        wp_send_json_error('Invalid due ID');
    }

    $table_dues      = $wpdb->prefix . 'sms_dues';
    $table_payments  = $wpdb->prefix . 'sms_dues_payments';

    // Fetch main due record
    $due = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_dues WHERE id = %d", $due_id)
    );
    if (!$due) {
        wp_send_json_error('Due not found.');
    }

    // Fetch payments
    $payments = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_payments WHERE due_id = %d ORDER BY payment_date ASC", $due_id)
    );

    $total_paid = 0;
    foreach ($payments as $p) {
        $total_paid += (float)$p->payment_amount;
    }

    ob_start();
    ?>
    <table class="widefat striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($payments): ?>
                <?php foreach ($payments as $i => $p): ?>
                    <tr>
                        <td><?php echo $i+1; ?></td>
                        <td><?php echo number_format((float)$p->payment_amount, 2); ?></td>
                        <td><?php echo esc_html($p->payment_date); ?></td>
                        <td><?php echo esc_html($p->note); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4"><em>No payments yet.</em></td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align:right"><strong>Total Sale:</strong></th>
                <th><?php echo number_format((float)$due->total_amount, 2); ?></th>
            </tr>
            <tr>
                <th colspan="3" style="text-align:right"><strong>Total Paid:</strong></th>
                <th><?php echo number_format($total_paid, 2); ?></th>
            </tr>
            <tr>
                <th colspan="3" style="text-align:right"><strong>Remaining:</strong></th>
                <th><?php echo number_format((float)$due->remaining_amount, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
    $html = ob_get_clean();

    wp_send_json_success($html);
}
add_action('wp_ajax_fbm_get_due_payments', 'fbm_get_due_payments');
add_action('wp_ajax_nopriv_fbm_get_due_payments', 'fbm_get_due_payments');


function getNextSaleInvoiceNo(){
	$table_sale_invoices = 'sms_invoices';
	return getNextInvoice($table_sale_invoices);
}

function getNextPurchaseInvoiceNo(){
	$table_purchase_invoices = 'sms_purchase_invoices';
	return getNextInvoice($table_purchase_invoices);
}
function getNextInvoice($invoice_container_table){
	$today_date = date('Y-m-d');
    global $wpdb;
    $table_purchase_invoices = $wpdb->prefix . $invoice_container_table;
    $sql = "SELECT count(*) FROM $table_purchase_invoices WHERE date = '$today_date'";
    $todaysNextInvoiceCount = $wpdb->get_var($sql) + 1;
    $invoiceDate = date('Ymd');
    $invoiceNo = $invoiceDate . str_pad($todaysNextInvoiceCount,2,0, STR_PAD_LEFT);
    return $invoiceNo;
}

// Function for getting Purchase(single) by using invoice_id
function get_purchase_by_invoice_no($invoice_no){

	global $wpdb;

	$table = $wpdb->prefix . 'sms_purchases';

	$sql = "SELECT * FROM $table WHERE purchase_invoice = '$invoice_no'";

	$purchase = $wpdb->get_row($sql);
	return $purchase;

}

function get_saleman_by_invoice_no($invoice_no){
	return get_saleman(get_purchase_by_invoice_no($invoice_no)->saleman_id);
}


// Get filtered pending payments when requested by FILTER on pending payments page
add_action('admin_post_get_filtered_pending_payments', 'get_filtered_pending_payments');
function get_filtered_pending_payments(){
	// if(!$_POST['due_type']) return true;
	// $due_type = strtolower(sanitize_text_field($_POST['due_type']));
	// return fbm_dues_get_all('', '', $due_type);

	$due_type = isset($_POST['due_type']) ? sanitize_text_field($_POST['due_type']) : '';
	$due_type = strtolower($due_type);    // optional
	$results  = fbm_dues_get_all('', '', $due_type);

	// Next: redirect with results or render output



}
