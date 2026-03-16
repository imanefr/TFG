<!DOCTYPE html>
<html lang="es">
<head>
<?php include 'head.php'; ?>
<link rel="stylesheet" href="style.css">
</head>

<?php
$rutaPdf = "https://site.educa.madrid.org/ies.laarboleda.alcorcon/wp-content/uploads/ies.laarboleda.alcorcon/2023/11/Libros-ESO-curso-23-24.pdf";
?>

<body>
    <section class="seccion-hero-universal">
        <div class="contenedor-max">
            <div class="hero-layout-universal">
                <div class="hero-icono-universal">
                    <i class="fas fa-book" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
                </div>
                <div class="hero-texto-universal">
                    <h1 class="hero-titulo-universal">Libros ESO</h1>
                    <p class="hero-subtitulo-universal">Listado oficial de libros de texto curso 23-24</p>
                </div>
            </div>
        </div>
    </section>

    <main class="info_eso_pagina">
        <section class="seccion-contenido">
            <div class="contenedor-max">
                <h2 class="info_eso_titulo">Libros de Texto ESO</h2>

                <!-- Botón de descarga -->
                <div class="info_eso_acciones">
                    <a href="<?php echo $rutaPdf; ?>" 
                       class="info_eso_btn_pdf"
                       target="_blank" 
                       rel="noopener">
                        <i class="fas fa-download"></i> Descargar PDF oficial
                    </a>
                </div>

                <!-- Vista previa PDF -->
                <div class="info_eso_vista">
                    <iframe 
                        src="<?php echo $rutaPdf; ?>" 
                        class="info_eso_visor"
                        title="Libros ESO curso 23-24">
                    </iframe>
                </div>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
