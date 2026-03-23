<?php
// info_familias.php COMPLETO - Cumple Resolución 4 dic 2023 Comunidad de Madrid
include 'conexion.php';

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// OBTENER DATOS DEL CENTRO
$query_centro = "SELECT * FROM centros WHERE id = 1";
$result_centro = $conexion->query($query_centro);
$datos_centro = $result_centro ? $result_centro->fetch_assoc() : [
    'direccion' => 'Av. del Oeste, s/n, 28922 Alcorcón, Madrid',
    'horario' => 'Lunes-Viernes 8:30–21:30',
    'telefono' => '916 43 99 91',
    'fax' => '91 644 0025',
    'email_direccion' => 'ies.laarboleda.alcorcon@educa.madrid.org',
    'email_secretaria' => 'secretaria.ies.laarboleda.alcorcon@educa.madrid.org'
];

// OBTENER DOCUMENTOS ACTIVOS
$query_documentos = "SELECT * FROM documentos WHERE activo = TRUE ORDER BY categoria, titulo";
$result_documentos = $conexion->query($query_documentos);
$documentos_db = [];
if ($result_documentos) {
    while ($row = $result_documentos->fetch_assoc()) {
        $documentos_db[] = $row;
    }
}

// OBTENER OFERTA EDUCATIVA
$query_oferta = "SELECT * FROM oferta_educativa ORDER BY orden";
$result_oferta = $conexion->query($query_oferta);
$oferta_educativa = [];
if ($result_oferta) {
    while ($row = $result_oferta->fetch_assoc()) {
        $oferta_educativa[] = $row;
    }
}

// FILTRAR ACTIVIDADES COMPLEMENTARIAS
$actividades_doc = array_filter($documentos_db, function ($doc) {
    return stripos($doc['titulo'], 'Actividades') !== false ||
    stripos($doc['categoria'], 'actividades') !== false;
});
$actividad = !empty($actividades_doc) ? reset($actividades_doc) : null;
?>

<?php include 'head.php'; ?>
<link rel="stylesheet" href="css/info_familias.css">

<main class="info_familias_pagina">
    <section class="seccion-hero-universal">
        <div class="contenedor-max">
            <div class="hero-layout-universal">
                <div class="hero-icono-universal">
                    <i class="fas fa-users" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
                </div>
                <div class="hero-texto-universal">
                    <h1 class="hero-titulo-universal">Información a las familias</h1>
                    <p class="hero-subtitulo-universal">Cumpliendo Resolución 4 diciembre 2023 - Comunidad de Madrid</p>
                </div>
            </div>
        </div>
    </section>

    <section class="seccion-contenido">
        <div class="info_familias_contenedor">
            <h2 class="info_familias_titulo">Información a las familias</h2>
            <p class="info_familias_intro">
                La Resolución conjunta de 4 de diciembre de 2023 establece que los centros educativos deben disponer 
                de un apartado "Información a las familias" con oferta educativa, programas, documentación y contactos.
            </p>

            <!-- 1. OFERTA EDUCATIVA -->
            <div class="info_familias_bloque">
                <h3><i class="fas fa-graduation-cap"></i> Oferta Educativa</h3>
                <div class="info_familias_tabla_responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Etapa</th>
                                <th>Detalles</th>
                                <th>Horario</th>
                                <th>Itinerarios 4º ESO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($oferta_educativa)): ?>
                                <?php foreach ($oferta_educativa as $oferta): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($oferta['etapa']); ?></strong>
                                            <?php if ($oferta['etapa'] == 'ESO'): ?>
                                                <br><small>1º a 4º (grupos diversificación)</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($oferta['enlace_detalles'])): ?>
                                                <a href="<?php echo htmlspecialchars($oferta['enlace_detalles']); ?>" target="_blank">Ver detalles</a>
                                            <?php else: ?>
                                                <?php echo htmlspecialchars($oferta['detalles']); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($oferta['horario'] ?? '-'); ?></td>
                                        <td>
                                            <?php if (!empty($oferta['itinerarios'])): ?>
                                                <?php
                                                $itinerarios = explode("\n", str_replace('\\n', "\n", $oferta['itinerarios']));
                                                echo '<ul style="margin: 0; padding-left: 1rem; font-size: 0.9rem;">';
                                                foreach ($itinerarios as $itinerario) {
                                                    $itinerario = trim($itinerario);
                                                    if (!empty($itinerario)) {
                                                        echo '<li>' . htmlspecialchars($itinerario) . '</li>';
                                                    }
                                                }
                                                echo '</ul>';
                                                ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td><strong>ESO</strong><br><small>1º a 4º (grupos diversificación)</small></td>
                                    <td>Enseñanzas básicas obligatorias</td>
                                    <td>8:30 - 14:15 h</td>
                                    <td rowspan="3">
                                        <ul style="margin: 0; padding-left: 1rem;">
                                            <li>Humanidades</li>
                                            <li>Ciencias</li>
                                            <li>Profesional</li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Bachillerato</strong></td>
                                    <td><a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/index.php/bachillerato/" target="_blank">Ver detalles</a></td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td><strong>Formación Profesional</strong></td>
                                    <td><a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/index.php/formacion-profesional/" target="_blank">Ver detalles</a></td>
                                    <td>-</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. CONTACTO - NUEVO DISEÑO SIN TABLA -->
            <!-- 2. CONTACTO - AHORA ES UN BLOQUE IGUAL AL RESTO -->
