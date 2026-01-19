<?php
// Ejemplo simple SIN BD, usando el PDF que has puesto:
$rutaPdf = "https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2023/11/Libros-ESO-curso-23-24.pdf";
?>


    <body>

               <?php include 'head.php'; ?>

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
