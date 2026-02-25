<?php
/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package slimgebruikt
 */

/**
 * WooCommerce setup function.
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)
 * @link https://github.com/woocommerce/woocommerce/wiki/Declaring-WooCommerce-support-in-themes
 *
 * @return void
 */
function slimgebruikt_woocommerce_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 150,
			'single_image_width'    => 300,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 1,
				'max_columns'     => 6,
			),
		)
	);
	// Geen zoom op hover – alleen lightbox bij klik
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'slimgebruikt_woocommerce_setup' );

/**
 * Parse swatch colors from theme customizer (slug=hex per line).
 *
 * @return array<string, string> Slug => hex
 */
function slimgebruikt_get_swatch_colors() {
	$raw   = get_theme_mod( 'slimgebruikt_swatch_colors', '' );
	$lines = array_filter( array_map( 'trim', explode( "\n", (string) $raw ) ) );
	$out   = array();
	foreach ( $lines as $line ) {
		if ( preg_match( '/^([a-z0-9_-]+)\s*=\s*#([0-9a-fA-F]{3,6})$/', $line, $m ) ) {
			$out[ $m[1] ] = '#' . $m[2];
		}
	}
	return $out;
}

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function slimgebruikt_woocommerce_scripts() {
	$wc_style_file = get_template_directory() . '/woocommerce.css';
	$wc_version    = file_exists( $wc_style_file ) ? filemtime( $wc_style_file ) : _S_VERSION;
	wp_enqueue_style( 'slimgebruikt-woocommerce-style', get_template_directory_uri() . '/woocommerce.css', array(), $wc_version );

	$swatch_bg      = get_theme_mod( 'slimgebruikt_swatch_bg', '#120E17' );
	$swatch_selected = get_theme_mod( 'slimgebruikt_swatch_selected', '#4BC9F1' );
	$swatch_size    = (int) get_theme_mod( 'slimgebruikt_swatch_size', 44 );
	$swatch_size    = max( 32, min( 64, $swatch_size ) );
	$swatch_css     = '.product-config__buttons--color { --swatch-bg: ' . esc_attr( $swatch_bg ) . '; --swatch-selected: ' . esc_attr( $swatch_selected ) . '; --swatch-size: ' . $swatch_size . 'px; }';
	wp_add_inline_style( 'slimgebruikt-woocommerce-style', $swatch_css );

	$font_path   = WC()->plugin_url() . '/assets/fonts/';
	$inline_font = '@font-face {
			font-family: "star";
			src: url("' . $font_path . 'star.eot");
			src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
				url("' . $font_path . 'star.woff") format("woff"),
				url("' . $font_path . 'star.ttf") format("truetype"),
				url("' . $font_path . 'star.svg#star") format("svg");
			font-weight: normal;
			font-style: normal;
		}';

	wp_add_inline_style( 'slimgebruikt-woocommerce-style', $inline_font );

	if ( is_product() ) {
		wp_enqueue_script( 'wc-add-to-cart-variation' ); // Zorg dat variatie-script laadt.
		wp_enqueue_script(
			'slimgebruikt-product-config',
			get_template_directory_uri() . '/js/product-config.js',
			array( 'jquery', 'wc-add-to-cart-variation' ),
			_S_VERSION,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'slimgebruikt_woocommerce_scripts' );

/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function slimgebruikt_woocommerce_active_body_class( $classes ) {
	$classes[] = 'woocommerce-active';

	if ( is_shop() || is_cart() || is_checkout() ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'slimgebruikt_woocommerce_active_body_class' );

/**
 * Related Products Args.
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function slimgebruikt_woocommerce_related_products_args( $args ) {
	$defaults = array(
		'posts_per_page' => 4,
		'columns'        => 4,
	);

	$args = wp_parse_args( $defaults, $args );

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'slimgebruikt_woocommerce_related_products_args' );

/**
 * Vervang variatie-dropdowns door knoppen.
 */
function slimgebruikt_variation_attribute_options_html( $html, $args ) {
	$options   = $args['options'];
	$product   = $args['product'];
	$attribute = $args['attribute'];
	$selected  = $args['selected'];
	$name      = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$id        = $args['id'] ? $args['id'] : sanitize_title( $attribute );
	$is_color  = ( false !== strpos( $attribute, 'kleur' ) || false !== strpos( $attribute, 'color' ) );

	if ( empty( $options ) && $product && $attribute ) {
		$attributes = $product->get_variation_attributes();
		$options    = $attributes[ $attribute ] ?? array();
	}
	if ( empty( $options ) ) {
		return $html;
	}

	$req   = ! empty( $args['required'] ) ? ' required' : '';
	$out   = '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" class="product-config__select" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" style="display:none"' . $req . '>';
	$out .= '<option value="">' . esc_html__( 'Choose an option', 'woocommerce' ) . '</option>';

	if ( $product && taxonomy_exists( $attribute ) ) {
		$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
		foreach ( $terms as $term ) {
			if ( in_array( $term->slug, (array) $options, true ) ) {
				$sel    = selected( sanitize_title( (string) $selected ), $term->slug, false );
				$out   .= '<option value="' . esc_attr( $term->slug ) . '" ' . $sel . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product ) ) . '</option>';
			}
		}
	} else {
		foreach ( (array) $options as $option ) {
			$sel  = sanitize_title( (string) $selected ) === (string) $selected ? selected( $selected, sanitize_title( $option ), false ) : selected( $selected, $option, false );
			$out .= '<option value="' . esc_attr( $option ) . '" ' . $sel . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product ) ) . '</option>';
		}
	}
	$out .= '</select>';

	$out .= '<div class="product-config__buttons' . ( $is_color ? ' product-config__buttons--color' : '' ) . '" data-select-id="' . esc_attr( $id ) . '">';

	if ( $product && taxonomy_exists( $attribute ) ) {
		$theme_colors = slimgebruikt_get_swatch_colors();
		foreach ( $terms as $term ) {
			if ( ! in_array( $term->slug, (array) $options, true ) ) {
				continue;
			}
			$hex = isset( $theme_colors[ $term->slug ] ) ? $theme_colors[ $term->slug ] : get_term_meta( $term->term_id, 'product_attribute_color', true );
			$cls = $is_color ? 'product-config__btn--swatch' : 'product-config__btn';
			if ( $hex ) {
				$cls .= ' product-config__btn--has-color';
			}
			$sel = sanitize_title( (string) $selected ) === $term->slug ? ' is-selected' : '';
			$out .= '<button type="button" class="' . esc_attr( $cls . $sel ) . '" data-value="' . esc_attr( $term->slug ) . '"';
			if ( $hex ) {
				$out .= ' style="--swatch-color:' . esc_attr( $hex ) . '"';
			}
			$out .= '>' . ( ! $hex ? esc_html( $term->name ) : '' ) . '</button>';
		}
	} else {
		foreach ( (array) $options as $option ) {
			$sel  = sanitize_title( (string) $selected ) === (string) $selected ? selected( $selected, sanitize_title( $option ), false ) : selected( $selected, $option, false );
			$cls  = 'product-config__btn' . ( strpos( $sel, 'selected' ) !== false ? ' is-selected' : '' );
			$val  = is_object( $option ) ? $option->slug : $option;
			$lab  = is_object( $option ) ? $option->name : $option;
			$out .= '<button type="button" class="' . esc_attr( $cls ) . '" data-value="' . esc_attr( $val ) . '">' . esc_html( apply_filters( 'woocommerce_variation_option_name', $lab, null, $attribute, $product ) ) . '</button>';
		}
	}
	$out .= '</div>';

	return $out;
}
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'slimgebruikt_variation_attribute_options_html', 10, 2 );

