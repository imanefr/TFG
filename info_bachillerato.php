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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --verde-principal: #2e7d32;
            --verde-claro: #4caf50;
            --blanco: #ffffff;
            --gris-fondo: #f8f9fa;
            --gris-oscuro: #333;
            --gris-claro: #a0a0a0;
            --sombra-suave: 0 10px 25px rgba(0,0,0,0.05);
            --sombra-fuerte: 0 20px 40px rgba(0,0,0,0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: var(--gris-oscuro);
            background: var(--gris-fondo);
        }

        .bach-contenido {
            padding: 2rem 0 4rem;
        }

        .seccion-contenido-h2 {
            text-align: center;
            text-transform: uppercase;
            color: var(--verde-principal);
            font-size: 1.25rem;
            font-weight: 700;
            margin: 3rem 0 2rem 0;
            letter-spacing: 0.1em;
            position: relative;
        }

        .seccion-contenido-h2::after {
            content: "";
            display: block;
            width: 45px;
            height: 3px;
            background-color: var(--verde-principal);
            margin: 10px auto 0;
        }

        /* Imagen */
        .bach-imagen {
            width: 100%;
            height: auto;
            max-height: 800px;
            object-fit: contain;
            object-position: center;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        /* Texto descriptivo */
        .bach-texto {
            font-size: 0.95rem;
            color: var(--gris-oscuro);
            line-height: 1.6;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        /* Botón PDF */
        .btn-pdf {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--verde-principal);
            color: var(--blanco);
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-pdf:hover {
            background: var(--verde-claro);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .bach-texto {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <!-- HERO HEADER BACHILLERATO -->
    <section class="seccion-hero-universal">
        <div class="contenedor-max">
            <div class="hero-layout-universal">
                <div class="hero-icono-universal">
                    <i class="fas fa-graduation-cap" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
                </div>
                <div class="hero-texto-universal">
                    <h1 class="hero-titulo-universal">Bachillerato</h1>
                    <p class="hero-subtitulo-universal">Preparación para el futuro universitario y profesional</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN INTRODUCTORIA -->
    <section class="bach-contenido">
        <div class="contenedor-max">
            <div style="text-align: center; max-width: 800px; margin: 0 auto 4rem;">
                <h2 style="color: var(--verde-principal); font-size: 1.8rem; margin-bottom: 1rem;">Modelo Organizativo</h2>
                <p style="font-size: 1.1rem; color: var(--gris-oscuro); line-height: 1.7; margin-bottom: 1.5rem;">
                    El Bachillerato comprende dos cursos y se organiza en modalidades diferentes para ofrecer una 
                    preparación especializada acorde con sus intereses.
                </p>
                <a href="https://www.comunidad.madrid/servicios/educacion/bachillerato" 
                   target="_blank" 
                   style="background: var(--verde-principal); color: var(--blanco); padding: 0.75rem 1.5rem; border-radius: 25px; font-weight: 600; text-decoration: none; transition: all 0.3s ease; font-size: 1rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-external-link-alt"></i>
                    Información oficial Comunidad de Madrid
                </a>
            </div>
        </div>
    </section>

    <!-- MODALIDADES -->
    <main class="bach-contenido">
        <?php foreach ($modalidades as $modalidad): ?>
            <?php if ($modalidad['nombre'] == 'Modalidad de Ciencias'): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="seccion-contenido-h2">Modalidad de Ciencias</h2>
                        <div style="max-width: 600px; margin: 0 auto;">
                            <?php if (!empty($modalidad['descripcion'])): ?>
                                <p class="bach-texto"><?php echo htmlspecialchars($modalidad['descripcion']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($modalidad['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($modalidad['imagen']); ?>" alt="<?php echo htmlspecialchars($modalidad['nombre']); ?>" class="bach-imagen">
                            <?php endif; ?>
                            <?php if (!empty($modalidad['pdf_url'])): ?>
                                <div style="text-align: center;">
                                    <a href="<?php echo htmlspecialchars($modalidad['pdf_url']); ?>" class="btn-pdf" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                        Descargar PDF
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($modalidad['nombre'] == 'Modalidad de Humanidades y Ciencias Sociales'): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="seccion-contenido-h2">Modalidad de Humanidades y Ciencias Sociales</h2>
                        <div style="max-width: 600px; margin: 0 auto;">
                            <?php if (!empty($modalidad['descripcion'])): ?>
                                <p class="bach-texto"><?php echo htmlspecialchars($modalidad['descripcion']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($modalidad['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($modalidad['imagen']); ?>" alt="<?php echo htmlspecialchars($modalidad['nombre']); ?>" class="bach-imagen">
                            <?php endif; ?>
                            <?php if (!empty($modalidad['pdf_url'])): ?>
                                <div style="text-align: center;">
                                    <a href="<?php echo htmlspecialchars($modalidad['pdf_url']); ?>" class="btn-pdf" target="_blank">
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

        <?php if (empty($modalidades)): ?>
            <section class="seccion-contenido">
                <div class="contenedor-max">
                    <div style="text-align: center; padding: 4rem 2rem; color: var(--gris-oscuro);">
                        <i class="fas fa-graduation-cap" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3; color: var(--verde-principal);"></i>
                        <h3>No hay modalidades disponibles</h3>
                        <p>Consulta con secretaría nuestra oferta de Bachillerato.</p>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
