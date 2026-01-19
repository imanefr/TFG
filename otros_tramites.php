<?php
include("conexion.php");

// Consulta otros trámites (por ejemplo, últimos 8)
$sql = "SELECT titulo, contenido, fecha, enlace 
        FROM otros_tramites 
        ORDER BY fecha DESC 
        LIMIT 8";
$resultado = $conexion->query($sql);

$tramites = [];
while ($fila = $resultado->fetch_assoc()) {
    $tramites[] = $fila;
}
?>
               <?php include 'head.php'; ?>


    <!-- CABECERA DE SECCIÓN -->
    <section class="erasmus-contenido">
        <div class="contenedor-max">
            <div class="avisos-layout">
                <div class="avisos-logo">
                    <i class="fas fa-file-signature" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
                </div>
                <div class="avisos-texto">
                    <h2>OTROS TRÁMITES</h2>
                    <p>Gestiones administrativas de secretaría</p>
                </div>
            </div>
        </div>
    </section>

    <!-- LISTA DE TRÁMITES (mismo estilo que Últimas Movilidades) -->
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="seccion-contenido-h2">Información y procedimientos</h2>
            
            <?php if (!empty($tramites)): ?>
                <div class="lista-avisos">
                    <?php foreach ($tramites as $tramite): ?>
                        <div class="aviso-item">
                            <div class="aviso-contenido">
                                <p class="aviso-fecha">
                                    <?php echo date('d/m/Y', strtotime($tramite['fecha'])); ?>
                                </p>
                                <h3 class="aviso-titulo">
                                    <?php echo htmlspecialchars($tramite['titulo']); ?>
                                </h3>
                                <p class="aviso-texto">
                                    <?php echo substr(strip_tags($tramite['contenido']), 0, 200); ?>...
                                </p>
                                <?php if (!empty($tramite['enlace'])): ?>
                                    <a href="<?php echo htmlspecialchars($tramite['enlace']); ?>" 
                                       class="aviso-enlace" target="_blank">
                                        Leer completo →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="sin-contenido">
                    <i class="fas fa-info-circle"></i>
                    <h3>No hay trámites disponibles</h3>
                    <p>Próximamente se publicará información sobre otros trámites de secretaría.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

                  <?php include 'footer.php'; ?>


    <script src="script.js"></script>
</body>
</html>
