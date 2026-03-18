<?php
/**
 * slimgebruikt functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package slimgebruikt
 */

if (!defined('_S_VERSION')) {
	// Replace the version number of the theme on each release.
	define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function slimgebruikt_setup()
{
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on slimgebruikt, use a find and replace
	 * to change 'slimgebruikt' to the name of your theme in all the template files.
	 */
	load_theme_textdomain('slimgebruikt', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support('title-tag');

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support('post-thumbnails');

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__('Primary', 'slimgebruikt'),
			'footer-info' => esc_html__('Footer: Informatie', 'slimgebruikt'),
			'footer-links' => esc_html__('Footer: Snelle links', 'slimgebruikt'),
			'footer-products' => esc_html__('Footer: Refurbished producten', 'slimgebruikt'),
		)
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'slimgebruikt_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height' => 250,
			'width' => 250,
			'flex-width' => true,
			'flex-height' => true,
		)
	);
}
add_action('after_setup_theme', 'slimgebruikt_setup');

/**
 * Disable WordPress emoji conversion – gebruik native (Apple/system) emoji's
 */
add_action('init', function () {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_filter('the_content', 'wp_staticize_emoji');
	remove_filter('the_excerpt', 'wp_staticize_emoji');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}, 20);
add_filter('tiny_mce_plugins', function ($plugins) {
	return is_array($plugins) ? array_diff($plugins, array('wpemoji')) : array();
});

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function slimgebruikt_content_width()
{
	$GLOBALS['content_width'] = apply_filters('slimgebruikt_content_width', 640);
}
add_action('after_setup_theme', 'slimgebruikt_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function slimgebruikt_widgets_init()
{
	register_sidebar(
		array(
			'name' => esc_html__('Sidebar', 'slimgebruikt'),
			'id' => 'sidebar-1',
			'description' => esc_html__('Add widgets here.', 'slimgebruikt'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h2 class="widget-title">',
			'after_title' => '</h2>',
		)
	);
}
add_action('widgets_init', 'slimgebruikt_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function slimgebruikt_scripts()
{
	wp_enqueue_style('degular-typekit', 'https://use.typekit.net/dlg4wca.css', array(), null);
	wp_enqueue_style(
		'slimgebruikt-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	$style_version = filemtime(get_stylesheet_directory() . '/style.css') ?: _S_VERSION;
	wp_enqueue_style('slimgebruikt-style', get_stylesheet_uri(), array('degular-typekit', 'slimgebruikt-fonts'), $style_version);
	wp_style_add_data('slimgebruikt-style', 'rtl', 'replace');

	$theme_js = get_template_directory() . '/js/theme.js';
	$theme_version = file_exists($theme_js) ? filemtime($theme_js) : _S_VERSION;
	wp_enqueue_script('slimgebruikt-theme', get_template_directory_uri() . '/js/theme.js', array(), $theme_version, true);
	wp_enqueue_script('slimgebruikt-motion', get_template_directory_uri() . '/js/motion.js', array(), _S_VERSION, true);

	if (is_front_page() || (function_exists('is_shop') && is_shop())) {
		wp_enqueue_script('slimgebruikt-weekdeal-countdown', get_template_directory_uri() . '/js/weekdeal-countdown.js', array(), _S_VERSION, true);
	}
	if (is_front_page()) {
		$hs_js = get_template_directory() . '/js/hero-search.js';
		wp_enqueue_script('slimgebruikt-hero-search', get_template_directory_uri() . '/js/hero-search.js', array(), file_exists($hs_js) ? filemtime($hs_js) : _S_VERSION, true);
	}
	if (function_exists('is_shop') && (is_shop() || is_product_taxonomy())) {
		$sf_js = get_template_directory() . '/js/shop-filters.js';
		wp_enqueue_script('slimgebruikt-shop-filters', get_template_directory_uri() . '/js/shop-filters.js', array(), file_exists($sf_js) ? filemtime($sf_js) : _S_VERSION, true);
	}
	if ((is_front_page() || is_page_template('template-landing.php') || is_page_template('page-winkelwagen.php')) && class_exists('WooCommerce')) {
		wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), '8');
		wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), '8', true);
		wp_enqueue_script('slimgebruikt-bestsellers', get_template_directory_uri() . '/js/bestsellers-swiper.js', array('swiper'), _S_VERSION, true);
		wp_enqueue_script('slimgebruikt-products-swiper', get_template_directory_uri() . '/js/products-swiper.js', array('swiper'), _S_VERSION, true);
	}

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}

	if (is_page_template('page-hulp.php') || is_page_template('template-landing.php') || is_front_page() || is_singular('help_artikel')) {
		$landing_css = get_template_directory() . '/css/landing.css';
		$landing_ver = file_exists($landing_css) ? filemtime($landing_css) : _S_VERSION;
		wp_enqueue_style('slimgebruikt-landing', get_template_directory_uri() . '/css/landing.css', array('slimgebruikt-style'), $landing_ver);
		$landing_js = get_template_directory() . '/js/landing-motion.js';
		wp_enqueue_script('slimgebruikt-landing-motion', get_template_directory_uri() . '/js/landing-motion.js', array(), file_exists($landing_js) ? filemtime($landing_js) : _S_VERSION, true);
	}
}
add_action('wp_enqueue_scripts', 'slimgebruikt_scripts');

/**
 * Add type="module" to Motion script (ES modules).
 */
function slimgebruikt_script_module($tag, $handle, $src)
{
	if (in_array($handle, array('slimgebruikt-motion', 'slimgebruikt-landing-motion'), true)) {
		return str_replace('<script ', '<script type="module" ', $tag);
	}
	return $tag;
}
add_filter('script_loader_tag', 'slimgebruikt_script_module', 10, 3);

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if (class_exists('WooCommerce')) {
	require get_template_directory() . '/inc/woocommerce.php';
	require get_template_directory() . '/inc/shop-filters.php';
}

/**
 * Load ACF field groups.
 */
require get_template_directory() . '/inc/acf.php';
require get_template_directory() . '/inc/help-cpt.php';
require get_template_directory() . '/inc/help-functions.php';

/**
 * ACF Options Page (Theme Settings).
 * Runs on init to avoid "translation loaded too early" notice (WP 6.7+).
 */
add_action('init', function () {
	if (function_exists('acf_add_options_page')) {
		acf_add_options_page(
			array(
				'page_title' => __('Theme-instellingen', 'slimgebruikt'),
				'menu_title' => __('Theme-instellingen', 'slimgebruikt'),
				'menu_slug' => 'theme-settings',
				'capability' => 'edit_posts',
				'redirect' => false,
			)
		);
		acf_add_options_page(
			array(
				'page_title' => __('Hulp & Contact', 'slimgebruikt'),
				'menu_title' => __('Hulp & Contact', 'slimgebruikt'),
				'menu_slug' => 'hulp-contact',
				'capability' => 'edit_posts',
				'redirect' => false,
			)
		);
	}
}, 20);
require get_template_directory() . '/inc/acf-options.php';
