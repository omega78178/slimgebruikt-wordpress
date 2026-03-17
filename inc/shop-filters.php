<?php
/**
 * Shop filters – query handling + render helpers
 *
 * @package slimgebruikt
 */

defined( 'ABSPATH' ) || exit;

/**
 * Filter the main shop/category query based on URL params.
 */
add_action( 'woocommerce_product_query', 'slimgebruikt_shop_filter_query' );
function slimgebruikt_shop_filter_query( $q ) {
	$tax_query = $q->get( 'tax_query' ) ?: array();

	$filters = array(
		'filter_conditie' => 'pa_optische-toestand',
		'filter_model'    => 'product_cat',
		'filter_geheugen' => 'pa_opslagruimte',
		'filter_kleur'    => 'pa_kleur',
	);

	foreach ( $filters as $param => $taxonomy ) {
		if ( empty( $_GET[ $param ] ) ) {
			continue;
		}
		$slugs = array_map( 'sanitize_title', explode( ',', wp_unslash( $_GET[ $param ] ) ) );
		$tax_query[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => $slugs,
			'operator' => 'IN',
		);
	}

	if ( ! empty( $tax_query ) ) {
		$tax_query['relation'] = 'AND';
		$q->set( 'tax_query', $tax_query );
	}

	// Price range
	$min = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : '';
	$max = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : '';
	if ( $min !== '' || $max !== '' ) {
		$meta = $q->get( 'meta_query' ) ?: array();
		$price_q = array(
			'key'     => '_price',
			'type'    => 'NUMERIC',
		);
		if ( $min !== '' && $max !== '' ) {
			$price_q['value']   = array( $min, $max );
			$price_q['compare'] = 'BETWEEN';
		} elseif ( $min !== '' ) {
			$price_q['value']   = $min;
			$price_q['compare'] = '>=';
		} else {
			$price_q['value']   = $max;
			$price_q['compare'] = '<=';
		}
		$meta[] = $price_q;
		$q->set( 'meta_query', $meta );
	}
}

/**
 * Get WC price range for published products.
 */
function slimgebruikt_get_price_range() {
	global $wpdb;
	$row = $wpdb->get_row(
		"SELECT MIN( CAST( meta_value AS DECIMAL(10,2) ) ) AS min_price,
		        MAX( CAST( meta_value AS DECIMAL(10,2) ) ) AS max_price
		 FROM {$wpdb->postmeta} pm
		 JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		 WHERE pm.meta_key = '_price'
		   AND p.post_type = 'product'
		   AND p.post_status = 'publish'
		   AND pm.meta_value != ''
		   AND pm.meta_value > 0"
	);
	return array(
		'min' => $row ? floor( (float) $row->min_price ) : 0,
		'max' => $row ? ceil( (float) $row->max_price ) : 1000,
	);
}

/**
 * Render the shop filter sidebar.
 */
