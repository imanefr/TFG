<?php
include("conexion.php");

// Consulta info_bachillerato desde BD
$sql = "SELECT * FROM info_bachillerato WHERE activo = 1 ORDER BY id";
$resultado = $conexion->query($sql);
$modalidades = [];
while ($fila = $resultado->fetch_assoc()) {
    $modalidades[] = $fila;
}
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'head.php'; ?>
   
</head>

<body>
    <!-- HERO HEADER BACHILLERATO -->
    <section class="seccion-hero-universal">
        <div class="contenedor-max">
            <div class="hero-layout-universal">
                <div class="hero-icono-universal">
                    <i class="fas fa-graduation-cap icono_universal"></i>
                </div>
                <div class="hero-texto-universal">
                    <h1 class="hero-titulo-universal">Bachillerato</h1>
                    <p class="hero-subtitulo-universal">Preparación para el futuro universitario y profesional</p>
                </div>
            </div>
        </div>
    </section>

     <!-- SECCIÓN INTRO BACHILLERATO - Texto explicativo fijo -->
    <section class="bach-contenido">
        <div class="contenedor-max">
            <div>  
                <h2>Modelo Organizativo</h2>  
                <p>
                    El Bachillerato comprende dos cursos y se organiza en modalidades diferentes para ofrecer una 
                    preparación especializada acorde con sus intereses.
                </p>
                <!-- ENLACE EXTERNO - Comunidad Madrid oficial -->
                <a href="https://www.comunidad.madrid/servicios/educacion/bachillerato" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    Información oficial Comunidad de Madrid
                </a>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENIDO - Modalidades dinámicas desde BD -->
    <main class="info_bachillerato_pagina">
        <!-- LOOP MODALIDADES - Itera cada registro BD -->
        <?php foreach ($modalidades as $modalidad): ?>
            
            <!-- MODALIDAD CIENCIAS - Condicional específica por nombre -->
            <?php if ($modalidad['nombre'] == 'Modalidad de Ciencias'): ?>
                <section class="seccion-contenido">  <!-- Sección individual -->
                    <div class="contenedor-max">
                        <!-- TÍTULO MODALIDAD -->
                        <h2 class="info_bachillerato_titulo">Modalidad de Ciencias</h2>
                        <div>  <!-- Contenido modalidad -->
                            <!-- DESCRIPCIÓN - Si existe en BD -->
                            <?php if (!empty($modalidad['descripcion'])): ?>
                                <p class="info_bachillerato_texto"><?php echo htmlspecialchars($modalidad['descripcion']); ?></p>
                            <?php endif; ?>
                            
                            <!-- IMAGEN - Si existe en BD -->
                            <?php if (!empty($modalidad['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($modalidad['imagen']); ?>" alt="<?php echo htmlspecialchars($modalidad['nombre']); ?>" class="info_bachillerato_imagen">
                            <?php endif; ?>
                            
                            <!-- BOTÓN PDF - Si existe URL en BD -->
                            <?php if (!empty($modalidad['pdf_url'])): ?>
                                <div>
                                    <a href="<?php echo htmlspecialchars($modalidad['pdf_url']); ?>" class="info_bachillerato_btn_pdf" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                        Descargar PDF
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- MODALIDAD HUMANIDADES -->
            <?php if ($modalidad['nombre'] == 'Modalidad de Humanidades y Ciencias Sociales'): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <!-- TÍTULO HUMANIDADES -->
                        <h2 class="info_bachillerato_titulo">Modalidad de Humanidades y Ciencias Sociales</h2>
                        <div>
                            <!-- DESCRIPCIÓN OPCIONAL -->
                            <?php if (!empty($modalidad['descripcion'])): ?>
                                <p class="info_bachillerato_texto"><?php echo htmlspecialchars($modalidad['descripcion']); ?></p>
                            <?php endif; ?>
                            
                            <!-- IMAGEN OPCIONAL -->
                            <?php if (!empty($modalidad['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($modalidad['imagen']); ?>" alt="<?php echo htmlspecialchars($modalidad['nombre']); ?>" class="info_bachillerato_imagen">
                            <?php endif; ?>
                            
                            <!-- PDF OPCIONAL -->
                            <?php if (!empty($modalidad['pdf_url'])): ?>
                                <div>
                                    <a href="<?php echo htmlspecialchars($modalidad['pdf_url']); ?>" class="info_bachillerato_btn_pdf" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                        Descargar PDF
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- SIN MODALIDADES - Mensaje fallback si BD vacío -->
        <?php if (empty($modalidades)): ?>
            <section class="seccion-contenido">
                <div class="contenedor-max">
                    <div>  <!-- Mensaje centrado -->
                        <i class="fas fa-graduation-cap"></i>  <!-- Icono vacío -->
                        <h3>No hay modalidades disponibles</h3>
                        <p>Consulta con secretaría nuestra oferta de Bachillerato.</p>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <!-- FOOTER GLOBAL -->
    <?php include 'footer.php'; ?>
</body>
</html>