/**
 * Remove default WooCommerce wrapper.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'slimgebruikt_woocommerce_wrapper_before' ) ) {
	/**
	 * Before Content.
	 *
	 * Wraps all WooCommerce content in wrappers which match the theme markup.
	 *
	 * @return void
	 */
	function slimgebruikt_woocommerce_wrapper_before() {
		?>
			<main id="primary" class="site-main">
		<?php
	}
}
add_action( 'woocommerce_before_main_content', 'slimgebruikt_woocommerce_wrapper_before' );

if ( ! function_exists( 'slimgebruikt_woocommerce_wrapper_after' ) ) {
	/**
	 * After Content.
	 *
	 * Closes the wrapping divs.
	 *
	 * @return void
	 */
	function slimgebruikt_woocommerce_wrapper_after() {
		?>
			</main><!-- #main -->
		<?php
	}
}
add_action( 'woocommerce_after_main_content', 'slimgebruikt_woocommerce_wrapper_after' );

/**
 * Sample implementation of the WooCommerce Mini Cart.
 *
 * You can add the WooCommerce Mini Cart to header.php like so ...
 *
	<?php
		if ( function_exists( 'slimgebruikt_woocommerce_header_cart' ) ) {
			slimgebruikt_woocommerce_header_cart();
		}
	?>
 */

