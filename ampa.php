<?php 
    // Incluimos el archivo head con el HTML del <head> y el inicio de la página
    include_once 'head.php'; 
?>

<?php 
    // Incluimos el archivo de la conexión a la base de datos
    include("conexion.php"); 
?>
<main class="ampa-pagina">
    <!-- Sección de cabecera reutilizable para todas las páginas -->
    <section class="seccion-hero-universal">
        <div class="contenedor-max">
            <div class="hero-layout-universal">
                <div class="hero-icono-universal">
                    <!-- Icono de fontawesome para representar al AMPA -->
                    <i class="fas fa-users" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
                </div>
                <div class="hero-texto-universal">
                    <!-- Título principal de la página -->
                    <h1 class="hero-titulo-universal">AMPA</h1>
                    <!-- Subtítulo explicando qué es el AMPA -->
                    <p class="hero-subtitulo-universal">Asociación de Madres y Padres del Alumnado</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección donde va el contenido que viene de la base de datos -->
    <section class="seccion-contenido">
        <div class="ampa-contenedor-max">
            <?php
            // Consulta en la que sacamos solo el registro con id = 1 de la tabla ampa
            $sql = "SELECT titulo, texto, imagen, tipo_imagen, enlace_formulario, enlace_video FROM ampa WHERE id = 1";
            
            // Ejecutamos la consulta usando el objeto $conexion
            $resultado = $conexion->query($sql);

            // Comprobamos que la consulta devolvió algo y obtenemos la primera fila
            if ($resultado && $fila = $resultado->fetch_assoc()) { 
                // Variable para saber si hay imagen o vídeo
                $hay_media = !empty($fila['imagen']) || !empty($fila['enlace_video']);
                ?>
                <?php if ($hay_media): ?>
                    <!-- Si hay imagen o vídeo, usamos un layout con 2 columnas -->
                    <article class="ampa-media-layout">
                        <div class="ampa-media-contenedor">
                            <?php if (!empty($fila['imagen'])): ?>
                                <!-- Mostramos la imagen que viene de la BD en binario, codificada en base64 -->
                                <img src="data:<?php echo $fila['tipo_imagen']; ?>;base64,<?php echo base64_encode($fila['imagen']); ?>" 
                                     alt="AMPA" class="ampa-media-imagen">
                            <?php elseif (!empty($fila['enlace_video'])): ?>
                                <!-- Si no hay imagen pero sí enlace de vídeo, incrustamos el vídeo (por ejemplo de YouTube) -->
                                <iframe src="<?php echo htmlspecialchars(str_replace('watch?v=', 'embed/', $fila['enlace_video'])); ?>" 
                                        frameborder="0" allowfullscreen class="ampa-media-video"></iframe>
                            <?php endif; ?>
                        </div>
                        <div class="texto-contenido-layout">
                            <!-- Título que viene de la base de datos, escapado por seguridad -->
                            <h2 class="ampa-titulo-principal"><?php echo htmlspecialchars($fila['titulo']); ?></h2>
                            <!-- Texto completo, respetando saltos de línea con nl2br -->
                            <div class="ampa-texto-completo"><?php echo nl2br(htmlspecialchars($fila['texto'])); ?></div>
                            <?php if (!empty($fila['enlace_formulario'])): ?>
                                <!-- Si existe un enlace a formulario, mostramos un botón que abre en pestaña nueva -->
                                <a href="<?php echo htmlspecialchars($fila['enlace_formulario']); ?>" target="_blank" 
                                   class="ampa-boton-primario">📋 Formulario de inscripción</a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php else: ?>
                    <!-- Si no hay ni imagen ni vídeo, solo mostramos el texto centrado -->
                    <article class="ampa-texto-centrado">
                        <!-- Título desde la BD -->
                        <h2 class="ampa-titulo-principal"><?php echo htmlspecialchars($fila['titulo']); ?></h2>
                        <!-- Texto desde la BD, también escapado y con saltos de línea -->
                        <div class="ampa-texto-completo"><?php echo nl2br(htmlspecialchars($fila['texto'])); ?></div>
                        <?php if (!empty($fila['enlace_formulario'])): ?>
                            <!-- Botón centrado para el formulario, si existe el enlace -->
                            <div class="ampa-boton-centrado">
                                <a href="<?php echo htmlspecialchars($fila['enlace_formulario']); ?>" target="_blank" 
                                   class="ampa-boton-primario">📋 Formulario de inscripción</a>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endif; ?>
            <?php } else { ?>
                <!-- Mensaje sencillo cuando no hay datos en la tabla o la consulta falla -->
                <div class="ampa-vacio">
                    <i class="fas fa-info-circle" style="font-size: 3rem; color: var(--gris-medio);"></i>
                    <p>No hay datos disponibles.</p>
                </div>
            <?php } ?>
        </div>
    </section>
</main>

<?php 
// Cerramos la conexión con la base de datos
$conexion->close(); 

// Incluimos el footer de la página
include 'footer.php'; 
?>
