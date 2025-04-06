=== DICALAPI Google Calendar Events ===
Contributors: digiraldo
Tags: google calendar, eventos, calendar, gcal, shortcode
Requires at least: 5.2
Tested up to: 6.4
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Muestra eventos de Google Calendar en tu sitio de WordPress con diseño personalizable y animaciones.

== Description ==

DICALAPI Google Calendar Events es un plugin que permite mostrar eventos de Google Calendar en tu sitio de WordPress. Ofrece dos formatos de visualización:

1. **Visualización completa** con el shortcode `[dicalapi_gcalendar]`: Muestra eventos en un formato de tres columnas con:
   - Fechas del evento en la primera columna
   - Contenido (título, descripción y ubicación) en la columna central
   - Botón de inscripción en la tercera columna (opcional)

2. **Visualización de títulos** con el shortcode `[dicalapi-gcalendar-titulo]`: Muestra títulos y fechas de eventos en formato compacto con rotación automática.

= Características principales =

* Fácil integración con Google Calendar
* Diseño totalmente personalizable desde el panel de administración
* Visualización atractiva de eventos con fechas, títulos, descripciones y ubicaciones
* Ticker de títulos con rotación automática
* Botones de inscripción personalizables
* Caché de eventos para mejor rendimiento
* Compatible con dispositivos móviles

= Opciones de personalización =

* Colores de fondo para cada columna
* Sombras y efectos visuales
* Colores y tamaños para textos (títulos, descripciones, fechas)
* Intervalo de rotación para el ticker de títulos
* URL de inscripción predeterminada y por evento

== Installation ==

1. Sube la carpeta 'dicalapi-google-calendar' al directorio `/wp-content/plugins/`
2. Activa el plugin a través del menú 'Plugins' en WordPress
3. Ve a Ajustes > Google Calendar para configurar el plugin
4. Inserta el ID del calendario de Google y tu API Key
5. Personaliza la apariencia según tus preferencias
6. Usa los shortcodes `[dicalapi_gcalendar]` o `[dicalapi-gcalendar-titulo]` en tus páginas

= Requisitos =
* Una cuenta de Google con acceso a Google Calendar
* Una API Key de Google Cloud Platform
* ID del calendario de Google Calendar

== Frequently Asked Questions ==

= ¿Cómo obtengo una API Key de Google? =

1. Ve a Google Cloud Platform Console
2. Crea un nuevo proyecto o selecciona uno existente
3. Ve a "Activar APIs y servicios" y activa la API de Google Calendar
4. Crea una API Key en la sección de Credenciales

= ¿Cómo encuentro mi Calendar ID? =

1. Ve a Google Calendar
2. Haz clic en los tres puntos junto al nombre de tu calendario
3. Selecciona "Configuración y compartir"
4. Baja hasta "Integrar calendario" donde encontrarás el ID

= ¿Puedo personalizar los colores? =

Sí, puedes personalizar los colores de todos los elementos: columnas, textos, fechas, botones, etc. desde el panel de administración.

= ¿El plugin funciona con caché? =

Sí, el plugin implementa un sistema de caché para mejorar el rendimiento y reducir las llamadas a la API de Google.

== Screenshots ==

1. Visualización de eventos en el frontend
2. Ticker de títulos con rotación automática
3. Panel de configuración del plugin
4. Personalización de estilos y colores
5. Vista previa de configuración

== Changelog ==

= 1.0.0 =
* Versión inicial

== Upgrade Notice ==

= 1.0.0 =
Esta es la primera versión del plugin.
