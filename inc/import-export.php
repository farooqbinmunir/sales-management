<?php

function fbm_get_exportable_entities(){
	return [
		'products' => [
			'label' => 'Products',
			'table' => 'sms_products',
			'columns' => [
				'product_id' => '%d',
				'product_name' => '%s',
				'product_purchase_price' => '%f',
				'product_sale_price' => '%f',
				'product_vendor' => '%s',
				'product_manufacturer' => '%d',
				'sold' => '%d',
				'product_location' => '%s',
				'product_meta' => '%s',
				'date' => '%s',
				'product_min_quantity' => '%d',
			],
			'ignore_insert_columns' => ['product_id'],
		],
		'customers' => [
			'label' => 'Customers',
			'table' => 'sms_customers',
			'columns' => [
				'customer_id' => '%d',
				'name' => '%s',
				'phone' => '%s',
				'email' => '%s',
				'address' => '%s',
				'date' => '%s',
			],
			'ignore_insert_columns' => ['customer_id'],
		],
		'purchases' => [
			'label' => 'Purchases',
			'table' => 'sms_purchases',
			'columns' => [
				'purchase_id' => '%d',
				'saleman_id' => '%d',
				'total_payment' => '%f',
				'paid' => '%f',
				'due' => '%f',
				'payment_status' => '%s',
				'payment_method' => '%s',
				'description' => '%s',
				'purchase_invoice' => '%s',
				'vendor' => '%s',
				'date' => '%s',
			],
			'ignore_insert_columns' => ['purchase_id'],
		],
		'sales' => [
			'label' => 'Sales',
			'table' => 'sms_sales',
			'columns' => [
				'sale_id' => '%d',
				'invoice_id' => '%d',
				'customer_id' => '%d',
				'quantity' => '%d',
				'gross_total' => '%f',
				'discount' => '%f',
				'net_total' => '%f',
				'profit' => '%f',
				'sale_time' => '%s',
				'sales_man' => '%d',
				'sale_type' => '%s',
				'payment_method' => '%s',
				'payment_status' => '%s',
				'sale_meta' => '%s',
				'date' => '%s',
			],
			'ignore_insert_columns' => ['sale_id'],
		],
		'returns' => [
			'label' => 'Returns',
			'table' => 'sms_sales_returns',
			'columns' => [
				'return_id' => '%d',
				'product_id' => '%d',
				'quantity' => '%d',
				'amount' => '%f',
				'return_date' => '%s',
				'return_reason' => '%s',
				'invoice_no' => '%s',
			],
			'ignore_insert_columns' => ['return_id'],
		],
		'stock' => [
			'label' => 'Stock',
			'table' => 'sms_stock',
			'columns' => [
				'stock_id' => '%d',
				'product_id' => '%d',
				'stock_quantity' => '%d',
				'stock_location' => '%s',
				'restock_date' => '%s',
				'low_stock_alert' => '%d',
			],
			'ignore_insert_columns' => ['stock_id'],
		],
		'manufacturers' => [
			'label' => 'Manufacturers',
			'table' => 'sms_manufacturers',
			'columns' => [
				'manufacturer_id' => '%d',
				'manufacturer_name' => '%s',
			],
			'ignore_insert_columns' => ['manufacturer_id'],
		],
	];
}

function fbm_handle_export(){
	if (!current_user_can('manage_options')) {
		wp_die('Permission denied');
	}
	if (!isset($_REQUEST['fbm_export_nonce']) || !wp_verify_nonce($_REQUEST['fbm_export_nonce'], 'fbm_export_data')) {
		wp_die('Invalid request.');
	}
	$entity = isset($_REQUEST['entity']) ? sanitize_text_field($_REQUEST['entity']) : '';
	$entities = fbm_get_exportable_entities();
	if (!isset($entities[$entity])) {
		wp_die('Invalid export entity.');
	}
	global $wpdb;
	$table = $wpdb->prefix . $entities[$entity]['table'];
	$columns = array_keys($entities[$entity]['columns']);
	$rows = $wpdb->get_results("SELECT " . implode(', ', $columns) . " FROM $table", ARRAY_A);
	$filename = sprintf('fbm-%s-export-%s.csv', $entity, date('Ymd'));
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename="' . $filename . '"');
	$fh = fopen('php://output', 'w');
	if ($fh === false) {
		wp_die('Unable to open output stream.');
	}
	fputcsv($fh, $columns);
	foreach ($rows as $row) {
		foreach ($row as &$value) {
			if (is_array($value) || is_object($value)) {
				$value = wp_json_encode($value);
			}
		}
		fputcsv($fh, $row);
	}
	fclose($fh);
	exit;
}
add_action('admin_post_fbm_export_data', 'fbm_handle_export');

