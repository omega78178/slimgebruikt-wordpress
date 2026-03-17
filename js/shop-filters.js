/**
 * Shop filters – smooth accordion, mobile drawer, live filtering
 */
(function () {
	var DURATION = 350;

	/* ── Smooth accordion (like FAQ) ── */
	document.querySelectorAll('.shop-filter-group > .shop-filter__label').forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			e.stopPropagation();

			var group = btn.closest('.shop-filter-group');
			var wrap = group.querySelector('.shop-filter__options-wrap');
			if (!wrap) return;

			var isOpen = group.classList.contains('is-open');

			if (isOpen) {
				var h = wrap.scrollHeight;
				wrap.style.height = h + 'px';
				wrap.offsetHeight;
				wrap.style.height = '0';
				group.classList.remove('is-open');
				btn.setAttribute('aria-expanded', 'false');
				setTimeout(function () { wrap.style.height = ''; }, DURATION);
			} else {
				group.classList.add('is-open');
				btn.setAttribute('aria-expanded', 'true');
				wrap.style.overflow = 'hidden';
				var target = wrap.scrollHeight;
				wrap.style.height = '0';
				wrap.offsetHeight;
				wrap.style.height = target + 'px';
				setTimeout(function () {
					wrap.style.height = 'auto';
					wrap.style.overflow = '';
				}, DURATION);
			}
		});
	});

	/* ── Mobile drawer ── */
	var drawer = document.getElementById('shop-filters-drawer');
	var toggleBtn = document.querySelector('.shop-filter-bar__btn');
	var closeBtn = drawer ? drawer.querySelector('.shop-filters__close') : null;

	function openDrawer() {
		if (!drawer) return;
		drawer.classList.add('is-open');
		if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
		document.body.style.overflow = 'hidden';
	}
	function closeDrawer() {
		if (!drawer) return;
		drawer.classList.remove('is-open');
		if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
		document.body.style.overflow = '';
	}

	if (toggleBtn) toggleBtn.addEventListener('click', openDrawer);
	if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
	if (drawer) {
		drawer.addEventListener('click', function (e) {
			if (e.target === drawer && drawer.classList.contains('is-open')) closeDrawer();
		});
	}
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && drawer && drawer.classList.contains('is-open')) closeDrawer();
	});

	/* ── Live filtering (no submit button) ── */
	var form = document.querySelector('.shop-filters__form');
	if (!form) return;

	function buildFilterUrl() {
		var url = new URL(form.action, window.location.origin);

		form.querySelectorAll('.shop-filter-group[data-filter]').forEach(function (group) {
			var param = group.dataset.filter;
			if (param === 'price') return;
			var vals = [];
			group.querySelectorAll('input[type="checkbox"]:checked').forEach(function (cb) {
				vals.push(cb.value);
			});
			if (vals.length) {
				url.searchParams.set(param, vals.join(','));
			}
		});

		var minInput = form.querySelector('input[name="min_price"]');
		var maxInput = form.querySelector('input[name="max_price"]');
		var minDefault = minInput ? minInput.getAttribute('min') : '';
		var maxDefault = maxInput ? maxInput.getAttribute('max') : '';

		if (minInput && minInput.value && minInput.value !== minDefault) {
			url.searchParams.set('min_price', minInput.value);
		}
		if (maxInput && maxInput.value && maxInput.value !== maxDefault) {
			url.searchParams.set('max_price', maxInput.value);
		}

		return url.toString();
	}

	function navigate() {
		window.location.href = buildFilterUrl();
	}

	form.querySelectorAll('input[type="checkbox"]').forEach(function (cb) {
		cb.addEventListener('change', navigate);
	});

	var priceTimer;
	form.querySelectorAll('.shop-filter__price-input').forEach(function (input) {
		input.addEventListener('input', function () {
			clearTimeout(priceTimer);
			priceTimer = setTimeout(navigate, 800);
		});
	});

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		navigate();
	});
})();
