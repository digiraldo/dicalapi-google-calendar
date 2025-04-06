/**
 * JavaScript para la página de administración
 */
jQuery(document).ready(function($) {
    // Inicializar los selectores de color
    $('.dicalapi-color-picker').wpColorPicker({
        change: function(event, ui) {
            // Actualizar vista previa cuando cambia un color
            updatePreview();
        }
    });
    
    // Función para actualizar la vista previa
    function updatePreview() {
        // Obtener todos los valores relevantes
        const column1Bg = $('input[name="dicalapi_gcalendar_options[column1_bg]"]').val() || '#f8f9fa';
        const column2Bg = $('input[name="dicalapi_gcalendar_options[column2_bg]"]').val() || '#ffffff';
        const column3Bg = $('input[name="dicalapi_gcalendar_options[column3_bg]"]').val() || '#f0f0f0';
        const rowShadow = $('#dicalapi-shadow-final-value').val() || '0px 2px 5px rgba(0,0,0,0.1)';
        
        // Aplicar estilos a la vista previa
        $('#dicalapi-preview-event').css('box-shadow', rowShadow);
        $('.dicalapi-gcalendar-date-column').css('background-color', column1Bg);
        $('.dicalapi-gcalendar-content-column').css('background-color', column2Bg);
        $('.dicalapi-gcalendar-signup-column').css('background-color', column3Bg);
    }
    
    // Control de sombras
    if ($('#dicalapi-shadow-preset').length) {
        // Evento de cambio para el selector de presets
        $('#dicalapi-shadow-preset').on('change', function() {
            const selectedValue = $(this).val();
            
            // Mostrar/ocultar campo personalizado
            if (selectedValue === 'custom') {
                $('.dicalapi-shadow-custom').show();
            } else {
                $('.dicalapi-shadow-custom').hide();
                $('#dicalapi-shadow-final-value').val(selectedValue);
                
                // Actualizar previsualización de la sombra
                $('#dicalapi-shadow-preview-box').css('box-shadow', selectedValue);
                
                // Actualizar la vista previa del evento
                updatePreview();
            }
        });
        
        // Evento para el campo personalizado
        $('#dicalapi-shadow-custom-input').on('input', function() {
            const customValue = $(this).val();
            $('#dicalapi-shadow-final-value').val(customValue);
            
            // Actualizar previsualización de la sombra
            $('#dicalapi-shadow-preview-box').css('box-shadow', customValue);
            
            // Actualizar la vista previa del evento
            updatePreview();
        });
    }
    
    // Control para la sombra del widget de títulos
    if ($('#dicalapi-title-shadow-preset').length) {
        $('#dicalapi-title-shadow-preset').on('change', function() {
            const selectedValue = $(this).val();
            
            if (selectedValue === 'custom') {
                $('.dicalapi-shadow-control.title-widget .dicalapi-shadow-custom').show();
                $('#dicalapi-title-shadow-value').val($('#dicalapi-title-shadow-custom').val());
            } else {
                $('.dicalapi-shadow-control.title-widget .dicalapi-shadow-custom').hide();
                $('#dicalapi-title-shadow-value').val(selectedValue);
            }
            
            // Actualizar vista previa
            updateTitlePreview();
        });
        
        $('#dicalapi-title-shadow-custom').on('input', function() {
            const customValue = $(this).val();
            $('#dicalapi-title-shadow-value').val(customValue);
            
            // Actualizar vista previa
            updateTitlePreview();
        });
    }
    
    // Función para actualizar vista previa del shortcode de títulos
    function updateTitlePreview() {
        const titleContainer = $('.dicalapi-preview-section .dicalapi-gcalendar-title-container');
        if (!titleContainer.length) return;
        
        // Actualizar estilos del contenedor
        const widgetBg = $('input[name="dicalapi_gcalendar_options[title_widget_bg]"]').val() || '#f8f9fa';
        const widgetShadow = $('#dicalapi-title-shadow-value').val() || '0px 2px 5px rgba(0,0,0,0.1)';
        titleContainer.css({
            'background-color': widgetBg,
            'box-shadow': widgetShadow
        });
        
        // Actualizar estilos de texto
        const titleColor = $('input[name="dicalapi_gcalendar_options[title_text_color]"]').val() || '#333333';
        const titleSize = $('input[name="dicalapi_gcalendar_options[title_text_size]"]').val() || '18px';
        $('.dicalapi-gcalendar-title-text').css({
            'color': titleColor,
            'font-size': titleSize
        });
        
        // Actualizar estilos de fechas
        const dateColor = $('input[name="dicalapi_gcalendar_options[title_date_color]"]').val() || '#007bff';
        const dateSize = $('input[name="dicalapi_gcalendar_options[title_date_size]"]').val() || '16px';
        $('.dicalapi-gcalendar-title-dates').css({
            'color': dateColor,
            'font-size': dateSize
        });
        
        // Actualizar color de indicadores
        const indicatorColor = $('input[name="dicalapi_gcalendar_options[title_indicator_color]"]').val() || '#007bff';
        $('.dicalapi-gcalendar-title-indicator.active').css('background-color', indicatorColor);
    }
    
    // Actualizar vista previa cuando cambia cualquier input de texto
    $('input[type="text"]').on('input', function() {
        updatePreview();
    });
    
    // Actualizar vista previa cuando cambia cualquier input relacionado con título
    $('input[name^="dicalapi_gcalendar_options[title_"]').on('input change', function() {
        updateTitlePreview();
    });
    
    // Ejecutar la actualización inicial
    updatePreview();
    
    // Inicializar vista previa del título
    updateTitlePreview();
});
