<?php
// info_familias.php COMPLETO - Cumple Resolución 4 dic 2023 Comunidad de Madrid
// TODOS LOS DATOS DESDE BASE DE DATOS arboledatablas
include 'conexion.php';

// Verificar conexión
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
    while($row = $result_documentos->fetch_assoc()) {
        $documentos_db[] = $row;
    }
}

// OBTENER OFERTA EDUCATIVA
$query_oferta = "SELECT * FROM oferta_educativa ORDER BY orden";
$result_oferta = $conexion->query($query_oferta);
$oferta_educativa = [];
if ($result_oferta) {
    while($row = $result_oferta->fetch_assoc()) {
        $oferta_educativa[] = $row;
    }
}

// FILTRAR ACTIVIDADES COMPLEMENTARIAS
$actividades_doc = array_filter($documentos_db, function($doc) {
    return stripos($doc['titulo'], 'Actividades') !== false || 
           stripos($doc['categoria'], 'actividades') !== false;
});
$actividad = !empty($actividades_doc) ? reset($actividades_doc) : null;
?>

<?php include 'head.php'; ?>

<style>
    :root {
        --verde-principal: #138b3c;
        --verde-oscuro: #0b5c28;
        --verde-muy-claro: #22c55e;
        --gris-texto: #222222;
        --gris-suave: #e5e7eb;
        --gris-medio: #6b7280;
        --blanco: #ffffff;
        --sombra-suave: 0 8px 18px rgba(0,0,0,0.06);
        --sombra-fuerte: 0 12px 24px rgba(0,0,0,0.12);
    }

    /* MAIN */
    .seccion-contenido { padding: 4rem 0; }
    .contenedor-max { max-width: 1200px; margin: 0 auto; padding: 0 2rem; }
    .seccion-contenido-h2 { 
        color: var(--verde-principal); 
        font-size: 2.2rem; 
        margin-bottom: 1rem; 
        text-align: center; 
    }

    .texto-intro {
        background: var(--gris-suave);
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        border-left: 4px solid var(--verde-principal);
        text-align: center;
        font-size: 1.1rem;
    }

    .bloque-info {
        margin-bottom: 3rem;
        padding: 2rem;
        background: var(--blanco);
        border-radius: 12px;
        box-shadow: var(--sombra-suave);
    }

    .bloque-info h3 {
        color: var(--verde-principal);
        margin-bottom: 1.5rem;
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .tabla-oferta, .tabla-contacto {
        overflow-x: auto;
        margin-top: 1rem;
        border-radius: 8px;
    }

    .tabla-oferta table, .tabla-contacto table {
        width: 100%;
        border-collapse: collapse;
        background: var(--blanco);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: var(--sombra-suave);
    }

    .tabla-oferta th, .tabla-contacto th {
        background: var(--verde-principal);
        color: var(--blanco);
        padding: 1rem 0.8rem;
        text-align: left;
        font-weight: 600;
    }

    .tabla-oferta td, .tabla-contacto td {
        padding: 1rem 0.8rem;
        border-bottom: 1px solid var(--gris-suave);
    }

    .tabla-oferta tbody tr:hover, .tabla-contacto tbody tr:hover {
        background: var(--verde-muy-claro);
    }

    /* === ESTILO LIBROS ESO PARA ACTIVIDADES COMPLEMENTARIAS === */
    .libros-acciones {
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-descargar-pdf {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.8rem 1.6rem;
        border-radius: 999px;
        background: var(--verde-principal);
        color: var(--blanco);
        text-decoration: none;
        font-weight: 600;
        border: 2px solid var(--verde-principal);
        transition: all 0.2s ease;
        font-size: 1rem;
    }

    .btn-descargar-pdf:hover {
        background: var(--blanco);
        color: var(--verde-principal);
        transform: translateY(-1px);
    }

    .libros-vista-previa {
        margin-top: 1.5rem;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: var(--sombra-suave);
    }

    .visor-pdf {
        width: 100%;
        height: 80vh;
        border: none;
        border-radius: 8px;
        box-shadow: var(--sombra-suave);
        display: block;
    }

    .visor-pdf.pequeña {
        height: 50vh;
    }

    .documentos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .documento-item {
        background: var(--blanco);
        border-radius: 8px;
        box-shadow: var(--sombra-suave);
        transition: transform 0.2s ease;
        overflow: hidden;
    }

    .documento-item:hover { transform: translateY(-2px); }

    .btn-documento {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.2rem;
        text-decoration: none;
        color: var(--gris-texto);
        border-radius: 0;
        transition: all 0.2s ease;
        width: 100%;
    }

    .btn-documento:hover {
        background: var(--verde-principal);
        color: var(--blanco);
    }

    .resultados .btn-documento {
        background: linear-gradient(135deg, var(--verde-principal), var(--verde-oscuro));
        color: var(--blanco);
    }

    @media (max-width: 768px) {
        .seccion-contenido-h2 { font-size: 1.8rem; }
        .bloque-info { padding: 1.5rem; }
        .tabla-oferta table, .tabla-contacto table { font-size: 0.9rem; }
        .visor-pdf { height: 60vh; }
        .visor-pdf.pequeña { height: 40vh; }
    }
</style>

<main>
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="seccion-contenido-h2">Información a las familias</h2>
            <p class="texto-intro">Cumpliendo la Resolución conjunta de 4 de diciembre de 2023, ponemos a su disposición toda la información del centro de forma organizada y accesible.</p>

            <!-- 1. OFERTA EDUCATIVA -->
            <div class="bloque-info">
                <h3><i class="fas fa-graduation-cap"></i> Oferta Educativa</h3>
                <div class="tabla-oferta">
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
                                        <?php if($oferta['etapa'] == 'ESO'): ?>
                                            <br>1º a 4º (grupos diversificación)
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($oferta['enlace_detalles'])): ?>
                                            <a href="<?php echo htmlspecialchars($oferta['enlace_detalles']); ?>" target="_blank">
                                                Ver detalles
                                            </a>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($oferta['detalles']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($oferta['horario'] ?? '-'); ?></td>
                                    <td>
                                        <?php if (!empty($oferta['itinerarios'])): ?>
                                            <?php 
                                            $itinerarios = explode("\n", $oferta['itinerarios']);
                                            echo '<ul style="margin: 0; padding-left: 1rem;">';
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
                                    <td><strong>ESO</strong><br>1º a 4º (grupos diversificación)</td>
                                    <td>Enseñanzas básicas obligatorias</td>
                                    <td>8:30 a 14:15 h</td>
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

            <!-- 2. CONTACTO -->
            <div class="bloque-info">
                <h3><i class="fas fa-phone"></i> Contacto Dirección y Administración</h3>
                <div class="tabla-contacto">
                    <table>
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Dirección</th>
                                <th>Horario</th>
                                <th>Teléfono</th>
                                <th>Fax</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Datos Generales</strong></td>
                                <td><?php echo htmlspecialchars($datos_centro['direccion']); ?></td>
                                <td><?php echo htmlspecialchars($datos_centro['horario']); ?></td>
                                <td><?php echo htmlspecialchars($datos_centro['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($datos_centro['fax']); ?></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td><strong>Dirección</strong></td>
                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                <td><a href="mailto:<?php echo htmlspecialchars($datos_centro['email_direccion']); ?>">
                                    <?php echo htmlspecialchars($datos_centro['email_direccion']); ?>
                                </a></td>
                            </tr>
                            <tr>
                                <td><strong>Secretaría</strong></td>
                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                <td><a href="mailto:<?php echo htmlspecialchars($datos_centro['email_secretaria']); ?>">
                                    <?php echo htmlspecialchars($datos_centro['email_secretaria']); ?>
                                </a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. PROGRAMAS Y PROYECTOS -->
            <div class="bloque-info">
                <h3><i class="fas fa-project-diagram"></i> Programas y Proyectos Educativos</h3>
                <p>Consulta los programas y proyectos que se desarrollan en nuestro centro:</p>
                <div class="libros-acciones">
                    <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/index.php/proyectos-de-centro/" class="btn-descargar-pdf" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Ver Proyectos de Centro
                    </a>
                </div>
            </div>

            <!-- 4. DOCUMENTOS -->
            <div class="bloque-info">
                <h3><i class="fas fa-file-pdf"></i> Documentación del Centro</h3>
                <div class="documentos-grid">
                    <?php if (!empty($documentos_db)): ?>
                        <?php foreach ($documentos_db as $doc): ?>
                        <div class="documento-item <?php echo strpos($doc['titulo'], 'Resultados') !== false ? 'resultados' : ''; ?>">
                            <a href="<?php echo htmlspecialchars($doc['url']); ?>" class="btn-documento" target="_blank" rel="noopener">
                                <i class="fas fa-file-pdf"></i>
                                <span><?php echo htmlspecialchars($doc['titulo']); ?></span>
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="documento-item">
                            <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/10/Proyecto-Educativo-del-Centro2425.pdf" class="btn-documento" target="_blank" rel="noopener">
                                <i class="fas fa-file-pdf"></i>
                                <span>Proyecto Educativo</span>
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                        <div class="documento-item">
                            <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/10/NORMAS-DE-CONVIVENCIA-IES-LA-ARBOLEDA.pdf" class="btn-documento" target="_blank" rel="noopener">
                                <i class="fas fa-file-pdf"></i>
                                <span>Normas de Convivencia</span>
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                        <div class="documento-item resultados">
                            <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/index.php/resultados-academicos/" class="btn-documento" target="_blank">
                                <i class="fas fa-chart-bar"></i>
                                <span>Resultados Académicos</span>
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                        <div class="documento-item">
                            <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2023/11/Libros-ESO-curso-23-24.pdf" class="btn-documento" target="_blank" rel="noopener">
                                <i class="fas fa-book"></i>
                                <span>Libros de Texto ESO</span>
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 5. ACTIVIDADES COMPLEMENTARIAS - ESTILO LIBROS ESO -->
            <div class="bloque-info">
                <h3><i class="fas fa-calendar-alt"></i> Actividades Complementarias</h3>
                
                <!-- Botón de descarga -->
                <div class="libros-acciones">
                    <?php if ($actividad): ?>
                        <a href="<?php echo htmlspecialchars($actividad['url']); ?>" 
                           class="btn-descargar-pdf" 
                           target="_blank" 
                           rel="noopener">
                            <i class="fas fa-download"></i> Programación 2025-26
                        </a>
                    <?php else: ?>
                        <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2025/10/WEB-Programacion-Actividades-complementarias-y-extraescolares-2025_26.docx.pdf" 
                           class="btn-descargar-pdf" 
                           target="_blank" 
                           rel="noopener">
                            <i class="fas fa-download"></i> Programación 2025-26
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Vista previa PDF - IGUAL QUE LIBROS ESO -->
                <div class="libros-vista-previa">
                    <?php if ($actividad): ?>
                        <iframe 
                            src="<?php echo htmlspecialchars($actividad['url']); ?>" 
                            class="visor-pdf"
                            title="Actividades complementarias 2025-26">
                        </iframe>
                    <?php else: ?>
                        <iframe 
                            src="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2025/10/WEB-Programacion-Actividades-complementarias-y-extraescolares-2025_26.docx.pdf" 
                            class="visor-pdf"
                            title="Actividades complementarias 2025-26">
                        </iframe>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php 
// Cerrar conexión
$conexion->close();
?>

<?php include 'footer.php'; ?>