if ( ! function_exists( 'slimgebruikt_woocommerce_cart_link_fragment' ) ) {
	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array Fragments to refresh via AJAX.
	 */
	function slimgebruikt_woocommerce_cart_link_fragment( $fragments ) {
		ob_start();
		slimgebruikt_woocommerce_cart_link();
		/* Unieke selector voorkomt dat oude cached fragments ons icon vervangen */
		$fragments['a.cart-contents.header-cart-link'] = ob_get_clean();

		return $fragments;
	}
}
add_filter( 'woocommerce_add_to_cart_fragments', 'slimgebruikt_woocommerce_cart_link_fragment', 999 );

if ( ! function_exists( 'slimgebruikt_woocommerce_cart_link' ) ) {
	/**
	 * Cart Link.
	 *
	 * Displayed a link to the cart including the number of items present and the cart total.
	 *
	 * @return void
	 */
	function slimgebruikt_woocommerce_cart_link() {
		$count = WC()->cart->get_cart_contents_count();
		?>
		<a class="cart-contents header-cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'slimgebruikt' ); ?>">
			<svg class="cart-contents__icon" width="27" height="28" viewBox="0 0 27 28" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M20.4545 20.4546C18.9483 20.4546 17.7273 21.6756 17.7273 23.1818C17.7273 24.6881 18.9483 25.9091 20.4545 25.9091C21.9608 25.9091 23.1818 24.6881 23.1818 23.1818C23.1818 21.6756 21.9608 20.4546 20.4545 20.4546ZM20.4545 20.4546H9.94628C9.31757 20.4546 9.00263 20.4546 8.74378 20.3427C8.51544 20.244 8.31734 20.0852 8.173 19.8826C8.01123 19.6555 7.94616 19.3518 7.81737 18.7507L4.46111 3.08817C4.32932 2.47315 4.26254 2.16598 4.0989 1.93627C3.95456 1.73367 3.7565 1.57421 3.52815 1.47553C3.26925 1.36365 2.95607 1.36365 2.32709 1.36365H1.36363M5.45454 5.45456H23.0089C23.9932 5.45456 24.4848 5.45456 24.8151 5.65959C25.1045 5.83919 25.3163 6.12086 25.4087 6.44865C25.5141 6.82287 25.3787 7.29542 25.1059 8.24109L23.2178 14.7865C23.0547 15.352 22.9731 15.6342 22.8076 15.8439C22.6616 16.0291 22.4689 16.1741 22.2505 16.2631C22.0038 16.3636 21.7106 16.3636 21.1256 16.3636H7.81427M8.18181 25.9091C6.67558 25.9091 5.45454 24.6881 5.45454 23.1818C5.45454 21.6756 6.67558 20.4546 8.18181 20.4546C9.68805 20.4546 10.9091 21.6756 10.9091 23.1818C10.9091 24.6881 9.68805 25.9091 8.18181 25.9091Z" stroke="currentColor" stroke-width="2.72727" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
			<?php if ( $count > 0 ) : ?>
				<span class="cart-contents__badge"><?php echo esc_html( $count ); ?></span>
			<?php endif; ?>
		</a>
		<?php
	}
}

/**
 * ACF product badge in shop loop.
 */
function slimgebruikt_woocommerce_product_badge() {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}
	$badge = get_field( 'product_badge', get_the_ID() );
	if ( $badge ) {
		echo '<span class="product-badge">' . esc_html( $badge ) . '</span>';
	}
}
add_action( 'woocommerce_before_shop_loop_item_title', 'slimgebruikt_woocommerce_product_badge', 8 );

/**
 * ACF product subtitle in shop loop.
 */
