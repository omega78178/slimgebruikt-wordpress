/**
 * Checkout Performance: Debounce AJAX Updates
 *
 * Dit script throttles het aantal AJAX checkout-updates
 * om database/server belasting te reduceren en snellere UX te geven
 */

(function ($) {
  "use strict";

  const DEBOUNCE_DELAY = window.slimgebruikt_checkout_debounce_delay || 1500;
  let lastAjaxTime = 0;
  let pendingUpdate = null;

  /**
   * Originele AJAX trigger van WooCommerce
   * We gaan deze intercepten en debounce-n
   */
  $(document).on("change", 'input[name="post_data"]', function () {
    const now = Date.now();
    const timeSinceLastAjax = now - lastAjaxTime;

    if (timeSinceLastAjax < DEBOUNCE_DELAY) {
      // Te snel - debounce deze update
      clearTimeout(pendingUpdate);
      pendingUpdate = setTimeout(function () {
        triggerCheckoutUpdate();
      }, DEBOUNCE_DELAY - timeSinceLastAjax);
    } else {
      // Genoeg tijd verstreken - voer update uit
      triggerCheckoutUpdate();
    }
  });

  /**
   * Debounce voor shipping method changes
   */
  $(document).on("change", 'input[name="shipping_method"]', function () {
    clearTimeout(pendingUpdate);
    lastAjaxTime = Date.now();

    // Laat de WC handler uitvoeren (gebrutikt door checkout block)
    setTimeout(function () {
      $("body").trigger("update_checkout");
    }, 100);
  });

  /**
   * Debounce voor country/state changes
   */
  $(document).on(
    "change",
    "select#billing_country, select#billing_state, select#shipping_country, select#shipping_state",
    function () {
      clearTimeout(pendingUpdate);
      pendingUpdate = setTimeout(function () {
        lastAjaxTime = Date.now();
        $("body").trigger("update_checkout");
      }, DEBOUNCE_DELAY);
    },
  );

  /**
   * Trigger checkout update met rate limiting
   */
  function triggerCheckoutUpdate() {
    lastAjaxTime = Date.now();

    // WooCommerce's native update_checkout trigger
    $("body").trigger("update_checkout");
  }

  /**
   * Cache AJAX responses om herhaalde calls te voorkomen
   */
  let ajaxCache = {};
  const CACHE_TTL = 60000; // 1 minuut

  const originalAjax = $.ajax;
  $.ajax = function (settings) {
    // Check cache voor checkout_fragment_refresh
    if (
      settings.data &&
      settings.data.action === "woocommerce_checkout_update_order_review"
    ) {
      const cacheKey = settings.data.post_data;
      const cached = ajaxCache[cacheKey];

      if (cached && Date.now() - cached.time < CACHE_TTL) {
        // Return cached result
        return $.Deferred().resolveWith(settings.context || window, [
          cached.data,
          "success",
        ]);
      }

      // Voer normale AJAX uit en cache result
      const dfd = originalAjax.apply(this, arguments);
      dfd.done(function (data) {
        ajaxCache[cacheKey] = {
          data: data,
          time: Date.now(),
        };

        // Clean up old cache entries
        Object.keys(ajaxCache).forEach((key) => {
          if (Date.now() - ajaxCache[key].time > CACHE_TTL) {
            delete ajaxCache[key];
          }
        });
      });

      return dfd;
    }

    return originalAjax.apply(this, arguments);
  };

  // Hergebruik jQuery versie
  $.ajax.constructor = originalAjax.constructor;

  /**
   * Visual feedback: Toon "Berekenen..." terwijl AJAX bezig is
   */
  $(document).on("update_checkout", function () {
    const $totals = $(".wc-block-components-order-summary");
    if ($totals.length) {
      $totals.addClass("is-calculating");
    }
  });

  $(document).on("checkout_error", function () {
    const $totals = $(".wc-block-components-order-summary");
    if ($totals.length) {
      $totals.removeClass("is-calculating");
    }
  });

  // WooCommerce compleets checkout update
  $(document.body).on("updated_checkout", function () {
    const $totals = $(".wc-block-components-order-summary");
    if ($totals.length) {
      $totals.removeClass("is-calculating");
    }
  });
})(jQuery);
