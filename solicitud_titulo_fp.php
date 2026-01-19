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

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { 
        font-family: var(--fuente-base);
        background: #fafafa; 
        color: var(--gris-texto);
        line-height: 1.6;
    }

    /* CONTENIDO PRINCIPAL */
    .seccion-contenido {
        background: var(--blanco); 
        border-radius: 12px;
        box-shadow: var(--sombra-suave); 
        padding: 2rem;
        margin: 2rem auto; 
        max-width: 1150px;
    }

    .solicitud-contenido { padding: 2rem 0; }
    .solicitud-layout {
        display: flex; gap: 2rem; align-items: flex-start;
        background: var(--blanco); padding: 2.5rem;
        border-radius: 12px; box-shadow: var(--sombra-suave);
        margin: 2rem 0; border-left: 5px solid var(--verde-principal);
    }
    .solicitud-icono {
        flex-shrink: 0; text-align: center; padding: 1rem;
        min-width: 300px;
    }
    .solicitud-icono img, .solicitud-icono i {
        font-size: 6rem; color: var(--verde-principal);
        width: 100%; max-width: 300px; height: 220px;
        border-radius: 12px; box-shadow: var(--sombra-fuerte);
        object-fit: cover;
    }
    .solicitud-texto { flex: 1; }
    .solicitud-texto h1 {
        color: var(--verde-principal); font-size: 2.2rem;
        margin-bottom: 1rem; text-transform: uppercase;
        letter-spacing: 0.03em; font-weight: 700;
    }
    .texto-principal {
        color: var(--gris-texto); font-size: 1.1rem;
        line-height: 1.7; margin-bottom: 2rem; text-align: justify;
    }
    .solicitud-horario {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        padding: 1.5rem; border-radius: 10px;
        border-left: 4px solid var(--verde-principal);
        margin: 2rem 0;
    }
    .solicitud-tasa {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        padding: 1.5rem; border-radius: 10px;
        border-left: 4px solid #3b82f6;
        margin: 2rem 0;
    }
    .solicitud-acciones {
        display: flex; gap: 1rem; flex-wrap: wrap;
    }
    .btn-normativa, .btn-tasa, .btn-autorizacion {
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
    .btn-autorizacion {
        background: linear-gradient(135deg, var(--verde-principal), var(--verde-oscuro));
        color: white !important;
    }
    .btn-autorizacion:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(19,139,60,0.4);
    }
    .aviso-importante {
        background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
        border-left: 5px solid #ef4444; padding: 1.5rem;
        border-radius: 8px; margin: 2rem 0;
        box-shadow: 0 4px 15px rgba(239,68,68,0.15);
    }
    .info-solicitud {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem; margin-top: 3rem; padding-top: 2rem;
        border-top: 2px solid var(--gris-suave);
    }
    .info-card-solicitud {
        background: var(--blanco); padding: 2rem;
        border-radius: 12px; text-align: center;
        box-shadow: var(--sombra-suave); 
        border-top: 4px solid var(--verde-muy-claro);
        transition: all 0.3s ease;
    }
    .info-card-solicitud:hover {
        transform: translateY(-5px); box-shadow: var(--sombra-fuerte);
    }
    .info-card-solicitud i {
        font-size: 3rem; color: var(--verde-principal);
        margin-bottom: 1rem; display: block;
    }

    @media (max-width: 768px) {
        .solicitud-layout { flex-direction: column; text-align: center; padding: 1.5rem; }
        .solicitud-acciones { flex-direction: column; align-items: center; }
        .btn-normativa, .btn-autorizacion { width: 100%; max-width: 300px; justify-content: center; }
        .info-solicitud { grid-template-columns: 1fr; }
    }
</style>

<?php 
include("conexion.php"); 

// UNA SOLA CONSULTA - AL PRINCIPIO
$sql_solicitud = "SELECT * FROM solicitudes_titulos WHERE tipo = 'FP' AND activo = 1 LIMIT 1";
$resultado_solicitud = $conexion->query($sql_solicitud);
$fila_solicitud = $resultado_solicitud ? $resultado_solicitud->fetch_assoc() : null;
$hay_media = $fila_solicitud && !empty($fila_solicitud['imagen']);
?>

