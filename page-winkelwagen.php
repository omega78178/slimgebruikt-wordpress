<?php
/**
 * Template Name: Winkelwagen
 *
 * Cart page template – renders cart content followed by the bestsellers section.
 *
 * @package slimgebruikt
 */

get_header();
?>

<main id="primary" class="site-main">

    <?php
    while (have_posts()):
        the_post();

        get_template_part('template-parts/content', 'page');

    endwhile;
    ?>

    <?php get_template_part('template-parts/home-bestsellers'); ?>

</main><!-- #main -->

<?php
get_footer();
