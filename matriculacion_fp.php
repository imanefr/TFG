<?php 
include("head.php"); 
include("conexion.php");
?>

<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-file-invoice" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Matriculación FP</h1>
                <p class="hero-subtitulo-universal">Información sobre la matriculación en Formación Profesional</p>
            </div>
        </div>
    </div>
</section>

<main>
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <h1 class="matriculacion_fp_titulo">Información general matrícula para alumnos de FP</h1>
            <div class="horario-secretaria">
                <h2 class="matriculacion_fp_subtitulo">HORARIO DE SECRETARÍA:</h2>
                <p class="matriculacion_fp_texto">
                    <strong>De lunes a viernes de 09:30 a 12:00</strong>
                    <div class="secretaria_contacto">
                        <p><strong>Teléfono:</strong> 916 43 99 91</p>
                        <p><strong>Fax:</strong> 916 44 00 25</p>
                        <p><strong>Correo:</strong> <a href="mailto:secretaria.ies.laarboleda.alcorcon@educa.madrid.org">secretaria.ies.laarboleda.alcorcon@educa.madrid.org</a></p>
                    </div>
                </p>
            </div>
            <div class="documentacion-necesaria-fp">
                <h2 class="documentacion_necesaria_titulo">DOCUMENTACIÓN NECESARIA PARA FORMALIZAR LA MATRÍCULA DE LOS ALUMNOS/AS DEL CENTRO.</h2>
                <ul class="lista_documentacion_fp">
                        <?php 
                            $sql = "SELECT * FROM matriculacion_fp ORDER BY fecha";
                            $result = $conexion->query($sql);
                            if($result->num_rows > 0){
                                while($row = $result->fetch_assoc())
                                {
                                    ?>
                                    <li class="lista_documentacion_fp_item">
                                        <h2 class="lista_documentacion_fp_item_titulo"><?php echo $row['titulo']; ?></h2>
                                        <p class="lista_documentacion_fp_item_texto">
                                            <?php echo $row['descripcion']; ?>
                                            <br/>
                                            <a href="<?php echo $row['enlace']; ?>" 
                                            data-type="link" 
                                            target="_blank"
                                            class="matriculacion_fp_link">
                                            <?php echo $row['titulo']; ?>
                                            </a>
                                        </p>
                                    </li>
                                    <?php
                                }
                            }
                        ?>
                </ul>
        </div>
    </section>
</main>

<?php include("footer.php"); ?>