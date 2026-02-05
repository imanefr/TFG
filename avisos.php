<?php include 'head.php'; ?>

<?php
include("conexion.php");

// Consulta para TODOS los avisos ordenados por fecha DESC
$sql = "SELECT * FROM avisos ORDER BY fecha DESC";
$resultado = $conexion->query($sql);
?>

<!-- HEADER AVISOS (estructura AMPA) -->
<section class="avisos-contenido">
    <div class="contenedor-max">
        <div class="avisos-layout">
            <div class="avisos-logo">
                <img src="img/avisos-icono.png" alt="Avisos del centro">
            </div>
            <div class="avisos-texto">
                <h2>Avisos del Centro</h2>
                <p>Comunicaciones oficiales, plazos importantes y novedades administrativas.</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL -->
<main>
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="seccion-contenido-h2">Todos los Avisos</h2>

            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <div class="lista-avisos">
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <div class="aviso-item <?php echo $fila['importante'] ? 'aviso-importante' : ''; ?>">
                            <?php if ($fila['importante']): ?>
                                <div class="aviso-badge">¡IMPORTANTE!</div>
                            <?php endif; ?>

                            <div class="aviso-contenido">
                                <p class="aviso-fecha"><?php echo date('d/m/Y H:i', strtotime($fila['fecha'])); ?></p>
                                <h3 class="aviso-titulo"><?php echo htmlspecialchars($fila['titulo']); ?></h3>
                                <p class="aviso-texto"><?php echo nl2br(htmlspecialchars($fila['texto'])); ?></p>

                                <?php if (!empty($fila['enlace'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila['enlace']); ?>" class="aviso-enlace" target="_blank">
                                        Ver documento →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="sin-contenido">
                    <i class="fas fa-info-circle"></i>
                    <h3>No hay avisos disponibles</h3>
                    <p>Revisa más tarde para nuevas comunicaciones oficiales.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php 
$conexion->close(); 
include 'footer.php'; 
?>
