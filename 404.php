<?php
/**
 * 404 pagina – Niet gevonden
 *
 * @package slimgebruikt
 */

get_header();
?>

<main id="primary" class="site-main">
	<section class="error-404 not-found">
		<div class="error-404__inner">
			<span class="error-404__code">404</span>
			<h1 class="error-404__title"><?php esc_html_e( 'Pagina niet gevonden', 'slimgebruikt' ); ?></h1>
			<p class="error-404__text"><?php esc_html_e( 'De pagina die je zoekt bestaat niet of is verplaatst. Gebruik de zoekfunctie of ga terug naar de homepage.', 'slimgebruikt' ); ?></p>

			<div class="error-404__actions">
				<?php get_search_form(); ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="error-404__btn"><?php esc_html_e( 'Naar homepage', 'slimgebruikt' ); ?></a>
				<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="error-404__btn error-404__btn--outline"><?php esc_html_e( 'Bekijk producten', 'slimgebruikt' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
