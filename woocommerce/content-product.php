<?php
/**
 * Product loop item – SlimGebruikt card design
 *
 * @package slimgebruikt
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) || ! $product->is_visible() ) {
	return;
}

$link = get_permalink( $product->get_id() );
?>
<li <?php wc_product_class( array_filter( array( 'product-card', ! empty( $GLOBALS['slimgebruikt_bestsellers'] ) ? 'swiper-slide' : '' ) ), $product ); ?>>
	<div class="product-card__inner">
		<a href="<?php echo esc_url( $link ); ?>" class="product-card__image-link">
			<?php
			if ( function_exists( 'get_field' ) ) {
				$badge = get_field( 'product_badge', $product->get_id() );
				if ( $badge ) {
					echo '<span class="product-badge">' . esc_html( $badge ) . '</span>';
				}
			}
			echo woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' );
			?>
		</a>
		<div class="product-card__body">
			<a href="<?php echo esc_url( $link ); ?>" class="product-card__title"><?php echo esc_html( $product->get_name() ); ?></a>
			<div class="product-card__row">
				<?php woocommerce_template_loop_price(); ?>
				<a href="<?php echo esc_url( $link ); ?>" class="product-card__arrow" aria-label="<?php esc_attr_e( 'Bekijk product', 'slimgebruikt' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M7 17L17 7M17 7H7M17 7v10"/></svg>
				</a>
			</div>
			<button type="button" class="product-card__compare" aria-label="<?php esc_attr_e( 'Vergelijken', 'slimgebruikt' ); ?>">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M7 16V4M7 4l4 4M7 4l-4 4"/><path d="M17 8v12M17 20l4-4M17 20l-4-4"/></svg>
				<?php esc_html_e( 'vergelijken', 'slimgebruikt' ); ?>
			</button>
		</div>
	</div>
</li>
