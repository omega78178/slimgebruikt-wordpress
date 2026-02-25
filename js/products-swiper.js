/**
 * Products carousel – hele producten, geen afgesneden slides
 */
(function () {
	'use strict';

	function init() {
		const el = document.querySelector('.products-swiper');
		if (!el || !window.Swiper) return;

		const prev = el.closest('.products-section')?.querySelector('.products-section__btn--prev');
		const next = el.closest('.products-section')?.querySelector('.products-section__btn--next');

		const swiper = new window.Swiper('.products-swiper', {
			loop: true,
			loopedSlides: 12,
			slidesPerView: 2,
			spaceBetween: 0,
			slidesPerGroup: 2,
			speed: 400,
			grabCursor: true,
			navigation: prev && next ? { nextEl: next, prevEl: prev } : false,
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
