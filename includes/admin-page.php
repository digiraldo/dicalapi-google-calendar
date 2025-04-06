<?php
/**
 * Página de administración para el plugin DICALAPI Google Calendar Events
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Agregar menú en el panel de administración
function dicalapi_gcalendar_add_admin_menu() {
    add_options_page(
        'DICALAPI Google Calendar', 
        'Google Calendar', 
        'manage_options', 
        'dicalapi-gcalendar-settings', 
        'dicalapi_gcalendar_settings_page'
    );
}
add_action('admin_menu', 'dicalapi_gcalendar_add_admin_menu');

// Registrar configuraciones
function dicalapi_gcalendar_settings_init() {
    register_setting('dicalapi_gcalendar', 'dicalapi_gcalendar_options');

    // Sección de API
    add_settings_section(
        'dicalapi_gcalendar_api_section',
        __('Configuración de API de Google Calendar', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_api_section_callback',
        'dicalapi-gcalendar'
    );

    add_settings_field(
        'calendar_id',
        __('ID del Calendario', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_calendar_id_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_api_section'
    );

    add_settings_field(
        'api_key',
        __('API Key de Google', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_api_key_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_api_section'
    );

    add_settings_field(
        'max_events',
        __('Número máximo de eventos a mostrar', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_max_events_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_api_section'
    );

    // Sección de estilos
    add_settings_section(
        'dicalapi_gcalendar_styles_section',
        __('Personalización de estilos', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_styles_section_callback',
        'dicalapi-gcalendar'
    );

    // Columnas
    add_settings_field(
        'column1_bg',
        __('Color de fondo columna de fechas', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_column1_bg_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    add_settings_field(
        'column2_bg',
        __('Color de fondo columna de contenido', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_column2_bg_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    add_settings_field(
        'row_shadow',
        __('Sombra de la fila', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_row_shadow_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    // Texto y colores
    add_settings_field(
        'title_style',
        __('Estilo del título', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_style_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    add_settings_field(
        'desc_style',
        __('Estilo de la descripción', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_desc_style_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    add_settings_field(
        'location_style',
        __('Estilo del lugar', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_location_style_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    add_settings_field(
        'date_style',
        __('Estilo de las fechas', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_date_style_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    add_settings_field(
        'column3_bg',
        __('Color de fondo columna de inscripción', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_column3_bg_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    // Sección de inscripción
    add_settings_section(
        'dicalapi_gcalendar_signup_section',
        __('Configuración de inscripción', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_signup_section_callback',
        'dicalapi-gcalendar'
    );

    add_settings_field(
        'signup_url',
        __('URL de inscripción predeterminada', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_signup_url_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_signup_section'
    );

    add_settings_field(
        'signup_button_text',
        __('Texto del botón', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_signup_button_text_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_signup_section'
    );

    add_settings_field(
        'button_colors',
        __('Colores del botón', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_button_colors_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_signup_section'
    );

    // Nueva sección para shortcode de títulos
    add_settings_section(
        'dicalapi_gcalendar_title_shortcode_section',
        __('Configuración del shortcode de títulos [dicalapi-gcalendar-titulo]', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_shortcode_section_callback',
        'dicalapi-gcalendar'
    );

    add_settings_field(
        'title_text_style',
        __('Estilo del texto del título', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_text_style_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_title_shortcode_section'
    );

    add_settings_field(
        'title_date_style',
        __('Estilo de las fechas', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_date_style_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_title_shortcode_section'
    );

    add_settings_field(
        'title_scroll_interval',
        __('Tiempo entre transiciones (segundos)', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_scroll_interval_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_title_shortcode_section'
    );

    add_settings_field(
        'title_widget_style',
        __('Estilo del widget', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_widget_style_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_title_shortcode_section'
    );

    add_settings_field(
        'title_indicator_color',
        __('Color de los indicadores', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_indicator_color_render',
        'dicalapi-gcalendar',
        'dicalapi_gcalendar_title_shortcode_section'
    );
}
add_action('admin_init', 'dicalapi_gcalendar_settings_init');

// Callbacks de secciones
function dicalapi_gcalendar_api_section_callback() {
    echo '<p>' . __('Configura los datos de acceso a la API de Google Calendar.', 'dicalapi-gcalendar') . '</p>';
}

function dicalapi_gcalendar_styles_section_callback() {
    echo '<p>' . __('Personaliza el aspecto visual de los eventos mostrados.', 'dicalapi-gcalendar') . '</p>';
}

function dicalapi_gcalendar_signup_section_callback() {
    echo '<p>' . __('Configura las opciones para la inscripción a eventos.', 'dicalapi-gcalendar') . '</p>';
    echo '<p>' . __('Para configurar una URL personalizada para un evento específico, incluye [signup_url:https://ejemplo.com] en la descripción del evento de Google Calendar.', 'dicalapi-gcalendar') . '</p>';
}

function dicalapi_gcalendar_title_shortcode_section_callback() {
    echo '<p>' . __('Configura el aspecto y comportamiento del shortcode para mostrar títulos de eventos con scroll automático.', 'dicalapi-gcalendar') . '</p>';
}

// Callbacks de campos
function dicalapi_gcalendar_calendar_id_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <input type='text' class="regular-text" name='dicalapi_gcalendar_options[calendar_id]' value='<?php echo esc_attr($options['calendar_id'] ?? ''); ?>'>
    <p class="description"><?php _e('Introduce el ID del calendario de Google. Por ejemplo: example@gmail.com', 'dicalapi-gcalendar'); ?></p>
    <?php
}

function dicalapi_gcalendar_api_key_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <input type='text' class="regular-text" name='dicalapi_gcalendar_options[api_key]' value='<?php echo esc_attr($options['api_key'] ?? ''); ?>'>
    <p class="description"><?php _e('Introduce tu API Key de Google Calendar.', 'dicalapi-gcalendar'); ?></p>
    <?php
}

function dicalapi_gcalendar_max_events_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <input type='number' min='1' max='50' name='dicalapi_gcalendar_options[max_events]' value='<?php echo esc_attr($options['max_events'] ?? 10); ?>'>
    <?php
}

function dicalapi_gcalendar_column1_bg_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[column1_bg]' value='<?php echo esc_attr($options['column1_bg'] ?? '#f8f9fa'); ?>'>
    <?php
}

function dicalapi_gcalendar_column2_bg_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[column2_bg]' value='<?php echo esc_attr($options['column2_bg'] ?? '#ffffff'); ?>'>
    <?php
}

function dicalapi_gcalendar_row_shadow_render() {
    $options = get_option('dicalapi_gcalendar_options');
    $current_shadow = $options['row_shadow'] ?? '0px 2px 5px rgba(0,0,0,0.1)';
    
    // Define las opciones de sombra predefinidas
    $shadow_presets = array(
        'none' => __('Sin sombra', 'dicalapi-gcalendar'),
        '0px 2px 5px rgba(0,0,0,0.1)' => __('Sutil', 'dicalapi-gcalendar'),
        '0px 4px 8px rgba(0,0,0,0.15)' => __('Media', 'dicalapi-gcalendar'),
        '0px 6px 12px rgba(0,0,0,0.2)' => __('Pronunciada', 'dicalapi-gcalendar'),
        '0px 8px 16px rgba(0,0,0,0.25)' => __('Fuerte', 'dicalapi-gcalendar'),
        '0px 12px 24px rgba(0,0,0,0.3)' => __('Muy fuerte', 'dicalapi-gcalendar'),
        '0px 3px 6px rgba(0,0,0,0.1), 0px 6px 12px rgba(0,0,0,0.15)' => __('Doble sombra', 'dicalapi-gcalendar'),
        '0px 4px 8px rgba(0,70,150,0.2)' => __('Azul', 'dicalapi-gcalendar'),
        '0px 4px 8px rgba(120,0,0,0.2)' => __('Rojo', 'dicalapi-gcalendar'),
        'custom' => __('Personalizada', 'dicalapi-gcalendar')
    );
    
    // Menú desplegable para sombras predefinidas
    ?>
    <div class="dicalapi-shadow-control">
        <div class="dicalapi-shadow-presets">
            <label for="dicalapi-shadow-preset"><?php _e('Sombra predefinida:', 'dicalapi-gcalendar'); ?></label>
            <select id="dicalapi-shadow-preset" class="regular-text">
                <?php 
                $selected_preset = 'custom';
                foreach ($shadow_presets as $value => $label) {
                    $is_selected = ($value === $current_shadow) ? 'selected' : '';
                    if ($is_selected) $selected_preset = $value;
                    echo '<option value="' . esc_attr($value) . '" ' . $is_selected . '>' . esc_html($label) . '</option>';
                }
                // Si no coincide con ninguna opción predefinida, seleccionar "Personalizada"
                if ($selected_preset === 'custom') {
                    echo '<script>document.addEventListener("DOMContentLoaded", function() { 
                        document.getElementById("dicalapi-shadow-preset").value = "custom";
                    });</script>';
                }
                ?>
            </select>
        </div>
        
        <div class="dicalapi-shadow-custom" <?php echo ($selected_preset !== 'custom') ? 'style="display:none;"' : ''; ?>>
            <label for="dicalapi-shadow-custom-input"><?php _e('Valor CSS personalizado:', 'dicalapi-gcalendar'); ?></label>
            <input type="text" id="dicalapi-shadow-custom-input" class="regular-text" value="<?php echo esc_attr($current_shadow); ?>">
        </div>
        
        <div class="dicalapi-shadow-preview">
            <label><?php _e('Vista previa de la sombra:', 'dicalapi-gcalendar'); ?></label>
            <div id="dicalapi-shadow-preview-box" style="box-shadow: <?php echo esc_attr($current_shadow); ?>">
                <?php _e('Ejemplo de sombra', 'dicalapi-gcalendar'); ?>
            </div>
        </div>
        
        <!-- Campo oculto que almacena el valor final -->
        <input type="hidden" name="dicalapi_gcalendar_options[row_shadow]" id="dicalapi-shadow-final-value" value="<?php echo esc_attr($current_shadow); ?>">
    </div>
    <?php
}

function dicalapi_gcalendar_title_style_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <label><?php _e('Color:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[title_color]' value='<?php echo esc_attr($options['title_color'] ?? '#333333'); ?>'>
    <label><?php _e('Tamaño:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' name='dicalapi_gcalendar_options[title_size]' value='<?php echo esc_attr($options['title_size'] ?? '18px'); ?>'>
    <?php
}

function dicalapi_gcalendar_desc_style_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <label><?php _e('Color:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[desc_color]' value='<?php echo esc_attr($options['desc_color'] ?? '#666666'); ?>'>
    <label><?php _e('Tamaño:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' name='dicalapi_gcalendar_options[desc_size]' value='<?php echo esc_attr($options['desc_size'] ?? '14px'); ?>'>
    <?php
}

function dicalapi_gcalendar_location_style_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <label><?php _e('Color:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[location_color]' value='<?php echo esc_attr($options['location_color'] ?? '#888888'); ?>'>
    <label><?php _e('Tamaño:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' name='dicalapi_gcalendar_options[location_size]' value='<?php echo esc_attr($options['location_size'] ?? '14px'); ?>'>
    <?php
}

function dicalapi_gcalendar_date_style_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <label><?php _e('Color:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[date_color]' value='<?php echo esc_attr($options['date_color'] ?? '#007bff'); ?>'>
    <label><?php _e('Tamaño:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' name='dicalapi_gcalendar_options[date_size]' value='<?php echo esc_attr($options['date_size'] ?? '16px'); ?>'>
    <?php
}

function dicalapi_gcalendar_column3_bg_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[column3_bg]' value='<?php echo esc_attr($options['column3_bg'] ?? '#f0f0f0'); ?>'>
    <?php
}

function dicalapi_gcalendar_signup_url_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <input type='url' class="regular-text" name='dicalapi_gcalendar_options[signup_url]' value='<?php echo esc_attr($options['signup_url'] ?? ''); ?>'>
    <p class="description"><?php _e('URL del formulario de inscripción predeterminado para todos los eventos.', 'dicalapi-gcalendar'); ?></p>
    <?php
}

function dicalapi_gcalendar_signup_button_text_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <input type='text' class="regular-text" name='dicalapi_gcalendar_options[signup_button_text]' value='<?php echo esc_attr($options['signup_button_text'] ?? 'Inscribirse'); ?>'>
    <?php
}

function dicalapi_gcalendar_button_colors_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <label><?php _e('Color de fondo:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[button_bg_color]' value='<?php echo esc_attr($options['button_bg_color'] ?? '#007bff'); ?>'>
    
    <label><?php _e('Color al pasar el ratón:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[button_hover_bg_color]' value='<?php echo esc_attr($options['button_hover_bg_color'] ?? '#0056b3'); ?>'>
    
    <label><?php _e('Color del texto:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[button_text_color]' value='<?php echo esc_attr($options['button_text_color'] ?? '#ffffff'); ?>'>
    
    <label><?php _e('Tamaño del texto:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' name='dicalapi_gcalendar_options[button_text_size]' value='<?php echo esc_attr($options['button_text_size'] ?? '14px'); ?>'>
    <?php
}

function dicalapi_gcalendar_title_text_style_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <label><?php _e('Color:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[title_text_color]' value='<?php echo esc_attr($options['title_text_color'] ?? '#333333'); ?>'>
    <label><?php _e('Tamaño:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' name='dicalapi_gcalendar_options[title_text_size]' value='<?php echo esc_attr($options['title_text_size'] ?? '18px'); ?>'>
    <?php
}

function dicalapi_gcalendar_title_date_style_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <label><?php _e('Color:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[title_date_color]' value='<?php echo esc_attr($options['title_date_color'] ?? '#007bff'); ?>'>
    <label><?php _e('Tamaño:', 'dicalapi-gcalendar'); ?></label>
    <input type='text' name='dicalapi_gcalendar_options[title_date_size]' value='<?php echo esc_attr($options['title_date_size'] ?? '16px'); ?>'>
    <?php
}

function dicalapi_gcalendar_title_scroll_interval_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <input type='number' min='1' max='20' name='dicalapi_gcalendar_options[title_scroll_interval]' value='<?php echo esc_attr($options['title_scroll_interval'] ?? 5); ?>'>
    <p class="description"><?php _e('Tiempo en segundos entre cada cambio de evento.', 'dicalapi-gcalendar'); ?></p>
    <?php
}

function dicalapi_gcalendar_title_widget_style_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <p class="description"><?php _e('El ticker de títulos no tiene fondo ni sombra para mostrar solo el texto en movimiento.', 'dicalapi-gcalendar'); ?></p>
    <input type='hidden' name='dicalapi_gcalendar_options[title_widget_bg]' value='transparent'>
    <input type='hidden' name='dicalapi_gcalendar_options[title_widget_shadow]' value='none'>
    <?php
}

function dicalapi_gcalendar_title_indicator_color_render() {
    echo '<input type="hidden" name="dicalapi_gcalendar_options[title_indicator_color]" value="#007bff">';
}

// Agregar un ejemplo visual de cómo se verán los eventos
function dicalapi_gcalendar_display_preview() {
    $options = get_option('dicalapi_gcalendar_options');
    $css = dicalapi_gcalendar_generate_dynamic_css($options);
    
    echo '<div class="dicalapi-preview-section">';
    echo '<h2>' . __('Vista previa', 'dicalapi-gcalendar') . '</h2>';
    echo '<p>' . __('Así es como se verán tus eventos con la configuración actual:', 'dicalapi-gcalendar') . '</p>';
    
    echo '<style>' . $css . '</style>';
    
    echo '<div class="dicalapi-gcalendar-container">';
    echo '<div class="dicalapi-gcalendar-event-wrapper">';
    echo '<div class="dicalapi-gcalendar-event" id="dicalapi-preview-event">';
    
    // Columna de fechas
    echo '<div class="dicalapi-gcalendar-date-column">';
    echo '<div class="dicalapi-gcalendar-day">15</div>';
    echo '<div class="dicalapi-gcalendar-month">Oct</div>';
    echo '</div>';
    
    // Columna de contenido
    echo '<div class="dicalapi-gcalendar-content-column">';
    echo '<h3 class="dicalapi-gcalendar-title">Evento de ejemplo</h3>';
    echo '<div class="dicalapi-gcalendar-description">Esta es una descripción de ejemplo para mostrar cómo se verán tus eventos.</div>';
    echo '<div class="dicalapi-gcalendar-location"><span class="dashicons dashicons-location"></span> Lugar de ejemplo</div>';
    echo '</div>';
    
    // Columna de inscripción
    echo '<div class="dicalapi-gcalendar-signup-column">';
    $button_text = !empty($options['signup_button_text']) ? $options['signup_button_text'] : __('Inscribirse', 'dicalapi-gcalendar');
    echo '<a href="#" class="dicalapi-gcalendar-signup-button">' . esc_html($button_text) . '</a>';
    echo '</div>';
    
    echo '</div>'; // Fin evento
    echo '</div>'; // Fin wrapper
    echo '</div>'; // Fin contenedor
    
    echo '</div>'; // Fin sección preview
}

// Función para vista previa del shortcode de títulos
function dicalapi_gcalendar_display_title_shortcode_preview() {
    $options = get_option('dicalapi_gcalendar_options');
    $scroll_interval = isset($options['title_scroll_interval']) ? intval($options['title_scroll_interval']) : 5;
    $scroll_interval_ms = $scroll_interval * 1000;
    
    $css = dicalapi_gcalendar_generate_title_css($options);
    
    echo '<div class="dicalapi-preview-section">';
    echo '<h2>' . __('Vista previa del shortcode de títulos', 'dicalapi-gcalendar') . '</h2>';
    echo '<p>' . __('Así es como se verá el shortcode [dicalapi-gcalendar-titulo] con la configuración actual:', 'dicalapi-gcalendar') . '</p>';
    
    echo '<style>' . $css . '</style>';
    
    echo '<div class="dicalapi-gcalendar-ticker-container" id="dicalapi-admin-preview-ticker" style="border: 1px solid #ddd; padding: 10px; border-radius: 5px;">';
    
    // Ejemplo de eventos para la vista previa
    $example_events = array(
        array('title' => 'Evento de ejemplo 1', 'dates' => '15 Oct - 18 Oct'),
        array('title' => 'Evento de ejemplo 2', 'dates' => '22 Oct'),
        array('title' => 'Evento de ejemplo 3', 'dates' => '5 Nov - 6 Nov')
    );
    
    echo '<div class="dicalapi-gcalendar-ticker-wrapper" data-interval="' . esc_attr($scroll_interval_ms) . '">';
    echo '<div class="dicalapi-gcalendar-ticker-viewport">';
    echo '<ul class="dicalapi-gcalendar-ticker-list" id="admin-preview-ticker-list">';
    
    foreach ($example_events as $index => $event) {
        echo '<li class="dicalapi-gcalendar-ticker-item" style="' . ($index > 0 ? 'position:absolute;visibility:hidden;opacity:0;' : 'position:relative;visibility:visible;opacity:1;') . '">';
        echo '<span class="dicalapi-gcalendar-title-text" style="color:' . esc_attr($options['title_text_color'] ?? '#333333') . ';font-size:' . esc_attr($options['title_text_size'] ?? '18px') . ';font-weight:bold;">' . $event['title'] . '</span> ';
        echo '<span class="dicalapi-gcalendar-title-dates" style="color:' . esc_attr($options['title_date_color'] ?? '#007bff') . ';font-size:' . esc_attr($options['title_date_size'] ?? '16px') . ';">(' . $event['dates'] . ')</span>';
        echo '</li>';
    }
    
    echo '</ul>';
    echo '</div>'; // Fin viewport
    echo '</div>'; // Fin wrapper
    echo '</div>'; // Fin contenedor
    
    // Script para la vista previa con funcionalidad mejorada
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inicio del código para la vista previa
        (function() {
            const container = document.getElementById("dicalapi-admin-preview-ticker");
            if (!container) return;
            
            const wrapper = container.querySelector(".dicalapi-gcalendar-ticker-wrapper");
            if (!wrapper) return;
            
            const items = container.querySelectorAll(".dicalapi-gcalendar-ticker-item");
            if (items.length <= 1) return;
            
            const interval = parseInt(wrapper.getAttribute("data-interval")) || 5000;
            const viewport = container.querySelector(".dicalapi-gcalendar-ticker-viewport");
            
            // Asegurarse de que la altura del viewport sea correcta
            const itemHeight = items[0].offsetHeight;
            viewport.style.height = itemHeight + "px";
            
            // Variables de control
            let currentIndex = 0;
            let isAnimating = false;
            let previewInterval;
            
            function animatePreview() {
                if (isAnimating) return;
                isAnimating = true;
                
                // Get current and next items
                const currentItem = items[currentIndex];
                const nextIndex = (currentIndex + 1) % items.length;
                const nextItem = items[nextIndex];
                
                // Prepare next item
                nextItem.style.transform = "translateY(100%)";
                nextItem.style.visibility = "visible";
                nextItem.style.opacity = "0";
                
                // Animate
                requestAnimationFrame(function() {
                    // Fade out current item while moving up
                    currentItem.style.transition = "transform 0.7s ease, opacity 0.7s ease";
                    currentItem.style.transform = "translateY(-100%)";
                    currentItem.style.opacity = "0";
                    
                    // Fade in next item while moving up
                    nextItem.style.transition = "transform 0.7s ease, opacity 0.7s ease";
                    nextItem.style.transform = "translateY(0)";
                    nextItem.style.opacity = "1";
                    
                    // Wait for animation to complete
                    setTimeout(function() {
                        // Reset position of current item
                        currentItem.style.transition = "none";
                        currentItem.style.transform = "translateY(100%)";
                        currentItem.style.visibility = "hidden";
                        
                        // Update index
                        currentIndex = nextIndex;
                        isAnimating = false;
                    }, 700);
                });
            }
            
            // Start preview animation
            previewInterval = setInterval(animatePreview, interval);
            
            // Actualizar estilos cuando cambian los inputs
            function updatePreviewStyles() {
                const titleColor = document.querySelector("input[name=\'dicalapi_gcalendar_options[title_text_color]\']").value || "#333333";
                const titleSize = document.querySelector("input[name=\'dicalapi_gcalendar_options[title_text_size]\']").value || "18px";
                const dateColor = document.querySelector("input[name=\'dicalapi_gcalendar_options[title_date_color]\']").value || "#007bff";
                const dateSize = document.querySelector("input[name=\'dicalapi_gcalendar_options[title_date_size]\']").value || "16px";
                
                // Update all title texts
                const titleElements = container.querySelectorAll(".dicalapi-gcalendar-title-text");
                titleElements.forEach(function(el) {
                    el.style.color = titleColor;
                    el.style.fontSize = titleSize;
                });
                
                // Update all date texts
                const dateElements = container.querySelectorAll(".dicalapi-gcalendar-title-dates");
                dateElements.forEach(function(el) {
                    el.style.color = dateColor;
                    el.style.fontSize = dateSize;
                });
            }
            
            // Monitor changes to color and text inputs
            const colorInputs = document.querySelectorAll("input[name^=\'dicalapi_gcalendar_options[title_\']");
            colorInputs.forEach(function(input) {
                input.addEventListener("input", updatePreviewStyles);
                input.addEventListener("change", updatePreviewStyles);
            });
            
            // Special handling for WP color pickers
            jQuery(function($) {
                $(".wp-color-picker").wpColorPicker({
                    change: function(event, ui) {
                        setTimeout(updatePreviewStyles, 50);
                    }
                });
            });
            
            // Monitor changes to interval
            const intervalInput = document.querySelector("input[name=\'dicalapi_gcalendar_options[title_scroll_interval]\']");
            if (intervalInput) {
                intervalInput.addEventListener("change", function() {
                    const newInterval = parseInt(this.value) * 1000;
                    clearInterval(previewInterval);
                    previewInterval = setInterval(animatePreview, newInterval);
                });
            }
            
            // Clean up on page visibility change
            document.addEventListener("visibilitychange", function() {
                if (document.hidden) {
                    clearInterval(previewInterval);
                } else {
                    clearInterval(previewInterval);
                    previewInterval = setInterval(animatePreview, interval);
                }
            });
        })();
    });
    </script>';
    
    echo '</div>'; // Fin sección preview
}

// Página de configuración
function dicalapi_gcalendar_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action='options.php' method='post'>
            <?php
            settings_fields('dicalapi_gcalendar');
            do_settings_sections('dicalapi-gcalendar');
            submit_button();
            ?>
        </form>
        
        <?php 
        // Mostrar vista previa del shortcode principal
        dicalapi_gcalendar_display_preview();
        
        // Mostrar vista previa del shortcode de títulos
        dicalapi_gcalendar_display_title_shortcode_preview();
        ?>
        
        <div class="dicalapi-shortcode-info">
            <h2><?php _e('Cómo usar los shortcodes', 'dicalapi-gcalendar'); ?></h2>
            
            <h3><?php _e('Shortcode de eventos completos', 'dicalapi-gcalendar'); ?></h3>
            <p><?php _e('Para mostrar los eventos de Google Calendar en formato detallado, utiliza:', 'dicalapi-gcalendar'); ?></p>
            <code>[dicalapi_gcalendar]</code>
            <p><?php _e('También puedes especificar un número personalizado de eventos a mostrar:', 'dicalapi-gcalendar'); ?></p>
            <code>[dicalapi_gcalendar max_events="5"]</code>
            
            <h3><?php _e('Shortcode de títulos con scroll', 'dicalapi-gcalendar'); ?></h3>
            <p><?php _e('Para mostrar solo los títulos y fechas de eventos con scroll automático, utiliza:', 'dicalapi-gcalendar'); ?></p>
            <code>[dicalapi-gcalendar-titulo]</code>
            <p><?php _e('También puedes especificar un número personalizado de eventos a mostrar:', 'dicalapi-gcalendar'); ?></p>
            <code>[dicalapi-gcalendar-titulo max_events="5"]</code>
        </div>
        
        <div class="dicalapi-url-custom-info">
            <h2><?php _e('Cómo configurar URLs específicas para eventos individuales', 'dicalapi-gcalendar'); ?></h2>
            <p><?php _e('Puedes configurar una URL de inscripción personalizada para cada evento incluyendo el siguiente código en la descripción del evento en Google Calendar:', 'dicalapi-gcalendar'); ?></p>
            <code>[signup_url:https://tuformulario.com/inscripcion]</code>
            <p><?php _e('Esto sobrescribirá la URL predeterminada configurada en este panel solo para ese evento específico.', 'dicalapi-gcalendar'); ?></p>
        </div>
    </div>
    <?php
}
