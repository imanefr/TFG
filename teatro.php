<?php 
include 'head.php';
include 'conexion.php';
 ?>

<!-- HERO -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-theater-masks" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">La trouppe Inestable</h1>
                <p class="hero-subtitulo-universal">Descubre nuestra propia tropa de teatro: ¡La trouppe inestable!</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL -->
<main class="teatro_pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h1 class="teatro_titulo">La Trouppe Inestable</h1>
            <h2 class="teatro_subtitulo">La trouppe inestable, el grupo de teatro del IES La Arboleda, reúne a amantes del teatro (alumnos, profesores y miembros de la comunidad educativa) desde 1997.</h2>
            <p class="teatro_texto">Puedes ver la trayectoria de nuestro grupo <a href="https://fandoylis.wordpress.com/la-trayectoria-de-la-trouppe-inestable/">aquí</a>.</p>
            <div class="teatro_info_contenedor">
                <h2 class="teatro_subtitulo">
                    Entre otros, en 2022 consiguió el Primer 
                    Premio en el XXIX Certamen de Teatro Escolar de la Comunidad de Madrid, 
                    en la categoría de Siglo de Oro, con una escena de Bodas de Sangre de 
                    Federico García Lorca.
                </h2>
                <iframe class="teatro_video" src="https://www.youtube.com/embed/WnKzndreA0s?autoplay=1&mute=1" 
                    title="Bodas de Sangre" frameborder="0" allow="autoplay" 
                    referrerpolicy="strict-origin-when-cross-origin" 
                    allowfullscreen></iframe>
                <h2 class="teatro_subtitulo">
                    El grupo está siempre abierto a nuevos miembros.
                </h2>
                <div class="teatro_imagenes_grid">
                    <img src="img/teatro1.png" alt="Imagen 1">
                    <img src="img/teatro2.png" alt="Imagen 2">
                    <img src="img/teatro3.png" alt="Imagen 3">
                    <img src="img/teatro4.png" alt="Imagen 4">
                    <img src="img/teatro5.png" alt="Imagen 5">
                    <img src="img/teatro6.png" alt="Imagen 6">
                </div>
                <?php 
                $sql = "SELECT * FROM teatro ORDER BY fecha_publicacion";
                $result = $conexion->query($sql);
                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc())
                    {
                        ?>
                        <div class="teatro_item">
                            <h2 class="teatro_item_titulo"><?php echo $row['titulo']; ?></h2>
                                    <div class="teatro_item_imagen">
                                        <img src="<?php echo $row['imagen']; ?>" alt="<?php echo $row['titulo']; ?>" class="teatro_item_imagen">
                                    </div>
                                    <p class="teatro_item_texto">
                                        <?php echo $row['texto']; ?>
                                    <br/>
                                    <a href="<?php echo $row['link']; ?>" 
                                    data-type="link" 
                                    target="_blank"
                                    class="teatro_link">
                                    <?php echo $row['texto_link']; ?>
                                    </a>
                                    </p>
                                </div>
                                <?php
                            }
                        }
                    ?>
            </div>
        </div>
    </section>
</main>

<?php
include 'footer.php';
?>