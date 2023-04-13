<?php

/**
 * WooCommerce class.
 *
 * @package WooCommerce_Default_Quantity
 */
defined('ABSPATH') || exit();

class WC_Hooks
{

    /**
     * Class constructor.
     */
    public function __construct()
    {
        add_filter('woocommerce_quantity_input_args', array(__CLASS__, 'update_quantity_input_args'), 10, 2);
        add_filter('woocommerce_available_variation', array(__CLASS__, 'update_product_variations'), 20);
    }

    /**
     * Updates the quantity arguments.
     *
     * @param array       $data List of data to update.
     * @param \WC_Product $product Product object.
     *
     * @return array
     */
    public static function update_quantity_input_args($data, $product)
    {
        $product_id = $product->get_id();
        $variation_id = 0;

        if ($product->is_type('variation')) {
            $product_id = $product->get_parent_id();
            $variation_id = $product->get_id();
        }

        $apply_default_quantity_on_single_page = get_option('apply_on_single_page');
        if ('yes' != $apply_default_quantity_on_single_page) {
            return $data;
        }

        $product_limits = self::get_product_limits($product_id, $variation_id);
        if ($product_limits['min_qty'] > 0) {
            if ($product->managing_stock() && !$product->backorders_allowed() && $product_limits['min_qty'] > $product->get_stock_quantity()) {
                $data['min_value'] = $product->get_stock_quantity();
            } else {
                $data['min_value'] = $product_limits['min_qty'];
            }
        }

        return $data;
    }

    /**
     * Update product varations.
     * 
     * @param array $data Product data.
     * 
     * @return array
     */
    public static function update_product_variations($data)
    {
        $variation_id = $data['variation_id'];
        $product = wc_get_product($variation_id);
        $parent_product_id = $product->get_parent_id();
        $apply_default_quantity_on_single_page = get_option('apply_on_single_page');
        if ('yes' != $apply_default_quantity_on_single_page) {
            return $data;
        }
        $variation_limits = self::get_variation_limits($variation_id, $parent_product_id);
        if ($variation_limits['min_qty'] > 0) {
            if ($product->managing_stock() && !$product->backorders_allowed() && $variation_limits['min_qty'] > $product->get_stock_quantity()) {
                $data['min_qty'] = $product->get_stock_quantity();
            } else {
                $data['min_qty'] = $variation_limits['min_qty'];
            }
        }

        return $data;
    }
    /**
     * get product limits.
     * 
     * @param int $product_id product id.
     * @param int $variation_id variation id.
     * 
     * @return array
     */
    public static function get_product_limits($product_id, $variation_id)
    {
        $global_default_quantity = get_option('default_quantity_amount');
        $global_cache_key = "woocommerce-default-quantity-{$product_id}-{$variation_id}";
        $limits = wp_cache_get($global_cache_key);
        if (false == $limits) {
            $product_quantity = get_post_meta($product_id, '_product_default_qty', true);
            if (!empty($product_quantity)) {
                $limits['min_qty'] = $product_quantity;
            } else {
                $limits['min_qty'] = !empty($global_default_quantity) ? $global_default_quantity : 1;
            }

            $limits = apply_filters('woocommerce_default_quantity_limits', $limits, $product_id, $variation_id);
            wp_cache_add($global_cache_key, $limits, 'woocommerce-default-quantity');
        }

        return $limits;
    }

    /**
     * Get variation limits.
     * 
     * @param int $variation_id product variation id.
     * @param int $parent_product_id Parent product id.
     * 
     * @return array
     */
    public static function get_variation_limits($variation_id, $parent_product_id)
    {
        $global_default_quantity = get_option('default_quantity_amount');
        $global_cache_key = "woocommerce-default-quantity-{$parent_product_id}-{$variation_id}";
        $limits = wp_cache_get($global_cache_key);
        if (false == $limits) {
            $variation_default_quantity = get_post_meta($variation_id, '_product_default_qty', true);
            $parent_product_default_quantity = get_post_meta($parent_product_id, '_product_default_qty', true);
            if (!empty($variation_default_quantity)) {
                $limits['min_qty'] = $variation_default_quantity;
            } elseif (!empty($parent_product_default_quantity)) {
                $limits['min_qty'] = $parent_product_default_quantity;
            } else {
                $limits['min_qty'] = !empty($global_default_quantity) ? $global_default_quantity : 1;
            }

            $limits = apply_filters('woocommerce_default_quantity_limits', $limits, $parent_product_id, $variation_id);
            wp_cache_add($global_cache_key, $limits, 'woocommerce-default-quantity');
        }
        return $limits;
    }
}
return new WC_Hooks();
