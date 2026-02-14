<?php
// Ejemplo simple SIN BD, usando el PDF que has puesto:
$rutaPdf = "https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2023/11/Libros-ESO-curso-23-24.pdf";
?>


<body>

    <?php include 'head.php'; ?>
    <section class="seccion-hero-universal">
        <div class="contenedor-max">
            <div class="hero-layout-universal">
                <div class="hero-icono-universal">
                    <i class="fas fa-info" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
                </div>
                <div class="hero-texto-universal">
                    <h1 class="hero-titulo-universal">información ESO</h1>
                    <p class="hero-subtitulo-universal">Proyectos de movilidad en Europa desde 2010
                    </p>
                </div>
            </div>
        </div>
    </section>

    <main>
        <section class="seccion-contenido">
            <div class="contenedor-max">
                <h2 class="seccion-contenido-h2">Libros de Texto ESO</h2>

                <!-- Botón de descarga -->
                <div class="libros-acciones">
                    <a href="<?php echo $rutaPdf; ?>" 
                       class="btn-descargar-pdf"
                       target="_blank" 
                       rel="noopener">
                        <i class="fas fa-download"></i> Descargar PDF
                    </a>
                </div>

                <!-- Vista previa PDF -->
                <div class="libros-vista-previa">
                    <iframe 
                        src="<?php echo $rutaPdf; ?>" 
                        class="visor-pdf"
                        title="Libros ESO curso 23-24">
                    </iframe>
                </div>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
