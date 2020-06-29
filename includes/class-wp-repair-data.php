<?php
/**
 * WP Repair Data setup
 *
 * @since   0.0.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main WP_Repair_Data Class.
 *
 * @class WP_Repair_Data
 */
final class WP_Repair_Data {

	/**
	 * WP_Repair_Data version.
	 *
	 * @var string
	 */
	public $version = '0.0.1';

	/**
	 * The single instance of the class.
	 *
	 * @var WP_Repair_Data
	 * @since 0.0.1
	 */
	protected static $_instance = null;

	/**
	 * Main WP_Repair_Data Instance.
	 *
	 * Ensures only one instance of WP_Repair_Data is loaded or can be loaded.
	 *
	 * @since 0.0.1
	 * @static
	 * @see WPRD()
	 * @return WP_Repair_Data - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * WP_Repair_Data Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define WPRD Constants.
	 */
	private function define_constants() {
		$this->define( 'WPRD_ABSPATH', dirname( WPRD_PLUGIN_FILE ) . '/' );
		$this->define( 'WPRD_PLUGIN_BASENAME', plugin_basename( WPRD_PLUGIN_FILE ) );
		$this->define( 'WPRD_VERSION', $this->version );
	}

	/**
	 * Include required core files.
	 */
	public function includes() {
		include_once WPRD_ABSPATH . 'includes/class-wprd-data.php';
		include_once WPRD_ABSPATH . 'includes/class-wprd-helpers.php';
		include_once WPRD_ABSPATH . 'includes/class-wprd-repair.php';
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 0.0.1
	 */
	private function init_hooks() {
		register_activation_hook( WPRD_PLUGIN_FILE, __CLASS__ . '::install' );
		register_deactivation_hook( WPRD_PLUGIN_FILE, __CLASS__ . '::deactivate' );

		add_action( 'wp_loaded', __CLASS__ . '::show_faulty_subscriptions', 20 );
		add_action( 'wp_loaded', __CLASS__ . '::update_repair_mode', 20 );
		add_action( 'wp_loaded', array( 'WPRD_Repair', 'start' ), 20 );
		add_action( 'wp_loaded', array( 'WPRD_Repair', 'apply_subscribeandsave5_coupon' ), 20 );
		add_action( 'wp_loaded', array( 'WPRD_Repair', 'show_subscriptions_needs_coupon' ), 20 );
	}

	public static function show_faulty_subscriptions() {
		if ( isset( $_GET['wprd_show_faulty'] ) && $_GET['wprd_show_faulty'] ) {
			if ( current_user_can( 'administrator' ) ) {
				$faulty_subscription_items = WPRD_Helpers::get_faulty_subscriptions();
				wc_get_template( 'faulty-subscriptions.php', array( 'faulty_subscription_items' => $faulty_subscription_items ), '', WPRD_ABSPATH . 'templates/' );
				die;
			} else {
				wp_die(
					__( 'You do not have permission to make this request.', 'repair-subscription-scheme' ),
					__( 'Permission denied.', 'repair-subscription-scheme' )
				);
			}
		}
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	public static function install() {
		update_option( 'wprd_repair_mode_enabled', 'no', 'no' );
	}

	public static function deactivate() {
		delete_option( 'wprd_repair_mode_enabled' );
	}

	public static function update_repair_mode() {
		if ( isset( $_GET['wprd_repair_mode'] ) ) {
			$enable = 'yes' === $_GET['wprd_repair_mode'] ? 'yes' : 'no';
			update_option( 'wprd_repair_mode_enabled', $enable );

			wp_die( sprintf( 'Repair mode enabled: %s', $enable ), 'Repair mode' );
		}
	}
}
