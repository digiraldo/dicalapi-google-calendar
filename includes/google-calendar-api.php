<?php
/**
 * Funciones para interactuar con la API de Google Calendar
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Obtener eventos del Google Calendar
 */
function dicalapi_gcalendar_get_events($max_events = null) {
    $options = get_option('dicalapi_gcalendar_options');
    $calendar_id = $options['calendar_id'] ?? '';
    $api_key = $options['api_key'] ?? '';
    
    // Si no hay ID de calendario o API key, devolver error
    if (empty($calendar_id) || empty($api_key)) {
        return array(
            'success' => false,
            'error' => __('Faltan configuraciones: ID de calendario o API Key', 'dicalapi-gcalendar')
        );
    }
    
    // Usar el valor de max_events proporcionado o el predeterminado
    $max_results = $max_events ?? ($options['max_events'] ?? 10);
    
    // Calcular fechas
    $timeMin = urlencode(date('c')); // Ahora
    
    // Preparar la URL de la API
    $calendar_id_encoded = urlencode($calendar_id);
    $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendar_id_encoded}/events?";
    $url .= "key={$api_key}&singleEvents=true&orderBy=startTime&timeMin={$timeMin}&maxResults={$max_results}";
    
    // Usar transient para almacenar en caché durante 5 minutos
    $transient_name = 'dicalapi_gcalendar_events_' . md5($url);
    $cached_result = get_transient($transient_name);
    
    if (false !== $cached_result) {
        return $cached_result;
    }
    
    // Realizar solicitud HTTP
    $response = wp_remote_get($url);
    
    // Verificar errores
    if (is_wp_error($response)) {
        return array(
            'success' => false,
            'error' => $response->get_error_message()
        );
    }
    
    // Obtener código de respuesta
    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code !== 200) {
        return array(
            'success' => false,
            'error' => __('Error de API:', 'dicalapi-gcalendar') . ' ' . $response_code
        );
    }
    
    // Decodificar la respuesta
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    // Verificar si hay eventos
    if (empty($data['items'])) {
        return array(
            'success' => true,
            'events' => array()
        );
    }
    
    // Procesar eventos
    $events = array();
    foreach ($data['items'] as $event) {
        // Solo procesar si tiene título
        if (empty($event['summary'])) {
            continue;
        }
        
        // Establecer fechas de inicio y fin
        $start = isset($event['start']['dateTime']) ? $event['start']['dateTime'] : $event['start']['date'];
        $end = isset($event['end']['dateTime']) ? $event['end']['dateTime'] : $event['end']['date'];
        
        // Convertir a timestamps
        $start_timestamp = strtotime($start);
        $end_timestamp = strtotime($end);
        
        // Ajustar para eventos de todo el día que terminan un día después
        if (isset($event['end']['date']) && !isset($event['end']['dateTime']) && $start !== $end) {
            $end_timestamp -= 24 * 60 * 60; // Restar un día
        }
        
        // Formatear fechas para mostrar solo día y mes
        $start_date = date_i18n('j M', $start_timestamp);
        $end_date = date_i18n('j M', $end_timestamp);
        
        // Agregar el evento
        $events[] = array(
            'title' => $event['summary'],
            'description' => isset($event['description']) ? $event['description'] : '',
            'location' => isset($event['location']) ? $event['location'] : '',
            'start_date' => $start_date,
            'end_date' => $end_date,
            'start_timestamp' => $start_timestamp,
            'end_timestamp' => $end_timestamp,
            'html_link' => isset($event['htmlLink']) ? $event['htmlLink'] : '',
            'is_all_day' => !isset($event['start']['dateTime']),
        );
    }
    
    $result = array(
        'success' => true,
        'events' => $events
    );
    
    // Guardar en caché por 5 minutos
    set_transient($transient_name, $result, 5 * MINUTE_IN_SECONDS);
    
    return $result;
}
