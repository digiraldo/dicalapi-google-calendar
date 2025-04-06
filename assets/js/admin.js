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
});
