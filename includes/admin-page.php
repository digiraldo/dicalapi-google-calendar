<?php
/**
 * Página de administración para el plugin DICALAPI Google Calendar Events
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar scripts y estilos para el admin
 */
function dicalapi_gcalendar_admin_scripts($hook) {
    // Solo cargar en la página de nuestro plugin
    if ($hook != 'settings_page_dicalapi_gcalendar') {
        return;
    }
    
    // Cargar color picker de WordPress
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    
    // Cargar Google Fonts para el selector de fuentes
    wp_enqueue_style('dicalapi-google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@300;400;600;700&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;700&family=Poppins:wght@300;400;500;600&family=Raleway:wght@300;400;500;600&family=Source+Sans+Pro:wght@300;400;600&family=Ubuntu:wght@300;400;500;700&family=Playfair+Display:wght@400;500;600;700&family=Merriweather:wght@300;400;700&display=swap', array());
    
    // Cargar select2 para mejorar selección de fuentes
    wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array());
    wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), null, true);
    
    // Cargar nuestros estilos y scripts
    wp_enqueue_style('dicalapi-gcalendar-admin', DICALAPI_GCALENDAR_PLUGIN_URL . 'assets/css/admin.css', array(), DICALAPI_GCALENDAR_VERSION);
    wp_enqueue_script('dicalapi-gcalendar-admin', DICALAPI_GCALENDAR_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'wp-color-picker', 'select2'), DICALAPI_GCALENDAR_VERSION, true);
    
    // Cargar Dashicons para iconos
    wp_enqueue_style('dashicons');
}
add_action('admin_enqueue_scripts', 'dicalapi_gcalendar_admin_scripts');

/**
 * Registrar la página de opciones del plugin
 */
function dicalapi_gcalendar_add_admin_menu() {
    add_options_page(
        __('Configuración de Google Calendar', 'dicalapi-google-calendar-events'),
        __('Google Calendar', 'dicalapi-google-calendar-events'),
        'manage_options',
        'dicalapi_gcalendar', // Este slug debe coincidir con el utilizado en register_setting
        'dicalapi_gcalendar_options_page'
    );
}
add_action('admin_menu', 'dicalapi_gcalendar_add_admin_menu');

/**
 * Registrar opciones del plugin
 */
