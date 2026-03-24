<?php
// INCLUYE CONEXIÓN BD - Carga MySQLi para consulta ciclos FP
include("conexion.php");

// CONSULTA CICLOS FORMATIVOS - Solo activos, ordenados por categoría/nivel/nombre
$sql = "SELECT * FROM ciclos_fp WHERE activo = 1 ORDER BY categoria, nivel, nombre";  // Orden jerárquico
$resultado = $conexion->query($sql);                                                  // Ejecuta consulta
$ciclos = [];                                                                         // Array vacío inicial

// PROCESA RESULTADOS - Convierte filas BD → array PHP
while ($fila = $resultado->fetch_assoc()) {                                           // Itera registros
    $ciclos[] = $fila;                                                                // Añade cada ciclo
}
$conexion->close();                                                                   // Cierra conexión
?>

<!DOCTYPE html>  
<html lang="es"> 
<head>
    <!-- INCLUYE HEAD COMPLETO -->
    <?php include 'head.php'; ?>
</head>

<body>
    <!-- HERO HEADER FP -->
    <section class="seccion-hero-universal">
        <div class="contenedor-max">  
            <div class="hero-layout-universal">  
                <div class="hero-icono-universal">
                    <i class="fas fa-info icono_universal"></i>
                </div>
                <div class="hero-texto-universal">
                    <h1 class="hero-titulo-universal">información fp</h1>  
                    <p class="hero-subtitulo-universal">Proyectos de movilidad en Europa desde 2010</p>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENIDO - 4 secciones por nivel FP -->
    <main class="info_fp_pagina">
        <?php if (!empty($ciclos)): ?>  <!-- SI HAY CICLOS BD -->
            
            <!-- 1. FP BÁSICA - Filtra nivel 'FPB' -->
            <?php $fp_basica = array_filter($ciclos, fn($c) => $c['nivel'] == 'FPB'); ?>
            <?php if (!empty($fp_basica)): ?>
                <section class="seccion-contenido">  <!-- Sección individual -->
                    <div class="contenedor-max">
                        <h2 class="info_fp_titulo">Formación Profesional Básica</h2>  <!-- Título nivel -->
                        <div class="info_fp_grid">  <!-- Grid responsive cards -->
                            <?php foreach ($fp_basica as $ciclo): ?>  <!-- LOOP FPB -->
                                <!-- CARD FPB - Enlace PDF fijo peluquería -->
                                <a href="https://www.comunidad.madrid/sites/default/files/impb01_peluqueria_y_estetica.pdf" 
                                   class="info_fp_card" 
                                   target="_blank"
                                   title="Ver PDF oficial - <?php echo htmlspecialchars($ciclo['nombre']); ?>">
                                    <h3><?php echo htmlspecialchars($ciclo['nombre']); ?></h3>  <!-- Nombre ciclo -->
                                    <p class="info_fp_modalidad"><?php echo htmlspecialchars($ciclo['modalidad']); ?></p>  <!-- Modalidad -->
                                    <!-- INFO TÉCNICA - Nivel + duración -->
                                    <div class="info_fp_info">
                                        <span class="info_fp_nivel"><?php echo $ciclo['nivel']; ?></span>
                                        <span class="info_fp_horas"><?php echo $ciclo['duracion']; ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 2. GRADO MEDIO - Filtra nivel 'GM' -->
            <?php $grado_medio = array_filter($ciclos, fn($c) => $c['nivel'] == 'GM'); ?>
            <?php if (!empty($grado_medio)): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="info_fp_titulo">Grado Medio</h2>
                        <div class="info_fp_grid">
                            <?php foreach ($grado_medio as $ciclo): ?>
                                <?php 
                                // ARRAY URLs ESPECÍFICAS GM - Mapeo nombre → PDF oficial
                                $pdf_urls_gm = [
                                    'Sistemas Microinformáticos y Redes' => 'https://www.comunidad.madrid/sites/default/files/ifcm01_sistemas_microinformaticos_y_redes.pdf',
                                    'Vídeo Disc-jockey y Sonido' => 'https://www.comunidad.madrid/sites/default/files/imsm01_video_disc_jockey_y_sonido.pdf',
                                    'Peluquería y Cosmética Capilar' => 'https://www.comunidad.madrid/sites/default/files/impm02_peluqueria_y_cosmetica_capilar.pdf',
                                    'Estética y Belleza' => 'https://www.comunidad.madrid/sites/default/files/impm01_estetica_y_belleza.pdf'
                                ];
                                // URL POR CICLO O FALLBACK ADMISIÓN
                                $pdf_url = $pdf_urls_gm[$ciclo['nombre']] ?? 'https://www.comunidad.madrid/sites/default/files/doc/educacion/fp/admision-gradomedio-oferta-junio-2019_20.pdf';
                                ?>
                                <!-- CARD GM - PDF dinámico por nombre -->
                                <a href="<?php echo $pdf_url; ?>" 
                                   class="info_fp_card" 
                                   target="_blank"
                                   title="Ver PDF oficial - <?php echo htmlspecialchars($ciclo['nombre']); ?>">
                                    <h3><?php echo htmlspecialchars($ciclo['nombre']); ?></h3>
                                    <p class="info_fp_modalidad"><?php echo htmlspecialchars($ciclo['modalidad']); ?></p>
                                    <div class="info_fp_info">
                                        <span class="info_fp_nivel"><?php echo $ciclo['nivel']; ?></span>
                                        <span class="info_fp_horas"><?php echo $ciclo['duracion']; ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 3. GRADO SUPERIOR - Filtra nivel 'GS' -->
            <?php $grado_superior = array_filter($ciclos, fn($c) => $c['nivel'] == 'GS'); ?>
            <?php if (!empty($grado_superior)): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="info_fp_titulo">Grado Superior</h2>
                        <div class="info_fp_grid">
                            <?php foreach ($grado_superior as $ciclo): ?>
                                <?php 
                                // ARRAY URLs ESPECÍFICAS GS - Incluye DAW tu carrera!
                                $pdf_urls_gs = [
                                    'Administración de Sistemas Informáticos en Red' => 'https://www.comunidad.madrid/sites/default/files/ifcs01_administracion_de_sistemas_informaticos_en_red.pdf',
                                    'Desarrollo de Aplicaciones Multiplataforma' => 'https://www.comunidad.madrid/sites/default/files/ifcs02_desarrollo_de_aplicaciones_multiplataforma.pdf',
                                    'Desarrollo de Aplicaciones Web' => 'https://www.comunidad.madrid/sites/default/files/ifcs03_desarrollo_de_aplicaciones_web.pdf',
                                    'Realización de Proyectos Audiovisuales y Espectáculos' => 'https://www.comunidad.madrid/sites/default/files/imss02_realizacion_de_proyectos_audiovisuales_y_espectaculos.pdf',
                                    'Estética Integral y Bienestar' => 'https://www.comunidad.madrid/sites/default/files/imps01_estetica_integral_y_bienestar.pdf'
                                ];
                                $pdf_url = $pdf_urls_gs[$ciclo['nombre']] ?? 'https://www.comunidad.madrid/sites/default/files/doc/educacion/fp/admision-gradosuperior-oferta-junio-2019_20.pdf';
                                ?>
                                <!-- CARD GS - DAW, ASIR, DAM destacados -->
                                <a href="<?php echo $pdf_url; ?>" 
                                   class="info_fp_card" 
                                   target="_blank"
                                   title="Ver PDF oficial - <?php echo htmlspecialchars($ciclo['nombre']); ?>">
                                    <h3><?php echo htmlspecialchars($ciclo['nombre']); ?></h3>
                                    <p class="info_fp_modalidad"><?php echo htmlspecialchars($ciclo['modalidad']); ?></p>
                                    <div class="info_fp_info">
                                        <span class="info_fp_nivel"><?php echo $ciclo['nivel']; ?></span>
                                        <span class="info_fp_horas"><?php echo $ciclo['duracion']; ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 4. CURSOS ESPECIALIZACIÓN  -->
            <?php $especializacion = array_filter($ciclos, fn($c) => $c['nivel'] == 'CE'); ?>
            <?php if (!empty($especializacion)): ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="info_fp_titulo">Cursos de Especialización</h2>
                        <div class="info_fp_grid">
                            <?php foreach ($especializacion as $ciclo): ?>
                                <!-- CARD CE - Videojuegos destacada + clase especial -->
                                <a href="https://www.comunidad.madrid/sites/default/files/ifces02_desarrollo_de_videojuegos_y_realidad_virtual.pdf" 
                                   class="info_fp_card info_fp_card_especial" 
                                   target="_blank"
                                   title="Ver PDF oficial - <?php echo htmlspecialchars($ciclo['nombre']); ?>">
                                    <h3><?php echo htmlspecialchars($ciclo['nombre']); ?></h3>
                                    <p class="info_fp_modalidad"><?php echo htmlspecialchars($ciclo['modalidad']); ?></p>
                                    <div class="info_fp_info">
                                        <span class="info_fp_nivel"><?php echo $ciclo['nivel']; ?></span>
                                        <span class="info_fp_horas"><?php echo $ciclo['duracion']; ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        <?php else: ?>  <!-- SIN CICLOS BD -->
            <section class="seccion-contenido">
                <div class="contenedor-max">
                    <!-- Fallback mensaje vacío -->
                    <div class="info_fp_sin_contenido">
                        <i class="fas fa-briefcase"></i>  <!-- Icono maletín FP -->
                        <h3>No hay ciclos disponibles</h3>
                        <p>Consulta con secretaría nuestra oferta formativa.</p>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <!-- FOOTER GLOBAL -->
    <?php include 'footer.php'; ?>
</body>
</html>
