<?php
	/* Sales Management - Custom Plugin functions */
	// Including utility functions to be available in this file
	require_once(FBM_PLUGIN_DIR . 'inc/utilities.php');

	function fbm_require_authenticated_ajax(){
		if (!wp_doing_ajax()) {
			return;
		}

		if (!is_user_logged_in()) {
			wp_send_json_error('Unauthorized request.', 401);
		}

		// Backward-compatible nonce validation: if provided by frontend, enforce it.
		if (isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], FBM_PLUGIN_NONCE)) {
			wp_send_json_error('Security check failed.', 403);
		}
	}

	// Enqueue scripts & styles for backend use
	function fbm_backend_enqueues(){
		// Enqueue Styles
		wp_enqueue_style('fbm_backend_styles', 
			FBM_PLUGIN_URL . '/assets/css/backend/backend.css', 
			'', time());

		wp_enqueue_style('fbm_css', 
			FBM_PLUGIN_URL . '/assets/css/backend/fbm.css', 
			'', time());

		wp_enqueue_style('popup-auth-css', 
		FBM_PLUGIN_URL . '/components/popup-auth/popup-auth.css', 
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

		wp_enqueue_script('popup-auth-js', 
			FBM_PLUGIN_URL . '/components/popup-auth/popup-auth.js', 
			['jquery'], 
			time(), true);

		wp_enqueue_script('fbm_key_events_js', 
			FBM_PLUGIN_URL . '/assets/js/backend/key-events.js', 
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

		wp_enqueue_script('search-field-js', 
	    	FBM_PLUGIN_URL . '/components/search-field/search-field.js', 
	    	['jquery'], 
	    	'1.0', true);

		wp_enqueue_script('search-filter-js', 
	    	FBM_PLUGIN_URL . '/components/search-filter/search-filter.js', 
	    	['jquery'], 
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

		// Tailwind CSS
	    wp_enqueue_script('tailwind-css', 'https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4', ['jquery'], '4.1.0');

		// Including Fixes js
		wp_enqueue_script('fbm-fixes-js', FBM_PLUGIN_URL . '/assets/js/backend/fixes.js', ['jquery'], time(), true);

		// Fontawesome
		wp_enqueue_style(
			'fbm-fontawesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
			[],
			'6.5.1'
		);
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

				'Customers',

				'Customers',

				'manage_options',

				'customers',

				'customers_callback',

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

	// Submenu page for Customers listing
	function customers_callback(){
		require_once(FBM_PLUGIN_DIR . 'templates/customers-panel.php');
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
		fbm_require_authenticated_ajax();


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
			$result = $wpdb->update(
				$table,
				[
					'product_name' => $product_name,
					'product_purchase_price' => $product_purchase_price,
					'product_sale_price' => $product_sale_price,
					'product_vendor' => $product_vendor,
					'product_manufacturer' => $product_manufacturer,
					'product_location' => $product_location,
					'product_min_quantity' => $product_min_quantity,
				],
				[
					'product_id' => $product_id,
				],
				['%s', '%f', '%f', '%s', '%d', '%s', '%d'],
				['%d']
			);
			if($result !== false){

				wp_send_json_success();
	
			}else{
	
				wp_send_json_error();
	
			}

		}elseif( $action == 'add'){
			$insert_result = $wpdb->insert(
				$table,
				[
					'product_name' => $product_name,
					'product_purchase_price' => $product_purchase_price,
					'product_sale_price' => $product_sale_price,
					'product_vendor' => $product_vendor,
					'product_manufacturer' => $product_manufacturer,
					'product_location' => $product_location,
					'product_min_quantity' => $product_min_quantity,
				],
				['%s', '%f', '%f', '%s', '%d', '%s', '%d']
			);
			if($insert_result === false){
				wp_send_json_error();
			}
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
		fbm_require_authenticated_ajax();
		global $wpdb;

		if (empty($_POST['payload']) || empty($_POST['purchase_info'])) {
			wp_send_json_error(['Invalid purchase payload.']);
		}

		$payload = json_decode(wp_unslash($_POST['payload']));
		$purchase_info = json_decode(wp_unslash($_POST['purchase_info']));
		if (!is_array($payload) || empty($payload) || !is_object($purchase_info)) {
			wp_send_json_error(['Invalid purchase data.']);
		}

		$purchase_invoice = isset($purchase_info->invoice) ? intval($purchase_info->invoice) : 0;
		if ($purchase_invoice <= 0) {
			wp_send_json_error(['Invalid purchase invoice number.']);
		}
		
		$total_payment = isset($purchase_info->totalPayment) ? (float) $purchase_info->totalPayment : 0;
		$payment_status_raw = isset($purchase_info->paymentStatus) ? sanitize_text_field((string) $purchase_info->paymentStatus) : '';
		$payment_status = ucwords(str_replace(['-', '_'], ' ', strtolower($payment_status_raw)));
		$payment_method_raw = isset($purchase_info->paymentMethod) ? sanitize_text_field((string) $purchase_info->paymentMethod) : '';
		$payment_method = strtolower($payment_status_raw) === 'unpaid' ? '' : ucwords(str_replace(['-', '_'], ' ', strtolower($payment_method_raw)));
		$description = isset($purchase_info->description) ? ucfirst(sanitize_text_field((string) $purchase_info->description)) : '';
		$vendor = isset($purchase_info->vendor) ? ucfirst(sanitize_text_field((string) $purchase_info->vendor)) : '';

		$purchase_paid_amount = isset($purchase_info->paymentPaid) ? (float) $purchase_info->paymentPaid : 0;
		$purchase_remaining_amount = isset($purchase_info->paymentRemaining) ? (float) $purchase_info->paymentRemaining : 0;
		$purchase_paid_amount = max(0, $purchase_paid_amount);
		$purchase_remaining_amount = max(0, $purchase_remaining_amount);
		$total_payment = max(0, $total_payment);

		if ($purchase_paid_amount > $total_payment) {
			wp_send_json_error(['Paid amount cannot exceed total payment.']);
		}
		if (abs(($purchase_paid_amount + $purchase_remaining_amount) - $total_payment) > 0.01) {
			$purchase_remaining_amount = max(0, $total_payment - $purchase_paid_amount);
		}

		// Prepare DB tables and date for saving/updating database
		$date = date('Y-m-d');
		$table_purchase = $wpdb->prefix . 'sms_purchases';
		$table_purchase_invoices = $wpdb->prefix . 'sms_purchase_invoices';
		$table_stock = $wpdb->prefix . 'sms_stock';

		// To store query results success/failure
		$response = [];
		$errors = [];

		// Add entry in Purchase Table
		$rows_inserted = $wpdb->insert(
			$table_purchase,
			[
				'total_payment' => $total_payment,
				'paid' => $purchase_paid_amount,
				'due' => $purchase_remaining_amount,
				'payment_status' => $payment_status,
				'payment_method' => $payment_method,
				'description' => $description,
				'vendor' => $vendor,
				'purchase_invoice' => $purchase_invoice,
				'date' => $date,
			],
			['%f', '%f', '%f', '%s', '%s', '%s', '%s', '%d', '%s']
		);

		$purchase_id = $wpdb->insert_id;

		if($rows_inserted){
			$response[] = 'Purchase added successfully.';
			if ($purchase_remaining_amount > 0) {
				$due_type = 'purchase';
				$saleman_id = 1; // Default saleman ID (--SALE-MAN--)

				// Create due record
				$due_added = fbm_dues_create_from_sale($purchase_id, $saleman_id, $total_payment, $purchase_paid_amount, $due_type);
				if($due_added){
					$response[] = 'Due payment initiated successfully.';
				}else{
					$errors[] = 'Failed to initiate due payment.';
				}
			}
		}else{
			$errors[] = 'Failed to save purchase data.';
			wp_send_json_error($errors);
			return;
		}

		
		// Group the products purchased to make single entry
		$purchase_invoice_data = [];
		$stockUpdated = [];
		foreach($payload as $product){

			$product_id = isset($product->product_id) ? intval(sanitize_text_field($product->product_id)) : 0;
			$manufacturer_id = isset($product->manufacturer_id) ? intval(sanitize_text_field($product->manufacturer_id)) : 0;
			$quantity = isset($product->quantity) ? intval(sanitize_text_field($product->quantity)) : 0;
			$purchase_rate = isset($product->rate) ? intval(sanitize_text_field($product->rate)) : 0;
			$item_total_payment = isset($product->payment) ? intval(sanitize_text_field($product->payment)) : 0;
			if ($product_id <= 0 || $quantity <= 0) {
				$errors[] = 'Invalid product or quantity in purchase payload.';
				continue;
			}
			
			$purchase_invoice_data[] = [
				'product_id' => $product_id,
				'manufacturer_id' => $manufacturer_id,
				'quantity' => $quantity,
				'purchase_rate' => $purchase_rate,
				'total_payment' => $item_total_payment,
			];

			$available_stock = intval($wpdb->get_var($wpdb->prepare("SELECT stock_quantity FROM $table_stock WHERE product_id = %d", $product_id)));
			$stock_new_quantity = $available_stock + $quantity;
			$stockQueryResult = $wpdb->update(
				$table_stock,
				['stock_quantity' => $stock_new_quantity],
				['product_id' => $product_id],
				['%d'],
				['%d']
			);
			
			if($stockQueryResult === false || $stockQueryResult === 0){ // record might not exist
				$product_info = get_product($product_id);
				$stock_location = $product_info ? $product_info->product_location : '';
				$low_stock_alert = $product_info ? intval($product_info->product_min_quantity) : 0;
				
				$stockQueryResult = $wpdb->insert(
					$table_stock,
					[
						'product_id' => $product_id,
						'stock_quantity' => $stock_new_quantity,
						'stock_location' => $stock_location,
						'restock_date' => $date,
						'low_stock_alert' => $low_stock_alert,
					],
					['%d', '%d', '%s', '%s', '%d']
				);
			}

			$stockUpdated[] = ($stockQueryResult !== false);
		}

		if(in_array(false, $stockUpdated, true)){
			$errors[] = 'Failed to update stock.';
		}else{
			$response[] = 'Stock updated successfully.';
		}

		// Serialize the purchased products to make it string as it'll be stored in a TEXT column in DB
		if (empty($purchase_invoice_data)) {
			$errors[] = 'No valid products found in purchase payload.';
		}
		$purchase_invoice_data_serialized = maybe_serialize($purchase_invoice_data);

		// Add entry in Purchase Invoces table referencing with purchase_invoice
		$pinvoice_rows_inserted = $wpdb->insert(
			$table_purchase_invoices,
			[
				'purchase_invoice' => $purchase_invoice,
				'invoice_data' => $purchase_invoice_data_serialized,
				'date' => $date,
			],
			['%d', '%s', '%s']
		);
		
		if($pinvoice_rows_inserted){
			$response[] = 'Purchase invoice saved successfully.';
		}else{
			$errors[] = 'Failed to save purchase invoice.';
		}

		if(empty($errors)){
			wp_send_json_success($response);
		}else{
			wp_send_json_error($errors);
			return;
		}
		

	}

	add_action('wp_ajax_handle_purchase', 'handle_purchase');

	add_action('wp_ajax_nopriv_handle_purchase', 'handle_purchase');





	function get_product($product_id){

		global $wpdb;

		$table = $wpdb->prefix . 'sms_products';
		$sanitized_product_id = intval(sanitize_text_field( $product_id ));
		if($sanitized_product_id){
			$sql = $wpdb->prepare("SELECT * FROM $table WHERE product_id = %d", $sanitized_product_id);
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
		$product_id = intval($product_id);
		$sql = $wpdb->prepare("SELECT * FROM $table WHERE product_id = %d", $product_id);

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
		$customer_id = intval($customer_id);
		$sql = $wpdb->prepare("SELECT * FROM $table WHERE customer_id = %d", $customer_id);

		$customers = $wpdb->get_results($sql);

		if(count($customers) > 0){

		    return $customers[0];

		}else{

			return false;

		}

	}

	function fbm_normalize_phone($phone){
		$phone = sanitize_text_field((string) $phone);
		return preg_replace('/\D+/', '', $phone);
	}

	function fbm_find_customer_by_phone($phone){
		global $wpdb;
		$table_customers = $wpdb->prefix . 'sms_customers';
		$normalized_phone = fbm_normalize_phone($phone);

		if(empty($normalized_phone)){
			return null;
		}

		// Fast path for already-normalized values.
		$exact_match = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table_customers WHERE phone = %s ORDER BY customer_id DESC LIMIT 1",
			$normalized_phone
		));
		if($exact_match){
			return $exact_match;
		}

		// Backward compatibility for older formatted phone values in DB.
		$all_customers = $wpdb->get_results("SELECT * FROM $table_customers ORDER BY customer_id DESC");
		foreach($all_customers as $customer){
			if(fbm_normalize_phone($customer->phone) === $normalized_phone){
				return $customer;
			}
		}

		return null;
	}

	function get_saleman($saleman_id){

		global $wpdb;

		$table = $wpdb->prefix . 'sms_salemans';
		$saleman_id = intval($saleman_id);
		$sql = $wpdb->prepare("SELECT * FROM $table WHERE id = %d", $saleman_id);

		$saleman = $wpdb->get_row($sql);

		return $saleman;

	}

	
	function save_sale(){
		fbm_require_authenticated_ajax();
		global $wpdb;
		$date = date('Y-m-d');
		$save_errors = [];

		if (empty($_POST['payload']) || !is_array($_POST['payload'])) {
			wp_send_json_error('Invalid sale payload.');
		}
		$payload = $_POST['payload'];

		// Get invoice data
		$invoice_data = isset($payload['invoice_data']) && is_array($payload['invoice_data']) ? $payload['invoice_data'] : [];
		$invoice_no = isset($invoice_data['invoice_no']) ? intval($invoice_data['invoice_no']) : 0;
		$invoice_data_arr = isset($invoice_data['data']) ? json_decode(stripcslashes($invoice_data['data']), true) : [];
		if ($invoice_no <= 0 || !is_array($invoice_data_arr) || empty($invoice_data_arr)) {
			wp_send_json_error('Invalid invoice payload.');
		}
		$invoice_data = maybe_serialize($invoice_data_arr);

		// Get Customer data
		$customer_id = 1; // Defualt customer  --WALKING-CUSTOMER-- database id
		$customer_data = isset($payload['customer_data']) && is_array($payload['customer_data']) ? $payload['customer_data'] : [];
		$cname = isset($customer_data['cname']) ? sanitize_text_field($customer_data['cname']) : '';
		$cphone = isset($customer_data['cphone']) ? sanitize_text_field($customer_data['cphone']) : '';
		$cemail = isset($customer_data['cemail']) ? sanitize_text_field($customer_data['cemail']) : '';
		$caddress = isset($customer_data['caddress']) ? sanitize_text_field($customer_data['caddress']) : '';
		$cphone = fbm_normalize_phone($cphone);

		// Save customer data
		$table_customers = $wpdb->prefix . 'sms_customers';
		$table_products = $wpdb->prefix . 'sms_products';
		if($cname && $cphone):
			$existing_customer = fbm_find_customer_by_phone($cphone);
			if($existing_customer){
				$customer_id = intval($existing_customer->customer_id);
			}else{
				$wpdb->insert(
					$table_customers,
					[
						'name' => $cname,
						'phone' => $cphone,
						'email' => $cemail,
						'address' => $caddress,
						'date' => $date,
					],
					['%s', '%s', '%s', '%s', '%s']
				);

				// Get the customer id, just added
				$customer_id = $wpdb->insert_id;
			}
		endif;

		// Update Stock
		foreach ($invoice_data_arr as $index => $product ) {
			$product_id = isset($product['prod_id']) ? intval($product['prod_id']) : 0;
			$product_quantity = isset($product['prod_quantity']) ? intval($product['prod_quantity']) : 0;
			if ($product_id <= 0 || $product_quantity <= 0) {
				$save_errors[] = 'Invalid product item in invoice.';
				continue;
			}
			// Store invoice data
			$table_stock = $wpdb->prefix . 'sms_stock';
			$stock_old_quantity = intval($wpdb->get_var($wpdb->prepare("SELECT stock_quantity FROM $table_stock WHERE product_id = %d", $product_id)));
			if($stock_old_quantity < $product_quantity){
				$save_errors[] = "Insufficient stock for product ID {$product_id}.";
				continue;
			}
			$new_stock_quantity = $stock_old_quantity - $product_quantity;
			$stock_updated = $wpdb->update(
				$table_stock,
				[
					'stock_quantity' => $new_stock_quantity,
					'restock_date' => $date,
				],
				['product_id' => $product_id],
				['%d', '%s'],
				['%d']
			);
			if($stock_updated === false){
				$save_errors[] = "Failed to update stock for product ID {$product_id}.";
			}

			// Update sold quantity in products table
			$sql_products = $wpdb->prepare("UPDATE $table_products SET sold = sold + %d WHERE product_id = %d", $product_quantity, $product_id);
			$product_updated = $wpdb->query($sql_products);
			if($product_updated === false){
				$save_errors[] = "Failed to update sold quantity for product ID {$product_id}.";
			}


		}
		if(!empty($save_errors)){
			wp_send_json_error(implode(' | ', $save_errors));
		}

		// Store invoice data
		$table_invoices = $wpdb->prefix . 'sms_invoices';
		$invoice_saved = $wpdb->insert(
			$table_invoices,
			[
				'invoice_no' => $invoice_no,
				'invoice_data' => $invoice_data,
				'date' => $date,
			],
			['%d', '%s', '%s']
		);
		if($invoice_saved === false){
			wp_send_json_error('Failed to store invoice data.');
		}

		// Get the invoice id, just added
		$invoice_id = $wpdb->insert_id;
		
		
		// Get sale data
		$sale_data = isset($payload['sale_data']) && is_array($payload['sale_data']) ? $payload['sale_data'] : [];
		$quantity = isset($sale_data['quantity']) ? intval($sale_data['quantity']) : 0;
		$gross_total = isset($sale_data['gross_total']) ? (float) $sale_data['gross_total'] : 0;
		$discount = isset($sale_data['discount']) ? (float) $sale_data['discount'] : 0;
		$net_total = isset($sale_data['net_total']) ? (float) $sale_data['net_total'] : 0;
		$sale_type = isset($sale_data['sale_type']) ? sanitize_text_field((string) $sale_data['sale_type']) : 'Cash Sale';
		$payment_method = isset($sale_data['payment_method']) ? sanitize_text_field((string) $sale_data['payment_method']) : '';

		$profit = isset($sale_data['profit']) ? (float) $sale_data['profit'] : 0;
		$sales_person = isset($sale_data['sales_person']) ? intval($sale_data['sales_person']) : 0;

		$paid_amount  = isset($sale_data['paid_amount']) ? (float) $sale_data['paid_amount'] : 0;
		$due_amount  = isset($sale_data['due_amount']) ? (float) $sale_data['due_amount'] : 0;
		if($paid_amount > $net_total){
			wp_send_json_error('Paid amount cannot exceed net total.');
		}
		if($due_amount > 0 && $paid_amount <= 0){
			$payment_status = 'Unpaid';
		}elseif($due_amount > 0){
			$payment_status = 'Partially Paid';
		}else{
			$payment_status = 'Paid';
		}
		$due_type = 'sale';

		$table_sales = $wpdb->prefix . 'sms_sales';
		$insertedSalesRows = $wpdb->insert(
			$table_sales,
			[
				'invoice_id' => $invoice_id,
				'customer_id' => $customer_id,
				'quantity' => $quantity,
				'gross_total' => $gross_total,
				'discount' => $discount,
				'net_total' => $net_total,
				'profit' => $profit,
				'sales_man' => $sales_person,
				'sale_type' => $sale_type,
				'payment_method' => $payment_method,
				'payment_status' => $payment_status,
				'date' => $date,
			],
			['%d', '%d', '%d', '%f', '%f', '%f', '%f', '%d', '%s', '%s', '%s', '%s']
		);
		$sale_id = $wpdb->insert_id;

		if($insertedSalesRows){

			if ($due_amount > 0) {
				// Create due record
				$due_added = fbm_dues_create_from_sale($sale_id, $customer_id, $net_total, $paid_amount, $due_type);
				if(!$due_added){
					wp_send_json_error('Sale saved but due record creation failed.');
				}
			}
			wp_send_json_success(['sale_id' => $sale_id]);
		}else{
			wp_send_json_error('Failed to add sale entry.');
		}
	}
	add_action('wp_ajax_save_sale', 'save_sale');
	add_action('wp_ajax_nopriv_save_sale', 'save_sale');


	// Ajax Request to get product data
	function get_product_details(){
		fbm_require_authenticated_ajax();
		$product_id = intval(sanitize_text_field($_POST['product_id']));
		$product = get_product($product_id);
		if(!$product){
			wp_send_json_error('Product not found.');
		}
		$manufacturer_id = $product->product_manufacturer;
		$manufacturer_name = get_manufacturer_name($manufacturer_id);
		if(!$manufacturer_name){
			$manufacturer_name = 'N/A';
		}
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
		fbm_require_authenticated_ajax();
		$purchase_id = intval(sanitize_text_field($_POST['purchase_id']));
		if($purchase_id <= 0){
			wp_send_json_error('Invalid purchase ID.');
		}
		global $wpdb;
		$table = $wpdb->prefix . 'sms_purchases';
		$purchase = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE purchase_id = %d", $purchase_id));
		if(!$purchase){
			wp_send_json_error('Purchase not found.');
		}
		wp_send_json_success(json_encode($purchase));
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
		fbm_require_authenticated_ajax();
		$id = intval(sanitize_text_field(stripcslashes($_POST['id'])));
		$table_name = sanitize_text_field( $_POST['table_name'] );
		$id_col_name = sanitize_text_field( $_POST['id_col_name'] );
		if(!preg_match('/^[A-Za-z0-9_]+$/', $table_name) || !preg_match('/^[A-Za-z0-9_]+$/', $id_col_name)){
			wp_send_json_error('Invalid delete target.');
		}
		global $wpdb;
		$table = $wpdb->prefix . $table_name;
		$sql = $wpdb->prepare("DELETE FROM $table WHERE $id_col_name = %d", $id);
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
	    fbm_require_authenticated_ajax();
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

		$sql = $wpdb->prepare("SELECT * FROM $table WHERE invoice_id = %d", intval($invoice_id));

		$sales = $wpdb->get_results($sql);

		if(count($sales) > 0){

		    return $sales[0];

		}else{

			return false;

		}

	}

	function get_invoiced_products() {
	    fbm_require_authenticated_ajax();
	    
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

		$sql = $wpdb->prepare("SELECT invoice_data FROM $table WHERE invoice_no = %d", intval($invoice_no));

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
        echo '<p>All products are well-stocked.</p>';
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
	fbm_require_authenticated_ajax();
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
	$manufacturer_name = $wpdb->get_var($wpdb->prepare("SELECT manufacturer_name FROM {$table_manufacturers} WHERE manufacturer_id = %d", intval($manufacturer_id)));
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
            <p>Old sales and returns older than 2 years were cleaned up today.</p>
        </div>
        <?php
    });

    // Set transient for 24 hours
    set_transient('entries_cleanup_ran_today', true, DAY_IN_SECONDS);
}



// Ajax for payment history for credit customer on pending payments page
function fbm_get_due_payments() {
    fbm_require_authenticated_ajax();
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
    $due_type = strtolower((string) $due->due_type);
    foreach ($payments as $p) {
        $total_paid += (float)$p->payment_amount;
    }

    $total_label = $due_type === 'purchase' ? 'Total Purchase:' : 'Total Sale:';
    $paid_label = $due_type === 'purchase' ? 'Total Paid:' : 'Total Received:';
    $remaining_label = $due_type === 'purchase' ? 'Remaining To Pay:' : 'Remaining:';

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
                <th colspan="3" style="text-align:right"><strong><?php echo esc_html($total_label); ?></strong></th>
                <th><?php echo number_format((float)$due->total_amount, 2); ?></th>
            </tr>
            <tr>
                <th colspan="3" style="text-align:right"><strong><?php echo esc_html($paid_label); ?></strong></th>
                <th><?php echo number_format($total_paid, 2); ?></th>
            </tr>
            <tr>
                <th colspan="3" style="text-align:right"><strong><?php echo esc_html($remaining_label); ?></strong></th>
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
    $sql = $wpdb->prepare("SELECT count(*) FROM $table_purchase_invoices WHERE date = %s", $today_date);
    $todaysNextInvoiceCount = $wpdb->get_var($sql) + 1;
    $invoiceDate = date('Ymd');
    $invoiceNo = $invoiceDate . str_pad($todaysNextInvoiceCount,2,0, STR_PAD_LEFT);
    return $invoiceNo;
}

// Function for getting Purchase(single) by using invoice_id
function get_purchase_by_invoice_no($invoice_no){

	global $wpdb;

	$table = $wpdb->prefix . 'sms_purchases';

	$sql = $wpdb->prepare("SELECT * FROM $table WHERE purchase_invoice = %s", sanitize_text_field((string) $invoice_no));

	$purchase = $wpdb->get_row($sql);
	return $purchase;

}

function get_saleman_by_invoice_no($invoice_no){
	$purchase = get_purchase_by_invoice_no($invoice_no);
	if(!$purchase){
		return false;
	}
	return get_saleman($purchase->saleman_id);
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

// Function to include the php template file and pass data to it
function include_template($template_name, $data = []) {
	$path = "templates/{$template_name}.php";
	$template_path = FBM_PLUGIN_DIR . $path;
	if (file_exists($template_path)) {
		require $template_path;
	} else {
		echo "<p>Template not found: <strong>{$path}</strong></p>";
	}
}

function include_component($component_name, $data = []) {
	$path = "components/{$component_name}/{$component_name}.php";
	$template_path = FBM_PLUGIN_DIR . $path;
	if (file_exists($template_path)) {
		require $template_path;
	} else {
		echo "<p>Template not found: <strong>{$path}</strong></p>";
	}
}

add_action('show_user_profile', 'fbm_render_auth_pincode_field');
add_action('edit_user_profile', 'fbm_render_auth_pincode_field');
function fbm_render_auth_pincode_field($user){
	if (!current_user_can('edit_user', $user->ID)) {
		return;
	}

	$pincode_hash = get_user_meta($user->ID, 'fbm_auth_pincode_hash', true);
	$pincode_status = !empty($pincode_hash) ? 'Set' : 'Not set';
	$pincode_updated_at = get_user_meta($user->ID, 'fbm_auth_pincode_updated_at', true);
	$pincode_updated_label = '';
	if (!empty($pincode_updated_at)) {
		$timestamp = strtotime($pincode_updated_at);
		if ($timestamp) {
			$pincode_updated_label = wp_date('M j, Y g:i A', $timestamp);
		}
	}
	?>
	<h2>Sales Management Authentication</h2>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="fbm_auth_pincode">Sales Pincode</label></th>
			<td>
				<input
					type="password"
					name="fbm_auth_pincode"
					id="fbm_auth_pincode"
					class="regular-text"
					autocomplete="new-password"
					inputmode="numeric"
						pattern="[0-9]*"
					/>
					<p class="description">Enter 4 to 10 digits. This field stays blank after save for security.</p>
					<p class="description">Leave blank to keep existing pincode. If forgotten, set a new one and save.</p>
					<p class="description"><strong>Status:</strong> <?php echo esc_html($pincode_status); ?></p>
					<?php if (!empty($pincode_updated_label)): ?>
						<p class="description"><strong>Last updated:</strong> <?php echo esc_html($pincode_updated_label); ?></p>
					<?php endif; ?>
					<label for="fbm_clear_auth_pincode">
						<input type="checkbox" name="fbm_clear_auth_pincode" id="fbm_clear_auth_pincode" value="1" />
						Clear current pincode (disables sales authentication for this user until a new pincode is set)
					</label>
				</td>
			</tr>
		</table>
	<?php
}

add_action('user_profile_update_errors', 'fbm_validate_auth_pincode_field', 10, 3);
function fbm_validate_auth_pincode_field($errors, $update, $user){
	if (!isset($_POST['fbm_auth_pincode']) && !isset($_POST['fbm_clear_auth_pincode'])) {
		return;
	}

	if (!current_user_can('edit_user', $user->ID)) {
		return;
	}

	$should_clear = !empty($_POST['fbm_clear_auth_pincode']);
	$pincode_raw = isset($_POST['fbm_auth_pincode']) ? trim((string) wp_unslash($_POST['fbm_auth_pincode'])) : '';

	if ($should_clear || $pincode_raw === '') {
		return;
	}

	if (!preg_match('/^\d{4,10}$/', $pincode_raw)) {
		$errors->add('fbm_auth_pincode', __('Sales pincode must be 4 to 10 digits.', 'sales-management'));
	}
}

add_action('personal_options_update', 'fbm_save_auth_pincode_field');
add_action('edit_user_profile_update', 'fbm_save_auth_pincode_field');
function fbm_save_auth_pincode_field($user_id){
	if (!current_user_can('edit_user', $user_id)) {
		return false;
	}

	$should_clear = !empty($_POST['fbm_clear_auth_pincode']);
	if ($should_clear) {
		delete_user_meta($user_id, 'fbm_auth_pincode_hash');
		delete_user_meta($user_id, 'fbm_auth_pincode_updated_at');
		return true;
	}

	if (!isset($_POST['fbm_auth_pincode'])) {
		return true;
	}

	$pincode_raw = trim((string) wp_unslash($_POST['fbm_auth_pincode']));
	if ($pincode_raw === '') {
		return true;
	}

	if (!preg_match('/^\d{4,10}$/', $pincode_raw)) {
		return false;
	}

	update_user_meta($user_id, 'fbm_auth_pincode_hash', wp_hash_password($pincode_raw));
	update_user_meta($user_id, 'fbm_auth_pincode_updated_at', current_time('mysql'));
	return true;
}

function fbm_verify_user(){

    if(!wp_verify_nonce($_POST['nonce'], FBM_PLUGIN_NONCE)){
        wp_send_json_error('Nonce failed');
    }

    if(!is_user_logged_in()){
        wp_send_json_error('User not logged in');
    }

    if(!current_user_can('manage_options')){
        wp_send_json_error('Permission denied');
    }

    $pincode = '';
    if (isset($_POST['pincode'])) {
        $pincode = trim((string) wp_unslash($_POST['pincode']));
    } elseif (isset($_POST['password'])) {
        // Backward compatibility for older frontend payload
        $pincode = trim((string) wp_unslash($_POST['password']));
    }

    if($pincode === ''){
        wp_send_json_error('Pincode is required');
    }

    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : get_current_user_id();
    $user = get_user_by('id', $user_id);
    if(!$user){
        wp_send_json_error('Invalid user');
    }

    $pincode_hash = get_user_meta($user->ID, 'fbm_auth_pincode_hash', true);
    if(empty($pincode_hash)){
        wp_send_json_error('Selected salesman pincode is not set. Please set it from user profile.');
    }

    // Verify pincode against dedicated pincode hash
    if(wp_check_password($pincode, $pincode_hash, $user->ID)){
        wp_send_json_success('Pincode matched.');
    }

    wp_send_json_error('Incorrect pincode.');
}

function get_purchase_invoice($invoice_no){
	global $wpdb;
	$table_invoices = $wpdb->prefix . 'sms_purchase_invoices';
	$invoice_query = $wpdb->prepare("SELECT * FROM $table_invoices WHERE purchase_invoice = %s", sanitize_text_field((string) $invoice_no));
	$invoice = $wpdb->get_row($invoice_query);
	return $invoice;
}

