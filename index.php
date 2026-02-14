<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php include_once 'head.php'; ?>

<!-- CONTENIDO ESPECÍFICO DE INDEX -->
<section id="inicio" class="hero-carrusel">
    <div class="contenedor-carrusel">
        <div class="imagen-carrusel activa" style="background-image: url('img/instituto_back_1.jpg');"></div>
        <div class="imagen-carrusel" style="background-image: url('img/instituto_back_2.jpg');"></div>
        <div class="imagen-carrusel" style="background-image: url('img/instituto_back_3.jpg');"></div>
    </div>
    <div class="contenido-hero">
        <h2 class="titulo-hero">
            <span class="texto-hero-superior">BIENVENIDOS A NUESTRO CENTRO</span>
            <span class="texto-hero-principal">IES LA ARBOLEDA</span>
            <span class="texto-hero-inferior">Alcorcón - Tu instituto de referencia</span>
        </h2>
    </div>
</section>

<section class="seccion-atajo">
    <div class="contenedor-principal">
        <h3 class="titulo-atajo">A UN CLIC</h3>
        <div class="grid-atajos">
            <a href="https://aulavirtual33.educa.madrid.org/ies.laarboleda.alcorcon/" class="tarjeta-atajo card-un-clic">
                <div class="icono-atajo"><i class="fas fa-graduation-cap"></i></div>
                <h4 class="titulo-atajo-card">Aula Virtual</h4>
            </a>
            <a href="https://correoweb.educa.madrid.org/" class="tarjeta-atajo card-un-clic">
                <div class="icono-atajo"><i class="fas fa-envelope"></i></div>
                <h4 class="titulo-atajo-card">Correo educamadrid</h4>
            </a>
            <a href="https://raices.madrid.org/" class="tarjeta-atajo card-un-clic">
                <div class="icono-atajo"><i class="fas fa-tree"></i></div>
                <h4 class="titulo-atajo-card">Roble/Raíces</h4>
            </a>
        </div>
    </div>
</section>

<main>
    <?php include("conexion.php"); ?>
    <section class="contenedor-principal">
        <h2 class="titulo-atajo">RELEVANTE AHORA</h2>
        <div class="grid-noticias">
            <a href="#" class="tarjeta-noticia card-un-clic">
                <img src="img/libros_texto.jpg" alt="Libros de texto">
                <p>Libros de texto 2025‑26</p>
            </a>
            <a href="#" class="tarjeta-noticia card-un-clic">
                <img src="img/matriculacion.jpg" alt="Matriculación">
                <p>Matriculación 2024‑25</p>
            </a>
            <a href="#" class="tarjeta-noticia card-un-clic">
                <img src="img/becas.jpg" alt="Becas y ayudas">
                <p>Becas y ayudas</p>
            </a>
            <a href="#" class="tarjeta-noticia card-un-clic">
                <img src="img/calendario.jpg" alt="Calendario escolar">
                <p>Calendario escolar 2025‑2026</p>
            </a>
        </div>

        <h2 class="titulo-atajo" style="margin-top:3rem;">ÚLTIMAS NOTICIAS</h2>
        <div class="grid-noticias">
            <?php
            $sql = "SELECT * FROM noticias ORDER BY fecha DESC LIMIT 5";
            $resultado = $conexion->query($sql);
            if ($resultado && $resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
            ?>
                <div class="tarjeta-noticia noticia-item">
                    <?php if (!empty($fila["imagen"])): ?>
                        <img src="<?php echo htmlspecialchars($fila['imagen']); ?>" alt="Noticia">
                    <?php endif; ?>
                    <p class="fecha-noticia"><?php echo date("d/m/Y", strtotime($fila["fecha"])); ?></p>
                    <h4 class="titulo-noticia"><?php echo htmlspecialchars($fila["titulo"]); ?></h4>
                    <p class="contenido-noticia"><?php echo htmlspecialchars($fila["contenido"]); ?></p>
                    <a href="noticia.php?id=<?php echo $fila['id']; ?>" class="boton-leer-mas">Leer más</a>
                </div>
            <?php
                }
            } else {
                echo '<p>No hay noticias disponibles por el momento.</p>';
            }
            $conexion->close();
            ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
