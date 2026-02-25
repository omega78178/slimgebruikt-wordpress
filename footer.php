<?php
/**
 * The template for displaying the footer
 *
 * @package slimgebruikt
 */

$footer_phone   = function_exists( 'get_field' ) ? get_field( 'footer_phone', 'option' ) : null;
$footer_email   = function_exists( 'get_field' ) ? get_field( 'footer_email', 'option' ) : null;
$footer_addr_1  = function_exists( 'get_field' ) ? get_field( 'footer_addr_1', 'option' ) : null;
$footer_addr_2  = function_exists( 'get_field' ) ? get_field( 'footer_addr_2', 'option' ) : null;
$footer_addr_3  = function_exists( 'get_field' ) ? get_field( 'footer_addr_3', 'option' ) : null;
$footer_hours   = function_exists( 'get_field' ) ? get_field( 'footer_hours', 'option' ) : null;
$footer_phone   = $footer_phone ?: '06 1234567';
$footer_email   = $footer_email ?: 'info@slimgebruikt.nl';
$footer_addr_1  = $footer_addr_1 ?: 'Infostraat 12';
$footer_addr_2  = $footer_addr_2 ?: '1234 AB, Hengelo';
$footer_addr_3  = $footer_addr_3 ?: 'Nederland';
$footer_hours   = $footer_hours ?: __( 'Geopend tot 17:00', 'slimgebruikt' );
?>
	<?php get_template_part( 'template-parts/newsletter' ); ?>
	<footer id="colophon" class="site-footer">
		<?php /* 1. Trust bar */ ?>
		<?php
		$footer_trust_texts = array(
			__( 'Professioneel refurbished', 'slimgebruikt' ),
			__( '2 jaar garantie*', 'slimgebruikt' ),
			__( '30 dagen bedenktijd', 'slimgebruikt' ),
			__( 'Strikte kwaliteitseisen', 'slimgebruikt' ),
		);
		?>
		<div class="footer-trust">
			<div class="footer-trust__inner">
				<div class="footer-trust__carousel" data-texts="<?php echo esc_attr( wp_json_encode( $footer_trust_texts ) ); ?>">
					<div class="footer-trust__carousel-wrap">
						<span class="footer-trust__carousel-text" aria-live="polite"><?php echo esc_html( $footer_trust_texts[0] ); ?></span>
					</div>
				</div>
				<div class="footer-trust__items">
					<?php foreach ( $footer_trust_texts as $t ) : ?>
						<span class="footer-trust__item"><?php echo esc_html( $t ); ?></span>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php /* 2. Main footer */ ?>
		<div class="footer-main">
			<div class="footer-main__inner">
				<div class="footer-brand">
					<div class="footer-brand__logo">
						<?php if ( has_custom_logo() ) : ?>
							<?php the_custom_logo(); ?>
						<?php else : ?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<span class="footer-brand__icon" aria-hidden="true">
									<svg width="32" height="32" viewBox="0 0 32 32" fill="none"><path d="M10 8h12v16H10V8z" stroke="currentColor" stroke-width="1.5"/><path d="M12 12h8M12 16h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
								</span>
								<span class="footer-brand__text"><span class="footer-brand__text--slim">Slim</span><span class="footer-brand__text--gebruikt">Gebruikt</span></span>
							</a>
						<?php endif; ?>
					</div>
					<div class="footer-contact">
						<a href="tel:<?php echo esc_attr( preg_replace( '/\s/', '', $footer_phone ) ); ?>" class="footer-contact__item">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
							<?php echo esc_html( $footer_phone ); ?>
						</a>
						<a href="mailto:<?php echo esc_attr( $footer_email ); ?>" class="footer-contact__item">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
							<?php echo esc_html( $footer_email ); ?>
						</a>
					</div>
					<address class="footer-address">
						<?php echo esc_html( $footer_addr_1 ); ?><br>
						<?php echo esc_html( $footer_addr_2 ); ?><br>
						<?php echo esc_html( $footer_addr_3 ); ?>
						<span class="footer-address__note"><?php esc_html_e( '(Geen bezoekadres)', 'slimgebruikt' ); ?></span>
					</address>
					<div class="footer-hours">
						<span class="footer-hours__dot"></span>
						<?php echo esc_html( $footer_hours ); ?>
					</div>
				</div>

				<nav class="footer-nav" aria-label="<?php esc_attr_e( 'Footer', 'slimgebruikt' ); ?>">
					<div class="footer-nav__col">
						<h3 class="footer-nav__title"><?php esc_html_e( 'Informatie', 'slimgebruikt' ); ?></h3>
						<?php
						if ( has_nav_menu( 'footer-info' ) ) {
							wp_nav_menu( array( 'theme_location' => 'footer-info', 'menu_class' => 'footer-nav__menu', 'container' => false ) );
						} else {
							echo '<ul class="footer-nav__menu">';
							foreach ( array( 'Contact', 'Blog', 'Over ons', 'Productcondities', 'Beoordelingen' ) as $item ) {
								echo '<li><a href="#">' . esc_html( $item ) . '</a></li>';
							}
							echo '</ul>';
						}
						?>
					</div>
					<div class="footer-nav__col">
						<h3 class="footer-nav__title"><?php esc_html_e( 'Snelle links', 'slimgebruikt' ); ?></h3>
						<?php
						if ( has_nav_menu( 'footer-links' ) ) {
							wp_nav_menu( array( 'theme_location' => 'footer-links', 'menu_class' => 'footer-nav__menu', 'container' => false ) );
						} else {
							echo '<ul class="footer-nav__menu">';
							foreach ( array( 'Garantievoorwaarden', 'Algemene voorwaarden', 'Privacy policy', 'Verzenden & retourneren' ) as $item ) {
								echo '<li><a href="#">' . esc_html( $item ) . '</a></li>';
							}
							echo '</ul>';
						}
						?>
					</div>
					<div class="footer-nav__col footer-nav__col--products">
						<h3 class="footer-nav__title"><?php esc_html_e( 'Refurbished producten', 'slimgebruikt' ); ?></h3>
						<?php
						if ( has_nav_menu( 'footer-products' ) ) {
							wp_nav_menu( array( 'theme_location' => 'footer-products', 'menu_class' => 'footer-nav__menu footer-nav__menu--cols', 'container' => false ) );
						} else {
							echo '<ul class="footer-nav__menu footer-nav__menu--cols">';
							foreach ( array( 'Refurbished iPhone', 'Refurbished iPad', 'iPhone 16 Pro Max', 'iPhone 16 Pro', 'iPhone 16', 'iPhone 15 Pro Max', 'iPhone 15 Pro', 'iPhone 15', 'iPhone 14 Pro Max', 'iPhone 14' ) as $item ) {
								echo '<li><a href="#">' . esc_html( $item ) . '</a></li>';
							}
							echo '</ul>';
						}
						?>
					</div>
				</nav>

				<div class="footer-badges">
					<div class="footer-badge" title="<?php esc_attr_e( 'Webshop Trustmark', 'slimgebruikt' ); ?>">
						<span class="footer-badge__trustmark"><?php esc_html_e( 'WEBSHOP TRUSTMARK', 'slimgebruikt' ); ?></span>
						<span class="footer-badge__keurmerk"><?php esc_html_e( 'WEBSHOP KEURMERK', 'slimgebruikt' ); ?></span>
					</div>
				</div>
			</div>
		</div>

		<?php /* 3. Bottom bar */ ?>
		<div class="footer-bottom">
			<div class="footer-bottom__inner">
				<p class="footer-bottom__copy">
					© <?php echo esc_html( date( 'Y' ) ); ?> slimgebruikt.nl | <?php esc_html_e( 'Alle rechten voorbehouden. Alle genoemde prijzen zijn inclusief btw.', 'slimgebruikt' ); ?>
				</p>
				<p class="footer-bottom__credit">
					<?php esc_html_e( 'Website door', 'slimgebruikt' ); ?>
					<a href="https://linqd.digital" target="_blank" rel="noopener noreferrer" class="footer-bottom__linqd" aria-label="LINQD">
						<svg class="footer-bottom__logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 651 177" fill="none" aria-hidden="true"><circle cx="112.186" cy="51.8169" r="51.8169" fill="#FF6D6D"></circle><circle cx="60.3692" cy="103.634" r="60.3692" fill="url(#paint0_linear_linqd)"></circle><path d="M235.674 140C219.798 140 207 127.202 207 111.326V42.8H220.77V111.326C220.77 119.264 227.25 125.906 235.026 126.23H274.23V140H249.444H235.674ZM296.068 42.8H309.838V140H296.068V42.8ZM345.592 140H331.822V85.568C331.822 62.078 350.938 42.8 374.428 42.8C397.918 42.8 417.196 62.078 417.196 85.568V140H403.264V85.568C403.264 69.692 390.304 56.732 374.428 56.732C358.552 56.732 345.592 69.692 345.592 85.568V140ZM526.402 140H510.202L500.32 123.8L495.622 115.7C494.326 113.594 492.22 112.46 489.628 112.46H487.522V98.528H489.628C497.08 98.528 503.722 102.254 507.61 108.572L511.984 116.186C518.302 109.868 522.352 101.12 522.352 91.4C522.352 72.284 506.8 56.732 487.522 56.732C468.406 56.732 452.854 72.284 452.854 91.4C452.854 110.678 468.406 126.23 487.522 126.23V128.498V140C460.792 140 439.084 118.292 439.084 91.4C439.084 64.67 460.792 42.8 487.522 42.8C514.414 42.8 536.122 64.67 536.122 91.4C536.122 106.142 529.642 119.426 519.274 128.336L526.402 140ZM576.841 56.732H558.211V42.8H576.841C603.571 42.8 625.441 64.832 625.441 91.4C625.441 118.13 603.571 140 576.841 140H558.211V126.23H576.841C595.795 126.23 611.509 110.516 611.509 91.4C611.509 72.446 595.795 56.732 576.841 56.732Z" fill="#151515"></path><path d="M561.283 163.917V153.417H566.458C567.628 153.417 568.658 153.632 569.548 154.062C570.438 154.482 571.133 155.082 571.633 155.862C572.133 156.642 572.383 157.572 572.383 158.652C572.383 159.742 572.133 160.682 571.633 161.472C571.133 162.252 570.438 162.857 569.548 163.287C568.658 163.707 567.628 163.917 566.458 163.917H561.283ZM564.823 161.157H566.308C566.808 161.157 567.243 161.062 567.613 160.872C567.993 160.682 568.288 160.402 568.498 160.032C568.708 159.652 568.813 159.192 568.813 158.652C568.813 158.122 568.708 157.672 568.498 157.302C568.288 156.932 567.993 156.652 567.613 156.462C567.243 156.272 566.808 156.177 566.308 156.177H564.823V161.157ZM573.675 163.917V153.417H577.215V163.917H573.675ZM584.364 164.157C583.514 164.157 582.729 164.027 582.009 163.767C581.299 163.497 580.679 163.117 580.149 162.627C579.629 162.137 579.224 161.557 578.934 160.887C578.644 160.217 578.499 159.477 578.499 158.667C578.499 157.857 578.644 157.117 578.934 156.447C579.224 155.777 579.634 155.197 580.164 154.707C580.694 154.217 581.319 153.842 582.039 153.582C582.769 153.312 583.569 153.177 584.439 153.177C585.479 153.177 586.404 153.352 587.214 153.702C588.034 154.052 588.709 154.552 589.239 155.202L586.974 157.197C586.644 156.817 586.284 156.532 585.894 156.342C585.514 156.142 585.089 156.042 584.619 156.042C584.229 156.042 583.874 156.102 583.554 156.222C583.244 156.342 582.979 156.517 582.759 156.747C582.539 156.977 582.369 157.252 582.249 157.572C582.129 157.892 582.069 158.257 582.069 158.667C582.069 159.057 582.129 159.417 582.249 159.747C582.369 160.067 582.539 160.342 582.759 160.572C582.979 160.802 583.239 160.982 583.539 161.112C583.849 161.232 584.194 161.292 584.574 161.292C584.974 161.292 585.364 161.227 585.744 161.097C586.124 160.957 586.529 160.722 586.959 160.392L588.939 162.822C588.299 163.252 587.569 163.582 586.749 163.812C585.929 164.042 585.134 164.157 584.364 164.157ZM585.864 162.372V158.382H588.939V162.822L585.864 162.372ZM590.448 163.917V153.417H593.988V163.917H590.448ZM597.972 163.917V156.162H594.897V153.417H604.587V156.162H601.512V163.917H597.972ZM603.839 163.917L608.429 153.417H611.909L616.499 163.917H612.839L609.449 155.097H610.829L607.439 163.917H603.839ZM606.569 162.087L607.469 159.537H612.299L613.199 162.087H606.569ZM617.064 163.917V153.417H620.604V161.172H625.344V163.917H617.064Z" fill="#151515"></path><defs><linearGradient id="paint0_linear_linqd" x1="86.5292" y1="49.3016" x2="22.1354" y2="151.426" gradientUnits="userSpaceOnUse"><stop stop-color="#8BF8FF"></stop><stop offset="1" stop-color="#FF6262"></stop></linearGradient></defs></svg>
					</a>
				</p>
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
