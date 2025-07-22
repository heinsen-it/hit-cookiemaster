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

    private static $settingname = 'hitcm_settings';
    private $modules = array();

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * Prüft ob ein Modul aktiv ist
     */
    public function is_module_active($module_key) {
        $settings = get_option($this->settingname, array());
        $active_modules = $settings['active_modules'] ?? array();

        return isset($active_modules[$module_key]) && $active_modules[$module_key];
    }

    /**
     * Aktiviert ein Modul
     */
    public function activate_module($module_key) {
        if (!isset($this->modules[$module_key])) {
            return false;
        }

        $settings = get_option($this->settingname, array());
        if (!isset($settings['active_modules'])) {
            $settings['active_modules'] = array();
        }

        $settings['active_modules'][$module_key] = true;
        update_option($this->settingname, $settings);

        return true;
    }

    /**
     * Deaktiviert ein Modul
     */
    public function deactivate_module($module_key) {
        $settings = get_option($this->settingname, array());
        if (isset($settings['active_modules'][$module_key])) {
            $settings['active_modules'][$module_key] = false;
            update_option($this->settingname, $settings);
        }

        return true;
    }

    
}