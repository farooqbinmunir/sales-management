<?php



	// Including functions.php file to use it's functions here

	require_once(FBM_PLUGIN_DIR . 'inc/functions.php');







	function fbm_display_products() {

		$args = array(

			'post_type' => 'product',

			'posts_per_page' => -1

		);

		$products = new WP_Query($args);

	

		$output = '<div class="products-list">';

		while ($products->have_posts()) : $products->the_post();

			$output .= '<h2>' . get_the_title() . '</h2>';

			$output .= '<div>' . get_the_excerpt() . '</div>';

		endwhile;

		$output .= '</div>';

		wp_reset_postdata();

	

		return $output;

	}

	add_shortcode('fbm_products', 'fbm_display_products');

	

	

