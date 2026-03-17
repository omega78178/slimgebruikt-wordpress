/**
 * Product carousels – bestsellers + vaak bekeken (zelfde configuratie)
 */
(function () {
	'use strict';

	function initSwiper(selector, prevId, nextId) {
		const el = document.querySelector(selector);
		if (!el || !window.Swiper) return null;

		const slideCount = el.querySelectorAll('.swiper-slide').length;
		if (slideCount === 0) return null;

		return new window.Swiper(selector, {
			slidesPerView: 2,
			spaceBetween: 0,
			slidesPerGroup: 1,
			speed: 350,
			grabCursor: true,
			watchOverflow: false,
			observer: true,
			observeParents: true,
			loop: slideCount >= 2,
			loopedSlides: Math.max(slideCount, 8),
			navigation: {
				nextEl: '#' + nextId,
				prevEl: '#' + prevId,
			},
			breakpoints: {
				480: { slidesPerView: 2, spaceBetween: 0 },
				640: { slidesPerView: 3, spaceBetween: 0 },
				768: { slidesPerView: 4, spaceBetween: 0 },
			},
		});
	}

	function init() {
		const bestsellers = initSwiper('.bestsellers-swiper', 'bestsellers-prev', 'bestsellers-next');
		const vaakBekeken = initSwiper('.vaak-bekeken-swiper', 'vaak-bekeken-prev', 'vaak-bekeken-next');

		window.addEventListener('resize', () => {
			bestsellers?.update?.();
			vaakBekeken?.update?.();
		});
		setTimeout(() => {
			bestsellers?.update?.();
			vaakBekeken?.update?.();
		}, 150);
	}

	function run() {
		init();
		if (document.querySelector('.vaak-bekeken-swiper')) {
			window.addEventListener('load', () => {
				setTimeout(() => {
					const swiper = document.querySelector('.vaak-bekeken-swiper')?.swiper;
					if (swiper) swiper.update();
				}, 200);
			});
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', run);
	} else {
		run();
	}
})();
