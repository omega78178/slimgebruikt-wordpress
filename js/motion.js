/**
 * Motion.dev – smooth animations for Mobistock
 * @see https://motion.dev/docs
 */
import { animate, inView } from "https://cdn.jsdelivr.net/npm/motion@11/+esm";

const duration = 0.5;
const easing = [0.32, 0.72, 0, 1];

function initFadeUp() {
	inView("[data-motion='fade-up']", (el) => {
		animate(el, { opacity: [0, 1], y: [24, 0] }, { duration, ease: easing });
	}, { amount: 0.2 });
}

function initFadeIn() {
	inView("[data-motion='fade-in']", (el) => {
		animate(el, { opacity: [0, 1] }, { duration, ease: easing });
	}, { amount: 0.2 });
}

function initProductCards() {
	inView(".products .product", (el) => {
		animate(el, { opacity: [0, 1], y: [16, 0] }, { duration: 0.4, ease: easing });
	}, { amount: 0.1 });
}

function initVariationWrap() {
	document.addEventListener("slimgebruikt:variationWrapVisible", (e) => {
		const wrap = e.detail?.el;
		if (!wrap || window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
		wrap.style.opacity = "0";
		wrap.style.transform = "translateY(20px)";
		animate(wrap, { opacity: [0, 1], y: [20, 0] }, { duration: 0.45, ease: easing }).finished.then(() => {
			wrap.style.opacity = "";
			wrap.style.transform = "";
		});
	});
}

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", init);
} else {
	init();
}

function init() {
	initFadeUp();
	initFadeIn();
	initProductCards();
	initVariationWrap();
}
