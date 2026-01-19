// ⭐ SCRIPT COMPLETO CORREGIDO Y LIMPIO ⭐
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. MENÚ MÓVIL
    const barraMenu = document.querySelector('.barra-menu');
    const navToggle = document.querySelector('.nav-toggle');

    navToggle?.addEventListener('click', () => {
        barraMenu?.classList.toggle('abierto');
    });

    // Cerrar menú al click
    document.querySelectorAll('.item-menu a').forEach(link => {
        link.addEventListener('click', () => {
            barraMenu?.classList.remove('abierto');
        });
    });

    // 2. CARRUSEL AUTOMÁTICO (SIN INDICADORES)
    let carruselIndex = 0;
    const imagenes = document.querySelectorAll('.carrusel-imagen');

    function mostrarImagen(index) {
        imagenes.forEach(img => img.classList.remove('activa'));
        imagenes[index].classList.add('activa');
    }

    function siguienteImagen() {
        carruselIndex = (carruselIndex + 1) % imagenes.length;
        mostrarImagen(carruselIndex);
    }

    // ⭐ CAMBIA CADA 2.5 SEGUNDOS AUTOMÁTICO ⭐
    setInterval(siguienteImagen, 2500);

    // 3. BREADCRUMBS DINÁMICOS MEJORADOS
function actualizarBreadcrumb() {
    const breadcrumbNav = document.querySelector('.breadcrumb-nav');
    if (!breadcrumbNav) return;
    
    const links = breadcrumbNav.querySelectorAll('.breadcrumb-link');
    const hash = window.location.hash.substring(1) || 'noticias';
    
    links.forEach(link => {
        link.classList.remove('activo');
        if (link.getAttribute('href') === `#${hash}` || 
            (hash === 'noticias' && link.textContent.includes('Noticias'))) {
            link.classList.add('activo');
        }
    });
    
    // Actualizar texto dinámico si existe
    const textoActual = breadcrumbNav.querySelector('.breadcrumb-actual');
    if (textoActual) {
        textoActual.textContent = hash.charAt(0).toUpperCase() + hash.slice(1);
    }
}


    // Inicializar y escuchar cambios
    actualizarBreadcrumb();
    window.addEventListener('hashchange', actualizarBreadcrumb);
});
