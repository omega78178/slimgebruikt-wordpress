<?php
/**
 * Home image + text block
 *
 * @package slimgebruikt
 */
$acf = ( isset( $args ) && isset( $args['acf'] ) ) ? $args['acf'] : array();
$img  = $acf['image'] ?? null;
$title = $acf['title'] ?? '';
$content = $acf['content'] ?? '';
$side  = ( $acf['image_side'] ?? 'left' ) === 'right' ? 'right' : 'left';

if ( empty( $img ) && empty( $title ) && empty( $content ) ) {
	return;
}
?>
<section class="home-image-text home-image-text--<?php echo esc_attr( $side ); ?>">
	<div class="home-image-text__inner">
		<?php if ( ! empty( $img ) && is_array( $img ) && ! empty( $img['url'] ) ) : ?>
			<div class="home-image-text__media">
				<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ?? '' ); ?>" loading="lazy">
			</div>
		<?php endif; ?>
		<div class="home-image-text__content">
			<?php if ( $title ) : ?>
				<h2 class="home-image-text__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $content ) : ?>
				<div class="home-image-text__body"><?php echo wp_kses_post( $content ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
