<?php
include 'head.php';
include 'conexion.php';
?>

<section class="seccion-hero-universal departamentos_hero">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-newspaper" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Blog</h1>
            </div>
        </div>
    </div>
</section>

<main class="departamentos_pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="departamentos_titulo">Blog</h2>
            <?php
            $sql = "SELECT * FROM blog ORDER BY fecha_publicacion DESC";
            $resultado = mysqli_query($conexion, $sql);
            $posts = [];
            while ($fila = $resultado->fetch_assoc()) {
                $posts[] = $fila;
            }
            ?>
            <div class="blog_grid">
                <?php
                if($posts != null)
                {
                    foreach ($posts as $post) {
                    ?>
                        <div class="blog_item">
                            <?php if (!empty($post['imagen'])): ?>
                                <div class="blog_item_imagen_wrapper">
                                    <img class="blog_item_imagen" src="<?php echo $post['imagen']; ?>" alt="<?php echo $post['titulo']; ?>">
                                </div>
                            <?php endif; ?>
                            
                            <div class="blog_item_texto_wrapper">
                                <p class="erasmus_fecha"><?php echo $post['fecha_publicacion']; ?></p>
                                <h3 class="blog_item_titulo"><?php echo $post['titulo']; ?></h3>
                                <p class="blog_item_contenido"><?php echo nl2br(htmlspecialchars($post['texto'])); ?></p>
                                
                                <?php if (!empty($post['pdf'])): ?>
                                    <iframe class="blog_item_pdf" src="<?php echo $post['pdf']; ?>" frameborder="0"></iframe>
                                <?php endif; ?>
                                
                                <?php if (!empty($post['video'])): ?>
                                    <div class="blog_video_wrapper">
                                        <iframe class="blog_item_video" src="<?php echo $post['video']; ?>" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($post['link'])): ?>
                                    <a href="<?php echo $post['link']; ?>" class="blog_item_link" target="_blank">Leer más <i class="fas fa-arrow-right"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php
                    }
                }
                else
                {
                    ?>
                    <div class="blog_sin_contenido">
                        <i class="fas fa-newspaper"></i>
                        <h3>No hay entradas recientes</h3>
                        <p>No te preocupes. ¡Habrán novedades pronto!</p>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </section>
</main>

<?php
include 'footer.php';
?>