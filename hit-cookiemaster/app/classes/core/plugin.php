<?php
namespace HITCookieMaster\App\Classes\Core;

/**
 * Hauptklasse für das HIT-CookieMaster Plugin
 *
 * Diese Klasse ist der Einstiegspunkt für alle Plugin-Funktionalitäten
 * und initialisiert alle notwendigen Komponenten.
 */
class Plugin {
    /**
     * Eine Instanz dieser Klasse (Singleton-Pattern)
     *
     * @var Plugin
     */
    private static $instance = null;

    /**
     * Plugin-Version
     *
     * @var string
     */
    private $version;

    /**
     * Plugin-Name
     *
     * @var string
     */
    private $plugin_name = 'hit-cookiemaster';

    /**
     * Konstruktor
     */
    private function __construct() {
        $this->version = HITCOOKIEMASTER_VERSION;
        $this->include_files();
        $this->init_hooks();
    }

    /**
     * Singleton-Pattern: Verhindert, dass mehr als eine Instanz dieser Klasse erstellt wird
     *
     * @return Plugin
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Bindet notwendige Dateien ein
     */
    private function include_files() {
        // Hier können zusätzliche Dateien eingebunden werden, die nicht
        // vom Autoloader erfasst werden (z.B. Funktionsdateien)
    }

    /**
     * Initialisiert WordPress Hooks
     */
    private function init_hooks() {
        // Aktivierungs- und Deaktivierungshooks
        register_activation_hook(HITCOOKIEMASTER_PLUGIN_BASENAME, array($this, 'activate'));
        register_deactivation_hook(HITCOOKIEMASTER_PLUGIN_BASENAME, array($this, 'deactivate'));
        register_uninstall_hook(HITCOOKIEMASTER_PLUGIN_BASENAME, array(__CLASS__, 'uninstall'));

        // Controller initialisieren
        add_action('init', array($this, 'init_controllers'));

        // Assets registrieren
        add_action('wp_enqueue_scripts', array($this, 'register_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'register_admin_assets'));

        // Internationalisierung laden
        add_action('init', array($this, 'load_textdomain'));

        // Security-spezifische Hooks (da es um verbesserte Sicherheit geht)
        add_action('init', array($this, 'init_security_features'));
    }

    /**
     * Controller initialisieren
     */
    public function init_controllers() {
        // Admin-Controller initialisieren (Verwaltung von Cookie-Einstellungen im Backend)
        $admin_controller = new \HITCookieMaster\App\Classes\Controller\AdminController();
        $admin_controller->init();  // Dummy in 1. Instanz

        // Frontend-Controller initialisieren (Cookie-Banner und -Einstellungen im Frontend)
        $frontend_controller = new \HITCookieMaster\App\Classes\Controller\FrontendController();
        $frontend_controller->init();  // Dummy in 1. Instanz

        // Cookie-Controller initialisieren (Verarbeitung und Management der Cookies)
        $cookie_controller = new \HITCookieMaster\App\Classes\Controller\CookieController();
        $cookie_controller->init();  // Dummy in 1. Instanz
    }

    /**
     * Sicherheitsfunktionen initialisieren
     */
    public function init_security_features() {
        // Cookie-Sicherheitsfeatures aktivieren
        $security_controller = new \HITCookieMaster\App\Classes\Controller\SecurityController();
        $security_controller->init();  // Dummy in 1. Instanz
    }

    /**
     * Frontend-Assets registrieren und einbinden
     */
    public function register_frontend_assets() {
        // CSS für das Cookie-Banner
        wp_enqueue_style(
            $this->plugin_name . '-frontend',
            HITCOOKIEMASTER_PLUGIN_URL . 'public/css/cookie-banner.css',
            array(),
            $this->version
        );

        // JavaScript für Cookie-Verarbeitung
        wp_enqueue_script(
            $this->plugin_name . '-frontend',
            HITCOOKIEMASTER_PLUGIN_URL . 'public/js/cookie-handler.js',
            array('jquery'),
            $this->version,
            true
        );

        // Localize Script für Frontend-Übersetzungen und Einstellungen
        wp_localize_script(
            $this->plugin_name . '-frontend',
            'hitCookieMaster',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('hit-cookiemaster-nonce'),
                'cookieExpiry' => apply_filters('hit_cookiemaster_cookie_expiry', 30), // Tage
                'cookiePath' => COOKIEPATH, // Zu definieren
                'cookieDomain' => COOKIE_DOMAIN, // Zu definieren
                'isSecure' => is_ssl(),
                'texts' => $this->get_frontend_texts()
            )
        );
    }

    /**
     * Admin-Assets registrieren und einbinden
     */
    public function register_admin_assets() {
        // Nur auf Plugin-Seiten laden
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, $this->plugin_name) === false) {
            return;
        }

