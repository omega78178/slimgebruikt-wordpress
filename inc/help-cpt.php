<?php
/**
 * Help-artikelen CPT – bewerkbare hulpcontent
 * Elke categorie en elk artikel is een post met eigen content.
 *
 * @package slimgebruikt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'slimgebruikt_register_help_artikel' );

function slimgebruikt_register_help_artikel() {
	register_post_type( 'help_artikel', array(
		'labels'       => array(
			'name'               => __( 'Help Artikelen', 'slimgebruikt' ),
			'singular_name'      => __( 'Help Artikel', 'slimgebruikt' ),
			'add_new'            => __( 'Nieuw artikel', 'slimgebruikt' ),
			'add_new_item'       => __( 'Nieuw help artikel', 'slimgebruikt' ),
			'edit_item'          => __( 'Bewerk artikel', 'slimgebruikt' ),
			'new_item'           => __( 'Nieuw artikel', 'slimgebruikt' ),
			'view_item'          => __( 'Bekijk artikel', 'slimgebruikt' ),
			'search_items'       => __( 'Zoek artikelen', 'slimgebruikt' ),
			'not_found'          => __( 'Geen artikelen gevonden', 'slimgebruikt' ),
			'not_found_in_trash' => __( 'Geen artikelen in prullenbak', 'slimgebruikt' ),
		),
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'hulp' ),
		'supports'     => array( 'title', 'editor', 'revisions' ),
		'menu_icon'    => 'dashicons-editor-help',
		'show_in_rest' => true,
	) );
}

/**
 * Auto-create help_artikel wanneer categorie-titel wordt ingevoerd zonder artikel.
 */
add_action( 'acf/save_post', 'slimgebruikt_help_auto_create_articles', 20 );

function slimgebruikt_help_auto_create_articles( $post_id ) {
	if ( $post_id !== 'options' ) {
		return;
	}

	$categories = get_field( 'help_categories', 'option' );
	if ( ! is_array( $categories ) ) {
		return;
	}

	$updated = false;
	foreach ( $categories as $i => $cat ) {
		$title   = trim( $cat['title'] ?? '' );
		$article = $cat['article'] ?? null;
		// article kan object (ACF return) of ID zijn
		if ( is_object( $article ) ) {
			$article = $article->ID;
		}
		if ( $title && ! $article ) {
			$post_id_new = wp_insert_post( array(
				'post_title'   => $title,
				'post_type'    => 'help_artikel',
				'post_status'  => 'publish',
				'post_content' => '',
			) );
			if ( ! is_wp_error( $post_id_new ) ) {
				$categories[ $i ]['article'] = $post_id_new;
				$updated = true;
			}
		}
	}
	if ( $updated ) {
		update_field( 'help_categories', $categories, 'option' );
	}
}
