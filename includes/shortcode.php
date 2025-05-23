<?php
/**
 * Implementación del shortcode para mostrar eventos de Google Calendar
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Registrar shortcode
function dicalapi_gcalendar_register_shortcode() {
    add_shortcode('dicalapi_gcalendar', 'dicalapi_gcalendar_shortcode');
}
add_action('init', 'dicalapi_gcalendar_register_shortcode');

// Registrar nuevo shortcode para títulos con scroll
function dicalapi_gcalendar_register_title_shortcode() {
    add_shortcode('dicalapi-gcalendar-titulo', 'dicalapi_gcalendar_title_shortcode');
}
add_action('init', 'dicalapi_gcalendar_register_title_shortcode');

// Función para el shortcode principal - Mejorar la renderización del HTML
function dicalapi_gcalendar_shortcode($atts) {
    // Atributos por defecto
    $atts = shortcode_atts(array(
        'max_events' => null,
    ), $atts);
    
    // Obtener eventos
    $events_data = dicalapi_gcalendar_get_events($atts['max_events']);
    
    // Si hay error, mostrarlo
    if (!$events_data['success']) {
        return '<div class="dicalapi-gcalendar-error">' . esc_html($events_data['error']) . '</div>';
    }
    
    // Si no hay eventos, mostrar mensaje
    if (empty($events_data['events'])) {
        return '<div class="dicalapi-gcalendar-no-events">' . __('No hay eventos próximos.', 'dicalapi-gcalendar') . '</div>';
    }
      // Obtener opciones para estilos
    $options = get_option('dicalapi_gcalendar_options');
    
    // Generar CSS dinámico
    $dynamic_css = dicalapi_gcalendar_generate_dynamic_css($options);
    
    // Iniciar el output con estilos inline para forzar la correcta aplicación
    $output = '<style>' . $dynamic_css . '</style>';
    $output .= '<div class="dicalapi-gcalendar-container">';
    
    // Estilos adicionales para forzar la aplicación correcta
    $column1_bg = esc_attr($options['column1_bg'] ?? '#f8f9fa');
    $column2_bg = esc_attr($options['column2_bg'] ?? '#ffffff');
    $column3_bg = esc_attr($options['column3_bg'] ?? '#f0f0f0');
    $row_shadow = esc_attr($options['row_shadow'] ?? '0px 2px 5px rgba(0,0,0,0.1)');
    
    // Estilos para título
    $title_color = esc_attr($options['title_color'] ?? '#333333');
    $title_size = esc_attr($options['title_size'] ?? '18px');
    $title_font = !empty($options['title_font']) ? "font-family: '" . esc_attr($options['title_font']) . "';" : '';
    $title_bold = !empty($options['title_bold']) ? 'font-weight: bold;' : '';
    $title_italic = !empty($options['title_italic']) ? 'font-style: italic;' : '';
    $title_underline = !empty($options['title_underline']) ? 'text-decoration: underline;' : '';
    $title_align = !empty($options['title_align']) ? 'text-align: ' . esc_attr($options['title_align']) . ';' : 'text-align: center;';
    
    // Estilos para descripción
    $desc_color = esc_attr($options['desc_color'] ?? '#666666');
    $desc_size = esc_attr($options['desc_size'] ?? '14px');
    $desc_font = !empty($options['desc_font']) ? "font-family: '" . esc_attr($options['desc_font']) . "';" : '';
    $desc_bold = !empty($options['desc_bold']) ? 'font-weight: bold;' : '';
    $desc_italic = !empty($options['desc_italic']) ? 'font-style: italic;' : '';
    $desc_underline = !empty($options['desc_underline']) ? 'text-decoration: underline;' : '';
    $desc_align = !empty($options['desc_align']) ? 'text-align: ' . esc_attr($options['desc_align']) . ';' : 'text-align: center;';
    
    // Estilos para ubicación
    $location_color = esc_attr($options['location_color'] ?? '#888888');
    $location_size = esc_attr($options['location_size'] ?? '14px');
    $location_font = !empty($options['location_font']) ? "font-family: '" . esc_attr($options['location_font']) . "';" : '';
    $location_bold = !empty($options['location_bold']) ? 'font-weight: bold;' : '';
    $location_italic = !empty($options['location_italic']) ? 'font-style: italic;' : '';
    $location_underline = !empty($options['location_underline']) ? 'text-decoration: underline;' : '';
    $location_align = !empty($options['location_align']) ? 'text-align: ' . esc_attr($options['location_align']) . ';' : 'text-align: center;';
    
    // Estilos para día
    $day_color = esc_attr($options['day_color'] ?? $options['date_color'] ?? '#007bff');
    $day_size = esc_attr($options['day_size'] ?? $options['date_size'] ?? '18px');
    $day_font = !empty($options['day_font']) ? "font-family: '" . esc_attr($options['day_font']) . "';" : '';
    $day_bold = !empty($options['day_bold']) ? 'font-weight: bold;' : '';
    $day_italic = !empty($options['day_italic']) ? 'font-style: italic;' : '';
    $day_underline = !empty($options['day_underline']) ? 'text-decoration: underline;' : '';
    
    // Estilos para mes
    $month_color = esc_attr($options['month_color'] ?? $options['date_color'] ?? '#007bff');
    $month_size = esc_attr($options['month_size'] ?? '14px');
    $month_font = !empty($options['month_font']) ? "font-family: '" . esc_attr($options['month_font']) . "';" : '';
    $month_bold = !empty($options['month_bold']) ? 'font-weight: bold;' : '';
    $month_italic = !empty($options['month_italic']) ? 'font-style: italic;' : '';
    $month_underline = !empty($options['month_underline']) ? 'text-decoration: underline;' : '';
    
    // Mantener compatibilidad con versiones anteriores
    $date_color = esc_attr($options['date_color'] ?? '#007bff');
    $date_size = esc_attr($options['date_size'] ?? '18px');
    
    // Iterar eventos con estilos inline
    foreach ($events_data['events'] as $event) {
        // Agregar un div wrapper para asegurar el layout correcto
        $output .= '<div class="dicalapi-gcalendar-event-wrapper">';
        $output .= '<div class="dicalapi-gcalendar-event" style="box-shadow:' . $row_shadow . '">';
        
        // Columna de fechas - con estructura clara y estilos inline
        $output .= '<div class="dicalapi-gcalendar-date-column" style="background-color:' . $column1_bg . '">';
          // Extraer día y mes de la fecha de inicio
        $start_day = date_i18n('j', $event['start_timestamp']);
        $start_month = date_i18n('M', $event['start_timestamp']);
        
        // Estilos inline para día y mes con las nuevas opciones
        $day_style = "color:{$day_color};font-size:{$day_size};{$day_font}{$day_bold}{$day_italic}{$day_underline}";
        $month_style = "color:{$month_color};font-size:{$month_size};{$month_font}{$month_bold}{$month_italic}{$month_underline}";
        
        $output .= '<div class="dicalapi-gcalendar-day" style="' . $day_style . '">' . esc_html($start_day) . '</div>';
        $output .= '<div class="dicalapi-gcalendar-month" style="' . $month_style . '">' . esc_html($start_month) . '</div>';
        
        // Si las fechas son diferentes, mostrar la fecha de fin
        if ($event['start_date'] !== $event['end_date']) {
            $end_day = date_i18n('j', $event['end_timestamp']);
            $end_month = date_i18n('M', $event['end_timestamp']);
            
            $output .= '<div class="dicalapi-gcalendar-day" style="' . $day_style . '">' . esc_html($end_day) . '</div>';
            $output .= '<div class="dicalapi-gcalendar-month" style="' . $month_style . '">' . esc_html($end_month) . '</div>';
        }
        
        $output .= '</div>'; // Fin columna fechas
        
        // Columna de contenido con estilos inline
        $output .= '<div class="dicalapi-gcalendar-content-column" style="background-color:' . $column2_bg . '">';
          // Título con todos los estilos configurables
        if (!empty($event['title'])) {
            $title_style = "color:{$title_color};font-size:{$title_size};{$title_font}{$title_bold}{$title_italic}{$title_underline}{$title_align}";
            $output .= '<h3 class="dicalapi-gcalendar-title" style="' . $title_style . '">' . esc_html($event['title']) . '</h3>';
        }
          // Descripción con todos los estilos configurables
        if (!empty($event['description'])) {
            // Limitar a 200 caracteres y eliminar HTML
            $description = strip_tags($event['description']);
            if (strlen($description) > 200) {
                $description = substr($description, 0, 200) . '...';
            }
            $desc_style = "color:{$desc_color};font-size:{$desc_size};{$desc_font}{$desc_bold}{$desc_italic}{$desc_underline}{$desc_align}";
            $output .= '<div class="dicalapi-gcalendar-description" style="' . $desc_style . '">' . esc_html($description) . '</div>';
        }
          // Lugar con todos los estilos configurables
        if (!empty($event['location'])) {
            $location_style = "color:{$location_color};font-size:{$location_size};{$location_font}{$location_bold}{$location_italic}{$location_underline}{$location_align}";
            $output .= '<div class="dicalapi-gcalendar-location" style="' . $location_style . '">';
            $output .= '<span class="dashicons dashicons-location"></span> ';
            $output .= esc_html($event['location']);
            $output .= '</div>';
        }
        
        $output .= '</div>'; // Fin columna contenido
        
        // Nueva columna de inscripción con estilos inline
        $output .= '<div class="dicalapi-gcalendar-signup-column" style="background-color:' . $column3_bg . '">';
        
        // Verificar si hay una URL específica para este evento (en la descripción)
        $signup_url = dicalapi_gcalendar_get_event_signup_url($event);
        
        // Obtener texto del botón de las opciones
        $button_text = !empty($options['signup_button_text']) ? $options['signup_button_text'] : __('Inscribirse', 'dicalapi-gcalendar');
        
        // Solo mostrar el botón si hay una URL configurada
        if (!empty($signup_url)) {
            $button_bg = esc_attr($options['button_bg_color'] ?? '#007bff');
            $button_text_color = esc_attr($options['button_text_color'] ?? '#ffffff');
            $button_size = esc_attr($options['button_text_size'] ?? '14px');
            
            $output .= '<a href="' . esc_url($signup_url) . '" class="dicalapi-gcalendar-signup-button" style="background-color:' . $button_bg . ';color:' . $button_text_color . ';font-size:' . $button_size . ';" target="_blank">';
            $output .= esc_html($button_text);
            $output .= '</a>';
        }
        
        $output .= '</div>'; // Fin columna inscripción
        
        $output .= '</div>'; // Fin evento
        $output .= '</div>'; // Fin wrapper
    }
    
    $output .= '</div>'; // Fin contenedor
    
    return $output;
}

// Función para el shortcode de títulos
function dicalapi_gcalendar_title_shortcode($atts) {
    // Atributos por defecto
    $atts = shortcode_atts(array(
        'max_events' => null,
    ), $atts);
    
    // Obtener eventos
    $events_data = dicalapi_gcalendar_get_events($atts['max_events']);
    
    // Si hay error, mostrarlo
    if (!$events_data['success']) {
        return '<div class="dicalapi-gcalendar-error">' . esc_html($events_data['error']) . '</div>';
    }
    
    // Si no hay eventos, mostrar mensaje
    if (empty($events_data['events'])) {
        return '<div class="dicalapi-gcalendar-no-events">' . __('No hay eventos próximos.', 'dicalapi-gcalendar') . '</div>';
    }
    
    // Obtener opciones para estilos
    $options = get_option('dicalapi_gcalendar_options');
    
    // Generar CSS dinámico
    $dynamic_css = dicalapi_gcalendar_generate_title_css($options);
    
    // Calcular el intervalo de scroll
    $scroll_interval = isset($options['title_scroll_interval']) ? intval($options['title_scroll_interval']) : 5;
    $scroll_interval_ms = $scroll_interval * 1000; // Convertir a milisegundos
    
    // ID único para este ticker (necesario si hay múltiples instancias en la misma página)
    $ticker_id = 'dicalapi-ticker-' . uniqid();
    
    // Iniciar el output
    $output = '<style>' . $dynamic_css . '</style>';
    $output .= '<div class="dicalapi-gcalendar-ticker-container" id="' . esc_attr($ticker_id) . '">';
    
    // Sólo crear el contenido simple si hay un único evento
    if (count($events_data['events']) == 1) {
        $event = $events_data['events'][0];
        $output .= '<div class="dicalapi-gcalendar-ticker-single">';
        
        // Aplicar estilos inline directamente para garantizar su aplicación
        $title_color = esc_attr($options['title_text_color'] ?? '#333333');
        $title_size = esc_attr($options['title_text_size'] ?? '18px');
        $date_color = esc_attr($options['title_date_color'] ?? '#007bff');
        $date_size = esc_attr($options['title_date_size'] ?? '16px');
        
        // Título del evento con estilos inline
        if (!empty($event['title'])) {
            $output .= '<span class="dicalapi-gcalendar-title-text" style="color:' . $title_color . ';font-size:' . $title_size . ';font-weight:bold;margin-right:3px;">' 
                . esc_html($event['title']) . '</span>';
        }
        
        // Fechas en formato (13 Sep - 16 Sep) con estilos inline - añadiendo un espacio antes del paréntesis
        $output .= '<span class="dicalapi-gcalendar-title-dates" style="color:' . $date_color . ';font-size:' . $date_size . ';margin-left:3px;"> (';
        $output .= esc_html($event['start_date']);
        
        // Si las fechas son diferentes, mostrar rango
        if ($event['start_date'] !== $event['end_date']) {
            $output .= ' - ' . esc_html($event['end_date']);
        }
        
        $output .= ')</span>';
        $output .= '</div>';
    } else {
        // Para múltiples eventos, crear un ticker vertical mejorado
        $output .= '<div class="dicalapi-gcalendar-ticker-wrapper" data-interval="' . esc_attr($scroll_interval_ms) . '">';
        $output .= '<div class="dicalapi-gcalendar-ticker-viewport">';
        $output .= '<ul class="dicalapi-gcalendar-ticker-list">';
        
        // Obtener valores de configuración para estilos
        $title_color = esc_attr($options['title_text_color'] ?? '#333333');
        $title_size = esc_attr($options['title_text_size'] ?? '18px');
        $date_color = esc_attr($options['title_date_color'] ?? '#007bff');
        $date_size = esc_attr($options['title_date_size'] ?? '16px');
        
        // Iterar eventos (sin duplicación) - aplicando estilos inline
        foreach ($events_data['events'] as $index => $event) {
            // Posición inicial (solo el primero visible, resto oculto)
            $visibility = ($index === 0) ? 'visible' : 'hidden';
            $opacity = ($index === 0) ? '1' : '0';
            $position = ($index === 0) ? 'relative' : 'absolute';
            
            // Crear el formato "Titulo Evento (13 Sep - 16 Sep)" con estilos inline
            $output .= '<li class="dicalapi-gcalendar-ticker-item" style="position:' . $position . ';visibility:' . $visibility . ';opacity:' . $opacity . ';">';
            
            // Título del evento
            if (!empty($event['title'])) {
                $output .= '<span class="dicalapi-gcalendar-title-text" style="color:' . $title_color . ';font-size:' . $title_size . ';font-weight:bold;margin-right:3px;">' 
                    . esc_html($event['title']) . '</span>';
            }
            
            // Fechas en formato (13 Sep - 16 Sep) - añadiendo un espacio antes del paréntesis
            $output .= '<span class="dicalapi-gcalendar-title-dates" style="color:' . $date_color . ';font-size:' . $date_size . ';margin-left:3px;"> (';
            $output .= esc_html($event['start_date']);
            
            // Si las fechas son diferentes, mostrar rango
            if ($event['start_date'] !== $event['end_date']) {
                $output .= ' - ' . esc_html($event['end_date']);
            }
            
            $output .= ')</span>';
            $output .= '</li>';
        }
        
        // Para crear un loop continuo, duplicamos solo el primer evento al final
        if (count($events_data['events']) > 1) {
            $first_event = $events_data['events'][0];
            $output .= '<li class="dicalapi-gcalendar-ticker-item" style="position:absolute;visibility:hidden;opacity:0;">';
            
            if (!empty($first_event['title'])) {
                $output .= '<span class="dicalapi-gcalendar-title-text" style="color:' . $title_color . ';font-size:' . $title_size . ';font-weight:bold;margin-right:3px;">' 
                    . esc_html($first_event['title']) . '</span>';
            }
            
            $output .= '<span class="dicalapi-gcalendar-title-dates" style="color:' . $date_color . ';font-size:' . $date_size . ';margin-left:3px;"> (';
            $output .= esc_html($first_event['start_date']);
            
            if ($first_event['start_date'] !== $first_event['end_date']) {
                $output .= ' - ' . esc_html($first_event['end_date']);
            }
            
            $output .= ')</span>';
            $output .= '</li>';
        }
        
        $output .= '</ul>';
        $output .= '</div>'; // Fin del viewport
        $output .= '</div>'; // Fin wrapper
    }
    
    $output .= '</div>'; // Fin contenedor
    
    // Agregar script mejorado para animación vertical con ID único
    if (count($events_data['events']) > 1) {
        $output .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Asegurar que todo esté cargado antes de iniciar
            setTimeout(function() {
                dicalapiInitTickerWithId("' . $ticker_id . '");
            }, 300);
            
            function dicalapiInitTickerWithId(tickerId) {
                const container = document.getElementById(tickerId);
                if (!container) return;
                
                const wrapper = container.querySelector(".dicalapi-gcalendar-ticker-wrapper");
                if (!wrapper) return;
                
                const ticker = wrapper.querySelector(".dicalapi-gcalendar-ticker-list");
                const items = ticker.querySelectorAll(".dicalapi-gcalendar-ticker-item");
                
                // Si no hay suficientes elementos, no hacemos nada
                if (items.length <= 1) return;
                
                const interval = parseInt(wrapper.getAttribute("data-interval")) || 5000;
                const viewport = wrapper.querySelector(".dicalapi-gcalendar-ticker-viewport");
                
                // Asegurarse de que cada elemento tenga la posición correcta
                items.forEach((item, index) => {
                    // Reset inicial para evitar conflictos con estilos anteriores
                    item.style.transition = "none";
                    
                    if (index === 0) {
                        item.style.position = "relative";
                        item.style.visibility = "visible";
                        item.style.opacity = "1";
                        item.style.transform = "translateY(0)";
                    } else {
                        item.style.position = "absolute";
                        item.style.top = "0";
                        item.style.left = "0";
                        item.style.visibility = "hidden";
                        item.style.opacity = "0";
                        item.style.transform = "translateY(100%)";
                    }
                });
                
                // Forzar reflow/repaint para que los cambios iniciales tengan efecto
                container.offsetHeight;
                
                // Configurar altura del viewport
                const itemHeight = items[0].offsetHeight;
                viewport.style.height = itemHeight + "px";
                
                // Variables de control
                let currentIndex = 0;
                let isAnimating = false;
                let tickerInterval;
                
                function animateNext() {
                    if (isAnimating) return;
                    isAnimating = true;
                    
                    // Obtener elementos actual y siguiente
                    const currentItem = items[currentIndex];
                    const nextIndex = (currentIndex + 1) % items.length;
                    const nextItem = items[nextIndex];
                    
                    // Preparar elemento siguiente para la animación
                    nextItem.style.transition = "none";
                    nextItem.style.transform = "translateY(100%)";
                    nextItem.style.visibility = "visible";
                    nextItem.style.opacity = "0";
                    
                    // Forzar reflow/repaint
                    nextItem.offsetHeight;
                    
                    // Iniciar animación con un pequeño retraso para garantizar que el navegador esté listo
                    setTimeout(function() {
                        // Elemento actual: mover hacia arriba y desvanecer
                        currentItem.style.transition = "transform 0.7s ease-in-out, opacity 0.7s ease-in-out";
                        currentItem.style.transform = "translateY(-100%)";
                        currentItem.style.opacity = "0";
                        
                        // Elemento siguiente: mover a posición central y mostrar
                        nextItem.style.transition = "transform 0.7s ease-in-out, opacity 0.7s ease-in-out";
                        nextItem.style.transform = "translateY(0)";
                        nextItem.style.opacity = "1";
                        
                        // Esperar a que termine la animación
                        setTimeout(function() {
                            // Ocultar elemento actual
                            currentItem.style.visibility = "hidden";
                            
                            // Actualizar índice
                            currentIndex = nextIndex;
                            
                            // Desbloquear para la siguiente animación
                            isAnimating = false;
                        }, 750);
                    }, 50);
                }
                
                // Iniciar la animación después de un pequeño retraso inicial
                setTimeout(function() {
                    // Configurar intervalo para las animaciones
                    tickerInterval = setInterval(function() {
                        if (!isAnimating) {
                            animateNext();
                        }
                    }, interval);
                }, 500);
                
                // Limpiar intervalo cuando la página no es visible
                document.addEventListener("visibilitychange", function() {
                    if (document.hidden) {
                        if (tickerInterval) clearInterval(tickerInterval);
                    } else {
                        // Reiniciar cuando la página vuelve a ser visible
                        if (tickerInterval) clearInterval(tickerInterval);
                        tickerInterval = setInterval(function() {
                            if (!isAnimating) {
                                animateNext();
                            }
                        }, interval);
                    }
                });
                
                // Definir función global para depuración si es necesario
                window["resetDicalapiTicker" + tickerId] = function() {
                    if (tickerInterval) clearInterval(tickerInterval);
                    items.forEach((item, index) => {
                        item.style.transition = "none";
                        if (index === 0) {
                            item.style.position = "relative";
                            item.style.visibility = "visible";
                            item.style.opacity = "1";
                            item.style.transform = "translateY(0)";
                        } else {
                            item.style.position = "absolute";
                            item.style.visibility = "hidden";
                            item.style.opacity = "0";
                            item.style.transform = "translateY(100%)";
                        }
                    });
                    container.offsetHeight;
                    currentIndex = 0;
                    isAnimating = false;
                    tickerInterval = setInterval(function() {
                        if (!isAnimating) {
                            animateNext();
                        }
                    }, interval);
                };
            }
        });
        </script>';
    }
    
    return $output;
}

/**
 * Extraer URL de inscripción de un evento
 * Busca una URL específica en la descripción o usa la URL por defecto
 */
