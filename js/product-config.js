/**
 * Product config – button-style variatie selectie
 * Sync custom buttons met verborgen select voor WooCommerce variations.
 */
(function () {
	'use strict';

	function updateConfigSummary(variationAttrs) {
		var root = document.querySelector('.product-detail') || document;
		var summary = root.querySelector('.product-detail__header .product-config-summary') || root.querySelector('.product-config-summary');
		if (!summary) return;
		var attrsJson = summary.getAttribute('data-attributes');
		if (!attrsJson) return;
		var attrNames = [];
		try { attrNames = JSON.parse(attrsJson); } catch (e) { return; }
		var parts = [];
		attrNames.forEach(function (attrName) {
			var name1 = 'attribute_' + attrName;
			var name2 = 'attribute_' + attrName.replace(/_/g, '-');
			var slug = null;
			if (variationAttrs) {
				slug = variationAttrs[name1] || variationAttrs[name2];
				if (!slug) {
					var keys = Object.keys(variationAttrs);
					for (var k = 0; k < keys.length; k++) {
						if (keys[k] === name1 || keys[k] === name2 || keys[k].indexOf(attrName) !== -1 || keys[k].indexOf(attrName.replace(/_/g, '-')) !== -1) {
							slug = variationAttrs[keys[k]];
							break;
						}
					}
				}
			}
			var select = root.querySelector('.product-config__select[name="' + name1 + '"]') ||
				root.querySelector('.product-config__select[name="' + name2 + '"]') ||
				root.querySelector('.product-config__select[data-attribute_name="' + name1 + '"]') ||
				root.querySelector('.product-config__select[data-attribute_name="' + name2 + '"]');
			if (!select) {
				var allSelects = root.querySelectorAll('.product-config__select');
				for (var s = 0; s < allSelects.length; s++) {
					var sel = allSelects[s];
					var n = (sel.getAttribute('name') || sel.getAttribute('data-attribute_name') || '');
					if (n === name1 || n === name2 || n.indexOf(attrName) !== -1 || n.indexOf(attrName.replace(/_/g, '-')) !== -1) {
						select = sel;
						break;
					}
				}
			}
			if (!select) return;
			if (slug) {
				var opt = null;
				var slugNorm = String(slug).toLowerCase().trim();
				for (var i = 0; i < select.options.length; i++) {
					var ov = select.options[i].value;
					if (ov && (ov === slug || String(ov).toLowerCase().trim() === slugNorm)) {
						opt = select.options[i];
						break;
					}
				}
				if (opt) parts.push(opt.text);
			} else if (select.value) {
				var opt = select.options[select.selectedIndex];
				if (opt && opt.value) parts.push(opt.text);
			}
		});
		summary.textContent = parts.join(' en ');
	}

	function onShowVariation(e, variation) {
		if (variation && variation.attributes) {
			updateConfigSummary(variation.attributes);
		} else {
			updateConfigSummary();
		}
	}

	document.addEventListener('change', function (e) {
		if (e.target.classList && e.target.classList.contains('product-config__select')) {
			updateConfigSummary();
		}
	});

	document.addEventListener('click', function (e) {
		var btn = e.target.closest('.product-config__buttons button');
		if (!btn) return;
		var btns = btn.closest('.product-config__buttons');
		if (!btns) return;
		var selectId = btns.getAttribute('data-select-id');
		var select = selectId ? document.getElementById(selectId) : null;
		if (!select) {
			var group = btns.closest('.product-config__group');
			select = group ? group.querySelector('.product-config__select') : null;
		}
		if (!select) return;
		e.preventDefault();
		var val = btn.getAttribute('data-value');
		select.value = val;
		select.dispatchEvent(new Event('change', { bubbles: true }));
		btns.querySelectorAll('button').forEach(function (b) {
			b.classList.toggle('is-selected', b === btn);
		});
		updateConfigSummary();
	});

	function syncAll() {
		var root = document.querySelector('.product-detail') || document;
		root.querySelectorAll('.product-config__buttons').forEach(function (btns) {
			var selectId = btns.getAttribute('data-select-id');
			var select = selectId ? document.getElementById(selectId) : null;
			if (!select) {
				var group = btns.closest('.product-config__group');
				select = group ? group.querySelector('.product-config__select') : null;
			}
			if (!select) return;
			var val = select.value;
			btns.querySelectorAll('button').forEach(function (b) {
				b.classList.toggle('is-selected', b.getAttribute('data-value') === val);
			});
		});
	}

	function init() {
		var form = document.querySelector('.product-detail .variations_form');
		if (!form) return;
		var rootEl = document.querySelector('.product-detail');
		if (rootEl) {
			rootEl.querySelectorAll('.product-config__select').forEach(function (select) {
				select.addEventListener('change', syncAll);
			});
		}
		syncAll();
		updateConfigSummary();
		if (typeof jQuery !== 'undefined') {
			jQuery(document.body).on('found_variation show_variation', 'form.variations_form', onShowVariation);
			jQuery(document.body).on('reset_data', 'form.variations_form', function () {
				updateConfigSummary();
			});
			jQuery(form).on('found_variation show_variation', onShowVariation);
		}
		var pollCount = 0;
		var pollId = setInterval(function () {
			updateConfigSummary();
			pollCount++;
			if (pollCount > 50) clearInterval(pollId);
		}, 200);
	}
	if (typeof jQuery !== 'undefined') {
		jQuery(init);
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
