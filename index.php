<?php
/**
 * Plugin Name: Sales Managment
 * Plugin URI: https://github.com/farooqbinmunir?tab=repositories
 * Author: Farooq Bin Munir
 * Author URI: https://www.linkedin.com/in/farooqbinmunir/
 * Version: 1.0
 * License: GNU General Public License v2
 * Description: A simple system to digitize your business. Manage products, purchases, sales, returns, and stock. Auto-deletes old records (2+ yrs). Dashboard includes top-selling & profitable products, low stock alerts, recent & monthly sales, and product performance.
 * Text Domain: fbm-textdomain
*/

/*  
	# Backend
		* Backend CSS
			write in 'assets/css/backend/backend.css'
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
// define('FBM_PLUGIN_NAME', 'fbm-starter'); // You plugin folder name
define('FBM_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('FBM_PLUGIN_DIR_NAME', plugin_basename(dirname(__FILE__)));
define('FBM_PLUGIN_TITLE', 'DDA Inventory');
define('FBM_PLUGIN_MENU_NAME', 'Inventory system');
define('FBM_PLUGIN_TABLE', $wpdb->prefix . 'sales_management'); // Change table name as per your requirement and customize the table below
define('FBM_PLUGIN_NONCE', 'fbm_ajax_nonce');

// Including functions.php file to use it's functions here
require_once(FBM_PLUGIN_DIR . 'inc/functions.php');
require_once(FBM_PLUGIN_DIR . 'inc/shortcodes.php');
require_once(FBM_PLUGIN_DIR . 'inc/actions.php');

// Plugin activation hook
register_activation_hook(__FILE__, 'fbm_activate');

// Create tables in the database when the plugin is activated
function fbm_activate() {
    global $wpdb;

    // Set charset and collation
    $charset_collate = $wpdb->get_charset_collate();
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Table products creation query
    $table_products = $wpdb->prefix . 'sms_products';
    $wpdb->query($drop_products_sql);
    $sql_products = "CREATE TABLE $table_products (
        product_id INT NOT NULL AUTO_INCREMENT,
        product_name VARCHAR(50) NOT NULL,
        product_purchase_price INT NOT NULL,
        product_sale_price INT NOT NULL,
        product_vendor VARCHAR(50) NOT NULL,
        product_location VARCHAR(50) NOT NULL,
        product_meta TEXT,
        date varchar(20) NOT NULL,
		product_min_quantity INT NOT NULL,
        PRIMARY KEY (product_id)
    ) $charset_collate;";
    
    // Table purchases creation query
    $table_purchases = $wpdb->prefix . 'sms_purchases';
    $wpdb->query($drop_purchases_sql);
    $sql_purchases = "CREATE TABLE $table_purchases (
        purchase_id INT NOT NULL AUTO_INCREMENT,
        product_id INT NOT NULL,
        vendor VARCHAR(50) NOT NULL,
        quantity INT NOT NULL,
        rate INT NOT NULL,
        total_payment INT NOT NULL,
        payment_method varchar(50) NOT NULL,
        payment_status varchar(20) NOT NULL,
        description TEXT,
        date varchar(20) NOT NULL,
        PRIMARY KEY (purchase_id)
    ) $charset_collate;";
    
    // Table sales creation query (if applicable)
    $table_sales = $wpdb->prefix . 'sms_sales';
    $wpdb->query($drop_sales_sql);
    $sql_sales = "CREATE TABLE $table_sales (
        sale_id INT NOT NULL AUTO_INCREMENT,
        invoice_id INT NOT NULL,
        customer_id INT NOT NULL,
        quantity INT NOT NULL,
        gross_total INT NOT NULL,
        discount INT NOT NULL,
        net_total INT NOT NULL,
        sale_type TEXT,
        payment_method TEXT,
        payment_status TEXT,
        sale_meta TEXT,
        date varchar(20) NOT NULL,
        PRIMARY KEY (sale_id)
    ) $charset_collate;";

    $table_customers = $wpdb->prefix . 'sms_customers';
    $wpdb->query($drop_customers_sql);
    $sql_customers = "CREATE TABLE $table_customers (
        customer_id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(50) NOT NULL,
        address VARCHAR(255),
        date varchar(20) NOT NULL,
        PRIMARY KEY (customer_id)
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

    // Create table for invoices
    $table_invoices = $wpdb->prefix . 'sms_invoices';
    $sql_invoices = "CREATE TABLE $table_invoices (
        invoice_id INT NOT NULL AUTO_INCREMENT,
        invoice_no INT NOT NULL,
        invoice_data TEXT NOT NULL,
        date varchar(20) NOT NULL,
        PRIMARY KEY (invoice_id)
    ) $charset_collate;";

    // Create table for returns
    $table_returns = $wpdb->prefix . 'sms_sales_returns';

    $sql_returns = "CREATE TABLE $table_returns (
        return_id BIGINT(20) NOT NULL AUTO_INCREMENT,
        product_id BIGINT(20) NOT NULL,
        quantity INT NOT NULL,
        return_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        return_reason TEXT NOT NULL,
        invoice_no TEXT NOT NULL,
        PRIMARY KEY (return_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Running dbDelta for each table
    dbDelta($sql_products);
    dbDelta($sql_purchases);
    dbDelta($sql_sales);
    dbDelta($sql_customers);
    dbDelta($sql_stock);
    dbDelta($sql_invoices);
    dbDelta($sql_returns);

    // Flush rewrite rules after activation
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'fbm_deactivate');

// Drop tables when the plugin is deactivated
function fbm_deactivate() {
    flush_rewrite_rules();
}

add_action('admin_init', 'delete_entries_older_than_two_years_once_per_day');
function delete_entries_older_than_two_years_once_per_day(){
    if (get_transient('entries_cleanup_ran_today')) {
        return;
    }

    global $wpdb;
    $table_sales = $wpdb->prefix . "sms_sales";
    $table_returns = $wpdb->prefix . "sms_sales_returns";
    $two_years_old_date = date('Y-m-d', strtotime('-2 years'));

    $sql_delete_sales = $wpdb->prepare("DELETE FROM $table_sales WHERE date < %s", $two_years_old_date);
    $wpdb->query($sql_delete_sales);

    $sql_delete_returns = $wpdb->prepare("DELETE FROM $table_returns WHERE date < %s", $two_years_old_date);
    $wpdb->query($sql_delete_returns);

    // Set transient for 24 hours
    set_transient('entries_cleanup_ran_today', true, DAY_IN_SECONDS);
}