function dicalapi_gcalendar_get_event_signup_url($event) {
    $options = get_option('dicalapi_gcalendar_options');
    $default_url = !empty($options['signup_url']) ? $options['signup_url'] : '';
    
    // Si no hay descripción, usar la URL por defecto
    if (empty($event['description'])) {
        return $default_url;
    }
    
    // Buscar patrones como [signup_url:https://example.com] en la descripción
    $pattern = '/\[signup_url:(https?:\/\/[^\]]+)\]/i';
    if (preg_match($pattern, $event['description'], $matches)) {
        return $matches[1];
    }
    
    // Si no se encuentra un patrón específico, usar la URL por defecto
    return $default_url;
}

/**
 * Generar CSS dinámico basado en las opciones
 */
function dicalapi_gcalendar_generate_dynamic_css($options) {
    // Recopilar todas las fuentes Google que se están usando
    $google_fonts = array();
    
    $font_fields = array('title_font', 'desc_font', 'location_font', 'day_font', 'month_font');
    
    foreach ($font_fields as $field) {
        if (!empty($options[$field])) {
            $font_name = $options[$field];
            if (!in_array($font_name, $google_fonts)) {
                $google_fonts[] = $font_name;
            }
        }
    }
    
    // Generar la URL de Google Fonts si hay fuentes para cargar
    $font_url = '';
    if (!empty($google_fonts)) {
        $font_families = array();
        foreach ($google_fonts as $font) {
            $font_families[] = str_replace(' ', '+', $font) . ':wght@300;400;500;600;700';
        }
        $font_url = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $font_families) . '&display=swap';
    }

    $css = '';
    
    // Agregar la importación de Google Fonts si es necesaria
    if (!empty($font_url)) {
        $css .= '@import url("' . $font_url . '");' . "\n";
    }
    
    $css .= '
    .dicalapi-gcalendar-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        margin: 20px 0;
        width: 100%;
    }
    
    .dicalapi-gcalendar-event {
        display: flex;
        margin-bottom: 20px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: ' . esc_attr($options['row_shadow'] ?? '0px 2px 5px rgba(0,0,0,0.1)') . ';
        width: 100%;
    }
    
    .dicalapi-gcalendar-date-column {
        background-color: ' . esc_attr($options['column1_bg'] ?? '#f8f9fa') . ';
        padding: 15px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-width: 80px;
        width: 15%;
        max-width: 120px;
        text-align: center;
        box-sizing: border-box;
    }
    
    .dicalapi-gcalendar-content-column {
        background-color: ' . esc_attr($options['column2_bg'] ?? '#ffffff') . ';
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        width: 65%;
        box-sizing: border-box;
    }
    
    .dicalapi-gcalendar-signup-column {
        background-color: ' . esc_attr($options['column3_bg'] ?? '#f0f0f0') . ';
        padding: 15px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-width: 100px;
        width: 20%;
        max-width: 150px;
        text-align: center;
        box-sizing: border-box;
    }
    
    .dicalapi-gcalendar-signup-button {
        display: inline-block;
        background-color: ' . esc_attr($options['button_bg_color'] ?? '#007bff') . ';
        color: ' . esc_attr($options['button_text_color'] ?? '#ffffff') . ' !important;
        padding: 8px 15px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        font-size: ' . esc_attr($options['button_text_size'] ?? '14px') . ';
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .dicalapi-gcalendar-signup-button:hover {
        background-color: ' . esc_attr($options['button_hover_bg_color'] ?? '#0056b3') . ';
        transform: translateY(-2px);
    }
      .dicalapi-gcalendar-day {
        color: ' . esc_attr($options['day_color'] ?? $options['date_color'] ?? '#007bff') . ';
        font-size: ' . esc_attr($options['day_size'] ?? $options['date_size'] ?? '18px') . ';
        font-family: ' . (!empty($options['day_font']) ? "'" . esc_attr($options['day_font']) . "'" : 'inherit') . ';
        font-weight: ' . (!empty($options['day_bold']) ? 'bold' : 'normal') . ';
        font-style: ' . (!empty($options['day_italic']) ? 'italic' : 'normal') . ';
        text-decoration: ' . (!empty($options['day_underline']) ? 'underline' : 'none') . ';
        line-height: 1.2;
        display: block;
        width: 100%;
        text-align: center;
    }
    
    .dicalapi-gcalendar-month {
        color: ' . esc_attr($options['month_color'] ?? $options['date_color'] ?? '#007bff') . ';
        font-size: ' . esc_attr($options['month_size'] ?? '14px') . ';
        font-family: ' . (!empty($options['month_font']) ? "'" . esc_attr($options['month_font']) . "'" : 'inherit') . ';
        font-weight: ' . (!empty($options['month_bold']) ? 'bold' : 'normal') . ';
        font-style: ' . (!empty($options['month_italic']) ? 'italic' : 'normal') . ';
        text-decoration: ' . (!empty($options['month_underline']) ? 'underline' : 'none') . ';
        margin-bottom: 8px;
        line-height: 1.2;
        display: block;
        width: 100%;
        text-align: center;
    }
    
    .dicalapi-gcalendar-title {
        color: ' . esc_attr($options['title_color'] ?? '#333333') . ';
        font-size: ' . esc_attr($options['title_size'] ?? '18px') . ';
        font-family: ' . (!empty($options['title_font']) ? "'" . esc_attr($options['title_font']) . "'" : 'inherit') . ';
        font-weight: ' . (!empty($options['title_bold']) ? 'bold' : 'normal') . ';
        font-style: ' . (!empty($options['title_italic']) ? 'italic' : 'normal') . ';
        text-decoration: ' . (!empty($options['title_underline']) ? 'underline' : 'none') . ';
        margin: 0 0 10px;
        text-align: ' . esc_attr($options['title_align'] ?? 'center') . ';
        width: 100%;
    }
    
    .dicalapi-gcalendar-description {
        color: ' . esc_attr($options['desc_color'] ?? '#666666') . ';
        font-size: ' . esc_attr($options['desc_size'] ?? '14px') . ';
        font-family: ' . (!empty($options['desc_font']) ? "'" . esc_attr($options['desc_font']) . "'" : 'inherit') . ';
        font-weight: ' . (!empty($options['desc_bold']) ? 'bold' : 'normal') . ';
        font-style: ' . (!empty($options['desc_italic']) ? 'italic' : 'normal') . ';
        text-decoration: ' . (!empty($options['desc_underline']) ? 'underline' : 'none') . ';
        margin-bottom: 10px;
        text-align: ' . esc_attr($options['desc_align'] ?? 'center') . ';
        width: 100%;
    }
    
    .dicalapi-gcalendar-location {
        color: ' . esc_attr($options['location_color'] ?? '#888888') . ';
        font-size: ' . esc_attr($options['location_size'] ?? '14px') . ';
        font-family: ' . (!empty($options['location_font']) ? "'" . esc_attr($options['location_font']) . "'" : 'inherit') . ';
        font-weight: ' . (!empty($options['location_bold']) ? 'bold' : 'normal') . ';
        font-style: ' . (!empty($options['location_italic']) ? 'italic' : 'normal') . ';
        text-decoration: ' . (!empty($options['location_underline']) ? 'underline' : 'none') . ';
        display: flex;
        align-items: center;
        text-align: ' . esc_attr($options['location_align'] ?? 'center') . ';
        justify-content: ' . (esc_attr($options['location_align'] ?? 'center') == 'center' ? 'center' : (esc_attr($options['location_align'] ?? 'center') == 'left' ? 'flex-start' : 'flex-end')) . ';
        justify-content: center;
        width: 100%;
    }
    
    .dicalapi-gcalendar-location .dashicons {
        margin-right: 5px;
    }
    
    .dicalapi-gcalendar-error, 
    .dicalapi-gcalendar-no-events {
        padding: 15px;
        background-color: #f8d7da;
        color: #721c24;
        border-radius: 4px;
        margin: 10px 0;
    }
    
    .dicalapi-gcalendar-no-events {
        background-color: #e2e3e5;
        color: #383d41;
    }
    
    @media (max-width: 767px) {
        .dicalapi-gcalendar-event {
            flex-direction: column;
        }
        
        .dicalapi-gcalendar-date-column,
        .dicalapi-gcalendar-content-column,
        .dicalapi-gcalendar-signup-column {
            width: 100%;
            max-width: none;
        }
        
        .dicalapi-gcalendar-signup-column {
            padding: 15px;
        }
        
        .dicalapi-gcalendar-signup-button {
            width: 80%;
            padding: 10px;
        }
    }
    ';
    
    return $css;
}

