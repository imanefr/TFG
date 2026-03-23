<?php include 'head.php'; ?>

<?php
include("conexion.php");

$sql = "SELECT a.*, u.nombre as ultima_edicion_usuario_nombre 
        FROM ampa a 
        LEFT JOIN usuarios u ON a.ultima_edicion_usuario_id = u.id 
        WHERE a.activo = 1 
        ORDER BY a.fecha_actualizacion DESC 
        LIMIT 1";
$resultado = $conexion->query($sql);
?>

<!-- HEADER AMPA -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-users" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Avisos del AMPA</h1>
                <p class="hero-subtitulo-universal">Comunicaciones oficiales, inscripciones y actividades.</p>
            </div>
        </div>
    </div>
</section>

<main class="info_ampa_pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="info_ampa_titulo">Último Aviso AMPA</h2>

            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php $fila = $resultado->fetch_assoc(); 
                $hay_media = !empty($fila['imagen']) || !empty($fila['enlace_video']); ?>
                
                <?php if ($hay_media): ?>
                    <!-- ✅ CON FOTO/VIDEO A LA IZQUIERDA -->
                    <div class="info_ampa_item info_ampa_con_media">
                        <div class="info_ampa_media_contenedor">
                            <?php if (!empty($fila['imagen'])): ?>
                                <?php if (strpos($fila['imagen'], 'img/') === 0): ?>
                                    <img src="<?php echo htmlspecialchars($fila['imagen']); ?>" 
                                         alt="AMPA" class="info_ampa_media_izquierda">
                                <?php else: ?>
                                    <img src="data:<?php echo $fila['tipo_imagen']; ?>;base64,<?php echo base64_encode($fila['imagen']); ?>" 
                                         alt="AMPA" class="info_ampa_media_izquierda">
                                <?php endif; ?>
                            <?php elseif (!empty($fila['enlace_video'])): ?>
                                <iframe src="<?php echo htmlspecialchars(str_replace('watch?v=', 'embed/', $fila['enlace_video'])); ?>" 
                                        frameborder="0" allowfullscreen class="info_ampa_media_izquierda_video"></iframe>
                            <?php endif; ?>
                        </div>
                        
                        <div class="info_ampa_contenido">
                            <!-- ✅ FECHA + USUARIO ARRIBA -->
                            <p class="info_ampa_fecha">
                                <?php echo date('d/m/Y', strtotime($fila['fecha_actualizacion'])); ?>
                                <?php if (!empty($fila['ultima_edicion_usuario_nombre'])): ?>
                                    <br><small style="color: #666;"><?php echo htmlspecialchars($fila['ultima_edicion_usuario_nombre']); ?></small>
                                <?php endif; ?>
                            </p>

                            <!-- ✅ TÍTULO + TEXTO -->
                            <h3 class="info_ampa_titulo_item"><?php echo htmlspecialchars($fila['titulo']); ?></h3>
                            <p class="info_ampa_texto"><?php echo nl2br(htmlspecialchars($fila['texto'])); ?></p>

                            <!-- ✅ BOTONES -->
                            <?php if (!empty($fila['enlace_formulario'])): ?>
                                <a href="<?php echo htmlspecialchars($fila['enlace_formulario']); ?>" class="info_ampa_enlace" target="_blank">
                                    📋 Formulario de inscripción →
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- ✅ SIN MEDIA -->
                    <div class="info_ampa_item">
                        <div class="info_ampa_contenido">
                            <p class="info_ampa_fecha">
                                <?php echo date('d/m/Y', strtotime($fila['fecha_actualizacion'])); ?>
                                <?php if (!empty($fila['ultima_edicion_usuario_nombre'])): ?>
                                    <br><small style="color: #666;"><?php echo htmlspecialchars($fila['ultima_edicion_usuario_nombre']); ?></small>
                                <?php endif; ?>
                            </p>
                            <h3 class="info_ampa_titulo_item"><?php echo htmlspecialchars($fila['titulo']); ?></h3>
                            <p class="info_ampa_texto"><?php echo nl2br(htmlspecialchars($fila['texto'])); ?></p>
                            <?php if (!empty($fila['enlace_formulario'])): ?>
                                <a href="<?php echo htmlspecialchars($fila['enlace_formulario']); ?>" class="info_ampa_enlace" target="_blank">
                                    📋 Formulario de inscripción →
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="info_ampa_sin_contenido">
                    <i class="fas fa-info-circle"></i>
                    <h3>No hay avisos AMPA activos</h3>
                    <p>El administrador aún no ha publicado comunicaciones.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
$conexion->close();
include 'footer.php';
?>
