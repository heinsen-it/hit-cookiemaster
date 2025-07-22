<?php
namespace HITCookieMaster\App\Classes\Core;

// Verhindere direkten Zugriff
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Klasse soll vorhandene Module des hit-cookiemaster laden
 */
class moduleloader
{


    private static $instance = null;
    private $modules = array();

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}