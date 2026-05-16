<?php
include("conexion.php");                     // Carga conexión MySQLi

// Función para obtener primeras 15 palabras del texto
function primeras15Palabras($texto) {
    $palabras = explode(' ', strip_tags($texto));  // Separa palabras, elimina HTML
    $primeras15 = array_slice($palabras, 0, 15);   // Toma primeras 15
    return implode(' ', $primeras15) . '...';      // Une y agrega puntos suspensivos
}

// Consulta últimas 8 noticias Erasmus activas con editor
$sql = "SELECT e.*
        FROM erasmus_news e
        WHERE e.activo = 1
        ORDER BY e.fecha DESC LIMIT 8";
$resultado = $conexion->query($sql);         // Ejecuta consulta
$noticias = [];
while ($fila = $resultado->fetch_assoc()) { // Almacena todas las noticias
    $noticias[] = $fila;
}
?>

<?php include 'head.php'; ?>                 <!-- Header HTML página -->

<!-- HERO PRINCIPAL -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-plane icono_universal"></i> 
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Erasmus</h1> <!-- Título página -->
                <p class="hero-subtitulo-universal">Proyectos de movilidad en Europa desde 2010</p> <!-- Descripción -->
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL -->
<main class="erasmus_pagina">
    <!-- SECCIÓN ÚLTIMAS MOVILIDADES -->
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="erasmus_titulo">Últimas Movilidades</h2> <!-- Título sección -->

            <?php if (!empty($noticias)): ?> <!-- Verifica si hay noticias -->
                <div class="erasmus_lista"> <!-- Lista de noticias -->
                    <?php foreach ($noticias as $noticia): ?> <!-- Itera cada noticia -->
                        <article class="erasmus_item"> <!-- Article individual -->
                            <div class="erasmus_contenido">
                                <?php if (!empty($noticia['imagen'])): ?> <!-- Imagen opcional -->
                                    <div class="erasmus_foto">
                                        <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                             alt="<?php echo htmlspecialchars($noticia['titulo']); ?>"> <!-- Imagen escapada -->
                                    </div>
                                <?php endif; ?>

                                <div class="erasmus_texto"> <!-- Contenido texto -->
                                    <p class="erasmus_fecha"> <!-- Fecha y editor -->
                                        <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?> <!-- Fecha formateada -->
                                        <?php if (!empty($noticia['ultima_edicion_nombre'])): ?>
                                            <br><small class="letra-666"><?php echo htmlspecialchars($noticia['ultima_edicion_nombre']); ?></small> <!-- Nombre editor -->
                                        <?php endif; ?>
                                    </p>

                                    <h3 class="erasmus_titulo_item"><?php echo htmlspecialchars($noticia['titulo']); ?></h3> <!-- Título escapado -->
                                    <p class="erasmus_resumen"><?php echo primeras15Palabras($noticia['contenido']); ?></p> <!-- Resumen 15 palabras -->
                                    <a href="erasmus_noticias.php?id=<?php echo (int) $noticia['id']; ?>" 
                                       class="erasmus_enlace">Leer completo →</a> <!-- Enlace noticia completa -->
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?> <!-- Sin noticias -->
                <div class="erasmus_sin_contenido">
                    <i class="fas fa-globe"></i> <!-- Icono globo -->
                    <h3>No hay noticias recientes</h3>
                    <p>Próximamente nuevas movilidades Erasmus+</p> <!-- Mensaje vacío -->
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- SECCIÓN ACREDITACIONES -->
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="erasmus_titulo">Nuestras Acreditaciones</h2> <!-- Título acreditaciones -->

            <!-- CARDS ACREDITACIONES -->
            <div class="erasmus_acreditaciones">
                <a href="https://erasmus-plus.ec.europa.eu/document/higher-education-institutions-holding-an-eche-2021-2027" class="erasmus_card" target="_blank">
                    <i class="fas fa-certificate"></i> <!-- Icono certificado -->
                    <h4>ERASMUS CHARTER FOR HIGHER EDUCATION</h4> <!-- ECHE 2021-2027 -->
                    <p>ECHE 2021-2027</p>
                </a>
                <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/03/2020-1-CERTIFICADO-ES01-KA120-VET-095056.pdf" class="erasmus_card" target="_blank">
                    <i class="fas fa-certificate"></i>
                    <h4>KA120-VET Acreditación FP</h4> <!-- Acreditación FP -->
                    <p>Formación Profesional</p>
                </a>
            </div>

            <!-- DOCUMENTOS PDF -->
            <div class="erasmus_documentos">
                <div class="erasmus_doc"> <!-- Carta Española -->
                    <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/04/CartaECHE2021_IES_LaArboleda_ES.pdf" target="_blank">
                        <i class="fas fa-file-pdf"></i> Carta ECHE (ES)
                    </a>
                </div>
                <div class="erasmus_doc"> <!-- Carta Inglesa -->
                    <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2024/04/ECHE_Letter2021_IES_LaArboleda_EN.pdf" target="_blank">
                        <i class="fas fa-file-pdf"></i> ECHE Charter (EN)
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
$conexion->close();                         // Cierra conexión BD
include 'footer.php';                       // Footer página
?>