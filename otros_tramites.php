<?php
include("head.php");
include("conexion.php");


?>

<section class="seccion-hero-universal">
    <div class="contenedor-max">
        <div class="hero-layout-universal">
            <div class="hero-icono-universal">
                <i class="fas fa-bookmark" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="hero-texto-universal">
                <h1 class="hero-titulo-universal">Otros Trámites</h1>
                <p class="hero-subtitulo-universal">Encuentra aquí los trámites no previamente listados</p>
            </div>
        </div>
    </div>
</section>

<section class="seccion-contenido">
    <div class="contenedor-max">
        <h1 class="otros_tramites_titulo">Otros Trámites</h1>
        <h2 class="otros_tramites_subtitulo">Encuentra aquí los trámites no previamente listados</h2>
        <div class="otros_tramites_info">
            <?php
            $sql = "SELECT * FROM otros_tramites";
            $result = $conexion->query($sql);
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    ?>
                        <h2 class="lista_formularios_otros_tramites_item_titulo"><?php echo $row['titulo']; ?></h2>
                        <ul class="lista_formularios_otros_tramites">
                            <li class="lista_formularios_otros_tramites_item">
                                <p class="lista_formularios_otros_tramites_item_texto">
                                    <?php echo $row['contenido']; ?>
                                    <br/>
                                    <a class="otros_tramites_link" href="<?php echo $row['enlace']; ?>">
                                        <?php echo $row['titulo']; ?>
                                    </a>
                                </p>
                            </li>
                        </ul>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</section>

<?php include("footer.php"); ?>