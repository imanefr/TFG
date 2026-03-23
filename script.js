document.addEventListener('DOMContentLoaded', function() {
    // MENÚ MÓVIL
    const navegacionPrincipal = document.querySelector('.navegacion-principal');
    const botonMovil = document.querySelector('.boton-menu-movil');

    if (botonMovil) {
        botonMovil.addEventListener('click', () => {
            navegacionPrincipal.classList.toggle('abierto');
        });
    }

    // CARRUSEL
    let carruselIndex = 0;
    const imagenes = document.querySelectorAll('.imagen-carrusel');

    function mostrarImagen(index) {
        imagenes.forEach(img => img.classList.remove('activa'));
        imagenes[index].classList.add('activa');
    }

    function siguienteImagen() {
        carruselIndex = (carruselIndex + 1) % imagenes.length;
        mostrarImagen(carruselIndex);
    }

    if (imagenes.length > 0) {
        setInterval(siguienteImagen, 2500);
    }
});
