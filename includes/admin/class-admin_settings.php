<?php
/**
 * Class Admin Settings.
 *
 * @package WooCommerce_Default_Quantity
*/
defined( 'ABSPATH' ) || exit();

class Admin_Settings {
	/**
	 * class constructor
	*/
	public function __construct() {
		add_filter( 'woocommerce_get_sections_products', array( $this, 'register_settings_sections' ) );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'get_default_quantity_settings' ), 10, 2 );
	}

	/**
	 * Register a new settings section on woocommerce product tab.
	 *
	 * @param array $sections Settings sections.
	 *
	 * @return array
	 * @since 1.0.0
	*/
	public function register_settings_sections( $sections ) {
		$sections['product-default-quantity'] = __( 'Default Quantity', 'woocommerce-product-default-quantity' );
		return $sections;
	}

	/**
	 * Get default quantity settings.
	 *
	 * @param array $settings WooCommerce Settings.
	 * @param string $current_section Current Section name.
	 *
	 * @return array
	 * @since 1.0.0
	*/
	public function get_default_quantity_settings( $settings, $current_section ) {
		if ( 'product-default-quantity' !== $current_section ) {
			return $settings;
		}

		$default_quantity_settings = array(
			array(
				'id'    => 'product_default_quantity_options',
				'title' => __( 'Default Quantity', 'woocommerce-product-default-quantity' ),
				'type'  => 'title',
				'desc'  => '',
			),
			array(
				'id'      => 'enable_default_quantity',
				'title'   => __( 'Enable/Disable', 'woocommerce-product-default-quantity' ),
				'type'    => 'checkbox',
				'desc'    => __( 'Enable this options to affect default quantity for products', 'woocommerce-product-default-quantity' ),
				'default' => 'yes',
			),
			array(
				'id'            => 'apply_on_single_page',
				'title'         => __( 'Apply default quantity', 'woocommerce-product-default-quantity' ),
				'desc'          => __( 'Apply default quantity on product single page', 'woocommerce-product-default-quantity' ),
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'default'       => 'yes',
			),
			array(
				'id'            => 'apply_on_shop_page',
				'desc'          => __( 'Apply default quantity on shop page', 'woocommerce-product-default-quantity' ),
				'type'          => 'checkbox',
				'checkboxgroup' => true,
				'default'       => 'yes',
			),
			array(
				'id'            => 'apply_on_archive_page',
				'desc'          => __( 'Apply default quantity on archive/category page', 'woocommerce-product-default-quantity' ),
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
				'default'       => 'yes',
			),
			array(
				'id'                => 'default_quantity_amount',
				'title'             => __( 'Product default quantity', 'woocommerce-product-default-quantity' ),
				'desc'              => __( 'Default quantity', 'woocommerce-product-default-quantity' ),
				'type'              => 'number',
				'custom_attributes' => array(
					'min'  => 0,
					'step' => 1,
				),
				'default'           => 2,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'product_default_quantity_options_end',
			),
		);

		return apply_filters( 'woocommerce_default_quantity_settings', $default_quantity_settings );
	}


}

return new Admin_Settings();
