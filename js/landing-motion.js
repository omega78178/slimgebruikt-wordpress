/**
 * Landing page – Motion animaties
 * @see https://motion.dev/docs
 */
import { animate, inView, stagger } from "https://cdn.jsdelivr.net/npm/motion@11/+esm";

const duration = 0.55;
const easing = [0.32, 0.72, 0, 1];

function initFrontPage() {
	if ( ! document.body.classList.contains( 'home' ) && ! document.body.classList.contains( 'front-page' ) ) return;
	if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) return;

	// Hero
	inView( '.hero', ( { target } ) => {
		animate( target.querySelector( '.hero__tagline' ), { opacity: [ 0, 1 ], y: [ 20, 0 ] }, { duration, ease: easing, delay: 0.2 } );
		animate( target.querySelector( '.hero__title' ), { opacity: [ 0, 1 ], y: [ 28, 0 ] }, { duration, ease: easing, delay: 0.35 } );
		animate( target.querySelector( '.hero__desc' ), { opacity: [ 0, 1 ], y: [ 20, 0 ] }, { duration, ease: easing, delay: 0.55 } );
		animate( target.querySelector( '.hero__cta' ), { opacity: [ 0, 1 ], y: [ 16, 0 ] }, { duration, ease: easing, delay: 0.7 } );
		animate( target.querySelector( '.hero__card--benefits' ), { opacity: [ 0, 1 ], y: [ 24, 0 ] }, { duration, ease: easing, delay: 0.4 } );
		animate( target.querySelector( '.hero__card--weekdeal' ), { opacity: [ 0, 1 ], scale: [ 0.96, 1 ] }, { duration: 0.8, ease: easing, delay: 0.6 } );
	}, { amount: 0.15, once: true } );

	// Categories
	inView( '.home-categories', ( { target } ) => {
		animate( target.querySelectorAll( '.home-cat' ), { opacity: [ 0, 1 ], y: [ 24, 0 ] }, { duration: 0.45, ease: easing, delay: stagger( 0.08, { start: 0.2 } ) } );
	}, { amount: 0.2, once: true } );

	// Products section
	inView( '.products-section__header', ( { target } ) => {
		animate( target, { opacity: [ 0, 1 ], y: [ 20, 0 ] }, { duration, ease: easing } );
	}, { amount: 0.2, once: true } );

	// Features
	inView( '.home-features', ( { target } ) => {
		animate( target.querySelector( '.home-features__header' ), { opacity: [ 0, 1 ], y: [ 20, 0 ] }, { duration, ease: easing } );
		animate( target.querySelectorAll( '.home-feature' ), { opacity: [ 0, 1 ], y: [ 16, 0 ] }, { duration: 0.45, ease: easing, delay: stagger( 0.08, { start: 0.25 } ) } );
	}, { amount: 0.2, once: true } );

	// Bestsellers
	inView( '.bestsellers-section__header', ( { target } ) => {
		animate( target, { opacity: [ 0, 1 ], y: [ 20, 0 ] }, { duration, ease: easing } );
	}, { amount: 0.2, once: true } );

	// Wysiwyg, CTA, Image-text
	inView( '.home-wysiwyg', ( { target } ) => {
		animate( target, { opacity: [ 0, 1 ], y: [ 20, 0 ] }, { duration, ease: easing } );
	}, { amount: 0.2, once: true } );
	inView( '.home-cta', ( { target } ) => {
		animate( target, { opacity: [ 0, 1 ], y: [ 20, 0 ] }, { duration, ease: easing } );
	}, { amount: 0.2, once: true } );
	inView( '.home-image-text', ( { target } ) => {
		animate( target, { opacity: [ 0, 1 ], y: [ 20, 0 ] }, { duration, ease: easing } );
	}, { amount: 0.2, once: true } );

	// FAQ
	inView( '.sg-faq-section .sg-faq', ( { target } ) => {
		animate( target.querySelector( '.sg-faq__header' ), { opacity: [ 0, 1 ], y: [ 20, 0 ] }, { duration, ease: easing, delay: 0.2 } );
		animate( target.querySelector( '.sg-faq__intro' ), { opacity: [ 0, 1 ], y: [ 12, 0 ] }, { duration, ease: easing, delay: 0.35 } );
		animate( target.querySelectorAll( '.sg-faq__item-wrap' ), { opacity: [ 0, 1 ], y: [ 16, 0 ] }, { duration: 0.4, ease: easing, delay: stagger( 0.08, { start: 0.5 } ) } );
		animate( target.querySelector( '.sg-faq__buttons' ), { opacity: [ 0, 1 ], y: [ 16, 0 ] }, { duration, ease: easing, delay: 0.85 } );
	}, { amount: 0.2, once: true } );
}