function slimgebruikt_woocommerce_product_subtitle() {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}
	$subtitle = get_field( 'product_subtitle', get_the_ID() );
	if ( $subtitle ) {
		echo '<span class="product-subtitle">' . esc_html( $subtitle ) . '</span>';
	}
}
add_action( 'woocommerce_shop_loop_item_title', 'slimgebruikt_woocommerce_product_subtitle', 15 );

/**
 * Wrapper open: titel + config summary in aparte div.
 */
function slimgebruikt_woocommerce_product_header_open() {
	echo '<div class="product-detail__header">';
}
add_action( 'woocommerce_single_product_summary', 'slimgebruikt_woocommerce_product_header_open', 4 );

/**
 * Config summary onder producttitel (Geheugen en Kleur voor variable products).
 */
function slimgebruikt_woocommerce_config_summary() {
	global $product;
	if ( ! $product || ! $product->is_type( 'variable' ) ) {
		return;
	}
	$attrs      = $product->get_variation_attributes();
	$mem_keys   = array( 'geheugen', 'memory', 'opslag', 'storage', 'ram', 'internal' );
	$color_keys = array( 'kleur', 'color' );
	$show       = array();
	foreach ( array_keys( $attrs ) as $attr_name ) {
		$lower = strtolower( $attr_name );
		$is_mem = false;
		foreach ( $mem_keys as $k ) {
			if ( strpos( $lower, $k ) !== false ) {
				$is_mem = true;
				break;
			}
		}
		$is_color = false;
		foreach ( $color_keys as $k ) {
			if ( strpos( $lower, $k ) !== false ) {
				$is_color = true;
				break;
			}
		}
		if ( $is_mem || $is_color ) {
			$show[] = array( 'name' => $attr_name, 'order' => $is_mem ? 0 : 1 );
		}
	}
	if ( empty( $show ) ) {
		return;
	}
	usort( $show, function ( $a, $b ) {
		return $a['order'] - $b['order'];
	} );
	$attr_names = array_column( $show, 'name' );
	echo '<span class="product-config-summary" data-attributes="' . esc_attr( wp_json_encode( $attr_names ) ) . '"></span>';
}
add_action( 'woocommerce_single_product_summary', 'slimgebruikt_woocommerce_config_summary', 6 );

/**
 * Product subtitle on single product (after title).
 */
function slimgebruikt_woocommerce_single_product_subtitle() {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}
	$subtitle = get_field( 'product_subtitle', get_the_ID() );
	if ( $subtitle ) {
		echo '<span class="product-subtitle">' . esc_html( $subtitle ) . '</span>';
	}
}
add_action( 'woocommerce_single_product_summary', 'slimgebruikt_woocommerce_single_product_subtitle', 7 );

/**
 * Wrapper sluiten: product-detail__header.
 */
function slimgebruikt_woocommerce_product_header_close() {
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'slimgebruikt_woocommerce_product_header_close', 8 );

/**
 * Related products heading: Vaak bekeken.
 */
add_filter( 'woocommerce_product_related_products_heading', function () {
	return __( 'Vaak bekeken', 'slimgebruikt' );
} );

/**
 * Add to cart button: add arrow icon.
 */
add_filter( 'woocommerce_product_single_add_to_cart_text', function () {
	return __( 'In winkelwagen', 'slimgebruikt' );
} );

add_action( 'woocommerce_after_add_to_cart_button', function () {
	if ( ! is_product() ) {
		return;
	}
	echo '<p class="product-config__klarna">' . esc_html__( 'Betaal later of in 3 delen', 'slimgebruikt' ) . '</p>';
} );

if ( ! function_exists( 'slimgebruikt_woocommerce_header_cart' ) ) {
	/**
	 * Display Header Cart.
	 *
	 * @return void
	 */
	function slimgebruikt_woocommerce_header_cart() {
		if ( is_cart() ) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
		?>
		<ul id="site-header-cart" class="site-header-cart">
			<li class="<?php echo esc_attr( $class ); ?>">
				<?php slimgebruikt_woocommerce_cart_link(); ?>
			</li>
			<li>
				<?php
				$instance = array(
					'title' => '',
				);

				the_widget( 'WC_Widget_Cart', $instance );
				?>
			</li>
		</ul>
		<?php
	}
}
