<?php
/**
 * Newsletter signup – boven de footer
 *
 * @package slimgebruikt
 */
?>
<section class="newsletter">
	<div class="newsletter__inner">
		<div class="newsletter__content">
			<h2 class="newsletter__title">
				<?php
				printf(
					/* translators: %s: discount amount */
					esc_html__( 'Krijg %s korting op je eerste bestelling', 'slimgebruikt' ),
					'<span class="newsletter__highlight">€15,-</span>'
				);
				?>
			</h2>
			<p class="newsletter__subtitle">
				<?php esc_html_e( 'Bij bestellingen vanaf €250,-, wanneer je aanmeldt voor e-mails.', 'slimgebruikt' ); ?>
			</p>
			<form class="newsletter__form" action="#" method="post">
				<label for="newsletter-email" class="screen-reader-text"><?php esc_html_e( 'E-mailadres', 'slimgebruikt' ); ?></label>
				<div class="newsletter__field">
					<input type="email" id="newsletter-email" name="email" placeholder="<?php esc_attr_e( 'E-mailadres', 'slimgebruikt' ); ?>" required class="newsletter__input">
					<svg class="newsletter__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
						<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
						<polyline points="22,6 12,13 2,6"/>
					</svg>
				</div>
				<button type="submit" class="newsletter__btn"><?php esc_html_e( 'Aanmelden', 'slimgebruikt' ); ?></button>
			</form>
		</div>
		<div class="newsletter__image" aria-hidden="true">
			<!-- Placeholder: afbeelding via ACF of theme customizer -->
		</div>
	</div>
</section>