function initLandingPage() {
	if ( ! document.body.classList.contains( 'landing-page' ) ) return;
	if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) return;

	// Hero – gelaagde into
	inView( '.sg-hero', ( { target } ) => {
		const bgCircle = target.querySelector( '.sg-hero__bg-circle' );
		const imageWrap = target.querySelector( '.sg-hero__image-wrap' );
		const content = target.querySelector( '.sg-hero__content' );
		if ( bgCircle ) animate( bgCircle, { opacity: [ 0, 1 ], scale: [ 0.96, 1 ] }, { duration: 0.9, ease: easing, delay: 0.2 } );
		if ( imageWrap ) animate( imageWrap, { opacity: [ 0, 1 ], scale: [ 0.92, 1 ] }, { duration: 0.8, ease: easing, delay: 0.35 } );
		if ( content ) {
			animate( content.querySelector( '.sg-hero__headline' ), { opacity: [ 0, 1 ], y: [ 28, 0 ] }, { duration, ease: easing, delay: 0.35 } );
			animate( content.querySelector( '.sg-hero__sub' ), { opacity: [ 0, 1 ], y: [ 20, 0 ] }, { duration, ease: easing, delay: 0.55 } );
			animate( content.querySelectorAll( '.sg-usp-card' ), { opacity: [ 0, 1 ], y: [ 24, 0 ] }, { duration: 0.45, ease: easing, delay: stagger( 0.1, { start: 0.65 } ) } );
			animate( content.querySelector( '.sg-hero__buttons' ), { opacity: [ 0, 1 ], y: [ 16, 0 ] }, { duration, ease: easing, delay: 1.1 } );
		}
	}, { amount: 0.15, once: true } );

	// Waarom
	inView( '.sg-waarom', ( { target } ) => {
		animate( target.querySelector( '.sg-section-title' ), { opacity: [ 0, 1 ], y: [ 20, 0 ] }, { duration, ease: easing, delay: 0.2 } );
		animate( target.querySelector( '.sg-waarom__body' ), { opacity: [ 0, 1 ], y: [ 16, 0 ] }, { duration, ease: easing, delay: 0.4 } );
		animate( target.querySelector( '.sg-waarom__image' ), { opacity: [ 0, 1 ], x: [ 24, 0 ] }, { duration, ease: easing, delay: 0.5 } );
	}, { amount: 0.2, once: true } );

	// Slim sectie
	inView( '.sg-slim', ( { target } ) => {
		animate( target.querySelector( '.sg-slim__headline' ), { opacity: [ 0, 1 ], y: [ 24, 0 ] }, { duration, ease: easing, delay: 0.2 } );
		animate( target.querySelector( '.sg-slim__body' ), { opacity: [ 0, 1 ], y: [ 16, 0 ] }, { duration, ease: easing, delay: 0.4 } );
		animate( target.querySelectorAll( '.sg-slim__usp-item' ), { opacity: [ 0, 1 ], x: [ -16, 0 ] }, { duration: 0.4, ease: easing, delay: stagger( 0.08, { start: 0.6 } ) } );
	}, { amount: 0.2, once: true } );

	// FAQ
	inView( '.sg-faq', ( { target } ) => {
		animate( target.querySelector( '.sg-faq__header' ), { opacity: [ 0, 1 ], y: [ 20, 0 ] }, { duration, ease: easing, delay: 0.2 } );
		animate( target.querySelector( '.sg-faq__intro' ), { opacity: [ 0, 1 ], y: [ 12, 0 ] }, { duration, ease: easing, delay: 0.35 } );
		animate( target.querySelectorAll( '.sg-faq__item-wrap' ), { opacity: [ 0, 1 ], y: [ 16, 0 ] }, { duration: 0.4, ease: easing, delay: stagger( 0.08, { start: 0.5 } ) } );
		animate( target.querySelector( '.sg-faq__buttons' ), { opacity: [ 0, 1 ], y: [ 16, 0 ] }, { duration, ease: easing, delay: 0.85 } );
	}, { amount: 0.2, once: true } );

	// Products section header (binnen landing)
	inView( '.landing-main .products-section__header', ( { target } ) => {
		animate( target, { opacity: [ 0, 1 ], y: [ 20, 0 ] }, { duration, ease: easing, delay: 0.2 } );
	}, { amount: 0.2, once: true } );
}

