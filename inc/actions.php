<?php

	// Including functions.php file to use it's functions here
	require_once(FBM_PLUGIN_DIR . 'inc/functions.php');

	// Add Menu to admin menu bar
	add_action('admin_menu', 'fbm_admin_menu_callback');

	// Enqueue scripts & styles for backend
	add_action('admin_enqueue_scripts', 'fbm_backend_enqueues');

	// Do stuff on wordpress init, like creating a post type
	add_action('init', 'fbm_init_callback');

	// AJAX action for deleting old entries
	add_action('admin_init', 'delete_entries_older_than_two_years_once_per_day');

	// AJAX action for verifying user credentials in the authentication popup
	add_action('wp_ajax_fbm_verify_user', 'fbm_verify_user');
