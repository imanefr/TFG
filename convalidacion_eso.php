<?php include 'head.php'; ?>                    <!-- Incluye el head.php -->

<?php include("conexion.php"); ?>               <!-- Carga conexión MySQLi -->

<!-- HEADER CONVALIDACION ESO -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-file-pdf icono_universal"></i> 
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Convalidación ESO</h1> <!-- Título principal -->
                <p class="hero-subtitulo-universal">Información y atención al público</p> <!-- Subtítulo -->
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL -->
<main class="convalidacion_pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <div class="convalidacion_simple"> <!-- Contenedor layout simple -->
                <?php
                // Consulta convalidación ESO activa (solo 1 registro)
                $sql = "SELECT titulo, texto AS descripcion, enlace_normativa, enlace_formulario 
                        FROM convalidaciones WHERE tipo = 'ESO' AND activo = 1 LIMIT 1";
                $resultado = $conexion->query($sql);    // Ejecuta consulta

                if ($resultado && $fila = $resultado->fetch_assoc()) { // Si hay datos
                ?>
                    <!-- AVISO CONVALIDACIÓN -->
                    <div class="convalidacion_aviso">
                        <div class="convalidacion_icono"><i class="fas fa-exclamation-triangle"></i></div> <!-- Icono aviso -->
                        <div class="convalidacion_texto">
                            <p><?php echo htmlspecialchars($fila['descripcion']); ?></p> <!-- Texto escapado -->
                        </div>
                    </div>
                    
                    <!-- ENLACES DESCARGAS -->
                    <div class="convalidacion_enlaces">
                        <?php if (!empty($fila['enlace_normativa'])): ?> <!-- Normativa oficial -->
                            <a href="<?php echo htmlspecialchars($fila['enlace_normativa']); ?>" target="_blank" class="convalidacion_btn_normativa">
                                <i class="fas fa-file-pdf"></i><span>Normativa oficial</span>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($fila['enlace_formulario'])): ?> <!-- Formulario solicitud -->
                            <a href="<?php echo htmlspecialchars($fila['enlace_formulario']); ?>" target="_blank" class="convalidacion_btn_formulario">
                                <i class="fas fa-edit"></i><span>Formulario solicitud</span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php } else { ?> <!-- Sin datos disponibles -->
                    <div class="convalidacion_aviso">
                        <div class="convalidacion_icono"><i class="fas fa-exclamation-triangle"></i></div> <!-- Icono aviso -->
                        <div class="convalidacion_texto">
                            <p><strong>No hay información disponible</strong><br>Contacta con secretaría</p> <!-- Mensaje por defecto -->
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</main>

<?php $conexion->close(); ?>                    <!-- Cierra conexión BD -->
<?php include 'footer.php'; ?>                  <!-- Llama la footer.php -->