function dicalapi_gcalendar_register_settings() {
    // Registrar el grupo de opciones con el mismo slug que la página
    register_setting(
        'dicalapi_gcalendar', // Debe coincidir con el slug de la página
        'dicalapi_gcalendar_options',
        array(
            'sanitize_callback' => 'dicalapi_gcalendar_sanitize_options',
            'default' => array()
        )
    );

    // Sección de API
    add_settings_section(
        'dicalapi_gcalendar_api_section',
        __('Configuración de API de Google Calendar', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_api_section_callback',
        'dicalapi_gcalendar'
    );

    add_settings_field(
        'calendar_id',
        __('ID del Calendario', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_calendar_id_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_api_section'
    );

    add_settings_field(
        'api_key',
        __('API Key de Google', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_api_key_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_api_section'
    );

    add_settings_field(
        'max_events',
        __('Número máximo de eventos a mostrar', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_max_events_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_api_section'
    );

    // Sección de estilos
    add_settings_section(
        'dicalapi_gcalendar_styles_section',
        __('Personalización de estilos', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_styles_section_callback',
        'dicalapi_gcalendar'
    );

    // Agrupamos los colores de fondo de columnas en un solo campo
    add_settings_field(
        'column_bg_colors',
        __('Colores de fondo de columnas', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_column_bg_colors_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    // Sombra de la fila (mantener como está)
    add_settings_field(
        'row_shadow',
        __('Sombra de la fila', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_row_shadow_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    // Texto y colores
    add_settings_field(
        'title_style',
        __('Estilo del título', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_style_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    add_settings_field(
        'desc_style',
        __('Estilo de la descripción', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_desc_style_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    add_settings_field(
        'location_style',
        __('Estilo del lugar', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_location_style_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    add_settings_field(
        'date_style',
        __('Estilo de las fechas', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_date_style_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_styles_section'
    );

    // Sección de inscripción
    add_settings_section(
        'dicalapi_gcalendar_signup_section',
        __('Configuración de inscripción', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_signup_section_callback',
        'dicalapi_gcalendar'
    );

    add_settings_field(
        'signup_url',
        __('URL de inscripción predeterminada', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_signup_url_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_signup_section'
    );

    add_settings_field(
        'signup_button_text',
        __('Texto del botón', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_signup_button_text_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_signup_section'
    );

    add_settings_field(
        'button_colors',
        __('Colores del botón', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_button_colors_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_signup_section'
    );

    // Nueva sección para shortcode de títulos
    add_settings_section(
        'dicalapi_gcalendar_title_shortcode_section',
        __('Configuración del shortcode de títulos [dicalapi-gcalendar-titulo]', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_shortcode_section_callback',
        'dicalapi_gcalendar'
    );

    add_settings_field(
        'title_text_style',
        __('Estilo del texto del título', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_text_style_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_title_shortcode_section'
    );

    add_settings_field(
        'title_date_style',
        __('Estilo de las fechas', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_date_style_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_title_shortcode_section'
    );

    add_settings_field(
        'title_scroll_interval',
        __('Tiempo entre transiciones (segundos)', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_scroll_interval_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_title_shortcode_section'
    );

    add_settings_field(
        'title_widget_style',
        __('Estilo del widget', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_widget_style_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_title_shortcode_section'
    );

    add_settings_field(
        'title_indicator_color',
        __('Color de los indicadores', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_title_indicator_color_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_title_shortcode_section'
    );

    // Añadir nueva sección para configuración de vistas previas
    add_settings_section(
        'dicalapi_gcalendar_preview_section',
        __('Configuración de vistas previas (solo panel admin)', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_preview_section_callback',
        'dicalapi_gcalendar'
    );

    add_settings_field(
        'preview_bg_color',
        __('Color de fondo de vistas previas', 'dicalapi-gcalendar'),
        'dicalapi_gcalendar_preview_bg_color_render',
        'dicalapi_gcalendar',
        'dicalapi_gcalendar_preview_section'
    );
}
add_action('admin_init', 'dicalapi_gcalendar_register_settings');

/**
 * Renderizar la página de opciones
 */
function dicalapi_gcalendar_options_page() {
    ?>
    <div class="wrap dicalapi-admin-wrap">
        <h1><?php echo esc_html__('Configuración de Google Calendar', 'dicalapi-google-calendar-events'); ?></h1>
        
        <?php 
        // Determinar la pestaña activa
        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
        ?>
        
        <nav class="nav-tab-wrapper">
            <a href="?page=dicalapi_gcalendar&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-admin-settings"></span> <?php _e('Configuración General', 'dicalapi-gcalendar'); ?>
            </a>
            <a href="?page=dicalapi_gcalendar&tab=styles" class="nav-tab <?php echo $active_tab == 'styles' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-admin-customizer"></span> <?php _e('Personalización', 'dicalapi-gcalendar'); ?>
            </a>
            <a href="?page=dicalapi_gcalendar&tab=preview" class="nav-tab <?php echo $active_tab == 'preview' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-visibility"></span> <?php _e('Vista Previa', 'dicalapi-gcalendar'); ?>
            </a>
            <a href="?page=dicalapi_gcalendar&tab=help" class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-editor-help"></span> <?php _e('Ayuda', 'dicalapi-gcalendar'); ?>
            </a>
        </nav>
        
        <div class="tab-content">
            <form method="post" action="options.php">
                <?php settings_fields('dicalapi_gcalendar'); ?>
                
                <?php if ($active_tab == 'general'): ?>
                    <div id="tab-general" class="tab-pane active">
                        <div class="dicalapi-settings-section">
                            <h2><?php _e('Configuración de API', 'dicalapi-gcalendar'); ?></h2>
                            <p class="section-description"><?php _e('Conecta tu calendario de Google para mostrar eventos.', 'dicalapi-gcalendar'); ?></p>
                            <table class="form-table">
                                <tbody>
                                    <?php 
                                    do_settings_fields('dicalapi_gcalendar', 'dicalapi_gcalendar_api_section');
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="dicalapi-settings-section">
                            <h2><?php _e('Configuración de inscripción', 'dicalapi-gcalendar'); ?></h2>
                            <p class="section-description"><?php _e('Define la URL y texto del botón de inscripción a eventos.', 'dicalapi-gcalendar'); ?></p>
                            <table class="form-table">
                                <tbody>
                                    <?php 
                                    do_settings_fields('dicalapi_gcalendar', 'dicalapi_gcalendar_signup_section');
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                <?php elseif ($active_tab == 'styles'): ?>
                    <div id="tab-styles" class="tab-pane active">
                        <div class="dicalapi-settings-columns">
                            <div class="dicalapi-settings-column">
                                <div class="dicalapi-settings-section">
                                    <h2><?php _e('Estilo General', 'dicalapi-gcalendar'); ?></h2>
                                    <table class="form-table">
                                        <tbody>
                                            <?php 
                                            // Campos de color de columnas y sombra
                                            dicalapi_gcalendar_column_bg_colors_render();
                                            dicalapi_gcalendar_row_shadow_render();
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="dicalapi-settings-section">
                                    <h2><?php _e('Textos y Fechas', 'dicalapi-gcalendar'); ?></h2>
                                    <table class="form-table">
                                        <tbody>
                                            <tr>
                                                <th scope="row"><?php _e('Título', 'dicalapi-gcalendar'); ?></th>
                                                <td><?php dicalapi_gcalendar_title_style_render(); ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e('Descripción', 'dicalapi-gcalendar'); ?></th>
                                                <td><?php dicalapi_gcalendar_desc_style_render(); ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e('Ubicación', 'dicalapi-gcalendar'); ?></th>
                                                <td><?php dicalapi_gcalendar_location_style_render(); ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e('Fechas', 'dicalapi-gcalendar'); ?></th>
                                                <td><?php dicalapi_gcalendar_date_style_render(); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="dicalapi-settings-column">
                                <div class="dicalapi-settings-section">
                                    <h2><?php _e('Shortcode de Títulos', 'dicalapi-gcalendar'); ?></h2>
                                    <table class="form-table">
                                        <tbody>
                                            <?php 
                                            do_settings_fields('dicalapi_gcalendar', 'dicalapi_gcalendar_title_shortcode_section');
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="dicalapi-settings-section">
                                    <h2><?php _e('Configuración de Vista Previa', 'dicalapi-gcalendar'); ?></h2>
                                    <p><?php _e('Este color solo afecta a las vistas previas del panel de administración.', 'dicalapi-gcalendar'); ?></p>
                                    <table class="form-table">
                                        <tbody>
                                            <tr>
                                                <th scope="row"><?php _e('Color de fondo', 'dicalapi-gcalendar'); ?></th>
                                                <td><?php dicalapi_gcalendar_preview_bg_color_render(); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php elseif ($active_tab == 'preview'): ?>
                    <div id="tab-preview" class="tab-pane active">
                        <div class="dicalapi-settings-section">
                            <?php
                            // Mostrar vista previa del shortcode principal 
                            dicalapi_gcalendar_display_preview();
                            
                            // Mostrar vista previa del shortcode de títulos
                            dicalapi_gcalendar_display_title_shortcode_preview();
                            ?>
                        </div>
                    </div>
                    
                <?php elseif ($active_tab == 'help'): ?>
                    <div id="tab-help" class="tab-pane active">
                        <div class="dicalapi-settings-section">
                            <h2><?php _e('Uso de Shortcodes', 'dicalapi-gcalendar'); ?></h2>
                            
                            <div class="dicalapi-help-grid">
                                <div class="dicalapi-help-card">
                                    <h3><?php _e('Shortcode Principal', 'dicalapi-gcalendar'); ?></h3>
                                    <div class="shortcode-example">
                                        <code>[dicalapi_gcalendar]</code>
                                    </div>
                                    <p><?php _e('Muestra eventos con toda la información: fechas, título, descripción, ubicación y botón.', 'dicalapi-gcalendar'); ?></p>
                                    <p><strong><?php _e('Parámetros:', 'dicalapi-gcalendar'); ?></strong></p>
                                    <ul>
                                        <li><code>max_events="5"</code> - <?php _e('Número de eventos a mostrar', 'dicalapi-gcalendar'); ?></li>
                                    </ul>
                                </div>
                                
                                <div class="dicalapi-help-card">
                                    <h3><?php _e('Shortcode de Títulos', 'dicalapi-gcalendar'); ?></h3>
                                    <div class="shortcode-example">
                                        <code>[dicalapi-gcalendar-titulo]</code>
                                    </div>
                                    <p><?php _e('Muestra solo títulos y fechas con animación automática vertical.', 'dicalapi-gcalendar'); ?></p>
                                    <p><strong><?php _e('Parámetros:', 'dicalapi-gcalendar'); ?></strong></p>
                                    <ul>
                                        <li><code>max_events="3"</code> - <?php _e('Número de eventos a mostrar', 'dicalapi-gcalendar'); ?></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <h2><?php _e('URLs personalizadas para eventos', 'dicalapi-gcalendar'); ?></h2>
                            <p><?php _e('Para definir una URL de inscripción específica para un evento, añade este código en la descripción del evento en Google Calendar:', 'dicalapi-gcalendar'); ?></p>
                            <div class="shortcode-example large">
                                <code>[signup_url:https://tuformulario.com/inscripcion]</code>
                            </div>
                            
                            <div class="dicalapi-help-note">
                                <p><?php _e('Esta URL sobrescribirá la URL predeterminada configurada en este panel solo para ese evento específico.', 'dicalapi-gcalendar'); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($active_tab != 'help' && $active_tab != 'preview'): ?>
                    <?php submit_button(); ?>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php
}

/**
 * Añadir un CSS adicional para mejorar la apariencia del panel de administración
 */
function dicalapi_gcalendar_admin_head_css() {
    ?>
    <style>
        /* Estilos generales */
        .dicalapi-admin-wrap {
            max-width: 1200px;
        }
        
        .dicalapi-color-picker {
            max-width: 100px;
        }
        
        /* Tabs de navegación */
        .dicalapi-admin-wrap .nav-tab {
            display: inline-flex;
            align-items: center;
            margin-bottom: 0;
        }
        
        .dicalapi-admin-wrap .nav-tab .dashicons {
            margin-right: 5px;
        }
        
        /* Contenido de tabs */
        .tab-content {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-top: none;
            padding: 20px;
            margin-top: 0;
        }
        
        /* Secciones */
        .dicalapi-settings-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .dicalapi-settings-section:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .section-description {
            color: #666;
            font-style: italic;
            margin-top: -5px;
        }
        
        /* Layout de columnas */
        .dicalapi-settings-columns {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }
        
        .dicalapi-settings-column {
            flex: 1;
            min-width: 300px;
            padding: 0 15px;
            box-sizing: border-box;
        }
        
        /* Control de sombras */
        .dicalapi-shadow-control {
            margin-top: 10px;
        }
        
        .dicalapi-shadow-preview {
            margin-top: 15px;
        }
        
        .dicalapi-shadow-preview #dicalapi-shadow-preview-box {
            padding: 15px;
            background: #fff;
            border-radius: 4px;
            width: 300px;
            text-align: center;
            margin-top: 8px;
            border: 1px solid #ddd;
        }
        
        .dicalapi-shadow-custom {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f2f2f2;
        }
        
        /* Colores de columnas */
        .dicalapi-columns-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        
        .dicalapi-column-item {
            display: flex;
            align-items: center;
            background: #f9f9f9;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        
        .dicalapi-column-item label {
            margin-right: 8px;
            font-weight: 500;
        }
        
        /* Vistas previas */
        .dicalapi-preview-section {
            margin-top: 0;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,.1);
        }
        
        .dicalapi-preview-background {
            border: 1px solid #eee;
            margin-bottom: 20px;
        }
        
        /* Sección de ayuda */
        .dicalapi-help-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .dicalapi-help-card {
            flex: 1;
            min-width: 300px;
            background: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 20px;
        }
        
        .shortcode-example {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 3px;
            margin: 15px 0;
            border-left: 4px solid #007cba;
            font-family: monospace;
        }
        
        .shortcode-example.large {
            font-size: 16px;
        }
        
        .dicalapi-help-note {
            background: #f0f6fc;
            border-left: 4px solid #007cba;
            padding: 15px;
            margin: 20px 0;
            color: #043959;
        }
    </style>
    
    <script>
        jQuery(document).ready(function($) {
            // Inicializar los color pickers
            $('.dicalapi-color-picker').wpColorPicker();
            
            // Manejar la selección de sombra predefinida
            $('#dicalapi-shadow-preset').on('change', function() {
                var value = $(this).val();
                var customBox = $('.dicalapi-shadow-custom');
                
                if (value === 'custom') {
                    customBox.show();
                } else {
                    customBox.hide();
                    
                    // Actualizar valor y vista previa
                    $('#dicalapi-shadow-final-value').val(value);
                    $('#dicalapi-shadow-preview-box').css('box-shadow', value);
                }
            });
            
            // Actualizar vista previa en tiempo real
            $('#dicalapi-shadow-custom-input').on('input', function() {
                var customValue = $(this).val();
                
                $('#dicalapi-shadow-final-value').val(customValue);
                $('#dicalapi-shadow-preview-box').css('box-shadow', customValue);
            });
            
            // Actualizar el color de fondo de las vistas previas en tiempo real
            $('input[name="dicalapi_gcalendar_options[preview_bg_color]"]').wpColorPicker({
                change: function(event, ui) {
                    var color = ui.color.toString();
                    $('.dicalapi-preview-background').css('background-color', color);
                }
            });
        });
    </script>
    <?php
}
add_action('admin_head', 'dicalapi_gcalendar_admin_head_css');

/**
 * Sanitiza todas las opciones del plugin
 * 
 * @param array $input Las opciones enviadas por el formulario
 * @return array Las opciones sanitizadas
 */
function dicalapi_gcalendar_sanitize_options($input) {
    // Si no hay input, devolver un array vacío
    if (!is_array($input)) {
        return array();
    }
    
    // IMPORTANTE: Obtener las opciones existentes para no perder datos entre pestañas
    $existing_options = get_option('dicalapi_gcalendar_options', array());
    
    // Fusionar las opciones nuevas con las existentes
    $input = array_merge($existing_options, $input);
    
    $sanitized_input = array();
    
    // Sanitizar Calendar ID
    if (isset($input['calendar_id'])) {
        $sanitized_input['calendar_id'] = sanitize_text_field($input['calendar_id']);
    }
    
    // Sanitizar API Key
    if (isset($input['api_key'])) {
        $sanitized_input['api_key'] = sanitize_text_field($input['api_key']);
    }
    
    // Sanitizar máximo de eventos
    if (isset($input['max_events'])) {
        $sanitized_input['max_events'] = absint($input['max_events']);
    }
    
    // Sanitizar colores (utilizando sanitize_hex_color)
    $color_fields = array(
        'column1_bg', 'column2_bg', 'column3_bg',
        'title_color', 'desc_color', 'location_color', 'date_color',
        'button_bg_color', 'button_text_color', 'button_hover_bg_color',
        'title_text_color', 'title_date_color', 'preview_bg_color',
        'day_color', 'month_color'
    );
    
    foreach ($color_fields as $field) {
        if (isset($input[$field])) {
            $sanitized_input[$field] = sanitize_hex_color($input[$field]);
        } elseif (isset($existing_options[$field])) {
            // Conservar el valor existente si no se proporcionó uno nuevo
            $sanitized_input[$field] = $existing_options[$field];
        }
    }
    
    // Sanitizar tamaños de texto (con unidades CSS)
    $size_fields = array(
        'title_size', 'desc_size', 'location_size', 'date_size',
        'button_text_size', 'title_text_size', 'title_date_size',
        'day_size', 'month_size'
    );
    
    foreach ($size_fields as $field) {
        if (isset($input[$field])) {
            // Permitir solo valores numéricos seguidos de px, em, rem, etc.
            if (preg_match('/^(\d*\.?\d+)(px|em|rem|%|pt)?$/', $input[$field])) {
                $sanitized_input[$field] = $input[$field];
            } else {
                // Valor por defecto si no es válido
                $sanitized_input[$field] = '16px';
            }
        } elseif (isset($existing_options[$field])) {
            // Conservar el valor existente si no se proporcionó uno nuevo
            $sanitized_input[$field] = $existing_options[$field];
        }
    }
    
    // Sanitizar row shadow
    if (isset($input['row_shadow'])) {
        $sanitized_input['row_shadow'] = sanitize_text_field($input['row_shadow']);
    } elseif (isset($existing_options['row_shadow'])) {
        $sanitized_input['row_shadow'] = $existing_options['row_shadow'];
    }
    
    // Sanitizar URL de inscripción
    if (isset($input['signup_url'])) {
        $sanitized_input['signup_url'] = esc_url_raw($input['signup_url']);
    } elseif (isset($existing_options['signup_url'])) {
        $sanitized_input['signup_url'] = $existing_options['signup_url'];
    }
    
    // Sanitizar texto del botón
    if (isset($input['signup_button_text'])) {
        $sanitized_input['signup_button_text'] = sanitize_text_field($input['signup_button_text']);
    } elseif (isset($existing_options['signup_button_text'])) {
        $sanitized_input['signup_button_text'] = $existing_options['signup_button_text'];
    }
    
    // Sanitizar intervalo de rotación
    if (isset($input['title_scroll_interval'])) {
        $sanitized_input['title_scroll_interval'] = absint($input['title_scroll_interval']);
        // Asegurar que el valor esté entre 2 y 30 segundos
        if ($sanitized_input['title_scroll_interval'] < 2) {
            $sanitized_input['title_scroll_interval'] = 2;
        }
        if ($sanitized_input['title_scroll_interval'] > 30) {
            $sanitized_input['title_scroll_interval'] = 30;
        }
    } elseif (isset($existing_options['title_scroll_interval'])) {
        $sanitized_input['title_scroll_interval'] = $existing_options['title_scroll_interval'];
    }
    
    // Sanitizar fuentes (solo nombres válidos de Google Fonts)
    $font_fields = array(
        'title_font', 'desc_font', 'location_font', 'day_font', 'month_font'
    );
    
    $allowed_fonts = array(
        '', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins', 
        'Raleway', 'Source Sans Pro', 'Ubuntu', 'Playfair Display', 'Merriweather'
    );
    
    foreach ($font_fields as $field) {
        if (isset($input[$field])) {
            if (in_array($input[$field], $allowed_fonts)) {
                $sanitized_input[$field] = $input[$field];
            } else {
                $sanitized_input[$field] = ''; // Valor por defecto
            }
        } elseif (isset($existing_options[$field])) {
            $sanitized_input[$field] = $existing_options[$field];
        }
    }
    
    // Sanitizar opciones de estilo de texto (checkboxes)
    $checkbox_fields = array(
        'title_bold', 'title_italic', 'title_underline',
        'desc_bold', 'desc_italic', 'desc_underline',
        'location_bold', 'location_italic', 'location_underline',
        'day_bold', 'day_italic', 'day_underline',
        'month_bold', 'month_italic', 'month_underline'
    );
    
    foreach ($checkbox_fields as $field) {
        if (isset($input[$field])) {
            $sanitized_input[$field] = (bool) $input[$field];
        } elseif (isset($existing_options[$field])) {
            $sanitized_input[$field] = $existing_options[$field];
        }
    }
    
    // Sanitizar opciones de alineación
    $align_fields = array('title_align', 'desc_align', 'location_align');
    $allowed_alignments = array('left', 'center', 'right');
    
    foreach ($align_fields as $field) {
        if (isset($input[$field])) {
            if (in_array($input[$field], $allowed_alignments)) {
                $sanitized_input[$field] = $input[$field];
            } else {
                $sanitized_input[$field] = 'center'; // Valor por defecto
            }
        } elseif (isset($existing_options[$field])) {
            $sanitized_input[$field] = $existing_options[$field];
        }
    }
    
    // Asegurar que todos los demás valores se conserven
    foreach ($existing_options as $key => $value) {
        if (!isset($sanitized_input[$key])) {
            $sanitized_input[$key] = $value;
        }
    }
    
    return $sanitized_input;
}

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

function dicalapi_gcalendar_preview_section_callback() {
    echo '<p>' . __('Configura el aspecto de las vistas previas solo en el panel de administración. Estos ajustes no afectan el frontend.', 'dicalapi-gcalendar') . '</p>';
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

function dicalapi_gcalendar_column_bg_colors_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <div class="dicalapi-columns-grid">
        <div class="dicalapi-column-item">
            <label><?php _e('Columna fechas:', 'dicalapi-gcalendar'); ?></label>
            <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[column1_bg]' value='<?php echo esc_attr($options['column1_bg'] ?? '#f8f9fa'); ?>'>
        </div>
        
        <div class="dicalapi-column-item">
            <label><?php _e('Columna contenido:', 'dicalapi-gcalendar'); ?></label>
            <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[column2_bg]' value='<?php echo esc_attr($options['column2_bg'] ?? '#ffffff'); ?>'>
        </div>
        
        <div class="dicalapi-column-item">
            <label><?php _e('Columna inscripción:', 'dicalapi-gcalendar'); ?></label>
            <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[column3_bg]' value='<?php echo esc_attr($options['column3_bg'] ?? '#f0f0f0'); ?>'>
        </div>
    </div>
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
        // Sombras para fondos oscuros
        '0px 3px 6px rgba(255,255,255,0.1)' => __('Sutil (modo oscuro)', 'dicalapi-gcalendar'),
        '0px 5px 10px rgba(255,255,255,0.15)' => __('Media (modo oscuro)', 'dicalapi-gcalendar'),
        '0px 8px 15px rgba(255,255,255,0.2)' => __('Pronunciada (modo oscuro)', 'dicalapi-gcalendar'),
        '0px 4px 8px rgba(255,255,255,0.1), 0px 8px 16px rgba(255,255,255,0.15)' => __('Doble (modo oscuro)', 'dicalapi-gcalendar'),
        // Sombras con colores
        '0px 4px 8px rgba(0,70,150,0.2)' => __('Azul', 'dicalapi-gcalendar'),
        '0px 4px 8px rgba(120,0,0,0.2)' => __('Rojo', 'dicalapi-gcalendar'),
        '0px 4px 8px rgba(0,120,0,0.2)' => __('Verde', 'dicalapi-gcalendar'),
        '0px 4px 8px rgba(120,0,120,0.2)' => __('Púrpura', 'dicalapi-gcalendar'),
        '0px 4px 8px rgba(255,165,0,0.25)' => __('Naranja', 'dicalapi-gcalendar'),
        // Efectos especiales
        '0px 1px 3px rgba(0,0,0,0.12), 0px 1px 2px rgba(0,0,0,0.24)' => __('Material Design', 'dicalapi-gcalendar'),
        '0px 3px 5px -1px rgba(0,0,0,0.2), 0px 6px 10px 0px rgba(0,0,0,0.14), 0px 1px 18px 0px rgba(0,0,0,0.12)' => __('Elevación', 'dicalapi-gcalendar'),
        '0px 0px 15px rgba(0,0,0,0.1)' => __('Neón suave', 'dicalapi-gcalendar'),
        '0px 0px 10px 5px rgba(66,133,244,0.3)' => __('Neón azul', 'dicalapi-gcalendar'),
        '0px 0px 10px 5px rgba(219,68,55,0.3)' => __('Neón rojo', 'dicalapi-gcalendar'),
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
            <div class="dicalapi-preview-background-selector">
                <label><?php _e('Fondo de previsualización:', 'dicalapi-gcalendar'); ?></label>
                <select id="dicalapi-shadow-preview-background">
                    <option value="#ffffff"><?php _e('Fondo claro', 'dicalapi-gcalendar'); ?></option>
                    <option value="#333333"><?php _e('Fondo oscuro', 'dicalapi-gcalendar'); ?></option>
                    <option value="#f5f5f5"><?php _e('Gris claro', 'dicalapi-gcalendar'); ?></option>
                    <option value="#1a1a1a"><?php _e('Gris oscuro', 'dicalapi-gcalendar'); ?></option>
                    <option value="#e6f7ff"><?php _e('Azul claro', 'dicalapi-gcalendar'); ?></option>
                    <option value="#f0fff0"><?php _e('Verde claro', 'dicalapi-gcalendar'); ?></option>
                </select>
            </div>
            <div id="dicalapi-shadow-preview-container">
                <div id="dicalapi-shadow-preview-box" style="box-shadow: <?php echo esc_attr($current_shadow); ?>">
                    <?php _e('Ejemplo de sombra', 'dicalapi-gcalendar'); ?>
                </div>
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
    <div class="dicalapi-style-grid">
        <div class="dicalapi-style-row">
            <label><?php _e('Fuente:', 'dicalapi-gcalendar'); ?></label>
            <select name="dicalapi_gcalendar_options[title_font]" class="dicalapi-font-select">
                <option value=""><?php _e('Predeterminado', 'dicalapi-gcalendar'); ?></option>
                <option value="Roboto" <?php selected($options['title_font'] ?? '', 'Roboto'); ?>>Roboto</option>
                <option value="Open Sans" <?php selected($options['title_font'] ?? '', 'Open Sans'); ?>>Open Sans</option>
                <option value="Lato" <?php selected($options['title_font'] ?? '', 'Lato'); ?>>Lato</option>
                <option value="Montserrat" <?php selected($options['title_font'] ?? '', 'Montserrat'); ?>>Montserrat</option>
                <option value="Poppins" <?php selected($options['title_font'] ?? '', 'Poppins'); ?>>Poppins</option>
                <option value="Raleway" <?php selected($options['title_font'] ?? '', 'Raleway'); ?>>Raleway</option>
                <option value="Source Sans Pro" <?php selected($options['title_font'] ?? '', 'Source Sans Pro'); ?>>Source Sans Pro</option>
                <option value="Ubuntu" <?php selected($options['title_font'] ?? '', 'Ubuntu'); ?>>Ubuntu</option>
                <option value="Playfair Display" <?php selected($options['title_font'] ?? '', 'Playfair Display'); ?>>Playfair Display</option>
                <option value="Merriweather" <?php selected($options['title_font'] ?? '', 'Merriweather'); ?>>Merriweather</option>
            </select>
        </div>
        
        <div class="dicalapi-style-row">
            <label><?php _e('Color:', 'dicalapi-gcalendar'); ?></label>
            <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[title_color]' value='<?php echo esc_attr($options['title_color'] ?? '#333333'); ?>'>
        </div>
        
        <div class="dicalapi-style-row">
            <label><?php _e('Tamaño:', 'dicalapi-gcalendar'); ?></label>
            <input type='text' class="dicalapi-size-input" name='dicalapi_gcalendar_options[title_size]' value='<?php echo esc_attr($options['title_size'] ?? '18px'); ?>'>
        </div>
        
        <div class="dicalapi-style-row">
            <label><?php _e('Estilo:', 'dicalapi-gcalendar'); ?></label>
            <div class="dicalapi-text-style-options">
                <input type="checkbox" id="title_bold" name="dicalapi_gcalendar_options[title_bold]" value="1" <?php checked($options['title_bold'] ?? false, 1); ?>>
                <label for="title_bold" title="<?php _e('Negrita', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-bold"></label>
                
                <input type="checkbox" id="title_italic" name="dicalapi_gcalendar_options[title_italic]" value="1" <?php checked($options['title_italic'] ?? false, 1); ?>>
                <label for="title_italic" title="<?php _e('Cursiva', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-italic"></label>
                
                <input type="checkbox" id="title_underline" name="dicalapi_gcalendar_options[title_underline]" value="1" <?php checked($options['title_underline'] ?? false, 1); ?>>
                <label for="title_underline" title="<?php _e('Subrayado', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-underline"></label>
            </div>
        </div>
        
        <div class="dicalapi-style-row">
            <label><?php _e('Alineación:', 'dicalapi-gcalendar'); ?></label>
            <div class="dicalapi-text-align-options">
                <input type="radio" id="title_align_left" name="dicalapi_gcalendar_options[title_align]" value="left" <?php checked($options['title_align'] ?? 'center', 'left'); ?>>
                <label for="title_align_left" title="<?php _e('Alinear a la izquierda', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-alignleft"></label>
                
                <input type="radio" id="title_align_center" name="dicalapi_gcalendar_options[title_align]" value="center" <?php checked($options['title_align'] ?? 'center', 'center'); ?>>
                <label for="title_align_center" title="<?php _e('Centrar', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-aligncenter"></label>
                
                <input type="radio" id="title_align_right" name="dicalapi_gcalendar_options[title_align]" value="right" <?php checked($options['title_align'] ?? 'center', 'right'); ?>>
                <label for="title_align_right" title="<?php _e('Alinear a la derecha', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-alignright"></label>
            </div>
        </div>
        
        <div class="dicalapi-style-preview">
            <div class="dicalapi-preview-title" id="title_preview">Vista previa del título</div>
        </div>
    </div>
    <?php
}

function dicalapi_gcalendar_desc_style_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <div class="dicalapi-style-grid">
        <div class="dicalapi-style-row">
            <label><?php _e('Fuente:', 'dicalapi-gcalendar'); ?></label>
            <select name="dicalapi_gcalendar_options[desc_font]" class="dicalapi-font-select">
                <option value=""><?php _e('Predeterminado', 'dicalapi-gcalendar'); ?></option>
                <option value="Roboto" <?php selected($options['desc_font'] ?? '', 'Roboto'); ?>>Roboto</option>
                <option value="Open Sans" <?php selected($options['desc_font'] ?? '', 'Open Sans'); ?>>Open Sans</option>
                <option value="Lato" <?php selected($options['desc_font'] ?? '', 'Lato'); ?>>Lato</option>
                <option value="Montserrat" <?php selected($options['desc_font'] ?? '', 'Montserrat'); ?>>Montserrat</option>
                <option value="Poppins" <?php selected($options['desc_font'] ?? '', 'Poppins'); ?>>Poppins</option>
                <option value="Raleway" <?php selected($options['desc_font'] ?? '', 'Raleway'); ?>>Raleway</option>
                <option value="Source Sans Pro" <?php selected($options['desc_font'] ?? '', 'Source Sans Pro'); ?>>Source Sans Pro</option>
                <option value="Ubuntu" <?php selected($options['desc_font'] ?? '', 'Ubuntu'); ?>>Ubuntu</option>
                <option value="Playfair Display" <?php selected($options['desc_font'] ?? '', 'Playfair Display'); ?>>Playfair Display</option>
                <option value="Merriweather" <?php selected($options['desc_font'] ?? '', 'Merriweather'); ?>>Merriweather</option>
            </select>
        </div>
        
        <div class="dicalapi-style-row">
            <label><?php _e('Color:', 'dicalapi-gcalendar'); ?></label>
            <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[desc_color]' value='<?php echo esc_attr($options['desc_color'] ?? '#666666'); ?>'>
        </div>
        
        <div class="dicalapi-style-row">
            <label><?php _e('Tamaño:', 'dicalapi-gcalendar'); ?></label>
            <input type='text' class="dicalapi-size-input" name='dicalapi_gcalendar_options[desc_size]' value='<?php echo esc_attr($options['desc_size'] ?? '14px'); ?>'>
        </div>
        
        <div class="dicalapi-style-row">
            <label><?php _e('Estilo:', 'dicalapi-gcalendar'); ?></label>
            <div class="dicalapi-text-style-options">
                <input type="checkbox" id="desc_bold" name="dicalapi_gcalendar_options[desc_bold]" value="1" <?php checked($options['desc_bold'] ?? false, 1); ?>>
                <label for="desc_bold" title="<?php _e('Negrita', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-bold"></label>
                
                <input type="checkbox" id="desc_italic" name="dicalapi_gcalendar_options[desc_italic]" value="1" <?php checked($options['desc_italic'] ?? false, 1); ?>>
                <label for="desc_italic" title="<?php _e('Cursiva', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-italic"></label>
                
                <input type="checkbox" id="desc_underline" name="dicalapi_gcalendar_options[desc_underline]" value="1" <?php checked($options['desc_underline'] ?? false, 1); ?>>
                <label for="desc_underline" title="<?php _e('Subrayado', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-underline"></label>
            </div>
        </div>
        
        <div class="dicalapi-style-row">
            <label><?php _e('Alineación:', 'dicalapi-gcalendar'); ?></label>
            <div class="dicalapi-text-align-options">
                <input type="radio" id="desc_align_left" name="dicalapi_gcalendar_options[desc_align]" value="left" <?php checked($options['desc_align'] ?? 'center', 'left'); ?>>
                <label for="desc_align_left" title="<?php _e('Alinear a la izquierda', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-alignleft"></label>
                
                <input type="radio" id="desc_align_center" name="dicalapi_gcalendar_options[desc_align]" value="center" <?php checked($options['desc_align'] ?? 'center', 'center'); ?>>
                <label for="desc_align_center" title="<?php _e('Centrar', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-aligncenter"></label>
                
                <input type="radio" id="desc_align_right" name="dicalapi_gcalendar_options[desc_align]" value="right" <?php checked($options['desc_align'] ?? 'center', 'right'); ?>>
                <label for="desc_align_right" title="<?php _e('Alinear a la derecha', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-alignright"></label>
            </div>
        </div>
        
        <div class="dicalapi-style-preview">
            <div class="dicalapi-preview-description" id="desc_preview">Vista previa de la descripción</div>
        </div>
    </div>
    <?php
}

function dicalapi_gcalendar_location_style_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <div class="dicalapi-style-grid">
        <div class="dicalapi-style-row">
            <label><?php _e('Fuente:', 'dicalapi-gcalendar'); ?></label>
            <select name="dicalapi_gcalendar_options[location_font]" class="dicalapi-font-select">
                <option value=""><?php _e('Predeterminado', 'dicalapi-gcalendar'); ?></option>
                <option value="Roboto" <?php selected($options['location_font'] ?? '', 'Roboto'); ?>>Roboto</option>
                <option value="Open Sans" <?php selected($options['location_font'] ?? '', 'Open Sans'); ?>>Open Sans</option>
                <option value="Lato" <?php selected($options['location_font'] ?? '', 'Lato'); ?>>Lato</option>
                <option value="Montserrat" <?php selected($options['location_font'] ?? '', 'Montserrat'); ?>>Montserrat</option>
                <option value="Poppins" <?php selected($options['location_font'] ?? '', 'Poppins'); ?>>Poppins</option>
                <option value="Raleway" <?php selected($options['location_font'] ?? '', 'Raleway'); ?>>Raleway</option>
                <option value="Source Sans Pro" <?php selected($options['location_font'] ?? '', 'Source Sans Pro'); ?>>Source Sans Pro</option>
                <option value="Ubuntu" <?php selected($options['location_font'] ?? '', 'Ubuntu'); ?>>Ubuntu</option>
                <option value="Playfair Display" <?php selected($options['location_font'] ?? '', 'Playfair Display'); ?>>Playfair Display</option>
                <option value="Merriweather" <?php selected($options['location_font'] ?? '', 'Merriweather'); ?>>Merriweather</option>
            </select>
        </div>
        
        <div class="dicalapi-style-row">
            <label><?php _e('Color:', 'dicalapi-gcalendar'); ?></label>
            <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[location_color]' value='<?php echo esc_attr($options['location_color'] ?? '#888888'); ?>'>
        </div>
        
        <div class="dicalapi-style-row">
            <label><?php _e('Tamaño:', 'dicalapi-gcalendar'); ?></label>
            <input type='text' class="dicalapi-size-input" name='dicalapi_gcalendar_options[location_size]' value='<?php echo esc_attr($options['location_size'] ?? '14px'); ?>'>
        </div>
        
        <div class="dicalapi-style-row">
            <label><?php _e('Estilo:', 'dicalapi-gcalendar'); ?></label>
            <div class="dicalapi-text-style-options">
                <input type="checkbox" id="location_bold" name="dicalapi_gcalendar_options[location_bold]" value="1" <?php checked($options['location_bold'] ?? false, 1); ?>>
                <label for="location_bold" title="<?php _e('Negrita', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-bold"></label>
                
                <input type="checkbox" id="location_italic" name="dicalapi_gcalendar_options[location_italic]" value="1" <?php checked($options['location_italic'] ?? false, 1); ?>>
                <label for="location_italic" title="<?php _e('Cursiva', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-italic"></label>
                
                <input type="checkbox" id="location_underline" name="dicalapi_gcalendar_options[location_underline]" value="1" <?php checked($options['location_underline'] ?? false, 1); ?>>
                <label for="location_underline" title="<?php _e('Subrayado', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-underline"></label>
            </div>
        </div>
        
        <div class="dicalapi-style-row">
            <label><?php _e('Alineación:', 'dicalapi-gcalendar'); ?></label>
            <div class="dicalapi-text-align-options">
                <input type="radio" id="location_align_left" name="dicalapi_gcalendar_options[location_align]" value="left" <?php checked($options['location_align'] ?? 'center', 'left'); ?>>
                <label for="location_align_left" title="<?php _e('Alinear a la izquierda', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-alignleft"></label>
                
                <input type="radio" id="location_align_center" name="dicalapi_gcalendar_options[location_align]" value="center" <?php checked($options['location_align'] ?? 'center', 'center'); ?>>
                <label for="location_align_center" title="<?php _e('Centrar', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-aligncenter"></label>
                
                <input type="radio" id="location_align_right" name="dicalapi_gcalendar_options[location_align]" value="right" <?php checked($options['location_align'] ?? 'center', 'right'); ?>>
                <label for="location_align_right" title="<?php _e('Alinear a la derecha', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-alignright"></label>
            </div>
        </div>
        
        <div class="dicalapi-style-preview">
            <div class="dicalapi-preview-location" id="location_preview"><span class="dashicons dashicons-location"></span> Vista previa de la ubicación</div>
        </div>
    </div>
    <?php
}

function dicalapi_gcalendar_date_style_render() {
    $options = get_option('dicalapi_gcalendar_options');
    ?>
    <div class="dicalapi-date-options">
        <h4><?php _e('Opciones para el día', 'dicalapi-gcalendar'); ?></h4>
        <div class="dicalapi-style-grid">
            <div class="dicalapi-style-row">
                <label><?php _e('Fuente:', 'dicalapi-gcalendar'); ?></label>
                <select name="dicalapi_gcalendar_options[day_font]" class="dicalapi-font-select">
                    <option value=""><?php _e('Predeterminado', 'dicalapi-gcalendar'); ?></option>
                    <option value="Roboto" <?php selected($options['day_font'] ?? '', 'Roboto'); ?>>Roboto</option>
                    <option value="Open Sans" <?php selected($options['day_font'] ?? '', 'Open Sans'); ?>>Open Sans</option>
                    <option value="Lato" <?php selected($options['day_font'] ?? '', 'Lato'); ?>>Lato</option>
                    <option value="Montserrat" <?php selected($options['day_font'] ?? '', 'Montserrat'); ?>>Montserrat</option>
                    <option value="Poppins" <?php selected($options['day_font'] ?? '', 'Poppins'); ?>>Poppins</option>
                    <option value="Raleway" <?php selected($options['day_font'] ?? '', 'Raleway'); ?>>Raleway</option>
                    <option value="Source Sans Pro" <?php selected($options['day_font'] ?? '', 'Source Sans Pro'); ?>>Source Sans Pro</option>
                    <option value="Ubuntu" <?php selected($options['day_font'] ?? '', 'Ubuntu'); ?>>Ubuntu</option>
                    <option value="Playfair Display" <?php selected($options['day_font'] ?? '', 'Playfair Display'); ?>>Playfair Display</option>
                    <option value="Merriweather" <?php selected($options['day_font'] ?? '', 'Merriweather'); ?>>Merriweather</option>
                </select>
            </div>
            
            <div class="dicalapi-style-row">
                <label><?php _e('Color:', 'dicalapi-gcalendar'); ?></label>
                <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[day_color]' value='<?php echo esc_attr($options['day_color'] ?? ($options['date_color'] ?? '#007bff')); ?>'>
            </div>
            
            <div class="dicalapi-style-row">
                <label><?php _e('Tamaño:', 'dicalapi-gcalendar'); ?></label>
                <input type='text' class="dicalapi-size-input" name='dicalapi_gcalendar_options[day_size]' value='<?php echo esc_attr($options['day_size'] ?? ($options['date_size'] ?? '18px')); ?>'>
            </div>
            
            <div class="dicalapi-style-row">
                <label><?php _e('Estilo:', 'dicalapi-gcalendar'); ?></label>
                <div class="dicalapi-text-style-options">
                    <input type="checkbox" id="day_bold" name="dicalapi_gcalendar_options[day_bold]" value="1" <?php checked($options['day_bold'] ?? true, 1); ?>>
                    <label for="day_bold" title="<?php _e('Negrita', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-bold"></label>
                    
                    <input type="checkbox" id="day_italic" name="dicalapi_gcalendar_options[day_italic]" value="1" <?php checked($options['day_italic'] ?? false, 1); ?>>
                    <label for="day_italic" title="<?php _e('Cursiva', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-italic"></label>
                    
                    <input type="checkbox" id="day_underline" name="dicalapi_gcalendar_options[day_underline]" value="1" <?php checked($options['day_underline'] ?? false, 1); ?>>
                    <label for="day_underline" title="<?php _e('Subrayado', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-underline"></label>
                </div>
            </div>
        </div>
        
        <h4><?php _e('Opciones para el mes', 'dicalapi-gcalendar'); ?></h4>
        <div class="dicalapi-style-grid">
            <div class="dicalapi-style-row">
                <label><?php _e('Fuente:', 'dicalapi-gcalendar'); ?></label>
                <select name="dicalapi_gcalendar_options[month_font]" class="dicalapi-font-select">
                    <option value=""><?php _e('Predeterminado', 'dicalapi-gcalendar'); ?></option>
                    <option value="Roboto" <?php selected($options['month_font'] ?? '', 'Roboto'); ?>>Roboto</option>
                    <option value="Open Sans" <?php selected($options['month_font'] ?? '', 'Open Sans'); ?>>Open Sans</option>
                    <option value="Lato" <?php selected($options['month_font'] ?? '', 'Lato'); ?>>Lato</option>
                    <option value="Montserrat" <?php selected($options['month_font'] ?? '', 'Montserrat'); ?>>Montserrat</option>
                    <option value="Poppins" <?php selected($options['month_font'] ?? '', 'Poppins'); ?>>Poppins</option>
                    <option value="Raleway" <?php selected($options['month_font'] ?? '', 'Raleway'); ?>>Raleway</option>
                    <option value="Source Sans Pro" <?php selected($options['month_font'] ?? '', 'Source Sans Pro'); ?>>Source Sans Pro</option>
                    <option value="Ubuntu" <?php selected($options['month_font'] ?? '', 'Ubuntu'); ?>>Ubuntu</option>
                    <option value="Playfair Display" <?php selected($options['month_font'] ?? '', 'Playfair Display'); ?>>Playfair Display</option>
                    <option value="Merriweather" <?php selected($options['month_font'] ?? '', 'Merriweather'); ?>>Merriweather</option>
                </select>
            </div>
            
            <div class="dicalapi-style-row">
                <label><?php _e('Color:', 'dicalapi-gcalendar'); ?></label>
                <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[month_color]' value='<?php echo esc_attr($options['month_color'] ?? ($options['date_color'] ?? '#007bff')); ?>'>
            </div>
            
            <div class="dicalapi-style-row">
                <label><?php _e('Tamaño:', 'dicalapi-gcalendar'); ?></label>
                <input type='text' class="dicalapi-size-input" name='dicalapi_gcalendar_options[month_size]' value='<?php echo esc_attr($options['month_size'] ?? ($options['date_size'] ?? '14px')); ?>'>
            </div>
            
            <div class="dicalapi-style-row">
                <label><?php _e('Estilo:', 'dicalapi-gcalendar'); ?></label>
                <div class="dicalapi-text-style-options">
                    <input type="checkbox" id="month_bold" name="dicalapi_gcalendar_options[month_bold]" value="1" <?php checked($options['month_bold'] ?? false, 1); ?>>
                    <label for="month_bold" title="<?php _e('Negrita', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-bold"></label>
                    
                    <input type="checkbox" id="month_italic" name="dicalapi_gcalendar_options[month_italic]" value="1" <?php checked($options['month_italic'] ?? false, 1); ?>>
                    <label for="month_italic" title="<?php _e('Cursiva', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-italic"></label>
                    
                    <input type="checkbox" id="month_underline" name="dicalapi_gcalendar_options[month_underline]" value="1" <?php checked($options['month_underline'] ?? false, 1); ?>>
                    <label for="month_underline" title="<?php _e('Subrayado', 'dicalapi-gcalendar'); ?>" class="dashicons dashicons-editor-underline"></label>
                </div>
            </div>
        </div>
        
        <div class="dicalapi-style-preview date-preview">
            <div class="dicalapi-preview-date" id="date_preview">
                <div class="dicalapi-preview-day" id="day_preview">15</div>
                <div class="dicalapi-preview-month" id="month_preview">May</div>
            </div>
        </div>
        
        <!-- Mantener el campo antiguo para compatibilidad -->
        <input type='hidden' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[date_color]' value='<?php echo esc_attr($options['date_color'] ?? '#007bff'); ?>'>
        <input type='hidden' name='dicalapi_gcalendar_options[date_size]' value='<?php echo esc_attr($options['date_size'] ?? '16px'); ?>'>
    </div>
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

function dicalapi_gcalendar_preview_bg_color_render() {
    $options = get_option('dicalapi_gcalendar_options');
    $preview_bg_color = isset($options['preview_bg_color']) ? $options['preview_bg_color'] : '#ffffff';
    ?>
    <input type='text' class="dicalapi-color-picker" name='dicalapi_gcalendar_options[preview_bg_color]' value='<?php echo esc_attr($preview_bg_color); ?>'>
    <p class="description"><?php _e('Este color solo afecta a las vistas previas en esta página de administración. Te ayuda a visualizar cómo se verán los shortcodes en el fondo de tu sitio web.', 'dicalapi-gcalendar'); ?></p>
    <?php
}

// Agregar un ejemplo visual de cómo se verán los eventos
function dicalapi_gcalendar_display_preview() {
    $options = get_option('dicalapi_gcalendar_options');
    $preview_bg_color = isset($options['preview_bg_color']) ? $options['preview_bg_color'] : '#ffffff';
    
    // Obtener los valores de configuración con valores por defecto
    $column1_bg = isset($options['column1_bg']) ? $options['column1_bg'] : '#f8f9fa';
    $column2_bg = isset($options['column2_bg']) ? $options['column2_bg'] : '#ffffff';
    $column3_bg = isset($options['column3_bg']) ? $options['column3_bg'] : '#f0f0f0';
    
    $title_color = isset($options['title_color']) ? $options['title_color'] : '#333333';
    $title_size = isset($options['title_size']) ? $options['title_size'] : '18px';
    
    $desc_color = isset($options['desc_color']) ? $options['desc_color'] : '#666666';
    $desc_size = isset($options['desc_size']) ? $options['desc_size'] : '14px';
    
    $location_color = isset($options['location_color']) ? $options['location_color'] : '#888888';
    $location_size = isset($options['location_size']) ? $options['location_size'] : '14px';
    
    $date_color = isset($options['date_color']) ? $options['date_color'] : '#007bff';
    $date_size = isset($options['date_size']) ? $options['date_size'] : '16px';
    
    $button_bg_color = isset($options['button_bg_color']) ? $options['button_bg_color'] : '#007bff';
    $button_hover_bg_color = isset($options['button_hover_bg_color']) ? $options['button_hover_bg_color'] : '#0056b3';
    $button_text_color = isset($options['button_text_color']) ? $options['button_text_color'] : '#ffffff';
    $button_text_size = isset($options['button_text_size']) ? $options['button_text_size'] : '14px';
    
    $row_shadow = isset($options['row_shadow']) ? $options['row_shadow'] : '0px 2px 5px rgba(0,0,0,0.1)';
    
    echo '<div class="dicalapi-preview-section">';
    echo '<h2>' . __('Vista previa', 'dicalapi-gcalendar') . '</h2>';
    echo '<p>' . __('Así es como se verán tus eventos con la configuración actual:', 'dicalapi-gcalendar') . '</p>';
    
    // Contenedor con color de fondo configurable
    echo '<div class="dicalapi-preview-background" style="background-color:' . esc_attr($preview_bg_color) . '; padding: 20px; border-radius: 4px;">';
    
    echo '<div class="dicalapi-gcalendar-container" id="dicalapi-preview-container">';
    echo '<div class="dicalapi-gcalendar-event-wrapper">';
    
    // Aplicar la sombra configurada directamente al elemento del evento
    echo '<div class="dicalapi-gcalendar-event" id="dicalapi-preview-event" style="display: flex; border-radius: 4px; overflow: hidden; margin-bottom: 15px; box-shadow: ' . esc_attr($row_shadow) . ';">';
    
    // Columna de fechas con estilo en línea
    echo '<div class="dicalapi-gcalendar-date-column" style="background-color: ' . esc_attr($column1_bg) . '; padding: 15px; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center; width: 80px;">';
    echo '<div class="dicalapi-gcalendar-day" style="font-size: ' . esc_attr($date_size) . '; color: ' . esc_attr($date_color) . '; font-weight: bold;">15</div>';
    echo '<div class="dicalapi-gcalendar-month" style="font-size: calc(' . esc_attr($date_size) . ' * 0.8); color: ' . esc_attr($date_color) . ';">Oct</div>';
    echo '</div>';
    
    // Columna de contenido con estilo en línea
    echo '<div class="dicalapi-gcalendar-content-column" style="background-color: ' . esc_attr($column2_bg) . '; padding: 15px; flex-grow: 1;">';
    echo '<h3 class="dicalapi-gcalendar-title" style="color: ' . esc_attr($title_color) . '; font-size: ' . esc_attr($title_size) . '; margin-top: 0; margin-bottom: 10px;">Evento de ejemplo</h3>';
    echo '<div class="dicalapi-gcalendar-description" style="color: ' . esc_attr($desc_color) . '; font-size: ' . esc_attr($desc_size) . '; margin-bottom: 10px;">Esta es una descripción de ejemplo para mostrar cómo se verán tus eventos.</div>';
    echo '<div class="dicalapi-gcalendar-location" style="color: ' . esc_attr($location_color) . '; font-size: ' . esc_attr($location_size) . ';"><span class="dashicons dashicons-location" style="vertical-align: text-bottom;"></span> Lugar de ejemplo</div>';
    echo '</div>';
    
    // Columna de inscripción con estilo en línea
    echo '<div class="dicalapi-gcalendar-signup-column" style="background-color: ' . esc_attr($column3_bg) . '; padding: 15px; display: flex; align-items: center; justify-content: center; width: 120px;">';
    $button_text = !empty($options['signup_button_text']) ? $options['signup_button_text'] : __('Inscribirse', 'dicalapi-gcalendar');
    echo '<a href="#" class="dicalapi-gcalendar-signup-button" style="display: inline-block; background-color: ' . esc_attr($button_bg_color) . '; color: ' . esc_attr($button_text_color) . '; text-decoration: none; padding: 8px 15px; border-radius: 4px; text-align: center; font-size: ' . esc_attr($button_text_size) . '; transition: background-color 0.3s;" data-hover-color="' . esc_attr($button_hover_bg_color) . '">' . esc_html($button_text) . '</a>';
    echo '</div>';
    
    echo '</div>'; // Fin evento
    echo '</div>'; // Fin wrapper
    echo '</div>'; // Fin contenedor
    
    echo '</div>'; // Fin del contenedor con fondo personalizado
    
    // Agregar script para simular el efecto hover del botón
    echo '<script>
        jQuery(document).ready(function($) {
            // Efecto hover del botón
            $(".dicalapi-gcalendar-signup-button").hover(
                function() {
                    var hoverColor = $(this).data("hover-color");
                    $(this).css("background-color", hoverColor);
                },
                function() {
                    $(this).css("background-color", "' . esc_js($button_bg_color) . '");
                }
            );
            
            // Actualizar la vista previa en tiempo real cuando cambien los colores
            $("input.dicalapi-color-picker").wpColorPicker({
                change: function(event, ui) {
                    updatePreview();
                }
            });
            
            // Actualizar la vista previa cuando cambien otros campos de entrada
            $("input[name^=\'dicalapi_gcalendar_options\']").on("input change", function() {
                updatePreview();
            });
            
            // Función para actualizar la vista previa
            function updatePreview() {
                var column1_bg = $("input[name=\'dicalapi_gcalendar_options[column1_bg]\']").val() || "#f8f9fa";
                var column2_bg = $("input[name=\'dicalapi_gcalendar_options[column2_bg]\']").val() || "#ffffff";
                var column3_bg = $("input[name=\'dicalapi_gcalendar_options[column3_bg]\']").val() || "#f0f0f0";
                
                var title_color = $("input[name=\'dicalapi_gcalendar_options[title_color]\']").val() || "#333333";
                var title_size = $("input[name=\'dicalapi_gcalendar_options[title_size]\']").val() || "18px";
                
                var desc_color = $("input[name=\'dicalapi_gcalendar_options[desc_color]\']").val() || "#666666";
                var desc_size = $("input[name=\'dicalapi_gcalendar_options[desc_size]\']").val() || "14px";
                
                var location_color = $("input[name=\'dicalapi_gcalendar_options[location_color]\']").val() || "#888888";
                var location_size = $("input[name=\'dicalapi_gcalendar_options[location_size]\']").val() || "14px";
                
                var date_color = $("input[name=\'dicalapi_gcalendar_options[date_color]\']").val() || "#007bff";
                var date_size = $("input[name=\'dicalapi_gcalendar_options[date_size]\']").val() || "16px";
                
                var button_bg = $("input[name=\'dicalapi_gcalendar_options[button_bg_color]\']").val() || "#007bff";
                var button_hover_bg = $("input[name=\'dicalapi_gcalendar_options[button_hover_bg_color]\']").val() || "#0056b3";
                var button_text_color = $("input[name=\'dicalapi_gcalendar_options[button_text_color]\']").val() || "#ffffff";
                var button_text_size = $("input[name=\'dicalapi_gcalendar_options[button_text_size]\']").val() || "14px";
                
                var shadow = $("#dicalapi-shadow-final-value").val() || "0px 2px 5px rgba(0,0,0,0.1)";
                
                // Actualizar los elementos en la vista previa
                $(".dicalapi-gcalendar-date-column").css("background-color", column1_bg);
                $(".dicalapi-gcalendar-content-column").css("background-color", column2_bg);
                $(".dicalapi-gcalendar-signup-column").css("background-color", column3_bg);
                
                $(".dicalapi-gcalendar-title").css({
                    "color": title_color,
                    "font-size": title_size
                });
                
                $(".dicalapi-gcalendar-description").css({
                    "color": desc_color,
                    "font-size": desc_size
                });
                
                $(".dicalapi-gcalendar-location").css({
                    "color": location_color,
                    "font-size": location_size
                });
                
                $(".dicalapi-gcalendar-day, .dicalapi-gcalendar-month").css("color", date_color);
                $(".dicalapi-gcalendar-day").css("font-size", date_size);
                $(".dicalapi-gcalendar-month").css("font-size", "calc(" + date_size + " * 0.8)");
                
                $(".dicalapi-gcalendar-signup-button").css({
                    "background-color": button_bg,
                    "color": button_text_color,
                    "font-size": button_text_size
                }).data("hover-color", button_hover_bg);
                
                $("#dicalapi-preview-event").css("box-shadow", shadow);
            }
        });
    </script>';
    
    echo '</div>'; // Fin sección preview
}

// Función para vista previa del shortcode de títulos
function dicalapi_gcalendar_display_title_shortcode_preview() {
    $options = get_option('dicalapi_gcalendar_options');
    $scroll_interval = isset($options['title_scroll_interval']) ? intval($options['title_scroll_interval']) : 5;
    $scroll_interval_ms = $scroll_interval * 1000;
    $preview_bg_color = isset($options['preview_bg_color']) ? $options['preview_bg_color'] : '#ffffff';
    
    echo '<div class="dicalapi-preview-section">';
    echo '<h2>' . __('Vista previa del shortcode de títulos', 'dicalapi-gcalendar') . '</h2>';
    echo '<p>' . __('Así es como se verá el shortcode [dicalapi-gcalendar-titulo] con la configuración actual:', 'dicalapi-gcalendar') . '</p>';
    
    // Contenedor con color de fondo configurable
    echo '<div class="dicalapi-preview-background" style="background-color:' . esc_attr($preview_bg_color) . '; padding: 20px; border-radius: 4px;">';
    
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
    echo '</div>'; // Fin contenedor ticker
    
    echo '</div>'; // Fin del contenedor con fondo personalizado
    
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
            do_settings_sections('dicalapi_gcalendar');
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

/**
 * Genera CSS dinámico para la vista previa basado en las opciones
 */
function dicalapi_gcalendar_generate_admin_preview_css($options) {
    // Generar CSS dinámico
    $css = '';
    $css .= '.dicalapi-gcalendar-container { font-family: Arial, sans-serif; }';
    $css .= '.dicalapi-gcalendar-date-column { background-color: ' . esc_attr($options['column1_bg'] ?? '#f8f9fa') . '; }';
    $css .= '.dicalapi-gcalendar-content-column { background-color: ' . esc_attr($options['column2_bg'] ?? '#ffffff') . '; }';
    $css .= '.dicalapi-gcalendar-signup-column { background-color: ' . esc_attr($options['column3_bg'] ?? '#f0f0f0') . '; }';
    $css .= '.dicalapi-gcalendar-title { color: ' . esc_attr($options['title_color'] ?? '#333333') . '; font-size: ' . esc_attr($options['title_size'] ?? '18px') . '; }';
    $css .= '.dicalapi-gcalendar-description { color: ' . esc_attr($options['desc_color'] ?? '#666666') . '; font-size: ' . esc_attr($options['desc_size'] ?? '14px') . '; }';
    $css .= '.dicalapi-gcalendar-location { color: ' . esc_attr($options['location_color'] ?? '#888888') . '; font-size: ' . esc_attr($options['location_size'] ?? '14px') . '; }';
    $css .= '.dicalapi-gcalendar-signup-button { background-color: ' . esc_attr($options['button_bg_color'] ?? '#007bff') . '; color: ' . esc_attr($options['button_text_color'] ?? '#ffffff') . '; font-size: ' . esc_attr($options['button_text_size'] ?? '14px') . '; }';
    $css .= '.dicalapi-gcalendar-signup-button:hover { background-color: ' . esc_attr($options['button_hover_bg_color'] ?? '#0056b3') . '; }';
    return $css;
}

/**
 * Genera CSS dinámico para el shortcode de títulos en la vista previa
 */
function dicalapi_gcalendar_generate_admin_title_css($options) {
    // Generar CSS dinámico
    $css = '';
    $css .= '.dicalapi-gcalendar-ticker-container { font-family: Arial, sans-serif; }';
    $css .= '.dicalapi-gcalendar-title-text { color: ' . esc_attr($options['title_text_color'] ?? '#333333') . '; font-size: ' . esc_attr($options['title_text_size'] ?? '18px') . '; }';
    $css .= '.dicalapi-gcalendar-title-dates { color: ' . esc_attr($options['title_date_color'] ?? '#007bff') . '; font-size: ' . esc_attr($options['title_date_size'] ?? '16px') . '; }';
    return $css;
}
