<?php
/**
 * Home bestsellers – horizontale swiper/carousel met productkaarten
 *
 * @package slimgebruikt
 */
$acf = ( isset( $args ) && isset( $args['acf'] ) ) ? $args['acf'] : array();
$bestsellers_title = $acf['title'] ?? __( 'Bestsellers', 'slimgebruikt' );

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$args = array(
	'post_type'      => 'product',
	'posts_per_page' => 12,
	'meta_key'       => 'total_sales',
	'orderby'        => 'meta_value_num',
	'order'          => 'DESC',
	'post_status'    => 'publish',
);

$query = new WP_Query( $args );

if ( ! $query->have_posts() ) {
	return;
}

$products = array();
while ( $query->have_posts() ) {
	$query->the_post();
	$product = wc_get_product( get_the_ID() );
	if ( $product && $product->is_visible() ) {
		$products[] = $product;
	}
}
wp_reset_postdata();

if ( empty( $products ) ) {
	return;
}
?>
<div class="bestsellers-section">
	<header class="bestsellers-section__header">
		<h2 class="bestsellers-section__title"><?php echo esc_html( $bestsellers_title ); ?></h2>
		<div class="bestsellers-section__nav" aria-label="<?php esc_attr_e( 'Carousel navigatie', 'slimgebruikt' ); ?>">
			<button type="button" class="bestsellers-section__btn bestsellers-section__btn--prev" id="bestsellers-prev" aria-label="<?php esc_attr_e( 'Vorige', 'slimgebruikt' ); ?>">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
			</button>
			<button type="button" class="bestsellers-section__btn bestsellers-section__btn--next" id="bestsellers-next" aria-label="<?php esc_attr_e( 'Volgende', 'slimgebruikt' ); ?>">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
			</button>
		</div>
	</header>
	<div class="swiper bestsellers-swiper">
		<ul class="swiper-wrapper products">
			<?php
			$GLOBALS['slimgebruikt_bestsellers'] = true;
			foreach ( $products as $product ) {
				$GLOBALS['product'] = $product;
				wc_get_template_part( 'content', 'product' );
			}
			$GLOBALS['slimgebruikt_bestsellers'] = false;
			?>
		</ul>
	</div>
</div>
