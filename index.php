<?php
/**
 * Plugin Name: Sales Managment
 * Plugin URI: https://github.com/farooqbinmunir?tab=repositories
 * Author: Farooq Bin Munir
 * Author URI: https://www.linkedin.com/in/farooqbinmunir/
 * Version: 1.0
 * License: GNU General Public License v2
 * Description: A simple system to digitize your business. Manage products, purchases, sales, returns, and stock. Auto-deletes old records (2+ yrs). Dashboard includes top-selling & profitable products, low stock alerts, recent & monthly sales, and product performance.
 * Text Domain: sales-management
*/

/*  
	# Backend
		* Backend CSS
			write in 'assets/css/backend/backend.css'
			write in 'assets/css/backend/fbm.css' // Dev - Farooq Bin Munir only will work in this file
		* Backend JavaScript/jQuery
			write in 'assets/js/backend/backend.js'
	
	# Frontend
		* Frontend CSS
			write in 'assets/css/frontend/frontend.css'
		* Frontend JavaScript/jQuery
			write in 'assets/js/frontend/frontend.js'
*/

// Exit the users if they try to access the plugin directly
if(!defined('ABSPATH')){ 
	exit;
}

// Defining CONSTANT to store important information to be available throught the plugin
global $wpdb;
define('FBM_PLUGIN_VERSION', '1.1.0');
define('FBM_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('FBM_PLUGIN_DIR_NAME', plugin_basename(dirname(__FILE__)));
define('FBM_PLUGIN_TITLE', 'DDA Inventory');
define('FBM_PLUGIN_MENU_NAME', 'Inventory system');
define('FBM_PLUGIN_TABLE', $wpdb->prefix . 'sales_management'); // Change table name as per your requirement and customize the table below
define('FBM_PLUGIN_NONCE', 'fbm_ajax_nonce');
define('FBM_PLUGIN_TEMPLATES_DIR', FBM_PLUGIN_DIR . '/templates/');
define('FBM_PLUGIN_PATH', FBM_PLUGIN_DIR . '/');
define('FBM_PLUGIN_URL', plugins_url(FBM_PLUGIN_DIR_NAME));

// Including functions.php file to use it's functions here
require_once(FBM_PLUGIN_DIR . 'inc/functions.php');
require_once(FBM_PLUGIN_DIR . 'inc/shortcodes.php');
require_once(FBM_PLUGIN_DIR . 'inc/actions.php');
require_once(FBM_PLUGIN_DIR . 'inc/dues.php');

// Plugin activation hook
register_activation_hook(__FILE__, 'fbm_activate');

// Create tables in the database when the plugin is activated
function fbm_activate() {
    global $wpdb;

    // Set charset and collation
    $charset_collate = $wpdb->get_charset_collate();

    // Table products creation query
    $table_products = $wpdb->prefix . 'sms_products';
    $sql_products = "CREATE TABLE $table_products (
        product_id INT NOT NULL AUTO_INCREMENT,
        product_name VARCHAR(50) NOT NULL,
        product_purchase_price INT NOT NULL,
        product_sale_price INT NOT NULL,
        product_vendor VARCHAR(50) NOT NULL,
        product_manufacturer INT NOT NULL,
        sold INT NOT NULL DEFAULT 0,
        product_location VARCHAR(50) NOT NULL,
        product_meta TEXT,
        date varchar(20) NOT NULL,
		product_min_quantity INT NOT NULL,
        PRIMARY KEY (product_id)
    ) $charset_collate;";
    
    // Table purchases creation query
    $table_purchases = $wpdb->prefix . 'sms_purchases';
    $sql_purchases = "CREATE TABLE $table_purchases (
        purchase_id INT NOT NULL AUTO_INCREMENT,
        saleman_id int(11) NOT NULL DEFAULT 1,
        total_payment INT NOT NULL,
        paid INT NOT NULL DEFAULT 0,
        due INT NOT NULL DEFAULT 0,
        payment_status varchar(20) NOT NULL,
        payment_method varchar(50) NOT NULL,
        description TEXT,
        purchase_invoice VARCHAR(50) NULL,
        date varchar(20) NOT NULL,
        PRIMARY KEY (purchase_id)
    ) $charset_collate;";
    
    // Table sales creation query (if applicable)
    $table_sales = $wpdb->prefix . 'sms_sales';
    $sql_sales = "CREATE TABLE $table_sales (
        sale_id INT NOT NULL AUTO_INCREMENT,
        invoice_id INT NOT NULL,
        customer_id INT NOT NULL,
        quantity INT NOT NULL,
        gross_total INT NOT NULL,
        discount INT NOT NULL,
        net_total INT NOT NULL,
        profit INT DEFAULT 0,
        sale_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
        sales_man INT NOT NULL,
        sale_type TEXT,
        payment_method TEXT,
        payment_status TEXT,
        sale_meta TEXT,
        date varchar(20) NOT NULL,
        PRIMARY KEY (sale_id)
    ) $charset_collate;";

    // Customers
    $table_customers = $wpdb->prefix . 'sms_customers';
    $sql_customers = "CREATE TABLE $table_customers (
        customer_id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        email VARCHAR(50) NOT NULL,
        address VARCHAR(255),
        date varchar(20) NOT NULL,
        PRIMARY KEY (customer_id)
    ) $charset_collate;";

    // Sale Man(s)
    $table_salemans = $wpdb->prefix . 'sms_salemans';
    $sql_salemans = "CREATE TABLE $table_salemans (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        email VARCHAR(50) NOT NULL,
        address VARCHAR(255),
        date varchar(20) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Create the stock table
    $table_stock = $wpdb->prefix . 'sms_stock';
    $sql_stock = "CREATE TABLE $table_stock (
        stock_id INT NOT NULL AUTO_INCREMENT,
        product_id INT NOT NULL,
        stock_quantity INT NOT NULL DEFAULT 0,
        stock_location VARCHAR(255) DEFAULT '',
        restock_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        low_stock_alert TINYINT(1) DEFAULT 0,
        PRIMARY KEY (stock_id)
    ) $charset_collate;";

    // Create table for returns
    $table_returns = $wpdb->prefix . 'sms_sales_returns';
    $sql_returns = "CREATE TABLE $table_returns (
        return_id BIGINT(20) NOT NULL AUTO_INCREMENT,
        product_id BIGINT(20) NOT NULL,
        quantity INT NOT NULL,
        amount INT NOT NULL,
        return_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        return_reason TEXT NOT NULL,
        invoice_no TEXT NOT NULL,
        PRIMARY KEY (return_id)
    ) $charset_collate;";

    // Table products creation query
    $table_manufacturers = $wpdb->prefix . 'sms_manufacturers';
    $sql_manufacturers = "CREATE TABLE $table_manufacturers (
        manufacturer_id INT NOT NULL AUTO_INCREMENT,
        manufacturer_name VARCHAR(50) NOT NULL,
        PRIMARY KEY (manufacturer_id)
    ) $charset_collate;";

    // New Tables for the feature - Pending Payments
    $table_dues = $wpdb->prefix . 'sms_dues';
    $sql_dues = "CREATE TABLE $table_dues (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        referer_id BIGINT UNSIGNED NULL,
        customer_saler_id VARCHAR(50) NULL,
        total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
        paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
        remaining_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
        status VARCHAR(20) NOT NULL DEFAULT 'open',
        due_type VARCHAR(50) NOT NULL DEFAULT '',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    $table_payments = $wpdb->prefix . 'sms_dues_payments';
    $sql_payments = "CREATE TABLE $table_payments (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        due_id BIGINT UNSIGNED NOT NULL,
        payment_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
        payment_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        note TEXT NULL,
        PRIMARY KEY (id),
        KEY due_id (due_id)
    ) $charset_collate;";

    // Create table for invoices
    $table_invoices = $wpdb->prefix . 'sms_invoices';
    $sql_invoices = "CREATE TABLE $table_invoices (
        invoice_id INT NOT NULL AUTO_INCREMENT,
        invoice_no INT NOT NULL,
        invoice_data TEXT NOT NULL,
        date varchar(20) NOT NULL,
        PRIMARY KEY (invoice_id)
    ) $charset_collate;";

    // Create table for purchase invoices
    $table_purchase_invoices = $wpdb->prefix . 'sms_purchase_invoices';
    $sql_purchase_invoices = "CREATE TABLE $table_purchase_invoices (
        purchase_invoice_id INT NOT NULL AUTO_INCREMENT,
        purchase_invoice INT NOT NULL,
        invoice_data TEXT NOT NULL,
        date varchar(20) NOT NULL,
        PRIMARY KEY (purchase_invoice_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Running dbDelta for each table
    dbDelta($sql_products);
    dbDelta($sql_purchases);
    dbDelta($sql_sales);
    dbDelta($sql_customers);
    dbDelta($sql_stock);
    dbDelta($sql_returns);
    dbDelta($sql_manufacturers);
    dbDelta($sql_invoices);

    dbDelta($sql_dues);
    dbDelta($sql_payments);

    dbDelta($sql_purchase_invoices);
    dbDelta($sql_salemans);

    // Flush rewrite rules after activation
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'fbm_deactivate');

// Drop tables when the plugin is deactivated
function fbm_deactivate() {
    flush_rewrite_rules();
}
