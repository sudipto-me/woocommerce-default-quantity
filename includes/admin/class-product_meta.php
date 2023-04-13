<?php

/**
 * Product meta options.
 *
 * @package WooCommerce_Default_Quantity
 */

defined('ABSPATH') || exit();

class Product_Meta
{
	/**
	 * class constructor
	 */
	public function __construct()
	{
		add_action('woocommerce_product_options_general_product_data', array($this, 'write_tab_options'));
		add_action('woocommerce_process_product_meta', array($this, 'save_product_meta'));
		add_action('woocommerce_variation_options_pricing', array($this, 'add_product_variation_options'), 10, 3);
		add_action('woocommerce_save_product_variation', array($this, 'save_variation_options'), 10, 2);
	}

	/**
	 * Add simple product meta options.
	 */
	public function write_tab_options()
	{
		global $post;
?>
		<div class="options_group woocommerce-product-default-quantity-settings">
			<?php
			echo '<div class="default-quantity-overrride-settings">';

			woocommerce_wp_text_input(
				array(
					'id'                => '_product_default_qty',
					'label'             => __('Product default quantity', 'woocommerce-product-default-quantity'),
					'description'       => __('Set an allowed default quantity of items customers can purchase for this products', 'woocommerce-product-default-quantity'),
					'desc_tip'          => true,
					'type'              => 'number',
					'custom_attributes' => array(
						'step' => 'any',
						'min'  => '0',
					),
				)
			);
			echo '</div>';

			do_action('default_quantity_after_override_settings');
			?>
		</div>
	<?php
	}

	/**
	 * Save product meta data.
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function save_product_meta($product_id)
	{
		$default_quantity = !empty(filter_input(INPUT_POST, '_product_default_qty', FILTER_SANITIZE_NUMBER_FLOAT)) ? filter_input(INPUT_POST, '_product_default_qty', FILTER_SANITIZE_NUMBER_FLOAT) : 0;
		update_post_meta($product_id, '_product_default_qty', $default_quantity);
	}

	/**
	 * Add default quantity on variations.
	 *
	 * @param int $loop Loop.
	 * @param array $variation_data Variation data.
	 * @param WC_Product_Variation $variation Variations.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function add_product_variation_options($loop, $variation_data, $variation)
	{
	?>
		<div class="options_group woocommerce-variation-product-default-quantity-settings">
			<?php
			echo '<div class="default-quantity-overrride-settings">';
			$variation_id     = $variation->ID;
			$default_quantity = get_post_meta($variation_id, '_product_default_qty', true);
			woocommerce_wp_text_input(
				array(
					'id'                => "_product_default_qty[$loop]",
					'name'              => "_product_default_qty_$loop",
					'value'             => !empty($default_quantity) ? $default_quantity : '',
					'label'             => __('Product default quantity', 'woocommerce-product-default-quantity'),
					'description'       => __('Set an allowed default quantity of items customers can purchase for this variation', 'woocommerce-product-default-quantity'),
					'desc_tip'          => true,
					'type'              => 'number',
					'custom_attributes' => array(
						'step' => 'any',
						'min'  => '0',
					),
					'wrapper_class'     => 'form-row',
				)
			);
			echo '</div>';

			do_action('default_quantity_variation_after_override_settings');
			?>
		</div>
<?php
	}

	/**
	 * Save variation data options.
	 *
	 * @param int $variation_id Variation ID.
	 * @param int $index Index.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function save_variation_options($variation_id, $index)
	{
		$default_quantity = !empty(filter_input(INPUT_POST, "_product_default_qty_$index", FILTER_SANITIZE_NUMBER_FLOAT)) ? filter_input(INPUT_POST, "_product_default_qty_$index", FILTER_SANITIZE_NUMBER_FLOAT) : 0;
		update_post_meta($variation_id, '_product_default_qty', $default_quantity);
	}
}
return new Product_Meta();
