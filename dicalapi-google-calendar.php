<?php
/**
 * Plugin Name:       DICALAPI Google Calendar Events
 * Plugin URI:        https://profiles.wordpress.org/digiraldo/
 * Description:       Plugin personalizado para mostrar eventos de Google Calendar en tu sitio de WordPress
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.0
 * Author:            DiGiraldo
 * Author URI:        https://github.com/digiraldo
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       dicalapi-google-calendar-events
 * Domain Path:       /languages
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Constantes del plugin
define('DICALAPI_GCALENDAR_VERSION', '1.0.0');
define('DICALAPI_GCALENDAR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DICALAPI_GCALENDAR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DICALAPI_GCALENDAR_PLUGIN_FILE', __FILE__);
define('DICALAPI_GCALENDAR_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Cargar archivo de textdomain para traducciones
 */
function dicalapi_gcalendar_load_textdomain() {
    load_plugin_textdomain('dicalapi-google-calendar-events', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'dicalapi_gcalendar_load_textdomain');

// Incluir archivos necesarios
require_once DICALAPI_GCALENDAR_PLUGIN_DIR . 'includes/google-calendar-api.php';
require_once DICALAPI_GCALENDAR_PLUGIN_DIR . 'includes/shortcode.php';
require_once DICALAPI_GCALENDAR_PLUGIN_DIR . 'includes/admin-page.php';
require_once DICALAPI_GCALENDAR_PLUGIN_DIR . 'includes/privacy.php';

// Funciones de activación y desactivación
register_activation_hook(__FILE__, 'dicalapi_gcalendar_activate');
register_deactivation_hook(__FILE__, 'dicalapi_gcalendar_deactivate');

function dicalapi_gcalendar_activate() {
    // Guardar opciones por defecto
    $default_options = array(
        'calendar_id' => '',
        'api_key' => '',
        'column1_bg' => '#f8f9fa',
        'column2_bg' => '#ffffff',
        'column3_bg' => '#f0f0f0',
        'row_shadow' => '0px 2px 5px rgba(0,0,0,0.1)',
        'title_color' => '#333333',
        'title_size' => '18px',
        'desc_color' => '#666666',
        'desc_size' => '14px',
        'location_color' => '#888888',
        'location_size' => '14px',
        'date_color' => '#007bff',
        'date_size' => '16px',
        'signup_url' => '',
        'signup_button_text' => 'Inscribirse',
        'button_bg_color' => '#007bff',
        'button_hover_bg_color' => '#0056b3',
        'button_text_color' => '#ffffff',
        'button_text_size' => '14px',
        'title_text_color' => '#333333',
        'title_text_size' => '18px',
        'title_date_color' => '#007bff',
        'title_date_size' => '16px',
        'title_scroll_interval' => 5,
        'title_widget_bg' => '#f8f9fa',
        'title_widget_shadow' => '0px 2px 5px rgba(0,0,0,0.1)',
        'title_indicator_color' => '#007bff',
        'max_events' => 10,
    );
    add_option('dicalapi_gcalendar_options', $default_options);
}

function dicalapi_gcalendar_deactivate() {
    // No eliminamos las opciones para conservar la configuración
}

// Registrar scripts y estilos
function dicalapi_gcalendar_enqueue_scripts() {
    // Cargar dashicons para usar en el icono de ubicación
    wp_enqueue_style('dashicons');
    
    // Frontend
    wp_enqueue_style('dicalapi-gcalendar-public', DICALAPI_GCALENDAR_PLUGIN_URL . 'assets/css/public.css', array('dashicons'), DICALAPI_GCALENDAR_VERSION);
    
    // Scripts para asegurar compatibilidad
    wp_register_script('dicalapi-gcalendar-frontend', DICALAPI_GCALENDAR_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), DICALAPI_GCALENDAR_VERSION, true);
    
    // Obtener opciones para pasar al script
    $options = get_option('dicalapi_gcalendar_options');
    
    // Pasar las opciones al script
    wp_localize_script('dicalapi-gcalendar-frontend', 'dicalapi_options', $options);
    
    // Enqueue el script
    wp_enqueue_script('dicalapi-gcalendar-frontend');
}
add_action('wp_enqueue_scripts', 'dicalapi_gcalendar_enqueue_scripts');

// Registrar scripts y estilos para admin
function dicalapi_gcalendar_admin_enqueue_scripts($hook) {
    if ('settings_page_dicalapi-gcalendar-settings' !== $hook) {
        return;
    }
    
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_style('dicalapi-gcalendar-admin', DICALAPI_GCALENDAR_PLUGIN_URL . 'assets/css/admin.css', array(), DICALAPI_GCALENDAR_VERSION);
    wp_enqueue_script('dicalapi-gcalendar-admin', DICALAPI_GCALENDAR_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'wp-color-picker'), DICALAPI_GCALENDAR_VERSION, true);
}
add_action('admin_enqueue_scripts', 'dicalapi_gcalendar_admin_enqueue_scripts');
