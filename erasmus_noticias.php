<?php
include("conexion.php");

// Validar el id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: erasmus.php");
    exit;
}

// Consulta completa con prepared statement
$sql = "SELECT titulo, contenido, fecha, imagen, video, pdf 
        FROM erasmus_news 
        WHERE id = ? AND activo = 1";
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

<main class="erasmus-noticias-pagina">
    <!-- HEADER ERASMUS -->
    <section class="seccion-hero-universal">
        <div class="contenedor-max">
            <div class="hero-layout-universal">
                <div class="hero-icono-universal">
                    <i class="fas fa-plane" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
                </div>
                <div class="hero-texto-universal">
                    <h1 class="hero-titulo-universal">erasmus</h1>
                    <p class="hero-subtitulo-universal">Movilidades Erasmus+</p>
                </div>
            </div>
        </div>
    </section>

    <section class="seccion-contenido">
        <div class="contenedor-max">
            <?php if ($noticia): ?>
                <article class="aviso-item">
                    <div class="aviso-contenido">
                        <!-- Foto grande (si existe) -->
                        <?php if (!empty($noticia['imagen'])): ?>
                            <div class="aviso-foto-grande">
                                <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                            </div>
                        <?php endif; ?>

                        <p class="aviso-fecha"><?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?></p>
                        <h2 class="aviso-titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h2>
                        
                        <!-- Contenido COMPLETO -->
                        <div class="aviso-texto-completo">
                            <?php echo $noticia['contenido']; ?>
                        </div>

                        <!-- VÍDEO (si existe) -->
                        <?php if (!empty($noticia['video'])): ?>
                            <div class="aviso-media">
                                <video controls class="aviso-video">
                                    <source src="<?php echo htmlspecialchars($noticia['video']); ?>">
                                    Tu navegador no soporta vídeo HTML5.
                                </video>
                            </div>
                        <?php endif; ?>

                        <!-- PDF (si existe) -->
                        <?php if (!empty($noticia['pdf'])): ?>
                            <div class="aviso-media">
                                <div class="doc-erasmus">
                                    <a href="<?php echo htmlspecialchars($noticia['pdf']); ?>" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Descargar documento
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Botón volver -->
                        <a href="erasmus.php" class="aviso-enlace">
                            ← Volver a las movilidades
                        </a>
                    </div>
                </article>
            <?php else: ?>
                <div class="sin-contenido">
                    <i class="fas fa-globe"></i>
                    <h3>Noticia no encontrada</h3>
                    <p>Es posible que la movilidad haya sido eliminada.</p>
                    <a href="erasmus.php" class="aviso-enlace">← Volver a Erasmus</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>
