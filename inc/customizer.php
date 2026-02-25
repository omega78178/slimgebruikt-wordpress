<?php
/**
 * slimgebruikt Theme Customizer
 *
 * @package slimgebruikt
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function slimgebruikt_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	/* Product kleurenkiezer */
	$wp_customize->add_section(
		'slimgebruikt_product_swatches',
		array(
			'title'    => __( 'Product kleurenkiezer', 'slimgebruikt' ),
			'priority' => 130,
		)
	);
	$wp_customize->add_setting(
		'slimgebruikt_swatch_bg',
		array(
			'default'           => '#120E17',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		'slimgebruikt_swatch_bg',
		array(
			'label'   => __( 'Achtergrond kleurenkiezer', 'slimgebruikt' ),
			'section' => 'slimgebruikt_product_swatches',
			'type'    => 'color',
		)
	);
	$wp_customize->add_setting(
		'slimgebruikt_swatch_selected',
		array(
			'default'           => '#4BC9F1',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		'slimgebruikt_swatch_selected',
		array(
			'label'   => __( 'Rand geselecteerde kleur', 'slimgebruikt' ),
			'section' => 'slimgebruikt_product_swatches',
			'type'    => 'color',
		)
	);
	$wp_customize->add_setting(
		'slimgebruikt_swatch_size',
		array(
			'default'           => 44,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'slimgebruikt_swatch_size',
		array(
			'label'       => __( 'Grootte cirkels (px)', 'slimgebruikt' ),
			'section'     => 'slimgebruikt_product_swatches',
			'type'        => 'number',
			'input_attrs' => array( 'min' => 32, 'max' => 64, 'step' => 2 ),
		)
	);
	$wp_customize->add_setting(
		'slimgebruikt_swatch_colors',
		array(
			'default'           => "zwart=#000000\nwit=#FFFFFF\nzilver=#C0C0C0\nspace-gray=#8E8E93\nspacezwart=#1C1C1E\nspacegrijs=#8E8E93\ngoud=#D4AF37\nroze=#FF2D55\nrood=#FF3B30\nblauw-titanium=#4A5568\npacific-blue=#0071E3\nsierra-blue=#5AC8FA\nmidnight=#191970\nmiddernacht=#191970\nnatuur-titanium=#A8A196\nwit-titanium=#F5F5F7\nwoestijn-titanium=#C4B8A8\nsterrenlicht=#FAF0E6\nlauw=#E8E4DC\npaars=#AF52DE\ndieppaars=#5856D6\ngroen=#34C759\ngeel=#FFD60A\norange=#FF9500\ngrafiet=#4A4A4A\nrosee-goud=#B76E79",
			'sanitize_callback' => function ( $v ) {
				$lines = array_filter( array_map( 'trim', explode( "\n", (string) $v ) ) );
				$out   = array();
				foreach ( $lines as $line ) {
					if ( preg_match( '/^([a-z0-9_-]+)\s*=\s*#([0-9a-fA-F]{3,6})$/', $line, $m ) ) {
						$out[] = $m[1] . '=#' . $m[2];
					}
				}
				return implode( "\n", $out );
			},
		)
	);
	$wp_customize->add_control(
		'slimgebruikt_swatch_colors',
		array(
			'label'       => __( 'Kleuren (slug=#hex per regel)', 'slimgebruikt' ),
			'description' => __( 'Bijv: zwart=#000000, wit=#FFFFFF. Overschrijft term meta.', 'slimgebruikt' ),
			'section'     => 'slimgebruikt_product_swatches',
			'type'        => 'textarea',
		)
	);

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'slimgebruikt_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'slimgebruikt_customize_partial_blogdescription',
			)
		);
	}
}
add_action( 'customize_register', 'slimgebruikt_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function slimgebruikt_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function slimgebruikt_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function slimgebruikt_customize_preview_js() {
	wp_enqueue_script( 'slimgebruikt-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), _S_VERSION, true );
}
add_action( 'customize_preview_init', 'slimgebruikt_customize_preview_js' );
