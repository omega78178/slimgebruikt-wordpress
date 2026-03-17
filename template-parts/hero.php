<?php
/**
 * Hero/banner section
 *
 * @package slimgebruikt
 */
$acf = ( isset( $args ) && isset( $args['acf'] ) ) ? $args['acf'] : array();
$tagline   = $acf['tagline'] ?? 'Slim gebruikt. Slim bespaard.';
$title     = $acf['title'] ?? 'Refurbished smartphones, tablets & gadgets met garantie.';
$desc      = $acf['desc'] ?? 'Tot wel 40% goedkoper dan nieuw. Volledig gecontroleerd en direct leverbaar.';
$btn_prim  = $acf['btn_primary'] ?? 'Bekijk smartphones';
$btn_sec   = $acf['btn_secondary'] ?? 'Bekijk alle deals';

$benefits_title  = $acf['benefits_title'] ?? 'Wie slim is,';
$benefits_accent = $acf['benefits_accent'] ?? 'betaalt minder.';
$benefits_items  = $acf['benefits_items'] ?? array(
	array( 'text' => __( 'Professioneel refurbished', 'slimgebruikt' ) ),
	array( 'text' => __( '2 jaar garantie*', 'slimgebruikt' ) ),
	array( 'text' => __( '30 dagen bedenktijd', 'slimgebruikt' ) ),
	array( 'text' => __( 'Strikte kwaliteitseisen', 'slimgebruikt' ) ),
);

$weekdeal_title = $acf['weekdeal_title'] ?? 'Weekdeal 🔥';
$weekdeal_btn   = $acf['weekdeal_btn'] ?? __( 'Aanbieding bekijken', 'slimgebruikt' );

$shop_url = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop/' );

$weekdeal_product_name = '';
$weekdeal_price        = '';
$weekdeal_link         = $shop_url;
$weekdeal_image_url    = null;

if ( ! empty( $acf['weekdeal_product'] ) && class_exists( 'WooCommerce' ) ) {
	$selected = $acf['weekdeal_product'];
	$product  = is_a( $selected, 'WC_Product' ) ? $selected : wc_get_product( is_object( $selected ) ? $selected->ID : $selected );
	if ( $product && $product->is_visible() ) {
		$weekdeal_product_name = $product->get_name();
		$weekdeal_price        = $product->get_price_html();
		$weekdeal_link         = $product->get_permalink();
		$thumb_id              = $product->get_image_id();
		if ( $thumb_id ) {
			$weekdeal_image_url = wp_get_attachment_image_url( $thumb_id, 'woocommerce_thumbnail' );
		}
	}
}

