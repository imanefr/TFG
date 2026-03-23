<?php
include("conexion.php");

// Función para obtener primeras 15 palabras
function primeras15Palabras($texto) {
    $palabras = explode(' ', strip_tags($texto));
    $primeras15 = array_slice($palabras, 0, 15);
    return implode(' ', $primeras15) . '...';
}

// Consulta noticias Erasmus (últimas 8) con imagen
$sql = "SELECT e.*, u.nombre as ultima_edicion_usuario_nombre
        FROM erasmus_news e 
        LEFT JOIN usuarios u ON e.ultima_edicion_usuario_id = u.id 
        WHERE e.activo = 1 
        ORDER BY e.fecha DESC LIMIT 8";

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
                <i class="fas fa-plane" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Erasmus</h1>
                <p class="hero-subtitulo-universal">Proyectos de movilidad en Europa desde 2010</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL -->
<main class="erasmus_pagina">
    <!-- ÚLTIMAS MOVILIDADES -->
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="erasmus_titulo">Últimas Movilidades</h2>

            <?php if (!empty($noticias)): ?>
                <div class="erasmus_lista">
                    <?php foreach ($noticias as $noticia): ?>
                        <article class="erasmus_item">
                            <div class="erasmus_contenido">
                                <?php if (!empty($noticia['imagen'])): ?>
                                    <div class="erasmus_foto">
                                        <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                             alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                                    </div>
                                <?php endif; ?>

                                <div class="erasmus_texto">
                                    <p class="erasmus_fecha">
                                        <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
                                        <?php if (!empty($noticia['ultima_edicion_usuario_nombre'])): ?>
                                            <br><small style="color: #666;"><?php echo htmlspecialchars($noticia['ultima_edicion_usuario_nombre']); ?></small>
                                        <?php endif; ?>
                                    </p>

                                    <h3 class="erasmus_titulo_item"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                                    <p class="erasmus_resumen"><?php echo primeras15Palabras($noticia['contenido']); ?></p>
                                    <a href="erasmus_noticias.php?id=<?php echo (int) $noticia['id']; ?>" 
                                       class="erasmus_enlace">Leer completo →</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="erasmus_sin_contenido">
                    <i class="fas fa-globe"></i>
                    <h3>No hay noticias recientes</h3>
                    <p>Próximamente nuevas movilidades Erasmus+</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ACREDITACIONES -->
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="erasmus_titulo">Nuestras Acreditaciones</h2>

            <div class="erasmus_acreditaciones">
                <a href="https://erasmus-plus.ec.europa.eu/document/higher-education-institutions-holding-an-eche-2021-2027" class="erasmus_card" target="_blank">
                    <i class="fas fa-certificate"></i>
                    <h4>ERASMUS CHARTER FOR HIGHER EDUCATION</h4>
                    <p>ECHE 2021-2027</p>
                </a>
                <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/03/2020-1-CERTIFICADO-ES01-KA120-VET-095056.pdf" class="erasmus_card" target="_blank">
                    <i class="fas fa-certificate"></i>
                    <h4>KA120-VET Acreditación FP</h4>
                    <p>Formación Profesional</p>
                </a>
            </div>

            <div class="erasmus_documentos">
                <div class="erasmus_doc">
                    <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/04/CartaECHE2021_IES_LaArboleda_ES.pdf" target="_blank">
                        <i class="fas fa-file-pdf"></i> Carta ECHE (ES)
                    </a>
                </div>
                <div class="erasmus_doc">
                    <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/04/ECHE_Letter2021_IES_LaArboleda_EN.pdf" target="_blank">
                        <i class="fas fa-file-pdf"></i> ECHE Charter (EN)
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
$conexion->close();
include 'footer.php';
?>
