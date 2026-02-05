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
        --fuente-base: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    /* CONVALIDACIÓN - ESPECÍFICO */
    .convalidacion-contenido { padding: 2rem 0; }
    .convalidacion-layout {
        display: flex; gap: 2rem; align-items: flex-start;
        background: var(--blanco); padding: 2.5rem;
        border-radius: 12px; box-shadow: var(--sombra-suave);
        margin: 2rem 0; border-left: 5px solid var(--verde-principal);
    }
    .convalidacion-logo {
        flex-shrink: 0; text-align: center; padding: 1rem;
        min-width: 300px;
    }
    .convalidacion-logo img, 
    .convalidacion-logo iframe {
        width: 100%; max-width: 300px; height: 220px;
        border-radius: 12px; box-shadow: var(--sombra-fuerte);
        object-fit: cover;
    }
    .convalidacion-texto { flex: 1; }
    .convalidacion-texto h1 {
        color: var(--verde-principal); font-size: 2.2rem;
        margin-bottom: 1rem; text-transform: uppercase;
        letter-spacing: 0.03em; font-weight: 700;
    }
    .texto-principal {
        color: var(--gris-texto); font-size: 1.1rem;
        line-height: 1.7; margin-bottom: 2rem; text-align: justify;
    }
    .convalidacion-acciones {
        display: flex; gap: 1rem; flex-wrap: wrap;
    }
    .btn-normativa, .btn-formulario {
        display: inline-flex; align-items: center; gap: 0.6rem;
        padding: 1rem 2rem; border-radius: 25px; font-weight: 700;
        text-decoration: none; transition: all 0.3s ease;
        font-size: 0.95rem; text-transform: uppercase;
    }
    .btn-normativa {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white !important;
    }
    .btn-normativa:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,123,255,0.3);
    }
    .btn-formulario {
        background: linear-gradient(135deg, var(--verde-principal), var(--verde-oscuro));
        color: white !important;
    }
    .btn-formulario:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(19,139,60,0.4);
    }
    .convalidacion-sin-media {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 3rem 2.5rem; border-radius: 16px;
        margin: 2rem 0; text-align: center; box-shadow: var(--sombra-suave);
    }
    .convalidacion-icono {
        font-size: 4.5rem; color: var(--verde-principal);
        margin-bottom: 1.5rem; display: block;
    }
    .aviso-importante {
        background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
        border-left: 5px solid #ef4444; padding: 1.5rem;
        border-radius: 8px; margin: 2rem 0;
        box-shadow: 0 4px 15px rgba(239,68,68,0.15);
    }
    .info-convalidacion {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem; margin-top: 3rem; padding-top: 2rem;
        border-top: 2px solid var(--gris-suave);
    }
    .info-card-convalidacion {
        background: var(--blanco); padding: 2rem;
        border-radius: 12px; text-align: center;
        box-shadow: var(--sombra-suave); 
        border-top: 4px solid var(--verde-muy-claro);
        transition: all 0.3s ease;
    }
    .info-card-convalidacion:hover {
        transform: translateY(-5px); box-shadow: var(--sombra-fuerte);
    }
    .info-card-convalidacion i {
        font-size: 3rem; color: var(--verde-principal);
        margin-bottom: 1rem; display: block;
    }
    .sin-convalidacion {
        text-align: center; padding: 4rem 2rem;
        color: var(--gris-medio);
    }
    .sin-convalidacion i {
        font-size: 5rem; color: var(--gris-suave);
        margin-bottom: 2rem; display: block;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .convalidacion-layout { 
            flex-direction: column; text-align: center; 
            padding: 1.5rem;
        }
        .convalidacion-acciones { 
            flex-direction: column; align-items: center; 
        }
        .btn-normativa, .btn-formulario {
            width: 100%; max-width: 300px; justify-content: center;
        }
        .info-convalidacion { grid-template-columns: 1fr; }
    }
</style>

<?php include("conexion.php"); ?>

