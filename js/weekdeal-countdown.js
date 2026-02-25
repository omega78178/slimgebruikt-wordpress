/**
 * Weekdeal countdown timer
 */
(function () {
	function pad(n) {
		return String(n).padStart(2, '0');
	}
	function format(days, hrs, min, sec) {
		if (days > 0) {
			return 'Nog ' + days + (days === 1 ? ' dag' : ' dagen');
		}
		return pad(hrs) + ':' + pad(min) + ':' + pad(sec);
	}
	function tick(el, end) {
		var now = Date.now();
		var diff = Math.max(0, end - now);
		if (diff <= 0) {
			el.textContent = '00:00:00';
			return;
		}
		var d = Math.floor(diff / 86400000);
		var h = Math.floor((diff % 86400000) / 3600000);
		var m = Math.floor((diff % 3600000) / 60000);
		var s = Math.floor((diff % 60000) / 1000);
		el.textContent = format(d, h, m, s);
	}
	document.querySelectorAll('[data-countdown]').forEach(function (card) {
		var timer = card.querySelector('.hero__weekdeal-timer');
		if (!timer) return;
		var end = new Date(card.getAttribute('data-countdown')).getTime();
		if (isNaN(end)) return;
		tick(timer, end);
		var i = setInterval(function () {
			tick(timer, end);
			if (Date.now() >= end) clearInterval(i);
		}, 1000);
	});
})();
