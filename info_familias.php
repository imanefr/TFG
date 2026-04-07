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

<main class="info_familias_pagina">
    <section class="seccion-hero-universal">
        <div class="contenedor-max">
            <div class="hero-layout-universal">
                <div class="hero-icono-universal">
                    <i class="fas fa-users icono_universal"></i>
                </div>
                <div class="hero-texto-universal">
                    <h1 class="hero-titulo-universal">Información a las familias</h1>
                    <p class="hero-subtitulo-universal">Cumpliendo Resolución 4 diciembre 2023 - Comunidad de Madrid</p>
                </div>
            </div>
        </div>
    </section>

   <!-- SECCIÓN PRINCIPAL - Información obligatoria para familias -->
<section class="seccion-contenido">
    <div class="info_familias_contenedor">  
        <!-- TÍTULO PRINCIPAL -->
        <h2 class="info_familias_titulo">Información a las familias</h2>
        <!-- REFERENCIA LEGAL - Resolución oficial Comunidad Madrid -->
        <p class="info_familias_intro">
            La Resolución conjunta de 4 de diciembre de 2023 establece que los centros educativos deben disponer 
            de un apartado "Información a las familias" con oferta educativa, programas, documentación y contactos.
        </p>

        <!-- 1. BLOQUE OFERTA EDUCATIVA - Tabla dinámica desde BD -->
        <div class="info_familias_bloque">
            <h3><i class="fas fa-graduation-cap"></i> Oferta Educativa</h3>  
            <div class="info_familias_tabla_responsive">  
                <table>  <!-- TABLA 4 COLUMNAS -->
                    <thead>  <!-- CABECERA FIJA -->
                        <tr>
                            <th>Etapa</th>           <!-- ESO, Bachillerato, FP -->
                            <th>Detalles</th>       <!-- Descripción/enlace -->
                            <th>Horario</th>        <!-- Horario lectivo -->
                            <th>Itinerarios 4º ESO</th>  <!-- Opciones ESO final -->
                        </tr>
                    </thead>
                    <tbody>  <!-- CUERPO DINÁMICO -->
                        <?php if (!empty($oferta_educativa)): ?>  <!-- SI HAY DATOS BD -->
                            <?php foreach ($oferta_educativa as $oferta): ?>  <!-- LOOP CADA ETAPA -->
                                <tr>  <!-- FILA INDIVIDUAL -->
                                    <!-- COLUMNA ETAPA - Con detalle ESO diversificación -->
                                    <td><strong><?php echo htmlspecialchars($oferta['etapa']); ?></strong>
                                        <?php if ($oferta['etapa'] == 'ESO'): ?>
                                            <br><small>1º a 4º (grupos diversificación)</small>
                                        <?php endif; ?>
                                    </td>
                                    <!-- COLUMNA DETALLES - Enlace o texto plano -->
                                    <td>
                                        <?php if (!empty($oferta['enlace_detalles'])): ?>
                                            <a href="<?php echo htmlspecialchars($oferta['enlace_detalles']); ?>" target="_blank">Ver detalles</a>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($oferta['detalles']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <!-- COLUMNA HORARIO - O guión si no aplica -->
                                    <td><?php echo htmlspecialchars($oferta['horario'] ?? '-'); ?></td>
                                    <!-- COLUMNA ITINERARIOS - Lista dinámica o guión -->
                                    <td>
                                        <?php if (!empty($oferta['itinerarios'])): ?>
                                            <?php
                                            // PROCESA SALTOS LÍNEA - \\\\n → <li> HTML
                                            $itinerarios = explode("\\n", str_replace('\\\\n', "\\n", $oferta['itinerarios']));
                                            echo '<ul style="margin: 0; padding-left: 1rem; font-size: 0.9rem;">';
                                            foreach ($itinerarios as $itinerario) {
                                                $itinerario = trim($itinerario);  // Limpia espacios
                                                if (!empty($itinerario)) {        // Solo no vacíos
                                                    echo '<li>' . htmlspecialchars($itinerario) . '</li>';
                                                }
                                            }
                                            echo '</ul>';
                                            ?>
                                        <?php else: ?>
                                            -  <!-- Sin itinerarios -->
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>  <!-- FALLBACK SIN BD - Datos fijos -->
                            <!-- ESO FILA ESTÁTICA -->
                            <tr>
                                <td><strong>ESO</strong><br><small>1º a 4º (grupos diversificación)</small></td>
                                <td>Enseñanzas básicas obligatorias</td>
                                <td>8:30 - 14:15 h</td>
                                <td rowspan="3">  <!-- OCUPA 3 FILAS - Itinerarios compartidos -->
                                    <ul style="margin: 0; padding-left: 1rem;">
                                        <li>Humanidades</li>
                                        <li>Ciencias</li>
                                        <li>Profesional</li>
                                    </ul>
                                </td>
                            </tr>
                            <!-- BACHILLERATO FILA -->
                            <tr>
                                <td><strong>Bachillerato</strong></td>
                                <td><a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/index.php/bachillerato/" target="_blank">Ver detalles</a></td>
                                <td>-</td>
                            </tr>
                            <!-- FP FILA -->
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

        <!-- 2. BLOQUE DATOS GENERALES - Diseño cards/lista (NO tabla) -->
        <div class="info_familias_bloque">
            <h3><i class="fas fa-info-circle"></i> Datos Generales del Centro</h3>  
            
            <div class="info_familias_datos_generales">  
                <!-- FILA DIRECCIÓN -->
                <div class="info_familias_dato_fila">
                    <span class="info_familias_dato_label">Dirección</span>  
                    <span class="info_familias_dato_valor"><?php echo htmlspecialchars($datos_centro['direccion']); ?></span>  <!-- Valor BD -->
                </div>

                <!-- FILA HORARIO -->
                <div class="info_familias_dato_fila">
                    <span class="info_familias_dato_label">Horario</span>
                    <span class="info_familias_dato_valor"><?php echo htmlspecialchars($datos_centro['horario']); ?></span>
                </div>

                <!-- FILA TELÉFONO -->
                <div class="info_familias_dato_fila">
                    <span class="info_familias_dato_label">Teléfono</span>
                    <span class="info_familias_dato_valor"><?php echo htmlspecialchars($datos_centro['telefono']); ?></span>
                </div>

                <!-- FILA FAX -->
                <div class="info_familias_dato_fila">
                    <span class="info_familias_dato_label">Fax</span>
                    <span class="info_familias_dato_valor"><?php echo htmlspecialchars($datos_centro['fax']); ?></span>
                </div>

                <!-- FILA EMAIL DIRECCIÓN - Enlace mailto -->
                <div class="info_familias_dato_fila">
                    <span class="info_familias_dato_label">Dirección</span>
                    <a href="mailto:<?php echo htmlspecialchars($datos_centro['email_direccion']); ?>" 
                       class="info_familias_dato_valor info_familias_email_inline">
                        <?php echo htmlspecialchars($datos_centro['email_direccion']); ?>
                    </a>
                </div>

                <!-- FILA EMAIL SECRETARÍA - Enlace mailto -->
                <div class="info_familias_dato_fila">
                    <span class="info_familias_dato_label">Secretaría</span>
                    <a href="mailto:<?php echo htmlspecialchars($datos_centro['email_secretaria']); ?>" 
                       class="info_familias_dato_valor info_familias_email_inline">
                        <?php echo htmlspecialchars($datos_centro['email_secretaria']); ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. BLOQUE PROYECTOS - Enlace externo fijo -->
        <div class="info_familias_bloque">
            <h3><i class="fas fa-project-diagram"></i> Programas y Proyectos Educativos</h3>
            <div class="info_familias_libros_acciones">  <!-- Contenedor botón -->
                <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/index.php/proyectos-de-centro/" 
                   class="info_familias_btn_pdf" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Ver Proyectos de Centro
                </a>
            </div>
        </div>

        <!-- 4. BLOQUE DOCUMENTOS - Grid PDFs dinámico -->
        <div class="info_familias_bloque">
            <h3><i class="fas fa-file-pdf"></i> Documentación del Centro</h3>
            <div class="info_familias_documentos_grid">  <!-- Grid responsive -->
                <?php if (!empty($documentos_db)): ?>  <!-- SI HAY DOCS BD -->
                    <?php foreach ($documentos_db as $doc): ?>  <!-- LOOP DOCUMENTOS -->
                        <!-- CLASE ESPECIAL - Resultados académicos diferente estilo -->
                        <div class="info_familias_documento_item <?php echo strpos($doc['titulo'], 'Resultados') !== false ? 'resultados' : ''; ?>">
                            <a href="<?php echo htmlspecialchars($doc['url']); ?>" class="info_familias_btn_pdf" target="_blank" rel="noopener">
                                <i class="fas fa-file-pdf"></i>  <!-- Icono PDF -->
                                <span><?php echo htmlspecialchars($doc['titulo']); ?></span>  <!-- Título doc -->
                                <i class="fas fa-download"></i>     <!-- Icono download -->
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>  <!-- FALLBACK DOCS FIJOS -->
                    <!-- PROYECTO EDUCATIVO PDF -->
                    <div class="info_familias_documento_item">
                        <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/10/Proyecto-Educativo-del-Centro2425.pdf" class="info_familias_btn_pdf" target="_blank" rel="noopener">
                            <i class="fas fa-file-pdf"></i>
                            <span>Proyecto Educativo</span>
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                    <!-- NORMAS CONVIVENCIA PDF -->
                    <div class="info_familias_documento_item">
                        <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/10/NORMAS-DE-CONVIVENCIA-IES-LA-ARBOLEDA.pdf" class="info_familias_btn_pdf" target="_blank" rel="noopener">
                            <i class="fas fa-file-pdf"></i>
                            <span>Normas de Convivencia</span>
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                    <!-- RESULTADOS ACADÉMICOS - Enlace página -->
                    <div class="info_familias_documento_item resultados">
                        <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/index.php/resultados-academicos/" class="info_familias_btn_pdf" target="_blank">
                            <i class="fas fa-chart-bar"></i>
                            <span>Resultados Académicos</span>
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 5. BLOQUE ACTIVIDADES - PDF + visor iframe -->
        <?php
        // URL CONDICIONAL - BD o fallback fijo
        $actividad_url = $actividad ? $actividad['url'] :
                'https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2025/10/WEB-Programacion-Actividades-complementarias-y-extraescolares-2025_26.docx.pdf';
        ?>
        <div class="info_familias_bloque">
            <h3><i class="fas fa-calendar-alt"></i> Actividades Complementarias</h3>
            <!-- BOTÓN DESCARGA -->
            <div class="info_familias_libros_acciones">
                <a href="<?php echo htmlspecialchars($actividad_url); ?>" class="info_familias_btn_pdf" target="_blank" rel="noopener">
                    <i class="fas fa-download"></i> Descargar Programación 2025-26
                </a>
            </div>
            <!-- VISOR IFRAME EMBEBIDO -->
            <div class="info_familias_vista_previa">
                <iframe src="<?php echo htmlspecialchars($actividad_url); ?>" 
                        class="info_familias_visor_pdf" 
                        title="Programación Actividades Complementarias 2025-26">
                </iframe>
            </div>
        </div>
    </div>  <!-- FIN CONTENEDOR PRINCIPAL -->
</section>  <!-- FIN SECCIÓN PRINCIPAL -->
</main>

<!-- CIERRA CONEXIÓN BD + FOOTER -->
<?php $conexion->close(); ?>
<?php include 'footer.php'; ?>  
