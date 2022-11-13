<?php
/**
 * Plugin Name: Default Product Quantity for WooCommerce
 * Plugin URI:  https://github.com/sudipto-me/woocommerce-product-default-quantity
 * Description: The best WooCommerce Plugin to set default quantity for products rather than 1.
 * Version:     1.0.0
 * Author:      shakhari
 * Author URI:  https://shakahri.cc
 * License:     GPLv2+
 * Text Domain: woocommerce-product-default-quantity
 * Domain Path: /i18n/languages/
 * Tested up to: 6.0
 * WC requires at least: 3.0.0
 * WC tested up to: 7.0
 */

// don't call the file directly
defined( 'ABSPATH' ) || exit();

/**
 * Default_Product_Quantity class.
 *
 * @class Default_Product_Quantity contains everything for the plugin.
 */
class Default_Product_Quantity {
	/**
	 * Default_Product_Quantity version.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $version = '1.0.0';

	/**
	 * This plugin's instance
	 *
	 * @var Default_Product_Quantity The one true Default_Product_Quantity
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * Main Default_Product_Quantity Instance
	 *
	 * Insures that only one instance of Default_Product_Quantity exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @return Default_Product_Quantity The one true Default_Product_Quantity
	 * @since 1.0.0
	 * @static var array $instance
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Default_Product_Quantity ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Return plugin version.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 **/
	public function get_version() {
		return $this->version;
	}

	/**
	 * Plugin URL getter.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Plugin path getter.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Plugin base path name getter.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function plugin_basename() {
		return plugin_basename( __FILE__ );
	}

	/**
	 * Initialize plugin for localization
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function localization_setup() {
		load_plugin_textdomain( 'woocommerce-product-default-quantity', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );
	}


	/**
	 * Determines if the wc active.
	 *
	 * @return bool
	 * @since 1.0.0
	 *
	 */
	public function is_wc_active() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		return is_plugin_active( 'woocommerce/woocommerce.php' ) == true;
	}

	/**
	 * WooCommerce plugin dependency notice
	 * @since 1.0.0
	 */
	public function wc_missing_notice() {
		if ( ! $this->is_wc_active() ) {
			$message = sprintf(
				__( '<strong>Default Product Quantity for WooCommerce</strong> requires <strong>WooCommerce</strong> installed and activated. Please Install %1$s WooCommerce. %2$s', 'woocommerce-product-default-quantity' ),
				'<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">',
				'</a>'
			);
			echo sprintf( '<div class="notice notice-error"><p>%s</p></div>', $message );
		}
	}

	/**
	 * Define constant if not already defined
	 *
	 * @param string $name
	 * @param string|bool $value
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @access protected
	 * @return void
	 */

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-product-default-quantity' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @access protected
	 * @return void
	 */

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-product-default-quantity' ), '1.0.0' );
	}

	/**
	 * Default_Product_Quantity constructor.
	 */
	private function __construct() {
		$this->define_constants();
		register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin' ) );

		add_action( 'woocommerce_loaded', array( $this, 'init_plugin' ) );
		add_action( 'admin_notices', array( $this, 'wc_missing_notice' ) );
	}

	/**
	 * Define all constants
	 * @return void
	 * @since 1.0.0
	 */
	public function define_constants() {
		$this->define( 'DEFAULT_PRODUCT_QUANTITY_PLUGIN_VERSION', $this->version );
		$this->define( 'DEFAULT_PRODUCT_QUANTITY_PLUGIN_FILE', __FILE__ );
		$this->define( 'DEFAULT_PRODUCT_QUANTITY_PLUGIN_DIR', dirname( __FILE__ ) );
		$this->define( 'DEFAULT_PRODUCT_QUANTITY_PLUGIN_INC_DIR', dirname( __FILE__ ) . '/includes' );
	}

	/**
	 * Activate plugin.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function activate_plugin() {
		//require_once dirname( __FILE__ ) . '/includes/class-installer.php';
		//Default_Product_Quantity_Installer::install();
	}

	/**
	 * Deactivate plugin.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function deactivate_plugin() {

	}

	/**
	 * Load the plugin when WooCommerce loaded.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init_plugin() {
		$this->includes();
		$this->init_hooks();
	}


	/**
	 * Include required core files used in admin and on the frontend.
	 * @since 1.0.0
	 */
	public function includes() {
		//        require_once dirname( __FILE__ ) . '/includes/woocommerce-product-default-quantity-functions.php';
		//        require_once dirname( __FILE__ ) . '/includes/woocommerce-product-default-quantity-misc-functions.php';
		//        require_once dirname( __FILE__ ) . '/includes/class-woocommerce-product-default-quantity-query.php';
		//        require_once dirname( __FILE__ ) . '/includes/class-woocommerce-product-default-quantity-installer.php';
		//        require_once dirname( __FILE__ ) . '/includes/class-woocommerce-product-default-quantity-order-handler.php';
		//        require_once dirname( __FILE__ ) . '/includes/class-woocommerce-product-default-quantity-encryption.php';
		//        require_once dirname( __FILE__ ) . '/includes/class-woocommerce-product-default-quantity-ajax.php';
		//        require_once dirname( __FILE__ ) . '/includes/class-woocommerce-product-default-quantity-api.php';
		//        require_once dirname( __FILE__ ) . '/includes/class-woocommerce-product-default-quantity-cron.php';
		//        require_once dirname( __FILE__ ) . '/includes/class-woocommerce-product-default-quantity-compat.php';
		//
		if ( is_admin() ) {
			require_once dirname( __FILE__ ) . '/includes/admin/class-admin_settings.php';
		}
		do_action( 'default_product_quantity__loaded' );
	}


	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'localization_setup' ) );
		//add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), - 1 );
	}


	/**
	 * When WP has loaded all plugins, trigger the `default_product_quantity__loaded` hook.
	 *
	 * This ensures `default_product_quantity__loaded` is called only after all other plugins
	 * are loaded, to avoid issues caused by plugin directory naming changing
	 *
	 * @since 1.0.0
	 */
	public function on_plugins_loaded() {
		do_action( 'default_product_quantity__loaded' );
	}

}

/**
 * The main function responsible for returning the one true WC Serial Numbers
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @return Default_Product_Quantity
 * @since 1.0.0
 */
function default_product_quantity() {
	return Default_Product_Quantity::init();
}

//lets go.
default_product_quantity();
