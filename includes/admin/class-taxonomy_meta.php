<?php
/**
 * Product taxonomy class.
 *
 * @package WooCommerce_Default_Quantity
*/
defined( 'ABSPATH' ) || exit();

class Taxonomy_Meta {
	/**
	 * class constructor
	*/
	public function __construct() {
		add_action( 'product_cat_add_form_fields', array( $this, 'add_default_quantity_fields' ) );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_default_quantity_fields' ) );
		add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );

		add_filter( 'manage_edit-product_cat_columns', array( $this, 'add_custom_columns_on_product_category_table' ) );
		add_filter( 'manage_product_cat_custom_column', array( $this, 'manage_custom_columns_content' ), 10, 3 );
	}

	/**
	 * Add default quantity meta fields on the product category add new page.
	 *
	 *
	 * @return void
	 * @since 1.0.0
	*/
	public function add_default_quantity_fields() {
		?>
		<div class="form-field term-display-type-wrap">
			<label for="_category_default_quantity"><?php esc_html_e( 'Default Quantity for this category', 'woocommerce-product-default-quantity' ); ?></label>
			<input type="text" id="_category_default_quantity" name="_category_default_quantity" size="40">
		</div>

		<?php
	}

	/**
	 * Add default quantity on the product category edit page.
	 *
	 * @param WP_Term $term Product category.
	 *
	 * @return void
	 * @since 1.0.0
	*/
	public function edit_default_quantity_fields( $term ) {
		$get_default_quantity = get_term_meta( $term->term_id, 'product_default_quantity', true );
		?>
		<tr class="form-field term-display-type-wrap">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Default Quantity for this category', 'woocommerce-product-default-quantity' ); ?></label></th>
			<td>
				<input type="text" id="_category_default_quantity" name="_category_default_quantity" size="40" value="<?php echo esc_attr( $get_default_quantity ); ?>">
			</td>
		</tr>

		<?php
	}

	/**
	 * Save category fields.
	 *
	 * @param mixed  $term_id Term ID being saved.
	 * @param mixed  $tt_id Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( isset( $_POST['_category_default_quantity'] ) && 'product_cat' === $taxonomy ) { // WPCS: CSRF ok, input var ok.
			update_term_meta( $term_id, 'product_default_quantity', esc_attr( $_POST['_category_default_quantity'] ) ); // WPCS: CSRF ok, sanitization ok, input var ok.
		}
	}

	/**
	 * Add custom column on the product category list table.
	 *
	 * @param array $columns list table columns.
	 *
	 * @return array
	 * @since 1.0.0
	*/
	public function add_custom_columns_on_product_category_table( $columns ) {
		$columns['default_quantity'] = __( 'Default Quantity', 'woocommerce-product-default-quantity' );
		return $columns;
	}

	/**
	 * Populate custom column with data.
	 *
	 * @param string $content Column content.
	 * @param string $column_name Column name.
	 * @param int $term_id Term id.
	 *
	 * @return string
	 * @since 1.0.0
	*/
	public function manage_custom_columns_content( $content, $column_name, $term_id ) {
		switch ( $column_name ) {
			case 'default_quantity':
				$default_quantity = get_term_meta( $term_id, 'product_default_quantity', true );
				if ( ! empty( $default_quantity ) ) {
					$content = $default_quantity;
				} else {
					$content = '&mdash;';
				}
				break;
			default:
				break;
		}
		return $content;
	}

}
return new Taxonomy_Meta();
