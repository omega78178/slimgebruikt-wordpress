/**
 * Bestsellers carousel – hele producten, vloeiende slider
 */
(function () {
	'use strict';

	function init() {
		const el = document.querySelector('.bestsellers-swiper');
		if (!el || !window.Swiper) return;

		const slideCount = el.querySelectorAll('.swiper-slide').length;

		const swiper = new window.Swiper('.bestsellers-swiper', {
			slidesPerView: 2,
			spaceBetween: 0,
			slidesPerGroup: 1,
			speed: 350,
			grabCursor: true,
			watchOverflow: true,
			loop: slideCount >= 6,
			loopedSlides: Math.min(slideCount, 12),
			navigation: {
				nextEl: '#bestsellers-next',
				prevEl: '#bestsellers-prev',
			},
			breakpoints: {
				480: { slidesPerView: 2, spaceBetween: 0 },
				640: { slidesPerView: 3, spaceBetween: 0 },
				768: { slidesPerView: 4, spaceBetween: 0 },
			},
		});

		window.addEventListener('resize', () => swiper?.update?.());
		setTimeout(() => swiper?.update?.(), 100);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
