<?php
/**
 * Weekdeal component – herbruikbaar (hero, shop)
 * Haalt weekdeal-data uit de homepage hero. Gebruik: get_template_part( 'template-parts/component-weekdeal', null, $args );
 * $args: acf = array (optioneel, voor hero-directe data), btn_text (optioneel)
 *
 * @package slimgebruikt
 */
$acf      = isset( $args['acf'] ) ? $args['acf'] : array();
$btn_text = isset( $args['btn_text'] ) ? $args['btn_text'] : null;

// Als geen acf doorgegeven: haal uit homepage hero (zelfde bron als homepage)
if ( empty( $acf['weekdeal_product'] ) && function_exists( 'get_field' ) ) {
	$front_id = (int) get_option( 'page_on_front' );
	$sections = $front_id ? get_field( 'homepage_sections', $front_id ) : null;
	if ( ! empty( $sections ) && is_array( $sections ) ) {
		foreach ( $sections as $row ) {
			if ( ( $row['acf_fc_layout'] ?? '' ) === 'hero' ) {
				$acf = $row;
				break;
			}
		}
	}
}

$weekdeal_title = $acf['weekdeal_title'] ?? 'Weekdeal 🔥';
$weekdeal_btn   = $btn_text ?: ( $acf['weekdeal_btn'] ?? __( 'Koop nu!', 'slimgebruikt' ) );
$weekdeal_product = $acf['weekdeal_product'] ?? null;

$product = null;
if ( $weekdeal_product && class_exists( 'WooCommerce' ) ) {
	$product = is_a( $weekdeal_product, 'WC_Product' ) ? $weekdeal_product : wc_get_product( is_object( $weekdeal_product ) ? $weekdeal_product->ID : $weekdeal_product );
}

$name  = $product && $product->is_visible() ? $product->get_name() : __( 'Accesoire: USB Oplaadkabel', 'slimgebruikt' );
$price = $product && $product->is_visible() ? $product->get_price_html() : '€1,00';
$link  = $product && $product->is_visible() ? $product->get_permalink() : ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) );
$image = null;
if ( $product && $product->is_visible() ) {
	$thumb_id = $product->get_image_id();
	if ( $thumb_id ) {
		$image = wp_get_attachment_image_url( $thumb_id, 'woocommerce_thumbnail' );
	}
}

$weekdeal_end    = function_exists( 'get_field' ) ? get_field( 'weekdeal_end_datetime', 'option' ) : '';
$weekdeal_end_iso = $weekdeal_end ? gmdate( 'c', strtotime( $weekdeal_end ) ) : '';
?>
<div class="hero__card hero__card--weekdeal" <?php echo $weekdeal_end_iso ? 'data-countdown="' . esc_attr( $weekdeal_end_iso ) . '"' : ''; ?>>
	<div class="hero__weekdeal-header">
		<h3 class="hero__weekdeal-title"><?php echo esc_html( $weekdeal_title ); ?></h3>
		<span class="hero__weekdeal-timer" aria-live="polite"><?php echo $weekdeal_end_iso ? '--:--:--' : '23:59:59'; ?></span>
	</div>
	<div class="hero__weekdeal-body">
		<div class="hero__weekdeal-info">
			<p class="hero__weekdeal-product"><?php echo esc_html( $name ); ?></p>
			<p class="hero__weekdeal-price"><?php echo wp_kses_post( $price ); ?></p>
			<a href="<?php echo esc_url( $link ); ?>" class="hero__weekdeal-btn"><?php echo esc_html( $weekdeal_btn ); ?></a>
		</div>
		<div class="hero__weekdeal-image">
			<?php if ( ! empty( $image ) ) : ?>
				<img src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy">
			<?php else : ?>
				<svg viewBox="0 0 80 80" fill="none" aria-hidden="true"><rect x="20" y="30" width="40" height="8" rx="2" stroke="currentColor" stroke-width="2"/><circle cx="30" cy="34" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M60 34h8M12 34h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
			<?php endif; ?>
		</div>
	</div>
</div>
