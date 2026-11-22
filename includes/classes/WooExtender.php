<?php

namespace SmartProductAddons;

defined('ABSPATH') || exit;

use WP_Query;

class WooExtender
{
    public function register()
    {
        add_action('woocommerce_before_add_to_cart_button', [$this, 'spa_product_form']);
        add_action('woocommerce_after_add_to_cart_button', [$this, 'spa_after_cart_button']);
        add_filter('woocommerce_add_cart_item_data', [$this, 'spa_woocommerce_add_cart_item_data'], 10, 2);
        add_filter('woocommerce_get_cart_item_from_session', [$this, 'spa_woocommerce_get_cart_from_session'], 10, 2);
        add_action('woocommerce_before_calculate_totals', [$this, 'spa_woocommerce_calculate_totals'], 10, 1);
        add_filter('woocommerce_get_item_data', [$this, 'spa_woocommerce_get_item_data'], 10, 2);
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'spa_checkout_create_order_line_item'], 10, 3);
    }

    public function spa_after_cart_button()
    {
        global $product;
        $product_id = (int) $product->get_id();

        $args = [
            'post_type'      => 'spa_options',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => [
                [
                    'key'     => 'spaSelectedProducts',
                    'value'   => 'i:' . $product_id . ';',
                    'compare' => 'LIKE',
                ],
            ],
        ];
        $fieldsQuery = new WP_Query($args);

        if (!$fieldsQuery->have_posts()) {
            // Get product category IDs
            $category_ids = wp_get_post_terms(
                $product_id,
                'product_cat',
                ['fields' => 'ids']
            );

            // Build OR meta_query for serialized integers
            $meta_query = ['relation' => 'OR'];

            foreach ($category_ids as $cat_id) {
                $meta_query[] = [
                    'key'     => 'spaSelectedCategories',
                    'value'   => 'i:' . (int) $cat_id . ';',
                    'compare' => 'LIKE',
                ];
            }

            $args = [
                'post_type'      => 'spa_options',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'meta_query'     => $meta_query,
            ];
            $fieldsQuery = new WP_Query($args);
        }

        if ($fieldsQuery->have_posts()):
            while ($fieldsQuery->have_posts()):
                $fieldsQuery->the_post();
                $fields = get_post_meta(get_the_ID(), 'spa_get_fields', true) ? get_post_meta(get_the_ID(), 'spa_get_fields', true) : [];
                if (!empty($fields)) {
                    echo '</div></div></div>';
                }
            endwhile;
            wp_reset_postdata();
        endif;
    }

    public function spa_woocommerce_add_cart_item_data($cart_item_data, $product_id)
    {

        if (isset($_POST['spa_addons_data'])) {
            $addons = json_decode(stripslashes($_POST['spa_addons_data']), true);

            if (!empty($addons) && is_array($addons)) {
                $cart_item_data['spa_addons'] = $addons;

                // Prevent item merging if addons differ
                $cart_item_data['spa_unique_key'] = md5(microtime() . rand());
            }
        }

        return $cart_item_data;
    }

    public function spa_woocommerce_get_cart_from_session($cart_item, $values)
    {

        if (!empty($values['spa_addons'])) {
            $cart_item['spa_addons'] = $values['spa_addons'];
        }

        if (!empty($values['spa_unique_key'])) {
            $cart_item['spa_unique_key'] = $values['spa_unique_key'];
        }

        return $cart_item;
    }

    public function spa_woocommerce_calculate_totals($cart)
    {

        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }

        foreach ($cart->get_cart() as $cart_item) {
            if (empty($cart_item['spa_addons'])) {
                continue;
            }

            $addon_total = 0;

            foreach ($cart_item['spa_addons'] as $addon) {
                $addon_total += floatval($addon['price']);
            }

            $base_price = $cart_item['data']->get_regular_price();

            $cart_item['data']->set_price($base_price + $addon_total);
        }
    }

    public function spa_woocommerce_get_item_data($item_data, $cart_item)
    {

        if (!empty($cart_item['spa_addons'])) {
            foreach ($cart_item['spa_addons'] as $addon) {
                $item_data[] = [
                    'name'  => esc_html($addon['label']),
                    'value' => wc_price($addon['price']),
                ];
            }
        }

        return $item_data;
    }


    public function spa_checkout_create_order_line_item($item, $cart_item_key, $values)
    {

        if (empty($values['spa_addons'])) {
            return;
        }

        $formatted = [];

        foreach ($values['spa_addons'] as $addon) {
            $formatted[] = sprintf(
                '%s (+%s)',
                $addon['label'],
                wc_price($addon['price'])
            );
        }

        $item->add_meta_data(
            'Add-ons',
            implode("\n", $formatted),
            true
        );
    }

    public function spa_product_form()
    {
        global $product;
        $product_id = (int) $product->get_id();

        $args = [
            'post_type'      => 'spa_options',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => [
                [
                    'key'     => 'spaSelectedProducts',
                    'value'   => 'i:' . $product_id . ';',
                    'compare' => 'LIKE',
                ],
            ],
        ];
        $fieldsQuery = new WP_Query($args);

        if (!$fieldsQuery->have_posts()) {
            // Get product category IDs
            $category_ids = wp_get_post_terms(
                $product_id,
                'product_cat',
                ['fields' => 'ids']
            );

            // Build OR meta_query for serialized integers
            $meta_query = ['relation' => 'OR'];

            foreach ($category_ids as $cat_id) {
                $meta_query[] = [
                    'key'     => 'spaSelectedCategories',
                    'value'   => 'i:' . (int) $cat_id . ';',
                    'compare' => 'LIKE',
                ];
            }

            $args = [
                'post_type'      => 'spa_options',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'meta_query'     => $meta_query,
            ];
            $fieldsQuery = new WP_Query($args);
        }

        if ($fieldsQuery->have_posts()):
            while ($fieldsQuery->have_posts()):
                $fieldsQuery->the_post();
                $fields = get_post_meta(get_the_ID(), 'spa_get_fields', true) ? get_post_meta(get_the_ID(), 'spa_get_fields', true) : [];
                if (!empty($fields)) {
                    require SPA_PATH . 'includes/templates/ProductOptions.php';
                }
            endwhile;
            wp_reset_postdata();
        endif;
    }
}
