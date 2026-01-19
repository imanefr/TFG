<?php include 'head.php'; ?>

<!-- CONTENIDO ESPECÍFICO DE INDEX -->
<section id="inicio" class="hero-carrusel">
    <div class="carrusel-imagenes">
        <div class="carrusel-imagen activa" style="background-image: url('img/instituto_back_1.jpg');"></div>
        <div class="carrusel-imagen" style="background-image: url('img/instituto_back_2.jpg');"></div>
        <div class="carrusel-imagen" style="background-image: url('img/instituto_back_3.jpg');"></div>
    </div>
    <div class="hero-centro">
        <h2 class="titulo-hero">
            <span class="texto-superior">BIENVENIDOS A NUESTRO CENTRO</span>
            <span class="texto-medio">IES LA ARBOLEDA</span>
            <span class="texto-inferior">Alcorcón - Tu instituto de referencia</span>
        </h2>
    </div>
    <div class="carrusel-indicadores">
        <span class="indicador activo" data-slide="0"></span>
        <span class="indicador" data-slide="1"></span>
        <span class="indicador" data-slide="2"></span>
    </div>
</section>

<section class="seccion-un-clic">
    <div class="contenedor-max">
        <h3 class="titulo-un-clic">A UN CLIC</h3>
        <div class="grid-un-clic">
            <a href="https://aulavirtual33.educa.madrid.org/ies.laarboleda.alcorcon/" class="card-un-clic">
                <div class="icono-un-clic"><i class="fas fa-graduation-cap"></i></div>
                <h4>Aula Virtual</h4>
            </a>
            <a href="https://correoweb.educa.madrid.org/" class="card-un-clic">
                <div class="icono-un-clic"><i class="fas fa-envelope"></i></div>
                <h4>Correo educamadrid</h4>
            </a>
            <a href="https://raices.madrid.org/" class="card-un-clic">
                <div class="icono-un-clic"><i class="fas fa-tree"></i></div>
                <h4>Roble/Raíces</h4>
            </a>
        </div>
    </div>
</section>

<main>
    <?php include("conexion.php"); ?>
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="titulo-un-clic">RELEVANTE AHORA</h2>
            <div class="grid-un-clic">
                <a href="#" class="card-un-clic">
                    <img src="img/libros_texto.jpg" alt="Libros de texto">
                    <p>Libros de texto 2025‑26</p>
                </a>
                <a href="#" class="card-un-clic">
                    <img src="img/matriculacion.jpg" alt="Matriculación">
                    <p>Matriculación 2024‑25</p>
                </a>
                <a href="#" class="card-un-clic">
                    <img src="img/becas.jpg" alt="Becas y ayudas">
                    <p>Becas y ayudas</p>
                </a>
                <a href="#" class="card-un-clic">
                    <img src="img/calendario.jpg" alt="Calendario escolar">
                    <p>Calendario escolar 2025‑2026</p>
                </a>
            </div>

            <h2 class="titulo-un-clic" style="margin-top:3rem;">ÚLTIMAS NOTICIAS</h2>
            <div class="grid-noticias">
                <?php
// ✅ AÑADIDO: Define la consulta SQL
                $sql = "SELECT * FROM noticias ORDER BY fecha DESC LIMIT 5";
                $resultado = $conexion->query($sql);
                if ($resultado && $resultado->num_rows > 0) {
                    while ($fila = $resultado->fetch_assoc()) {
                        ?>
                        <div class="noticia-item">
                            <?php if (!empty($fila["imagen"])): ?>
                                <img src="<?php echo htmlspecialchars($fila['imagen']); ?>" alt="Noticia">
                            <?php endif; ?>
                            <p class="fecha"><?php echo date("d/m/Y", strtotime($fila["fecha"])); ?></p>
                            <h4 class="titulo"><?php echo htmlspecialchars($fila["titulo"]); ?></h4>
                            <p><?php echo htmlspecialchars($fila["contenido"]); ?></p>
                            <a href="noticia.php?id=<?php echo $fila['id']; ?>" class="leer-mas">Leer más</a>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>No hay noticias disponibles por el momento.</p>';
                }
                $conexion->close();
                ?>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
