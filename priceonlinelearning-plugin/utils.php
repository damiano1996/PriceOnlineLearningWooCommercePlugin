<?php

include 'api.php';

function is_woocommerce_active(): bool {
	return is_plugin_active( 'woocommerce/woocommerce.php' );
}


/**
 * @throws Exception
 */
function add_all_products_to_pol() {
	if ( ! is_woocommerce_active() ) {
		throw new Exception( 'WooCommerce is not active' );
	} else {

		$options = get_option( 'plugin_options' );

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => - 1,
		);

		$loop = new WP_Query( $args );

		while ( $loop->have_posts() ) : $loop->the_post();
			global $product;

			$original_price = doubleval( $product->get_price() );

			echo add_product(
				$product->get_id(),
				$product->get_name(),
				$original_price,
				$original_price + $original_price * get_min_percentage(),
				$original_price + $original_price * get_max_percentage(),
				get_woocommerce_currency(),
				intval( $options['days-consistency-radio'] )
			);

			echo '<br>' . get_price( $product->get_id(), 0 );

			echo '<br>' . get_all_prices()[0][0]['price'];
			echo '<br>' . get_all_prices()[0][1]['price'];
			echo '<br>' . get_all_prices()[0][2]['price'];
			echo '<br>' . get_all_prices()[0][3]['price'];
			echo '<br>' . get_all_prices()[0][4]['price'];
			echo '<br>' . get_all_prices()[0][5]['price'];

			echo '<br>' . json_encode( update_price( $product->get_id(), 0, true ) );
			echo '<br>' . json_encode( update_price( $product->get_id(), 0, false ) );

		endwhile;
	}
}

function get_min_percentage() {
	$options              = get_option( 'plugin_options' );
	$min_price_percentage = $options['min-price-radio'];
//	echo '<br>' . $min_price_percentage;

	if ( strcmp( $min_price_percentage, 'original_price - 50%' ) == 0 ) {
		return - 0.5;
	} elseif ( strcmp( $min_price_percentage, 'original_price - 20%' ) == 0 ) {
		return - 0.2;
	} elseif ( strcmp( $min_price_percentage, 'original_price - 10%' ) == 0 ) {
		return - 0.1;
	} elseif ( strcmp( $min_price_percentage, 'original_price (recommended)' ) == 0 ) {
		return 0.0;
	}
}

function get_max_percentage() {
	$options              = get_option( 'plugin_options' );
	$max_price_percentage = $options['max-price-radio'];
//	echo '<br>' . $max_price_percentage;

	if ( strcmp( $max_price_percentage, 'original_price' ) == 0 ) {
		return 0.0;
	} elseif ( strcmp( $max_price_percentage, 'original_price + 10%' ) == 0 ) {
		return 0.1;
	} elseif ( strcmp( $max_price_percentage, 'original_price + 20% (recommended)' ) == 0 ) {
		return 0.2;
	} elseif ( strcmp( $max_price_percentage, 'original_price + 50%' ) == 0 ) {
		return 0.5;
	}

}