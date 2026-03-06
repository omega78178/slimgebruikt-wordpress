/**
 * Product config – button-style variatie selectie
 * Sync custom buttons met verborgen select voor WooCommerce variations.
 */
(function () {
	'use strict';

	function showConditieUitlegBySlug(slug) {
		var all = document.querySelectorAll('.product-conditie-uitleg');
		var val = String(slug || '').trim().toLowerCase().replace(/_/g, '-');
		all.forEach(function (el) {
			var s = String(el.getAttribute('data-conditie-slug') || '').trim().toLowerCase().replace(/_/g, '-');
			var match = val && s && (s === val || s.replace(/-/g, '') === val.replace(/-/g, ''));
			el.style.display = match ? 'block' : 'none';
		});
	}

	function updateConditieUitleg() {
		var root = document.querySelector('.product-detail') || document;
		var wrap = root.querySelector('.product-conditie-uitleg-wrap');
		if (!wrap) return;
		var container = wrap.closest('.product-config__values');
		var sel = container ? container.querySelector('select[name^="attribute_"]') : null;
		showConditieUitlegBySlug(sel ? sel.value : '');
	}

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

	function findSelectByAttrName(form, attrName) {
		var names = [attrName, attrName.replace(/-/g, '_'), attrName.replace(/_/g, '-')];
		for (var i = 0; i < names.length; i++) {
			var n = names[i];
			var sel = form.querySelector('select[name="' + n + '"]') || form.querySelector('select[data-attribute_name="' + n + '"]');
			if (sel) return sel;
		}
		return null;
	}

	function updateCheckoutSummary(variation) {
		var summary = document.querySelector('.product-checkout-summary');
		if (!summary) return;
		var form = summary.closest('form.variations_form') || document.querySelector('form.variations_form');
		if (!form) return;
		var rows = summary.querySelectorAll('.product-checkout-summary__row[data-attribute-name]');
		var allSelects = form.querySelectorAll('select[name^="attribute_"]');
		rows.forEach(function (row, rowIdx) {
			var attrName = row.getAttribute('data-attribute-name');
			if (!attrName) return;
			var valSpan = row.querySelector('.product-checkout-summary__value');
			if (!valSpan) return;
			var slug = null;
			if (variation && variation.attributes) {
				slug = variation.attributes[attrName] || variation.attributes[attrName.replace(/-/g, '_')] || variation.attributes[attrName.replace(/_/g, '-')];
				if (!slug) {
					var attrNorm = attrName.replace(/_/g, '-').toLowerCase();
					var keys = Object.keys(variation.attributes);
					for (var k = 0; k < keys.length; k++) {
						if (keys[k].replace(/_/g, '-').toLowerCase() === attrNorm) {
							slug = variation.attributes[keys[k]];
							break;
						}
					}
				}
			}
			if (!slug) {
				var sel = findSelectByAttrName(form, attrName);
				if (!sel && allSelects[rowIdx]) sel = allSelects[rowIdx];
				if (sel) slug = sel.value;
			}
			var displayText = '–';
			if (slug) {
				var optsJson = row.getAttribute('data-options');
				if (optsJson) {
					try {
						var opts = JSON.parse(optsJson);
						displayText = opts[slug] || opts[String(slug).replace(/_/g, '-')] || opts[String(slug).replace(/-/g, '_')] || '–';
						var slugNorm = String(slug).toLowerCase().replace(/_/g, '-');
						if (displayText === '–') {
							for (var pk in opts) {
								if (String(pk).toLowerCase().replace(/_/g, '-') === slugNorm) {
									displayText = opts[pk];
									break;
								}
							}
						}
					} catch (e) {}
				}
			}
			valSpan.textContent = displayText;
		});
		var wrap = summary.closest('.single_variation_wrap');
		if (wrap) {
			var allFilled = allSelects.length > 0;
			for (var si = 0; si < allSelects.length; si++) {
				if (!allSelects[si].value) { allFilled = false; break; }
			}
			var shouldShow = allFilled || (variation && variation.attributes && Object.keys(variation.attributes).length);
			if (shouldShow) {
				if (!wrap.classList.contains('is-visible')) {
					wrap.classList.add('is-visible');
					wrap.dispatchEvent(new CustomEvent('slimgebruikt:variationWrapVisible', { detail: { el: wrap }, bubbles: true }));
				}
			} else {
				wrap.classList.remove('is-visible');
			}
		}
	}

	function onShowVariation(e, variation) {
		var wrap = document.querySelector('.single_variation_wrap');
		var hasVariation = !!(variation && (variation.variation_id || (variation.attributes && Object.keys(variation.attributes).length)));
		if (wrap) {
			var wasVisible = wrap.classList.contains('is-visible');
			wrap.classList.toggle('is-visible', hasVariation);
			if (hasVariation && !wasVisible) {
				wrap.dispatchEvent(new CustomEvent('slimgebruikt:variationWrapVisible', { detail: { el: wrap }, bubbles: true }));
			}
		}
		if (variation && variation.attributes) {
			updateConfigSummary(variation.attributes);
		} else {
			updateConfigSummary();
		}
		updateCheckoutSummary(variation);
		updateConditieUitleg();
	}

	document.addEventListener('change', function (e) {
		var form = e.target.closest && e.target.closest('form.variations_form');
		if (form && (e.target.classList && e.target.classList.contains('product-config__select') || (e.target.name && e.target.name.indexOf('attribute_') === 0))) {
			updateAvailabilityForAllAttributes(form);
			updateConfigSummary();
			updateCheckoutSummary(null);
			updateConditieUitleg();
		}
	});

	function getFormSelections(form) {
		var sel = {};
		(form.querySelectorAll('select[name^="attribute_"]') || []).forEach(function (s) {
			var name = s.getAttribute('name') || s.getAttribute('data-attribute_name') || '';
			if (name && s.value) sel[name] = s.value;
		});
		return sel;
	}

	function optionHasInStockVariation(variations, attrKey, optionVal, currentSelections) {
		var optNorm = String(optionVal || '').toLowerCase().trim().replace(/_/g, '-');
		for (var i = 0; i < variations.length; i++) {
			var v = variations[i];
			var attrs = v.attributes || {};
			var vVal = attrs[attrKey];
			if (vVal === undefined) {
				var keys = Object.keys(attrs);
				for (var k = 0; k < keys.length; k++) {
					if (keys[k].indexOf(attrKey) !== -1 || attrKey.indexOf(keys[k]) !== -1) {
						vVal = attrs[keys[k]];
						break;
					}
				}
			}
			var vNorm = vVal ? String(vVal).toLowerCase().trim().replace(/_/g, '-') : '';
			if (vNorm !== optNorm && vNorm.replace(/-/g, '') !== optNorm.replace(/-/g, '')) continue;
			if (!v.is_in_stock) continue;
			var match = true;
			for (var selKey in currentSelections) {
				if (selKey === attrKey || !currentSelections[selKey]) continue;
				var selVal = String(currentSelections[selKey]).toLowerCase().trim();
				var aVal = attrs[selKey];
				if (aVal === undefined) {
					var kk = Object.keys(attrs);
					for (var k = 0; k < kk.length; k++) {
						if (kk[k].indexOf(selKey) !== -1) { aVal = attrs[kk[k]]; break; }
					}
				}
				var aNorm = aVal ? String(aVal).toLowerCase().trim() : '';
				if (aNorm !== selVal && aNorm.replace(/-/g, '') !== selVal.replace(/-/g, '')) {
					match = false;
					break;
				}
			}
			if (match) return true;
		}
		return false;
	}

	function updateAvailabilityForAllAttributes(form) {
		var json = form.getAttribute('data-all_variations');
		if (!json) return;
		var variations;
		try { variations = JSON.parse(json); } catch (e) { return; }
		if (!Array.isArray(variations)) return;
		var selects = form.querySelectorAll('select[name^="attribute_"]');
		var currentSelections = getFormSelections(form);
		selects.forEach(function (select) {
			var attrKey = select.getAttribute('name') || select.getAttribute('data-attribute_name') || '';
			if (!attrKey) return;
			var container = select.closest('.product-config__values') || select.closest('.product-config__group');
			var sid = select.id || '';
			var blocks = container ? (container.querySelector('.product-conditie-blocks[data-select-id="' + sid + '"]') || container.querySelector('.product-batterij-blocks[data-select-id="' + sid + '"]') || container.querySelector('.product-config__buttons[data-select-id="' + sid + '"]') || container.querySelector('.product-conditie-blocks') || container.querySelector('.product-batterij-blocks') || container.querySelector('.product-config__buttons')) : null;
			var btns = blocks ? blocks.querySelectorAll('button[data-value]') : [];
			btns.forEach(function (btn) {
				var val = btn.getAttribute('data-value');
				var inStock = optionHasInStockVariation(variations, attrKey, val, currentSelections);
				btn.classList.toggle('is-out-of-stock', !inStock);
				btn.disabled = !inStock;
				btn.setAttribute('aria-disabled', inStock ? 'false' : 'true');
			});
			for (var i = 0; i < select.options.length; i++) {
				var opt = select.options[i];
				if (!opt.value) continue;
				var inStock = optionHasInStockVariation(variations, attrKey, opt.value, currentSelections);
				opt.disabled = !inStock;
				var txt = (opt.textContent || '').replace(/\s*\([^)]*[Uu]itverkocht[^)]*\)\s*/gi, '').trim();
				opt.textContent = inStock ? txt : txt + ' (Uitverkocht)';
			}
			if (select.value && select.options[select.selectedIndex] && select.options[select.selectedIndex].disabled) {
				select.value = '';
				select.dispatchEvent(new Event('change', { bubbles: true }));
			}
		});
	}

	function onProductConfigClick(e) {
		var btn = e.target.closest('.product-config__buttons button, .product-conditie-block, .product-batterij-block');
		if (!btn) return;
		if (btn.disabled || btn.classList.contains('is-out-of-stock')) return;
		var btns = btn.closest('.product-config__buttons, .product-conditie-blocks, .product-batterij-blocks');
		if (!btns) return;
		var val = btn.getAttribute('data-value');
		if ((btns.classList.contains('product-conditie-blocks') || btns.classList.contains('product-batterij-blocks')) && val) {
			showConditieUitlegBySlug(val);
		}
		var selectId = btns.getAttribute('data-select-id');
		var select = selectId ? document.getElementById(selectId) : null;
		if (!select) {
			var container = btns.closest('.product-config__values') || btns.closest('.product-config__group');
			select = container ? container.querySelector('.product-config__select, select[name^="attribute_"]') : null;
		}
		if (!select) return;
		e.preventDefault();
		select.value = val;
		select.dispatchEvent(new Event('change', { bubbles: true }));
		(btns.querySelectorAll('button') || []).forEach(function (b) {
			b.classList.toggle('is-selected', b === btn);
		});
		updateConfigSummary();
		updateCheckoutSummary(null);
		if (!btns.classList.contains('product-conditie-blocks')) updateConditieUitleg();
	}
	document.addEventListener('click', onProductConfigClick, true);

	function syncAll() {
		var root = document.querySelector('.product-detail') || document;
		root.querySelectorAll('.product-config__buttons, .product-conditie-blocks, .product-batterij-blocks').forEach(function (btns) {
			var selectId = btns.getAttribute('data-select-id');
			var select = selectId ? document.getElementById(selectId) : null;
			if (!select) {
				var group = btns.closest('.product-config__group');
				select = group ? group.querySelector('.product-config__select') : null;
			}
			if (!select) return;
			var val = select.value;
			(btns.querySelectorAll('button') || []).forEach(function (b) {
				b.classList.toggle('is-selected', b.getAttribute('data-value') === val);
			});
		});
	}

	function init() {
		var form = document.querySelector('form.variations_form');
		if (!form) return;
		var rootEl = document.querySelector('.product-detail') || form.closest('.product') || form;
		var selects = rootEl ? rootEl.querySelectorAll('.product-config__select') : form.querySelectorAll('.product-config__select');
		if (selects && selects.length) {
			selects.forEach(function (select) {
				select.addEventListener('change', function () {
					syncAll();
					updateAvailabilityForAllAttributes(form);
					updateCheckoutSummary(null);
				});
			});
		}
		syncAll();
		updateAvailabilityForAllAttributes(form);
		updateConfigSummary();
		updateCheckoutSummary(null);
		updateConditieUitleg();
		setTimeout(function () {
			updateAvailabilityForAllAttributes(form);
			syncAll();
		}, 100);
		setTimeout(function () {
			updateAvailabilityForAllAttributes(form);
		}, 500);
		if (typeof jQuery !== 'undefined') {
			jQuery(document.body).on('found_variation show_variation', 'form.variations_form', onShowVariation);
			jQuery(document.body).on('reset_data hide_variation', 'form.variations_form', function () {
				var w = document.querySelector('.single_variation_wrap');
				if (w) w.classList.remove('is-visible');
				var f = document.querySelector('form.variations_form');
				if (f) {
					updateConfigSummary();
					updateCheckoutSummary(null);
					updateAvailabilityForAllAttributes(f);
				}
			});
			jQuery(form).on('found_variation show_variation', onShowVariation);
		}
		var pollCount = 0;
		var pollId = setInterval(function () {
			updateConfigSummary();
			updateCheckoutSummary(null);
			if (form && form.getAttribute('data-all_variations')) updateAvailabilityForAllAttributes(form);
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
