<?php
/**
 * Template Name: Hulp & Contact
 * Toont help-categorieën uit ACF opties (Hulp & Contact paneel)
 *
 * @package slimgebruikt
 */

get_header();

$help_pages       = get_pages( array( 'meta_key' => '_wp_page_template', 'meta_value' => 'page-hulp.php' ) );
$help_url         = ! empty( $help_pages ) ? get_permalink( $help_pages[0] ) : home_url( '/' );
$hero_title       = get_field( 'help_hero_title', 'option' ) ?: __( 'Hulp nodig? Wij zijn er voor je.', 'slimgebruikt' );
$hero_subtitle    = get_field( 'help_hero_subtitle', 'option' ) ?: __( 'Heb je een vraag over een product, bestelling of garantie? Ons team helpt je graag verder.', 'slimgebruikt' );
$categories       = get_field( 'help_categories', 'option' );
$faq_title        = get_field( 'help_faq_title', 'option' ) ?: __( 'Veelgestelde vragen', 'slimgebruikt' );
$faq_intro        = get_field( 'help_faq_intro', 'option' ) ?: __( 'Heb je twijfels? Geen probleem. Daarom krijg je bij SlimGebruikt altijd garantie, bedenktijd en eerlijke prijzen.', 'slimgebruikt' );
$faq_items        = get_field( 'help_faq_items', 'option' );
$faq_more_url     = get_field( 'help_faq_more_url', 'option' ) ?: $help_url . '#help-faq';
$faq_btn_primary  = get_field( 'help_faq_btn_primary', 'option' ) ?: __( 'Bekijk refurbished smartphones', 'slimgebruikt' );
$faq_btn_primary_url = get_field( 'help_faq_btn_primary_url', 'option' ) ?: ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) );
$faq_btn_accent   = get_field( 'help_faq_btn_accent', 'option' ) ?: __( 'Bekijk onze webwinkel', 'slimgebruikt' );
$faq_btn_accent_url = get_field( 'help_faq_btn_accent_url', 'option' ) ?: ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) );
$contact_title    = get_field( 'help_contact_title', 'option' ) ?: __( 'Nog hulp nodig?', 'slimgebruikt' );
$contact_text     = get_field( 'help_contact_text', 'option' );
$contact_email    = get_field( 'help_contact_email', 'option' ) ?: 'support@slimgebruikt.nl';
$contact_phone    = get_field( 'help_contact_phone', 'option' ) ?: '023 - 3487 89';
$contact_hours    = get_field( 'help_contact_hours', 'option' ) ?: __( 'Werkdagen 9:00 - 17:00', 'slimgebruikt' );
$orders_url       = do_shortcode( '[help_orders_link]' );
$faq_more_url     = get_field( 'help_faq_more_url', 'option' ) ?: $help_url . '#help-faq';
$myaccount_url    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );
?>

	<main id="primary" class="site-main site-main--hulp">
		<section class="help-hero">
			<h1 class="help-hero__title"><?php echo esc_html( $hero_title ); ?></h1>
			<?php if ( $hero_subtitle ) : ?>
				<p class="help-hero__subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
			<?php endif; ?>
			<div class="help-service-cards">
				<div class="help-service-card">
					<div class="help-service-card__icon" aria-hidden="true">
						<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
					</div>
					<h3 class="help-service-card__title"><?php esc_html_e( 'Klantenservice', 'slimgebruikt' ); ?></h3>
					<div class="help-service-card__content">
						<p><?php printf( esc_html__( 'Stuur ons een e-mail via %s', 'slimgebruikt' ), '<a href="mailto:' . esc_attr( $contact_email ) . '">' . esc_html( $contact_email ) . '</a>' ); ?></p>
						<?php if ( $contact_phone ) : ?>
							<p><?php printf( esc_html__( 'Telefonische ondersteuning %s %s', 'slimgebruikt' ), '<span>' . esc_html( $contact_hours ) . '</span>', '<a href="tel:' . esc_attr( preg_replace( '/\s+/', '', $contact_phone ) ) . '">' . esc_html( $contact_phone ) . '</a>' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<div class="help-service-card">
					<div class="help-service-card__icon" aria-hidden="true">
						<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
					</div>
					<h3 class="help-service-card__title"><?php esc_html_e( 'Mijn pagina\'s', 'slimgebruikt' ); ?></h3>
					<p class="help-service-card__content"><?php esc_html_e( 'Beheer je bestelling, eigen betalingen, retour aanvragen en meer.', 'slimgebruikt' ); ?></p>
					<a href="<?php echo esc_url( $myaccount_url ); ?>" class="help-service-card__btn"><?php esc_html_e( 'Inloggen', 'slimgebruikt' ); ?></a>
				</div>
				<div class="help-service-card">
					<div class="help-service-card__icon" aria-hidden="true">
						<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12v7h7"/><path d="M21 12V5h-7"/><path d="M3 12l4-4 4 4 4-4"/><path d="M21 12l-4 4-4-4-4 4"/></svg>
					</div>
					<h3 class="help-service-card__title"><?php esc_html_e( 'Retourneren', 'slimgebruikt' ); ?></h3>
					<p class="help-service-card__content"><?php esc_html_e( 'Voor de gemoedsrust bieden wij gratis retouren en een 30-dagen geld terug garantie.', 'slimgebruikt' ); ?></p>
					<a href="<?php echo esc_url( $orders_url ); ?>" class="help-service-card__btn"><?php esc_html_e( 'Retour aanmelden', 'slimgebruikt' ); ?></a>
				</div>
			</div>
		</section>

		<?php if ( ! empty( $faq_items ) && is_array( $faq_items ) ) : ?>
			<section class="help-faq sg-faq-section" id="help-faq">
				<div class="sg-faq">
					<div class="sg-faq__inner">
						<div class="sg-faq__header">
							<h2 class="sg-section-title"><?php echo esc_html( $faq_title ); ?></h2>
							<a href="<?php echo esc_url( $faq_more_url ); ?>#help-faq" class="sg-btn-outline">
								<?php esc_html_e( 'Bekijk alles', 'slimgebruikt' ); ?>
								<svg fill="none" height="16" viewBox="0 0 16 16" width="16"><path d="M2 14L14 2M14 2H5M14 2V11" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
							</a>
						</div>
						<?php if ( $faq_intro ) : ?>
							<p class="sg-faq__intro"><?php echo esc_html( $faq_intro ); ?></p>
						<?php endif; ?>
						<div class="sg-faq__grid">
							<?php foreach ( $faq_items as $i => $item ) :
								$q = $item['question'] ?? '';
								$a = $item['answer'] ?? '';
								if ( empty( $q ) ) continue;
							?>
								<div class="sg-faq__item-wrap">
									<button class="sg-faq__item" aria-expanded="false" aria-controls="help-faq-<?php echo esc_attr( (string) $i ); ?>" data-faq-toggle>
										<span class="sg-faq__question"><?php echo esc_html( $q ); ?></span>
										<span class="sg-faq__icon" aria-hidden="true">+</span>
									</button>
									<div class="sg-faq__answer-wrap" id="help-faq-<?php echo esc_attr( (string) $i ); ?>" role="region">
										<div class="sg-faq__answer"><?php echo wp_kses_post( $a ); ?></div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="sg-faq__buttons">
							<a href="<?php echo esc_url( $faq_btn_primary_url ); ?>" class="sg-btn-primary"><?php echo esc_html( $faq_btn_primary ); ?></a>
							<a href="<?php echo esc_url( $faq_btn_accent_url ); ?>" class="sg-btn-accent"><?php echo esc_html( $faq_btn_accent ); ?></a>
						</div>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( ! empty( $categories ) && is_array( $categories ) ) : ?>
			<section class="help-categories">
				<h2 class="help-categories__title"><?php esc_html_e( 'Blader door artikelen per onderwerp', 'slimgebruikt' ); ?></h2>
				<div class="help-categories__grid">
					<?php foreach ( $categories as $cat ) :
						$article = $cat['article'] ?? null;
						$title   = $article ? get_the_title( $article ) : ( $cat['title'] ?? '' );
						$link    = $article ? get_permalink( $article ) : '';
						if ( ! $title ) continue;
						$initial = mb_strtoupper( mb_substr( $title, 0, 1 ) );
						if ( $link ) : ?>
						<a href="<?php echo esc_url( $link ); ?>" class="help-card">
						<?php else : ?>
						<div class="help-card help-card--no-link">
						<?php endif; ?>
							<span class="help-card__icon" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
							<h3 class="help-card__title"><?php echo esc_html( $title ); ?></h3>
						<?php echo $link ? '</a>' : '</div>'; ?>
					<?php endforeach; ?>
				</div>
				<div class="help-info-cards">
					<div class="help-info-card">
						<svg class="help-info-card__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
						<span><?php esc_html_e( 'Krijg antwoord binnen 24 uur', 'slimgebruikt' ); ?></span>
					</div>
					<div class="help-info-card">
						<svg class="help-info-card__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
						<span><?php esc_html_e( 'Ontvang een e-mail update', 'slimgebruikt' ); ?></span>
					</div>
					<div class="help-info-card">
						<svg class="help-info-card__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
						<span><?php esc_html_e( 'Krijg vriendelijke hulp van echte mensen', 'slimgebruikt' ); ?></span>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $contact_title || $contact_text ) : ?>
			<section class="help-contact">
				<?php if ( $contact_title ) : ?>
					<h2 class="help-contact__title"><?php echo esc_html( $contact_title ); ?></h2>
				<?php endif; ?>
				<?php if ( $contact_text ) : ?>
					<div class="help-contact__text">
						<?php echo wp_kses_post( do_shortcode( $contact_text ) ); ?>
					</div>
				<?php endif; ?>
			</section>
		<?php endif; ?>
	</main>

<?php
get_footer();
