<?php
/**
 * Single Help Artikel – toont bewerkbare hulpcontent
 *
 * @package slimgebruikt
 */

get_header();

$article_id = get_the_ID();
$crumbs     = function_exists( 'slimgebruikt_help_breadcrumbs' ) ? slimgebruikt_help_breadcrumbs( $article_id ) : array();
$siblings    = function_exists( 'slimgebruikt_help_get_siblings' ) ? slimgebruikt_help_get_siblings( $article_id ) : array();
?>

	<main id="primary" class="site-main site-main--hulp site-main--help-artikel">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<?php if ( ! empty( $crumbs ) ) : ?>
				<nav class="help-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'slimgebruikt' ); ?>">
					<ol class="help-breadcrumbs__list">
						<?php foreach ( $crumbs as $i => $cr ) : ?>
							<li>
								<?php if ( ! empty( $cr['url'] ) ) : ?>
									<a href="<?php echo esc_url( $cr['url'] ); ?>"><?php echo esc_html( $cr['title'] ); ?></a>
								<?php else : ?>
									<span aria-current="page"><?php echo esc_html( $cr['title'] ); ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ol>
				</nav>
			<?php endif; ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'help-artikel' ); ?>>
				<header class="help-artikel__header">
					<h1 class="help-artikel__title"><?php the_title(); ?></h1>
					<?php
					$help_pages = get_pages( array( 'meta_key' => '_wp_page_template', 'meta_value' => 'page-hulp.php' ) );
					$help_url   = ! empty( $help_pages ) ? get_permalink( $help_pages[0] ) : home_url( '/' );
					?>
					<a href="<?php echo esc_url( $help_url ); ?>" class="help-artikel__back">
						← <?php esc_html_e( 'Terug naar overzicht', 'slimgebruikt' ); ?>
					</a>
				</header>
				<div class="help-artikel__content">
					<?php the_content(); ?>
				</div>
				<?php
				$siblings = function_exists( 'slimgebruikt_help_get_siblings' ) ? slimgebruikt_help_get_siblings( get_the_ID() ) : array();
				if ( ! empty( $siblings ) ) :
					?>
					<aside class="help-artikel__siblings">
						<h3 class="help-artikel__siblings-title"><?php esc_html_e( 'Lees ook', 'slimgebruikt' ); ?></h3>
						<ul class="help-artikel__siblings-list">
							<?php foreach ( $siblings as $sib ) :
								$sib_id = is_object( $sib ) ? $sib->ID : (int) $sib;
								if ( ! $sib_id ) continue;
								?>
								<li><a href="<?php echo esc_url( get_permalink( $sib_id ) ); ?>"><?php echo esc_html( get_the_title( $sib_id ) ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</aside>
				<?php endif; ?>
			</article>
			<?php
		endwhile;
		?>
	</main>

<?php
get_footer();
