<?php 
include 'head.php';
include 'conexion.php';
 ?>

<!-- HERO -->
<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-briefcase" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Bolsa de Empleo</h1>
                <p class="hero-subtitulo-universal">Accede a EmpleaFP y revisa todas las ofertas para Formación Profesional.</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO PRINCIPAL -->
<main class="bolsa_empleo_pagina">
    <!-- ÚLTIMAS MOVILIDADES -->
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h2 class="bolsa_empleo_titulo">Bolsa de Empleo</h2>
            <?php 
                $sql = "SELECT * FROM bolsa_empleo ORDER BY fecha_publicacion";
                $result = $conexion->query($sql);
                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc())
                    {
                        ?>
                        <div class="bolsa_empleo_item">
                            <h2 class="bolsa_empleo_item_titulo"><?php echo $row['titulo']; ?></h2>
                            <div class="bolsa_empleo_item_imagen">
                                <?php if($row['imagen'] != '') { ?>
                                <img src="<?php echo $row['imagen']; ?>" alt="<?php echo $row['titulo']; ?>" class="bolsa_empleo_item_imagen">
                                <?php } ?>
                            </div>
                            <p class="bolsa_empleo_item_texto">
                                <?php echo $row['texto']; ?>
                            <br/>
                            <?php if($row['pdf'] != '') { ?>
                            <iframe src="<?php echo $row['pdf']; ?>" width="100%" height="800px" class="plan_igualdad_pdf_iframe"></iframe>
                            <?php } ?>
                            <?php if($row['link'] != '') { ?>
                            <a href="<?php echo $row['link']; ?>" 
                            data-type="link" 
                            target="_blank"
                            class="bolsa_empleo_link">
                            <?php echo $row['texto_link']; ?>
                            </a>
                            <?php } ?>
                            </p>
                        </div>
                        <?php
                    }
                }
            ?>
        </div>
    </section>
</main>

<?php
include 'footer.php';
?>
