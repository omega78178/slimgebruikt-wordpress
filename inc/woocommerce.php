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
function slimgebruikt_woocommerce_setup()
{
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 150,
			'single_image_width' => 300,
			'product_grid' => array(
				'default_rows' => 3,
				'min_rows' => 1,
				'default_columns' => 4,
				'min_columns' => 1,
				'max_columns' => 6,
			),
		)
	);
	// Geen zoom op hover – alleen lightbox bij klik
	add_theme_support('wc-product-gallery-lightbox');
	add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'slimgebruikt_woocommerce_setup');

/**
 * Parse swatch colors from theme customizer (slug=hex per line).
 *
 * @return array<string, string> Slug => hex
 */
function slimgebruikt_get_swatch_colors()
{
	$raw = get_theme_mod('slimgebruikt_swatch_colors', '');
	$lines = array_filter(array_map('trim', explode("\n", (string) $raw)));
	$out = array();
	foreach ($lines as $line) {
		if (preg_match('/^([a-z0-9_-]+)\s*=\s*#([0-9a-fA-F]{3,6})$/', $line, $m)) {
			$out[$m[1]] = '#' . $m[2];
		}
	}
	return $out;
}

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function slimgebruikt_woocommerce_scripts()
{
	$wc_style_file = get_template_directory() . '/woocommerce.css';
	$wc_version = file_exists($wc_style_file) ? filemtime($wc_style_file) : _S_VERSION;
	wp_enqueue_style('slimgebruikt-woocommerce-style', get_template_directory_uri() . '/woocommerce.css', array(), $wc_version);

	$swatch_bg = get_theme_mod('slimgebruikt_swatch_bg', '#120E17');
	$swatch_selected = get_theme_mod('slimgebruikt_swatch_selected', '#4BC9F1');
	$swatch_size = (int) get_theme_mod('slimgebruikt_swatch_size', 44);
	$swatch_size = max(32, min(64, $swatch_size));
	$swatch_css = '.product-config__buttons--color { --swatch-bg: ' . esc_attr($swatch_bg) . '; --swatch-selected: ' . esc_attr($swatch_selected) . '; --swatch-size: ' . $swatch_size . 'px; }';
	wp_add_inline_style('slimgebruikt-woocommerce-style', $swatch_css);

	$font_path = WC()->plugin_url() . '/assets/fonts/';
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

	wp_add_inline_style('slimgebruikt-woocommerce-style', $inline_font);

	if ( is_product() ) {
		wp_enqueue_script( 'wc-add-to-cart-variation' ); // Zorg dat variatie-script laadt.
		wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), '8' );
		wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), '8', true );
		wp_enqueue_script(
			'slimgebruikt-product-config',
			get_template_directory_uri() . '/js/product-config.js',
			array('jquery', 'wc-add-to-cart-variation'),
			_S_VERSION,
			true
		);
		wp_enqueue_script( 'slimgebruikt-bestsellers', get_template_directory_uri() . '/js/bestsellers-swiper.js', array( 'swiper' ), _S_VERSION, true );
	}
}
add_action('wp_enqueue_scripts', 'slimgebruikt_woocommerce_scripts');

