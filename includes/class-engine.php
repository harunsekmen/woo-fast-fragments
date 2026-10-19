<?php
declare(strict_types=1);

namespace WooFastFragments;

defined('ABSPATH') || exit;

final class Engine {
    public const DEFAULT_CACHE_TTL = 3600; // 1 hour

    public function register(): void {
        add_action('wp_enqueue_scripts', [$this, 'optimize_scripts'], 99);
        add_action('woocommerce_add_to_cart', [$this, 'invalidate_cart_hash']);
        add_action('woocommerce_cart_item_removed', [$this, 'invalidate_cart_hash']);
        add_action('woocommerce_cart_item_restored', [$this, 'invalidate_cart_hash']);
    }

    public function optimize_scripts(): void {
        // We only optimize non-cart / non-checkout pages to preserve static page caching
        if (is_cart() || is_checkout()) {
            return;
        }

        $settings = get_option(Plugin::OPTION_KEY, []);
        $disable_default_fragments = !isset($settings['disable_default_fragments']) || !empty($settings['disable_default_fragments']);
        $cache_ttl = isset($settings['cache_ttl']) ? (int) $settings['cache_ttl'] : self::DEFAULT_CACHE_TTL;

        if ($disable_default_fragments) {
            // Dequeue WooCommerce default heavy cart-fragments script
            wp_dequeue_script('wc-cart-fragments');
            wp_deregister_script('wc-cart-fragments');
        }

        $plugin_url = Plugin::get_plugin_url();

        // Enqueue ultra-light turbo fragment client
        wp_enqueue_script(
            'wff-turbo-cart',
            $plugin_url . 'assets/js/turbo-fragments.js',
            ['jquery'],
            Plugin::VERSION,
            true
        );

        wp_enqueue_style(
            'wff-turbo-styles',
            $plugin_url . 'assets/css/turbo-fragments.css',
            [],
            Plugin::VERSION
        );

        $cart_hash = WC()->cart ? WC()->cart->get_cart_hash() : '';
        $cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
        $cart_total = WC()->cart ? WC()->cart->get_cart_total() : '';

        wp_localize_script('wff-turbo-cart', 'wffConfig', [
            'restUrl'     => esc_url_raw(rest_url('wff/v1/fragments')),
            'cartHash'    => $cart_hash,
            'cartCount'   => $cart_count,
            'cartTotal'   => $cart_total,
            'cacheTtl'    => $cache_ttl,
            'storageKey'  => 'wff_wc_fragments_cache',
            'debug'       => defined('WP_DEBUG') && WP_DEBUG,
        ]);
    }

    public function invalidate_cart_hash(): void {
        // Trigger cookie update so client immediately knows the hash shifted
        if (WC()->cart) {
            WC()->cart->maybe_set_cart_cookies();
        }
    }
}

