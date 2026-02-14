<?php
include("conexion.php");

// Consulta ciclos formativos desde BD
$sql = "SELECT * FROM ciclos_fp WHERE activo = 1 ORDER BY categoria, nivel, nombre";
$resultado = $conexion->query($sql);
$ciclos = [];
while ($fila = $resultado->fetch_assoc()) {
    $ciclos[] = $fila;
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

        .fp-contenido {
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

        .grid-fp {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 4rem;
        }

        .seccion-contenido:first-of-type .grid-fp {
            justify-items: center;
        }

        /* TARJETAS CLICABLES CON PDF INDIVIDUAL */
        .card-fp {
            background: var(--blanco);
            border-radius: 15px;
            padding: 45px 30px 25px 30px;
            text-align: center;
            box-shadow: var(--sombra-suave);
            border-top: 6px solid var(--verde-principal);
            display: flex;
            flex-direction: column;
            min-height: 250px;
            transition: all 0.3s ease;
            position: relative;
            width: 100%;
            text-decoration: none;
            color: inherit;
            cursor: pointer;
            user-select: none;
        }

        .card-fp:hover {
            transform: translateY(-5px);
            box-shadow: var(--sombra-fuerte);
        }

        .card-fp:active {
            transform: scale(0.98);
        }

        .card-fp.especial {
            border-top-color: #ff9800;
        }

        .card-fp h3 {
            font-size: 1.15rem;
            color: var(--gris-oscuro);
            margin: 0 0 12px 0;
            font-weight: 700;
            line-height: 1.3;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fp-modalidad {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: var(--verde-principal);
            font-weight: 700;
            letter-spacing: 0.05em;
            margin: 15px 0;
        }

        .fp-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .fp-nivel {
            background-color: var(--verde-principal);
            color: var(--blanco);
            font-size: 0.65rem;
            font-weight: 800;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            text-transform: uppercase;
            box-shadow: 0 2px 5px rgba(19, 139, 60, 0.3);
            flex-shrink: 0;
        }

        .fp-horas {
            font-size: 0.75rem;
            color: var(--gris-claro);
            font-weight: 500;
            margin-left: 15px;
        }

        .sin-contenido {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gris-oscuro);
        }

        .sin-contenido i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
            color: var(--verde-principal);
        }

        .sin-contenido h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .sin-contenido p {
            color: var(--gris-claro);
        }

        @media (max-width: 768px) {
            .grid-fp {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .seccion-contenido:first-of-type .grid-fp {
                justify-items: stretch;
            }

            .card-fp {
                padding: 35px 20px 20px 20px;
                min-height: 220px;
            }
        }

        @media (max-width: 480px) {
            .contenedor-max {
                padding: 0 1rem;
            }

            .card-fp {
                padding: 30px 15px 20px 15px;
            }
        }
    </style>
</head>

<body>
    <!-- HERO HEADER ESO -->
    <section class="seccion-hero-universal">
        <div class="contenedor-max">
            <div class="hero-layout-universal">
                <div class="hero-icono-universal">
                    <i class="fas fa-info" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
                </div>
                <div class="hero-texto-universal">
                    <h1 class="hero-titulo-universal">información fp</h1>
                    <p class="hero-subtitulo-universal">Proyectos de movilidad en Europa desde 2010</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="fp-contenido">
        <?php if (!empty($ciclos)): ?>
            <!-- FP BÁSICA -->
            <?php $fp_basica = array_filter($ciclos, fn($c) => $c['nivel'] == 'FPB'); ?>
            <?php if (!empty($fp_basica)): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="seccion-contenido-h2">Formación Profesional Básica</h2>
                        <div class="grid-fp">
                            <?php foreach ($fp_basica as $ciclo): ?>
                                <a href="https://www.comunidad.madrid/sites/default/files/impb01_peluqueria_y_estetica.pdf" 
                                   class="card-fp" 
                                   target="_blank"
                                   title="Ver PDF oficial - <?php echo htmlspecialchars($ciclo['nombre']); ?>">
                                    <h3><?php echo htmlspecialchars($ciclo['nombre']); ?></h3>
                                    <p class="fp-modalidad"><?php echo htmlspecialchars($ciclo['modalidad']); ?></p>
                                    <div class="fp-info">
                                        <span class="fp-nivel"><?php echo $ciclo['nivel']; ?></span>
                                        <span class="fp-horas"><?php echo $ciclo['duracion']; ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- GRADO MEDIO -->
            <?php $grado_medio = array_filter($ciclos, fn($c) => $c['nivel'] == 'GM'); ?>
            <?php if (!empty($grado_medio)): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="seccion-contenido-h2">Grado Medio</h2>
                        <div class="grid-fp">
                            <?php foreach ($grado_medio as $ciclo): ?>
                                <?php 
                                // URLs específicas por nombre del ciclo GM
                                $pdf_urls_gm = [
                                    'Sistemas Microinformáticos y Redes' => 'https://www.comunidad.madrid/sites/default/files/ifcm01_sistemas_microinformaticos_y_redes.pdf',
                                    'Vídeo Disc-jockey y Sonido' => 'https://www.comunidad.madrid/sites/default/files/imsm01_video_disc_jockey_y_sonido.pdf',
                                    'Peluquería y Cosmética Capilar' => 'https://www.comunidad.madrid/sites/default/files/impm02_peluqueria_y_cosmetica_capilar.pdf',
                                    'Estética y Belleza' => 'https://www.comunidad.madrid/sites/default/files/impm01_estetica_y_belleza.pdf'
                                ];
                                $pdf_url = $pdf_urls_gm[$ciclo['nombre']] ?? 'https://www.comunidad.madrid/sites/default/files/doc/educacion/fp/admision-gradomedio-oferta-junio-2019_20.pdf';
                                ?>
                                <a href="<?php echo $pdf_url; ?>" 
                                   class="card-fp" 
                                   target="_blank"
                                   title="Ver PDF oficial - <?php echo htmlspecialchars($ciclo['nombre']); ?>">
                                    <h3><?php echo htmlspecialchars($ciclo['nombre']); ?></h3>
                                    <p class="fp-modalidad"><?php echo htmlspecialchars($ciclo['modalidad']); ?></p>
                                    <div class="fp-info">
                                        <span class="fp-nivel"><?php echo $ciclo['nivel']; ?></span>
                                        <span class="fp-horas"><?php echo $ciclo['duracion']; ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- GRADO SUPERIOR -->
            <?php $grado_superior = array_filter($ciclos, fn($c) => $c['nivel'] == 'GS'); ?>
            <?php if (!empty($grado_superior)): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="seccion-contenido-h2">Grado Superior</h2>
                        <div class="grid-fp">
                            <?php foreach ($grado_superior as $ciclo): ?>
                                <?php 
                                // URLs específicas por nombre del ciclo GS
                                $pdf_urls_gs = [
                                    'Administración de Sistemas Informáticos en Red' => 'https://www.comunidad.madrid/sites/default/files/ifcs01_administracion_de_sistemas_informaticos_en_red.pdf',
                                    'Desarrollo de Aplicaciones Multiplataforma' => 'https://www.comunidad.madrid/sites/default/files/ifcs02_desarrollo_de_aplicaciones_multiplataforma.pdf',
                                    'Desarrollo de Aplicaciones Web' => 'https://www.comunidad.madrid/sites/default/files/ifcs03_desarrollo_de_aplicaciones_web.pdf',
                                    'Realización de Proyectos Audiovisuales y Espectáculos' => 'https://www.comunidad.madrid/sites/default/files/imss02_realizacion_de_proyectos_audiovisuales_y_espectaculos.pdf',
                                    'Estética Integral y Bienestar' => 'https://www.comunidad.madrid/sites/default/files/imps01_estetica_integral_y_bienestar.pdf'
                                ];
                                $pdf_url = $pdf_urls_gs[$ciclo['nombre']] ?? 'https://www.comunidad.madrid/sites/default/files/doc/educacion/fp/admision-gradosuperior-oferta-junio-2019_20.pdf';
                                ?>
                                <a href="<?php echo $pdf_url; ?>" 
                                   class="card-fp" 
                                   target="_blank"
                                   title="Ver PDF oficial - <?php echo htmlspecialchars($ciclo['nombre']); ?>">
                                    <h3><?php echo htmlspecialchars($ciclo['nombre']); ?></h3>
                                    <p class="fp-modalidad"><?php echo htmlspecialchars($ciclo['modalidad']); ?></p>
                                    <div class="fp-info">
                                        <span class="fp-nivel"><?php echo $ciclo['nivel']; ?></span>
                                        <span class="fp-horas"><?php echo $ciclo['duracion']; ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- ESPECIALIZACIÓN -->
            <?php $especializacion = array_filter($ciclos, fn($c) => $c['nivel'] == 'CE'); ?>
            <?php if (!empty($especializacion)): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="seccion-contenido-h2">Cursos de Especialización</h2>
                        <div class="grid-fp">
                            <?php foreach ($especializacion as $ciclo): ?>
                                <a href="https://www.comunidad.madrid/sites/default/files/ifces02_desarrollo_de_videojuegos_y_realidad_virtual.pdf" 
                                   class="card-fp especial" 
                                   target="_blank"
                                   title="Ver PDF oficial - <?php echo htmlspecialchars($ciclo['nombre']); ?>">
                                    <h3><?php echo htmlspecialchars($ciclo['nombre']); ?></h3>
                                    <p class="fp-modalidad"><?php echo htmlspecialchars($ciclo['modalidad']); ?></p>
                                    <div class="fp-info">
                                        <span class="fp-nivel"><?php echo $ciclo['nivel']; ?></span>
                                        <span class="fp-horas"><?php echo $ciclo['duracion']; ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        <?php else: ?>
            <section class="seccion-contenido">
                <div class="contenedor-max">
                    <div class="sin-contenido">
                        <i class="fas fa-briefcase"></i>
                        <h3>No hay ciclos disponibles</h3>
                        <p>Consulta con secretaría nuestra oferta formativa.</p>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
