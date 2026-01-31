<?php

	// Custom Function for registering Custom Post Types
	function fbm_plugin_register_cpt($postTypeTitle, $singularName = 'Item', $menuPosition = 5, $hasTaxonomy = true, $taxonomyName = 'Categories'){

	    $cpt_name = ucwords(str_replace(['-', '_'], ' ', $postTypeTitle), ' ');
	    $cpt_slug = sanitize_title_with_dashes($cpt_name);
	    $taxonomy = $hasTaxonomy == true ? "{$cpt_slug}-cat" : '';
	    if(!post_type_exists($cpt_slug)){
	        $labels = [
	            'name' => $cpt_name,
	            'add_new' => "Add New " . ucwords(str_replace(['-', '_'], ' ', $singularName), ' '),
	        ];
	        $args = [
	            'labels' => $labels,
	            'public' => true,
	            'has_archive' => true,
	            'show_in_rest' => true,
	            'menu_position' => $menuPosition,
	            'taxonomies' => [$taxonomy],
	            'supports' => [
	                'title',
	                'editor',
	                'thumbnail',
	                'excerpt',
	                'page-attributes',
	                'trackbacks',
	                'revisions',
	                'custom-fields',
	                'author',
	                'comments',
	                'post-formats',
	            ]
	        ];
	        register_post_type($cpt_slug, $args);
	        if($hasTaxonomy == true){
	            $tax_args = array(
	                'labels'            => ['name' => ucwords(str_replace(['-', '_'], ' ', $taxonomyName), ' ')],
	                'hierarchical'      => true,
	                'public'            => true,
	                'show_ui'           => true,
	                'show_admin_column' => true,
	                'query_var'         => true,
	                'show_in_rest'      => true,
	            );
	            register_taxonomy($taxonomy , $cpt_slug, $tax_args);
	        }
	        flush_rewrite_rules();
	    }
	}

	function sm_update_product_stock($product_id, $quantity, $action = 'add') {
	    global $wpdb;
		$table_stock = $wpdb->prefix . 'sms_stock';
	    // Get current stock

	    $current_stock = $wpdb->get_var($wpdb->prepare(
	        "SELECT stock_quantity FROM $table_stock WHERE product_id = %d",
	        $product_id
	    ));

	    if ($current_stock !== null) {
	        // Adjust stock based on the action
	        $new_stock = ($action === 'add') ? $current_stock + $quantity : $current_stock - $quantity;

	        // Update the stock in the products table
	        $wpdb->update(
	            $table_stock,
	            array('stock_quantity' => $new_stock),
	            array('product_id' => $product_id),
	            array('%d'),
	            array('%d')
	        );
	    }
	}

	