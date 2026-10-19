<?php
declare(strict_types=1);

namespace WooFastFragments;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined('ABSPATH') || exit;

final class RestEndpoint {
    public const NAMESPACE = 'wff/v1';

    public function register(): void {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void {
        register_rest_route(self::NAMESPACE, '/fragments', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_fragments'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function get_fragments(WP_REST_Request $request): WP_REST_Response {
        if (!function_exists('WC') || !WC()->cart) {
            return new WP_REST_Response([
                'fragments' => [],
                'cart_hash' => '',
                'count'     => 0,
            ], 200);
        }

        ob_start();
        woocommerce_mini_cart();
        $mini_cart_html = ob_get_clean();

        $fragments = [
            'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart_html . '</div>',
            'span.wff-cart-count'              => '<span class="wff-cart-count">' . WC()->cart->get_cart_contents_count() . '</span>',
            'span.wff-cart-total'              => '<span class="wff-cart-total">' . WC()->cart->get_cart_total() . '</span>',
        ];

        /**
         * Allow third-party theme filters to hook additional fragments
         */
        $fragments = apply_filters('woocommerce_add_to_cart_fragments', $fragments);

        $response_data = [
            'fragments' => $fragments,
            'cart_hash' => WC()->cart->get_cart_hash(),
            'count'     => WC()->cart->get_cart_contents_count(),
            'total'     => WC()->cart->get_cart_total(),
            'timestamp' => time(),
        ];

        $response = new WP_REST_Response($response_data, 200);
        $response->header('Cache-Control', 'no-cache, must-revalidate, max-age=0');

        return $response;
    }
}

