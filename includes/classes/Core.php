<?php

namespace SmartProductAddons;

defined('ABSPATH') || exit;

class Core
{
    private static function dependencies()
    {
        require_once SPA_PATH . 'includes/classes/Admin.php';
        require_once SPA_PATH . 'includes/classes/Database.php';
        require_once SPA_PATH . 'includes/classes/FrontEnd.php';
        require_once SPA_PATH . 'includes/classes/WooExtender.php';
    }

    public static function init()
    {
        self::dependencies();
        if (is_admin()) {
            (new Admin)->register();
        }

        (new FrontEnd)->register();
        (new Database)->register();
        (new WooExtender)->register();
    }
}
