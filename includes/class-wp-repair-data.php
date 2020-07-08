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
	public $version = '1.0.5';

	/**
	 * The single instance of the class.
	 *
	 * @var WP_Repair_Data
	 * @since 0.0.1
	 */
	protected static $_instance = null;

	private static $months = array(
			'jan' => array(
				'start_date' => '2020-01-01 00:00:00',
				'end_date'   => '2020-02-01 00:00:00',
			),

			'feb' => array(
				'start_date' => '2020-02-01 00:00:00',
				'end_date'   => '2020-03-01 00:00:00',
			),

			'mar' => array(
				'start_date' => '2020-03-01 00:00:00',
				'end_date'   => '2020-04-01 00:00:00',
			),

			'apr' => array(
				'start_date' => '2020-04-01 00:00:00',
				'end_date'   => '2020-05-01 00:00:00',
			),

			'may' => array(
				'start_date' => '2020-05-01 00:00:00',
				'end_date'   => '2020-06-01 00:00:00',
			),

			'jun' => array(
				'start_date' => '2020-06-01 00:00:00',
				'end_date'   => '2020-07-01 00:00:00',
			),

			'jul' => array(
				'start_date' => '2020-07-01 00:00:00',
				'end_date'   => '2020-08-01 00:00:00',
			),
		);

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
		include_once WPRD_ABSPATH . 'includes/wprd-functions.php';
		include_once WPRD_ABSPATH . 'includes/class-wprd-data.php';
		include_once WPRD_ABSPATH . 'includes/class-wprd-helpers.php';
		include_once WPRD_ABSPATH . 'includes/class-wprd-repair.php';
		include_once WPRD_ABSPATH . 'includes/class-wprd-refund-missing-discount.php';
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
		add_action( 'wp_loaded', __CLASS__ . '::show_invalid_renewals', 20 );
		add_action( 'wp_loaded', __CLASS__ . '::show_invalid_subs', 20 );
		add_action( 'wp_loaded', array( 'WPRD_Repair', 'get_query_to_reset_coupon_used_by' ), 20 );
	}

	public static function show_invalid_subs() {
		if ( ! isset( $_GET['show_invalid_subs'] ) || ! $_GET['show_invalid_subs'] ) {
			return;
		}
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		$month = $_GET['mon'] ?? 'jan';

		$subscriptions        = WPRD_Data::get_subscriptions( self::$months[ $month ]['start_date'], self::$months[ $month ]['end_date'] );
		$coupon_subscriptions = array_filter( $subscriptions, 'wprd_has_coupon' );
		include_once WPRD_ABSPATH . 'templates/invalid-subs.php';
		die;
	}

	public static function show_invalid_renewals() {
		if ( ! isset( $_GET['show_invalid_renewals'] ) || ! $_GET['show_invalid_renewals'] ) {
			return;
		}

		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		$month = $_GET['mon'] ?? 'jan';

		$subscriptions        = WPRD_Data::get_subscriptions( self::$months[ $month ]['start_date'], self::$months[ $month ]['end_date'] );
		$coupon_subscriptions = array_filter( $subscriptions, 'wprd_has_coupon' );
		include_once WPRD_ABSPATH . 'templates/invalid-renewals-compact.php';
		die;
	}

	private static function calculate_discount( $order, $percent ) {
		$discount = 0.00;

		foreach ( $order->get_items() as $item ) {
			$item_discount = $item->get_subtotal() * ($percent/100);
			$discount     += round($item_discount, 2, PHP_ROUND_HALF_ODD);
		}

		return $discount;
	}

	private static function calculate_difference( $actual, $calculated ) {
		$precision = 0.05;

		$diff = $actual - $calculated;

		if ( $diff < 0 ) {
			if ( (-1 * $diff) < $precision ) {
				return sprintf( '<strong>Correct discount given</strong>' );
			} else {
				return sprintf( '<strong>Discount missing: $%s</strong>', wc_format_decimal( $diff * -1 ) );
			}
		} elseif ( $diff > 0 ) {
			if ( $diff < $precision ) {
				return sprintf( '<strong>Correct discount given</strong>' );
			} else {
				return sprintf( '<strong>Charged less: <strong>$%s</strong>', wc_format_decimal( $diff ) );
			}
		} else {
			return sprintf( '<strong>Correct discount given</strong>' );
		}
	}

	private static function calculate_difference_renewal( $actual, $calculated ) {
		$precision = 0.05;

		$diff = $actual - $calculated;

		if ( $diff < 0 ) {
			if ( ( -1 * $diff) < $precision ) {
				return 0;
			} else {
				return floatval( wc_format_decimal( $diff ) );
			}
		} elseif ( $diff > 0 ) {
			if ( $diff < $precision ) {
				return 0;
			} else {
				return floatval( wc_format_decimal( $diff ) );
			}
		} else {
			return 0;
		}
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
