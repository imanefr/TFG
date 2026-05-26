<?php
include("conexion.php");

// Función para obtener primeras 15 palabras
function primeras15Palabras($texto) {
    $palabras = explode(' ', strip_tags($texto));
    $primeras15 = array_slice($palabras, 0, 15);
    return implode(' ', $primeras15) . '...';
}

// Consulta noticias Erasmus (últimas 8) con imagen
$sql = "SELECT r.*
        FROM noticias r 
        WHERE r.destacada = 1
        ORDER BY r.fecha DESC LIMIT 8";

$resultado = $conexion->query($sql);
$noticias = [];
while ($fila = $resultado->fetch_assoc()) {
    $noticias[] = $fila;
}
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
<main class="relevante_pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="relevante_titulo">Noticias Destacadas</h2>

            <?php if (!empty($noticias)): ?>
                <div class="relevante_lista">
                    <?php foreach ($noticias as $noticia): ?>
                        <article class="relevante_item">
                            <div class="relevante_contenido">
                                <?php if (!empty($noticia['imagen'])): ?>
                                    <div class="relevante_foto">
                                        <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                             alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                                    </div>
                                <?php endif; ?>

                                <div class="relevante_texto">
                                    <p class="relevante_fecha">
                                        <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
                                        <?php if (!empty($noticia['ultima_edicion_usuario_nombre'])): ?>
                                            <br><small style="color: #666;"><?php echo htmlspecialchars($noticia['ultima_edicion_usuario_nombre']); ?></small>
                                        <?php endif; ?>
                                    </p>

                                    <h3 class="relevante_titulo_item"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                                    <p class="relevante_resumen"><?php echo primeras15Palabras($noticia['contenido']); ?></p>
                                    <a href="noticias_relevantes.php?id=<?php echo (int) $noticia['id']; ?>" 
                                       class="relevante_enlace">Leer completo →</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="relevante_sin_contenido">
                    <i class="fas fa-globe"></i>
                    <h3>No hay noticias recientes.</h3>
                    <p>Se colocarán nuevas noticias pronto.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
$conexion->close();
include 'footer.php';
?>