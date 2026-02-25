/**
 * Bestsellers carousel – hele producten, geen afgesneden slides
 */
(function () {
	'use strict';

	function init() {
		const el = document.querySelector('.bestsellers-swiper');
		if (!el || !window.Swiper) return;

		const swiper = new window.Swiper('.bestsellers-swiper', {
			loop: true,
			loopedSlides: 12,
			slidesPerView: 2,
			spaceBetween: 0,
			slidesPerGroup: 2,
			speed: 400,
			grabCursor: true,
			navigation: {
				nextEl: '#bestsellers-next',
				prevEl: '#bestsellers-prev',
			},
			breakpoints: {
				768: { slidesPerView: 4, slidesPerGroup: 4 },
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
