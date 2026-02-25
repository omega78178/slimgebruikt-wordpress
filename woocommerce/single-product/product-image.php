<?php
/**
 * Single Product Image – SlimGebruikt gallery design
 *
 * @package slimgebruikt
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

use Automattic\WooCommerce\Enums\ProductType;

global $product;

$garantie     = function_exists( 'get_field' ) ? get_field( 'product_gallery_garantie', 'option' ) : '';
$refurbished  = function_exists( 'get_field' ) ? get_field( 'product_gallery_refurbished', 'option' ) : 'Professioneel refurbished';
$rating       = function_exists( 'get_field' ) ? (int) get_field( 'product_gallery_rating', 'option' ) : 5;
$trustmark_1  = function_exists( 'get_field' ) ? get_field( 'product_gallery_trustmark_1', 'option' ) : 'Webshop Trustmark';
$trustmark_2  = function_exists( 'get_field' ) ? get_field( 'product_gallery_trustmark_2', 'option' ) : 'Webshop Keurmerk';

if ( empty( $garantie ) ) {
	$garantie = __( '24 maanden garantie', 'slimgebruikt' );
}

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);
?>
<div class="product-gallery-wrap">
	<div class="product-gallery-card">
		<?php if ( $garantie ) : ?>
			<span class="product-gallery__tag product-gallery__tag--garantie">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
				<?php echo esc_html( $garantie ); ?>
			</span>
		<?php endif; ?>

		<?php if ( $refurbished || $rating > 0 ) : ?>
			<div class="product-gallery__refurbished">
				<span class="product-gallery__brand"><span class="product-gallery__brand--slim">Slim</span><span class="product-gallery__brand--gebruikt">Gebruikt</span></span>
				<p class="product-gallery__refurbished-text"><?php echo esc_html( $refurbished ); ?></p>
				<?php if ( $rating > 0 ) : ?>
					<div class="product-gallery__stars" aria-hidden="true">
						<?php for ( $i = 0; $i < 5; $i++ ) : ?>
							<span class="product-gallery__star<?php echo $i < $rating ? ' product-gallery__star--filled' : ''; ?>">★</span>
						<?php endfor; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $trustmark_1 || $trustmark_2 ) : ?>
			<div class="product-gallery__trustmarks">
				<?php if ( $trustmark_1 ) : ?>
					<span class="product-gallery__trustmark">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
						<?php echo esc_html( $trustmark_1 ); ?>
					</span>
				<?php endif; ?>
				<?php if ( $trustmark_2 ) : ?>
					<span class="product-gallery__trustmark">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
						<?php echo esc_html( $trustmark_2 ); ?>
					</span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
			<div class="woocommerce-product-gallery__wrapper">
				<?php
				if ( $post_thumbnail_id ) {
					$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
				} else {
					$wrapper_classname = $product->is_type( ProductType::VARIABLE ) && ! empty( $product->get_visible_children() ) && '' !== $product->get_price() ?
						'woocommerce-product-gallery__image woocommerce-product-gallery__image--placeholder' :
						'woocommerce-product-gallery__image--placeholder';
					$html  = sprintf( '<div class="%s">', esc_attr( $wrapper_classname ) );
					$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
					$html .= '</div>';
				}
				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				do_action( 'woocommerce_product_thumbnails' );
				?>
			</div>
		</div>
	</div>
</div>