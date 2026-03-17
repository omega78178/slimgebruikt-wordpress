<?php
/**
 * Shop archive template – Mobistock
 *
 * @package slimgebruikt
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/* Breadcrumbs – zelfde WooCommerce default als productpagina */
if ( function_exists( 'woocommerce_breadcrumb' ) ) {
	echo '<div class="shop-breadcrumbs-wrap">';
	woocommerce_breadcrumb();
	echo '</div>';
}

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

// Hero tekst: dynamisch per categorie via ACF, met fallback
$refurb_title = __( 'Onze producten', 'slimgebruikt' );
$refurb_text  = __( 'Al onze producten zijn professioneel hersteld, gereinigd en getest. Ze voldoen aan strikte kwaliteitseisen en werken als nieuw. Koop met vertrouwen en bespaar tot wel 40% ten opzichte van nieuw.', 'slimgebruikt' );

if ( is_product_taxonomy() ) {
	$term = get_queried_object();
	if ( $term && function_exists( 'get_field' ) ) {
		$acf_title = get_field( 'cat_hero_title', $term );
		$acf_text  = get_field( 'cat_hero_text', $term );
		$refurb_title = $acf_title ?: $term->name;
		$refurb_text  = $acf_text ?: ( $term->description ?: $refurb_text );
	} elseif ( $term ) {
		$refurb_title = $term->name;
		if ( $term->description ) {
			$refurb_text = $term->description;
		}
	}
}

// Benefits voor "Wie slim is"
$shop_benefits = array(
	__( 'Professionele service & up-to-date', 'slimgebruikt' ),
	__( 'Gratis garantie & retourneren', 'slimgebruikt' ),
	__( 'Duurzame keuze', 'slimgebruikt' ),
	__( 'BTW-betaald & betaalbaar', 'slimgebruikt' ),
	__( 'Betaal later met Klarna', 'slimgebruikt' ),
	__( 'Niet goed, geld terug', 'slimgebruikt' ),
);
?>

<div class="shop-page">
	<?php /* Shop hero: Refurbished info + Weekdeal */ ?>
	<section class="shop-hero">
		<div class="shop-hero__inner">
			<div class="shop-hero__info">
				<h1 class="shop-hero__info-title"><?php echo esc_html( $refurb_title ); ?></h1>
				<p class="shop-hero__info-text"><?php echo esc_html( $refurb_text ); ?></p>
			</div>
			<?php get_template_part( 'template-parts/component-weekdeal' ); ?>
		</div>
	</section>

	<?php /* Mobile filter toggle */ ?>
	<div class="shop-filter-bar">
		<button type="button" class="shop-filter-bar__btn" aria-controls="shop-filters-drawer" aria-expanded="false">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M8 12h8M11 18h2"/></svg>
			<?php esc_html_e( 'Filteren', 'slimgebruikt' ); ?>
		</button>
	</div>

	<div class="shop-content">
		<?php /* Filter sidebar (desktop + mobile drawer) */ ?>
		<aside id="shop-filters-drawer" class="shop-filters" aria-label="<?php esc_attr_e( 'Filters', 'slimgebruikt' ); ?>">
			<div class="shop-filters__header">
				<h2 class="shop-filters__title"><?php esc_html_e( 'Filters', 'slimgebruikt' ); ?></h2>
				<button type="button" class="shop-filters__close" aria-label="<?php esc_attr_e( 'Sluiten', 'slimgebruikt' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
				</button>
			</div>
			<?php slimgebruikt_render_shop_filters( $shop_url ); ?>
		</aside>

		<div class="shop-main">
			<?php do_action( 'woocommerce_shop_loop_header' ); ?>

			<?php if ( woocommerce_product_loop() ) : ?>
				<?php do_action( 'woocommerce_before_shop_loop' ); ?>
				<div class="products-grid">
					<div class="products-grid__inner">
						<?php
						woocommerce_product_loop_start();
						if ( wc_get_loop_prop( 'total' ) ) {
							while ( have_posts() ) {
								the_post();
								do_action( 'woocommerce_shop_loop' );
								wc_get_template_part( 'content', 'product' );
							}
						}
						woocommerce_product_loop_end();
						?>
					</div>
				</div>
				<?php do_action( 'woocommerce_after_shop_loop' ); ?>
			<?php else : ?>
				<?php do_action( 'woocommerce_no_products_found' ); ?>
			<?php endif; ?>
		</div>
	</div>

	<?php /* Promo banners */ ?>
	<section class="shop-promos">
		<div class="shop-promos__inner">
			<div class="shop-promo shop-promo--dark">
				<div class="shop-promo__content">
					<h3 class="shop-promo__title"><?php esc_html_e( 'Gratis 2 cadeaus bij elke refurbished iPad!', 'slimgebruikt' ); ?></h3>
					<p class="shop-promo__value"><?php esc_html_e( 'Ter waarde van € 30,00', 'slimgebruikt' ); ?></p>
					<a href="<?php echo esc_url( $shop_url . '?product_cat=ipad' ); ?>" class="shop-promo__btn"><?php esc_html_e( 'Bekijk actie', 'slimgebruikt' ); ?></a>
				</div>
			</div>
			<div class="shop-promo shop-promo--earth">
				<p class="shop-promo__text"><?php esc_html_e( 'Koop jouw iPhone bij slimgebruikt.nl en draag bij aan de strijd tegen onnodige elektronische afval!', 'slimgebruikt' ); ?></p>
			</div>
			<div class="shop-promo shop-promo--pink">
				<p class="shop-promo__text"><?php esc_html_e( 'We zorgen dat iedereen mee kan doen: Refurbished en betaalbaar', 'slimgebruikt' ); ?></p>
			</div>
		</div>
	</section>

	<?php /* Wie slim is + Benefits */ ?>
	<section class="shop-benefits-section">
		<div class="shop-benefits__inner">
			<h2 class="shop-benefits__title"><?php esc_html_e( 'Wie slim is, betaalt minder.', 'slimgebruikt' ); ?></h2>
			<p class="shop-benefits__subtitle"><?php esc_html_e( 'Refurbished kopen betekent kwaliteit tegen een eerlijke prijs. Geen concessies aan kwaliteit – wel aan de prijs.', 'slimgebruikt' ); ?></p>
			<ul class="shop-benefits__list">
				<?php foreach ( $shop_benefits as $benefit ) : ?>
					<li class="shop-benefits__item"><?php echo esc_html( $benefit ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<?php /* FAQ */ ?>
	<section class="shop-faq-section">
		<?php
		get_template_part( 'template-parts/component-faq', null, array(
			'prefix'   => 'shop_faq_',
			'post_id'  => null,
		) );
		?>
	</section>

</div>

<?php
get_footer( 'shop' );
