/**
 * Script para asegurar la inicialización correcta de los tickers de título
 */
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un poco para asegurar que todo esté cargado
    setTimeout(function() {
        initAllTickers();
    }, 300);

    function initAllTickers() {
        // Verificar si hay tickers en la página
        const tickers = document.querySelectorAll('.dicalapi-gcalendar-ticker-container');
        if (tickers.length === 0) return;

        // Inicializar cada ticker encontrado
        tickers.forEach(function(ticker) {
            // Solo inicializar si aún no se ha hecho
            if (!ticker.dataset.initialized) {
                ticker.dataset.initialized = true;
                initDicalapiTicker(ticker.id);
            }
        });
    }

    function initDicalapiTicker(tickerId) {
        const container = document.getElementById(tickerId);
        if (!container) return;
        
        const wrapper = container.querySelector(".dicalapi-gcalendar-ticker-wrapper");
        if (!wrapper) return;
        
        const items = container.querySelectorAll(".dicalapi-gcalendar-ticker-item");
        if (items.length <= 1) return;
        
        const interval = parseInt(wrapper.getAttribute("data-interval")) || 5000;
        const viewport = container.querySelector(".dicalapi-gcalendar-ticker-viewport");
        
        // Asegurarse de que cada ítem tenga el posicionamiento correcto
        items.forEach((item, index) => {
            if (index > 0) {
                item.style.position = "absolute";
                item.style.visibility = "hidden";
                item.style.opacity = "0";
                item.style.transform = "translateY(100%)";
            } else {
                item.style.position = "relative";
                item.style.visibility = "visible";
                item.style.opacity = "1";
                item.style.transform = "translateY(0)";
            }
        });
        
        // Configurar altura del viewport
        const itemHeight = items[0].offsetHeight;
        viewport.style.height = itemHeight + "px";
        
        // Variables de control
        let currentIndex = 0;
        let isAnimating = false;
        let tickInterval;
        
        function animate() {
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
            
            // Use requestAnimationFrame for smooth animation
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
        
        // Start animation with delay between animations
        tickInterval = setInterval(animate, interval);
        
        // Cleanup on page visibility change
        document.addEventListener("visibilitychange", function() {
            if (document.hidden) {
                clearInterval(tickInterval);
            } else {
                // Restart when page becomes visible again
                clearInterval(tickInterval);
                tickInterval = setInterval(animate, interval);
            }
        });
    }

    // Re-check on window resize to handle potential layout changes
    window.addEventListener('resize', function() {
        const tickers = document.querySelectorAll('.dicalapi-gcalendar-ticker-container');
        tickers.forEach(function(ticker) {
            const viewport = ticker.querySelector('.dicalapi-gcalendar-ticker-viewport');
            const firstItem = ticker.querySelector('.dicalapi-gcalendar-ticker-item');
            if (viewport && firstItem) {
                viewport.style.height = firstItem.offsetHeight + "px";
            }
        });
    });
});