function slimgebruikt_render_shop_filters( $shop_url ) {
	$filters = array(
		array(
			'label'    => __( 'Conditie', 'slimgebruikt' ),
			'param'    => 'filter_conditie',
			'taxonomy' => 'pa_optische-toestand',
			'type'     => 'checkbox',
		),
		array(
			'label'    => __( 'Model', 'slimgebruikt' ),
			'param'    => 'filter_model',
			'taxonomy' => 'product_cat',
			'type'     => 'checkbox',
		),
		array(
			'label'    => __( 'Geheugen', 'slimgebruikt' ),
			'param'    => 'filter_geheugen',
			'taxonomy' => 'pa_opslagruimte',
			'type'     => 'checkbox',
		),
		array(
			'label'    => __( 'Kleur', 'slimgebruikt' ),
			'param'    => 'filter_kleur',
			'taxonomy' => 'pa_kleur',
			'type'     => 'swatch',
		),
	);

	$price_range  = slimgebruikt_get_price_range();
	$cur_min      = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : $price_range['min'];
	$cur_max      = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : $price_range['max'];
	?>
	<form class="shop-filters__form" action="<?php echo esc_url( $shop_url ); ?>" method="get">

		<?php foreach ( $filters as $filter ) :
			$terms = get_terms( array(
				'taxonomy'   => $filter['taxonomy'],
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
			) );
			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				continue;
			}
			$active = ! empty( $_GET[ $filter['param'] ] )
				? array_map( 'sanitize_title', explode( ',', wp_unslash( $_GET[ $filter['param'] ] ) ) )
				: array();
			?>
		<?php $has_active = ! empty( $active ); ?>
		<div class="shop-filter-group<?php echo $has_active ? ' is-open' : ''; ?>" data-filter="<?php echo esc_attr( $filter['param'] ); ?>">
			<button type="button" class="shop-filter__label" aria-expanded="<?php echo $has_active ? 'true' : 'false'; ?>">
				<span class="shop-filter__label-text"><?php echo esc_html( $filter['label'] ); ?></span>
				<svg class="shop-filter__arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
			</button>
			<div class="shop-filter__options-wrap"<?php echo $has_active ? ' style="height:auto"' : ''; ?>>
				<div class="shop-filter__options<?php echo $filter['type'] === 'swatch' ? ' shop-filter__options--swatches' : ''; ?>">
					<?php foreach ( $terms as $term ) :
						$checked = in_array( $term->slug, $active, true );
						$id_attr = 'sf-' . $filter['param'] . '-' . $term->slug;
						?>
						<?php if ( $filter['type'] === 'swatch' ) : ?>
							<label class="shop-filter__swatch<?php echo $checked ? ' is-active' : ''; ?>" for="<?php echo esc_attr( $id_attr ); ?>" title="<?php echo esc_attr( $term->name ); ?>">
								<input type="checkbox" id="<?php echo esc_attr( $id_attr ); ?>"
									name="<?php echo esc_attr( $filter['param'] ); ?>[]"
									value="<?php echo esc_attr( $term->slug ); ?>"
									<?php checked( $checked ); ?>>
								<span class="shop-filter__swatch-dot" data-color="<?php echo esc_attr( $term->slug ); ?>"></span>
								<span class="shop-filter__swatch-name"><?php echo esc_html( $term->name ); ?></span>
							</label>
						<?php else : ?>
							<label class="shop-filter__checkbox<?php echo $checked ? ' is-active' : ''; ?>" for="<?php echo esc_attr( $id_attr ); ?>">
								<input type="checkbox" id="<?php echo esc_attr( $id_attr ); ?>"
									name="<?php echo esc_attr( $filter['param'] ); ?>[]"
									value="<?php echo esc_attr( $term->slug ); ?>"
									<?php checked( $checked ); ?>>
								<span class="shop-filter__check"></span>
								<span class="shop-filter__name"><?php echo esc_html( $term->name ); ?></span>
								<span class="shop-filter__count">(<?php echo esc_html( $term->count ); ?>)</span>
							</label>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php endforeach; ?>

		<?php /* Price range */ ?>
	<?php $price_active = isset( $_GET['min_price'] ) || isset( $_GET['max_price'] ); ?>
	<div class="shop-filter-group<?php echo $price_active ? ' is-open' : ''; ?>" data-filter="price">
		<button type="button" class="shop-filter__label" aria-expanded="<?php echo $price_active ? 'true' : 'false'; ?>">
			<span class="shop-filter__label-text"><?php esc_html_e( 'Prijs', 'slimgebruikt' ); ?></span>
			<svg class="shop-filter__arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
		</button>
		<div class="shop-filter__options-wrap"<?php echo $price_active ? ' style="height:auto"' : ''; ?>>
			<div class="shop-filter__price-range">
				<div class="shop-filter__price-inputs">
					<label>
						<span class="screen-reader-text"><?php esc_html_e( 'Min prijs', 'slimgebruikt' ); ?></span>
						<span class="shop-filter__price-prefix">&euro;</span>
						<input type="number" name="min_price" class="shop-filter__price-input"
							value="<?php echo esc_attr( $cur_min ); ?>"
							min="<?php echo esc_attr( $price_range['min'] ); ?>"
							max="<?php echo esc_attr( $price_range['max'] ); ?>"
							step="1" placeholder="<?php echo esc_attr( $price_range['min'] ); ?>">
					</label>
					<span class="shop-filter__price-sep">&ndash;</span>
					<label>
						<span class="screen-reader-text"><?php esc_html_e( 'Max prijs', 'slimgebruikt' ); ?></span>
						<span class="shop-filter__price-prefix">&euro;</span>
						<input type="number" name="max_price" class="shop-filter__price-input"
							value="<?php echo esc_attr( $cur_max ); ?>"
							min="<?php echo esc_attr( $price_range['min'] ); ?>"
							max="<?php echo esc_attr( $price_range['max'] ); ?>"
							step="1" placeholder="<?php echo esc_attr( $price_range['max'] ); ?>">
					</label>
				</div>
			</div>
		</div>
	</div>

	<?php
	$any_active = false;
	foreach ( array( 'filter_conditie', 'filter_model', 'filter_geheugen', 'filter_kleur', 'min_price', 'max_price' ) as $p ) {
		if ( ! empty( $_GET[ $p ] ) ) { $any_active = true; break; }
	}
	if ( $any_active ) : ?>
		<div class="shop-filters__actions">
			<a href="<?php echo esc_url( $shop_url ); ?>" class="shop-filters__btn shop-filters__btn--reset"><?php esc_html_e( 'Filters wissen', 'slimgebruikt' ); ?></a>
		</div>
	<?php endif; ?>
	</form>

	<?php /* USP box met website logo + bewerkbare USPs */ ?>
	<div class="shop-usps-box">
		<div class="shop-usps-box__logo">
			<?php
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			if ( $custom_logo_id ) {
				echo wp_get_attachment_image( $custom_logo_id, 'medium', false, array( 'class' => 'shop-usps-box__logo-img' ) );
			} else {
				echo '<span class="shop-usps-box__logo-text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
			}
			?>
		</div>
		<?php
		$usps = function_exists( 'get_field' ) ? get_field( 'shop_sidebar_usps', 'option' ) : null;
		$defaults = array(
			array( 'text' => __( '2 jaar garantie*', 'slimgebruikt' ) ),
			array( 'text' => __( 'Laagste prijs', 'slimgebruikt' ) ),
			array( 'text' => __( 'Gratis verzending', 'slimgebruikt' ) ),
			array( 'text' => __( 'Gratis accessoires', 'slimgebruikt' ) ),
		);
		$usps = ! empty( $usps ) ? $usps : $defaults;
		?>
		<ul class="shop-usps-box__list">
			<?php foreach ( $usps as $usp ) :
				$icon_url = '';
				if ( ! empty( $usp['icon'] ) && is_array( $usp['icon'] ) ) {
					$icon_url = $usp['icon']['url'] ?? '';
				}
				$text = $usp['text'] ?? '';
				if ( empty( $text ) ) continue;
				?>
				<li class="shop-usps-box__item">
					<?php if ( $icon_url ) : ?>
						<img src="<?php echo esc_url( $icon_url ); ?>" alt="" class="shop-usps-box__icon" width="24" height="24" loading="lazy">
					<?php else : ?>
						<span class="shop-usps-box__icon-default">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
						</span>
					<?php endif; ?>
					<span class="shop-usps-box__text"><?php echo esc_html( $text ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}