function fbm_handle_import(){
	if (!current_user_can('manage_options')) {
		wp_die('Permission denied');
	}
	if (!isset($_POST['fbm_import_nonce']) || !wp_verify_nonce($_POST['fbm_import_nonce'], 'fbm_import_data')) {
		wp_die('Invalid request.');
	}
	$entity = isset($_POST['entity']) ? sanitize_text_field($_POST['entity']) : '';
	$entities = fbm_get_exportable_entities();
	if (!isset($entities[$entity])) {
		wp_die('Invalid import entity.');
	}
	if (empty($_FILES['import_file']['tmp_name']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
		$redirect = add_query_arg(['import_status' => 'error', 'message' => 'File upload failed.'], admin_url('admin.php?page=import-export'));
		wp_safe_redirect($redirect);
		exit;
	}
	$handle = fopen($_FILES['import_file']['tmp_name'], 'r');
	if ($handle === false) {
		$redirect = add_query_arg(['import_status' => 'error', 'message' => 'Unable to open uploaded file.'], admin_url('admin.php?page=import-export'));
		wp_safe_redirect($redirect);
		exit;
	}
	$header = fgetcsv($handle);
	if (empty($header) || !is_array($header)) {
		fclose($handle);
		$redirect = add_query_arg(['import_status' => 'error', 'message' => 'The import file is missing a header row.'], admin_url('admin.php?page=import-export'));
		wp_safe_redirect($redirect);
		exit;
	}
	$header = array_map('trim', $header);
	$allowed_columns = array_keys($entities[$entity]['columns']);
	$ignored_columns = $entities[$entity]['ignore_insert_columns'];
	$insert_columns = array_values(array_diff($header, $ignored_columns));
	$invalid_columns = array_diff($insert_columns, $allowed_columns);
	if (!empty($invalid_columns)) {
		fclose($handle);
		$redirect = add_query_arg(['import_status' => 'error', 'message' => 'Invalid columns: ' . implode(', ', $invalid_columns)], admin_url('admin.php?page=import-export'));
		wp_safe_redirect($redirect);
		exit;
	}
	$insertable = array_intersect($allowed_columns, $insert_columns);
	$insert_count = 0;
	$errors = [];
	global $wpdb;
	$table = $wpdb->prefix . $entities[$entity]['table'];
	$placeholders_map = $entities[$entity]['columns'];
	while (($row = fgetcsv($handle)) !== false) {
		if (count($row) !== count($header)) {
			continue;
		}
		$data = array_combine($header, $row);
		$insert_data = [];
		$placeholders = [];
		foreach ($insertable as $column) {
			$value = isset($data[$column]) ? trim($data[$column]) : '';
			$insert_data[$column] = $value === '' ? null : $value;
			$placeholders[] = $placeholders_map[$column];
		}
		if (empty($insert_data)) {
			continue;
		}
		$insert_result = $wpdb->insert($table, $insert_data, $placeholders);
		if ($insert_result === false) {
			$errors[] = 'Failed to import row: ' . implode(', ', $row);
		} else {
			$insert_count++;
		}
	}
	fclose($handle);
	$query = ['import_status' => 'success', 'imported' => $insert_count];
	if (!empty($errors)) {
		$query['message'] = rawurlencode(implode(' | ', array_slice($errors, 0, 3)));
	}
	$redirect = add_query_arg($query, admin_url('admin.php?page=import-export'));
	wp_safe_redirect($redirect);
	exit;
}
add_action('admin_post_fbm_import_data', 'fbm_handle_import');