if ( empty( $weekdeal_product_name ) ) {
	$weekdeal_product_name = __( 'Apple lightning USB Cable 1m', 'slimgebruikt' );
	$weekdeal_price        = '€1,00';
}
?>
<section class="hero">
	<div class="hero__inner">
		<div class="hero__card hero__card--main">
			<p class="hero__tagline"><?php echo esc_html( $tagline ); ?></p>
			<h1 class="hero__title"><?php echo esc_html( $title ); ?></h1>
			<p class="hero__desc"><?php echo wp_kses_post( $desc ); ?></p>
			<div class="hero__cta">
				<a href="<?php echo esc_url( $shop_url ); ?>?filter_product_cat=iphone" class="hero__btn hero__btn--primary">
					<?php echo esc_html( $btn_prim ); ?>
				</a>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="hero__btn hero__btn--secondary">
					<?php echo esc_html( $btn_sec ); ?>
				</a>
			</div>
			<?php
			$parent_cats = get_terms( array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'parent'     => 0,
				'exclude'    => array( get_option( 'default_product_cat' ) ),
				'orderby'    => 'name',
			) );
			$cat_tree     = array();
			$has_children = false;
			if ( ! is_wp_error( $parent_cats ) ) {
				foreach ( $parent_cats as $pc ) {
					$children = get_terms( array(
						'taxonomy'   => 'product_cat',
						'hide_empty' => true,
						'parent'     => $pc->term_id,
						'orderby'    => 'name',
					) );
					$kids = ( ! is_wp_error( $children ) && $children )
						? array_map( function( $c ) { return array( 'slug' => $c->slug, 'name' => $c->name ); }, $children )
						: array();
					if ( $kids ) {
						$has_children = true;
					}
					$cat_tree[] = array(
						'slug'     => $pc->slug,
						'name'     => $pc->name,
						'children' => $kids,
					);
				}
			}
			$storage_terms = get_terms( array( 'taxonomy' => 'pa_opslagruimte', 'hide_empty' => true, 'orderby' => 'name' ) );
			$color_terms   = get_terms( array( 'taxonomy' => 'pa_kleur', 'hide_empty' => true, 'orderby' => 'name' ) );
		?>
			<form class="hero__search" action="<?php echo esc_url( $shop_url ); ?>" method="get"
				data-categories="<?php echo esc_attr( wp_json_encode( $cat_tree ) ); ?>">
				<select class="hero__select" name="hero_merk" id="hero-merk" aria-label="<?php esc_attr_e( 'Merk', 'slimgebruikt' ); ?>">
					<option value=""><?php esc_html_e( 'Merk', 'slimgebruikt' ); ?></option>
					<?php foreach ( $cat_tree as $cat ) : ?>
						<option value="<?php echo esc_attr( $cat['slug'] ); ?>"><?php echo esc_html( $cat['name'] ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php if ( $has_children ) : ?>
				<select class="hero__select" name="hero_model" id="hero-model" aria-label="<?php esc_attr_e( 'Model', 'slimgebruikt' ); ?>" disabled>
					<option value=""><?php esc_html_e( 'Model', 'slimgebruikt' ); ?></option>
				</select>
				<?php endif; ?>
				<select class="hero__select" name="hero_opslag" id="hero-opslag" aria-label="<?php esc_attr_e( 'Opslag', 'slimgebruikt' ); ?>">
					<option value=""><?php esc_html_e( 'Opslag', 'slimgebruikt' ); ?></option>
					<?php if ( ! is_wp_error( $storage_terms ) ) : foreach ( $storage_terms as $t ) : ?>
						<option value="<?php echo esc_attr( $t->slug ); ?>"><?php echo esc_html( $t->name ); ?></option>
					<?php endforeach; endif; ?>
				</select>
				<select class="hero__select" name="hero_kleur" id="hero-kleur" aria-label="<?php esc_attr_e( 'Kleur', 'slimgebruikt' ); ?>">
					<option value=""><?php esc_html_e( 'Kleur', 'slimgebruikt' ); ?></option>
					<?php if ( ! is_wp_error( $color_terms ) ) : foreach ( $color_terms as $t ) : ?>
						<option value="<?php echo esc_attr( $t->slug ); ?>"><?php echo esc_html( $t->name ); ?></option>
					<?php endforeach; endif; ?>
				</select>
				<button type="submit" class="hero__search-btn">
					<?php esc_html_e( 'Zoeken', 'slimgebruikt' ); ?>
					<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
<path d="M0.75 8.75L8.75 0.75M8.75 0.75H2.35M8.75 0.75V7.15" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
				</button>
			</form>
		</div>

		<div class="hero__right">
			<div class="hero__card hero__card--benefits">
				<h2 class="hero__benefits-title"><?php echo esc_html( $benefits_title ); ?><br> <span class="hero__benefits-accent"><?php echo esc_html( $benefits_accent ); ?></span></h2>
				<ul class="hero__benefits-list">
					<?php foreach ( $benefits_items as $item ) : ?>
						<?php if ( ! empty( $item['text'] ) ) : ?>
							<li><?php echo esc_html( $item['text'] ); ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php
			$weekdeal_end = function_exists( 'get_field' ) ? get_field( 'weekdeal_end_datetime', 'option' ) : '';
			$weekdeal_end_iso = $weekdeal_end ? gmdate( 'c', strtotime( $weekdeal_end ) ) : '';
			?>
			<div class="hero__card hero__card--weekdeal" <?php echo $weekdeal_end_iso ? 'data-countdown="' . esc_attr( $weekdeal_end_iso ) . '"' : ''; ?>>
				<div class="hero__weekdeal-header">
					<h3 class="hero__weekdeal-title"><?php echo esc_html( $weekdeal_title ); ?></h3>
					<span class="hero__weekdeal-timer" aria-live="polite"><?php echo $weekdeal_end_iso ? '--:--:--' : '23:59:59'; ?></span>
				</div>
				<div class="hero__weekdeal-body">
					<div class="hero__weekdeal-info">
						<p class="hero__weekdeal-product"><?php echo esc_html( $weekdeal_product_name ); ?></p>
						<p class="hero__weekdeal-price"><?php echo wp_kses_post( $weekdeal_price ); ?></p>
						<a href="<?php echo esc_url( $weekdeal_link ); ?>" class="hero__weekdeal-btn"><?php echo esc_html( $weekdeal_btn ); ?></a>
					</div>
					<div class="hero__weekdeal-image">
						<?php if ( ! empty( $weekdeal_image_url ) ) : ?>
							<img src="<?php echo esc_url( $weekdeal_image_url ); ?>" alt="" loading="lazy">
						<?php else : ?>
							<svg viewBox="0 0 80 80" fill="none" aria-hidden="true"><rect x="20" y="30" width="40" height="8" rx="2" stroke="currentColor" stroke-width="2"/><circle cx="30" cy="34" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M60 34h8M12 34h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