<div class="info_familias_bloque">
    <h3><i class="fas fa-info-circle"></i> Datos Generales del Centro</h3>
    
    <div class="info_familias_datos_generales">
        <!-- Dirección Física -->
        <div class="info_familias_dato_fila">
            <span class="info_familias_dato_label">Dirección</span>
            <span class="info_familias_dato_valor"><?php echo htmlspecialchars($datos_centro['direccion']); ?></span>
        </div>

        <!-- Horario -->
        <div class="info_familias_dato_fila">
            <span class="info_familias_dato_label">Horario</span>
            <span class="info_familias_dato_valor"><?php echo htmlspecialchars($datos_centro['horario']); ?></span>
        </div>

        <!-- Teléfono -->
        <div class="info_familias_dato_fila">
            <span class="info_familias_dato_label">Teléfono</span>
            <span class="info_familias_dato_valor"><?php echo htmlspecialchars($datos_centro['telefono']); ?></span>
        </div>

        <!-- Fax -->
        <div class="info_familias_dato_fila">
            <span class="info_familias_dato_label">Fax</span>
            <span class="info_familias_dato_valor"><?php echo htmlspecialchars($datos_centro['fax']); ?></span>
        </div>

        <!-- Email Dirección -->
        <div class="info_familias_dato_fila">
            <span class="info_familias_dato_label">Dirección</span>
            <a href="mailto:<?php echo htmlspecialchars($datos_centro['email_direccion']); ?>" 
               class="info_familias_dato_valor info_familias_email_inline">
                <?php echo htmlspecialchars($datos_centro['email_direccion']); ?>
            </a>
        </div>

        <!-- Email Secretaría -->
        <div class="info_familias_dato_fila">
            <span class="info_familias_dato_label">Secretaría</span>
            <a href="mailto:<?php echo htmlspecialchars($datos_centro['email_secretaria']); ?>" 
               class="info_familias_dato_valor info_familias_email_inline">
                <?php echo htmlspecialchars($datos_centro['email_secretaria']); ?>
            </a>
        </div>
    </div>
</div>



            <!-- 3. PROGRAMAS Y PROYECTOS -->
            <div class="info_familias_bloque">
                <h3><i class="fas fa-project-diagram"></i> Programas y Proyectos Educativos</h3>
                <div class="info_familias_libros_acciones">
                    <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/index.php/proyectos-de-centro/" 
                       class="info_familias_btn_pdf" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Ver Proyectos de Centro
                    </a>
                </div>
            </div>

            <!-- 4. DOCUMENTACIÓN -->
            <div class="info_familias_bloque">
    <h3><i class="fas fa-file-pdf"></i> Documentación del Centro</h3>
    <div class="info_familias_documentos_grid">
        <?php if (!empty($documentos_db)): ?>
            <?php foreach ($documentos_db as $doc): ?>
                <div class="info_familias_documento_item <?php echo strpos($doc['titulo'], 'Resultados') !== false ? 'resultados' : ''; ?>">
                    <a href="<?php echo htmlspecialchars($doc['url']); ?>" class="info_familias_btn_pdf" target="_blank" rel="noopener">
                        <i class="fas fa-file-pdf"></i>
                        <span><?php echo htmlspecialchars($doc['titulo']); ?></span>
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div>
                <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/10/Proyecto-Educativo-del-Centro2425.pdf" class="info_familias_btn_documento" target="_blank" rel="noopener">
                    <i class="fas fa-file-pdf"></i> Proyecto Educativo <i class="fas fa-download"></i>
                </a>
            </div>
            <div>
                <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/10/NORMAS-DE-CONVIVENCIA-IES-LA-ARBOLEDA.pdf" class="info_familias_btn_documento" target="_blank" rel="noopener">
                    <i class="fas fa-file-pdf"></i> Normas de Convivencia <i class="fas fa-download"></i>
                </a>
            </div>
            <div>
                <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/index.php/resultados-academicos/" class="info_familias_btn_documento" target="_blank">
                    <i class="fas fa-chart-bar"></i> Resultados Académicos <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>


            <!-- 5. ACTIVIDADES COMPLEMENTARIAS -->
            <?php
            $actividad_url = $actividad ? $actividad['url'] :
                    'https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2025/10/WEB-Programacion-Actividades-complementarias-y-extraescolares-2025_26.docx.pdf';
            ?>
            <div class="info_familias_bloque">
                <h3><i class="fas fa-calendar-alt"></i> Actividades Complementarias</h3>
                <div class="info_familias_libros_acciones">
                    <a href="<?php echo htmlspecialchars($actividad_url); ?>" class="info_familias_btn_pdf" target="_blank" rel="noopener">
                        <i class="fas fa-download"></i> Descargar Programación 2025-26
                    </a>
                </div>
                <div class="info_familias_vista_previa">
                    <iframe src="<?php echo htmlspecialchars($actividad_url); ?>" 
                            class="info_familias_visor_pdf" 
                            title="Programación Actividades Complementarias 2025-26">
                    </iframe>
                </div>
            </div>
        </div>
    </section>
</main>

<?php $conexion->close();
include 'footer.php';
?>
