<?php
/**
 * Header – SlimGebruikt design
 *
 * @package slimgebruikt
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'slimgebruikt' ); ?></a>

	<header id="masthead" class="site-header">
		<?php /* 1. Promo bar – roterende teksten */ ?>
		<?php
		$promo_items = function_exists( 'get_field' ) ? get_field( 'promo_texts', 'option' ) : null;
		$promo_texts = array();
		if ( is_array( $promo_items ) && ! empty( $promo_items ) ) {
			foreach ( $promo_items as $row ) {
				if ( ! empty( $row['text'] ) ) {
					$promo_texts[] = $row['text'];
				}
			}
		}
		if ( empty( $promo_texts ) ) {
			$promo_texts = array(
				'2 jaar garantie op iPhones en iPads. MacBooks.',
				'2 jaar garantie op batterij van iPhones/iPads.',
				'Voor 12 uur besteld vandaag verzonden.',
				'Gratis verzending in Nederland & België.',
				'30 dagen bedenktijd.',
				'Gratis retourzegel.',
			);
		}
		?>
		<div class="header-promo">
			<script type="application/json" id="promo-texts"><?php echo wp_json_encode( $promo_texts ); ?></script>
			<!-- <button class="header-promo__prev" type="button" aria-label="<?php esc_attr_e( 'Previous', 'slimgebruikt' ); ?>">&lt;</button> -->
			<div class="header-promo__text-wrap">
				<span class="header-promo__text" aria-live="polite"><?php echo esc_html( $promo_texts[0] ); ?></span>
			</div>
			<!-- <button class="header-promo__next" type="button" aria-label="<?php esc_attr_e( 'Next', 'slimgebruikt' ); ?>">&gt;</button> -->
		</div>

		<?php
		$help_page = get_page_by_path( 'contact' );
		$help_url  = $help_page ? get_permalink( $help_page ) : home_url( '/contact/' );
		?>

		<?php /* 2a. Desktop header */ ?>
		<div class="header-desktop">
			<div class="header-main">
				<div class="site-logo">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<span class="site-logo__icon" aria-hidden="true">
								<svg width="32" height="32" viewBox="0 0 32 32" fill="none"><path d="M10 8h12v16H10V8z" stroke="currentColor" stroke-width="1.5"/><path d="M12 12h8M12 16h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
							</span>
							<span class="site-logo__text"><span class="site-logo__text--slim">Slim</span><span class="site-logo__text--gebruikt">Gebruikt</span></span>
						</a>
					<?php endif; ?>
				</div>
				<div class="header-search">
					<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<label class="screen-reader-text" for="header-search-input"><?php esc_html_e( 'Search', 'slimgebruikt' ); ?></label>
						<span class="header-search__icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg></span>
						<input type="search" id="header-search-input" name="s" placeholder="<?php esc_attr_e( 'Waar ben je naar op zoek?', 'slimgebruikt' ); ?>" value="<?php echo get_search_query(); ?>">
					</form>
				</div>
				<div class="header-actions">
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="header-actions__account" title="<?php esc_attr_e( 'Mijn account', 'slimgebruikt' ); ?>" aria-label="<?php esc_attr_e( 'Account', 'slimgebruikt' ); ?>">
						<svg width="23" height="28" viewBox="0 0 23 28" fill="none"><path d="M20.8333 26.3889C20.8333 21.0194 16.4805 16.6667 11.1111 16.6667C5.74167 16.6667 1.38889 21.0194 1.38889 26.3889M11.1111 12.5C8.04286 12.5 5.55555 10.0127 5.55555 6.94444C5.55555 3.87619 8.04286 1.38889 11.1111 1.38889C14.1794 1.38889 16.6667 3.87619 16.6667 6.94444C16.6667 10.0127 14.1794 12.5 11.1111 12.5Z" stroke="#120E17" stroke-width="2.77778" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</a>
						<?php if ( function_exists( 'slimgebruikt_woocommerce_header_cart' ) ) : ?>
							<?php slimgebruikt_woocommerce_header_cart(); ?>
						<?php endif; ?>
					<?php endif; ?>
					<a href="<?php echo esc_url( $help_url ); ?>" class="header-actions__help"><?php esc_html_e( 'Hulp nodig?', 'slimgebruikt' ); ?></a>
				</div>
			</div>
			<nav class="header-nav" aria-label="<?php esc_attr_e( 'Primary', 'slimgebruikt' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu-1',
						'menu_id'        => 'primary-menu',
						'menu_class'     => 'header-nav__menu',
						'container'     => false,
					)
				);
				?>
			</nav>
		</div>

		<?php /* 2b. Mobile header */ ?>
		<div class="header-mobile">
			<div class="header-main header-main--mobile">
				<button class="header-nav-toggle" type="button" aria-label="<?php esc_attr_e( 'Menu openen', 'slimgebruikt' ); ?>" aria-expanded="false" aria-controls="mobile-nav-panel">
					<span class="header-nav-toggle__line"></span>
					<span class="header-nav-toggle__line"></span>
					<span class="header-nav-toggle__line"></span>
				</button>
				<div class="site-logo">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<span class="site-logo__icon" aria-hidden="true">
								<svg width="32" height="32" viewBox="0 0 32 32" fill="none"><path d="M10 8h12v16H10V8z" stroke="currentColor" stroke-width="1.5"/><path d="M12 12h8M12 16h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
							</span>
							<span class="site-logo__text"><span class="site-logo__text--slim">Slim</span><span class="site-logo__text--gebruikt">Gebruikt</span></span>
						</a>
					<?php endif; ?>
				</div>
				<div class="header-actions">
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="header-actions__account" title="<?php esc_attr_e( 'Mijn account', 'slimgebruikt' ); ?>" aria-label="<?php esc_attr_e( 'Account', 'slimgebruikt' ); ?>">
						<svg width="23" height="28" viewBox="0 0 23 28" fill="none"><path d="M20.8333 26.3889C20.8333 21.0194 16.4805 16.6667 11.1111 16.6667C5.74167 16.6667 1.38889 21.0194 1.38889 26.3889M11.1111 12.5C8.04286 12.5 5.55555 10.0127 5.55555 6.94444C5.55555 3.87619 8.04286 1.38889 11.1111 1.38889C14.1794 1.38889 16.6667 3.87619 16.6667 6.94444C16.6667 10.0127 14.1794 12.5 11.1111 12.5Z" stroke="#120E17" stroke-width="2.77778" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</a>
						<?php if ( function_exists( 'slimgebruikt_woocommerce_header_cart' ) ) : ?>
							<?php slimgebruikt_woocommerce_header_cart(); ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
			<div class="header-mobile__row2">
				<div class="header-search">
					<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<label class="screen-reader-text" for="header-search-input-mobile"><?php esc_html_e( 'Search', 'slimgebruikt' ); ?></label>
						<span class="header-search__icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg></span>
						<input type="search" id="header-search-input-mobile" name="s" placeholder="<?php esc_attr_e( 'Waar ben je naar op zoek?', 'slimgebruikt' ); ?>" value="<?php echo get_search_query(); ?>">
					</form>
				</div>
				<a href="<?php echo esc_url( $help_url ); ?>" class="header-actions__help"><?php esc_html_e( 'Hulp nodig?', 'slimgebruikt' ); ?></a>
			</div>
		</div>

		<?php /* Mobile nav popup */ ?>
		<div id="mobile-nav-panel" class="mobile-nav-panel" aria-hidden="true">
			<div class="mobile-nav-panel__backdrop"></div>
			<div class="mobile-nav-panel__drawer">
				<button class="mobile-nav-panel__close" type="button" aria-label="<?php esc_attr_e( 'Menu sluiten', 'slimgebruikt' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
				</button>
				<nav class="mobile-nav-panel__menu" aria-label="<?php esc_attr_e( 'Hoofdmenu', 'slimgebruikt' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menu-1',
							'menu_id'        => 'mobile-menu',
							'menu_class'     => 'mobile-nav__menu',
							'container'     => false,
						)
					);
					?>
				</nav>
				<div class="mobile-nav-panel__actions">
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="mobile-nav__link"><?php esc_html_e( 'Mijn account', 'slimgebruikt' ); ?></a>
						<?php if ( function_exists( 'slimgebruikt_woocommerce_header_cart' ) ) : ?>
							<div class="mobile-nav__cart"><?php slimgebruikt_woocommerce_header_cart(); ?></div>
						<?php endif; ?>
					<?php endif; ?>
					<?php
					$help_page = get_page_by_path( 'contact' );
					$help_url  = $help_page ? get_permalink( $help_page ) : home_url( '/contact/' );
					?>
					<a href="<?php echo esc_url( $help_url ); ?>" class="mobile-nav__link mobile-nav__link--help"><?php esc_html_e( 'Hulp nodig?', 'slimgebruikt' ); ?></a>
				</div>
			</div>
		</div>
	</header><!-- #masthead -->
