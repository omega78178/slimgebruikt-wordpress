/**
 * Hero product search – cascading dropdowns + navigate to shop
 */
(function () {
	var form = document.querySelector('.hero__search');
	if (!form) return;

	var categories = [];
	try { categories = JSON.parse(form.dataset.categories || '[]'); } catch (e) {}

	var merkSelect  = form.querySelector('#hero-merk');
	var modelSelect = form.querySelector('#hero-model');

	if (merkSelect && modelSelect) {
		merkSelect.addEventListener('change', function () {
			var slug = merkSelect.value;
			var children = [];
			for (var i = 0; i < categories.length; i++) {
				if (categories[i].slug === slug) {
					children = categories[i].children || [];
					break;
				}
			}
			modelSelect.innerHTML = '<option value="">Model</option>';
			children.forEach(function (c) {
				var opt = document.createElement('option');
				opt.value = c.slug;
				opt.textContent = c.name;
				modelSelect.appendChild(opt);
			});
			modelSelect.disabled = children.length === 0;
		});
	}

	form.addEventListener('submit', function (e) {
		e.preventDefault();

		var url = new URL(form.action, window.location.origin);

		var merk   = merkSelect ? merkSelect.value : '';
		var model  = modelSelect ? modelSelect.value : '';
		var opslag = form.querySelector('#hero-opslag');
		var kleur  = form.querySelector('#hero-kleur');

		if (model) {
			url.searchParams.set('filter_model', model);
		} else if (merk) {
			url.searchParams.set('filter_model', merk);
		}

		if (opslag && opslag.value) {
			url.searchParams.set('filter_geheugen', opslag.value);
		}

		if (kleur && kleur.value) {
			url.searchParams.set('filter_kleur', kleur.value);
		}

		window.location.href = url.toString();
	});
})();
