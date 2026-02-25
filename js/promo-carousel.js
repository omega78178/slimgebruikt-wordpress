/**
 * Promo carousel – roterende teksten (header + footer trust)
 */
(function () {
	function initCarousel(containerClass, textClass, dataId, hasButtons) {
		var el = document.querySelector(containerClass);
		if (!el) return;
		var textEl = el.querySelector(textClass);
		var data = el.getAttribute("data-texts") || (document.getElementById(dataId) && document.getElementById(dataId).textContent);
		if (!textEl || !data) return;
		var texts;
		try {
			texts = JSON.parse(data);
		} catch (e) {
			return;
		}
		if (!Array.isArray(texts) || texts.length < 2) return;

		var index = 0;
		var timeoutId;

		var direction = 1; // 1 = next (slide left), -1 = prev (slide right)

		var transition = "transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
		function show(idx) {
			var newIdx = ((idx % texts.length) + texts.length) % texts.length;
			var from = direction > 0 ? "100%" : "-100%";
			textEl.style.transition = "none";
			textEl.style.transform = "translate3d(" + from + ", 0, 0)";
			textEl.textContent = texts[newIdx];
			index = newIdx;
			textEl.offsetHeight; // reflow
			textEl.style.transition = transition;
			textEl.offsetHeight; // reflow so transition is applied before transform
			textEl.style.transform = "translate3d(0, 0, 0)";
		}

		function goNext() {
			direction = 1;
			textEl.style.transition = transition;
			textEl.style.transform = "translate3d(-100%, 0, 0)";
			setTimeout(function () { show(index + 1); }, 400);
			restartInterval();
		}

		function goPrev() {
			direction = -1;
			textEl.style.transition = transition;
			textEl.style.transform = "translate3d(100%, 0, 0)";
			setTimeout(function () { show(index - 1); }, 400);
			restartInterval();
		}

		function restartInterval() {
			clearTimeout(timeoutId);
			timeoutId = setTimeout(goNext, 4000);
		}

		if (hasButtons) {
			var prevBtn = el.querySelector(".header-promo__prev");
			var nextBtn = el.querySelector(".header-promo__next");
			if (prevBtn) prevBtn.addEventListener("click", goPrev);
			if (nextBtn) nextBtn.addEventListener("click", goNext);
		}
		timeoutId = setTimeout(goNext, 4000);
	}

	function init() {
		initCarousel(".header-promo", ".header-promo__text", "promo-texts", true);
		initCarousel(".footer-trust__carousel", ".footer-trust__carousel-text", "footer-trust-texts", false);
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", init);
	} else {
		init();
	}
})();
