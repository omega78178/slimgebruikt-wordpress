<?php
/**
 * Help & Support – breadcrumbs, shortcode orders-link
 *
 * @package slimgebruikt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bepaal parent-categorie voor een help-artikel (uit ACF opties).
 *
 * @param int $article_id Post ID van help_artikel.
 * @return array|null [ 'title' => string, 'url' => string ] of null.
 */
function slimgebruikt_help_get_parent( $article_id ) {
	$categories = get_field( 'help_categories', 'option' );
	if ( ! is_array( $categories ) ) {
		return null;
	}
	foreach ( $categories as $cat ) {
		$art = $cat['article'] ?? null;
		$art_id = is_object( $art ) ? $art->ID : (int) $art;
		if ( $art_id && (int) $art_id === (int) $article_id ) {
			return array(
				'title' => $cat['title'] ?? get_the_title( $art_id ),
				'url'   => get_permalink( $art_id ),
			);
		}
		$items = $cat['items'] ?? array();
		foreach ( $items as $item ) {
			$item_art = $item['article'] ?? null;
			$item_id = is_object( $item_art ) ? $item_art->ID : (int) $item_art;
			if ( $item_id && (int) $item_id === (int) $article_id ) {
				$parent_art = $cat['article'] ?? null;
				$parent_id = is_object( $parent_art ) ? $parent_art->ID : (int) $parent_art;
				if ( $parent_id ) {
					return array(
						'title' => $cat['title'] ?? get_the_title( $parent_id ),
						'url'   => get_permalink( $parent_id ),
					);
				}
			}
		}
	}
	return null;
}

/**
 * Bepaal sibling-artikelen (andere items in dezelfde categorie).
 *
 * @param int $article_id Huidig artikel.
 * @return array WP_Post[]
 */
function slimgebruikt_help_get_siblings( $article_id ) {
	$parent = slimgebruikt_help_get_parent( $article_id );
	if ( ! $parent ) {
		return array();
	}
	$categories = get_field( 'help_categories', 'option' );
	if ( ! is_array( $categories ) ) {
		return array();
	}
	$siblings = array();
	foreach ( $categories as $cat ) {
		$parent_art = $cat['article'] ?? null;
		$parent_id  = is_object( $parent_art ) ? $parent_art->ID : (int) $parent_art;
		if ( ! $parent_id || get_permalink( $parent_id ) !== $parent['url'] ) {
			continue;
		}
		$items = $cat['items'] ?? array();
		foreach ( $items as $item ) {
			$item_art = $item['article'] ?? null;
			$item_id  = is_object( $item_art ) ? $item_art->ID : (int) $item_art;
			if ( $item_id && (int) $item_id !== (int) $article_id ) {
				$post = get_post( $item_id );
				if ( $post && $post->post_status === 'publish' ) {
					$siblings[] = $post;
				}
			}
		}
		break;
	}
	return $siblings;
}

/**
 * Breadcrumbs voor help-pagina's.
 *
 * @param int|null $article_id Huidig artikel (null = hulppagina zelf).
 * @return array [ ['title'=>, 'url'=>] ]
 */
function slimgebruikt_help_breadcrumbs( $article_id = null ) {
	$help_pages = get_pages( array( 'meta_key' => '_wp_page_template', 'meta_value' => 'page-hulp.php' ) );
	$help_url   = ! empty( $help_pages ) ? get_permalink( $help_pages[0] ) : home_url( '/' );
	$help_title = ! empty( $help_pages ) ? get_the_title( $help_pages[0] ) : __( 'Hulp', 'slimgebruikt' );

	$crumbs = array(
		array( 'title' => $help_title, 'url' => $help_url ),
	);

	if ( ! $article_id ) {
		return $crumbs;
	}

	$parent = slimgebruikt_help_get_parent( $article_id );
	if ( $parent ) {
		$crumbs[] = $parent;
	}
	$crumbs[] = array( 'title' => get_the_title( $article_id ), 'url' => '' );
	return $crumbs;
}

