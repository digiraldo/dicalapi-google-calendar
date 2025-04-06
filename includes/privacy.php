<?php
/**
 * Funciones relacionadas con la privacidad para el plugin
 *
 * @link       https://profiles.wordpress.org/digiraldo/
 * @package    DICALAPI_GCalendar
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Añade el texto exportador de privacidad al registrador.
 */
function dicalapi_gcalendar_register_privacy_policy_content() {
    if (!function_exists('wp_add_privacy_policy_content')) {
        return;
    }

    $content = '<h3>' . __('DICALAPI Google Calendar Events', 'dicalapi-gcalendar') . '</h3>';
    
    $content .= '<p>' . __('Este plugin utiliza la API de Google Calendar para mostrar eventos desde un calendario de Google en tu sitio web.', 'dicalapi-gcalendar') . '</p>';
    
    $content .= '<p>' . __('El plugin no almacena ni procesa datos personales de los visitantes del sitio. Sin embargo, debes tener en cuenta lo siguiente:', 'dicalapi-gcalendar') . '</p>';
    
    $content .= '<ul>';
    $content .= '<li>' . __('El plugin almacena en caché temporalmente los datos de eventos obtenidos de Google Calendar para mejorar el rendimiento.', 'dicalapi-gcalendar') . '</li>';
    $content .= '<li>' . __('La información mostrada en los eventos (títulos, descripciones, ubicaciones) es la que se configura en Google Calendar y es visible públicamente en tu sitio.', 'dicalapi-gcalendar') . '</li>';
    $content .= '<li>' . __('Si los visitantes hacen clic en un enlace de inscripción, pueden ser dirigidos a un sitio externo según la configuración del evento.', 'dicalapi-gcalendar') . '</li>';
    $content .= '</ul>';
    
    $content .= '<p>' . __('Para más información sobre cómo Google procesa los datos, puedes consultar la política de privacidad de Google Calendar.', 'dicalapi-gcalendar') . '</p>';

    wp_add_privacy_policy_content('DICALAPI Google Calendar Events', wp_kses_post($content));
}
add_action('admin_init', 'dicalapi_gcalendar_register_privacy_policy_content');
