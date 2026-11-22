<?php

namespace SmartProductAddons;

defined('ABSPATH') || exit;

class FrontEnd
{
    public function register()
    {
        add_action('wp_enqueue_scripts', [$this, 'frontend_scripts']);
    }

    public function frontend_scripts()
    {
        // ENQUEUE FRONTEND CSS FILES
        wp_enqueue_style('spa-remixicon', 'https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css', [], SPA_VER);
        wp_enqueue_style('spa-style', SPA_URL . 'assets/public/css/main.css', [], SPA_VER);

        // ENQUEUE FRONTEND JS FILES
        wp_enqueue_script('spa-script', SPA_URL . 'assets/public/js/main.js', [], SPA_VER, true);
        wp_localize_script('spa-script', 'spa_wc_price', [
            'currency_symbol'     => get_woocommerce_currency_symbol(),
            'currency_position'   => get_option('woocommerce_currency_pos'), // left, right, left_space, right_space
            'decimal_separator'   => wc_get_price_decimal_separator(),
            'thousand_separator'  => wc_get_price_thousand_separator(),
            'decimals'            => wc_get_price_decimals(),
        ]);
    }
}
