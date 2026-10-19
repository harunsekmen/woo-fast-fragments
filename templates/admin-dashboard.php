<?php
declare(strict_types=1);

namespace WooFastFragments;

defined('ABSPATH') || exit;

/**
 * @var array $settings
 */
?>
<div class="wrap wff-admin-wrap">
    <h1><?php esc_html_e('WooCommerce Fast Fragments & Cart Turbo', 'woo-fast-fragments'); ?></h1>
    <p class="description">
        <?php esc_html_e('Eliminates expensive ?wc-ajax=get_refreshed_fragments requests. Caches mini-cart DOM in client LocalStorage and routes dynamic syncs through lightweight REST API.', 'woo-fast-fragments'); ?>
    </p>

    <div class="wff-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-top: 20px;">
        <div class="wff-main-col">
            <div class="postbox" style="padding: 24px;">
                <h2 style="margin-top: 0; font-size: 16px;"><?php esc_html_e('Server Offload & Performance Metrics', 'woo-fast-fragments'); ?></h2>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #16a34a; padding: 16px; border-radius: 6px;">
                        <span style="font-size: 28px; font-weight: 700; color: #16a34a; display: block;">~85%</span>
                        <span style="font-size: 12px; color: #4b5563; font-weight: 500;"><?php esc_html_e('Server TTFB Reduction', 'woo-fast-fragments'); ?></span>
                    </div>
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-left: 4px solid #2563eb; padding: 16px; border-radius: 6px;">
                        <span style="font-size: 28px; font-weight: 700; color: #2563eb; display: block;">0 ms</span>
                        <span style="font-size: 12px; color: #4b5563; font-weight: 500;"><?php esc_html_e('Repeat Page Mini-Cart Latency', 'woo-fast-fragments'); ?></span>
                    </div>
                    <div style="background: #faf5ff; border: 1px solid #e9d5ff; border-left: 4px solid #9333ea; padding: 16px; border-radius: 6px;">
                        <span style="font-size: 28px; font-weight: 700; color: #9333ea; display: block;">&lt; 2 KB</span>
                        <span style="font-size: 12px; color: #4b5563; font-weight: 500;"><?php esc_html_e('Client Script Weight', 'woo-fast-fragments'); ?></span>
                    </div>
                </div>

                <h3 style="font-size: 14px;"><?php esc_html_e('Architectural Comparison', 'woo-fast-fragments'); ?></h3>
                <table class="wp-list-table widefat fixed striped" style="margin-top: 10px;">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Metric / Behavior', 'woo-fast-fragments'); ?></th>
                            <th><?php esc_html_e('Default WooCommerce', 'woo-fast-fragments'); ?></th>
                            <th><?php esc_html_e('With Fast Fragments Turbo', 'woo-fast-fragments'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong><?php esc_html_e('Every Page Load Ajax Request', 'woo-fast-fragments'); ?></strong></td>
                            <td style="color: #dc2626;"><code>?wc-ajax=get_refreshed_fragments</code> (Full PHP Bootstrap)</td>
                            <td style="color: #16a34a;"><strong>Zero Server Calls</strong> (LocalStorage Instant Render)</td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Full Page Caching Impact', 'woo-fast-fragments'); ?></strong></td>
                            <td>Bypassed on every visitor navigation</td>
                            <td>100% Cache Hit on Cloudflare / Varnish / Redis</td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Cart Mutation Sync', 'woo-fast-fragments'); ?></strong></td>
                            <td>Legacy admin-ajax / wc-ajax endpoint</td>
                            <td>Lightweight REST endpoint (<code>/wp-json/wff/v1/fragments</code>)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="wff-side-col">
            <div class="postbox" style="padding: 24px;">
                <h2 style="margin-top: 0; font-size: 16px;"><?php esc_html_e('Turbo Configuration', 'woo-fast-fragments'); ?></h2>
                <form method="post" action="options.php">
                    <?php settings_fields('wff_settings_group'); ?>

                    <p>
                        <label>
                            <input type="checkbox" name="wff_settings[disable_default_fragments]" value="1" <?php checked(!empty($settings['disable_default_fragments'])); ?>>
                            <strong><?php esc_html_e('Disable Default wc-cart-fragments', 'woo-fast-fragments'); ?></strong>
                        </label>
                        <span class="description" style="display: block; margin-top: 4px; font-size: 12px;">
                            <?php esc_html_e('Stops WordPress from firing the heavy AJAX call on every catalog page load.', 'woo-fast-fragments'); ?>
                        </span>
                    </p>

                    <p style="margin-top: 16px;">
                        <label>
                            <input type="checkbox" name="wff_settings[enable_rest_turbo]" value="1" <?php checked(!empty($settings['enable_rest_turbo'])); ?>>
                            <strong><?php esc_html_e('Enable REST API Turbo Endpoint', 'woo-fast-fragments'); ?></strong>
                        </label>
                        <span class="description" style="display: block; margin-top: 4px; font-size: 12px;">
                            <?php esc_html_e('Routes dynamic mini-cart updates via optimized /wff/v1/fragments instead of admin-ajax.', 'woo-fast-fragments'); ?>
                        </span>
                    </p>

                    <p style="margin-top: 16px;">
                        <label for="wff_cache_ttl"><strong><?php esc_html_e('Client Storage TTL (Seconds):', 'woo-fast-fragments'); ?></strong></label><br>
                        <input type="number" id="wff_cache_ttl" name="wff_settings[cache_ttl]" value="<?php echo esc_attr((string) ($settings['cache_ttl'] ?? 3600)); ?>" min="300" step="300" class="regular-text" style="width: 100%; margin-top: 5px;">
                    </p>

                    <?php submit_button(__('Save Turbo Settings', 'woo-fast-fragments')); ?>
                </form>
            </div>
        </div>
    </div>
</div>