<main>
    <!-- SECCIÓN 1 -->
    <section class="seccion-contenido">
        <div class="solicitud-contenido">
            <?php if ($fila_solicitud): ?>
                <?php if ($hay_media): ?>
                    <div class="solicitud-layout">
                        <div class="solicitud-icono">
                            <img src="data:<?php echo $fila_solicitud['tipo_imagen']; ?>;base64,<?php echo base64_encode($fila_solicitud['imagen']); ?>" alt="Título FP">
                        </div>
                        <div class="solicitud-texto">
                            <h1><?php echo htmlspecialchars($fila_solicitud['titulo']); ?></h1>
                            <div class="texto-principal"><?php echo nl2br(htmlspecialchars($fila_solicitud['texto'])); ?></div>
                            <div class="solicitud-acciones">
                                <?php if (!empty($fila_solicitud['enlace_normativa'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila_solicitud['enlace_normativa']); ?>" target="_blank" class="btn-normativa">
                                        <i class="fas fa-file-pdf"></i> Normativa oficial
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($fila_solicitud['enlace_autorizacion'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila_solicitud['enlace_autorizacion']); ?>" target="_blank" class="btn-autorizacion">
                                        <i class="fas fa-edit"></i> Autorización terceros
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="solicitud-layout">
                        <div class="solicitud-icono">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="solicitud-texto">
                            <h1><?php echo htmlspecialchars($fila_solicitud['titulo']); ?></h1>
                            <div class="texto-principal"><?php echo nl2br(htmlspecialchars($fila_solicitud['texto'])); ?></div>
                            <div class="solicitud-acciones">
                                <?php if (!empty($fila_solicitud['enlace_normativa'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila_solicitud['enlace_normativa']); ?>" target="_blank" class="btn-normativa">
                                        <i class="fas fa-file-pdf"></i> Normativa oficial
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($fila_solicitud['enlace_autorizacion'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila_solicitud['enlace_autorizacion']); ?>" target="_blank" class="btn-autorizacion">
                                        <i class="fas fa-edit"></i> Autorización terceros
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- HORARIO -->
                <div class="solicitud-horario">
                    <i class="fas fa-clock"></i>
                    <h3>📅 Horario recogida títulos</h3>
                    <p><strong><?php echo htmlspecialchars($fila_solicitud['horario'] ?? 'L-V 9:30-12:00'); ?></strong></p>
                    <p><em>En Secretaría del Centro, previa identificación con DNI/NIE.</em></p>
                </div>

                <!-- TASA PAGO -->
                <div class="solicitud-tasa">
                    <i class="fas fa-euro-sign"></i>
                    <h3>💰 Pago tasa modelo 030</h3>
                    <p><strong>Tasa aproximada: 51,49€</strong></p>
                    <p><em>Pago online en <a href="https://sede.comunidad.madrid" target="_blank">sede.comunidad.madrid</a></em></p>
                </div>

                <!-- AVISO IMPORTANTE -->
                <div class="aviso-importante">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>IMPORTANTE:</strong> Necesitará justificante de pago del modelo 030 + DNI/NIE. Verifique disponibilidad en secretaría antes de tramitar.
                </div>

                <!-- INFO CARDS -->
                <div class="info-solicitud">
                    <div class="info-card-solicitud">
                        <i class="fas fa-id-card"></i>
                        <h4>Documentación</h4>
                        <p>Justificante modelo 030 + DNI/NIE original.</p>
                    </div>
                    <div class="info-card-solicitud">
                        <i class="fas fa-clock"></i>
                        <h4>Secretaría</h4>
                        <p><?php echo htmlspecialchars($fila_solicitud['horario'] ?? '9:30-12:00 L-V'); ?>.</p>
                    </div>
                    <div class="info-card-solicitud">
                        <i class="fas fa-credit-card"></i>
                        <h4>Pago Tasa</h4>
                        <p>Modelo 030: ~51,49€ online Comunidad Madrid.</p>
                    </div>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: 4rem 2rem; color: var(--gris-medio);">
                    <i class="fas fa-file-search" style="font-size: 5rem; color: var(--gris-suave); margin-bottom: 2rem; display: block;"></i>
                    <h1>Solicitud Título FP</h1>
                    <p>No hay información disponible en este momento.</p>
                    <p><strong>Secretaría:</strong> 916 43 99 91</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php 
// UNA SOLA VEZ al final
$conexion->close();
?>

<?php include 'footer.php'; ?>
</body>
</html>
