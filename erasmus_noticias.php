<?php
// PÁGINA DETALLE NOTICIA ERASMUS 
// Muestra noticia individual ID con multimedia completo
include("conexion.php"); // Conexión MySQLi preparada
// VALIDACIÓN ENTRADA: ID desde GET sanitizado
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: erasmus.php"); // Redirigir si ID inválido
    exit;
}

// CONSULTA SEGURA: Prepared statement + activo = 1
$sql = "SELECT e.*, u.nombre as ultima_edicion_usuario_nombre
        FROM erasmus_news e 
        LEFT JOIN usuarios u ON e.ultima_edicion_usuario_id = u.id 
        WHERE e.id = ? AND e.activo = 1"; // Solo noticias publicadas

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);     // 'i' = integer ID
$stmt->execute();
$resultado = $stmt->get_result();

// VERIFICAR EXISTENCIA: Exactamente 1 fila esperada
if ($resultado && $resultado->num_rows === 1) {
    $noticia = $resultado->fetch_assoc(); // Datos completos noticia
} else {
    $noticia = null; // Noticia eliminada o no existe
}
$stmt->close();
?>

<!-- HEADER GLOBAL PÚBLICO -->
<?php include 'head.php'; ?>

<!-- HERO PRINCIPAL -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-plane icono_universal"></i> <!-- Icono movilidad -->
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Erasmus</h1>
                <p class="hero-subtitulo-universal">Movilidades Erasmus+</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL DETALLE -->
<main class="erasmus_noticias_detalle">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <?php if ($noticia): ?> <!-- NOTICIA ENCONTRADA -->
                <article class="erasmus_noticias_post">

                    <!-- IMAGEN PRINCIPAL OPCIONAL -->
                    <?php if (!empty($noticia['imagen'])): ?>
                        <figure class="erasmus_noticias_imagen">
                            <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                 alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                            <figcaption>Imagen de la movilidad</figcaption>
                        </figure>
                    <?php endif; ?>

                    <!-- HEADER METADATOS -->
                    <header class="erasmus_noticias_header">
                        <!-- FECHA SEMÁNTICA HTML5 -->
                        <time class="erasmus_noticias_fecha" 
                              datetime="<?php echo date('Y-m-d', strtotime($noticia['fecha'])); ?>">
                                  <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
                                  <?php if (!empty($noticia['ultima_edicion_nombre'])): ?>
                                <br><small class="letra-666"><?php echo htmlspecialchars($noticia['ultima_edicion_nombre']); ?></small> <!-- Nombre editor -->
                            <?php endif; ?>
                        </time>

                        <!-- AUDITORÍA EDITOR -->
                        <?php if (!empty($noticia['ultima_edicion_usuario_nombre'])): ?>
                            <br>
                            <small class="letra-666">
                                <?php echo htmlspecialchars($noticia['ultima_edicion_usuario_nombre']); ?>
                            </small>
                        <?php endif; ?>

                        <!-- TÍTULO H1 PRINCIPAL -->
                        <h1 class="erasmus_noticias_titulo">
                            <?php echo htmlspecialchars($noticia['titulo']); ?>
                        </h1>
                    </header>

                    <!-- CONTENIDO COMPLETO HTML -->
                    <div class="erasmus_noticias_contenido">
                        <?php echo $noticia['contenido']; ?> <!-- HTML permitido -->
                    </div>

                    <!-- VIDEO EMBEBIDO OPCIONAL -->
                    <?php if (!empty($noticia['video'])): ?>
                        <figure class="erasmus_noticias_video_wrap">
                            <video controls class="erasmus_noticias_video">
                                <source src="<?php echo htmlspecialchars($noticia['video']); ?>" 
                                        type="video/mp4">
                                Tu navegador no soporta vídeo HTML5.
                            </video>
                        </figure>
                    <?php endif; ?>

                    <!-- DOCUMENTO PDF DESCARGABLE -->
                    <?php if (!empty($noticia['pdf'])): ?>
                        <div class="erasmus_noticias_documento">
                            <a href="<?php echo htmlspecialchars($noticia['pdf']); ?>" 
                               target="_blank" 
                               class="erasmus_noticias_btn_pdf">
                                <i class="fas fa-file-pdf"></i> Descargar documento oficial
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- NAVEGACIÓN REGRESO -->
                    <nav class="erasmus_noticias_navegacion">
                        <a href="erasmus.php" class="erasmus_noticias_volver">
                            ← Volver al listado de movilidades
                        </a>
                    </nav>
                </article>

            <?php else: ?> <!-- NOTICIA NO ENCONTRADA -->
                <article class="erasmus_noticias_error">
                    <i class="fas fa-globe"></i>
                    <h3>Noticia no encontrada</h3>
                    <p>Es posible que la movilidad haya sido eliminada.</p>
                    <a href="erasmus.php" class="erasmus_noticias_volver">
                        ← Volver a Erasmus
                    </a>
                </article>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
$conexion->close(); // CERRAR CONEXIÓN 
include 'footer.php'; // FOOTER GLOBAL
?>
