<?php
include("conexion.php");

// Validar el id
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: erasmus.php");
    exit;
}

// Consulta completa con prepared statement
$sql = "SELECT e.*, u.nombre as ultima_edicion_usuario_nombre
        FROM erasmus_news e 
        LEFT JOIN usuarios u ON e.ultima_edicion_usuario_id = u.id 
        WHERE e.id = ? AND e.activo = 1";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows === 1) {
    $noticia = $resultado->fetch_assoc();
} else {
    $noticia = null;
}
$stmt->close();
?>

<?php include 'head.php'; ?>

<!-- HERO -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-plane" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Erasmus</h1>
                <p class="hero-subtitulo-universal">Movilidades Erasmus+</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL -->
<main class="erasmus_noticias_detalle">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <?php if ($noticia): ?>
                <article class="erasmus_noticias_post">
                    <!-- Foto principal -->
                    <?php if (!empty($noticia['imagen'])): ?>
                        <figure class="erasmus_noticias_imagen">
                            <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                 alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                            <figcaption>Imagen de la movilidad</figcaption>
                        </figure>
                    <?php endif; ?>

                    <!-- Meta datos -->
                    <header class="erasmus_noticias_header">
                        <time class="erasmus_noticias_fecha" datetime="<?php echo date('Y-m-d', strtotime($noticia['fecha'])); ?>">
                            <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
                        </time>
                        <?php if (!empty($noticia['ultima_edicion_usuario_nombre'])): ?>
                            <br><small style="color: #666;"><?php echo htmlspecialchars($noticia['ultima_edicion_usuario_nombre']); ?></small>
                        <?php endif; ?>
                        <h1 class="erasmus_noticias_titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h1>
                    </header>

                    <!-- Contenido -->
                    <div class="erasmus_noticias_contenido">
                        <?php echo $noticia['contenido']; ?>
                    </div>

                    <!-- Video -->
                    <?php if (!empty($noticia['video'])): ?>
                        <figure class="erasmus_noticias_video_wrap">
                            <video controls class="erasmus_noticias_video">
                                <source src="<?php echo htmlspecialchars($noticia['video']); ?>" type="video/mp4">
                                Tu navegador no soporta vídeo HTML5.
                            </video>
                        </figure>
                    <?php endif; ?>

                    <!-- PDF -->
                    <?php if (!empty($noticia['pdf'])): ?>
                        <div class="erasmus_noticias_documento">
                            <a href="<?php echo htmlspecialchars($noticia['pdf']); ?>" target="_blank" class="erasmus_noticias_btn_pdf">
                                <i class="fas fa-file-pdf"></i> Descargar documento oficial
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Navegación -->
                    <nav class="erasmus_noticias_navegacion">
                        <a href="erasmus.php" class="erasmus_noticias_volver">
                            ← Volver al listado de movilidades
                        </a>
                    </nav>
                </article>
            <?php else: ?>
                <article class="erasmus_noticias_error">
                    <i class="fas fa-globe"></i>
                    <h3>Noticia no encontrada</h3>
                    <p>Es posible que la movilidad haya sido eliminada.</p>
                    <a href="erasmus.php" class="erasmus_noticias_volver">← Volver a Erasmus</a>
                </article>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php $conexion->close();
include 'footer.php'; ?>
