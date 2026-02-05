<?php
include("conexion.php");

// ✅ TODOS los registros activos de Bachillerato (SIN LIMIT 1)
$sql = "SELECT id, titulo, ruta_pdf, fecha FROM matriculacion_bachillerato WHERE activo = 1 ORDER BY fecha DESC";
$resultado = $conexion->query($sql);
$conexion->close();
?>

<?php include 'head.php'; ?>

<!-- HEADER Bachillerato -->
<section class="matricula-contenido">
    <div class="contenedor-max">
        <div class="avisos-layout">
            <div class="avisos-logo">
                <!-- ✅ Icono Bachillerato -->
                <i class="fas fa-user-graduate" style="font-size: 3rem; color: var(--verde-principal);"></i>
            </div>
            <div class="avisos-texto">
                <h2>Matriculación Bachillerato</h2>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO - MÚLTIPLES DOCUMENTOS BACHILLERATO -->
<main>
    <?php if ($resultado && $resultado->num_rows > 0): ?>
        <section class="seccion-contenido">
            <div class="contenedor-max">
                <h3 style="color: var(--verde-principal); margin-bottom: 1.5rem;">Documentos Oficiales Bachillerato</h3>
                <div class="lista-avisos">
                    <?php while ($bach = $resultado->fetch_assoc()): ?>
                        <div class="aviso-item">
                            <div class="aviso-contenido">
                                <!-- FECHA DESDE BD -->
                                <p class="aviso-fecha">
                                    <i class="fas fa-calendar"></i> 
                                    <?php echo date('d/m/Y', strtotime($bach['fecha'])); ?>
                                </p>
                                
                                <!-- TÍTULO DESDE BD -->
                                <h3 class="aviso-titulo">
                                    📄 <?php echo htmlspecialchars($bach['titulo']); ?>
                                </h3>
                                
                                <!-- PDF DESDE BD -->
                                <div class="pdf-actions">
                                    <a href="<?php echo htmlspecialchars($bach['ruta_pdf']); ?>" class="aviso-enlace" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> Abrir PDF Oficial
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>

        <section class="seccion-contenido">
            <div class="contenedor-max">
                <h2 class="seccion-contenido-h2">Información Matriculación Bachillerato</h2>
                <div class="info-grid">
                    <div class="info-card">
                        <i class="fas fa-clock"></i>
                        <h4>Horario Secretaría</h4>
                        <p>Lunes a Viernes<br><strong>09:30 - 12:00 h</strong></p>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-university"></i>
                        <h4>Cuenta Pagos</h4>
                        <p><strong>CAIXABANK</strong><br>ES09 2100 6366 9713 0018 0224</p>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>Importante</h4>
                        <p><strong>Importe exacto</strong><br>No se devuelve dinero sobrante</p>
                    </div>
                </div>
            </div>
        </section>
    <?php else: ?>
        <section class="seccion-contenido">
            <div class="contenedor-max">
                <div class="sin-contenido">
                    <i class="fas fa-info-circle"></i>
                    <h3>No hay documentos Bachillerato disponibles</h3>
                    <p>Contacta con secretaría para información de matrícula.</p>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

<style>
.pdf-actions .aviso-enlace { 
    background: linear-gradient(135deg, var(--verde-principal), var(--verde-oscuro));
    color: var(--blanco) !important; 
    padding: 1rem 2rem; 
    border-radius: 25px; 
    text-decoration: none; 
    display: inline-flex; 
    align-items: center; 
    gap: 0.5rem; 
    font-weight: 600; 
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(19,139,60,0.3);
    border: 2px solid transparent;
}

.pdf-actions .aviso-enlace:hover { 
    transform: translateY(-3px); 
    box-shadow: 0 8px 25px rgba(19,139,60,0.4);
    background: linear-gradient(135deg, var(--verde-oscuro), var(--verde-principal));
}

.info-card i {
    color: var(--verde-principal);
    font-size: 2.5rem;
    margin-bottom: 1rem;
    display: block;
}

.info-card h4 {
    color: var(--verde-principal);
}
</style>

<script src="script.js"></script>
</body>
</html>
