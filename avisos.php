<?php include 'head.php'; ?>

<?php
include("conexion.php");

// Consulta para TODOS los avisos ordenados por fecha DESC
$sql = "SELECT a.*, u.nombre as ultima_edicion_usuario_nombre
        FROM avisos a 
        LEFT JOIN usuarios u ON a.ultima_edicion_usuario_id = u.id 
        ORDER BY a.importante DESC, a.fecha DESC";
$resultado = $conexion->query($sql);
?>

<!-- HEADER AVISOS (estructura AMPA) -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-users" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Avisos del Centro</h1>
                <p class="hero-subtitulo-universal">Comunicaciones oficiales, plazos importantes y novedades administrativas.</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL -->
<main class="info_avisos_pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="info_avisos_titulo">Todos los Avisos</h2>

            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <div class="info_avisos_lista">
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <div class="info_avisos_item <?php echo $fila['importante'] ? 'info_avisos_importante' : ''; ?>">
                            <?php if ($fila['importante']): ?>
                                <div class="info_avisos_badge">¡IMPORTANTE!</div>
                            <?php endif; ?>

                            <div class="info_avisos_contenido">
                                <p class="info_avisos_fecha">
                                    <?php echo date('d/m/Y', strtotime($fila['fecha'])); ?>
                                    <?php if (!empty($fila['ultima_edicion_usuario_nombre'])): ?>
                                        <br><small style="color: #666;"><?php echo htmlspecialchars($fila['ultima_edicion_usuario_nombre']); ?></small>
                                    <?php endif; ?>
                                </p>

                                <h3 class="info_avisos_titulo_item"><?php echo htmlspecialchars($fila['titulo']); ?></h3>
                                <p class="info_avisos_texto"><?php echo nl2br(htmlspecialchars($fila['texto'])); ?></p>

                                <?php if (!empty($fila['enlace'])): ?>
                                    <a href="<?php echo htmlspecialchars($fila['enlace']); ?>" class="info_avisos_enlace" target="_blank">
                                        Ver documento →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="info_avisos_sin_contenido">
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
