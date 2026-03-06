<?php
/**
 * Variable product add to cart – SlimGebruikt button-style configuratie
 *
 * @package slimgebruikt
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Volgorde: 1 kleur, 2 opslagruimte, 3 optische toestand, 4 batterij
$attr_order = array( 'kleur', 'color', 'opslag', 'storage', 'geheugen', 'ram', 'optisch', 'conditie', 'batterij', 'battery' );
uksort( $attributes, function ( $a, $b ) use ( $attr_order ) {
	$a_lower = strtolower( (string) $a );
	$b_lower = strtolower( (string) $b );
	$pos_a   = 999;
	$pos_b   = 999;
	foreach ( $attr_order as $i => $key ) {
		if ( false !== strpos( $a_lower, $key ) ) { $pos_a = $i; break; }
	}
	foreach ( $attr_order as $i => $key ) {
		if ( false !== strpos( $b_lower, $key ) ) { $pos_b = $i; break; }
	}
	return $pos_a - $pos_b;
} );

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

// Alle variaties incl. uitverkocht voor beschikbaarheid per stap
$all_variations = array();
foreach ( $product->get_children() as $variation_id ) {
	$var = wc_get_product( $variation_id );
	if ( $var && $var->exists() && $var->is_type( 'variation' ) ) {
		$data = $product->get_available_variation( $var );
		if ( is_array( $data ) ) {
			$all_variations[] = $data;
		}
	}
}
$all_variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( wp_json_encode( $all_variations ) ) : _wp_specialchars( wp_json_encode( $all_variations ), ENT_QUOTES, 'UTF-8', true );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart product-config" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data" data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // phpcs:ignore ?>" data-all_variations="<?php echo $all_variations_attr; // phpcs:ignore ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		<div class="variations product-config__options">
			<?php foreach ( $attributes as $attribute_name => $options ) : ?>
				<?php
				$attr_lower  = strtolower( (string) $attribute_name );
				$is_optisch  = ( false !== strpos( $attr_lower, 'optisch' ) || false !== strpos( $attr_lower, 'conditie' ) );
				$tooltip_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M7.04487 6.97298C7.21324 6.45418 7.52166 5.99248 7.9363 5.63812C8.35095 5.28375 8.85604 5.05089 9.39475 4.96542C9.93345 4.87995 10.485 4.94503 10.9889 5.15365C11.4929 5.36227 11.9293 5.70639 12.25 6.14761C12.5707 6.58882 12.7627 7.10995 12.8056 7.6537C12.8485 8.19745 12.74 8.74261 12.4925 9.22863C12.2449 9.71464 11.8681 10.1224 11.4031 10.4074C10.938 10.6924 10.4032 10.8433 9.85779 10.8433V11.8295M9.85779 18.7297C4.95795 18.7297 0.98584 14.7576 0.98584 9.85773C0.98584 4.95789 4.95795 0.985779 9.85779 0.985779C14.7576 0.985779 18.7297 4.95789 18.7297 9.85773C18.7297 14.7576 14.7576 18.7297 9.85779 18.7297ZM9.90689 14.7866V14.8852L9.80869 14.8854V14.7866H9.90689Z" stroke="currentColor" stroke-width="1.97154" stroke-linecap="round" stroke-linejoin="round"/></svg>';
				?>
				<div class="product-config__group">
					<label class="product-config__label">
						<?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?>
						<?php if ( $is_optisch ) : ?>
							<span class="product-config__tooltip-wrap" title="<?php echo esc_attr__( 'Meer informatie', 'slimgebruikt' ); ?>">
								<span class="product-config__tooltip-icon"><?php echo $tooltip_svg; // phpcs:ignore ?></span>
								<span class="product-config__tooltip-text"><?php esc_html_e( 'Meer informatie', 'slimgebruikt' ); ?></span>
							</span>
						<?php endif; ?>
					</label>
					<div class="product-config__values">
						<?php
						wc_dropdown_variation_attribute_options(
							array(
								'options'   => $options,
								'attribute' => $attribute_name,
								'product'   => $product,
							)
						);
						?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="reset_variations_alert screen-reader-text" role="alert" aria-live="polite"></div>
		<?php echo wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '' ) ); ?>
		<?php do_action( 'woocommerce_after_variations_table' ); ?>

		<div class="single_variation_wrap">
			<?php
			do_action( 'woocommerce_before_single_variation' );
			do_action( 'woocommerce_single_variation' );
			do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
