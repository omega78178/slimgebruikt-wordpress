<?php
/**
 * Template Name: Landingspage - SlimGebruikt
 * Template Post Type: page
 *
 * Volledige landingspage gebaseerd op Figma ontwerp.
 * Gebruikt theme header en footer.
 *
 * @package slimgebruikt
 */

add_filter(
	'body_class',
	function ( $classes ) {
		$classes[] = 'landing-page';
		return $classes;
	}
);

get_header();

$hero_headline  = function_exists( 'get_field' ) ? get_field( 'landing_hero_title' ) : null;
$hero_sub       = function_exists( 'get_field' ) ? get_field( 'landing_hero_subtitle' ) : null;
$hero_image     = function_exists( 'get_field' ) ? get_field( 'landing_hero_image' ) : null;
$hero_usps      = function_exists( 'get_field' ) ? get_field( 'landing_hero_usps' ) : null;
$waarom_body    = function_exists( 'get_field' ) ? get_field( 'landing_waarom_body' ) : null;
$waarom_image   = function_exists( 'get_field' ) ? get_field( 'landing_waarom_image' ) : null;

$default_usps = array(
	array( 'title' => __( 'Garantie inbegrepen', 'slimgebruikt' ), 'text' => __( 'Je krijgt standaard 2 jaar garantie op al onze refurbished producten.', 'slimgebruikt' ), 'icon' => 'shield' ),
	array( 'title' => __( '100% gecontroleerd', 'slimgebruikt' ), 'text' => __( 'Elk product wordt professioneel getest en gecontroleerd op 30+ punten.', 'slimgebruikt' ), 'icon' => 'magnifier' ),
	array( 'title' => __( 'Snelle levering', 'slimgebruikt' ), 'text' => __( 'Wij leveren direct uit eigen voorraad.', 'slimgebruikt' ), 'icon' => 'truck' ),
	array( 'title' => __( 'Slim besparen', 'slimgebruikt' ), 'text' => __( 'Bespaar tot wel 40% ten opzichte van nieuw.', 'slimgebruikt' ), 'icon' => 'wallet' ),
);
$hero_usps = ! empty( $hero_usps ) ? $hero_usps : $default_usps;
?>

