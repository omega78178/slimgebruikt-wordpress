<?php
/**
 * FAQ component – accordion, herbruikbaar
 *
 * Gebruik: get_template_part( 'template-parts/component-faq', null, $args );
 * $args: prefix (ACF veld-prefix, default 'landing_faq_'), post_id
 *
 * ACF velden (met prefix): title, intro, more_url, items (repeater: question, answer),
 * primary_btn_text, primary_btn_url, accent_btn_text, accent_btn_url
 *
 * Op andere templates: enqueue landing.css en landing-motion.js voor styling + accordion.
 *
 * @package slimgebruikt
 */

$prefix = isset( $args['prefix'] ) ? $args['prefix'] : 'landing_faq_';
$post_id = isset( $args['post_id'] ) ? $args['post_id'] : null;

$title        = function_exists( 'get_field' ) ? get_field( $prefix . 'title', $post_id ) : null;
$intro        = function_exists( 'get_field' ) ? get_field( $prefix . 'intro', $post_id ) : null;
$more_url     = function_exists( 'get_field' ) ? get_field( $prefix . 'more_url', $post_id ) : null;
$items        = function_exists( 'get_field' ) ? get_field( $prefix . 'items', $post_id ) : null;
$primary_text = function_exists( 'get_field' ) ? get_field( $prefix . 'primary_btn_text', $post_id ) : null;
$primary_url  = function_exists( 'get_field' ) ? get_field( $prefix . 'primary_btn_url', $post_id ) : null;
$accent_text  = function_exists( 'get_field' ) ? get_field( $prefix . 'accent_btn_text', $post_id ) : null;
$accent_url   = function_exists( 'get_field' ) ? get_field( $prefix . 'accent_btn_url', $post_id ) : null;

$default_items = array(
	array( 'question' => __( 'Wat betekent refurbished?', 'slimgebruikt' ), 'answer' => __( 'Refurbished betekent dat een apparaat is hersteld, gereinigd en grondig getest. Het product voldoet aan strikte kwaliteitseisen en werkt als nieuw.', 'slimgebruikt' ) ),
	array( 'question' => __( 'Is refurbished net zo goed als nieuw?', 'slimgebruikt' ), 'answer' => __( 'Ja! Alle producten worden professioneel getest en gereinigd. Ze voldoen aan dezelfde kwaliteitseisen als nieuwe apparaten.', 'slimgebruikt' ) ),
	array( 'question' => __( 'Welke condities bieden jullie aan?', 'slimgebruikt' ), 'answer' => __( 'Wij bieden producten aan in de condities: Zo goed als nieuw, Uitstekend, Goed en Redelijk. Elke conditie is helder omschreven op de productpagina.', 'slimgebruikt' ) ),
	array( 'question' => __( 'Zit er garantie op refurbished producten?', 'slimgebruikt' ), 'answer' => __( 'Ja, op al onze refurbished producten geef je standaard 2 jaar garantie. Je kunt ook een uitgebreide garantie afsluiten.', 'slimgebruikt' ) ),
	array( 'question' => __( 'Hoe zit het met de batterij?', 'slimgebruikt' ), 'answer' => __( 'Alle batterijen worden gecontroleerd en hebben minimaal 80% capaciteit. Bij producten met een lagere capaciteit wordt de batterij vervangen.', 'slimgebruikt' ) ),
	array( 'question' => __( 'Kan ik het product retourneren?', 'slimgebruikt' ), 'answer' => __( 'Ja, je hebt 30 dagen bedenktijd. Je kunt het product zonder opgave van reden retourneren. We vergoeden het aankoopbedrag volledig.', 'slimgebruikt' ) ),
);

$items = ! empty( $items ) ? $items : $default_items;
$title = $title ?: __( 'Veel gestelde vragen', 'slimgebruikt' );
$intro = $intro ?: __( 'Nog twijfels? Dat snappen we. Daarom krijg je bij Slimgebruikt altijd garantie, bedenktijd en eerlijke prijzen.', 'slimgebruikt' );
$more_url = $more_url ?: home_url( '/faq/' );
$primary_text = $primary_text ?: __( 'Bekijk refurbished smartphones', 'slimgebruikt' );
$primary_url  = $primary_url ?: home_url( '/shop/' );
$accent_text  = $accent_text ?: __( 'Bekijk onze weekdeal', 'slimgebruikt' );
$accent_url   = $accent_url ?: home_url( '/weekdeal/' );
?>
<section class="sg-faq" aria-label="<?php echo esc_attr( $title ); ?>">
	<div class="sg-faq__inner">
		<div class="sg-faq__header">
			<h2 class="sg-section-title"><?php echo esc_html( $title ); ?></h2>
			<a href="<?php echo esc_url( $more_url ); ?>" class="sg-btn-outline">
				<?php esc_html_e( 'Bekijk alles', 'slimgebruikt' ); ?>
				<svg fill="none" height="16" viewBox="0 0 16 16" width="16"><path d="M2 14L14 2M14 2H5M14 2V11" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
			</a>
		</div>
		<p class="sg-faq__intro"><?php echo esc_html( $intro ); ?></p>
		<div class="sg-faq__grid">
			<?php foreach ( $items as $i => $item ) : ?>
				<?php $q = isset( $item['question'] ) ? $item['question'] : ( $item['q'] ?? '' ); ?>
				<?php $a = isset( $item['answer'] ) ? $item['answer'] : ( $item['a'] ?? '' ); ?>
				<?php if ( empty( $q ) ) continue; ?>
				<div class="sg-faq__item-wrap">
					<button class="sg-faq__item" aria-expanded="false" aria-controls="sg-faq-<?php echo esc_attr( (string) $i ); ?>" data-faq-toggle>
						<span class="sg-faq__question"><?php echo esc_html( $q ); ?></span>
						<span class="sg-faq__icon" aria-hidden="true">+</span>
					</button>
					<div class="sg-faq__answer-wrap" id="sg-faq-<?php echo esc_attr( (string) $i ); ?>" role="region">
						<div class="sg-faq__answer">
							<?php echo wp_kses_post( $a ); ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="sg-faq__buttons">
			<a href="<?php echo esc_url( $primary_url ); ?>" class="sg-btn-primary"><?php echo esc_html( $primary_text ); ?></a>
			<a href="<?php echo esc_url( $accent_url ); ?>" class="sg-btn-accent"><?php echo esc_html( $accent_text ); ?></a>
		</div>
	</div>
</section>