/**
 * Vul Hulp & Contact automatisch bij eerste admin-bezoek.
 */
add_action( 'admin_init', function () {
	if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
		return;
	}
	$categories = get_field( 'help_categories', 'option' );
	if ( is_array( $categories ) && ! empty( $categories ) ) {
		return;
	}
	if ( get_transient( 'slimgebruikt_help_seeding' ) ) {
		return;
	}
	set_transient( 'slimgebruikt_help_seeding', 1, 30 );
	slimgebruikt_help_seed_content();
	delete_transient( 'slimgebruikt_help_seeding' );
} );

function slimgebruikt_help_seed_content() {
	$orders_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) . 'orders/' : '';

	// Categorie-artikelen + sub-items
	$cats_data = array(
		'Retouren & terugbetalingen' => array(
			'content' => "<h2>Hoe retourneren?</h2>\n<p>Je hebt 30 dagen bedenktijd. Ga naar Mijn account > Bestellingen en klik op 'Hulp krijgen' naast je bestelling. We sturen je een verzendlabel waarmee je het product gratis kunt terugsturen.</p>\n<h2>Wanneer krijg ik mijn geld terug?</h2>\n<p>Na ontvangst van het product controleren we het binnen 3 werkdagen. Je terugbetaling volgt binnen 5 werkdagen na goedkeuring.</p>",
			'items'   => array(
				array( 'Hoe vraag ik een terugbetaling aan?', "<p>Ga naar Mijn account > Bestellingen. Klik bij de betreffende bestelling op 'Retour aanvragen'. Vul het formulier in en stuur het product terug met het retouretiket. Na ontvangst wordt het bedrag binnen 14 dagen terugbetaald.</p>" ),
				array( 'Hoe annuleer ik mijn bestelling?', "<p>Zolang je bestelling nog niet verzonden is, kun je annuleren via Mijn account > Bestellingen. Is je bestelling al onderweg? Dan kun je deze ontvangen en retourneren binnen 30 dagen.</p>" ),
			),
		),
		'Contact & ondersteuning' => array(
			'content' => "<h2>Neem contact op</h2>\n<p>We helpen je graag! Voor vragen over een bestelling: ga naar Mijn account > Bestellingen en selecteer 'Hulp krijgen'. Voor overige vragen kun je ons bereiken via het contactformulier of e-mail.</p>\n<h2>Reactietijd</h2>\n<p>We reageren binnen 24 uur (ook in het weekend).</p>",
			'items'   => array(
				array( 'Hoe neem ik contact op?', "<p>Voor hulp bij een bestelling: ga naar Bestellingen en klik op 'Hulp krijgen'. Voor overige vragen: gebruik het contactformulier of stuur een e-mail. We reageren binnen 24 uur.</p>" ),
				array( 'Waar kan ik mijn bestelling volgen?', "<p>Na verzending ontvang je een e-mail met Track & Trace. Via Mijn account > Bestellingen zie je ook de actuele status van je bestelling.</p>" ),
			),
		),
		'Garantie & beschermingsplan' => array(
			'content' => "<h2>Onze garantie</h2>\n<p>Op alle refurbished producten krijg je standaard 24 maanden garantie. Dit dekt fabrieksfouten en technische gebreken.</p>\n<h2>Beschermingsplan</h2>\n<p>Uitgebreid je apparaat beschermen? Sluit een beschermingsplan af bij het afrekenen. Dit dekt ook schade door vallen en vloeistoffen.</p>",
			'items'   => array(
				array( 'Wat dekt de garantie?', "<p>De standaardgarantie dekt fabricagefouten en defecten die niet door eigen gebruik zijn ontstaan. Lees de volledige garantievoorwaarden in je orderbevestiging.</p>" ),
				array( 'Hoe claim ik mijn garantie?', "<p>Neem contact met ons op via Hulp krijgen bij je bestelling. We helpen je graag om een garantieclaim in te dienen.</p>" ),
			),
		),
		'Verzending' => array(
			'content' => "<h2>Verzending</h2>\n<p>We verzenden met PostNL of DHL. Standaard levering binnen 2-4 werkdagen in Nederland en België.</p>\n<h2>Track & Trace</h2>\n<p>Na verzending ontvang je een e-mail met Track & Trace. Zo volg je je pakket tot thuisbezorging.</p>",
			'items'   => array(
				array( 'Hoe lang duurt de levering?', "<p>Standaard 2-4 werkdagen in Nederland en België. Bij verzending naar een PostNL-punt kun je je pakket ophalen wanneer het jou uitkomt.</p>" ),
				array( 'Kan ik mijn pakket volgen?', "<p>Ja. Na verzending ontvang je een Track & Trace-code per e-mail.</p>" ),
			),
		),
		'Betaling' => array(
			'content' => "<h2>Veiligheid</h2>\n<p>Alle betalingen gaan via beveiligde verbinding. Wij slaan geen betaalgegevens op.</p>\n<h2>Betaalmethoden</h2>\n<p>We accepteren iDEAL, creditcard, Bancontact, PayPal en achteraf betalen met Klarna.</p>",
			'items'   => array(
				array( 'Welke betaalmethoden accepteren jullie?', "<p>iDEAL, creditcard (Visa, Mastercard, American Express), Bancontact, PayPal en Klarna (betaal later of in 3 delen).</p>" ),
				array( 'Is betalen veilig?', "<p>Ja. Alle betalingen gaan via beveiligde verbinding. We slaan geen betaalgegevens op.</p>" ),
			),
		),
		'Over refurbished kopen' => array(
			'content' => "<h2>Wat is refurbished?</h2>\n<p>Refurbished producten zijn professioneel nagekeken, gereinigd en getest. Ze voldoen aan strikte kwaliteitseisen en werken als nieuw.</p>\n<h2>Waarom refurbished?</h2>\n<p>Bespaar geld en verminder e-waste. Kwaliteit die je vertrouwt, tegen een eerlijke prijs.</p>",
			'items'   => array(
				array( 'Wat betekent de conditierating?', "<p>Elke conditie (zo goed als nieuw, uitstekend, goed, redelijk) is helder omschreven op de productpagina. Zo weet je precies wat je kunt verwachten.</p>" ),
				array( 'Zijn refurbished producten betrouwbaar?', "<p>Ja! Onze producten worden professioneel getest en gecontroleerd. Ze voldoen aan strikte kwaliteitseisen en je krijgt 24 maanden garantie.</p>" ),
			),
		),
		'Over mijn account' => array(
			'content' => "<p>In je account vind je je bestelgeschiedenis, status van bestellingen, retouraanvragen en persoonlijke gegevens. Log in om je bestellingen te beheren of hulp te krijgen bij een specifieke bestelling.</p>",
			'items'   => array(),
		),
	);

	$categories = array();
	foreach ( $cats_data as $title => $data ) {
		$post_id = wp_insert_post( array(
			'post_title'   => $title,
			'post_type'    => 'help_artikel',
			'post_status'  => 'publish',
			'post_content' => $data['content'],
		) );
		if ( is_wp_error( $post_id ) ) {
			continue;
		}
		$items = array();
		foreach ( $data['items'] as $item_data ) {
			$item_title   = $item_data[0];
			$item_content = $item_data[1] ?? '<p>Bewerk dit artikel in Help Artikelen.</p>';
			$item_post    = wp_insert_post( array(
				'post_title'   => $item_title,
				'post_type'    => 'help_artikel',
				'post_status'  => 'publish',
				'post_content' => $item_content,
			) );
			if ( ! is_wp_error( $item_post ) ) {
				$items[] = array( 'article' => $item_post, 'title' => '', 'url' => '' );
			}
		}
		$categories[] = array(
			'title'   => $title,
			'article' => $post_id,
			'items'   => $items,
		);
	}
	update_field( 'help_categories', $categories, 'option' );

	$faq = array(
		array( 'question' => 'Hoe vraag ik een terugbetaling aan?', 'answer' => '<p>Ga naar <strong>Mijn account</strong> > <strong>Bestellingen</strong> en klik op <strong>Hulp krijgen</strong> naast de betreffende bestelling. Kies voor retour en volg de stappen. Je ontvangt een gratis verzendlabel per e-mail.</p>' ),
		array( 'question' => 'Hoe annuleer ik mijn bestelling?', 'answer' => '<p>Zolang je bestelling nog niet verzonden is, kun je annuleren via Mijn account > Bestellingen. Is je bestelling al onderweg? Dan kun je deze ontvangen en retourneren binnen 30 dagen.</p>' ),
		array( 'question' => 'Hoe neem ik contact op?', 'answer' => '<p>Voor hulp bij een bestelling: ga naar Bestellingen en klik op Hulp krijgen. Voor overige vragen: gebruik het contactformulier of stuur een e-mail. We reageren binnen 24 uur.</p>' ),
		array( 'question' => 'Hoe lang duurt de garantie?', 'answer' => '<p>Op alle refurbished producten krijg je standaard <strong>24 maanden garantie</strong>. Je kunt bij het afrekenen ook een uitgebreid beschermingsplan afsluiten.</p>' ),
		array( 'question' => 'Is refurbished net zo goed als nieuw?', 'answer' => '<p>Ja! Onze producten worden professioneel getest, gereinigd en gecontroleerd. Ze voldoen aan strikte kwaliteitseisen en werken als nieuw. Je bespaart geld én vermindert e-waste.</p>' ),
	);
	update_field( 'help_faq_items', $faq, 'option' );
	update_field( 'help_faq_title', 'Veelgestelde vragen', 'option' );
	update_field( 'help_faq_intro', 'Heb je twijfels? Geen probleem. Daarom krijg je bij SlimGebruikt altijd garantie, bedenktijd en eerlijke prijzen.', 'option' );
	update_field( 'help_hero_title', 'Hulp nodig? Wij zijn er voor je.', 'option' );
	update_field( 'help_hero_subtitle', 'Heb je een vraag over een product, bestelling of garantie? Ons team helpt je graag verder.', 'option' );
	update_field( 'help_contact_email', 'support@slimgebruikt.nl', 'option' );
	update_field( 'help_contact_phone', '023 - 3487 89', 'option' );
	update_field( 'help_contact_hours', 'Werkdagen 9:00 - 17:00', 'option' );

	$contact_text = '<p>Om contact op te nemen over een bestelling, ga naar <strong>Bestellingen</strong> en selecteer <strong>Hulp krijgen</strong> naast de betreffende bestelling. We nemen binnen 1 werkdag contact met je op.</p>
<p><a href="[help_orders_link]" class="help-contact__btn" style="display:inline-flex;align-items:center;gap:0.5em;padding:0.75em 1.25em;background:#3A28CC;color:#fff;font-weight:600;text-decoration:none;border-radius:10px;margin-top:1em;">Hulp krijgen bij een bestelling <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M1.27539 18.2749L18.2754 1.2749M18.2754 1.2749H4.67539M18.2754 1.2749V14.8749" stroke="white" stroke-width="2.55" stroke-linecap="round" stroke-linejoin="round"/></svg></a></p>
<p>Voor al het andere, stuur ons een bericht via het contactformulier.</p>';
	update_field( 'help_contact_text', $contact_text, 'option' );
	if ( $orders_url ) {
		update_field( 'help_orders_url', $orders_url, 'option' );
	}
}

/**
 * Shortcode [help_orders_link] – URL naar bestellingen (WooCommerce).
 */
add_shortcode( 'help_orders_link', function () {
	$url = get_field( 'help_orders_url', 'option' );
	if ( $url ) {
		return esc_url( $url );
	}
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		return esc_url( wc_get_page_permalink( 'myaccount' ) . 'orders/' );
	}
	return '#';
} );
