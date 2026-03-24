<?php include 'head.php'; ?>  <!-- Llamada al head.php -->

<?php
include("conexion.php");  // Conecta a MySQLi

// Consulta último aviso activo + nombre usuario editor
$sql = "SELECT a.*, u.nombre as ultima_edicion_usuario_nombre 
        FROM ampa a 
        LEFT JOIN usuarios u ON a.ultima_edicion_usuario_id = u.id 
        WHERE a.activo = 1 
        ORDER BY a.fecha_actualizacion DESC 
        LIMIT 1";
$resultado = $conexion->query($sql);  // Ejecuta consulta
?>

<!-- HEADER AMPA -->
<section class="seccion-hero-universal">  
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-users" class="icono_universal"></i>  
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Avisos del AMPA</h1>
                <p class="hero-subtitulo-universal">Comunicaciones oficiales, inscripciones y actividades.</p>
            </div>
        </div>
    </div>
</section>

<main class="info_ampa_pagina">  <!-- Contenido principal -->
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="info_ampa_titulo">Último Aviso AMPA</h2>

            <?php if ($resultado && $resultado->num_rows > 0): ?>  <!-- En el caso de que haya avisos -->
                <?php $fila = $resultado->fetch_assoc();  // Obtiene datos
                $hay_media = !empty($fila['imagen']) || !empty($fila['enlace_video']);  // Detecta media ?>

                <?php if ($hay_media): ?>  <!-- Layout CON imagen/video -->
                    <div class="info_ampa_item info_ampa_con_media">
                        <div class="info_ampa_media_contenedor"> 
                            <?php if (!empty($fila['imagen'])): ?>  <!-- Imagen -->
                                <?php if (strpos($fila['imagen'], 'img/') === 0): ?>  <!-- Ruta normal -->
                                    <img src="<?php echo htmlspecialchars($fila['imagen']); ?>" 
                                         alt="AMPA" class="info_ampa_media_izquierda">
                                <?php else: ?>  <!-- Base64 -->
                                    <img src="data:<?php echo $fila['tipo_imagen']; ?>;base64,<?php echo base64_encode($fila['imagen']); ?>" 
                                         alt="AMPA" class="info_ampa_media_izquierda">
                                <?php endif; ?>
                            <?php elseif (!empty($fila['enlace_video'])): ?>  <!-- YouTube -->
                                <iframe src="<?php echo htmlspecialchars(str_replace('watch?v=', 'embed/', $fila['enlace_video'])); ?>" 
                                        frameborder="0" allowfullscreen class="info_ampa_media_izquierda_video"></iframe>
                            <?php endif; ?>
                        </div>
                        
                        <div class="info_ampa_contenido">  <!-- Texto derecha -->
                            <p class="info_ampa_fecha">  <!-- Fecha + editor -->
                                <?php echo date('d/m/Y', strtotime($fila['fecha_actualizacion'])); ?>
                                <?php if (!empty($fila['ultima_edicion_usuario_nombre'])): ?>
                                    <br><small class="letra-666"><?php echo htmlspecialchars($fila['ultima_edicion_usuario_nombre']); ?></small>
                                <?php endif; ?>
                            </p>
                            <h3 class="info_ampa_titulo_item"><?php echo htmlspecialchars($fila['titulo']); ?></h3>  <!-- Título -->
                            <p class="info_ampa_texto"><?php echo nl2br(htmlspecialchars($fila['texto'])); ?></p>  <!-- Texto -->
                            <?php if (!empty($fila['enlace_formulario'])): ?>  <!-- Enlace opcional -->
                                <a href="<?php echo htmlspecialchars($fila['enlace_formulario']); ?>" class="info_ampa_enlace" target="_blank">
                                    Formulario de inscripción
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>  <!-- Layout SIN media -->
                    <div class="info_ampa_item">
                        <div class="info_ampa_contenido">
                            <p class="info_ampa_fecha">
                                <?php echo date('d/m/Y', strtotime($fila['fecha_actualizacion'])); ?>
                                <?php if (!empty($fila['ultima_edicion_usuario_nombre'])): ?>
                                    <br><small class="letra-666"><?php echo htmlspecialchars($fila['ultima_edicion_usuario_nombre']); ?></small>
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
            <?php else: ?>  <!-- Sin avisos -->
                <div class="info_ampa_sin_contenido">
                    <i class="fas fa-info-circle"></i>  <!-- Icono info -->
                    <h3>No hay avisos AMPA activos</h3>
                    <p>El administrador aún no ha publicado comunicaciones.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
$conexion->close();  // Cierra BD
include 'footer.php';  // Llama al footer.php
?>
