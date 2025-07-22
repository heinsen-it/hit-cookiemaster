<?php
namespace HITCookieMaster\App\Modules\CookieBlocker;


class CookieBlocker{



    private static $instance = null;


    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * Blocker initialisieren
     */
    private function init_blocker() {
       //TODO
    }

}