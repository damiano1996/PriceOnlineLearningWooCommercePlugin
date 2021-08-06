<?php

/*
Plugin Name: Price Online Learning
Plugin URI: http://www.priceonlinelearning.com/plugins/
Description: Price Online Learning plugin for WooCommerce
Version: 1.0
Author: Price Online Learning
Author URI: http://www.priceonlinelearning.com/
License: GPL2
*/

include 'utils.php';

function fx_admin_notice_example_activation_hook() {
	set_transient( 'fx-admin-notice-example', true, 5 );
}

register_activation_hook( __FILE__, 'fx_admin_notice_example_activation_hook' );

function fx_admin_notice_example_notice() {

	/* Check transient, if available display notice */
	if ( get_transient( 'fx-admin-notice-example' ) ) {
		if ( is_woocommerce_active() ) {
			?>
            <div class="updated notice is-dismissible">
                <p>WooCommerce is installed.</p>
            </div>
			<?php
		} else {
			?>
            <div class="updated notice is-dismissible">
                <p>WooCommerce is NOT installed.</p>
            </div>
			<?php
		}
		/* Delete transient, only display this notice once. */
		delete_transient( 'fx-admin-notice-example' );
	}
}

add_action( 'admin_notices', 'fx_admin_notice_example_notice' );


// ---------------------------------------------------------------------------------------------------------------------
// GENERAL SETTINGS

/**
 * custom option and settings
 */
function pol_general_settings() {

	register_setting( 'plugin_options', 'plugin_options' );

	add_settings_section( 'main-section', 'General Settings', 'section_text_fn', __FILE__ );

	// access token
	add_settings_field( 'access-token', 'Personal Access Token', 'access_token_fn', __FILE__, 'main-section' );

	// min price
	add_settings_field( 'min-price-radio', 'Minimum price', 'minimum_price_fn', __FILE__, 'main-section' );
	// max price
	add_settings_field( 'max-price-radio', 'Maximum price', 'maximum_price_fn', __FILE__, 'main-section' );

	// days consistency
	add_settings_field( 'days-consistency-radio', 'Days Consistency', 'days_consistency_fn', __FILE__, 'main-section' );

}

/**
 * Register our pol_general_settings to the admin_init action hook.
 */
add_action( 'admin_init', 'pol_general_settings' );

/**
 * Custom option and settings:
 *  - callback functions
 */


function access_token_fn() {
	$options = get_option( 'plugin_options' );
	echo "<p>Get it from your Price Online Learning <a href='https://priceonlinelearning.herokuapp.com/home_account/'>account page</a>.</p>";
	echo "<input id='access-token' name='plugin_options[access_token]' size='40' type='text' value='{$options['access_token']}' />";
}

function minimum_price_fn() {
	$options = get_option( 'plugin_options' );
	$items   = array(
		"original_price - 50%",
		"original_price - 20%",
		"original_price - 10%",
		"original_price (recommended)"
	);

	foreach ( $items as $item ) {
		$checked = ( $options['min-price-radio'] == $item ) ? ' checked="checked" ' : '';
		echo "<label><input " . $checked . " value='$item' name='plugin_options[min-price-radio]' type='radio' /> $item</label><br/>";
	}
}

function maximum_price_fn() {
	$options = get_option( 'plugin_options' );
	$items   = array(
		"original_price",
		"original_price + 10%",
		"original_price + 20% (recommended)",
		"original_price + 50%"
	);

	foreach ( $items as $item ) {
		$checked = ( $options['max-price-radio'] == $item ) ? ' checked="checked" ' : '';
		echo "<label><input " . $checked . " value='$item' name='plugin_options[max-price-radio]' type='radio' /> $item</label><br/>";
	}
}

function days_consistency_fn() {
	$options = get_option( 'plugin_options' );
	$items   = array( "1", "2", "3 (recommended)", "7" );

	foreach ( $items as $item ) {
		$checked = ( $options['days-consistency-radio'] == $item ) ? ' checked="checked" ' : '';
		echo "<label><input " . $checked . " value='$item' name='plugin_options[days-consistency-radio]' type='radio' /> $item</label><br/>";
	}
}


/**
 * Add the top level menu page.
 */
function pol_options_page() {
	add_menu_page(
		'Price Online Learning Settings',
		'POL',
		'manage_options',
		'POL',
		'pol_options_page_html'
	);
}


/**
 * Register our pol_options_page to the admin_menu action hook.
 */
add_action( 'admin_menu', 'pol_options_page' );

/**
 * Top level menu callback function
 */
function pol_options_page_html() {
	?>
    <div class="wrap">
        <h2>Price Online Learning Settings</h2>

        <div>
            <form action="options.php" method="post">
				<?php
				if ( function_exists( 'wp_nonce_field' ) ) {
					wp_nonce_field( 'plugin-name-action_' . "yep" );
				}
				?>
				<?php settings_fields( 'plugin_options' ); ?>
				<?php do_settings_sections( __FILE__ ); ?>
                <p class="submit">
                    <input name="Submit" type="submit" class="button-primary"
                           value="<?php esc_attr_e( 'Save Changes' ); ?>"/>
                </p>
            </form>
        </div>
    </div>
	<?php
}