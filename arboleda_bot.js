// Espera a que el DOM esté completamente cargado antes de ejecutar el código del chatbot
document.addEventListener('DOMContentLoaded', function() {
    
    // OBTENER CONFIGURACIÓN - Lee datos de PHP vía variable global window (pasada desde PHP)
    const config = window.arboledaBotConfig || { notifications: [] };
    
    // ELEMENTOS DOM - Captura referencias a todos los elementos HTML necesarios para el chat
    const messagesEl = document.getElementById('arboleda_bot_messages');     // Contenedor de mensajes del chat
    const inputEl = document.getElementById('arboleda_bot_input');           // Campo de texto para escribir mensajes
    const toggleEl = document.getElementById('arboleda_bot_toggle');         // Botón para abrir/cerrar chat
    const containerEl = document.getElementById('arboleda_bot_container');   // Contenedor principal del chat
    const sendEl = document.querySelector('.arboleda_bot_send');             // Botón de enviar mensaje
    
    // HABILITAR INPUT - Quita atributos que bloquean el campo de texto (disabled, readonly)
    if (inputEl) {
        inputEl.disabled = false;                       // Habilita el campo si estaba deshabilitado
        inputEl.readOnly = false;                       // Quita modo solo lectura
        inputEl.removeAttribute('readonly');            // Elimina atributo readonly del HTML
        inputEl.style.pointerEvents = 'auto';           // Permite eventos del ratón (click, focus)
    }
    
    // FUNCIÓN NOTIFICACIONES - Crea popups estilo WhatsApp en esquina superior derecha
    function showWhatsAppNotification(notif) {
        // Busca el contenedor HTML donde se mostrarán las notificaciones
        const container = document.getElementById('arboleda_bot_notifications');
        
        // Si no existe el contenedor en el DOM, termina la función sin hacer nada
        if (!container) return;
        
        // Crea un nuevo elemento div para la notificación individual
        const notifDiv = document.createElement('div');
        
        // Añade la clase CSS principal para aplicar estilos (posición fixed, animaciones)
        notifDiv.className = 'arboleda_bot_notification';
        
        // Verifica si la notificación tiene un enlace URL definido
        if (notif.enlace) {
            // Cambia cursor a "mano" para indicar elemento clickeable
            notifDiv.style.cursor = 'pointer';
            
            // Asigna evento onclick: abre enlace en nueva pestaña
            notifDiv.onclick = () => window.open(notif.enlace, '_blank');
        }
        
        // Define el HTML interno usando template literal con datos de la notificación
        notifDiv.innerHTML = `
            <div class="arboleda_bot_notification_header">
                <div class="arboleda_bot_notification_avatar">${notif.titulo?.charAt(0) || '?'}</div>
                <div>
                    <div style="font-weight: 600;">${notif.titulo || 'Notificación'}</div>
                    <div class="arboleda_bot_notification_time">${new Date(notif.fecha).toLocaleTimeString()}</div>
                </div>
            </div>
            <div class="arboleda_bot_notification_message">${notif.descripcion || ''}</div>
        `;
        
        // Inserta como PRIMERA hija (aparece encima de otras notificaciones)
        container.insertBefore(notifDiv, container.firstChild);
        
        // Auto-elimina la notificación después de 10 segundos
        setTimeout(() => notifDiv.remove(), 10000);
    }
    
    // CARGAR NOTIFICACIONES - Muestra las últimas 2 notificaciones con delay progresivo
    if (config.notifications?.length > 0) {
        // Toma solo las primeras 2 notificaciones del array
        config.notifications.slice(0, 2).forEach((notif, i) => {
            // Muestra cada una con delay de 2 segundos entre ellas
            setTimeout(() => showWhatsAppNotification(notif), i * 2000);
        });
    }
    
    // AGREGAR MENSAJE - Inserta mensaje en el chat y hace scroll automático al final
    function addMessage(text, sender) {
        const div = document.createElement('div');                           // Crea div para el mensaje
        div.className = `arboleda_bot_message arboleda_bot_${sender}`;       // Clases CSS: mensaje + user/bot
        // Escapa saltos de línea (\\\\n → <br>) para mostrar correctamente
        div.innerHTML = `<div class="arboleda_bot_content">${(text || '').replace(/\\\\n/g, '<br>')}</div>`;
        messagesEl.appendChild(div);                                        // Añade al contenedor de mensajes
        messagesEl.scrollTop = messagesEl.scrollHeight;                     // Scroll automático al final
    }
    
    // INDICADOR ESCRIBIENDO - Muestra "Buscando..." con animación de puntitos
    function showTyping() {
        hideTyping();                                                       // Limpia indicador anterior
        const typing = document.createElement('div');                        // Crea div del indicador
        typing.id = 'arboleda_bot_typing';                                  // ID único para identificarlo
        typing.className = 'arboleda_bot_message arboleda_bot_bot';         // Estilos de mensaje del bot
        typing.innerHTML = `
            <div class="arboleda_bot_content">
                <span>Buscando...</span>
                <div class="arboleda_bot_typing_dots">
                    <span></span><span></span><span></span>                    <!-- 3 puntitos animados -->
                </div>
            </div>
        `;
        messagesEl.appendChild(typing);                                     // Añade al chat
        messagesEl.scrollTop = messagesEl.scrollHeight;                     // Scroll al final
    }
    
    // OCULTAR ESCRIBIENDO - Elimina el indicador de "Buscando..." del DOM
    function hideTyping() {
        const typing = document.getElementById('arboleda_bot_typing');      // Busca por ID
        if (typing) typing.remove();                                        // Elimina si existe
    }
    
    // ENVÍO A API PHP - Llama al backend chatgpt_api.php con el mensaje del usuario
    async function sendToArboledaBot(message) {
        try {
            showTyping();                                                   // Muestra indicador escribiendo
            const formData = new FormData();                                // Crea objeto FormData para POST
            formData.append('message', message);                            // Añade mensaje como campo 'message'
            
            // Hace petición POST a chatgpt_api.php (o URL de config.apiUrl)
            const response = await fetch(config.apiUrl || 'chatgpt_api.php', {
                method: 'POST',                                             // Método POST
                body: formData                                               // Datos del formulario
            });
            
            hideTyping();                                                   // Oculta indicador
            const data = await response.json();                             // Parsea respuesta JSON
            
            // Muestra respuesta del bot o mensaje de error
            if (data.response) {
                addMessage(data.response, 'bot');                           // Respuesta exitosa
            } else {
                addMessage('No hay respuesta del servidor', 'bot');         // Error del servidor
            }
        } catch (error) {
            hideTyping();                                                   // Oculta indicador en caso de error
            console.error('Chatbot error:', error);                         // Log del error en consola
            addMessage('Error de conexión. Revisa chatgpt_api.php', 'bot'); // Mensaje de error al usuario
        }
    }
    
    // PROCESAR ENVÍO - Función principal que maneja el envío de mensajes
    async function sendMessage() {
        const message = inputEl.value.trim();                               // Obtiene y limpia texto del input
        if (!message || !inputEl) return;                                   // Sale si está vacío o no hay input
        
        addMessage(message, 'user');                                        // Añade mensaje del usuario al chat
        const tempValue = inputEl.value;                                    // Guarda valor temporal
        inputEl.value = '';                                                 // Limpia el input
        inputEl.placeholder = 'Enviando...';                                // Cambia placeholder
        
        await sendToArboledaBot(message);                                   // Envía al backend
        inputEl.placeholder = 'Ej: ¿matriculación?';                        // Restaura placeholder
    }
    
    // TOGGLE CHAT - Abre/cierra la ventana del chat con animación CSS
    if (toggleEl) {
        toggleEl.onclick = () => {                                          // Evento click del botón toggle
            containerEl.classList.toggle('arboleda_bot_active');            // Alterna clase active
            // Si se abre el chat, enfoca el input después de la animación
            if (containerEl.classList.contains('arboleda_bot_active') && inputEl) {
                setTimeout(() => inputEl.focus(), 300);                     // Focus tras 300ms (duración animación)
            }
        };
    }
    
    // BOTÓN ENVIAR - Asigna evento click al botón de enviar
    if (sendEl) {
        sendEl.onclick = sendMessage;                                       // Llama función sendMessage
    }
    
    // ENTER ENVÍA - Maneja tecla Enter (Shift+Enter = nueva línea)
    if (inputEl) {
        inputEl.onkeydown = (e) => {                                        // Evento tecla presionada
            if (e.key === 'Enter' && !e.shiftKey) {                        // Enter SIN Shift = enviar
                e.preventDefault();                                         // Evita salto de línea
                sendMessage();                                              // Envía mensaje
            }
        };
    }
    
    // FOCUS AUTOMÁTICO - Enfoca input cuando termina la animación de apertura
    if (containerEl) {
        containerEl.addEventListener('transitionend', () => {               // Evento fin de transición CSS
            if (containerEl.classList.contains('arboleda_bot_active') && inputEl) {
                inputEl.focus();                                            // Enfoca input si chat está abierto
            }
        });
    }
    
    // ESC CIERRA CHAT - Tecla Escape cierra el chat desde cualquier parte de la página
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && containerEl) {                            // Escape + contenedor existe
            containerEl.classList.remove('arboleda_bot_active');            // Cierra chat (quita clase active)
        }
    });
});
