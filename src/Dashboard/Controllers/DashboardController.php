<?php

namespace MyShopKitMBWP\Dashboard\Controllers;


use MyShopKitMBWP\Dashboard\Shared\GeneralHelper;
use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;

class DashboardController
{
    use GeneralHelper;

        const MYSMBWP_GLOBAL = 'MYSMBWP_GLOBAL';

    //private string $wookitEditor = 'https://wookit-editor.netlify.app';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScriptsToDashboard']);
    }

    public function enqueueScriptsToDashboard($hook): bool
    {
        wp_localize_script('jquery', self::MYSMBWP_GLOBAL, [
            'url'              => admin_url('admin-ajax.php'),
            'restBase'         => trailingslashit(rest_url(MYSHOPKIT_MB_WP_REST)),
            'email'            => get_option('admin_email'),
            'clientSite'       => home_url('/'),
            'purchaseCode'     => $this->getToken(),
            'purchaseCodeLink' => 'https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code',
            'tidio'            => 'bdzedo8yftsclnwmwmbcqcsyscbk4rtl'
        ]);

        if ((strpos($hook, $this->getDashboardSlug()) !== false) || (strpos($hook, $this->getAuthSlug()) !== false)) {
            // enqueue script
            wp_enqueue_script(
                AutoPrefix::namePrefix('dashboard-script'),
                plugin_dir_url(__FILE__) . '../Assets/Js/Script.js',
                ['jquery'],
                MYSHOPKIT_MB_WP_VERSION,
                true
            );


            wp_enqueue_style(
                AutoPrefix::namePrefix('dashboard-style'),
                plugin_dir_url(__FILE__) . '../Assets/Css/Style.css',
                [],
                MYSHOPKIT_MB_WP_VERSION,
                ''
            );
        }
        return false;
    }

    public function registerMenu()
    {
        add_menu_page(
            esc_html__('Magic Badge Dashboard', MYSHOPKIT_MB_WP_REST_NAMESPACE),
            esc_html__('Magic Badge Dashboard', MYSHOPKIT_MB_WP_REST_NAMESPACE),
            'administrator',
            $this->getDashboardSlug(),
            [$this, 'renderSettings'],
            'dashicons-admin-network'
        );
    }

    public function renderSettings()
    {
        ?>
        <div id="mskmbwp-dashboard">
            <iframe id="mskmbwp-iframe" src="<?php echo esc_url($this->getIframe()); ?>"></iframe>
        </div>
        <?php
    }

    private function getIframe(): string
    {
        return defined('MSKMBWP_IFRAME') ? MSKMBWP_IFRAME : 'https://localhost::3000/';
    }
}
