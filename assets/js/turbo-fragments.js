/**
 * WooCommerce Fast Fragments & Cart Turbo
 * Client-Side LocalStorage Fragment Layer
 *
 * @package WooFastFragments
 * @version 1.0.0
 */

(function ($) {
  'use strict';

  if (typeof window.wffConfig === 'undefined') {
    return;
  }

  var config = window.wffConfig;
  var storageKey = config.storageKey || 'wff_wc_fragments_cache';
  var ttl = (parseInt(config.cacheTtl, 10) || 3600) * 1000;

  var Turbo = {
    init: function () {
      Turbo.hydrateFromCache();

      // Listen for cart mutations triggered across WooCommerce
      $(document.body).on('added_to_cart removed_from_cart updated_cart_totals', this.onCartMutated);
      $(document).on('click', '.add_to_cart_button', this.onAddToCartClick);
    },

    getCookie: function (name) {
      var match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
      return match ? decodeURIComponent(match[3]) : null;
    },

    hydrateFromCache: function () {
      try {
        var raw = localStorage.getItem(storageKey);
        var currentCookieHash = Turbo.getCookie('woocommerce_cart_hash') || config.cartHash;

        if (raw) {
          var cached = JSON.parse(raw);
          var now = Date.now();

          // Check hash match and TTL freshness
          if (cached && cached.hash === currentCookieHash && (now - cached.time) < ttl) {
            Turbo.applyFragments(cached.fragments);
            return;
          }
        }
      } catch (e) {
        // LocalStorage fallback
      }

      // If cache is missing, empty, or hash mismatch, refresh via REST
      Turbo.fetchFreshFragments();
    },

    applyFragments: function (fragments) {
      if (!fragments || typeof fragments !== 'object') {
        return;
      }

      $.each(fragments, function (selector, html) {
        var $target = $(selector);
        if ($target.length) {
          $target.replaceWith(html);
        }
      });

      $(document.body).trigger('wc_fragments_refreshed');
      $(document.body).trigger('wff_fragments_hydrated', [fragments]);
    },

    saveToCache: function (hash, fragments) {
      try {
        var payload = {
          hash: hash,
          time: Date.now(),
          fragments: fragments
        };
        localStorage.setItem(storageKey, JSON.stringify(payload));
      } catch (e) {
        // Silent quota limit handling
      }
    },

    fetchFreshFragments: function () {
      $.ajax({
        url: config.restUrl,
        type: 'GET',
        dataType: 'json',
        cache: false,
        success: function (data) {
          if (data && data.fragments) {
            Turbo.applyFragments(data.fragments);
            Turbo.saveToCache(data.cart_hash || '', data.fragments);
          }
        }
      });
    },

    onCartMutated: function () {
      Turbo.fetchFreshFragments();
    },

    onAddToCartClick: function () {
      // Trigger light micro-pulse animation on cart badge
      $('.wff-cart-count, .cart-contents-count').addClass('wff-pulse');
      setTimeout(function () {
        $('.wff-cart-count, .cart-contents-count').removeClass('wff-pulse');
      }, 600);
    }
  };

  $(document).ready(function () {
    Turbo.init();
  });
})(jQuery);

