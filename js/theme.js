(() => {
  // js/navigation.js
  (function() {
    const siteNavigation = document.getElementById("site-navigation");
    if (!siteNavigation) {
      return;
    }
    const button = siteNavigation.getElementsByTagName("button")[0];
    if ("undefined" === typeof button) {
      return;
    }
    const menu = siteNavigation.getElementsByTagName("ul")[0];
    if ("undefined" === typeof menu) {
      button.style.display = "none";
      return;
    }
    if (!menu.classList.contains("nav-menu")) {
      menu.classList.add("nav-menu");
    }
    button.addEventListener("click", function() {
      siteNavigation.classList.toggle("toggled");
      if (button.getAttribute("aria-expanded") === "true") {
        button.setAttribute("aria-expanded", "false");
      } else {
        button.setAttribute("aria-expanded", "true");
      }
    });
    document.addEventListener("click", function(event2) {
      const isClickInside = siteNavigation.contains(event2.target);
      if (!isClickInside) {
        siteNavigation.classList.remove("toggled");
        button.setAttribute("aria-expanded", "false");
      }
    });
    const links = menu.getElementsByTagName("a");
    const linksWithChildren = menu.querySelectorAll(".menu-item-has-children > a, .page_item_has_children > a");
    for (const link of links) {
      link.addEventListener("focus", toggleFocus, true);
      link.addEventListener("blur", toggleFocus, true);
    }
    for (const link of linksWithChildren) {
      link.addEventListener("touchstart", toggleFocus, false);
    }
    function toggleFocus() {
      if (event.type === "focus" || event.type === "blur") {
        let self = this;
        while (!self.classList.contains("nav-menu")) {
          if ("li" === self.tagName.toLowerCase()) {
            self.classList.toggle("focus");
          }
          self = self.parentNode;
        }
      }
      if (event.type === "touchstart") {
        const menuItem = this.parentNode;
        event.preventDefault();
        for (const link of menuItem.parentNode.children) {
          if (menuItem !== link) {
            link.classList.remove("focus");
          }
        }
        menuItem.classList.toggle("focus");
      }
    }
  })();

  // js/mobile-nav.js
  (function() {
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
    panel.addEventListener("keydown", function(e) {
      if (e.key === "Escape") close();
    });
    document.addEventListener("keydown", function(e) {
      if (e.key === "Escape" && panel.classList.contains("is-open")) close();
    });
    window.addEventListener("resize", function() {
      if (window.matchMedia("(min-width: 48em)").matches && panel.classList.contains("is-open")) {
        close();
      }
    });
  })();

  // js/promo-carousel.js
  (function() {
    function initCarousel(containerClass, textClass, dataId, hasButtons) {
      var el = document.querySelector(containerClass);
      if (!el) return;
      var textEl = el.querySelector(textClass);
      var data = el.getAttribute("data-texts") || document.getElementById(dataId) && document.getElementById(dataId).textContent;
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
      var direction = 1;
      var transition = "transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
      function show(idx) {
        var newIdx = (idx % texts.length + texts.length) % texts.length;
        var from = direction > 0 ? "100%" : "-100%";
        textEl.style.transition = "none";
        textEl.style.transform = "translate3d(" + from + ", 0, 0)";
        textEl.textContent = texts[newIdx];
        index = newIdx;
        textEl.offsetHeight;
        textEl.style.transition = transition;
        textEl.offsetHeight;
        textEl.style.transform = "translate3d(0, 0, 0)";
      }
      function goNext() {
        direction = 1;
        textEl.style.transition = transition;
        textEl.style.transform = "translate3d(-100%, 0, 0)";
        setTimeout(function() {
          show(index + 1);
        }, 400);
        restartInterval();
      }
      function goPrev() {
        direction = -1;
        textEl.style.transition = transition;
        textEl.style.transform = "translate3d(100%, 0, 0)";
        setTimeout(function() {
          show(index - 1);
        }, 400);
        restartInterval();
      }
      function restartInterval() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(goNext, 4e3);
      }
      if (hasButtons) {
        var prevBtn = el.querySelector(".header-promo__prev");
        var nextBtn = el.querySelector(".header-promo__next");
        if (prevBtn) prevBtn.addEventListener("click", goPrev);
        if (nextBtn) nextBtn.addEventListener("click", goNext);
      }
      timeoutId = setTimeout(goNext, 4e3);
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
})();
