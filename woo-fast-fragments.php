<?php
/**
 * Plugin Name:       WooCommerce Fast Fragments & Cart Turbo
 * Plugin URI:        https://github.com/harunsekmen/woo-fast-fragments
 * Description:       High-performance client-side cart fragment caching and REST turbo layer for WooCommerce. Eliminates server-choking ?wc-ajax=get_refreshed_fragments bottlenecks.
 * Version:           1.0.0
 * Author:            Harun Sekmen
 * Author URI:        https://github.com/harunsekmen
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       woo-fast-fragments
 * Domain Path:       /languages
 * Requires at least: 6.2
 * Requires PHP:      8.1
 * WC requires at least: 8.0
 * WC tested up to:   9.4
 */

declare(strict_types=1);

namespace WooFastFragments;

defined('ABSPATH') || exit;

// Autoloader for internal classes
foreach (glob(__DIR__ . '/includes/*.php') as $inc) {
    require_once $inc;
}

// Declare WooCommerce High-Performance Order Storage (HPOS) & Blocks compatibility
add_action('before_woocommerce_init', static function (): void {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            __FILE__,
            true
        );
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'cart_checkout_blocks',
            __FILE__,
            true
        );
    }
});

// Bootstrap plugin lifecycle
add_action('plugins_loaded', static function (): void {
    Plugin::instance()->init();
});

