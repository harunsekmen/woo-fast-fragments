<?php
declare(strict_types=1);

namespace WooFastFragments;

defined('ABSPATH') || exit;

final class Admin {
    public function register(): void {
        add_action('admin_menu', [$this, 'add_menu_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_menu_page(): void {
        add_submenu_page(
            'woocommerce',
            __('Fast Fragments Turbo', 'woo-fast-fragments'),
            __('Fast Fragments', 'woo-fast-fragments'),
            'manage_woocommerce',
            'wff-settings',
            [$this, 'render_dashboard']
        );
    }

    public function register_settings(): void {
        register_setting('wff_settings_group', Plugin::OPTION_KEY, [
            'type'              => 'array',
            'sanitize_callback' => [$this, 'sanitize_settings'],
            'default'           => [
                'disable_default_fragments' => 1,
                'cache_ttl'                  => Engine::DEFAULT_CACHE_TTL,
                'enable_rest_turbo'          => 1,
            ],
        ]);
    }

    public function sanitize_settings(mixed $input): array {
        $output = [];
        $output['disable_default_fragments'] = !empty($input['disable_default_fragments']) ? 1 : 0;
        $output['enable_rest_turbo']          = !empty($input['enable_rest_turbo']) ? 1 : 0;
        $output['cache_ttl']                  = isset($input['cache_ttl']) ? max(300, (int) $input['cache_ttl']) : Engine::DEFAULT_CACHE_TTL;

        return $output;
    }

    public function render_dashboard(): void {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        $settings = get_option(Plugin::OPTION_KEY, [
            'disable_default_fragments' => 1,
            'cache_ttl'                  => Engine::DEFAULT_CACHE_TTL,
            'enable_rest_turbo'          => 1,
        ]);

        require_once Plugin::get_plugin_dir() . 'templates/admin-dashboard.php';
    }
}

