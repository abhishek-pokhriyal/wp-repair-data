<?php
/**
 * Plugin Name:       WP Repair Data
 * Plugin URI:        https://github.com/coloredcow-admin/megafitmeals/
 * Description:       Identify, repair, migrate, basically play with data in your WordPress site.
 * Version:           1.0.0
 * Author:            ColoredCow
 * Author URI:        https://coloredcow.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-repair-data
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WPRD_PLUGIN_FILE' ) ) {
	define( 'WPRD_PLUGIN_FILE', __FILE__ );
}

// Include the main plugin class.
if ( ! class_exists( 'WP_Repair_Data', false ) ) {
	include_once dirname( WPRD_PLUGIN_FILE ) . '/includes/class-wp-repair-data.php';
}

/**
 * Returns the main instance of WPRD.
 *
 * @since  0.0.1
 * @return WP_Repair_Data
 */
function WPRD() {
	return WP_Repair_Data::instance();
}

$dependencies = array(
	'woocommerce/woocommerce.php',	// WooCommerce
	'woocommerce-subscriptions/woocommerce-subscriptions.php', // WooCommerce Subscriptions
);

if ( ! array_diff( $dependencies, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	WPRD();
}
