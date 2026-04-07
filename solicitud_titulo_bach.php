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
                <h1 class="hero-titulo-universal">Solicitud Título Bachillerato</h1>
                <p class="hero-subtitulo-universal">Información sobre la solicitud del título de Bachillerato.</p>
            </div>
        </div>
    </div>
</section>

<main>
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h1 class="solicitud_titulo_bachillerato_titulo">Información general para solicitudes de título Bachillerato</h1>
            <div class="horario-secretaria">
                <h2 class="matriculacion_bachillerato_subtitulo">HORARIO DE SECRETARÍA:</h2>
                <p class="matriculacion_bachillerato_texto">
                    <strong>De lunes a viernes de 09:30 a 12:00</strong>
                    <div class="secretaria_contacto">
                        <p><strong>Teléfono:</strong> 916 43 99 91</p>
                        <p><strong>Fax:</strong> 916 44 00 25</p>
                        <p><strong>Correo:</strong> <a href="mailto:secretaria.ies.laarboleda.alcorcon@educa.madrid.org">secretaria.ies.laarboleda.alcorcon@educa.madrid.org</a></p>
                    </div>
                </p>
            </div>
            <div class="solicitud_titulo_bachillerato_info">
                <?php 
                $sql = "SELECT * FROM titulo_bach ORDER BY fecha_publicacion";
                $result = $conexion->query($sql);
                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc())
                    {
                        ?>
                        <div class="titulo_bachillerato_item">
                            <h2 class="solicitud_titulo_bachillerato_info_titulo"><?php echo $row['titulo']; ?></h2>
                            <div class="titulo_bachillerato_item_imagen">
                                <?php if($row['imagen'] != '') { ?>
                                <img src="<?php echo $row['imagen']; ?>" alt="<?php echo $row['titulo']; ?>" class="titulo_bachillerato_item_imagen">
                                <?php } ?>
                            </div>
                            <p class="solicitud_titulo_bachillerato_texto">
                                <?php echo $row['texto']; ?>
                            <br/>
                            <?php if($row['pdf'] != '') { ?>
                            <iframe src="<?php echo $row['pdf']; ?>" width="100%" height="800px" class="plan_igualdad_pdf_iframe"></iframe>
                            <?php } ?>
                            <?php if($row['link'] != '') { ?>
                            <a href="<?php echo $row['link']; ?>" 
                            data-type="link" 
                            target="_blank"
                            class="titulo_bachillerato_link">
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