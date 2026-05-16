document.addEventListener("DOMContentLoaded", () => {
    // 1. CONTROL DEL MENÚ MÓVIL GENERAL
    const navToggle = document.querySelector(".header_pagina_nav_toggle"); 
    const barraMenu = document.querySelector(".header_pagina_barra_menu");

    if (navToggle && barraMenu) {
        navToggle.addEventListener("click", () => {
            barraMenu.classList.toggle("open");
            
            if (barraMenu.classList.contains("open")) {
                navToggle.innerHTML = "✕";
            } else {
                navToggle.innerHTML = "☰";
            }
        });
    }

    // 2. CONTROL DEL NIVEL 2 (Nuestro Centro, Secretaría, Oferta...)
    const triggersNivel2 = document.querySelectorAll(".header_pagina_desplegable_trigger");

    triggersNivel2.forEach(trigger => {
        trigger.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            const padre = trigger.closest(".header_pagina_desplegable");
            
            if (padre) {
                // Cerramos otros menús del mismo nivel (Efecto acordeón)
                document.querySelectorAll(".header_pagina_desplegable").forEach(item => {
                    if (item !== padre) item.classList.remove("abierto");
                });

                padre.classList.toggle("abierto");
            }
        });
    });

    // 3. CONTROL DEL NIVEL 3 (Matriculación, Convalidación, etc.) - ¡REPARADO!
    // Seleccionamos toda la barra contenedora del título de nivel 3
    const contenedoresNivel3 = document.querySelectorAll(".header_pagina_contenedor_titulo_nivel3");

    contenedoresNivel3.forEach(contenedorClick => {
        contenedorClick.addEventListener("click", (e) => {
            // Buscamos si el clic viene de un enlace <a> real con un enlace válido
            const enlace = e.target.closest("a");
            
            // Si es un enlace real y NO apunta a "#", dejamos que navegue normalmente
            if (enlace && enlace.getAttribute("href") !== "#") {
                return; 
            }

            // Si es un enlace con "#" o se hizo clic en la flecha/contenedor, controlamos el despliegue
            e.preventDefault();
            e.stopPropagation(); // Evita que se cierre el menú superior (Secretaría)

            const contenedorPadreNivel3 = contenedorClick.closest(".header_pagina_submenu_item_desplegable");
            
            if (contenedorPadreNivel3) {
                // Cerrar otros submenús de nivel 3 en el mismo bloque para evitar solapamientos
                const hermanos = contenedorPadreNivel3.parentElement.querySelectorAll(".header_pagina_submenu_item_desplegable");
                hermanos.forEach(hermano => {
                    if (hermano !== contenedorPadreNivel3) {
                        hermano.classList.remove("abierto_anidado");
                    }
                });

                // Alternamos la clase para mostrar/ocultar el submenú anidado
                contenedorPadreNivel3.classList.toggle("abierto_anidado");
            }
        });
    });
});