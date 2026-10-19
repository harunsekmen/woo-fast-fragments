<?php
declare(strict_types=1);

namespace WooFastFragments;

defined('ABSPATH') || exit;

final class Plugin {
    public const VERSION = '1.0.0';
    public const OPTION_KEY = 'wff_settings';

    private static ?self $instance = null;
    private Engine $engine;
    private RestEndpoint $rest;
    private Admin $admin;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        $this->engine = new Engine();
        $this->rest   = new RestEndpoint();
        $this->admin  = new Admin();
    }

    public function init(): void {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', [$this, 'render_woocommerce_missing_notice']);
            return;
        }

        $this->engine->register();
        $this->rest->register();
        $this->admin->register();
    }

    public function render_woocommerce_missing_notice(): void {
        ?>
        <div class="notice notice-error">
            <p><?php esc_html_e('WooCommerce Fast Fragments requires WooCommerce to be installed and active.', 'woo-fast-fragments'); ?></p>
        </div>
        <?php
    }

    public static function get_plugin_dir(): string {
        return plugin_dir_path(dirname(__FILE__));
    }

    public static function get_plugin_url(): string {
        return plugin_dir_url(dirname(__FILE__));
    }
}

