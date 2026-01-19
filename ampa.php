<?php include 'head.php'; ?>

<?php include("conexion.php"); ?>
<main>
    <section class="seccion-contenido">
        <h2 class="seccion-contenido-h2">AMPA</h2>
        <div class="contenedor-max">
            <?php
            $sql = "SELECT titulo, texto, imagen, tipo_imagen, enlace_formulario, enlace_video FROM ampa WHERE id = 1";
            $resultado = $conexion->query($sql);

            if ($resultado && $fila = $resultado->fetch_assoc()) {
                $hay_media = !empty($fila['imagen']) || !empty($fila['enlace_video']);
                ?>
                <?php if ($hay_media): ?>
                    <!-- ✅ HAY IMAGEN O VIDEO: 2 columnas -->
                    <div style="display: flex; gap: 2rem; padding: 2rem; background: #f8f9fa; border-radius: 10px; margin: 2rem 0;">
                        <!-- COLUMNA MEDIA (izquierda) -->
                        <div style="flex: 0 0 300px; text-align: center;">
                            <?php if (!empty($fila['imagen'])): ?>
                                <!-- MOSTRAR IMAGEN -->
                                <img src="data:<?php echo $fila['tipo_imagen']; ?>;base64,<?php echo base64_encode($fila['imagen']); ?>" 
                                     alt="AMPA" style="max-width: 100%; height: auto; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                             <?php elseif (!empty($fila['enlace_video'])): ?>
                                <!-- MOSTRAR VIDEO -->
                                <iframe width="100%" height="200" 
                                        src="<?php echo htmlspecialchars(str_replace('watch?v=', 'embed/', $fila['enlace_video'])); ?>" 
                                        frameborder="0" allowfullscreen 
                                        style="border-radius: 10px;"></iframe>
                            <?php endif; ?>
                        </div>

                        <!-- COLUMNA TEXTO (derecha) -->
                        <div style="flex: 1;">
                            <h2 style="color: #2c3e50; margin-bottom: 1rem;"><?php echo htmlspecialchars($fila['titulo']); ?></h2>
                            <p style="line-height: 1.6; margin-bottom: 1rem;"><?php echo nl2br(htmlspecialchars($fila['texto'])); ?></p>

                            <?php if (!empty($fila['enlace_formulario'])): ?>
                                <p style="margin-bottom: 1rem;">
                                    <a href="<?php echo htmlspecialchars($fila['enlace_formulario']); ?>" target="_blank" 
                                       style="background: #28a745; color: white; padding: 0.7rem 1.5rem; text-decoration: none; border-radius: 5px; display: inline-block;">
                                        📋 Formulario de inscripción
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- ❌ SIN IMAGEN NI VIDEO: texto ocupa TODO -->
                    <div style="padding: 2rem; background: #f8f9fa; border-radius: 10px; margin: 2rem 0; max-width: 800px; margin-left: auto; margin-right: auto;">
                        <h2 style="color: #2c3e50; margin-bottom: 1rem; text-align: center;"><?php echo htmlspecialchars($fila['titulo']); ?></h2>
                        <div style="line-height: 1.7; text-align: justify;"><?php echo nl2br(htmlspecialchars($fila['texto'])); ?></div>

                        <?php if (!empty($fila['enlace_formulario'])): ?>
                            <div style="text-align: center; margin-top: 1.5rem;">
                                <a href="<?php echo htmlspecialchars($fila['enlace_formulario']); ?>" target="_blank" 
                                   style="background: #28a745; color: white; padding: 0.8rem 2rem; text-decoration: none; border-radius: 5px; display: inline-block; font-size: 1.1rem;">
                                    📋 Formulario de inscripción
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php } else { ?>
                <div style="text-align: center; padding: 3rem; color: #666;">
                    <p>No hay datos AMPA disponibles.</p>
                </div>
            <?php } ?>
        </div>
    </section>
</main>

<?php 
$conexion->close(); 
include 'footer.php'; 
?>
