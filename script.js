// ESPERA DOM CARGADO - Ejecuta cuando HTML completo (sin esperar imágenes/CSS)
document.addEventListener('DOMContentLoaded', function() {
    
    // === MENÚ HAMBURGUESA MÓVIL ===
    // Captura elementos menú móvil
    const navegacionPrincipal = document.querySelector('.navegacion-principal');  // Menú principal desktop/móvil
    const botonMovil = document.querySelector('.boton-menu-movil');              // ☰ botón hamburguesa

    // Evento CLICK hamburguesa - Abre/cierra menú responsive
    if (botonMovil) {  // Verifica botón existe en DOM
        botonMovil.addEventListener('click', () => {
            // Toggle clase 'abierto' → muestra/oculta menú con CSS transform/opacidad
            navegacionPrincipal.classList.toggle('abierto');
        });
    }

    // === CARRUSEL AUTOMÁTICO HERO ===
    let carruselIndex = 0;  // Índice imagen actual (0 = primera)
    const imagenes = document.querySelectorAll('.imagen-carrusel');  // Array todas imágenes carrusel

    // FUNCIÓN: Muestra imagen específica quitando clase activa de todas
    function mostrarImagen(index) {
        imagenes.forEach(img => img.classList.remove('activa'));  // Quita 'activa' de TODAS
        imagenes[index].classList.add('activa');                  // Añade 'activa' SOLO a actual
    }

    // FUNCIÓN: Avanza carrusel (cíclico: fin → inicio)
    function siguienteImagen() {
        carruselIndex = (carruselIndex + 1) % imagenes.length;  // +1 módulo total = cíclico
        mostrarImagen(carruselIndex);                            // Muestra nueva imagen
    }

    // INICIA CARRUSEL - Cada 2.5 segundos si hay imágenes
    if (imagenes.length > 0) {
        setInterval(siguienteImagen, 2500);  // 2500ms = 2.5s entre slides
    }
});
