<?php
/**
 * Front Page template – ACF Flexible Content Page Builder
 *
 * @package slimgebruikt
 */

get_header();

$sections = get_field( 'homepage_sections' );
$default_order = array( 'hero', 'categories', 'products', 'features', 'bestsellers' );
?>

	<main id="primary" class="site-main site-main--front">
		<?php
		$part_map = array(
			'hero'       => 'hero',
			'categories' => 'home-categories',
			'features'   => 'home-features',
			'bestsellers'=> 'home-bestsellers',
			'products'   => 'home-products',
		);

		if ( ! empty( $sections ) && is_array( $sections ) ) {
			foreach ( $sections as $row ) {
				$layout = $row['acf_fc_layout'] ?? '';
				if ( 'wysiwyg' === $layout ) {
					$content = $row['content'] ?? '';
					if ( $content ) {
						echo '<section class="home-wysiwyg"><div class="home-wysiwyg__inner">' . wp_kses_post( $content ) . '</div></section>';
					}
				} elseif ( 'cta' === $layout ) {
					$cta = $row;
					if ( ! empty( $cta['title'] ) || ! empty( $cta['button_text'] ) ) {
						echo '<section class="home-cta"><div class="home-cta__inner">';
						if ( ! empty( $cta['title'] ) ) {
							echo '<h2 class="home-cta__title">' . esc_html( $cta['title'] ) . '</h2>';
						}
						if ( ! empty( $cta['text'] ) ) {
							echo '<p class="home-cta__text">' . wp_kses_post( nl2br( $cta['text'] ) ) . '</p>';
						}
						if ( ! empty( $cta['button_text'] ) && ! empty( $cta['url'] ) ) {
							echo '<a href="' . esc_url( $cta['url'] ) . '" class="home-cta__btn">' . esc_html( $cta['button_text'] ) . '</a>';
						}
						echo '</div></section>';
					}
				} elseif ( 'spacer' === $layout ) {
					$h = isset( $row['height'] ) ? (int) $row['height'] : 40;
					if ( $h > 0 ) {
						echo '<div class="home-spacer" style="height:' . esc_attr( $h ) . 'px"></div>';
					}
				} elseif ( 'image_text' === $layout ) {
					get_template_part( 'template-parts/home-image-text', null, array( 'acf' => $row ) );
				} elseif ( isset( $part_map[ $layout ] ) ) {
					get_template_part( 'template-parts/' . $part_map[ $layout ], null, array( 'acf' => $row ) );
				}
			}
		} else {
			foreach ( $default_order as $slug ) {
				get_template_part( 'template-parts/' . $part_map[ $slug ], null, array( 'acf' => array() ) );
			}
		}
		?>
	</main>

<?php
get_footer();
