<?php
/**
 * Home categories – blokken met tekst, afbeelding en knop
 *
 * @package slimgebruikt
 */
$acf = ( isset( $args ) && isset( $args['acf'] ) ) ? $args['acf'] : array();
$shop_url = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop/' );

$default_cats = array(
	array( 'title' => __( 'Refurbished iPhones', 'slimgebruikt' ), 'url' => add_query_arg( 'filter_product_cat', 'iphone', $shop_url ), 'style' => 'iphones', 'button_text' => __( 'Aanbod bekijken', 'slimgebruikt' ), 'image' => null ),
	array( 'title' => __( 'Refurbished iPads', 'slimgebruikt' ), 'url' => add_query_arg( 'filter_product_cat', 'ipad', $shop_url ), 'style' => 'ipads', 'button_text' => __( 'Aanbod bekijken', 'slimgebruikt' ), 'image' => null ),
	array( 'title' => __( 'Refurbished Apple kopen', 'slimgebruikt' ), 'url' => add_query_arg( 'filter_product_cat', 'apple', $shop_url ), 'style' => 'apple', 'button_text' => __( 'Aanbod bekijken', 'slimgebruikt' ), 'image' => null ),
	array( 'title' => __( 'Goede deals bekijken', 'slimgebruikt' ), 'url' => $shop_url, 'style' => 'deals', 'button_text' => __( 'Aanbod bekijken', 'slimgebruikt' ), 'image' => null ),
);

$items = ! empty( $acf['items'] ) ? $acf['items'] : $default_cats;
$valid_styles = array( 'iphones', 'ipads', 'apple', 'deals' );
?>
<section class="home-categories">
	<div class="home-categories__inner">
		<?php
		foreach ( $items as $i => $cat ) {
			$def   = $default_cats[ $i ] ?? $default_cats[0];
			$title = $cat['title'] ?? $def['title'];
			$url   = ! empty( $cat['url'] ) ? $cat['url'] : $def['url'];
			$style = in_array( $cat['style'] ?? '', $valid_styles ) ? $cat['style'] : $def['style'];
			$btn   = $cat['button_text'] ?? $def['button_text'];
			$img   = isset( $cat['image'] ) && is_array( $cat['image'] ) ? $cat['image'] : null;
			$class   = 'home-cat--' . $style;
			?>
			<a href="<?php echo esc_url( $url ); ?>" class="home-cat <?php echo esc_attr( $class ); ?>">
				<div class="home-cat__content">
					<h2 class="home-cat__title"><?php echo esc_html( $title ); ?></h2>
					<span class="home-cat__btn"><?php echo esc_html( $btn ); ?></span>
				</div>
				<div class="home-cat__image" aria-hidden="true">
					<?php if ( ! empty( $img['url'] ) ) : ?>
						<img src="<?php echo esc_url( $img['url'] ); ?>" alt="" loading="lazy">
					<?php endif; ?>
				</div>
			</a>
			<?php
		}
		?>
	</div>
</section>