/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function slimgebruikt_woocommerce_active_body_class($classes)
{
	$classes[] = 'woocommerce-active';

	if (is_shop() || is_cart() || is_checkout()) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter('body_class', 'slimgebruikt_woocommerce_active_body_class');

/**
 * Related Products Args.
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function slimgebruikt_woocommerce_related_products_args($args)
{
	$defaults = array(
		'posts_per_page' => 12,
		'columns'        => 4,
	);

	$args = wp_parse_args($defaults, $args);

	return $args;
}
add_filter('woocommerce_output_related_products_args', 'slimgebruikt_woocommerce_related_products_args');

/**
 * Always provide a fallback shipping rate when no rates are returned.
 *
 * This keeps checkout shipping visible for physical products in block checkout.
 *
 * @param array $rates   Calculated shipping rates.
 * @param array $package Shipping package.
 * @return array
 */
function slimgebruikt_ensure_fallback_shipping_rate($rates, $package)
{
	if (!is_array($rates)) {
		$rates = array();
	}

	if (!empty($rates)) {
		return $rates;
	}

	if (empty($package['contents']) || !is_array($package['contents'])) {
		return $rates;
	}

	$needs_shipping = false;
	foreach ($package['contents'] as $item) {
		if (empty($item['data']) || !is_object($item['data'])) {
			continue;
		}

		if (method_exists($item['data'], 'needs_shipping') && $item['data']->needs_shipping()) {
			$needs_shipping = true;
			break;
		}
	}

	if (!$needs_shipping) {
		return $rates;
	}

	$rate = new WC_Shipping_Rate(
		'slimgebruikt_fallback_flat_rate',
		__('Verzendkosten', 'slimgebruikt'),
		1,
		array(),
		'flat_rate'
	);

	$rates[$rate->id] = $rate;

	return $rates;
}
add_filter('woocommerce_package_rates', 'slimgebruikt_ensure_fallback_shipping_rate', 9999, 2);

/**
 * PERFORMANCE: Cache checkout totals berekening
 * Voorkomt repeated berekeningen bij het laden van de checkout pagina
 */
function slimgebruikt_cache_checkout_fragments($fragments)
{
	// Cache totals sectie voor 1 minuut per session
	$cache_key = 'wc_checkout_totals_' . md5(WC()->cart->get_cart_total());
	$cached = wp_cache_get($cache_key);

	if ($cached) {
		return $cached;
	}

	// Cache de nieuwe berekening
	wp_cache_set($cache_key, $fragments, '', 60);

	return $fragments;
}
add_filter('woocommerce_checkout_update_order_review_fragments', 'slimgebruikt_cache_checkout_fragments', 5);

/**
 * PERFORMANCE: Debounce AJAX checkout updates
 * Enqueue script die AJAX calls throttles (max 1 per 1.5 seconden)
 */
function slimgebruikt_enqueue_checkout_debounce()
{
	if (function_exists('is_checkout') && is_checkout()) {
		wp_enqueue_script(
			'slimgebruikt-checkout-debounce',
			get_template_directory_uri() . '/js/checkout-debounce.js',
			array('jquery'),
			_S_VERSION,
			true
		);

		// Inline script om AJAX debounce in te stellen
		wp_add_inline_script('slimgebruikt-checkout-debounce', '
		window.slimgebruikt_checkout_debounce_delay = 1500;
		');
	}
}
add_action('wp_enqueue_scripts', 'slimgebruikt_enqueue_checkout_debounce', 20);

/**
 * PERFORMANCE: Lazyload niet-essentiële WooCommerce assets op checkout
 */
function slimgebruikt_defer_woocommerce_scripts()
{
	if (function_exists('is_checkout') && is_checkout()) {
		// Defer non-critical WooCommerce JS
		wp_dequeue_script('wc-country-select');
		wp_dequeue_script('wc-address-i18n');
	}
}
add_action('wp_enqueue_scripts', 'slimgebruikt_defer_woocommerce_scripts', 999);

/**
 * PERFORMANCE: Optimize tax calculation caching
 */
function slimgebruikt_cache_tax_calculations($calculated_taxes)
{
	// Tax berekeningen zijn al gecached door WC, maar we forceren het
	$cart = WC()->cart;
	if (!$cart) {
		return $calculated_taxes;
	}

	$cache_key = 'wc_tax_cache_' . md5(wp_json_encode($cart->get_cart_contents()));
	$cached = wp_cache_get($cache_key);

	if ($cached) {
		return $cached;
	}

	// Cache voor 5 minuten
	wp_cache_set($cache_key, $calculated_taxes, '', 300);

	return $calculated_taxes;
}
add_filter('woocommerce_calculated_total', 'slimgebruikt_cache_tax_calculations', 10, 1);

/**
 * PERFORMANCE: Pre-render initial checkout totals
 * Voeg placeholder HTML toe dat dan wordt geüpdate
 */
function slimgebruikt_preload_checkout_totals()
{
	if (function_exists('is_checkout') && is_checkout()) {
		wp_add_inline_script(
			'jquery',
			"
			document.addEventListener('DOMContentLoaded', function() {
				// Vul totals snel in met placeholder waarden
				var subtotal = document.querySelector('.wc-block-components-order-summary-item:nth-child(1)');
				if (subtotal) {
					subtotal.style.opacity = '0.6';
					subtotal.setAttribute('aria-busy', 'true');
				}
			});
			",
			'after'
		);
	}
}
add_action('wp_footer', 'slimgebruikt_preload_checkout_totals');

/**
 * Vervang variatie-dropdowns door knoppen.
 */
function slimgebruikt_variation_attribute_options_html($html, $args)
{
	$options = $args['options'];
	$product = $args['product'];
	$attribute = $args['attribute'];
	$selected = $args['selected'];
	$name = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title($attribute);
	$id = $args['id'] ? $args['id'] : sanitize_title($attribute);
	$is_color = (false !== strpos($attribute, 'kleur') || false !== strpos($attribute, 'color'));
	$attr_lower = strtolower($attribute);
	$is_conditie = (false !== strpos($attr_lower, 'conditie') || false !== strpos($attr_lower, 'optische-toestand') || false !== strpos($attr_lower, 'optische_toestand'));
	$is_batterij = (false !== strpos($attr_lower, 'batterij') || false !== strpos($attr_lower, 'battery'));

	if (empty($options) && $product && $attribute) {
		$attributes = $product->get_variation_attributes();
		$options = $attributes[$attribute] ?? array();
	}
	if (empty($options)) {
		return $html;
	}

	// Conditie-blokken: Data Value, Sterren, Prijs, Levertijd
	if ($is_conditie && $product && $product->is_type('variable')) {
		$attr_key = 'attribute_' . sanitize_title($attribute);
		$attr_key2 = 'attribute_pa_' . str_replace('pa_', '', $attribute);
		$variations = $product->get_available_variations();
		$sterren = slimgebruikt_conditie_sterren_map();
		$levertijd = apply_filters('slimgebruikt_conditie_levertijd', __('2 werkdagen', 'slimgebruikt'));

		$req = !empty($args['required']) ? ' required' : '';
		$out = '<select id="' . esc_attr($id) . '" name="' . esc_attr($name) . '" class="product-config__select" data-attribute_name="' . esc_attr($attr_key) . '" style="display:none"' . $req . '>';
		$out .= '<option value="">' . esc_html__('Kies een conditie', 'slimgebruikt') . '</option>';

		$blocks = '';
		$terms = taxonomy_exists($attribute) ? wc_get_product_terms($product->get_id(), $attribute, array('fields' => 'all')) : array();
		$options_arr = array_values((array) $options);
		$conditie_order = array('goed', 'zeer-goed', 'premium', 'zo-goed-als-nieuw');
		usort($options_arr, function ($a, $b) use ($conditie_order) {
			$sa = sanitize_title(is_object($a) ? $a->slug : (is_string($a) ? $a : ''));
			$sb = sanitize_title(is_object($b) ? $b->slug : (is_string($b) ? $b : ''));
			$ia = array_search($sa, $conditie_order, true);
			$ib = array_search($sb, $conditie_order, true);
			if (false === $ia) {
				$ia = 999;
			}
			if (false === $ib) {
				$ib = 999;
			}
			return $ia - $ib;
		});

		foreach ($options_arr as $idx => $opt) {
			$slug = is_object($opt) ? $opt->slug : (is_string($opt) ? $opt : '');
			$label = is_object($opt) ? $opt->name : $opt;
			if (taxonomy_exists($attribute) && !empty($terms)) {
				$term = $terms[$idx] ?? null;
				foreach ($terms as $t) {
					if (in_array($t->slug, $options_arr, true) && ((string) $t->slug === (string) $slug || (string) $opt === $t->slug)) {
						$slug = $t->slug;
						$label = $t->name;
						break;
					}
				}
			}
			$slug_norm = sanitize_title((string) $slug);
			$stars = $sterren[$slug_norm] ?? $sterren[$slug] ?? '';
			$var_for_opt = null;
			foreach ($variations as $v) {
				$v_attr = $v['attributes'][$attr_key] ?? $v['attributes'][$attr_key2] ?? '';
				if ((string) $v_attr === (string) $slug || (string) $v_attr === $slug_norm) {
					$var_for_opt = $v;
					break;
				}
			}
			$in_stock = true;
			$price_html = '';
			if ($var_for_opt) {
				$in_stock = !empty($var_for_opt['is_in_stock']);
				$price_html = $var_for_opt['price_html'] ?? '';
				if (!$price_html && isset($var_for_opt['display_price'])) {
					$price_html = wc_price($var_for_opt['display_price']);
				}
			} else {
				$data_store = \WC_Data_Store::load('product');
				$variation_id = $data_store->find_matching_product_variation($product, array($attr_key => $slug));
				if ($variation_id) {
					$var = wc_get_product($variation_id);
					if ($var && $var->is_type('variation')) {
						$in_stock = $var->is_in_stock();
						$price_html = $var->get_price_html();
					}
				}
			}
			$out .= '<option value="' . esc_attr($slug) . '"' . (!$in_stock ? ' disabled' : '') . '>' . esc_html($label) . (!$in_stock ? ' (' . esc_html__('Uitverkocht', 'slimgebruikt') . ')' : '') . '</option>';

			$sel_cls = (sanitize_title((string) $selected) === $slug_norm || (string) $selected === (string) $slug) ? ' is-selected' : '';
			$stock_cls = !$in_stock ? ' is-out-of-stock' : '';
			$blocks .= '<button type="button" class="product-conditie-block' . esc_attr($sel_cls . $stock_cls) . '" data-value="' . esc_attr($slug) . '"' . (!$in_stock ? ' disabled aria-disabled="true"' : '') . '>';
			if ($slug_norm === 'zo-goed-als-nieuw' || $slug === 'zo-goed-als-nieuw') {
				$blocks .= '<span class="product-conditie-block__badge">' . esc_html__('Meest gekozen', 'slimgebruikt') . '</span>';
			}
			$blocks .= '<span class="product-conditie-block__label">' . esc_html($label) . '</span>';
			$blocks .= '<span class="product-conditie-block__stars">' . slimgebruikt_render_conditie_stars($stars) . '</span>';
			$blocks .= '<span class="product-conditie-block__price">' . ($price_html ? wp_kses_post($price_html) : '–') . '</span>';
			$blocks .= '<span class="product-conditie-block__levertijd">' . esc_html($levertijd) . '</span>';
			$blocks .= '</button>';
		}
		$out .= '</select>';
		$out .= '<div class="product-conditie-wrapper">';
		$out .= '<div class="product-conditie-blocks product-config__buttons" data-select-id="' . esc_attr($id) . '">' . $blocks . '</div>';

		$uitleg_map = slimgebruikt_conditie_uitleg_map();
		$product_uitleg = function_exists('get_field') ? get_field('conditie_uitleg', $product->get_id()) : array();
		$custom_map = array();
		if (is_array($product_uitleg)) {
			foreach ($product_uitleg as $row) {
				$slug = $row['conditie'] ?? '';
				$tekst = trim((string) ($row['uitleg'] ?? ''));
				if ($slug && $tekst) {
					$custom_map[$slug] = $custom_map[sanitize_title($slug)] = $tekst;
				}
			}
		}
		$out .= '<div class="product-conditie-uitleg-wrap">';
		foreach ($options_arr as $opt) {
			$s = is_object($opt) ? $opt->slug : (is_string($opt) ? $opt : '');
			$lb = is_object($opt) ? $opt->name : $opt;
			$sn = sanitize_title((string) $s);
			$u = $uitleg_map[$s] ?? $uitleg_map[$sn] ?? null;
			$tekst = $custom_map[$s] ?? $custom_map[$sn] ?? ($u ? ($u['tekst'] ?? '') : '');
			$slug_attr = esc_attr($s);
			$out .= '<div class="product-conditie-uitleg" data-conditie-slug="' . $slug_attr . '" style="display:none">';
			$out .= '<p class="product-conditie-uitleg__title">' . esc_html__('Gekozen conditie:', 'slimgebruikt') . ' ' . esc_html($lb) . '</p>';
			$out .= '<p class="product-conditie-uitleg__text">' . esc_html($tekst) . '</p>';
			$out .= '</div>';
		}
		$out .= '</div>';
		$out .= '</div>';
		$out .= "<script>
		(function(){
			function sc(slug){
				var v=(slug||'').trim().toLowerCase().replace(/_/g,'-');
				document.querySelectorAll('.product-conditie-uitleg').forEach(function(el){
					var s=(el.getAttribute('data-conditie-slug')||'').trim().toLowerCase().replace(/_/g,'-');
					el.style.display=(v&&s&&(s===v||s.replace(/-/g,'')===v.replace(/-/g,'')))?'block':'none';
				});
			}
			document.querySelectorAll('.product-conditie-block').forEach(function(btn){
				btn.addEventListener('click',function(){sc(this.getAttribute('data-value'));});
			});
		})();
		</script>";
		return $out;
	}

	// Batterij-blokken: label, bedrag, subtext + icoon
	if ($is_batterij && $product && $product->is_type('variable')) {
		$attr_key = 'attribute_' . sanitize_title($attribute);
		$attr_key2 = 'attribute_pa_' . str_replace('pa_', '', $attribute);
		$variations = $product->get_available_variations();
		$batterij_map = slimgebruikt_batterij_info_map();

		$req = !empty($args['required']) ? ' required' : '';
		$out = '<select id="' . esc_attr($id) . '" name="' . esc_attr($name) . '" class="product-config__select" data-attribute_name="' . esc_attr($attr_key) . '" style="display:none"' . $req . '>';
		$out .= '<option value="">' . esc_html__('Kies een optie', 'slimgebruikt') . '</option>';

		$terms = taxonomy_exists($attribute) ? wc_get_product_terms($product->get_id(), $attribute, array('fields' => 'all')) : array();
		$options_arr = array_values((array) $options);
		$blocks = '';

		foreach ($options_arr as $idx => $opt) {
			$slug = is_object($opt) ? $opt->slug : (is_string($opt) ? $opt : '');
			$label = is_object($opt) ? $opt->name : $opt;
			if (taxonomy_exists($attribute) && !empty($terms)) {
				foreach ($terms as $t) {
					if ((string) $t->slug === (string) $slug) {
						$label = $t->name;
						break;
					}
				}
			}
			$slug_norm = sanitize_title((string) $slug);
			$info = $batterij_map[$slug_norm] ?? $batterij_map[$slug] ?? null;
			$subtext = $info['subtext'] ?? '';
			$icon_svg = apply_filters('slimgebruikt_batterij_optie_icon', $info['icon'] ?? slimgebruikt_batterij_default_icon(), $slug);
			$var_for_opt = null;
			foreach ($variations as $v) {
				$v_attr = $v['attributes'][$attr_key] ?? $v['attributes'][$attr_key2] ?? '';
				if ((string) $v_attr === (string) $slug || (string) $v_attr === $slug_norm) {
					$var_for_opt = $v;
					break;
				}
			}
			$in_stock = true;
			$price_html = '';
			if ($var_for_opt) {
				$in_stock = !empty($var_for_opt['is_in_stock']);
				$price_html = $var_for_opt['price_html'] ?? '';
				if (!$price_html && isset($var_for_opt['display_price'])) {
					$price_html = wc_price($var_for_opt['display_price']);
				}
			} else {
				$data_store = \WC_Data_Store::load('product');
				$variation_id = $data_store->find_matching_product_variation($product, array($attr_key => $slug));
				if ($variation_id) {
					$var = wc_get_product($variation_id);
					if ($var && $var->is_type('variation')) {
						$in_stock = $var->is_in_stock();
						$price_html = $var->get_price_html();
					}
				}
			}
			$out .= '<option value="' . esc_attr($slug) . '"' . (!$in_stock ? ' disabled' : '') . '>' . esc_html($label) . (!$in_stock ? ' (' . esc_html__('Uitverkocht', 'slimgebruikt') . ')' : '') . '</option>';

			$sel_cls = (sanitize_title((string) $selected) === $slug_norm || (string) $selected === (string) $slug) ? ' is-selected' : '';
			$stock_cls = !$in_stock ? ' is-out-of-stock' : '';
			$blocks .= '<button type="button" class="product-batterij-block' . esc_attr($sel_cls . $stock_cls) . '" data-value="' . esc_attr($slug) . '"' . (!$in_stock ? ' disabled aria-disabled="true"' : '') . '>';
			$blocks .= '<span class="product-batterij-block__label">' . esc_html($label) . '</span>';
			$blocks .= '<span class="product-batterij-block__price">' . ($price_html ? wp_kses_post($price_html) : '–') . '</span>';
			if ($subtext) {
				$blocks .= '<span class="product-batterij-block__subtext">' . esc_html($subtext);
				if ($icon_svg) {
					$blocks .= ' <span class="product-batterij-block__icon">' . $icon_svg . '</span>';
				}
				$blocks .= '</span>';
			}
			$blocks .= '</button>';
		}
		$out .= '</select>';
		$out .= '<div class="product-batterij-blocks product-config__buttons" data-select-id="' . esc_attr($id) . '">' . $blocks . '</div>';
		return $out;
	}

	$req = !empty($args['required']) ? ' required' : '';
	$out = '<select id="' . esc_attr($id) . '" name="' . esc_attr($name) . '" class="product-config__select" data-attribute_name="attribute_' . esc_attr(sanitize_title($attribute)) . '" style="display:none"' . $req . '>';
	$out .= '<option value="">' . esc_html__('Choose an option', 'woocommerce') . '</option>';

	if ($product && taxonomy_exists($attribute)) {
		$terms = wc_get_product_terms($product->get_id(), $attribute, array('fields' => 'all'));
		foreach ($terms as $term) {
			if (in_array($term->slug, (array) $options, true)) {
				$sel = selected(sanitize_title((string) $selected), $term->slug, false);
				$out .= '<option value="' . esc_attr($term->slug) . '" ' . $sel . '>' . esc_html(apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute, $product)) . '</option>';
			}
		}
	} else {
		foreach ((array) $options as $option) {
			$sel = sanitize_title((string) $selected) === (string) $selected ? selected($selected, sanitize_title($option), false) : selected($selected, $option, false);
			$out .= '<option value="' . esc_attr($option) . '" ' . $sel . '>' . esc_html(apply_filters('woocommerce_variation_option_name', $option, null, $attribute, $product)) . '</option>';
		}
	}
	$out .= '</select>';

	$out .= '<div class="product-config__buttons' . ($is_color ? ' product-config__buttons--color' : '') . '" data-select-id="' . esc_attr($id) . '">';

	if ($product && taxonomy_exists($attribute)) {
		$theme_colors = slimgebruikt_get_swatch_colors();
		foreach ($terms as $term) {
			if (!in_array($term->slug, (array) $options, true)) {
				continue;
			}
			$hex = isset($theme_colors[$term->slug]) ? $theme_colors[$term->slug] : get_term_meta($term->term_id, 'product_attribute_color', true);
			$cls = $is_color ? 'product-config__btn--swatch' : 'product-config__btn';
			if ($hex) {
				$cls .= ' product-config__btn--has-color';
			}
			$sel = sanitize_title((string) $selected) === $term->slug ? ' is-selected' : '';
			$out .= '<button type="button" class="' . esc_attr($cls . $sel) . '" data-value="' . esc_attr($term->slug) . '"';
			if ($hex) {
				$out .= ' style="--swatch-color:' . esc_attr($hex) . '"';
			}
			$out .= '>' . (!$hex ? esc_html($term->name) : '') . '</button>';
		}
	} else {
		foreach ((array) $options as $option) {
			$sel = sanitize_title((string) $selected) === (string) $selected ? selected($selected, sanitize_title($option), false) : selected($selected, $option, false);
			$cls = 'product-config__btn' . (strpos($sel, 'selected') !== false ? ' is-selected' : '');
			$val = is_object($option) ? $option->slug : $option;
			$lab = is_object($option) ? $option->name : $option;
			$out .= '<button type="button" class="' . esc_attr($cls) . '" data-value="' . esc_attr($val) . '">' . esc_html(apply_filters('woocommerce_variation_option_name', $lab, null, $attribute, $product)) . '</button>';
		}
	}
	$out .= '</div>';

	return $out;
}
add_filter('woocommerce_dropdown_variation_attribute_options_html', 'slimgebruikt_variation_attribute_options_html', 10, 2);

/**
 * Render sterren-rating als SVG. Max 6 sterren.
 * Filter 'slimgebruikt_star_svg' om eigen SVG te gebruiken.
 *
 * @param float|int $rating Waarde (3, 4, 5, 5.5, 6).
 * @return string HTML met .product-conditie-stars
 */
function slimgebruikt_render_conditie_stars($rating)
{
	$rating = (float) $rating;
	$max = 5;
	$rating = ($rating / 6) * 5;
	$path = 'M0.190608 9.04102C-0.179944 8.69834 0.0213408 8.07885 0.522543 8.01943L7.62433 7.17707C7.8286 7.15285 8.00603 7.02458 8.09219 6.83779L11.0876 0.343775C11.299 -0.114533 11.9506 -0.114621 12.162 0.343687L15.1574 6.83765C15.2436 7.02444 15.4198 7.15306 15.6241 7.17728L22.7263 8.01943C23.2275 8.07885 23.4282 8.69853 23.0577 9.0412L17.8077 13.8971C17.6567 14.0368 17.5894 14.2446 17.6295 14.4464L19.0229 21.4608C19.1212 21.9558 18.5944 22.3394 18.154 22.0928L11.9136 18.5989C11.7341 18.4984 11.5161 18.4989 11.3366 18.5993L5.09559 22.092C4.65518 22.3385 4.12737 21.9558 4.22573 21.4608L5.61928 14.4468C5.65936 14.2451 5.5923 14.0367 5.44127 13.8971L0.190608 9.04102Z';
	$path = apply_filters('slimgebruikt_star_svg_path', $path);
	$svg_full = '<svg viewBox="0 0 24 23" xmlns="http://www.w3.org/2000/svg"><path d="' . esc_attr($path) . '" fill="currentColor"/></svg>';
	$svg_empty = '<svg viewBox="0 0 24 23" xmlns="http://www.w3.org/2000/svg"><path d="' . esc_attr($path) . '" fill="none" stroke="currentColor" stroke-width="1.2"/></svg>';
	$out = '<span class="product-conditie-stars" aria-label="' . esc_attr($rating . ' ' . __('sterren', 'slimgebruikt')) . '">';
	for ($i = 1; $i <= $max; $i++) {
		$fill = 0;
		if ($rating >= $i) {
			$fill = 1;
		} elseif ($rating >= $i - 0.5) {
			$fill = 0.5;
		}
		$cls = 'product-conditie-star product-conditie-star--' . ($fill >= 1 ? 'full' : ($fill >= 0.5 ? 'half' : 'empty'));
		if ($fill >= 1) {
			$out .= '<span class="' . esc_attr($cls) . '">' . $svg_full . '</span>';
		} elseif ($fill >= 0.5) {
			$out .= '<span class="' . esc_attr($cls) . '"><span class="product-conditie-star__bg">' . $svg_empty . '</span><span class="product-conditie-star__fill">' . $svg_full . '</span></span>';
		} else {
			$out .= '<span class="' . esc_attr($cls) . '">' . $svg_empty . '</span>';
		}
	}
	$out .= '</span>';
	return $out;
}

/**
 * Mapping conditie slug -> aantal sterren.
 * Condities: goed (4), zeer goed (5), premium (5.5), zo goed als nieuw (6).
 *
 * @return array<string, float|int>
 */
function slimgebruikt_conditie_sterren_map()
{
	return array(
		'zo-goed-als-nieuw' => 6,
		'premium' => 5.5,
		'zeer-goed' => 5,
		'goed' => 4,
	);
}

/**
 * Mapping batterij slug -> subtext en optioneel icon.
 * Filter 'slimgebruikt_batterij_info_map' om aan te passen.
 *
 * @return array<string, array{subtext: string, icon?: string}>
 */
function slimgebruikt_batterij_info_map()
{
	$map = array(
		'nieuwe-batterij' => array('subtext' => __('Nieuwe batterij met 100% capaciteit', 'slimgebruikt')),
		'optimale-batterij' => array('subtext' => __('Batterij met optimale capaciteit', 'slimgebruikt')),
		'standaard' => array('subtext' => ''),
	);
	return apply_filters('slimgebruikt_batterij_info_map', $map);
}

/**
 * Standaard batterij-icoon (placeholder). Vervang via filter 'slimgebruikt_batterij_optie_icon'.
 *
 * @return string SVG
 */
function slimgebruikt_batterij_default_icon()
{
	return '<svg class="product-batterij-block__icon-svg" viewBox="0 0 24 12" xmlns="http://www.w3.org/2000/svg" width="16" height="8" aria-hidden="true"><rect x="0.5" y="0.5" width="18" height="11" rx="1.5" fill="none" stroke="currentColor" stroke-width="1"/><rect x="20" y="3" width="2" height="6" rx="0.5" fill="currentColor"/><rect x="2" y="2" width="15" height="8" rx="1" fill="currentColor" opacity="0.9"/></svg>';
}

/**
 * Mapping conditie slug -> uitleg (label + tekst).
 *
 * @return array<string, array{label: string, tekst: string}>
 */
function slimgebruikt_conditie_uitleg_map()
{
	$map = array(
		'goed' => array(
			'label' => __('Goed', 'slimgebruikt'),
			'tekst' => __('De telefoon vertoont duidelijke gebruikssporen en kan krasjes of kleine beschadigingen hebben. Deze zijn functioneel getest en werken goed. De telefoon is simlock vrij.', 'slimgebruikt'),
		),
		'zeer-goed' => array(
			'label' => __('Zeer goed', 'slimgebruikt'),
			'tekst' => __('De telefoon is in goede staat met lichte gebruikssporen. Mogelijk kleine krasjes op het scherm of de behuizing. Volledig getest, simlock vrij en werkt uitstekend.', 'slimgebruikt'),
		),
		'premium' => array(
			'label' => __('Premium', 'slimgebruikt'),
			'tekst' => __('De iPhone is in het algemeen als nieuw, maar kan lichte gebruikssporen vertonen zoals kleine krasjes, maar geen opvallende markeringen of beschadigingen. De telefoon is simlock vrij, volledig getest én werkt als nieuw.', 'slimgebruikt'),
		),
		'zo-goed-als-nieuw' => array(
			'label' => __('Zo goed als nieuw', 'slimgebruikt'),
			'tekst' => __('De telefoon ziet er vrijwel nieuw uit, zonder zichtbare gebruikssporen. Volledig getest, simlock vrij en klaar voor gebruik.', 'slimgebruikt'),
		),
	);
	return apply_filters('slimgebruikt_conditie_uitleg_map', $map);
}

/**
 * Remove default WooCommerce wrapper.
 */
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

if (!function_exists('slimgebruikt_woocommerce_wrapper_before')) {
	/**
	 * Before Content.
	 *
	 * Wraps all WooCommerce content in wrappers which match the theme markup.
	 *
	 * @return void
	 */
	function slimgebruikt_woocommerce_wrapper_before()
	{
		?>
		<main id="primary" class="site-main">
			<?php
	}
}
add_action('woocommerce_before_main_content', 'slimgebruikt_woocommerce_wrapper_before');

if (!function_exists('slimgebruikt_woocommerce_wrapper_after')) {
	/**
	 * After Content.
	 *
	 * Closes the wrapping divs.
	 *
	 * @return void
	 */
	function slimgebruikt_woocommerce_wrapper_after()
	{
		?>
		</main><!-- #main -->
		<?php
	}
}
add_action('woocommerce_after_main_content', 'slimgebruikt_woocommerce_wrapper_after');

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

if (!function_exists('slimgebruikt_woocommerce_cart_link_fragment')) {
	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array Fragments to refresh via AJAX.
	 */
	function slimgebruikt_woocommerce_cart_link_fragment($fragments)
	{
		ob_start();
		slimgebruikt_woocommerce_cart_link();
		/* Unieke selector voorkomt dat oude cached fragments ons icon vervangen */
		$fragments['a.cart-contents.header-cart-link'] = ob_get_clean();

		return $fragments;
	}
}
add_filter('woocommerce_add_to_cart_fragments', 'slimgebruikt_woocommerce_cart_link_fragment', 999);

if (!function_exists('slimgebruikt_woocommerce_cart_link')) {
	/**
	 * Cart Link.
	 *
	 * Displayed a link to the cart including the number of items present and the cart total.
	 *
	 * @return void
	 */
	function slimgebruikt_woocommerce_cart_link()
	{
		$count = WC()->cart->get_cart_contents_count();
		?>
		<a class="cart-contents header-cart-link" href="<?php echo esc_url(wc_get_cart_url()); ?>"
			title="<?php esc_attr_e('View your shopping cart', 'slimgebruikt'); ?>">
			<svg class="cart-contents__icon" width="27" height="28" viewBox="0 0 27 28" fill="none"
				xmlns="http://www.w3.org/2000/svg">
				<path
					d="M20.4545 20.4546C18.9483 20.4546 17.7273 21.6756 17.7273 23.1818C17.7273 24.6881 18.9483 25.9091 20.4545 25.9091C21.9608 25.9091 23.1818 24.6881 23.1818 23.1818C23.1818 21.6756 21.9608 20.4546 20.4545 20.4546ZM20.4545 20.4546H9.94628C9.31757 20.4546 9.00263 20.4546 8.74378 20.3427C8.51544 20.244 8.31734 20.0852 8.173 19.8826C8.01123 19.6555 7.94616 19.3518 7.81737 18.7507L4.46111 3.08817C4.32932 2.47315 4.26254 2.16598 4.0989 1.93627C3.95456 1.73367 3.7565 1.57421 3.52815 1.47553C3.26925 1.36365 2.95607 1.36365 2.32709 1.36365H1.36363M5.45454 5.45456H23.0089C23.9932 5.45456 24.4848 5.45456 24.8151 5.65959C25.1045 5.83919 25.3163 6.12086 25.4087 6.44865C25.5141 6.82287 25.3787 7.29542 25.1059 8.24109L23.2178 14.7865C23.0547 15.352 22.9731 15.6342 22.8076 15.8439C22.6616 16.0291 22.4689 16.1741 22.2505 16.2631C22.0038 16.3636 21.7106 16.3636 21.1256 16.3636H7.81427M8.18181 25.9091C6.67558 25.9091 5.45454 24.6881 5.45454 23.1818C5.45454 21.6756 6.67558 20.4546 8.18181 20.4546C9.68805 20.4546 10.9091 21.6756 10.9091 23.1818C10.9091 24.6881 9.68805 25.9091 8.18181 25.9091Z"
					stroke="currentColor" stroke-width="2.72727" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
			<?php if ($count > 0): ?>
				<span class="cart-contents__badge"><?php echo esc_html($count); ?></span>
			<?php endif; ?>
		</a>
		<?php
	}
}

/**
 * ACF product badge in shop loop.
 */
function slimgebruikt_woocommerce_product_badge()
{
	if (!function_exists('get_field')) {
		return;
	}
	$badge = get_field('product_badge', get_the_ID());
	if ($badge) {
		echo '<span class="product-badge">' . esc_html($badge) . '</span>';
	}
}
add_action('woocommerce_before_shop_loop_item_title', 'slimgebruikt_woocommerce_product_badge', 8);

/**
 * ACF product subtitle in shop loop.
 */
function slimgebruikt_woocommerce_product_subtitle()
{
	if (!function_exists('get_field')) {
		return;
	}
	$subtitle = get_field('product_subtitle', get_the_ID());
	if ($subtitle) {
		echo '<span class="product-subtitle">' . esc_html($subtitle) . '</span>';
	}
}
add_action('woocommerce_shop_loop_item_title', 'slimgebruikt_woocommerce_product_subtitle', 15);

/**
 * Verberg product meta (Artikelnummer, Categorie, Merk).
 */
add_action('wp', function () {
	if (is_product()) {
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
	}
});

/**
 * Wrapper open: titel + config summary in aparte div.
 */
function slimgebruikt_woocommerce_product_header_open()
{
	echo '<div class="product-detail__header">';
}
add_action('woocommerce_single_product_summary', 'slimgebruikt_woocommerce_product_header_open', 4);

/**
 * Config summary onder producttitel (Geheugen en Kleur voor variable products).
 */
function slimgebruikt_woocommerce_config_summary()
{
	global $product;
	if (!$product || !$product->is_type('variable')) {
		return;
	}
	$attrs = $product->get_variation_attributes();
	$mem_keys = array('geheugen', 'memory', 'opslag', 'storage', 'ram', 'internal');
	$color_keys = array('kleur', 'color');
	$show = array();
	foreach (array_keys($attrs) as $attr_name) {
		$lower = strtolower($attr_name);
		$is_mem = false;
		foreach ($mem_keys as $k) {
			if (strpos($lower, $k) !== false) {
				$is_mem = true;
				break;
			}
		}
		$is_color = false;
		foreach ($color_keys as $k) {
			if (strpos($lower, $k) !== false) {
				$is_color = true;
				break;
			}
		}
		if ($is_mem || $is_color) {
			$show[] = array('name' => $attr_name, 'order' => $is_mem ? 0 : 1);
		}
	}
	if (empty($show)) {
		return;
	}
	usort($show, function ($a, $b) {
		return $a['order'] - $b['order'];
	});
	$attr_names = array_column($show, 'name');
	echo '<span class="product-config-summary" data-attributes="' . esc_attr(wp_json_encode($attr_names)) . '"></span>';
}
add_action('woocommerce_single_product_summary', 'slimgebruikt_woocommerce_config_summary', 6);

/**
 * Product subtitle on single product (after title).
 */
function slimgebruikt_woocommerce_single_product_subtitle()
{
	if (!function_exists('get_field')) {
		return;
	}
	$subtitle = get_field('product_subtitle', get_the_ID());
	if ($subtitle) {
		echo '<span class="product-subtitle">' . esc_html($subtitle) . '</span>';
	}
}
add_action('woocommerce_single_product_summary', 'slimgebruikt_woocommerce_single_product_subtitle', 7);

/**
 * Wrapper sluiten: product-detail__header.
 */
function slimgebruikt_woocommerce_product_header_close()
{
	echo '</div>';
}
add_action('woocommerce_single_product_summary', 'slimgebruikt_woocommerce_product_header_close', 8);

/**
 * Related products heading: Vaak bekeken.
 */
add_filter('woocommerce_product_related_products_heading', function () {
	return __('Vaak bekeken', 'slimgebruikt');
});

/**
 * Add to cart button: add arrow icon.
 */
add_filter('woocommerce_product_single_add_to_cart_text', function () {
	return __('In winkelwagen', 'slimgebruikt');
});

add_action('woocommerce_before_single_variation', 'slimgebruikt_woocommerce_checkout_summary');
function slimgebruikt_woocommerce_checkout_summary()
{
	global $product;
	if (!$product || !$product->is_type('variable')) {
		return;
	}
	$labels = array(
		'pa_kleur' => __('Kleur:', 'slimgebruikt'),
		'pa_opslagruimte' => __('Opslagruimte:', 'slimgebruikt'),
		'pa_optische-toestand' => __('Optische toestand:', 'slimgebruikt'),
		'pa_batterij' => __('Batterij:', 'slimgebruikt'),
	);
	$attr_order = array('pa_kleur', 'pa_opslagruimte', 'pa_optische-toestand', 'pa_batterij');
	$attrs = $product->get_variation_attributes();
	$attr_keys = array_keys($attrs);
	usort($attr_keys, function ($a, $b) use ($attr_order) {
		$ia = array_search($a, $attr_order, true);
		$ib = array_search($b, $attr_order, true);
		if (false === $ia) {
			$ia = 999;
		}
		if (false === $ib) {
			$ib = 999;
		}
		return $ia - $ib;
	});
	?>
	<div class="product-checkout-summary">
		<h3 class="product-checkout-summary__title">
			<?php printf(esc_html__('Jouw %s', 'slimgebruikt'), esc_html($product->get_name())); ?>
		</h3>
		<div class="product-checkout-summary__details">
			<?php
			foreach ($attr_keys as $attr_name) {
				$label = $labels[$attr_name] ?? wc_attribute_label($attr_name);
				$key = wc_variation_attribute_name($attr_name);
				$options = array();
				if (taxonomy_exists($attr_name)) {
					$terms = wc_get_product_terms($product->get_id(), $attr_name, array('fields' => 'all'));
					foreach ($terms as $term) {
						if (in_array($term->slug, (array) ($attrs[$attr_name] ?? array()), true)) {
							$options[$term->slug] = $term->name;
						}
					}
				} else {
					foreach ((array) ($attrs[$attr_name] ?? array()) as $opt) {
						$options[$opt] = $opt;
					}
				}
				$options_json = wp_json_encode($options);
				?>
				<p class="product-checkout-summary__row" data-attribute-name="<?php echo esc_attr($key); ?>"
					data-options="<?php echo esc_attr($options_json); ?>">
					<span class="product-checkout-summary__label"><?php echo esc_html($label); ?></span>
					<span class="product-checkout-summary__value">–</span>
				</p>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}

add_action('woocommerce_after_add_to_cart_button', function () {
	if (!is_product()) {
		return;
	}
	$klarna_url = 'https://www.klarna.com/nl/';
	$klarna_img = get_template_directory_uri() . '/images/klarna.png';
	$klarna_text = __('Betaal later of in 3 delen', 'slimgebruikt');
	echo '<p class="product-config__klarna">';
	echo '<a href="' . esc_url($klarna_url) . '" target="_blank" rel="noopener noreferrer" class="product-config__klarna-link">';
	echo '<img src="' . esc_url($klarna_img) . '" alt="Klarna" class="product-config__klarna-logo" width="50" loading="lazy" />';
	echo '<span class="product-config__klarna-text">' . esc_html($klarna_text) . '</span>';
	echo '</a>';
	echo '</p>';

	// Uitklapbare "30 dagen retour" blok
	$returns_title = __('Krijg 30 dagen om van gedachten te veranderen', 'slimgebruikt');
	$returns_heading = __('30 dagen gratis retourneren', 'slimgebruikt');
	$returns_p1 = __('Regel de retourzending met slechts een paar klikken in je slimgebruikt-account.', 'slimgebruikt');
	$returns_p2 = __('Stuur het toestel gratis terug binnen 30 dagen na ontvangst.', 'slimgebruikt');
	$returns_p3 = __('Ontvang je geld terug binnen 3 werkdagen nadat de verkoper het toestel heeft ontvangen.', 'slimgebruikt');
	$icon_envelope = '<svg xmlns="http://www.w3.org/2000/svg" width="27" height="22" viewBox="0 0 27 22" fill="none">
<path d="M2.66683 2.66671L10.8103 8.8164L10.8131 8.81865C11.7173 9.48175 12.1697 9.8135 12.6652 9.94166C13.1031 10.0549 13.5635 10.0549 14.0015 9.94166C14.4974 9.81339 14.9511 9.48067 15.8569 8.8164C15.8569 8.8164 21.0803 4.80796 24.0002 2.66671M1.3335 15.7336V5.6003C1.3335 4.10683 1.3335 3.35953 1.62415 2.7891C1.87981 2.28734 2.28746 1.87969 2.78923 1.62402C3.35966 1.33337 4.10695 1.33337 5.60042 1.33337H21.0671C22.5606 1.33337 23.3063 1.33337 23.8767 1.62402C24.3785 1.87969 24.7875 2.28734 25.0431 2.7891C25.3335 3.35898 25.3335 4.10537 25.3335 5.59592V15.7381C25.3335 17.2287 25.3335 17.974 25.0431 18.5439C24.7875 19.0456 24.3785 19.454 23.8767 19.7097C23.3068 20 22.5615 20 21.071 20H5.59604C4.10549 20 3.3591 20 2.78923 19.7097C2.28746 19.454 1.87981 19.0456 1.62415 18.5439C1.3335 17.9735 1.3335 17.2271 1.3335 15.7336Z" stroke="#120E17" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
</svg>';
	$icon_calendar = '<svg xmlns="http://www.w3.org/2000/svg" width="27" height="30" viewBox="0 0 27 30" fill="none">
  <path d="M1.5 10.5H25.5M1.5 10.5V23.7003C1.5 25.3805 1.5 26.2201 1.82698 26.8618C2.1146 27.4263 2.57321 27.8857 3.1377 28.1733C3.7788 28.5 4.61849 28.5 6.29537 28.5H20.7046C22.3815 28.5 23.22 28.5 23.8611 28.1733C24.4256 27.8857 24.8857 27.4263 25.1733 26.8618C25.5 26.2207 25.5 25.3822 25.5 23.7054V10.5M1.5 10.5V9.30029C1.5 7.62014 1.5 6.77943 1.82698 6.1377C2.1146 5.57321 2.57321 5.1146 3.1377 4.82698C3.77943 4.5 4.62014 4.5 6.30029 4.5H7.5M25.5 10.5V9.29536C25.5 7.61849 25.5 6.7788 25.1733 6.1377C24.8857 5.57321 24.4256 5.1146 23.8611 4.82698C23.2194 4.5 22.3805 4.5 20.7003 4.5H19.5M7.5 4.5H19.5M7.5 4.5V1.5M19.5 4.5V1.5M18 16.5L12 22.5L9 19.5" stroke="#120E17" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
</svg>';
	$icon_euro = '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
<path d="M8.021 0.927002C14.1755 -1.38186 21.0874 1.62477 23.5659 7.72778L23.7788 8.29614C25.8265 14.1922 23.0558 20.7522 17.312 23.3489L17.311 23.3499C17.0331 23.4734 16.7673 23.4645 16.5454 23.3635C16.327 23.2641 16.1616 23.0801 16.0669 22.8694C15.9721 22.6586 15.9436 22.4118 16.0112 22.1809C16.0799 21.9468 16.2452 21.7383 16.5161 21.6067L16.519 21.6047L16.5708 21.719L16.5815 21.7415L16.52 21.6047C21.5263 19.3384 23.8457 13.5148 21.7905 8.42896C19.7485 3.37489 14.07 0.841914 8.94873 2.60571L8.45557 2.78931C3.21132 4.9241 0.681446 10.9067 2.81592 16.1506H2.81689C3.7431 18.4661 5.5247 20.428 7.93994 21.5745L8.14502 20.4836V20.4827C8.17139 20.3508 8.27609 20.2632 8.38721 20.2385C8.48951 20.2159 8.60551 20.2443 8.68408 20.3372L8.71533 20.3811L8.71631 20.3831L10.6948 23.8059L10.8091 24.0022L10.5835 24.0295L6.62646 24.511L6.62549 24.51C6.48184 24.5329 6.36855 24.4446 6.31787 24.3489C6.2641 24.2473 6.25793 24.0984 6.36768 23.9885L7.0542 23.2571C4.22663 21.8909 2.15392 19.5797 1.0415 16.8518L1.04053 16.8508C-1.47685 10.6243 1.52847 3.55801 7.72803 1.04028L8.021 0.927002Z" fill="black" stroke="black" stroke-width="0.3"/>
<path d="M12.8115 7.19958C14.5581 7.19966 15.7605 7.95968 16.5713 8.92224L16.7275 9.11755L16.7314 9.12146L16.6885 9.14783L16.7314 9.12244C16.8169 9.26505 16.8469 9.41143 16.8174 9.55896C16.7886 9.70275 16.7012 9.85454 16.5469 9.91638L15.8291 10.2885H15.8281C15.5942 10.4054 15.3269 10.3482 15.1777 10.14C14.6514 9.45622 13.7833 8.95749 12.8115 8.9574C11.3349 8.9574 10.1666 9.74522 9.60059 10.9965H13.293C13.5878 10.9967 13.8242 11.2328 13.8242 11.5277V11.6342C13.8242 11.929 13.5878 12.1652 13.293 12.1654H9.27637C9.27459 12.2445 9.27015 12.3239 9.26465 12.4037C9.25797 12.5006 9.25195 12.5996 9.25195 12.7045C9.25195 12.8764 9.25277 13.0654 9.27246 13.2152H13.2656C13.5605 13.2152 13.7966 13.4517 13.7969 13.7465V13.8539C13.7969 14.1489 13.5606 14.3851 13.2656 14.3851H9.57324C10.1149 15.6368 11.3076 16.4504 12.7842 16.4506C13.7568 16.4506 14.6517 15.9508 15.1777 15.267C15.327 15.0595 15.5945 15.0027 15.8281 15.1195H15.8291L16.5508 15.4935L16.5537 15.4955L16.6514 15.5658C16.7388 15.6424 16.7949 15.7369 16.8174 15.849L16.8184 15.851C16.8468 16.0217 16.8186 16.1702 16.7285 16.2904C15.9182 17.3708 14.674 18.2083 12.8115 18.2084C10.2641 18.2084 8.19279 16.7401 7.5332 14.3588H7.22266C6.9279 14.3586 6.69163 14.1222 6.69141 13.8275V13.7201C6.69143 13.4252 6.92778 13.189 7.22266 13.1888H7.2998C7.28061 13.0325 7.28027 12.8419 7.28027 12.6771C7.28027 12.5064 7.28205 12.3334 7.30273 12.139H7.19629C6.90129 12.139 6.66504 11.9028 6.66504 11.6078V11.5004C6.66529 11.2056 6.90144 10.9691 7.19629 10.9691H7.50781C8.19453 8.66745 10.2914 7.19958 12.8115 7.19958Z" fill="black" stroke="black" stroke-width="0.1"/>
</svg>';
	$icon_chevron = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>';
	?>
	<div class="product-config__returns-accordion">
		<button type="button" class="product-config__returns-toggle" aria-expanded="false"
			aria-controls="product-returns-content" data-returns-toggle>
			<span><?php echo esc_html($returns_title); ?></span>
			<span class="product-config__returns-chevron"
				aria-hidden="true"><?php echo $icon_chevron; // phpcs:ignore ?></span>
		</button>
		<div class="product-config__returns-content" id="product-returns-content" role="region" aria-hidden="true">
			<div class="product-config__returns-inner">
				<h4 class="product-config__returns-heading"><?php echo esc_html($returns_heading); ?></h4>
				<div class="product-config__returns-item">
					<span class="product-config__returns-icon"><?php echo $icon_envelope; // phpcs:ignore ?></span>
					<span><?php echo esc_html($returns_p1); ?></span>
				</div>
				<div class="product-config__returns-item">
					<span class="product-config__returns-icon"><?php echo $icon_calendar; // phpcs:ignore ?></span>
					<span><?php echo esc_html($returns_p2); ?></span>
				</div>
				<div class="product-config__returns-item">
					<span class="product-config__returns-icon"><?php echo $icon_euro; // phpcs:ignore ?></span>
					<span><?php echo esc_html($returns_p3); ?></span>
				</div>
			</div>
		</div>
	</div>
	<script>
		document.addEventListener('click', function (e) {
			var b = e.target.closest('[data-returns-toggle]');
			if (!b) return;
			var c = document.getElementById('product-returns-content');
			if (!c) return;
			e.preventDefault();
			var open = c.classList.contains('is-open');
			var inner = c.querySelector('.product-config__returns-inner');
			if (open) {
				c.style.height = c.offsetHeight + 'px';
				c.offsetHeight;
				c.style.height = '0';
				c.classList.remove('is-open');
				b.setAttribute('aria-expanded', 'false');
				c.setAttribute('aria-hidden', 'true');
				setTimeout(function () { c.style.height = ''; }, 320);
			} else {
				c.classList.add('is-open');
				b.setAttribute('aria-expanded', 'true');
				c.removeAttribute('aria-hidden');
				c.style.overflow = 'hidden';
				c.style.height = 'auto';
				var h = c.offsetHeight || (inner ? inner.scrollHeight : 200) || 200;
				c.style.height = '0';
				c.offsetHeight;
				c.style.height = h + 'px';
				setTimeout(function () { c.style.height = 'auto'; c.style.overflow = ''; }, 320);
			}
		});
	</script>
	<?php
	// Betalingsiconen (ACF Theme-instellingen)
	$payment_icons = function_exists('get_field') ? get_field('payment_icons', 'option') : null;
	if (!empty($payment_icons) && is_array($payment_icons)):
		?>
		<div class="product-config__payment-icons">
			<?php
			foreach ($payment_icons as $item):
				$img = is_array($item['image'] ?? null) ? ($item['image']['url'] ?? '') : '';
				$alt = !empty($item['alt']) ? $item['alt'] : (is_array($item['image'] ?? null) ? ($item['image']['alt'] ?? '') : '');
				$link = !empty($item['link']) ? $item['link'] : '';
				if (empty($img)) {
					continue;
				}
				$img_tag = sprintf('<img src="%s" alt="%s" width="48" height="32" loading="lazy" />', esc_url($img), esc_attr($alt));
				if ($link) {
					echo '<a href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer" class="product-config__payment-icon">' . wp_kses_post($img_tag) . '</a>';
				} else {
					echo '<span class="product-config__payment-icon">' . wp_kses_post($img_tag) . '</span>';
				}
			endforeach;
			?>
		</div>
		<?php
	endif;
	// Gratis verzending & 30 dagen bedenktijd
	?>
	<p class="product-config__usp-line">
		<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none" aria-hidden="true">
			<path
				d="M16.2324 9.98907L11.2378 14.9837L8.74046 12.4864M14.0424 1.82154L15.567 3.12077C15.9511 3.44809 16.428 3.64598 16.931 3.68612L18.9277 3.84525C20.1016 3.93893 21.034 4.87078 21.1277 6.04469L21.2865 8.04169C21.3266 8.54473 21.5252 9.02233 21.8525 9.40643L23.1512 10.9306C23.915 11.8269 23.9152 13.1453 23.1513 14.0417L21.8527 15.5661C21.5253 15.9502 21.327 16.4279 21.2869 16.931L21.1271 18.9276C21.0334 20.1016 20.1024 21.034 18.9285 21.1277L16.9312 21.287C16.4281 21.3272 15.9508 21.5245 15.5667 21.8519L14.0424 23.1511C13.1461 23.915 11.8268 23.9151 10.9305 23.1513L9.40625 21.852C9.02216 21.5247 8.54479 21.3269 8.04175 21.2868L6.04437 21.1277C4.87046 21.034 3.93947 20.1018 3.8458 18.9279L3.68605 16.9311C3.64591 16.4281 3.44758 15.9507 3.12025 15.5666L1.82159 14.0417C1.05775 13.1454 1.0574 11.8274 1.82123 10.9311L3.12106 9.4062C3.44839 9.0221 3.64482 8.54474 3.68496 8.04169L3.8447 6.04492C3.93838 4.87102 4.87205 3.93881 6.04596 3.84513L8.04113 3.686C8.54418 3.64585 9.02178 3.44813 9.40587 3.12081L10.9306 1.82154C11.827 1.0577 13.1461 1.0577 14.0424 1.82154Z"
				stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
		</svg>
		<?php echo esc_html__('Gratis verzending & 30 dagen bedenktijd', 'slimgebruikt'); ?>
	</p>
	<?php
});

if (!function_exists('slimgebruikt_woocommerce_header_cart')) {
	/**
	 * Display Header Cart.
	 *
	 * @return void
	 */
	function slimgebruikt_woocommerce_header_cart()
	{
		if (is_cart()) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
		?>
		<ul id="site-header-cart" class="site-header-cart">
			<li class="<?php echo esc_attr($class); ?>">
				<?php slimgebruikt_woocommerce_cart_link(); ?>
			</li>
			<li>
				<?php
				$instance = array(
					'title' => '',
				);

				the_widget('WC_Widget_Cart', $instance);
				?>
			</li>
		</ul>
		<?php
	}
}
