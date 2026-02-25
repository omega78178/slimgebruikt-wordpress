/**
 * Mobile hamburger menu – open/close popup, scroll lock
 */
(function () {
	var toggle = document.querySelector(".header-nav-toggle");
	var panel = document.getElementById("mobile-nav-panel");
	var closeBtn = panel && panel.querySelector(".mobile-nav-panel__close");
	var backdrop = panel && panel.querySelector(".mobile-nav-panel__backdrop");

	if (!toggle || !panel) return;

	function open() {
		panel.classList.add("is-open");
		panel.setAttribute("aria-hidden", "false");
		toggle.setAttribute("aria-expanded", "true");
		toggle.setAttribute("aria-label", "Menu sluiten");
		document.body.style.overflow = "hidden";
	}

	function close() {
		panel.classList.remove("is-open");
		panel.setAttribute("aria-hidden", "true");
		toggle.setAttribute("aria-expanded", "false");
		toggle.setAttribute("aria-label", "Menu openen");
		document.body.style.overflow = "";
	}

	function toggleMenu() {
		if (panel.classList.contains("is-open")) {
			close();
		} else {
			open();
		}
	}

	toggle.addEventListener("click", toggleMenu);
	if (closeBtn) closeBtn.addEventListener("click", close);
	if (backdrop) backdrop.addEventListener("click", close);

	panel.addEventListener("keydown", function (e) {
		if (e.key === "Escape") close();
	});

	document.addEventListener("keydown", function (e) {
		if (e.key === "Escape" && panel.classList.contains("is-open")) close();
	});

	// Close on resize to desktop
	window.addEventListener("resize", function () {
		if (window.matchMedia("(min-width: 48em)").matches && panel.classList.contains("is-open")) {
			close();
		}
	});
})();
