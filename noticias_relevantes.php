<?php
include("conexion.php");

// Validar el id
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: relevante_ahora.php");
    exit;
}

// Consulta completa con prepared statement
$sql = "SELECT r.*, u.nombre as ultima_edicion_usuario_nombre
        FROM noticias r 
        LEFT JOIN usuarios u ON r.ultima_edicion_usuario_id = u.id 
        WHERE r.id = ? AND r.destacada = 1";

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
                <i class="fas fa-bookmark" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Noticias Relevantes</h1>
                <p class="hero-subtitulo-universal">Noticias relevantes para ti</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL -->
<main class="noticias_relevantes_detalle">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <?php if ($noticia): ?>
                <article class="noticias_relevantes_post">
                    <!-- Foto principal -->
                    <?php if (!empty($noticia['imagen'])): ?>
                        <figure class="noticias_relevantes_imagen">
                            <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                 alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                            <figcaption>Imagen de la noticia</figcaption>
                        </figure>
                    <?php endif; ?>

                    <!-- Meta datos -->
                    <header class="noticias_relevantes_header">
                        <time class="noticias_relevantes_fecha" datetime="<?php echo date('Y-m-d', strtotime($noticia['fecha'])); ?>">
                            <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
                        </time>
                        <?php if (!empty($noticia['ultima_edicion_usuario_nombre'])): ?>
                            <br><small style="color: #666;"><?php echo htmlspecialchars($noticia['ultima_edicion_usuario_nombre']); ?></small>
                        <?php endif; ?>
                        <h1 class="noticias_relevantes_titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h1>
                    </header>

                    <!-- Contenido -->
                    <div class="noticias_relevantes_contenido">
                        <?php echo $noticia['contenido']; ?>
                    </div>

                    <!-- Video -->
                    <?php if (!empty($noticia['video'])): ?>
                        <figure class="noticias_relevantes_video_wrap">
                            <video controls class="noticias_relevantes_video">
                                <source src="<?php echo htmlspecialchars($noticia['video']); ?>" type="video/mp4">
                                Tu navegador no soporta vídeo HTML5.
                            </video>
                        </figure>
                    <?php endif; ?>

                    <!-- PDF -->
                    <?php if (!empty($noticia['pdf'])): ?>
                        <div class="noticias_relevantes_documento">
                            <a href="<?php echo htmlspecialchars($noticia['pdf']); ?>" target="_blank" class="noticias_relevantes_btn_pdf">
                                <i class="fas fa-file-pdf"></i> Descargar documento oficial
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Navegación -->
                    <nav class="noticias_relevantes_navegacion">
                        <a href="relevante_ahora.php" class="noticias_relevantes_volver">
                            ← Volver a Noticias
                        </a>
                    </nav>
                </article>
            <?php else: ?>
                <article class="noticias_relevantes_error">
                    <i class="fas fa-globe"></i>
                    <h3>Noticia no encontrada</h3>
                    <p>Es posible que la noticia haya sido eliminada.</p>
                    <a href="relevante_ahora.php" class="noticias_relevantes_volver">← Volver a Noticias</a>
                </article>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php $conexion->close();
include 'footer.php'; ?>
