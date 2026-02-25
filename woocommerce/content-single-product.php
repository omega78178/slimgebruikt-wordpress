<?php
/**
 * Single product content – SlimGebruikt design
 *
 * @package slimgebruikt
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'product-detail', $product ); ?>>

	<div class="product-detail__top">
		<div class="product-detail__gallery">
			<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
		</div>
		<div class="product-detail__summary entry-summary">
			<?php do_action( 'woocommerce_single_product_summary' ); ?>
			<div class="product-detail__delivery">
				<p><?php esc_html_e( '2 werkdagen levertijd', 'slimgebruikt' ); ?></p>
				<p><?php esc_html_e( 'Gratis verzending & retourneren', 'slimgebruikt' ); ?></p>
			</div>
			<p class="product-detail__pay">
				<a href="#"><?php esc_html_e( 'Betaal veilig en snel achteraf', 'slimgebruikt' ); ?></a><br>
				<small><?php esc_html_e( 'Gratis retourneren binnen 30 dagen', 'slimgebruikt' ); ?></small>
			</p>
			<div class="product-detail__retour">
				<h3 class="product-detail__retour-title"><?php esc_html_e( '30 dagen gratis retourneren', 'slimgebruikt' ); ?></h3>
				<ul class="product-detail__retour-list">
					<li><?php esc_html_e( 'Regel de retourzending met slechts een paar klikken in je slimgebruikt-account.', 'slimgebruikt' ); ?></li>
					<li><?php esc_html_e( 'Stuur het toestel gratis terug binnen 30 dagen na ontvangst.', 'slimgebruikt' ); ?></li>
					<li><?php esc_html_e( 'Ontvang je geld terug binnen 3 werkdagen nadat de verkoper het toestel heeft ontvangen.', 'slimgebruikt' ); ?></li>
				</ul>
			</div>
			<p class="product-detail__guarantee">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
				<?php esc_html_e( 'Gratis verzending & 30 dagen bedenktijd', 'slimgebruikt' ); ?>
			</p>
		</div>
	</div>

	<?php
	// Over het product
	$about_title = sprintf( __( 'Over de %s', 'slimgebruikt' ), get_the_title() );
	$about_content = $product->get_description();
	if ( function_exists( 'get_field' ) ) {
		$about_acf = get_field( 'product_about_text', get_the_ID() );
		if ( $about_acf ) {
			$about_content = $about_acf;
		}
	}
	if ( $about_content ) :
		?>
		<section class="product-about">
			<h2 class="product-about__title"><?php echo esc_html( $about_title ); ?></h2>
			<div class="product-about__inner">
				<div class="product-about__image">
					<?php echo wp_kses_post( $product->get_image( 'woocommerce_single' ) ); ?>
				</div>
				<div class="product-about__content">
					<?php echo wp_kses_post( wpautop( $about_content ) ); ?>
					<ul class="product-about__bullets">
						<li><?php esc_html_e( 'Professioneel refurbished', 'slimgebruikt' ); ?></li>
						<li><?php esc_html_e( '2 jaar garantie*', 'slimgebruikt' ); ?></li>
						<li><?php esc_html_e( '30 dagen bedenktijd', 'slimgebruikt' ); ?></li>
					</ul>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	// Technische specificaties
	$specs = array();
	if ( function_exists( 'get_field' ) ) {
		$specs_acf = get_field( 'product_specs', get_the_ID() );
		if ( is_array( $specs_acf ) && ! empty( $specs_acf ) ) {
			foreach ( $specs_acf as $row ) {
				if ( ! empty( $row['label'] ) && ! empty( $row['value'] ) ) {
					$specs[] = array( 'label' => $row['label'], 'value' => $row['value'] );
				}
			}
		}
	}
	if ( empty( $specs ) ) {
		$specs[] = array( 'label' => __( 'Model', 'slimgebruikt' ), 'value' => $product->get_name() );
		if ( $product->has_attributes() ) {
			foreach ( $product->get_attributes() as $attr ) {
				if ( $attr->get_visible() ) {
					$val = $product->get_attribute( $attr->get_name() );
					if ( $val ) {
						$specs[] = array(
							'label' => wc_attribute_label( $attr->get_name() ),
							'value' => $val,
						);
					}
				}
			}
		}
	}
	if ( ! empty( $specs ) ) :
		?>
		<section class="product-specs">
			<h2 class="product-specs__title"><?php esc_html_e( 'Technische specificaties', 'slimgebruikt' ); ?></h2>
			<dl class="product-specs__list">
				<?php foreach ( $specs as $s ) : ?>
					<dt><?php echo esc_html( $s['label'] ); ?></dt>
					<dd><?php echo esc_html( $s['value'] ); ?></dd>
				<?php endforeach; ?>
			</dl>
		</section>
	<?php endif; ?>

	<section class="product-vaak-bekeken">
		<?php woocommerce_output_related_products(); ?>
	</section>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
