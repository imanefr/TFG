<?php 
include('head.php'); 
include('conexion.php');
?>

<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-graduation-cap" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Solicitud Título ESO</h1>
                <p class="hero-subtitulo-universal">Solicitud del título de Educación Secundaria Obligatoria.</p>
            </div>
        </div>
    </div>
</section>

<main>
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h1 class="solicitud_titulo_eso_titulo">Información general para solicitudes de título ESO</h1>
            <div class="solicitud_titulo_eso_info">
                <?php 
                $sql = "SELECT * FROM titulo_eso ORDER BY fecha_publicacion";
                $result = $conexion->query($sql);
                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc())
                    {
                        ?>
                        <div class="titulo_eso_item">
                            <h2 class="solicitud_titulo_eso_info_titulo"><?php echo $row['titulo']; ?></h2>
                            <div class="titulo_eso_item_imagen">
                                <?php if($row['imagen'] != '') { ?>
                                <img src="<?php echo $row['imagen']; ?>" alt="<?php echo $row['titulo']; ?>" class="titulo_eso_item_imagen">
                                <?php } ?>
                            </div>
                            <p class="solicitud_titulo_eso_texto">
                                <?php echo $row['texto']; ?>
                            <br/>
                            <?php if($row['pdf'] != '') { ?>
                            <iframe src="<?php echo $row['pdf']; ?>" width="100%" height="800px" class="plan_igualdad_pdf_iframe"></iframe>
                            <?php } ?>
                            <?php if($row['link'] != '') { ?>
                            <a href="<?php echo $row['link']; ?>" 
                            data-type="link" 
                            target="_blank"
                            class="titulo_eso_link">
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
        </div>
    </section>
</main>

<?php include('footer.php'); ?>