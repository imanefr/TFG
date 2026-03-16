<?php include 'head.php'; ?>
<?php include("conexion.php"); ?>

<!-- Hero -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-briefcase" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Convalidación FP</h1>
                <p class="hero-subtitulo-universal">Información y atención al público</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO -->
<main class="convalidacion_pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <div class="convalidacion_simple">
                <?php
                $sql = "SELECT titulo, texto AS descripcion, enlace_normativa, enlace_formulario 
                        FROM convalidaciones WHERE tipo = 'FP' AND activo = 1 LIMIT 1";
                $resultado = $conexion->query($sql);

                if ($resultado && $fila = $resultado->fetch_assoc()) {
                ?>
                    <div class="convalidacion_aviso">
                        <div class="convalidacion_icono"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="convalidacion_texto">
                            <p><?php echo htmlspecialchars($fila['descripcion']); ?></p>
                        </div>
                    </div>
                    <div class="convalidacion_enlaces">
                        <?php if (!empty($fila['enlace_normativa'])): ?>
                        <a href="<?php echo htmlspecialchars($fila['enlace_normativa']); ?>" target="_blank" class="convalidacion_btn_normativa">
                            <i class="fas fa-file-pdf"></i><span>Normativa oficial</span>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($fila['enlace_formulario'])): ?>
                        <a href="<?php echo htmlspecialchars($fila['enlace_formulario']); ?>" target="_blank" class="convalidacion_btn_formulario">
                            <i class="fas fa-edit"></i><span>Formulario solicitud</span>
                        </a>
                        <?php endif; ?>
                    </div>
                <?php } else { ?>
                    <div class="convalidacion_aviso">
                        <div class="convalidacion_icono"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="convalidacion_texto">
                            <p><strong>No hay información disponible</strong><br>Contacta con secretaría</p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</main>

<?php $conexion->close(); include 'footer.php'; ?>