        // CSS für die Admin-Seite
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            HITCOOKIEMASTER_PLUGIN_URL . 'public/css/admin.css',
            array(),
            $this->version
        );

        // JavaScript für die Admin-Seite
        wp_enqueue_script(
            $this->plugin_name . '-admin',
            HITCOOKIEMASTER_PLUGIN_URL . 'public/js/admin.js',
            array('jquery', 'wp-i18n'),
            $this->version,
            true
        );

        // Localize Script für Admin-Übersetzungen und Einstellungen
        wp_localize_script(
            $this->plugin_name . '-admin',
            'hitCookieMasterAdmin',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('hit-cookiemaster-admin-nonce')
            )
        );
    }

    /**
     * Frontend-Texte für Lokalisierung bereitstellen
     */
    private function get_frontend_texts() {
        return array(
            'banner_title' => __('Cookie-Einstellungen', 'hit-cookiemaster'),
            'banner_desc' => __('Diese Website verwendet Cookies, um Ihre Erfahrung zu verbessern.', 'hit-cookiemaster'),
            'accept_all' => __('Alle akzeptieren', 'hit-cookiemaster'),
            'accept_selected' => __('Auswahl speichern', 'hit-cookiemaster'),
            'reject_all' => __('Alle ablehnen', 'hit-cookiemaster'),
            'settings' => __('Einstellungen anpassen', 'hit-cookiemaster'),
            'necessary' => __('Notwendig', 'hit-cookiemaster'),
            'preferences' => __('Präferenzen', 'hit-cookiemaster'),
            'statistics' => __('Statistiken', 'hit-cookiemaster'),
            'marketing' => __('Marketing', 'hit-cookiemaster')
        );
    }

    /**
     * Wird beim Aktivieren des Plugins ausgeführt
     */
    public function activate() {
        // Datenbanktabellen erstellen (falls nötig)
        $model = new \HITCookieMaster\App\Classes\Model\SettingsModel();
        $model->create_tables();

        // Standardeinstellungen setzen
        $model->set_default_settings();

        // Rewrite-Regeln aktualisieren
        flush_rewrite_rules();

        // Version in der Datenbank speichern
        update_option('hit_cookiemaster_version', $this->version);
    }

    /**
     * Wird beim Deaktivieren des Plugins ausgeführt
     */
    public function deactivate() {
        // Temporäre Daten bereinigen

        // Rewrite-Regeln aktualisieren
        flush_rewrite_rules();
    }

    /**
     * Wird beim Deinstallieren des Plugins ausgeführt
     */
    public static function uninstall() {
        // Plugin-Daten aus der Datenbank entfernen
        $model = new \HITCookieMaster\App\Classes\Model\SettingsModel();
        $model->delete_tables();

        // Optionen löschen
        delete_option('hit_cookiemaster_version');
        delete_option('hit_cookiemaster_settings');
    }

    /**
     * Lädt die Textdomäne für Internationalisierung
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'hit-cookiemaster',
            false,
            dirname(HITCOOKIEMASTER_PLUGIN_BASENAME) . '/languages'
        );
    }
}