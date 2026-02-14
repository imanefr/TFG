<?php include 'head.php'; ?> <!-- Cabecera -->
<?php include("conexion.php"); ?> <!-- Conexión BD -->

<!-- Contenido principal -->
<main>
    <!-- Hero -->
    <section class="seccion-hero-universal">
        <div class="contenedor-max">
            <div class="hero-layout-universal">
                <div class="hero-icono-universal">
                    <i class="fas fa-file-pdf" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
                </div>
                <div class="hero-texto-universal">
                    <h1 class="hero-titulo-universal">Convalidación ESO</h1>
                    <p class="hero-subtitulo-universal">Información y atención al público</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Sección de contenido -->
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <div class="convalidacion-contenido">
                <?php
                // Consulta convalidación activa de ESO
                $sql = "SELECT titulo, texto AS descripcion, enlace_normativa, enlace_formulario 
                        FROM convalidaciones 
                        WHERE tipo = 'ESO' AND activo = 1 LIMIT 1";
                $resultado = $conexion->query($sql);

                // Si hay resultados
                if ($resultado && $fila = $resultado->fetch_assoc()) {
                ?>
                <!-- Convalidación encontrada -->
                <div class="convalidacion-simple">
                    <div class="aviso-convalidacion">
                        <div class="aviso-icono">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="aviso-texto">
                            <p><?php echo htmlspecialchars($fila['descripcion']); ?></p>
                        </div>
                    </div>

                    <!-- Enlaces a normativa y formulario -->
                    <div class="enlaces-convalidacion">
                        <?php if (!empty($fila['enlace_normativa'])): ?>
                        <a href="<?php echo htmlspecialchars($fila['enlace_normativa']); ?>" target="_blank" class="btn-normativa">
                            <i class="fas fa-file-pdf"></i>
                            <span>Normativa oficial</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($fila['enlace_formulario'])): ?>
                        <a href="<?php echo htmlspecialchars($fila['enlace_formulario']); ?>" target="_blank" class="btn-formulario">
                            <i class="fas fa-edit"></i>
                            <span>Formulario solicitud</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php 
                } else { 
                ?>
                <!-- Sin resultados -->
                <div class="convalidacion-simple">
                    <div class="aviso-convalidacion">
                        <div class="aviso-icono"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="aviso-texto">
                            <p><strong>No hay información disponible</strong><br>Contacta con secretaría</p>
                        </div>
                    </div>
                </div>
                <?php 
                }
                // Cerrar conexión
                $conexion->close();
                ?>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?> <!-- Pie de página -->
