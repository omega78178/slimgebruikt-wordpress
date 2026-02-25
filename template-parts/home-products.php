<?php
/**
 * Home products – WooCommerce product grid
 *
 * @package slimgebruikt
 */
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$iphone_slugs = array( 'iphone', 'iphones' );
$products     = array();
foreach ( $iphone_slugs as $slug ) {
	$products = wc_get_products(
		array(
			'limit'    => 12,
			'category' => array( $slug ),
			'orderby'  => 'menu_order',
			'order'    => 'ASC',
			'status'   => 'publish',
		)
	);
	if ( ! empty( $products ) ) {
		break;
	}
}

if ( empty( $products ) ) {
	return;
}

$iphone_term = get_term_by( 'slug', 'iphone', 'product_cat' ) ?: get_term_by( 'slug', 'iphones', 'product_cat' );
?>
<?php
$acf = ( isset( $args ) && isset( $args['acf'] ) ) ? $args['acf'] : array();
$products_title  = $acf['title'] ?? 'Refurbished iPhones';
$products_link   = $acf['link_text'] ?? __( 'bekijk alle iphones', 'slimgebruikt' );

$iphone_link = get_permalink( wc_get_page_id( 'shop' ) );
if ( $iphone_term ) {
	$term_link = get_term_link( $iphone_term );
	if ( ! is_wp_error( $term_link ) ) {
		$iphone_link = $term_link;
	}
}
?>
<div class="products-section">
	<header class="products-section__header">
		<h2 class="products-section__title"><?php echo esc_html( $products_title ); ?></h2>
		<div class="products-section__actions">
			<div class="products-section__nav" aria-label="<?php esc_attr_e( 'Carousel navigatie', 'slimgebruikt' ); ?>">
				<button type="button" class="products-section__btn products-section__btn--prev" aria-label="<?php esc_attr_e( 'Vorige', 'slimgebruikt' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
				</button>
				<button type="button" class="products-section__btn products-section__btn--next" aria-label="<?php esc_attr_e( 'Volgende', 'slimgebruikt' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
				</button>
			</div>
			<a href="<?php echo esc_url( $iphone_link ); ?>" class="products-section__link">
				<?php echo esc_html( $products_link ); ?>
				<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
<path d="M1.65878 23.776L23.776 1.65881M23.776 1.65881H6.08222M23.776 1.65881V19.3526" stroke="black" stroke-width="3.31758" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
			</a>
		</div>
	</header>
	<section class="products-grid products-section__grid">
		<div class="products-grid__inner">
			<ul class="products columns-4">
				<?php
				foreach ( $products as $product ) {
					if ( $product->is_visible() ) {
						$GLOBALS['product'] = $product;
						wc_get_template_part( 'content', 'product' );
					}
				}
				?>
			</ul>
		</div>
	</section>
	<div class="swiper products-swiper products-section__slider">
		<ul class="swiper-wrapper products">
			<?php
			foreach ( $products as $product ) {
				if ( $product->is_visible() ) {
					$GLOBALS['product'] = $product;
					$GLOBALS['slimgebruikt_bestsellers'] = true;
					wc_get_template_part( 'content', 'product' );
					unset( $GLOBALS['slimgebruikt_bestsellers'] );
				}
			}
			?>
		</ul>
	</div>
</div>
