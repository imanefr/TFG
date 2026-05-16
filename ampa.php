<?php include 'head.php'; ?>  <!-- Incluye el header HTML/CSS/JS -->

<?php
include("conexion.php");  // Conecta a la base de datos MySQL

// Consulta el último aviso activo y el nombre del usuario que lo editó
$sql = "SELECT a.* 
        FROM ampa a 
        WHERE a.activo = 1 
        ORDER BY a.fecha_actualizacion DESC 
        LIMIT 1";
$resultado = $conexion->query($sql);  // Ejecuta la consulta SQL
?>

<!-- HEADER AMPA - Sección hero principal -->
<section class="seccion-hero-universal">  
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-users" class="icono_universal"></i>  <!-- Icono de usuarios -->
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Avisos del AMPA</h1>
                <p class="hero-subtitulo-universal">Comunicaciones oficiales, inscripciones y actividades.</p>
            </div>
        </div>
    </div>
</section>

<main class="info_ampa_pagina">  <!-- Contenedor principal de contenido -->
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="info_ampa_titulo">Último Aviso AMPA</h2>

            <?php if ($resultado && $resultado->num_rows > 0): ?>  <!-- Verifica si hay avisos -->
                <?php $fila = $resultado->fetch_assoc();  // Obtiene la primera fila de resultados
                $hay_media = !empty($fila['imagen']) || !empty($fila['enlace_video']);  // Detecta si hay imagen o video ?>

                <?php if ($hay_media): ?>  <!-- Muestra layout con media (imagen/video) -->
                    <div class="info_ampa_item info_ampa_con_media">
                        <div class="info_ampa_media_contenedor">  <!-- Contenedor de media izquierda -->
                            <?php if (!empty($fila['imagen'])): ?>  <!-- Si existe imagen -->
                                <?php if (strpos($fila['imagen'], 'img/') === 0): ?>  <!-- Imagen con ruta de archivo -->
                                    <img src="<?php echo htmlspecialchars($fila['imagen']); ?>" 
                                         alt="AMPA" class="info_ampa_media_izquierda">
                                <?php else: ?>  <!-- Imagen en base64 -->
                                    <img src="data:<?php echo $fila['tipo_imagen']; ?>;base64,<?php echo base64_encode($fila['imagen']); ?>" 
                                         alt="AMPA" class="info_ampa_media_izquierda">
                                <?php endif; ?>
                            <?php elseif (!empty($fila['enlace_video'])): ?>  <!-- Video de YouTube -->
                                <iframe src="<?php echo htmlspecialchars(str_replace('watch?v=', 'embed/', $fila['enlace_video'])); ?>" 
                                        frameborder="0" allowfullscreen class="info_ampa_media_izquierda_video"></iframe>
                            <?php endif; ?>
                        </div>
                        
                        <div class="info_ampa_contenido">  <!-- Contenido de texto (derecha) -->
                            <p class="info_ampa_fecha">  <!-- Fecha y nombre del editor -->
                                <?php echo date('d/m/Y', strtotime($fila['fecha_actualizacion'])); ?>
                                <?php if (!empty($fila['ultima_edicion_nombre'])): ?>
                                    <br><small class="letra-666"><?php echo htmlspecialchars($fila['ultima_edicion_nombre']); ?></small>
                                <?php endif; ?>
                            </p>
                            <h3 class="info_ampa_titulo_item"><?php echo htmlspecialchars($fila['titulo']); ?></h3>  <!-- Título del aviso -->
                            <p class="info_ampa_texto"><?php echo nl2br(htmlspecialchars($fila['texto'])); ?></p>  <!-- Texto con saltos de línea -->
                            <?php if (!empty($fila['enlace_formulario'])): ?>  <!-- Enlace a formulario si existe -->
                                <a href="<?php echo htmlspecialchars($fila['enlace_formulario']); ?>" class="info_ampa_enlace" target="_blank">
                                    Formulario de inscripción
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>  <!-- Layout sin media (solo texto centrado) -->
                    <div class="info_ampa_item">
                        <div class="info_ampa_contenido">
                            <p class="info_ampa_fecha">
                                <?php echo date('d/m/Y', strtotime($fila['fecha_actualizacion'])); ?>
                                <?php if (!empty($fila['ultima_edicion_nombre'])): ?>
                                    <br><small class="letra-666"><?php echo htmlspecialchars($fila['ultima_edicion_nombre']); ?></small>
                                <?php endif; ?>
                            </p>
                            <h3 class="info_ampa_titulo_item"><?php echo htmlspecialchars($fila['titulo']); ?></h3>
                            <p class="info_ampa_texto"><?php echo nl2br(htmlspecialchars($fila['texto'])); ?></p>
                            <?php if (!empty($fila['enlace_formulario'])): ?>
                                <a href="<?php echo htmlspecialchars($fila['enlace_formulario']); ?>" class="info_ampa_enlace" target="_blank">
                                    Formulario de inscripción
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>  <!-- Mensaje cuando no hay avisos activos -->
                <div class="info_ampa_sin_contenido">
                    <i class="fas fa-info-circle"></i>  <!-- Icono de información -->
                    <h3>No hay avisos AMPA activos</h3>
                    <p>El administrador aún no ha publicado comunicaciones.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
$conexion->close();  // Cierra la conexión a la base de datos
include 'footer.php';  // Incluye el footer HTML
?>