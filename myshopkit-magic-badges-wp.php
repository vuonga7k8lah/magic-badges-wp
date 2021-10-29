<?php
/**
 * Plugin Name: Myshopkit Magic Badges WP
 * Plugin URI: https://myshopkit.app
 * Author: myshopkit
 * Author URI: https://myshopkit.app
 * Version: 1.0
 */

add_action('admin_notices', function () {

    if (!class_exists('WooCommerce')) {
        ?>
        <div id="mysmbwp-converter-warning" class="notice notice-error">
            <?php esc_html_e('Please install and activate WooCommerce to use Multi Currency for WooCommerce plugin.',
                'magic-badges-wp'); ?>
        </div>
        <?php
    }
});


use MyShopKitMBWP\Dashboard\Controllers\AuthController;

define('MYSHOPKIT_MB_WP_VERSION', '1.0');
define('MYSHOPKIT_MB_WP_HOOK_PREFIX', 'mskmbwp/');
define('MYSHOPKIT_MB_WP_PREFIX', 'mskmbwp_');
define('MYSHOPKIT_MB_WP_REST_VERSION', 'v1');
define('MYSHOPKIT_MB_WP_REST_NAMESPACE', 'magic-badges-wp');
define('MYSHOPKIT_MB_WP_REST', MYSHOPKIT_MB_WP_REST_NAMESPACE . '/' . MYSHOPKIT_MB_WP_REST_VERSION);
define('MYSHOPKIT_MB_WP_URL', plugin_dir_url(__FILE__));
define('MYSHOPKIT_MB_WP_PATH', plugin_dir_path(__FILE__));

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';


require_once plugin_dir_path(__FILE__) . 'src/Dashboard/Dashboard.php';
require_once plugin_dir_path(__FILE__) . 'src/DefaultBadge/DefaultBadge.php';
require_once plugin_dir_path(__FILE__) . 'src/Product/Product.php';
require_once plugin_dir_path(__FILE__) . 'src/Automatic/Automatic.php';


register_activation_hook(__FILE__, function () {
    AuthController::generateAuth();
});

register_deactivation_hook(__FILE__, function () {
    AuthController::autoDeleteAuth();
});

add_filter('wp_is_application_passwords_available', '__return_true', 9999);