/**
 * Generar CSS dinámico para el shortcode de títulos
 */
function dicalapi_gcalendar_generate_title_css($options) {
    $css = '
    .dicalapi-gcalendar-ticker-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        overflow: hidden;
        position: relative;
        width: 100%;
        height: auto;
        line-height: 1.5;
        margin: 0;
        padding: 0;
        text-align: center;
    }
    
    .dicalapi-gcalendar-ticker-single {
        text-align: center;
        padding: 0;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .dicalapi-gcalendar-ticker-wrapper {
        position: relative;
        overflow: hidden;
        padding: 0;
        margin: 0 auto;
        width: 100%;
    }
    
    .dicalapi-gcalendar-ticker-viewport {
        overflow: hidden;
        position: relative;
        height: 30px; /* Se ajustará mediante JavaScript */
        padding: 0;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }
    
    .dicalapi-gcalendar-ticker-list {
        list-style: none;
        padding: 0;
        margin: 0 auto;
        position: relative;
        text-align: center;
        width: 100%;
        height: 100%;
    }
    
    .dicalapi-gcalendar-ticker-item {
        padding: 0;
        margin: 0 auto;
        box-sizing: border-box;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: 100%;
        top: 0;
        left: 0;
        right: 0;
        text-align: center;
    }
    
    .dicalapi-gcalendar-title-text {
        color: ' . esc_attr($options['title_text_color'] ?? '#333333') . ' !important;
        font-size: ' . esc_attr($options['title_text_size'] ?? '18px') . ' !important;
        font-weight: bold !important;
        margin: 0;
        padding: 0;
        text-align: center;
        margin-right: 3px !important;
    }
    
    .dicalapi-gcalendar-title-dates {
        color: ' . esc_attr($options['title_date_color'] ?? '#007bff') . ' !important;
        font-size: ' . esc_attr($options['title_date_size'] ?? '16px') . ' !important;
        margin-left: 3px !important;
        padding: 0;
        text-align: center;
    }
    
    @media (max-width: 767px) {
        .dicalapi-gcalendar-ticker-item {
            white-space: normal;
            flex-direction: column;
        }
        
        .dicalapi-gcalendar-title-text,
        .dicalapi-gcalendar-title-dates {
            display: block;
            text-align: center;
        }
        
        .dicalapi-gcalendar-title-dates {
            margin-left: 0;
            margin-top: 5px;
        }
    }
    ';
    
    return $css;
}
