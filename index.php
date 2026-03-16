<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php include_once 'head.php'; ?>

<!-- CONTENIDO ESPECÍFICO DE INDEX -->
<section id="inicio" class="indice_pagina_hero_carrusel">
    <div class="indice_pagina_contenedor_carrusel">
        <div class="indice_pagina_imagen_carrusel indice_pagina_activa" style="background-image: url('img/instituto_back_1.jpg');"></div>
        <div class="indice_pagina_imagen_carrusel" style="background-image: url('img/instituto_back_2.jpg');"></div>
        <div class="indice_pagina_imagen_carrusel" style="background-image: url('img/instituto_back_3.jpg');"></div>
    </div>
    <div class="indice_pagina_contenido_hero">
        <h2 class="indice_pagina_titulo_hero">
            <span class="indice_pagina_texto_hero_superior">BIENVENIDOS A NUESTRO CENTRO</span>
            <span class="indice_pagina_texto_hero_principal">IES LA ARBOLEDA</span>
            <span class="indice_pagina_texto_hero_inferior">Alcorcón - Tu instituto de referencia</span>
        </h2>
    </div>
</section>

<section class="indice_pagina_seccion_atajo">
    <div class="indice_pagina_contenedor_principal">
        <h3 class="indice_pagina_titulo_atajo">A UN CLIC</h3>
        <div class="indice_pagina_grid_atajos">
            <a href="https://aulavirtual33.educa.madrid.org/ies.laarboleda.alcorcon/" class="indice_pagina_tarjeta_atajo card-un-clic">
                <div class="indice_pagina_icono_atajo"><i class="fas fa-graduation-cap"></i></div>
                <h4 class="indice_pagina_titulo_atajo_card">Aula Virtual</h4>
            </a>
            <a href="https://correoweb.educa.madrid.org/" class="indice_pagina_tarjeta_atajo card-un-clic">
                <div class="indice_pagina_icono_atajo"><i class="fas fa-envelope"></i></div>
                <h4 class="indice_pagina_titulo_atajo_card">Correo educamadrid</h4>
            </a>
            <a href="https://raices.madrid.org/" class="indice_pagina_tarjeta_atajo card-un-clic">
                <div class="indice_pagina_icono_atajo"><i class="fas fa-tree"></i></div>
                <h4 class="indice_pagina_titulo_atajo_card">Roble/Raíces</h4>
            </a>
        </div>
    </div>
</section>

<main>
    <?php include("conexion.php"); ?>
    <section class="indice_pagina_contenedor_principal">
        <a href="relevante_ahora.php" style="text-decoration: none;"><h2 class="indice_pagina_titulo_atajo">RELEVANTE AHORA</h2></a>
        <?php
            function primeras15Palabras($texto) {
                $palabras = explode(' ', strip_tags($texto));
                $primeras15 = array_slice($palabras, 0, 15);
                return implode(' ', $primeras15) . '...';
            }

            $sql = "SELECT r.*, u.nombre as ultima_edicion_usuario_nombre
                    FROM noticias r 
                    LEFT JOIN usuarios u ON r.ultima_edicion_usuario_id = u.id 
                    WHERE r.destacada = 1 
                    ORDER BY r.fecha DESC LIMIT 8";

            $resultado = $conexion->query($sql);
            $noticias = [];
            while ($fila = $resultado->fetch_assoc()) {
                $noticias[] = $fila;
            }
        ?>
        <div class="indice_pagina_grid_noticias">
            <?php foreach ($noticias as $noticia): ?>
                <a href="noticias_relevantes.php?id=<?php echo $noticia['id']; ?>" class="indice_pagina_tarjeta_noticia card-un-clic">
                    <img src="<?php echo $noticia['imagen']; ?>" alt="<?php echo $noticia['titulo']; ?>">
                    <p><?php echo primeras15Palabras($noticia['contenido']); ?></p>
                </a>
            <?php endforeach; ?>
        </div>

        <a href="ultimas_noticias.php" style="text-decoration: none;"><h2 class="indice_pagina_titulo_atajo" style="margin-top:3rem;">ÚLTIMAS NOTICIAS</h2></a>
        <div class="indice_pagina_grid_noticias">
            <?php
            $sql = "SELECT * FROM noticias ORDER BY fecha DESC LIMIT 5";
            $resultado = $conexion->query($sql);
            if ($resultado && $resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
            ?>
                <div class="indice_pagina_tarjeta_noticia noticia-item">
                    <?php if (!empty($fila["imagen"])): ?>
                        <img src="<?php echo htmlspecialchars($fila['imagen']); ?>" alt="Noticia">
                    <?php endif; ?>
                    <p class="indice_pagina_fecha_noticia"><?php echo date("d/m/Y", strtotime($fila["fecha"])); ?></p>
                    <h4 class="indice_pagina_titulo_noticia"><?php echo htmlspecialchars($fila["titulo"]); ?></h4>
                    <p class="indice_pagina_contenido_noticia"><?php echo htmlspecialchars($fila["contenido"]); ?></p>
                    <a href="noticia.php?id=<?php echo $fila['id']; ?>" class="indice_pagina_boton_leer_mas">Leer más</a>
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
