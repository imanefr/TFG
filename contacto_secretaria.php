<?php include 'conexion.php'; ?>

<?php
// Obtener datos de la tabla contacto_secretaria
$sql = "SELECT telefono, fax, horario, correo, aviso FROM contacto_secretaria LIMIT 1";
$resultado = $conexion->query($sql);
$datos_contacto = $resultado->fetch_assoc();
?>

<?php include 'head.php'; ?>

<!-- Hero principal -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-phone" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Contacto secretaría</h1>
                <p class="hero-subtitulo-universal">Información y atención al público</p>
            </div>
        </div>
    </div>
</section>

<!-- Contenido principal -->
<main class="info_contacto_pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="info_contacto_titulo">Para contactar con secretaría</h2>

            <div class="info_contacto_lista">
                <div class="info_contacto_item">
                    <i class="fas fa-phone"></i>
                    <strong>Teléfono:</strong> <?php echo htmlspecialchars($datos_contacto['telefono']); ?>
                </div>

                <div class="info_contacto_item">
                    <i class="fas fa-fax"></i>
                    <strong>Fax:</strong> <?php echo htmlspecialchars($datos_contacto['fax']); ?>
                </div>

                <div class="info_contacto_item">
                    <i class="fas fa-clock"></i>
                    <strong>Horario:</strong> <?php echo htmlspecialchars($datos_contacto['horario']); ?>
                </div>

                <div class="info_contacto_item">
                    <i class="fas fa-envelope"></i>
                    <strong>Correo:</strong> 
                    <a href="mailto:<?php echo htmlspecialchars($datos_contacto['correo']); ?>" class="info_contacto_email">
                        <?php echo htmlspecialchars($datos_contacto['correo']); ?>
                    </a>
                </div>
            </div>

            <div class="info_contacto_aviso">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>AVISO IMPORTANTE:</strong> <?php echo htmlspecialchars($datos_contacto['aviso']); ?>
            </div>
        </div>
    </section>
</main>

<?php $conexion->close(); ?>

<?php include 'footer.php'; ?>
