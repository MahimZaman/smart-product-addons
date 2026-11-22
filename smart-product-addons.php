<?php

/**
 * Plugin Name: Smart Product Addons
 * Description: Create custom product addons, customize product price based on selection, create conditional product options
 * Version: 1.1.0
 * Plugin URI: https://github.com/MahimZaman/smart-product-addons
 * Author: Mahim Zaman (Condixia)
 * Author URI: https://condixia.com
 * Text Domain: smart-product-addons
 * Domain Path: /languages
 * Requires Plugins:  woocommerce
 */

defined('ABSPATH') || exit;

define('SPA_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('SPA_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('SPA_VER', time());
define('SPA_TEXTDOMAIN', 'smart-product-addons');

require_once SPA_PATH . '/includes/classes/Core.php';

use SmartProductAddons\Core;

Core::init();
