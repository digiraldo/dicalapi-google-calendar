/**
 * Script para asegurar el funcionamiento correcto de los elementos en el frontend
 */
document.addEventListener('DOMContentLoaded', function() {
    // Ejecutar después de un breve retraso para asegurar que todos los elementos estén cargados
    setTimeout(function() {
        // Aplicar centrado adicional a los tickers
        const tickerContainers = document.querySelectorAll('.dicalapi-gcalendar-ticker-container');
        tickerContainers.forEach(function(container) {
            // Forzar centrado en el contenedor
            container.style.textAlign = 'center';
            container.style.margin = '0 auto';
            
            // Centrar elementos internos
            const viewport = container.querySelector('.dicalapi-gcalendar-ticker-viewport');
            if (viewport) {
                viewport.style.textAlign = 'center';
                viewport.style.margin = '0 auto';
            }
            
            // Centrar textos de eventos
            const items = container.querySelectorAll('.dicalapi-gcalendar-ticker-item');
            items.forEach(function(item) {
                item.style.textAlign = 'center';
                item.style.margin = '0 auto';
                item.style.justifyContent = 'center';
                
                // Centrar título y fecha
                const titleText = item.querySelector('.dicalapi-gcalendar-title-text');
                const dateText = item.querySelector('.dicalapi-gcalendar-title-dates');
                
                if (titleText) titleText.style.textAlign = 'center';
                if (dateText) dateText.style.textAlign = 'center';
            });
        });
        
        // Asegurar estilos correctos en el shortcode principal
        const mainContainers = document.querySelectorAll('.dicalapi-gcalendar-container');
        mainContainers.forEach(function(container) {
            // Verificar si está asociado con el shortcode principal (no tiene class ticker)
            if (!container.classList.contains('dicalapi-gcalendar-ticker-container')) {
                // Forzar aplicación de estilos con !important para evitar sobreescrituras
                const events = container.querySelectorAll('.dicalapi-gcalendar-event');
                
                events.forEach(function(event) {
                    // Aplicar estilos inline si están en las opciones globales
                    if (window.dicalapi_options) {
                        const opts = window.dicalapi_options;
                        
                        // Aplicar colores y sombras
                        if (opts.row_shadow) event.style.boxShadow = opts.row_shadow + ' !important';
                        
                        // Columnas
                        const dateCol = event.querySelector('.dicalapi-gcalendar-date-column');
                        const contentCol = event.querySelector('.dicalapi-gcalendar-content-column');
                        const signupCol = event.querySelector('.dicalapi-gcalendar-signup-column');
                        
                        if (dateCol && opts.column1_bg) dateCol.style.backgroundColor = opts.column1_bg + ' !important';
                        if (contentCol && opts.column2_bg) contentCol.style.backgroundColor = opts.column2_bg + ' !important';
                        if (signupCol && opts.column3_bg) signupCol.style.backgroundColor = opts.column3_bg + ' !important';
                        
                        // Aplicar colores y tamaños a los elementos de texto
                        const titleElements = event.querySelectorAll('.dicalapi-gcalendar-title');
                        const descElements = event.querySelectorAll('.dicalapi-gcalendar-description');
                        const locElements = event.querySelectorAll('.dicalapi-gcalendar-location');
                        const dayElements = event.querySelectorAll('.dicalapi-gcalendar-day');
                        const monthElements = event.querySelectorAll('.dicalapi-gcalendar-month');
                        
                        if (opts.title_color) titleElements.forEach(el => el.style.color = opts.title_color + ' !important');
                        if (opts.title_size) titleElements.forEach(el => el.style.fontSize = opts.title_size + ' !important');
                        if (opts.desc_color) descElements.forEach(el => el.style.color = opts.desc_color + ' !important');
                        if (opts.desc_size) descElements.forEach(el => el.style.fontSize = opts.desc_size + ' !important');
                        if (opts.location_color) locElements.forEach(el => el.style.color = opts.location_color + ' !important');
                        if (opts.location_size) locElements.forEach(el => el.style.fontSize = opts.location_size + ' !important');
                        if (opts.date_color) {
                            dayElements.forEach(el => el.style.color = opts.date_color + ' !important');
                            monthElements.forEach(el => el.style.color = opts.date_color + ' !important');
                        }
                        if (opts.date_size) {
                            dayElements.forEach(el => el.style.fontSize = opts.date_size + ' !important');
                            monthElements.forEach(el => el.style.fontSize = opts.date_size + ' !important');
                        }
                    }
                });
            }
        });
        
        // Inicializar los tickers que aún no tengan la animación
        initializeTickers();
    }, 300);
    
    // Asegurar que hay espacio entre título y fecha
    setTimeout(function() {
        const dateElements = document.querySelectorAll('.dicalapi-gcalendar-title-dates');
        dateElements.forEach(function(element) {
            const text = element.textContent || element.innerText;
            if (text && text.charAt(0) === '(') {
                element.innerHTML = ' ' + text;
            }
        });
    }, 500);
    
    // Función para inicializar los tickers restantes
    function initializeTickers() {
        // Esperar un momento para asegurarse de que todo esté cargado
        setTimeout(function() {
            // Buscar todos los tickers en la página
            const tickers = document.querySelectorAll('.dicalapi-gcalendar-ticker-container');
            if (tickers.length === 0) return;
            
            // Verificar si alguno necesita inicialización adicional
            tickers.forEach(function(ticker) {
                // Solo procesar si no ha sido inicializado por el script inline
                if (!ticker.dataset.initialized) {
                    ticker.dataset.initialized = true;
                    const id = ticker.id;
                    
                    // Si hay una función global específica para este ticker, usarla
                    if (window["dicalapiInitTickerWithId"]) {
                        window.dicalapiInitTickerWithId(id);
                    } else {
                        // Implementación de respaldo para asegurar que funcione
                        initTickerBackup(ticker);
                    }
                }
            });
        }, 500);
        
        // Función de respaldo para inicializar ticker
        function initTickerBackup(container) {
            const wrapper = container.querySelector(".dicalapi-gcalendar-ticker-wrapper");
            if (!wrapper) return;
            
            const ticker = wrapper.querySelector(".dicalapi-gcalendar-ticker-list");
            const items = ticker.querySelectorAll(".dicalapi-gcalendar-ticker-item");
            
            // Si no hay suficientes elementos, no hacemos nada
            if (items.length <= 1) return;
            
            const interval = parseInt(wrapper.getAttribute("data-interval")) || 5000;
            const viewport = wrapper.querySelector(".dicalapi-gcalendar-ticker-viewport");
            
            // Forzar aplicación de estilos según las opciones globales
            if (window.dicalapi_options) {
                const titleColor = window.dicalapi_options.title_text_color || '#333333';
                const titleSize = window.dicalapi_options.title_text_size || '18px';
                const dateColor = window.dicalapi_options.title_date_color || '#007bff';
                const dateSize = window.dicalapi_options.title_date_size || '16px';
                
                items.forEach(item => {
                    const titleText = item.querySelector(".dicalapi-gcalendar-title-text");
                    const dateText = item.querySelector(".dicalapi-gcalendar-title-dates");
                    
                    if (titleText) {
                        titleText.style.color = titleColor;
                        titleText.style.fontSize = titleSize;
                        titleText.style.fontWeight = "bold";
                    }
                    
                    if (dateText) {
                        dateText.style.color = dateColor;
                        dateText.style.fontSize = dateSize;
                    }
                });
            }
            
            // Configurar posiciones iniciales
            items.forEach((item, index) => {
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
            
            // Forzar reflow
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
                
                // Preparar elemento siguiente
                nextItem.style.transition = "none";
                nextItem.style.transform = "translateY(100%)";
                nextItem.style.visibility = "visible";
                nextItem.style.opacity = "0";
                
                // Forzar reflow
                nextItem.offsetHeight;
                
                // Iniciar animación
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
            
            // Iniciar animación después de un pequeño retraso
            setTimeout(function() {
                tickerInterval = setInterval(animateNext, interval);
            }, 500);
            
            // Manejar visibilidad de página
            document.addEventListener("visibilitychange", function() {
                if (document.hidden) {
                    if (tickerInterval) clearInterval(tickerInterval);
                } else {
                    if (tickerInterval) clearInterval(tickerInterval);
                    tickerInterval = setInterval(animateNext, interval);
                }
            });
        }
        
        // Reiniciar tickers cuando hay cambios de tamaño de ventana
        window.addEventListener('resize', function() {
            const tickers = document.querySelectorAll('.dicalapi-gcalendar-ticker-container');
            tickers.forEach(function(ticker) {
                const viewport = ticker.querySelector('.dicalapi-gcalendar-ticker-viewport');
                const firstItem = ticker.querySelector('.dicalapi-gcalendar-ticker-item');
                
                if (viewport && firstItem && firstItem.offsetHeight > 0) {
                    viewport.style.height = firstItem.offsetHeight + "px";
                }
            });
        });
    }
});