const faqEasing = [0.32, 0.72, 0, 1];
const faqDuration = 0.35;
const prefersReducedMotion = () => window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

function initFaqAccordion() {
	document.querySelectorAll( '.sg-faq' ).forEach( ( faq ) => {
		faq.addEventListener( 'click', ( e ) => {
			const btn = e.target.closest( '[data-faq-toggle]' );
			if ( ! btn ) return;
			const wrap = btn.closest( '.sg-faq__item-wrap' );
			const answerWrap = wrap?.querySelector( '.sg-faq__answer-wrap' );
			const answerInner = answerWrap?.querySelector( '.sg-faq__answer' );
			if ( ! answerWrap || ! answerInner ) return;

			const isOpen = answerWrap.classList.contains( 'is-open' );
			const targetHeight = answerInner.scrollHeight;

			const instant = prefersReducedMotion();

			const closeItem = ( el ) => {
				const prev = el.previousElementSibling;
				if ( prev ) prev.setAttribute( 'aria-expanded', 'false' );
				if ( instant ) {
					el.classList.remove( 'is-open' );
					el.style.height = '';
					return;
				}
				const h = el.offsetHeight;
				animate( el, { height: [ h + 'px', '0px' ] }, { duration: faqDuration, ease: faqEasing } ).finished.then( () => {
					el.classList.remove( 'is-open' );
					el.style.height = '';
				} );
			};

			// Sluit andere open items
			faq.querySelectorAll( '.sg-faq__answer-wrap.is-open' ).forEach( ( el ) => {
				if ( el === answerWrap ) return;
				closeItem( el );
			} );

			if ( isOpen ) {
				btn.setAttribute( 'aria-expanded', 'false' );
				if ( instant ) {
					answerWrap.classList.remove( 'is-open' );
					answerWrap.style.height = '';
					return;
				}
				animate( answerWrap, { height: [ targetHeight + 'px', '0px' ] }, { duration: faqDuration, ease: faqEasing } ).finished.then( () => {
					answerWrap.classList.remove( 'is-open' );
					answerWrap.style.height = '';
				} );
			} else {
				answerWrap.classList.add( 'is-open' );
				btn.setAttribute( 'aria-expanded', 'true' );
				if ( instant ) {
					answerWrap.style.height = 'auto';
					return;
				}
				animate( answerWrap, { height: [ '0px', targetHeight + 'px' ] }, { duration: faqDuration, ease: faqEasing } ).finished.then( () => {
					answerWrap.style.height = 'auto';
				} );
			}
		} );
	} );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', () => {
		initLandingPage();
		initFrontPage();
		initFaqAccordion();
	} );
} else {
	initLandingPage();
	initFrontPage();
	initFaqAccordion();
}
