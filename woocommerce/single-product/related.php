<?php
/**
 * Related Products – SlimGebruikt slider (zoals bestsellers)
 *
 * @package slimgebruikt
 * @version 10.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$min_products = 4;
$current_id   = $product ? $product->get_id() : 0;

// Bij te weinig gerelateerde producten: vervang door bestsellers of categorie
if ( count( $related_products ) < $min_products ) {
	$exclude_ids = array( $current_id );
	foreach ( $related_products as $rp ) {
		$exclude_ids[] = $rp->get_id();
	}
	$cats = $product ? wc_get_product_term_ids( $product->get_id(), 'product_cat' ) : array();
	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => 12,
		'post__not_in'   => $exclude_ids,
		'post_status'    => 'publish',
	);
	if ( ! empty( $cats ) ) {
		$args['tax_query'] = array( array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $cats ) );
	}
	$fallback = new WP_Query( $args );
	$related_products = array();
	while ( $fallback->have_posts() && count( $related_products ) < $min_products ) {
		$fallback->the_post();
		$p = wc_get_product( get_the_ID() );
		if ( $p && $p->is_visible() ) {
			$related_products[] = $p;
		}
	}
	wp_reset_postdata();
	if ( count( $related_products ) < $min_products ) {
		unset( $args['tax_query'] );
		$args['post__not_in']   = array( $current_id );
		$args['meta_key']       = 'total_sales';
		$args['orderby']        = 'meta_value_num';
		$args['order']          = 'DESC';
		$args['posts_per_page'] = 12;
		$bestsellers = new WP_Query( $args );
		$related_products = array();
		while ( $bestsellers->have_posts() && count( $related_products ) < 12 ) {
			$bestsellers->the_post();
			$p = wc_get_product( get_the_ID() );
			if ( $p && $p->is_visible() ) {
				$related_products[] = $p;
			}
		}
		wp_reset_postdata();
	}
	if ( empty( $related_products ) ) {
		unset( $args['meta_key'], $args['order'] );
		$args['orderby']        = 'rand';
		$args['post__not_in']   = array( $current_id );
		$args['posts_per_page'] = 12;
		$recent = new WP_Query( $args );
		$related_products = array();
		while ( $recent->have_posts() && count( $related_products ) < 12 ) {
			$recent->the_post();
			$p = wc_get_product( get_the_ID() );
			if ( $p && $p->is_visible() && $p->get_id() !== (int) $current_id ) {
				$related_products[] = $p;
			}
		}
		wp_reset_postdata();
	}
}

if ( empty( $related_products ) ) {
	return;
}

if ( function_exists( 'wp_increase_content_media_count' ) ) {
	$content_media_count = wp_increase_content_media_count( 0 );
	if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
		wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
	}
}

$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Vaak bekeken', 'slimgebruikt' ) );
?>
<section class="related products product-vaak-bekeken__slider">
	<div class="bestsellers-section">
		<header class="bestsellers-section__header">
			<?php if ( $heading ) : ?>
				<h2 class="bestsellers-section__title"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<div class="bestsellers-section__nav" aria-label="<?php esc_attr_e( 'Carousel navigatie', 'slimgebruikt' ); ?>">
				<button type="button" class="bestsellers-section__btn bestsellers-section__btn--prev" id="vaak-bekeken-prev" aria-label="<?php esc_attr_e( 'Vorige', 'slimgebruikt' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
				</button>
				<button type="button" class="bestsellers-section__btn bestsellers-section__btn--next" id="vaak-bekeken-next" aria-label="<?php esc_attr_e( 'Volgende', 'slimgebruikt' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
				</button>
			</div>
		</header>
		<div class="vaak-bekeken-swiper-wrap">
			<div class="swiper vaak-bekeken-swiper">
				<ul class="swiper-wrapper products">
				<?php
				$saved_product = isset( $GLOBALS['product'] ) ? $GLOBALS['product'] : null;
				$GLOBALS['slimgebruikt_bestsellers'] = true;
				foreach ( $related_products as $related_product ) :
					$GLOBALS['product'] = $related_product;
					$post_object       = get_post( $related_product->get_id() );
					setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore
					wc_get_template_part( 'content', 'product' );
				endforeach;
				$GLOBALS['slimgebruikt_bestsellers'] = false;
				$GLOBALS['product'] = $saved_product;
				?>
				</ul>
			</div>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
