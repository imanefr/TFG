<?php
include("conexion.php");

// Obtener PDF e info desde base de datos (FP)
$sql = "SELECT titulo, descripcion, ruta_pdf, fecha FROM matriculacion_fp WHERE activo = 1 ORDER BY fecha DESC LIMIT 1";
$resultado = $conexion->query($sql);
$fp = $resultado->fetch_assoc();
?>

               <?php include 'head.php'; ?>

        <!-- HEADER AVISOS ESTILO -->
        <section class="matricula-contenido">
            <div class="contenedor-max">
                <div class="avisos-layout">
                    <div class="avisos-logo">
                        <i class="fas fa-tools" style="font-size: 3rem; color: var(--verde-principal);"></i>
                    </div>
                    <div class="avisos-texto">
                        <h2><?php echo $fp ? htmlspecialchars($fp['titulo']) : 'Matriculación FP'; ?></h2>
                        <p><?php echo $fp ? htmlspecialchars($fp['descripcion']) : 'Horario secretaría: L-V 09:30-12:00'; ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CONTENIDO PRINCIPAL - ESTILO AVISOS -->
        <main>
            <?php if ($fp && $fp['ruta_pdf']): ?>
                <!-- CARD PDF PRINCIPAL (estilo aviso-item) -->
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <div class="lista-avisos">
                            <div class="aviso-item">
                                <div class="aviso-contenido">
                                    <p class="aviso-fecha">
                                        <i class="fas fa-file-pdf"></i> 
                                        <?php echo $fp['fecha'] ? date('d/m/Y', strtotime($fp['fecha'])) : date('d/m/Y'); ?>
                                    </p>
                                    <h3 class="aviso-titulo">📄 Documento Oficial Matrícula FP</h3>
                                    <p class="aviso-texto">Descarga el PDF oficial con toda la información y formularios de matrícula de Formación Profesional.</p>

                                    <div class="pdf-actions">
                                        <a href="descargar_pdf_fp.php?id=1" class="aviso-enlace" target="_blank">
                                            <i class="fas fa-download"></i> Descargar PDF Oficial
                                        </a>
                                        <a href="ver_pdf_fp.php?id=1" class="ver-pdf-btn" target="_blank">
                                            <i class="fas fa-eye"></i> Ver Online
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- INFO ADICIONAL (estilo seccion-contenido) -->
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <h2 class="seccion-contenido-h2">Información Adicional</h2>

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

                        <div class="pasos-matricula">
                            <h3>Documentos Necesarios FP</h3>
                            <div class="pasos-lista">
                                <div class="paso-item">
                                    <span class="paso-numero">1</span>
                                    <div>Formulario matrícula FP (en PDF oficial)</div>
                                </div>
                                <div class="paso-item">
                                    <span class="paso-numero">2</span>
                                    <div>DNI / NIE (original + fotocopia)</div>
                                </div>
                                <div class="paso-item">
                                    <span class="paso-numero">3</span>
                                    <div>Título requerido (Grado Medio/Superior)</div>
                                </div>
                                <div class="paso-item importante">
                                    <span class="paso-numero">★</span>
                                    <div><strong>Certificado titulación anterior (original)</strong></div>
                                </div>
                                <div class="paso-item">
                                    <span class="paso-numero">4</span>
                                    <div>Historial académico (último ciclo)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php else: ?>
                <section class="seccion-contenido">
                    <div class="contenedor-max">
                        <div class="sin-contenido">
                            <i class="fas fa-info-circle"></i>
                            <h3>No hay documentos disponibles</h3>
                            <p>Contacta con secretaría para información de matrícula FP.</p>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        </main>

                       <?php include 'footer.php'; ?>


        <script src="script.js"></script>
    </body>
</html>
