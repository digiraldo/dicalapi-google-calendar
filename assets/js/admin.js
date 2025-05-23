/**
 * Scripts de administración para el plugin DICALAPI Google Calendar Events
 */
jQuery(document).ready(function($) {
    // Inicialización de color pickers
    $('.dicalapi-color-picker').wpColorPicker();
    
    // Manejo del control de sombra
    const shadowPreset = $('#dicalapi-shadow-preset');
    const shadowCustom = $('.dicalapi-shadow-custom');
    const shadowCustomInput = $('#dicalapi-shadow-custom-input');
    const shadowFinalValue = $('#dicalapi-shadow-final-value');
    const shadowPreviewBox = $('#dicalapi-shadow-preview-box');
    
    // Cambio en selector de preset
    shadowPreset.on('change', function() {
        const value = $(this).val();
        
        if (value === 'custom') {
            shadowCustom.show();
            shadowCustomInput.focus();
        } else {
            shadowCustom.hide();
            
            // Actualizar valor y vista previa
            shadowFinalValue.val(value);
            shadowPreviewBox.css('box-shadow', value);
        }
    });
    
    // Actualización en tiempo real del input personalizado
    shadowCustomInput.on('input', function() {
        const customValue = $(this).val();
        
        shadowFinalValue.val(customValue);
        shadowPreviewBox.css('box-shadow', customValue);
    });
    
    // Poner valor inicial si ya está en custom
    if (shadowPreset.val() === 'custom') {
        shadowCustom.show();
    } else {
        shadowCustom.hide();
    }
    
    // Gestión del selector de color de fondo para previsualización
    const previewBackground = $('#dicalapi-shadow-preview-background');
    const previewContainer = $('#dicalapi-shadow-preview-container');
    const previewBox = $('#dicalapi-shadow-preview-box');
    
    previewBackground.on('change', function() {
        const bgColor = $(this).val();
        previewContainer.css('background-color', bgColor);
        
        // Cambiar el color del texto del box según el fondo
        if (bgColor === '#333333' || bgColor === '#1a1a1a') {
            previewBox.css('color', '#ffffff');
            previewBox.css('border-color', '#555');
        } else {
            previewBox.css('color', '#333333');
            previewBox.css('border-color', '#ddd');
        }
    });

    // Función para aplicar fuente Google Font
    function applyGoogleFont(fontName, element) {
        if (fontName && fontName !== '') {
            // Cargar la fuente si no está cargada
            if (!$('link[href*="' + fontName.replace(' ', '+') + '"]').length) {
                $('<link>')
                    .attr('rel', 'stylesheet')
                    .attr('href', 'https://fonts.googleapis.com/css2?family=' + fontName.replace(' ', '+') + ':wght@300;400;700&display=swap')
                    .appendTo('head');
            }
            element.css('font-family', '"' + fontName + '", sans-serif');
        } else {
            element.css('font-family', '');
        }
    }

    // Función para actualizar los fondos de los elementos de vista previa
    function updatePreviewBackgrounds() {
        const column1_bg = $('input[name="dicalapi_gcalendar_options[column1_bg]"]').val() || '#f8f9fa';
        const column2_bg = $('input[name="dicalapi_gcalendar_options[column2_bg]"]').val() || '#ffffff';
        
        // Actualizar el fondo de los elementos de vista previa de contenido (título, descripción, ubicación)
        $('.dicalapi-style-preview').not('.date-preview').css('background-color', column2_bg);
        
        // Actualizar el fondo del elemento de vista previa de fechas
        $('.dicalapi-preview-date').css('background-color', column1_bg);
    }

    // Vista previa en tiempo real para título
    function updateTitlePreview() {
        const preview = $('#title_preview');
        if (preview.length) {
            const font = $('select[name="dicalapi_gcalendar_options[title_font]"]').val();
            const color = $('input[name="dicalapi_gcalendar_options[title_color]"]').val();
            const size = $('input[name="dicalapi_gcalendar_options[title_size]"]').val();
            const bold = $('input[name="dicalapi_gcalendar_options[title_bold]"]').is(':checked');
            const italic = $('input[name="dicalapi_gcalendar_options[title_italic]"]').is(':checked');
            const underline = $('input[name="dicalapi_gcalendar_options[title_underline]"]').is(':checked');
            const align = $('input[name="dicalapi_gcalendar_options[title_align]"]:checked').val();

            applyGoogleFont(font, preview);
            preview.css({
                'color': color,
                'font-size': size,
                'font-weight': bold ? 'bold' : 'normal',
                'font-style': italic ? 'italic' : 'normal',
                'text-decoration': underline ? 'underline' : 'none',
                'text-align': align || 'center'
            });
            
            // Actualizar el fondo del contenedor de vista previa (columna de contenido)
            updatePreviewBackgrounds();
        }
    }

    // Vista previa en tiempo real para descripción
    function updateDescPreview() {
        const preview = $('#desc_preview');
        if (preview.length) {
            const font = $('select[name="dicalapi_gcalendar_options[desc_font]"]').val();
            const color = $('input[name="dicalapi_gcalendar_options[desc_color]"]').val();
            const size = $('input[name="dicalapi_gcalendar_options[desc_size]"]').val();
            const bold = $('input[name="dicalapi_gcalendar_options[desc_bold]"]').is(':checked');
            const italic = $('input[name="dicalapi_gcalendar_options[desc_italic]"]').is(':checked');
            const underline = $('input[name="dicalapi_gcalendar_options[desc_underline]"]').is(':checked');
            const align = $('input[name="dicalapi_gcalendar_options[desc_align]"]:checked').val();

            applyGoogleFont(font, preview);
            preview.css({
                'color': color,
                'font-size': size,
                'font-weight': bold ? 'bold' : 'normal',
                'font-style': italic ? 'italic' : 'normal',
                'text-decoration': underline ? 'underline' : 'none',
                'text-align': align || 'center'
            });
            
            // Actualizar el fondo del contenedor de vista previa (columna de contenido)
            updatePreviewBackgrounds();
        }
    }

    // Vista previa en tiempo real para ubicación
    function updateLocationPreview() {
        const preview = $('#location_preview');
        if (preview.length) {
            const font = $('select[name="dicalapi_gcalendar_options[location_font]"]').val();
            const color = $('input[name="dicalapi_gcalendar_options[location_color]"]').val();
            const size = $('input[name="dicalapi_gcalendar_options[location_size]"]').val();
            const bold = $('input[name="dicalapi_gcalendar_options[location_bold]"]').is(':checked');
            const italic = $('input[name="dicalapi_gcalendar_options[location_italic]"]').is(':checked');
            const underline = $('input[name="dicalapi_gcalendar_options[location_underline]"]').is(':checked');
            const align = $('input[name="dicalapi_gcalendar_options[location_align]"]:checked').val();

            applyGoogleFont(font, preview);
            preview.css({
                'color': color,
                'font-size': size,
                'font-weight': bold ? 'bold' : 'normal',
                'font-style': italic ? 'italic' : 'normal',
                'text-decoration': underline ? 'underline' : 'none',
                'text-align': align || 'center'
            });
            
            // Actualizar el fondo del contenedor de vista previa (columna de contenido)
            updatePreviewBackgrounds();
        }
    }

    // Vista previa en tiempo real para día y mes
    function updateDatePreview() {
        const dayPreview = $('#day_preview');
        const monthPreview = $('#month_preview');
        
        if (dayPreview.length) {
            const dayFont = $('select[name="dicalapi_gcalendar_options[day_font]"]').val();
            const dayColor = $('input[name="dicalapi_gcalendar_options[day_color]"]').val();
            const daySize = $('input[name="dicalapi_gcalendar_options[day_size]"]').val();
            const dayBold = $('input[name="dicalapi_gcalendar_options[day_bold]"]').is(':checked');
            const dayItalic = $('input[name="dicalapi_gcalendar_options[day_italic]"]').is(':checked');
            const dayUnderline = $('input[name="dicalapi_gcalendar_options[day_underline]"]').is(':checked');

            applyGoogleFont(dayFont, dayPreview);
            dayPreview.css({
                'color': dayColor,
                'font-size': daySize,
                'font-weight': dayBold ? 'bold' : 'normal',
                'font-style': dayItalic ? 'italic' : 'normal',
                'text-decoration': dayUnderline ? 'underline' : 'none'
            });
        }

        if (monthPreview.length) {
            const monthFont = $('select[name="dicalapi_gcalendar_options[month_font]"]').val();
            const monthColor = $('input[name="dicalapi_gcalendar_options[month_color]"]').val();
            const monthSize = $('input[name="dicalapi_gcalendar_options[month_size]"]').val();
            const monthBold = $('input[name="dicalapi_gcalendar_options[month_bold]"]').is(':checked');
            const monthItalic = $('input[name="dicalapi_gcalendar_options[month_italic]"]').is(':checked');
            const monthUnderline = $('input[name="dicalapi_gcalendar_options[month_underline]"]').is(':checked');

            applyGoogleFont(monthFont, monthPreview);
            monthPreview.css({
                'color': monthColor,
                'font-size': monthSize,
                'font-weight': monthBold ? 'bold' : 'normal',
                'font-style': monthItalic ? 'italic' : 'normal',
                'text-decoration': monthUnderline ? 'underline' : 'none'
            });
        }
        
        // Actualizar el fondo del contenedor de vista previa de fechas (columna de fechas)
        updatePreviewBackgrounds();
    }

    // Eventos para título
    $('select[name="dicalapi_gcalendar_options[title_font]"], input[name="dicalapi_gcalendar_options[title_color]"], input[name="dicalapi_gcalendar_options[title_size]"], input[name="dicalapi_gcalendar_options[title_bold]"], input[name="dicalapi_gcalendar_options[title_italic]"], input[name="dicalapi_gcalendar_options[title_underline]"], input[name="dicalapi_gcalendar_options[title_align]"]').on('change input', updateTitlePreview);

    // Eventos para descripción
    $('select[name="dicalapi_gcalendar_options[desc_font]"], input[name="dicalapi_gcalendar_options[desc_color]"], input[name="dicalapi_gcalendar_options[desc_size]"], input[name="dicalapi_gcalendar_options[desc_bold]"], input[name="dicalapi_gcalendar_options[desc_italic]"], input[name="dicalapi_gcalendar_options[desc_underline]"], input[name="dicalapi_gcalendar_options[desc_align]"]').on('change input', updateDescPreview);

    // Eventos para ubicación
    $('select[name="dicalapi_gcalendar_options[location_font]"], input[name="dicalapi_gcalendar_options[location_color]"], input[name="dicalapi_gcalendar_options[location_size]"], input[name="dicalapi_gcalendar_options[location_bold]"], input[name="dicalapi_gcalendar_options[location_italic]"], input[name="dicalapi_gcalendar_options[location_underline]"], input[name="dicalapi_gcalendar_options[location_align]"]').on('change input', updateLocationPreview);

    // Eventos para fechas (día y mes)
    $('select[name="dicalapi_gcalendar_options[day_font]"], input[name="dicalapi_gcalendar_options[day_color]"], input[name="dicalapi_gcalendar_options[day_size]"], input[name="dicalapi_gcalendar_options[day_bold]"], input[name="dicalapi_gcalendar_options[day_italic]"], input[name="dicalapi_gcalendar_options[day_underline]"]').on('change input', updateDatePreview);
    
    $('select[name="dicalapi_gcalendar_options[month_font]"], input[name="dicalapi_gcalendar_options[month_color]"], input[name="dicalapi_gcalendar_options[month_size]"], input[name="dicalapi_gcalendar_options[month_bold]"], input[name="dicalapi_gcalendar_options[month_italic]"], input[name="dicalapi_gcalendar_options[month_underline]"]').on('change input', updateDatePreview);

    // Eventos para colores de columnas (para actualizar fondos de vista previa)
    $('input[name="dicalapi_gcalendar_options[column1_bg]"], input[name="dicalapi_gcalendar_options[column2_bg]"]').on('change input', updatePreviewBackgrounds);

    // Inicializar vistas previas al cargar la página
    updateTitlePreview();
    updateDescPreview();
    updateLocationPreview();
    updateDatePreview();
    updatePreviewBackgrounds();
});
