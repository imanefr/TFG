<?php 
include 'head.php';
include 'conexion.php';
 ?>

<!-- HERO -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-balance-scale" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Plan de Igualdad</h1>
                <p class="hero-subtitulo-universal">Descubre nuestro Plan de Igualdad</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL -->
<main class="plan_igualdad_pagina">
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <img src="img/plan_igualdad.png" alt="Cabezera Plan de Igualdad">
            <h1 class="plan_igualdad_titulo">Plan de Igualdad</h1>
            <h2 class="plan_igualdad_subtitulo">El Plan de Igualdad del IES La Arboleda es un documento que establece las medidas necesarias para garantizar la igualdad de oportunidades entre hombres y mujeres en el ámbito educativo.</h2>
            <p class="plan_igualdad_texto">Puedes ver el Plan de Igualdad del IES La Arboleda aquí abajo: </p>
            <?php 
                $sql = "SELECT * FROM plan_igualdad ORDER BY fecha_publicacion";
                $result = $conexion->query($sql);
                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc())
                    {
                        ?>
                        <div class="plan_igualdad_item">
                            <h2 class="plan_igualdad_item_titulo"><?php echo $row['titulo']; ?></h2>
                            <div class="plan_igualdad_item_imagen">
                                <?php if($row['imagen'] != '') { ?>
                                <img src="<?php echo $row['imagen']; ?>" alt="<?php echo $row['titulo']; ?>" class="plan_igualdad_item_imagen">
                                <?php } ?>
                            </div>
                            <p class="plan_igualdad_item_texto">
                                <?php echo $row['texto']; ?>
                            <br/>
                            <?php if($row['pdf'] != '') { ?>
                            <iframe src="<?php echo $row['pdf']; ?>" width="100%" height="800px" class="plan_igualdad_pdf_iframe"></iframe>
                            <a href="<?php echo $row['pdf']; ?>" class="plan_igualdad_btn_descarga" download>
                                <i class="fas fa-file-pdf"></i> Descargar PDF
                            </a>
                            <?php } ?>
                            <?php if($row['link'] != '') { ?>
                            <a href="<?php echo $row['link']; ?>" 
                            class="plan_igualdad_link"
                            data-type="link"
                            target="_blank">
                            <?php echo $row['texto_link']; ?>
                            </a>
                            <?php } ?>
                            </p>
                        </div>
                        <?php
                    }
                }
            ?>
        <h2 class="plan_igualdad_subtitulo">Coordinadora del Plan de Igualdad en el instituto: 
            <strong>Elena Rojo Joga.</strong> <a href="mailto:erojojoga@educa.madrid.org">erojojoga@educa.madrid.org</a>.</h2>
        <h3 class="plan_igualdad_texto">Podéis escribirme cualquier comentario, duda, propuesta o problema relacionados con el tema.</h3>
        <h2 class="plan_igualdad_subtitulo">
            <a href="https://site.educa.madrid.org/ies.laarboleda.alcorcon/index.php/2025/05/05/acampada-y-fiesta-de-la-multiculturalidad/" class="actividades-link" data-type="link" target="_blank">Pincha <strong>aquí</strong> para ver la lista de actividades de nuestro centro.</a>
        </h2>
    </section>
</main>

<?php include 'footer.php'; ?>