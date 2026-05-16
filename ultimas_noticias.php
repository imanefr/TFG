<?php
include("conexion.php");

// Función para obtener primeras 15 palabras
function primeras15Palabras($texto) {
    $palabras = explode(' ', strip_tags($texto));
    $primeras15 = array_slice($palabras, 0, 15);
    return implode(' ', $primeras15) . '...';
}

// Consulta noticias Erasmus (últimas 8) con imagen
$sql = "SELECT * FROM noticias ORDER BY fecha DESC LIMIT 5";

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
                <h1 class="hero-titulo-universal">Últimas Noticias</h1>
                <p class="hero-subtitulo-universal">Descubre las últimas noticias subidas por nuestro centro.</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL -->
<main class="ultimas_noticias_pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="ultimas_noticias_titulo">Últimas Noticias</h2>

            <?php if (!empty($noticias)): ?>
                <div class="ultimas_noticias_lista">
                    <?php foreach ($noticias as $noticia): ?>
                        <article class="ultimas_noticias_item">
                            <div class="ultimas_noticias_contenido">
                                <?php if (!empty($noticia['imagen'])): ?>
                                    <div class="ultimas_noticias_foto">
                                        <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                             alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                                    </div>
                                <?php endif; ?>

                                <div class="ultimas_noticias_texto">
                                    <p class="ultimas_noticias_fecha">
                                        <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
                                        <?php if (!empty($noticia['ultima_edicion_usuario_nombre'])): ?>
                                            <br><small style="color: #666;"><?php echo htmlspecialchars($noticia['ultima_edicion_usuario_nombre']); ?></small>
                                        <?php endif; ?>
                                    </p>

                                    <h3 class="ultimas_noticias_titulo_item"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                                    <p class="ultimas_noticias_resumen"><?php echo primeras15Palabras($noticia['contenido']); ?></p>
                                    <a href="noticia.php?id=<?php echo (int) $noticia['id']; ?>" 
                                       class="ultimas_noticias_enlace">Leer completo →</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="ultimas_noticias_sin_contenido">
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