<main>
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <div class="convalidacion-contenido">
                <?php
                // Query específica para BACHILLERATO
                $sql_convalidacion = "SELECT titulo, texto, imagen, tipo_imagen, enlace_video, enlace_normativa, enlace_formulario 
                                    FROM convalidaciones WHERE tipo = 'BACHILLERATO' AND activo = 1 LIMIT 1";
                $resultado_convalidacion = $conexion->query($sql_convalidacion);

                if ($resultado_convalidacion && $fila_convalidacion = $resultado_convalidacion->fetch_assoc()) {
                    $hay_media = !empty($fila_convalidacion['imagen']) || !empty($fila_convalidacion['enlace_video']);
                ?>

                    <?php if ($hay_media): ?>
                        <!-- LAYOUT CON IMAGEN/VIDEO -->
                        <div class="convalidacion-layout">
                            <div class="convalidacion-logo">
                                <?php if (!empty($fila_convalidacion['imagen'])): ?>
                                    <img src="data:<?php echo $fila_convalidacion['tipo_imagen']; ?>;base64,<?php echo base64_encode($fila_convalidacion['imagen']); ?>" alt="Convalidación Bachillerato">
                                <?php elseif (!empty($fila_convalidacion['enlace_video'])): ?>
                                    <iframe src="<?php echo htmlspecialchars(str_replace('watch?v=', 'embed/', $fila_convalidacion['enlace_video'])); ?>" frameborder="0" allowfullscreen></iframe>
                                <?php endif; ?>
                            </div>
                            <div class="convalidacion-texto">
                                <h1><?php echo htmlspecialchars($fila_convalidacion['titulo']); ?></h1>
                                <div class="texto-principal"><?php echo nl2br(htmlspecialchars($fila_convalidacion['texto'])); ?></div>
                                <div class="convalidacion-acciones">
                                    <?php if (!empty($fila_convalidacion['enlace_normativa'])): ?>
                                        <a href="<?php echo htmlspecialchars($fila_convalidacion['enlace_normativa']); ?>" target="_blank" class="btn-normativa">
                                            <i class="fas fa-file-pdf"></i> Normativa oficial
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($fila_convalidacion['enlace_formulario'])): ?>
                                        <a href="<?php echo htmlspecialchars($fila_convalidacion['enlace_formulario']); ?>" target="_blank" class="btn-formulario">
                                            <i class="fas fa-edit"></i> Formulario solicitud
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- LAYOUT SIN MEDIA -->
                        <div class="convalidacion-sin-media">
                            <i class="fas fa-file-signature convalidacion-icono"></i>
                            <h1><?php echo htmlspecialchars($fila_convalidacion['titulo']); ?></h1>
                            <div class="texto-principal"><?php echo nl2br(htmlspecialchars($fila_convalidacion['texto'])); ?></div>
                            <div class="convalidacion-acciones">
                                <?php if (!empty($fila_convalidacion['enlace_normativa'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila_convalidacion['enlace_normativa']); ?>" target="_blank" class="btn-normativa">
                                        <i class="fas fa-file-pdf"></i> Normativa oficial
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($fila_convalidacion['enlace_formulario'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila_convalidacion['enlace_formulario']); ?>" target="_blank" class="btn-formulario">
                                        <i class="fas fa-edit"></i> Formulario solicitud
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- AVISO IMPORTANTE -->
                    <div class="aviso-importante">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>IMPORTANTE:</strong> NO SE RECOGERÁN NINGÚN FORMULARIO ESCRITO A MANO. Descargue, rellene y entregue el formulario digital.
                    </div>

                    <!-- INFO CARDS -->
                    <div class="info-convalidacion">
                        <div class="info-card-convalidacion">
                            <i class="fas fa-file-contract"></i>
                            <h4>Normativa oficial</h4>
                            <p>Regulación completa del procedimiento de convalidación de Bachillerato según la Comunidad de Madrid.</p>
                        </div>
                        <div class="info-card-convalidacion">
                            <i class="fas fa-download"></i>
                            <h4>Formulario digital</h4>
                            <p>Descargue el formulario oficial en PDF, rellénelo digitalmente y entréguelo en secretaría.</p>
                        </div>
                        <div class="info-card-convalidacion">
                            <i class="fas fa-clock"></i>
                            <h4>Plazos de presentación</h4>
                            <p>Consulte los plazos específicos en la normativa oficial y en secretaría del centro.</p>
                        </div>
                    </div>

                <?php } else { ?>
                    <div class="sin-convalidacion">
                        <i class="fas fa-file-search"></i>
                        <h1>Convalidación Bachillerato</h1>
                        <p>No hay información disponible en este momento.</p>
                        <p><strong>Secretaría:</strong> 916 43 99 91</p>
                    </div>
                <?php } 
                $conexion->close();
                ?>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