<!-- Hero – full-width achtergrond -->
<section class="sg-hero" aria-label="Hero sectie">
		<div class="sg-hero__bg-circle"></div>
		<?php if ( $hero_image ) : ?>
		<div class="sg-hero__image-wrap">
			<img class="sg-hero__image" src="<?php echo esc_url( $hero_image ); ?>" alt="" loading="eager" />
		</div>
		<?php endif; ?>
		<div class="sg-hero__content">
			<h1 class="sg-hero__headline">
				<?php echo esc_html( $hero_headline ?: 'Kies slim, kies refurbished.' ); ?>
			</h1>
			<p class="sg-hero__sub">
				<strong>Slimgebruikt</strong>
				<?php echo esc_html( $hero_sub ?: 'is dé expert in refurbished telefoons, duurzame technologie en advies voor consumenten. Van smartphones tot tablets, wij verzorgen alles van begin tot eind.' ); ?>
			</p>
			<div class="sg-hero__usps">
				<?php
				foreach ( $hero_usps as $usp ) :
					$icon    = $usp['icon'] ?? 'shield';
					$icon_svg = ! empty( $usp['icon_svg'] ) ? trim( $usp['icon_svg'] ) : '';
					$allowed_svg = array(
						'svg' => array( 'viewbox' => true, 'xmlns' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'clip-rule' => true, 'width' => true, 'height' => true ),
						'path' => array( 'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'clip-rule' => true ),
						'circle' => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true ),
						'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'fill' => true, 'stroke' => true ),
						'line' => array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true ),
						'polyline' => array( 'points' => true, 'fill' => true, 'stroke' => true ),
						'defs' => array(),
						'lineargradient' => array( 'id' => true, 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true ),
						'stop' => array( 'offset' => true, 'stop-color' => true ),
					);
				?>
				<div class="sg-usp-card">
					<div class="sg-usp-card__icon">
						<?php if ( 'custom' === $icon && $icon_svg ) : ?>
						<?php echo wp_kses( $icon_svg, $allowed_svg ); ?>
						<?php elseif ( 'custom' === $icon ) : ?>
						<svg fill="none" viewBox="0 0 35 39" xmlns="http://www.w3.org/2000/svg"><path d="M23.63 13.79C20.55 16.87 15.75 21.67 15.75 21.67L11.81 17.73" stroke="#4BC9F1" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.94"/><path d="M19.54 36.71C23.69 34.83 33.47 29.03 33.47 16.09V8.27C33.47 6.07 33.47 4.96 33.04 4.12C32.66 3.38 32.06 2.78 31.32 2.4C30.48 1.97 29.38 1.97 27.17 1.97H8.27C6.07 1.97 4.96 1.97 4.12 2.4C3.38 2.78 2.78 3.38 2.4 4.12C1.97 4.96 1.97 6.07 1.97 8.28V16.09C1.97 29.03 11.75 34.83 15.9 36.71C16.34 36.91 16.56 37.01 17.05 37.09C17.37 37.15 18.07 37.15 18.39 37.09C18.88 37.01 19.1 36.91 19.54 36.71Z" stroke="#120E17" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.94"/></svg>
						<?php elseif ( 'shield' === $icon ) : ?>
						<svg fill="none" viewBox="0 0 35 39" xmlns="http://www.w3.org/2000/svg"><path d="M23.63 13.79C20.55 16.87 15.75 21.67 15.75 21.67L11.81 17.73" stroke="#4BC9F1" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.94"/><path d="M19.54 36.71C23.69 34.83 33.47 29.03 33.47 16.09V8.27C33.47 6.07 33.47 4.96 33.04 4.12C32.66 3.38 32.06 2.78 31.32 2.4C30.48 1.97 29.38 1.97 27.17 1.97H8.27C6.07 1.97 4.96 1.97 4.12 2.4C3.38 2.78 2.78 3.38 2.4 4.12C1.97 4.96 1.97 6.07 1.97 8.28V16.09C1.97 29.03 11.75 34.83 15.9 36.71C16.34 36.91 16.56 37.01 17.05 37.09C17.37 37.15 18.07 37.15 18.39 37.09C18.88 37.01 19.1 36.91 19.54 36.71Z" stroke="#120E17" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.94"/></svg>
						<?php elseif ( 'magnifier' === $icon ) : ?>
						<svg fill="none" viewBox="0 0 35 35" xmlns="http://www.w3.org/2000/svg"><path d="M22.87 22.87L33.42 33.42M14.07 26.39C7.27 26.39 1.76 20.87 1.76 14.07C1.76 7.27 7.27 1.76 14.07 1.76C20.87 1.76 26.39 7.27 26.39 14.07C26.39 20.87 20.87 26.39 14.07 26.39Z" stroke="#120E17" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.52"/><path d="M17.07 12.08C15.59 13.56 13.29 15.87 13.29 15.87L11.4 13.98" stroke="#4BC9F1" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.89"/></svg>
						<?php elseif ( 'truck' === $icon ) : ?>
						<svg fill="none" viewBox="0 0 52 52" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="M24.69 35.76C24.65 38.11 22.76 39.97 20.47 39.94C18.18 39.91 16.35 37.99 16.36 35.64C16.38 33.29 18.24 31.4 20.53 31.4C21.64 31.41 22.71 31.87 23.49 32.69C24.27 33.51 24.7 34.61 24.69 35.76Z" stroke="#4BC9F1" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.01"/><path clip-rule="evenodd" d="M44.53 35.76C44.49 38.11 42.6 39.97 40.31 39.94C38.02 39.91 36.19 37.99 36.2 35.64C36.22 33.29 38.08 31.4 40.37 31.4C41.49 31.41 42.55 31.87 43.33 32.69C44.11 33.51 44.55 34.61 44.53 35.76Z" stroke="#4BC9F1" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.01"/><path d="M35.71 37.26H24.69V34.25H35.71V37.26Z" fill="#120E17"/></svg>
						<?php elseif ( 'wallet' === $icon ) : ?>
						<svg fill="none" viewBox="0 0 37 35" xmlns="http://www.w3.org/2000/svg"><path d="M18.48 6.47V11.09M18.48 6.47C18.48 3.92 20.55 1.85 23.1 1.85C25.66 1.85 27.73 3.92 27.73 6.47C27.73 9.02 25.66 11.09 23.1 11.09M18.48 6.47C18.48 3.92 16.41 1.85 13.86 1.85C11.31 1.85 9.24 3.92 9.24 6.47C9.24 9.02 11.31 11.09 13.86 11.09M18.48 11.09H23.1M18.48 11.09H13.86M18.48 11.09V22.18M23.1 11.09H29.2C31.28 11.09 32.31 11.09 33.1 11.49C33.8 11.85 34.36 12.41 34.72 13.11C35.12 13.9 35.12 14.93 35.12 17V22.18M13.86 11.09H7.76C5.69 11.09 4.66 11.09 3.87 11.49C3.17 11.85 2.61 12.41 2.25 13.11C1.85 13.9 1.85 14.94 1.85 17.01V22.18M1.85 22.18V27.36C1.85 29.43 1.85 30.46 2.25 31.25C2.61 31.95 3.17 32.51 3.87 32.87C4.66 33.27 5.69 33.27 7.76 33.27H18.48M1.85 22.18H18.48M18.48 22.18V33.27M18.48 22.18H35.12M18.48 33.27H29.21C31.28 33.27 32.31 33.27 33.1 32.87C33.8 32.51 34.36 31.95 34.72 31.25C35.12 30.46 35.12 29.43 35.12 27.36V22.18" stroke="#120E17" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.7"/></svg>
						<?php else : ?>
						<svg fill="none" viewBox="0 0 37 35" xmlns="http://www.w3.org/2000/svg"><path d="M18.48 6.47V11.09M18.48 6.47C18.48 3.92 20.55 1.85 23.1 1.85C25.66 1.85 27.73 3.92 27.73 6.47C27.73 9.02 25.66 11.09 23.1 11.09M18.48 6.47C18.48 3.92 16.41 1.85 13.86 1.85C11.31 1.85 9.24 3.92 9.24 6.47C9.24 9.02 11.31 11.09 13.86 11.09M18.48 11.09H23.1M18.48 11.09H13.86M18.48 11.09V22.18M23.1 11.09H29.2C31.28 11.09 32.31 11.09 33.1 11.49C33.8 11.85 34.36 12.41 34.72 13.11C35.12 13.9 35.12 14.93 35.12 17V22.18M13.86 11.09H7.76C5.69 11.09 4.66 11.09 3.87 11.49C3.17 11.85 2.61 12.41 2.25 13.11C1.85 13.9 1.85 14.94 1.85 17.01V22.18M1.85 22.18V27.36C1.85 29.43 1.85 30.46 2.25 31.25C2.61 31.95 3.17 32.51 3.87 32.87C4.66 33.27 5.69 33.27 7.76 33.27H18.48M1.85 22.18H18.48M18.48 22.18V33.27M18.48 22.18H35.12M18.48 33.27H29.21C31.28 33.27 32.31 33.27 33.1 32.87C33.8 32.51 34.36 31.95 34.72 31.25C35.12 30.46 35.12 29.43 35.12 27.36V22.18" stroke="#120E17" stroke-linecap="round" stroke-linejoin="round" stroke-width="3.7"/></svg>
						<?php endif; ?>
					</div>
					<?php if ( ! empty( $usp['title'] ) ) : ?>
					<div class="sg-usp-card__title"><?php echo esc_html( $usp['title'] ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $usp['text'] ) ) : ?>
					<div class="sg-usp-card__text"><?php echo esc_html( $usp['text'] ); ?></div>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>
			<div class="sg-hero__buttons">
				<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="sg-btn-primary"><?php esc_html_e( 'Bekijk aanbod', 'slimgebruikt' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="sg-btn-accent"><?php esc_html_e( 'Hulp nodig?', 'slimgebruikt' ); ?></a>
			</div>
		</div>
	</section>

<main id="primary" class="site-main site-main--landing landing-main">

	<!-- Waarom SlimGebruikt -->
	<section class="sg-waarom" aria-label="<?php esc_attr_e( 'Waarom Slimgebruikt', 'slimgebruikt' ); ?>">
		<div class="sg-waarom__inner">
			<div class="sg-waarom__text-col">
				<h2 class="sg-section-title"><?php esc_html_e( 'Waarom slimgebruikt.nl', 'slimgebruikt' ); ?></h2>
				<p class="sg-waarom__body">
					<?php echo wp_kses_post( $waarom_body ?: __( 'Bij Slimgebruikt.nl koop je refurbished elektronica zonder risico. Professioneel getest, 2 jaar garantie en 30 dagen bedenktijd. Je bespaart slim, terwijl je kiest voor kwaliteit en zekerheid.', 'slimgebruikt' ) ); ?>
				</p>
			</div>
			<img class="sg-waarom__image" src="<?php echo esc_url( $waarom_image ?: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=400&h=600&fit=crop' ); ?>" alt="" loading="lazy" />
		</div>
	</section>

	<!-- Producten (home-products template part) -->
	<?php get_template_part( 'template-parts/home-products' ); ?>

	<!-- Wie slim is, betaalt minder -->
	<section class="sg-slim" aria-label="<?php esc_attr_e( 'Wie slim is, betaalt minder', 'slimgebruikt' ); ?>">
		<div class="sg-slim__inner">
			<div class="sg-slim__text-col">
				<h2 class="sg-slim__headline">
					<?php esc_html_e( 'Wie slim is,', 'slimgebruikt' ); ?><br>
					<em><?php esc_html_e( 'betaalt minder.', 'slimgebruikt' ); ?></em>
				</h2>
				<p class="sg-slim__body"><?php esc_html_e( 'Bij Slimgebruikt.nl koop je refurbished elektronica zonder risico. Je bespaart slim, terwijl je kiest voor kwaliteit en zekerheid.', 'slimgebruikt' ); ?></p>
			</div>
			<div class="sg-slim__banner">
				<div class="sg-slim__usps">
					<div class="sg-slim__usp-item"><div class="sg-slim__usp-check"><svg fill="none" viewBox="0 0 18 18"><path d="M11.68 7.76L8.08 11.78L6.29 8.98" stroke="#4BC9F1" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg></div><span><strong><?php esc_html_e( 'Professionele', 'slimgebruikt' ); ?></strong> <?php esc_html_e( 'inspectie op 25 punten', 'slimgebruikt' ); ?></span></div>
					<div class="sg-slim__usp-item"><div class="sg-slim__usp-check"><svg fill="none" viewBox="0 0 18 18"><path d="M11.68 7.76L8.08 11.78L6.29 8.98" stroke="#4BC9F1" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg></div><span><strong><?php esc_html_e( 'Gratis garantie', 'slimgebruikt' ); ?></strong> <?php esc_html_e( 'bij elke aankoop*', 'slimgebruikt' ); ?></span></div>
					<div class="sg-slim__usp-item"><div class="sg-slim__usp-check"><svg fill="none" viewBox="0 0 18 18"><path d="M11.68 7.76L8.08 11.78L6.29 8.98" stroke="#4BC9F1" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg></div><span><strong><?php esc_html_e( 'Erkende', 'slimgebruikt' ); ?></strong> <?php esc_html_e( 'refurbishers', 'slimgebruikt' ); ?></span></div>
					<div class="sg-slim__usp-item"><div class="sg-slim__usp-check"><svg fill="none" viewBox="0 0 18 18"><path d="M11.68 7.76L8.08 11.78L6.29 8.98" stroke="#4BC9F1" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg></div><span>BTW <strong><?php esc_html_e( 'zakelijk aftrekbaar', 'slimgebruikt' ); ?></strong></span></div>
					<div class="sg-slim__usp-item"><div class="sg-slim__usp-check"><svg fill="none" viewBox="0 0 18 18"><path d="M11.68 7.76L8.08 11.78L6.29 8.98" stroke="#4BC9F1" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg></div><span><strong><?php esc_html_e( 'Strikte', 'slimgebruikt' ); ?></strong> <?php esc_html_e( 'kwaliteitseisen', 'slimgebruikt' ); ?></span></div>
					<div class="sg-slim__usp-item"><div class="sg-slim__usp-check"><svg fill="none" viewBox="0 0 18 18"><path d="M11.68 7.76L8.08 11.78L6.29 8.98" stroke="#4BC9F1" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg></div><span><strong>30 <?php esc_html_e( 'dagen', 'slimgebruikt' ); ?></strong> <?php esc_html_e( 'bedenktijd', 'slimgebruikt' ); ?></span></div>
				</div>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/component-faq', null, array( 'prefix' => 'landing_faq_' ) ); ?>

</main>

<?php get_footer(); ?>
